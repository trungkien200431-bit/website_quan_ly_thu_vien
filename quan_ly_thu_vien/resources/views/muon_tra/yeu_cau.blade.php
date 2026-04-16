@extends('bo_cuc.ung_dung')

@section('title', 'Yêu cầu mượn')
@section('page-title', 'Duyệt yêu cầu mượn')
@section('page-subtitle', 'Tiếp nhận yêu cầu từ khách hàng, duyệt nhanh để tạo phiếu mượn hoặc từ chối kèm lý do rõ ràng.')

@section('content')
<div class="row g-3 mb-3">
    <div class="col-6 col-xl-3">
        <div class="stat-card h-100">
            <div class="stat-label">Chờ duyệt</div>
            <div class="stat-value">{{ number_format($requestStats['pending']) }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card h-100">
            <div class="stat-label">Đã duyệt</div>
            <div class="stat-value">{{ number_format($requestStats['approved']) }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card h-100">
            <div class="stat-label">Từ chối</div>
            <div class="stat-value">{{ number_format($requestStats['rejected']) }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card h-100">
            <div class="stat-label">Khách đã hủy</div>
            <div class="stat-value">{{ number_format($requestStats['cancelled']) }}</div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-6">
                <label class="form-label">Từ khóa</label>
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Mã yêu cầu / tên độc giả / mã sách / tên sách">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="pending" @selected(request('status') === 'pending')>Chờ duyệt</option>
                    <option value="approved" @selected(request('status') === 'approved')>Đã duyệt</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Từ chối</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Đã hủy</option>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex flex-wrap gap-2 justify-content-md-end">
                <button class="btn btn-primary">Lọc</button>
                <a href="{{ route('muon_tra.yeu_cau') }}" class="btn btn-outline-secondary">Đặt lại</a>
                <a href="{{ route('muon_tra.index') }}" class="btn btn-outline-primary">Phiếu mượn</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
            <tr>
                <th>Mã yêu cầu</th>
                <th>Độc giả</th>
                <th>Sách yêu cầu</th>
                <th>Ngày gửi</th>
                <th>Trạng thái</th>
                <th>Người xử lý</th>
                <th style="width: 110px">Chi tiết</th>
            </tr>
            </thead>
            <tbody>
            @forelse($borrowingRequests as $borrowingRequest)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $borrowingRequest->code }}</div>
                        <small class="text-muted">Khách gửi: {{ $borrowingRequest->requestedBy->name ?? '-' }}</small>
                    </td>
                    <td>
                        <div>{{ $borrowingRequest->reader->full_name }}</div>
                        <small class="text-muted">{{ $borrowingRequest->reader->card_number }}</small>
                    </td>
                    <td>
                        @foreach($borrowingRequest->items as $item)
                            <div>
                                {{ $item->book->title ?? 'Sách đã xóa' }}
                                <span class="text-muted">x{{ $item->quantity }}</span>
                            </div>
                        @endforeach
                    </td>
                    <td>{{ $borrowingRequest->request_date?->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $borrowingRequest->status === 'approved' ? 'text-bg-success' : ($borrowingRequest->status === 'pending' ? 'text-bg-warning' : ($borrowingRequest->status === 'rejected' ? 'text-bg-danger' : 'text-bg-secondary')) }}">
                            {{ match($borrowingRequest->status) {
                                'pending' => 'Chờ duyệt',
                                'approved' => 'Đã duyệt',
                                'rejected' => 'Từ chối',
                                'cancelled' => 'Đã hủy',
                                default => ucfirst($borrowingRequest->status),
                            } }}
                        </span>
                    </td>
                    <td>
                        @if($borrowingRequest->status === 'cancelled' && $borrowingRequest->processedBy?->isCustomer())
                            Khách tự hủy
                        @else
                            {{ $borrowingRequest->processedBy->name ?? '-' }}
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('muon_tra.yeu_cau.chi_tiet', $borrowingRequest) }}" class="btn btn-sm btn-primary">Xem</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Chưa có yêu cầu mượn nào.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $borrowingRequests->links() }}
    </div>
</div>
@endsection
