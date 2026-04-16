@extends('bo_cuc.ung_dung')

@section('title', 'Chi tiết yêu cầu mượn')
@section('page-title', 'Chi tiết yêu cầu: '.$borrowingRequest->code)
@section('page-subtitle', 'Kiểm tra tình trạng độc giả, số lượng sách còn sẵn và quyết định duyệt hay từ chối yêu cầu mượn.')

@section('content')
@php
    $statusLabel = match($borrowingRequest->status) {
        'pending' => 'Chờ duyệt',
        'approved' => 'Đã duyệt',
        'rejected' => 'Từ chối',
        'cancelled' => 'Đã hủy',
        default => ucfirst($borrowingRequest->status),
    };

    $statusBadgeClass = match($borrowingRequest->status) {
        'approved' => 'text-bg-success',
        'pending' => 'text-bg-warning',
        'rejected' => 'text-bg-danger',
        default => 'text-bg-secondary',
    };
@endphp

<div class="row g-3 mb-3">
    <div class="col-12 col-xl-8">
        <div class="card h-100">
            <div class="card-header fw-semibold">Thông tin yêu cầu mượn</div>
            <div class="card-body">
                <div class="surface-note mb-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <span class="info-label">Tóm tắt</span>
                            <div class="info-value">Yêu cầu {{ $borrowingRequest->code }} của độc giả {{ $borrowingRequest->reader->full_name }}</div>
                        </div>
                        <span class="badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Mã yêu cầu</span>
                        <div class="info-value">{{ $borrowingRequest->code }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Trạng thái</span>
                        <div class="info-value">{{ $statusLabel }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Độc giả</span>
                        <div class="info-value">{{ $borrowingRequest->reader->full_name }} ({{ $borrowingRequest->reader->card_number }})</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Ngày gửi</span>
                        <div class="info-value">{{ $borrowingRequest->request_date?->format('d/m/Y') }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tài khoản gửi</span>
                        <div class="info-value">{{ $borrowingRequest->requestedBy->name ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Hết hạn thẻ</span>
                        <div class="info-value">{{ $borrowingRequest->reader->expiry_date?->format('d/m/Y') ?: '-' }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Người xử lý</span>
                        <div class="info-value">
                            @if($borrowingRequest->status === 'cancelled' && $borrowingRequest->processedBy?->isCustomer())
                                Khách tự hủy
                            @else
                                {{ $borrowingRequest->processedBy->name ?? '-' }}
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Thời điểm xử lý</span>
                        <div class="info-value">{{ $borrowingRequest->processed_at?->format('d/m/Y H:i') ?: '-' }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Hạn trả khi duyệt</span>
                        <div class="info-value">{{ $borrowingRequest->due_date?->format('d/m/Y') ?: '-' }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phiếu mượn đã tạo</span>
                        <div class="info-value">
                            @if($borrowingRequest->approvedBorrowing)
                                <a href="{{ route('muon_tra.show', $borrowingRequest->approvedBorrowing) }}" class="link-primary fw-semibold">
                                    {{ $borrowingRequest->approvedBorrowing->code }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="info-item full">
                        <span class="info-label">Ghi chú của khách</span>
                        <div class="info-value">{{ $borrowingRequest->note ?: '-' }}</div>
                    </div>
                    <div class="info-item full">
                        <span class="info-label">Phản hồi xử lý</span>
                        <div class="info-value">{{ $borrowingRequest->processed_note ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card h-100">
            <div class="card-header fw-semibold">Điều hướng</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('muon_tra.yeu_cau') }}" class="btn btn-outline-secondary">Quay về danh sách yêu cầu</a>
                <a href="{{ route('muon_tra.index') }}" class="btn btn-outline-primary">Xem danh sách phiếu mượn</a>
                @if($borrowingRequest->approvedBorrowing)
                    <a href="{{ route('muon_tra.show', $borrowingRequest->approvedBorrowing) }}" class="btn btn-success">Mở phiếu mượn đã tạo</a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header fw-semibold">Danh sách sách trong yêu cầu</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
            <tr>
                <th>Mã sách</th>
                <th>Tên sách</th>
                <th>Tác giả</th>
                <th>Còn sẵn</th>
                <th>Số lượng yêu cầu</th>
            </tr>
            </thead>
            <tbody>
            @forelse($borrowingRequest->items as $item)
                <tr>
                    <td>{{ $item->book->code ?? '-' }}</td>
                    <td>{{ $item->book->title ?? 'Sách đã bị xóa' }}</td>
                    <td>{{ $item->book?->authors?->pluck('name')->join(', ') ?: '-' }}</td>
                    <td>{{ $item->book->available_copies ?? 0 }}</td>
                    <td>{{ $item->quantity }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Yêu cầu này chưa có đầu sách hợp lệ.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($borrowingRequest->status === 'pending')
    <div class="row g-3">
        <div class="col-12 col-xl-7">
            <div class="action-panel success">
                <div class="card-header fw-semibold rounded-top-3 border-0 px-0 pt-0 pb-3 bg-transparent">Duyệt yêu cầu và tạo phiếu mượn</div>
                <div>
                    <p class="text-muted mb-3">Khối này dành cho quyết định chấp nhận yêu cầu. Hệ thống sẽ tạo phiếu mượn thật và tự trừ số lượng sách còn sẵn.</p>
                    <form action="{{ route('muon_tra.yeu_cau.duyet', $borrowingRequest) }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-12 col-md-6">
                            <label class="form-label required">Hạn trả</label>
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date', now()->addDays(14)->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ghi chú duyệt</label>
                            <textarea name="process_note" rows="3" class="form-control" placeholder="Ví dụ: Ưu tiên phục vụ sinh viên đang làm khóa luận.">{{ old('process_note') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">Duyệt và tạo phiếu mượn</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="action-panel danger">
                <div class="card-header fw-semibold rounded-top-3 border-0 px-0 pt-0 pb-3 bg-transparent">Từ chối yêu cầu</div>
                <div>
                    <p class="text-muted mb-3">Khối này dành cho trường hợp không thể phục vụ. Nhập lý do rõ ràng để khách hàng nhìn thấy và biết cần xử lý gì tiếp theo.</p>
                    <form action="{{ route('muon_tra.yeu_cau.tu_choi', $borrowingRequest) }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label class="form-label required">Lý do từ chối</label>
                            <textarea name="process_note" rows="5" class="form-control" placeholder="Ví dụ: sách đã hết, thẻ độc giả hết hạn hoặc còn khoản phạt chưa thanh toán." required>{{ old('process_note') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-outline-danger">Từ chối yêu cầu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
