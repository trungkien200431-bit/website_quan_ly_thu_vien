<?php

namespace App\Http\Controllers;

use App\Models\ChiTietPhieuMuon;
use App\Models\DocGia;
use App\Models\PhieuMuon;
use App\Models\Sach;
use App\Models\ThanhToanPhat;
use App\Models\YeuCauMuon;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MuonTraController extends BoDieuKhien
{
    private const FINE_PER_DAY = 5000;

    public function index(Request $request): View
    {
        $this->updateOverdueStatuses();

        $query = PhieuMuon::query()->with(['reader', 'processedBy'])->latest('borrow_date');

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('code', 'like', "%{$search}%")
                    ->orWhereHas('reader', function (Builder $readerQuery) use ($search): void {
                        $readerQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('card_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $borrowings = $query->paginate(10)->withQueryString();

        return view('muon_tra.danh_sach', compact('borrowings'));
    }

    public function requestIndex(Request $request): View
    {
        $query = YeuCauMuon::query()
            ->with(['reader', 'requestedBy', 'processedBy', 'items.book', 'approvedBorrowing'])
            ->latest('request_date');

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('code', 'like', "%{$search}%")
                    ->orWhereHas('reader', function (Builder $readerQuery) use ($search): void {
                        $readerQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('card_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items.book', function (Builder $bookQuery) use ($search): void {
                        $bookQuery->where('title', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $borrowingRequests = $query->paginate(10)->withQueryString();
        $requestStats = [
            'pending' => YeuCauMuon::query()->where('status', 'pending')->count(),
            'approved' => YeuCauMuon::query()->where('status', 'approved')->count(),
            'rejected' => YeuCauMuon::query()->where('status', 'rejected')->count(),
            'cancelled' => YeuCauMuon::query()->where('status', 'cancelled')->count(),
        ];

        return view('muon_tra.yeu_cau', compact('borrowingRequests', 'requestStats'));
    }

    public function create(): View
    {
        return view('muon_tra.tao', [
            'readers' => DocGia::query()->where('status', 'active')->orderBy('full_name')->get(),
            'books' => Sach::query()
                ->with('authors')
                ->where('is_active', true)
                ->where('available_copies', '>', 0)
                ->orderBy('title')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reader_id' => ['required', 'exists:readers,id'],
            'borrow_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:borrow_date'],
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        $selectedItems = collect($data['items'] ?? [])
            ->mapWithKeys(fn ($quantity, $bookId) => [(int) $bookId => (int) $quantity])
            ->filter(fn ($quantity) => $quantity > 0)
            ->all();

        if ($selectedItems === []) {
            throw ValidationException::withMessages([
                'items' => 'Vui lòng chọn ít nhất 1 sách và nhập số lượng mượn.',
            ]);
        }

        $reader = DocGia::query()->findOrFail($data['reader_id']);
        $this->ensureReaderCanBorrow($reader);

        $borrowing = DB::transaction(function () use ($reader, $data, $selectedItems): PhieuMuon {
            return $this->createBorrowingFromSelectedItems(
                $reader,
                $selectedItems,
                $data['borrow_date'],
                $data['due_date'],
                $data['note'] ?? null,
            );
        });

        return redirect()->route('muon_tra.show', $borrowing)->with('success', 'Tạo phiếu mượn thành công.');
    }

    public function show(PhieuMuon $borrowing): View
    {
        $borrowing->load([
            'reader',
            'processedBy',
            'items.book.authors',
            'items.finePayments.paidBy',
        ]);

        $paidFineTotal = $borrowing->items->sum(fn ($item) => $item->finePayments->sum('amount'));
        $outstandingFine = max(0, (float) $borrowing->fine_total - (float) $paidFineTotal);

        return view('muon_tra.chi_tiet', compact('borrowing', 'paidFineTotal', 'outstandingFine'));
    }

    public function requestShow(YeuCauMuon $borrowingRequest): View
    {
        $borrowingRequest->load([
            'reader',
            'requestedBy',
            'processedBy',
            'approvedBorrowing',
            'items.book.authors',
        ]);

        return view('muon_tra.yeu_cau_chi_tiet', compact('borrowingRequest'));
    }

    public function approveBorrowingRequest(Request $request, YeuCauMuon $borrowingRequest): RedirectResponse
    {
        $data = $request->validate([
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'process_note' => ['nullable', 'string'],
        ]);

        $approvedBorrowing = DB::transaction(function () use ($borrowingRequest, $data): PhieuMuon {
            $borrowingRequest = YeuCauMuon::query()
                ->with(['reader', 'items.book'])
                ->lockForUpdate()
                ->findOrFail($borrowingRequest->id);

            if ($borrowingRequest->status !== 'pending') {
                throw ValidationException::withMessages([
                    'due_date' => 'Yêu cầu này không còn ở trạng thái chờ duyệt.',
                ]);
            }

            $reader = $borrowingRequest->reader;
            $this->ensureReaderCanBorrow($reader);

            $selectedItems = $borrowingRequest->items
                ->mapWithKeys(fn ($item) => [(int) $item->book_id => (int) $item->quantity])
                ->all();

            if ($selectedItems === []) {
                throw ValidationException::withMessages([
                    'due_date' => 'Yêu cầu mượn này chưa có đầu sách hợp lệ.',
                ]);
            }

            $borrowing = $this->createBorrowingFromSelectedItems(
                $reader,
                $selectedItems,
                now()->toDateString(),
                $data['due_date'],
                $data['process_note'] ?? ('Duyệt từ yêu cầu mượn '.$borrowingRequest->code),
            );

            $borrowingRequest->update([
                'status' => 'approved',
                'due_date' => $data['due_date'],
                'processed_by' => auth()->id(),
                'processed_at' => now(),
                'processed_note' => $data['process_note'] ?? null,
                'approved_borrowing_id' => $borrowing->id,
            ]);

            return $borrowing;
        });

        return redirect()
            ->route('muon_tra.yeu_cau.chi_tiet', $borrowingRequest)
            ->with('success', 'Đã duyệt yêu cầu mượn và tạo phiếu mượn '.$approvedBorrowing->code.'.');
    }

    public function rejectBorrowingRequest(Request $request, YeuCauMuon $borrowingRequest): RedirectResponse
    {
        $data = $request->validate([
            'process_note' => ['required', 'string'],
        ]);

        if ($borrowingRequest->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể từ chối yêu cầu đang chờ xử lý.');
        }

        $borrowingRequest->update([
            'status' => 'rejected',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
            'processed_note' => $data['process_note'],
        ]);

        return back()->with('success', 'Đã từ chối yêu cầu mượn.');
    }

    public function processReturn(Request $request, PhieuMuon $borrowing): RedirectResponse
    {
        $data = $request->validate([
            'return_date' => ['required', 'date'],
            'returns' => ['required', 'array'],
            'returns.*' => ['nullable', 'integer', 'min:0'],
            'return_note' => ['nullable', 'string'],
            'payment_amount' => ['nullable', 'numeric', 'min:0.01'],
            'paid_at' => ['nullable', 'date'],
            'payment_note' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($borrowing, $data): void {
            $borrowing = PhieuMuon::query()
                ->with('items.book')
                ->lockForUpdate()
                ->findOrFail($borrowing->id);

            if (in_array($borrowing->status, ['returned', 'cancelled'], true)) {
                throw ValidationException::withMessages([
                    'returns' => 'Phiếu mượn này không thể cập nhật trả sách.',
                ]);
            }

            $returnDate = Carbon::parse($data['return_date']);
            $hasAnyReturn = false;

            foreach ($borrowing->items as $item) {
                $returnQty = (int) ($data['returns'][$item->id] ?? 0);
                if ($returnQty === 0) {
                    continue;
                }

                $remainingQty = $item->quantity - $item->returned_quantity;
                if ($returnQty > $remainingQty) {
                    throw ValidationException::withMessages([
                        'returns' => 'Số lượng trả của sách "'.$item->book->title.'" vượt quá số lượng còn lại.',
                    ]);
                }

                $book = Sach::query()->lockForUpdate()->findOrFail($item->book_id);
                $book->increment('available_copies', $returnQty);

                $item->returned_quantity += $returnQty;

                $overdueDays = $returnDate->greaterThan($item->due_date)
                    ? $item->due_date->diffInDays($returnDate)
                    : 0;

                $item->fine_amount += $overdueDays * self::FINE_PER_DAY * $returnQty;

                if (! empty($data['return_note'])) {
                    $item->note = $data['return_note'];
                }

                if ($item->returned_quantity >= $item->quantity) {
                    $item->status = 'returned';
                    $item->return_date = $returnDate;
                } elseif ($returnDate->greaterThan($item->due_date)) {
                    $item->status = 'overdue';
                } else {
                    $item->status = 'partially_returned';
                }

                $item->save();
                $hasAnyReturn = true;
            }

            if (! $hasAnyReturn) {
                throw ValidationException::withMessages([
                    'returns' => 'Bạn chưa nhập số lượng trả cho sách nào.',
                ]);
            }

            if (! empty($data['payment_amount'])) {
                $this->allocateFinePayment(
                    $borrowing,
                    (float) $data['payment_amount'],
                    Carbon::parse($data['paid_at'] ?? $data['return_date']),
                    $data['payment_note'] ?? null,
                );
            }

            $this->refreshBorrowingStatus($borrowing->fresh('items'));
        });

        return redirect()->route('muon_tra.show', $borrowing)->with('success', 'Cập nhật trả sách thành công.');
    }

    public function payFine(Request $request, PhieuMuon $borrowing, ChiTietPhieuMuon $item): RedirectResponse
    {
        if ((int) $item->borrowing_id !== (int) $borrowing->id) {
            abort(404);
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $outstandingFine = max(0, (float) $item->fine_amount - (float) $item->finePayments()->sum('amount'));

        if ((float) $data['amount'] > $outstandingFine) {
            throw ValidationException::withMessages([
                'amount' => 'Số tiền thanh toán vượt quá số tiền phạt còn lại.',
            ]);
        }

        ThanhToanPhat::create([
            'borrowing_item_id' => $item->id,
            'amount' => $data['amount'],
            'paid_at' => $data['paid_at'],
            'paid_by' => auth()->id(),
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('muon_tra.show', $borrowing)->with('success', 'Thanh toán tiền phạt thành công.');
    }

    private function updateOverdueStatuses(): void
    {
        ChiTietPhieuMuon::query()
            ->whereIn('status', ['borrowed', 'partially_returned'])
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        PhieuMuon::query()
            ->where('status', 'borrowed')
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);
    }

    private function refreshBorrowingStatus(PhieuMuon $borrowing): void
    {
        $items = $borrowing->items;
        $today = now()->toDateString();

        foreach ($items as $item) {
            if ($item->returned_quantity < $item->quantity && $item->due_date->toDateString() < $today) {
                if ($item->status !== 'overdue') {
                    $item->status = 'overdue';
                    $item->save();
                }
            }
        }

        $allReturned = $items->every(fn ($item) => $item->returned_quantity >= $item->quantity);
        $hasOverdue = $items->contains(
            fn ($item) => $item->returned_quantity < $item->quantity
                && $item->due_date->toDateString() < $today
        );

        $borrowing->update([
            'status' => $allReturned ? 'returned' : ($hasOverdue ? 'overdue' : 'borrowed'),
            'return_date' => $allReturned ? $items->max('return_date') : null,
            'fine_total' => $items->sum('fine_amount'),
        ]);
    }

    private function allocateFinePayment(PhieuMuon $borrowing, float $amount, Carbon $paidAt, ?string $note): void
    {
        $remainingAmount = $amount;

        $items = ChiTietPhieuMuon::query()
            ->where('borrowing_id', $borrowing->id)
            ->orderBy('due_date')
            ->get();

        $totalOutstanding = $items->sum(function ($item): float {
            $paid = (float) $item->finePayments()->sum('amount');

            return max(0, (float) $item->fine_amount - $paid);
        });

        if ($remainingAmount > $totalOutstanding + 0.0001) {
            throw ValidationException::withMessages([
                'payment_amount' => 'Số tiền thanh toán vượt quá tổng tiền phạt còn lại.',
            ]);
        }

        foreach ($items as $item) {
            if ($remainingAmount <= 0.0001) {
                break;
            }

            $paid = (float) $item->finePayments()->sum('amount');
            $outstanding = max(0, (float) $item->fine_amount - $paid);
            if ($outstanding <= 0) {
                continue;
            }

            $paymentAmount = min($remainingAmount, $outstanding);

            ThanhToanPhat::create([
                'borrowing_item_id' => $item->id,
                'amount' => $paymentAmount,
                'paid_at' => $paidAt->toDateString(),
                'paid_by' => auth()->id(),
                'note' => $note,
            ]);

            $remainingAmount -= $paymentAmount;
        }
    }

    private function createBorrowingFromSelectedItems(
        DocGia $reader,
        array $selectedItems,
        string $borrowDate,
        string $dueDate,
        ?string $note
    ): PhieuMuon {
        $bookIds = array_keys($selectedItems);
        $bookCount = Sach::query()->whereIn('id', $bookIds)->count();

        if ($bookCount !== count($bookIds)) {
            throw ValidationException::withMessages([
                'items' => 'Danh sách sách mượn không hợp lệ.',
            ]);
        }

        $borrowing = PhieuMuon::query()->create([
            'code' => $this->generateBorrowingCode(),
            'reader_id' => $reader->id,
            'processed_by' => auth()->id(),
            'borrow_date' => $borrowDate,
            'due_date' => $dueDate,
            'status' => 'borrowed',
            'fine_total' => 0,
            'note' => $note,
        ]);

        foreach ($selectedItems as $bookId => $quantity) {
            $book = Sach::query()->lockForUpdate()->findOrFail((int) $bookId);

            if (! $book->is_active) {
                throw ValidationException::withMessages([
                    'items' => 'Sách "'.$book->title.'" không ở trạng thái hoạt động.',
                ]);
            }

            if ($book->available_copies < $quantity) {
                throw ValidationException::withMessages([
                    'items' => 'Sách "'.$book->title.'" không đủ số lượng có sẵn.',
                ]);
            }

            $book->decrement('available_copies', $quantity);

            ChiTietPhieuMuon::query()->create([
                'borrowing_id' => $borrowing->id,
                'book_id' => $book->id,
                'quantity' => $quantity,
                'returned_quantity' => 0,
                'due_date' => $dueDate,
                'status' => 'borrowed',
            ]);
        }

        return $borrowing;
    }

    private function ensureReaderCanBorrow(DocGia $reader): void
    {
        if ($reader->status !== 'active') {
            throw ValidationException::withMessages([
                'reader_id' => 'Độc giả này không ở trạng thái hoạt động.',
            ]);
        }

        if ($reader->expiry_date && $reader->expiry_date->isPast()) {
            throw ValidationException::withMessages([
                'reader_id' => 'Thẻ độc giả đã hết hạn.',
            ]);
        }

        if ($reader->borrowings()
            ->whereIn('status', ['borrowed', 'overdue'])
            ->whereDate('due_date', '<', now()->toDateString())
            ->exists()) {
            throw ValidationException::withMessages([
                'reader_id' => 'Độc giả đang có phiếu mượn quá hạn.',
            ]);
        }

        if ($this->outstandingFineForReader($reader) > 0.0001) {
            throw ValidationException::withMessages([
                'reader_id' => 'Độc giả vẫn còn tiền phạt chưa thanh toán.',
            ]);
        }
    }

    private function outstandingFineForReader(DocGia $reader): float
    {
        return (float) ChiTietPhieuMuon::query()
            ->with('finePayments')
            ->whereHas('borrowing', function (Builder $query) use ($reader): void {
                $query->where('reader_id', $reader->id);
            })
            ->get()
            ->sum(fn (ChiTietPhieuMuon $item) => max(
                0,
                (float) $item->fine_amount - (float) $item->finePayments->sum('amount')
            ));
    }

    private function generateBorrowingCode(): string
    {
        do {
            $code = 'PM-'.now()->format('Ymd').'-'.random_int(1000, 9999);
        } while (PhieuMuon::query()->where('code', $code)->exists());

        return $code;
    }
}
