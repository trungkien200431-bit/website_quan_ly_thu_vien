@extends('bo_cuc.khach_hang')

@section('title', 'Tổng quan khách hàng | Quản Lý Thư Viện')
@section('page-title', 'Tổng quan tài khoản')
@section('page-subtitle', 'Theo dõi nhanh tình trạng thẻ độc giả, sách đang mượn, yêu cầu mượn đang chờ duyệt và các đầu sách đang sẵn có.')

@section('content')
<div class="row g-3">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-accent-teal">
            <div class="stat-label">Tổng phiếu mượn</div>
            <div class="stat-value">{{ number_format($customerStats['total_borrowings']) }}</div>
            <div class="stat-note">Toàn bộ giao dịch của bạn tại thư viện</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-accent-gold">
            <div class="stat-label">Phiếu đang mở</div>
            <div class="stat-value">{{ number_format($customerStats['active_borrowings']) }}</div>
            <div class="stat-note">Các phiếu chưa hoàn tất trả sách</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-accent-olive">
            <div class="stat-label">Yêu cầu chờ duyệt</div>
            <div class="stat-value">{{ number_format($customerStats['pending_requests']) }}</div>
            <div class="stat-note">Số yêu cầu mượn đang chờ thư viện xử lý</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-accent-rose">
            <div class="stat-label">Hạn thẻ độc giả</div>
            <div class="stat-value">{{ optional($customerStats['membership_expiry'])->format('d/m/Y') ?? 'N/A' }}</div>
            <div class="stat-note">Ngày hết hiệu lực của thẻ khách hàng</div>
        </div>
    </div>
</div>

<div class="section-card">
    <div class="section-head">
        <h3 class="section-title">Yêu cầu mượn gần đây</h3>
        <a href="{{ route('khach_hang.yeu_cau_muon') }}" class="btn btn-outline-secondary">Quản lý yêu cầu</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Mã yêu cầu</th>
                    <th>Ngày gửi</th>
                    <th>Sách</th>
                    <th>Trạng thái</th>
                    <th>Kết quả</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBorrowingRequests as $borrowingRequest)
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
                            {{ $borrowingRequest->approvedBorrowing?->code ?? ($borrowingRequest->processed_note ?: '-') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Bạn chưa gửi yêu cầu mượn nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="section-card">
    <div class="section-head">
        <h3 class="section-title">Lịch sử mượn gần đây</h3>
        <a href="{{ route('khach_hang.lich_su_muon') }}" class="btn btn-outline-secondary">Xem toàn bộ</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Mã phiếu</th>
                    <th>Ngày mượn</th>
                    <th>Hạn trả</th>
                    <th>Trạng thái</th>
                    <th>Tiền phạt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBorrowings as $borrowing)
                    @php
                        $paidFine = $borrowing->items->sum(fn ($item) => $item->finePayments->sum('amount'));
                        $remainingFine = max(0, (float) $borrowing->fine_total - (float) $paidFine);
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $borrowing->code }}</td>
                        <td>{{ $borrowing->borrow_date?->format('d/m/Y') }}</td>
                        <td>{{ $borrowing->due_date?->format('d/m/Y') }}</td>
                        <td>
                            <span class="status-pill {{ $borrowing->status === 'returned' ? 'ok' : ($borrowing->status === 'overdue' ? 'danger' : 'warn') }}">
                                {{ match($borrowing->status) {
                                    'borrowed' => 'Đang mượn',
                                    'returned' => 'Đã trả',
                                    'overdue' => 'Quá hạn',
                                    default => ucfirst($borrowing->status),
                                } }}
                            </span>
                        </td>
                        <td>{{ number_format($remainingFine, 0, ',', '.') }} đ</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Bạn chưa có giao dịch mượn trả nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="section-card">
    <div class="section-head">
        <h3 class="section-title">Sách đang sẵn có</h3>
        <a href="{{ route('khach_hang.sach') }}" class="btn btn-primary">Tra cứu toàn bộ sách</a>
    </div>

    <div class="row g-3 mt-1">
        @forelse($catalogHighlights as $book)
            <div class="col-md-6 col-xl-4">
                <article class="book-card">
                    <div class="d-flex justify-content-between gap-2 align-items-start">
                        <div class="book-title">{{ $book->title }}</div>
                        <span class="availability-pill ok">
                            <i class="bi bi-check2-circle"></i>{{ $book->available_copies }} bản
                        </span>
                    </div>
                    <div class="book-meta mt-2">
                        Tác giả: {{ $book->authors->pluck('name')->join(', ') ?: 'Đang cập nhật' }}<br>
                        Nhà xuất bản: {{ $book->publisher?->name ?? 'Đang cập nhật' }}<br>
                        Mã sách: {{ $book->code }}
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="book-card muted-copy">Hiện chưa có sách đang sẵn có để phục vụ khách hàng.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
