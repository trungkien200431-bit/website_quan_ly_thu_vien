@extends('bo_cuc.ung_dung')

@section('title', 'Tổng quan')
@section('page-title', 'Bảng điều khiển')

@section('content')
<div class="card mb-3">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h5 class="mb-1">Xin chào, {{ auth()->user()?->name }}</h5>
            <div class="text-muted">Hôm nay hệ thống đang theo dõi {{ number_format($stats['open_borrowings']) }} phiếu mượn đang xử lý.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('muon_tra.create') }}" class="btn btn-primary btn-sm">+ Tạo phiếu mượn</a>
            <a href="{{ route('sach.create') }}" class="btn btn-outline-primary btn-sm">+ Thêm sách</a>
            <a href="{{ route('doc_gia.create') }}" class="btn btn-outline-secondary btn-sm">+ Thêm độc giả</a>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-label">Tổng đầu sách</div>
            <div class="stat-value">{{ number_format($stats['total_books']) }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-label">Bản sách sẵn có</div>
            <div class="stat-value">{{ number_format($stats['available_copies']) }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-label">Độc giả hoạt động</div>
            <div class="stat-value">{{ number_format($stats['active_readers']) }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-label">Phiếu mượn đang mở</div>
            <div class="stat-value">{{ number_format($stats['open_borrowings']) }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Sách quá hạn</div>
                <div class="h4 mb-0">{{ number_format($stats['overdue_items']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Tổng tiền phạt</div>
                <div class="h4 mb-0">{{ number_format($stats['total_fines']) }} đ</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Đã thu tiền phạt</div>
                <div class="h4 mb-0">{{ number_format($stats['paid_fines']) }} đ</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-7">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title mb-0">Phiếu mượn gần nhất</h5>
                    <a href="{{ route('muon_tra.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                        <tr>
                            <th>Mã phiếu</th>
                            <th>Độc giả</th>
                            <th>Hạn trả</th>
                            <th>Trạng thái</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($latestBorrowings as $borrowing)
                            <tr>
                                <td>
                                    <a href="{{ route('muon_tra.show', $borrowing) }}">{{ $borrowing->code }}</a>
                                </td>
                                <td>{{ $borrowing->reader->full_name }}</td>
                                <td>{{ $borrowing->due_date?->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge {{ $borrowing->status === 'overdue' ? 'text-bg-danger' : ($borrowing->status === 'returned' ? 'text-bg-success' : 'text-bg-warning') }}">
                                        {{ strtoupper($borrowing->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Chưa có dữ liệu.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Top sách được mượn nhiều</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Mã sách</th>
                            <th>Tên sách</th>
                            <th>Lượt mượn</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($topBooks as $book)
                            <tr>
                                <td>{{ $book->code }}</td>
                                <td>{{ $book->title }}</td>
                                <td>{{ number_format($book->total_borrowed) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Chưa có dữ liệu.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
