<?php

namespace App\Http\Controllers;

use App\Models\ChiTietPhieuMuon;
use App\Models\ChiTietYeuCauMuon;
use App\Models\DocGia;
use App\Models\NhanVien;
use App\Models\Sach;
use App\Models\TheLoai;
use App\Models\YeuCauMuon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KhachHangController extends BoDieuKhien
{
    public function index(Request $request): View
    {
        $reader = $this->resolveReaderProfile($request);

        $recentBorrowings = $reader->borrowings()
            ->with(['items.book', 'items.finePayments'])
            ->latest('borrow_date')
            ->take(5)
            ->get();

        $recentBorrowingRequests = $reader->borrowingRequests()
            ->with('items.book')
            ->latest('request_date')
            ->take(5)
            ->get();

        return view('khach_hang.tong_quan', [
            'reader' => $reader,
            'recentBorrowings' => $recentBorrowings,
            'recentBorrowingRequests' => $recentBorrowingRequests,
            'catalogHighlights' => Sach::query()
                ->with(['authors', 'publisher'])
                ->where('is_active', true)
                ->where('available_copies', '>', 0)
                ->orderByDesc('available_copies')
                ->orderBy('title')
                ->take(6)
                ->get(),
            'customerStats' => [
                'total_borrowings' => $reader->borrowings()->count(),
                'active_borrowings' => $reader->borrowings()->whereIn('status', ['borrowed', 'overdue'])->count(),
                'outstanding_fines' => $this->outstandingFineForReader($reader),
                'membership_expiry' => $reader->expiry_date,
                'pending_requests' => $reader->borrowingRequests()->where('status', 'pending')->count(),
            ],
        ]);
    }

    public function books(Request $request): View
    {
        $reader = $this->resolveReaderProfile($request);

        $query = Sach::query()
            ->with(['authors', 'category', 'publisher'])
            ->where('is_active', true);

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('authors', function (Builder $authorQuery) use ($search): void {
                        $authorQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('publisher', function (Builder $publisherQuery) use ($search): void {
                        $publisherQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($categoryId = $request->integer('category_id')) {
            $query->where('category_id', $categoryId);
        }

        match ((string) $request->input('availability')) {
            'available' => $query->where('available_copies', '>', 0),
            'out' => $query->where('available_copies', 0),
            default => null,
        };

        match ((string) $request->input('sort')) {
            'title_asc' => $query->orderBy('title'),
            'stock_desc' => $query->orderByDesc('available_copies')->orderBy('title'),
            'year_desc' => $query->orderByDesc('published_year')->orderBy('title'),
            default => $query->latest(),
        };

        $pendingRequestBookIds = ChiTietYeuCauMuon::query()
            ->whereHas('borrowingRequest', function (Builder $builder) use ($reader): void {
                $builder->where('reader_id', $reader->id)
                    ->where('status', 'pending');
            })
            ->pluck('book_id')
            ->all();

        return view('khach_hang.sach', [
            'books' => $query->paginate(12)->withQueryString(),
            'categories' => TheLoai::query()->where('is_active', true)->orderBy('name')->get(),
            'pendingRequestBookIds' => $pendingRequestBookIds,
        ]);
    }

    public function borrowings(Request $request): View
    {
        $reader = $this->resolveReaderProfile($request);

        $query = $reader->borrowings()
            ->with(['items.book', 'items.finePayments', 'processedBy'])
            ->latest('borrow_date');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return view('khach_hang.lich_su_muon', [
            'borrowings' => $query->paginate(8)->withQueryString(),
            'reader' => $reader,
        ]);
    }

    public function borrowingRequests(Request $request): View
    {
        $reader = $this->resolveReaderProfile($request);

        $query = $reader->borrowingRequests()
            ->with(['items.book', 'processedBy', 'approvedBorrowing'])
            ->latest('request_date');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return view('khach_hang.yeu_cau_muon', [
            'borrowingRequests' => $query->paginate(8)->withQueryString(),
        ]);
    }

    public function storeBorrowingRequest(Request $request): RedirectResponse
    {
        $reader = $this->resolveReaderProfile($request);
        /** @var NhanVien $account */
        $account = $request->user();

        $data = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $book = Sach::query()->findOrFail($data['book_id']);

        if ($reader->status !== 'active') {
            throw ValidationException::withMessages([
                'book_id' => 'Tài khoản độc giả của bạn chưa ở trạng thái hoạt động.',
            ]);
        }

        if ($reader->expiry_date && $reader->expiry_date->isPast()) {
            throw ValidationException::withMessages([
                'book_id' => 'Thẻ độc giả của bạn đã hết hạn, vui lòng gia hạn trước khi gửi yêu cầu mượn.',
            ]);
        }

        if ($this->hasOverdueBorrowing($reader)) {
            throw ValidationException::withMessages([
                'book_id' => 'Bạn đang có phiếu mượn quá hạn, vui lòng hoàn tất trả sách trước khi gửi yêu cầu mới.',
            ]);
        }

        if ($this->outstandingFineForReader($reader) > 0.0001) {
            throw ValidationException::withMessages([
                'book_id' => 'Bạn vẫn còn khoản phạt chưa thanh toán, vui lòng xử lý trước khi gửi yêu cầu mượn.',
            ]);
        }

        if (! $book->is_active || $book->available_copies <= 0) {
            throw ValidationException::withMessages([
                'book_id' => 'Đầu sách này hiện chưa sẵn sàng để yêu cầu mượn.',
            ]);
        }

        if ((int) $data['quantity'] > (int) $book->available_copies) {
            throw ValidationException::withMessages([
                'quantity' => 'Số lượng yêu cầu vượt quá số bản hiện đang có sẵn.',
            ]);
        }

        $hasPendingRequestForBook = YeuCauMuon::query()
            ->where('reader_id', $reader->id)
            ->where('status', 'pending')
            ->whereHas('items', function (Builder $builder) use ($book): void {
                $builder->where('book_id', $book->id);
            })
            ->exists();

        if ($hasPendingRequestForBook) {
            throw ValidationException::withMessages([
                'book_id' => 'Bạn đang có một yêu cầu mượn chờ xử lý cho đầu sách này.',
            ]);
        }

        DB::transaction(function () use ($reader, $account, $book, $data): void {
            $borrowingRequest = YeuCauMuon::query()->create([
                'code' => $this->generateBorrowingRequestCode(),
                'reader_id' => $reader->id,
                'requested_by' => $account->id,
                'request_date' => now()->toDateString(),
                'status' => 'pending',
                'note' => $this->nullableString($data['note'] ?? null),
            ]);

            ChiTietYeuCauMuon::query()->create([
                'borrowing_request_id' => $borrowingRequest->id,
                'book_id' => $book->id,
                'quantity' => $data['quantity'],
            ]);
        });

        return back()->with('success', 'Đã gửi yêu cầu mượn sách. Nhân viên thư viện sẽ duyệt trong bước tiếp theo.');
    }

    public function cancelBorrowingRequest(Request $request, YeuCauMuon $borrowingRequest): RedirectResponse
    {
        $reader = $this->resolveReaderProfile($request);
        /** @var NhanVien $account */
        $account = $request->user();

        abort_unless((int) $borrowingRequest->reader_id === (int) $reader->id, 404);

        if ($borrowingRequest->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể hủy các yêu cầu đang chờ xử lý.');
        }

        $data = $request->validate([
            'cancel_note' => ['nullable', 'string', 'max:500'],
        ]);

        $borrowingRequest->update([
            'status' => 'cancelled',
            'processed_by' => $account->id,
            'processed_at' => now(),
            'processed_note' => $this->nullableString($data['cancel_note'] ?? null) ?? 'Khách hàng chủ động hủy yêu cầu.',
        ]);

        return back()->with('success', 'Đã hủy yêu cầu mượn.');
    }

    public function profile(Request $request): View
    {
        return view('khach_hang.ho_so', [
            'reader' => $this->resolveReaderProfile($request),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var NhanVien $account */
        $account = $request->user();
        $reader = $this->resolveReaderProfile($request);

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($account->id),
                Rule::unique('readers', 'email')->ignore($reader->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $normalizedName = Str::squish((string) $data['full_name']);
        $normalizedEmail = Str::lower(Str::squish((string) $data['email']));
        $normalizedPhone = $this->nullableString($data['phone'] ?? null);
        $normalizedAddress = $this->nullableString($data['address'] ?? null);

        $account->fill([
            'name' => $normalizedName,
            'email' => $normalizedEmail,
            'phone' => $normalizedPhone,
            'address' => $normalizedAddress,
        ]);

        if (! empty($data['new_password'])) {
            $account->password = $data['new_password'];
        }

        $account->save();

        $reader->fill([
            'full_name' => $normalizedName,
            'email' => $normalizedEmail,
            'phone' => $normalizedPhone,
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'address' => $normalizedAddress,
        ]);
        $reader->save();

        return back()->with('success', 'Đã cập nhật hồ sơ khách hàng.');
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

    private function hasOverdueBorrowing(DocGia $reader): bool
    {
        return $reader->borrowings()
            ->whereIn('status', ['borrowed', 'overdue'])
            ->whereDate('due_date', '<', now()->toDateString())
            ->exists();
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = Str::squish((string) $value);

        return $normalized === '' ? null : $normalized;
    }

    private function resolveReaderProfile(Request $request): DocGia
    {
        /** @var NhanVien|null $account */
        $account = $request->user();
        $reader = $account?->readerProfile;

        abort_unless($account?->isCustomer() && $reader, 403);

        return $reader;
    }

    private function generateBorrowingRequestCode(): string
    {
        do {
            $code = 'YC-'.now()->format('Ymd').'-'.random_int(1000, 9999);
        } while (YeuCauMuon::query()->where('code', $code)->exists());

        return $code;
    }
}
