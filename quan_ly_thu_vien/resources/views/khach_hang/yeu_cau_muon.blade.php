@extends('bo_cuc.khach_hang')

@section('title', 'Yêu cầu mượn | Quản Lý Thư Viện')
@section('page-title', 'Yêu cầu mượn')
@section('page-subtitle', 'Theo dõi các yêu cầu mượn bạn đã gửi và hủy nhanh những yêu cầu vẫn đang chờ thư viện xử lý.')

@section('content')
<div class="section-card">
    <form method="GET" action="{{ route('khach_hang.yeu_cau_muon') }}" class="row g-3">
        <div class="col-md-4">
            <label class="form-label" for="status">Trạng thái</label>
            <select id="status" name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <option value="pending" @selected(request('status') === 'pending')>Chờ duyệt</option>
                <option value="approved" @selected(request('status') === 'approved')>Đã duyệt</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Từ chối</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Đã hủy</option>
            </select>
        </div>
        <div class="col-md-2 d-grid align-items-end">
            <button type="submit" class="btn btn-primary">Lọc</button>
        </div>
    </form>
</div>

<div class="section-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Mã yêu cầu</th>
                    <th>Ngày gửi</th>
                    <th>Sách</th>
                    <th>Trạng thái</th>
                    <th>Phản hồi</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($borrowingRequests as $borrowingRequest)
                    <tr>
                        <td class="fw-semibold">{{ $borrowingRequest->code }}</td>
                        <td>{{ $borrowingRequest->request_date?->format('d/m/Y') }}</td>
                        <td class="muted-copy">
                            {{ $borrowingRequest->items->map(fn ($item) => $item->book?->title ? $item->book->title.' x'.$item->quantity : null)->filter()->join(', ') ?: 'Chưa có dữ liệu' }}
                        </td>
                        <td>
                            <span class="status-pill {{ $borrowingRequest->status === 'approved' ? 'ok' : ($borrowingRequest->status === 'pending' ? 'warn' : 'neutral') }}">
                                {{ match($borrowingRequest->status) {
                                    'pending' => 'Chờ duyệt',
                                    'approved' => 'Đã duyệt',
                                    'rejected' => 'Từ chối',
                                    'cancelled' => 'Đã hủy',
                                    default => ucfirst($borrowingRequest->status),
                                } }}
                            </span>
                        </td>
                        <td class="muted-copy">
                            @if($borrowingRequest->approvedBorrowing)
                                Đã tạo phiếu {{ $borrowingRequest->approvedBorrowing->code }}
                            @else
                                {{ $borrowingRequest->processed_note ?: '-' }}
                            @endif
                        </td>
                        <td>
                            @if($borrowingRequest->status === 'pending')
                                <form action="{{ route('khach_hang.yeu_cau_muon.huy', $borrowingRequest) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hủy yêu cầu</button>
                                </form>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Bạn chưa có yêu cầu mượn nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $borrowingRequests->links() }}
</div>
@endsection
