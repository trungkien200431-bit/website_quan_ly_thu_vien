<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\PhieuMuon;
use App\Models\ChiTietPhieuMuon;
use App\Models\ThanhToanPhat;
use App\Models\DocGia;
use Illuminate\View\View;

class TongQuanController extends BoDieuKhien
{
    public function index(): View
    {
        ChiTietPhieuMuon::query()
            ->whereIn('status', ['borrowed', 'partially_returned'])
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        PhieuMuon::query()
            ->where('status', 'borrowed')
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        $stats = [
            'total_books' => Sach::count(),
            'available_copies' => Sach::sum('available_copies'),
            'active_readers' => DocGia::where('status', 'active')->count(),
            'open_borrowings' => PhieuMuon::whereIn('status', ['borrowed', 'overdue'])->count(),
            'overdue_items' => ChiTietPhieuMuon::where('status', 'overdue')->count(),
            'total_fines' => ChiTietPhieuMuon::sum('fine_amount'),
            'paid_fines' => ThanhToanPhat::sum('amount'),
        ];

        $latestBorrowings = PhieuMuon::with(['reader', 'processedBy'])
            ->latest('borrow_date')
            ->limit(8)
            ->get();

        $topBooks = Sach::query()
            ->select('books.id', 'books.title', 'books.code')
            ->join('borrowing_items', 'borrowing_items.book_id', '=', 'books.id')
            ->selectRaw('SUM(borrowing_items.quantity) as total_borrowed')
            ->groupBy('books.id', 'books.title', 'books.code')
            ->orderByDesc('total_borrowed')
            ->limit(5)
            ->get();

        return view('tong_quan.trang_chinh', compact('stats', 'latestBorrowings', 'topBooks'));
    }
}
