<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\PhieuMuon;
use App\Models\ChiTietPhieuMuon;
use App\Models\ThanhToanPhat;
use App\Models\DocGia;
use Carbon\Carbon;
use Illuminate\View\View;

class BaoCaoController extends BoDieuKhien
{
    public function index(): View
    {
        $today = Carbon::today();
        $this->syncOverdueStatuses($today);

        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();
        $startOfPreviousMonth = $today->copy()->subMonthNoOverflow()->startOfMonth();
        $endOfPreviousMonth = $today->copy()->subMonthNoOverflow()->endOfMonth();

        $totalFines = (float) ChiTietPhieuMuon::sum('fine_amount');
        $paidFines = (float) ThanhToanPhat::sum('amount');
        $inventoryTotal = (int) Sach::sum('total_copies');
        $inventoryAvailable = (int) Sach::sum('available_copies');
        $inventoryInUse = max(0, $inventoryTotal - $inventoryAvailable);

        $stats = [
            'borrowings_this_month' => PhieuMuon::whereBetween('borrow_date', [$startOfMonth, $endOfMonth])->count(),
            'returns_this_month' => PhieuMuon::whereBetween('return_date', [$startOfMonth, $endOfMonth])->count(),
            'overdue_borrowings' => PhieuMuon::where('status', 'overdue')->count(),
            'total_fines' => $totalFines,
            'paid_fines' => $paidFines,
            'outstanding_fines' => max(0, $totalFines - $paidFines),
            'catalog_titles' => Sach::count(),
            'active_readers' => DocGia::where('status', 'active')->count(),
            'inventory_in_use' => $inventoryInUse,
        ];

        $comparison = [
            'borrowings' => $this->buildTrend(
                PhieuMuon::whereBetween('borrow_date', [$startOfPreviousMonth, $endOfPreviousMonth])->count(),
                $stats['borrowings_this_month']
            ),
            'returns' => $this->buildTrend(
                PhieuMuon::whereBetween('return_date', [$startOfPreviousMonth, $endOfPreviousMonth])->count(),
                $stats['returns_this_month']
            ),
        ];

        $returnedItems = ChiTietPhieuMuon::whereNotNull('return_date')
            ->where('returned_quantity', '>', 0)
            ->count();

        $onTimeReturnedItems = ChiTietPhieuMuon::whereNotNull('return_date')
            ->where('returned_quantity', '>', 0)
            ->whereColumn('return_date', '<=', 'due_date')
            ->count();

        $health = [
            'fine_collection_rate' => $totalFines > 0 ? (int) round(($paidFines / $totalFines) * 100) : 100,
            'on_time_return_rate' => $returnedItems > 0 ? (int) round(($onTimeReturnedItems / $returnedItems) * 100) : 100,
            'inventory_utilization' => $inventoryTotal > 0 ? (int) round(($inventoryInUse / $inventoryTotal) * 100) : 0,
        ];

        $statusCounts = PhieuMuon::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusBreakdown = [
            ['label' => 'Đang mượn', 'value' => (int) ($statusCounts['borrowed'] ?? 0), 'tone' => 'borrowed'],
            ['label' => 'Quá hạn', 'value' => (int) ($statusCounts['overdue'] ?? 0), 'tone' => 'overdue'],
            ['label' => 'Đã trả', 'value' => (int) ($statusCounts['returned'] ?? 0), 'tone' => 'returned'],
        ];

        $overdueItems = ChiTietPhieuMuon::query()
            ->with(['borrowing.reader', 'book'])
            ->withSum('finePayments as paid_fine', 'amount')
            ->whereIn('status', ['overdue', 'borrowed', 'partially_returned'])
            ->whereDate('due_date', '<', $today->toDateString())
            ->orderBy('due_date')
            ->paginate(10);

        $topBorrowedBooks = Sach::query()
            ->select('books.id', 'books.code', 'books.title')
            ->join('borrowing_items', 'borrowing_items.book_id', '=', 'books.id')
            ->selectRaw('SUM(borrowing_items.quantity) as total_borrowed')
            ->groupBy('books.id', 'books.code', 'books.title')
            ->orderByDesc('total_borrowed')
            ->limit(5)
            ->get();

        $topBorrowingMax = max(1, (int) $topBorrowedBooks->max('total_borrowed'));

        $topBorrowedBooks = $topBorrowedBooks->map(function (Sach $book) use ($topBorrowingMax): Sach {
            $book->share = (int) round((((int) $book->total_borrowed) / $topBorrowingMax) * 100);

            return $book;
        });

        $monthlyBorrowingsRaw = PhieuMuon::query()
            ->whereYear('borrow_date', $today->year)
            ->get(['borrow_date'])
            ->groupBy(fn (PhieuMuon $borrowing): int => (int) $borrowing->borrow_date->format('n'))
            ->map(fn ($items): int => $items->count());

        $monthlyBorrowings = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyBorrowings[] = [
                'month' => $month,
                'label' => 'Tháng '.$month,
                'short_label' => 'T'.$month,
                'total' => (int) ($monthlyBorrowingsRaw[$month] ?? 0),
            ];
        }

        $peakMonth = collect($monthlyBorrowings)
            ->sortByDesc('total')
            ->first() ?? ['label' => 'Tháng hiện tại', 'short_label' => 'Hiện tại', 'total' => 0];

        $overdueReaderInsights = ChiTietPhieuMuon::query()
            ->with(['borrowing.reader'])
            ->withSum('finePayments as paid_fine', 'amount')
            ->whereIn('status', ['overdue', 'borrowed', 'partially_returned'])
            ->whereDate('due_date', '<', $today->toDateString())
            ->get()
            ->groupBy(fn (ChiTietPhieuMuon $item) => $item->borrowing?->reader?->id)
            ->map(function ($items): array {
                /** @var ChiTietPhieuMuon $firstItem */
                $firstItem = $items->first();
                $reader = $firstItem->borrowing->reader;

                return [
                    'reader' => $reader,
                    'overdue_titles' => $items->count(),
                    'outstanding_units' => $items->sum(fn (ChiTietPhieuMuon $item): int => $item->remaining_quantity),
                    'outstanding_fine' => $items->sum(
                        fn (ChiTietPhieuMuon $item): float => max(0, (float) $item->fine_amount - (float) ($item->paid_fine ?? 0))
                    ),
                ];
            })
            ->sortByDesc('outstanding_fine')
            ->take(4)
            ->values();

        $insights = [
            'report_date' => $today,
            'peak_month' => $peakMonth,
            'open_borrowings' => (int) (($statusCounts['borrowed'] ?? 0) + ($statusCounts['overdue'] ?? 0)),
        ];

        return view('bao_cao.tong_hop', compact(
            'comparison',
            'health',
            'insights',
            'monthlyBorrowings',
            'overdueItems',
            'overdueReaderInsights',
            'stats',
            'statusBreakdown',
            'topBorrowedBooks',
        ));
    }

    private function syncOverdueStatuses(Carbon $today): void
    {
        ChiTietPhieuMuon::query()
            ->whereIn('status', ['borrowed', 'partially_returned'])
            ->whereDate('due_date', '<', $today->toDateString())
            ->update(['status' => 'overdue']);

        PhieuMuon::query()
            ->where('status', 'borrowed')
            ->whereDate('due_date', '<', $today->toDateString())
            ->update(['status' => 'overdue']);
    }

    private function buildTrend(int|float $previous, int|float $current): array
    {
        $difference = $current - $previous;

        if ($previous > 0) {
            $percentage = (int) round((abs($difference) / $previous) * 100);
        } else {
            $percentage = $current > 0 ? 100 : 0;
        }

        return [
            'difference' => $difference,
            'percentage' => $percentage,
            'direction' => $difference > 0 ? 'up' : ($difference < 0 ? 'down' : 'steady'),
        ];
    }
}
