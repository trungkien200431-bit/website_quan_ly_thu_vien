@extends('bo_cuc.ung_dung')

@section('title', 'Mượn trả')
@section('page-title', 'Quản lý phiếu mượn trả')
@section('page-subtitle', 'Theo dõi các phiếu mượn đang hoạt động, xử lý trả sách và chuyển nhanh sang khu duyệt yêu cầu mượn của khách hàng.')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label">Từ khóa</label>
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Mã phiếu / tên độc giả / mã thẻ">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="borrowed" @selected(request('status') === 'borrowed')>Đang mượn</option>
                    <option value="overdue" @selected(request('status') === 'overdue')>Quá hạn</option>
                    <option value="returned" @selected(request('status') === 'returned')>Đã trả</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Đã hủy</option>
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex flex-wrap gap-2 justify-content-md-end">
                <button class="btn btn-primary">Lọc</button>
                <a href="{{ route('muon_tra.index') }}" class="btn btn-outline-secondary">Đặt lại</a>
                <a href="{{ route('muon_tra.yeu_cau') }}" class="btn btn-outline-primary">Yêu cầu mượn</a>
                <a href="{{ route('muon_tra.create') }}" class="btn btn-success">+ Tạo phiếu</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
            <tr>
                <th>Mã phiếu</th>
                <th>Độc giả</th>
                <th>Ngày mượn</th>
                <th>Hạn trả</th>
                <th>Tiền phạt</th>
                <th>Trạng thái</th>
                <th style="width: 90px">Chi tiết</th>
            </tr>
            </thead>
            <tbody>
            @forelse($borrowings as $borrowing)
                <tr>
                    <td>{{ $borrowing->code }}</td>
                    <td>
                        {{ $borrowing->reader->full_name }}<br>
                        <small class="text-muted">{{ $borrowing->reader->card_number }}</small>
                    </td>
                    <td>{{ $borrowing->borrow_date?->format('d/m/Y') }}</td>
                    <td>{{ $borrowing->due_date?->format('d/m/Y') }}</td>
                    <td>{{ number_format($borrowing->fine_total) }} đ</td>
                    <td>
                        <span class="badge {{ $borrowing->status === 'overdue' ? 'text-bg-danger' : ($borrowing->status === 'returned' ? 'text-bg-success' : ($borrowing->status === 'borrowed' ? 'text-bg-warning' : 'text-bg-secondary')) }}">
                            {{ match($borrowing->status) {
                                'borrowed' => 'Đang mượn',
                                'overdue' => 'Quá hạn',
                                'returned' => 'Đã trả',
                                'cancelled' => 'Đã hủy',
                                default => strtoupper($borrowing->status),
                            } }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('muon_tra.show', $borrowing) }}" class="btn btn-sm btn-primary">Xem</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Không có dữ liệu phiếu mượn.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $borrowings->links() }}
    </div>
</div>
@endsection
