@extends('bo_cuc.khach_hang')

@section('title', 'Lịch sử mượn | Quản Lý Thư Viện')
@section('page-title', 'Lịch sử mượn trả')
@section('page-subtitle', 'Theo dõi từng phiếu mượn, trạng thái hoàn trả, hạn trả và tiền phạt còn lại của chính bạn.')

@section('content')
<div class="section-card">
    <form method="GET" action="{{ route('khach_hang.lich_su_muon') }}" class="row g-3">
        <div class="col-md-4">
            <label class="form-label" for="status">Trạng thái</label>
            <select id="status" name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <option value="borrowed" @selected(request('status') === 'borrowed')>Đang mượn</option>
                <option value="overdue" @selected(request('status') === 'overdue')>Quá hạn</option>
                <option value="returned" @selected(request('status') === 'returned')>Đã trả</option>
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
                    <th>Phiếu mượn</th>
                    <th>Ngày mượn</th>
                    <th>Hạn trả</th>
                    <th>Nhân viên xử lý</th>
                    <th>Sách</th>
                    <th>Trạng thái</th>
                    <th>Phạt còn lại</th>
                </tr>
            </thead>
            <tbody>
                @forelse($borrowings as $borrowing)
                    @php
                        $remainingFine = $borrowing->items->sum(fn ($item) => max(0, (float) $item->fine_amount - (float) $item->finePayments->sum('amount')));
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $borrowing->code }}</td>
                        <td>{{ $borrowing->borrow_date?->format('d/m/Y') }}</td>
                        <td>{{ $borrowing->due_date?->format('d/m/Y') }}</td>
                        <td>{{ $borrowing->processedBy?->name ?? 'Đang cập nhật' }}</td>
                        <td class="muted-copy">
                            {{ $borrowing->items->map(fn ($item) => $item->book?->title ? $item->book->title.' x'.$item->quantity : null)->filter()->join(', ') ?: 'Chưa có dữ liệu' }}
                        </td>
                        <td>
                            <span class="status-pill {{ in_array($borrowing->status, ['borrowed', 'returned'], true) ? 'ok' : 'warn' }}">
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
                        <td colspan="7" class="text-center py-4 text-muted">Chưa có phiếu mượn nào phù hợp.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $borrowings->links() }}
</div>
@endsection
