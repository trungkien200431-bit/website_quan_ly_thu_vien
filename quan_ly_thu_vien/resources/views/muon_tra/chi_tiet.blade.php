@extends('bo_cuc.ung_dung')

@section('title', 'Chi tiết phiếu mượn')
@section('page-title', 'Chi tiết phiếu mượn: '.$borrowing->code)

@section('content')
<div class="row g-3 mb-3">
    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-header fw-semibold">Thông tin phiếu mượn</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-12 col-md-6"><strong>Mã phiếu:</strong> {{ $borrowing->code }}</div>
                    <div class="col-12 col-md-6"><strong>Thủ thư xử lý:</strong> {{ $borrowing->processedBy->name }}</div>
                    <div class="col-12 col-md-6"><strong>Độc giả:</strong> {{ $borrowing->reader->full_name }} ({{ $borrowing->reader->card_number }})</div>
                    <div class="col-12 col-md-6"><strong>Ngày mượn:</strong> {{ $borrowing->borrow_date?->format('d/m/Y') }}</div>
                    <div class="col-12 col-md-6"><strong>Hạn trả:</strong> {{ $borrowing->due_date?->format('d/m/Y') }}</div>
                    <div class="col-12 col-md-6"><strong>Ngày trả hoàn tất:</strong> {{ $borrowing->return_date?->format('d/m/Y') ?: '-' }}</div>
                    <div class="col-12 col-md-6">
                        <strong>Trạng thái:</strong>
                        <span class="badge {{ $borrowing->status === 'overdue' ? 'text-bg-danger' : ($borrowing->status === 'returned' ? 'text-bg-success' : ($borrowing->status === 'borrowed' ? 'text-bg-warning' : 'text-bg-secondary')) }}">
                            {{ strtoupper($borrowing->status) }}
                        </span>
                    </div>
                    <div class="col-12 col-md-6"><strong>Tổng tiền phạt:</strong> {{ number_format($borrowing->fine_total) }} đ</div>
                    <div class="col-12 col-md-6"><strong>Đã thanh toán:</strong> {{ number_format($paidFineTotal) }} đ</div>
                    <div class="col-12 col-md-6"><strong>Còn lại:</strong> {{ number_format($outstandingFine) }} đ</div>
                    <div class="col-12"><strong>Ghi chú:</strong> {{ $borrowing->note ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-semibold">Thao tác</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('muon_tra.index') }}" class="btn btn-outline-secondary">Quay về danh sách</a>
                <a href="{{ route('muon_tra.create') }}" class="btn btn-outline-primary">Tạo phiếu mượn mới</a>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header fw-semibold">Danh sách sách trong phiếu</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
            <tr>
                <th>Sách</th>
                <th>Số lượng</th>
                <th>Hạn trả</th>
                <th>Trạng thái</th>
                <th>Tiền phạt</th>
                <th>Đã thanh toán</th>
                <th>Còn lại</th>
                <th style="width: 260px">Nộp phạt nhanh</th>
            </tr>
            </thead>
            <tbody>
            @forelse($borrowing->items as $item)
                @php
                    $paid = $item->finePayments->sum('amount');
                    $outstanding = max(0, (float) $item->fine_amount - (float) $paid);
                @endphp
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $item->book->title }}</div>
                        <small class="text-muted">{{ $item->book->code }}</small>
                    </td>
                    <td>
                        Mượn: {{ $item->quantity }}<br>
                        Đã trả: {{ $item->returned_quantity }}<br>
                        Còn lại: <strong>{{ max(0, $item->quantity - $item->returned_quantity) }}</strong>
                    </td>
                    <td>{{ $item->due_date?->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $item->status === 'overdue' ? 'text-bg-danger' : ($item->status === 'returned' ? 'text-bg-success' : 'text-bg-warning') }}">
                            {{ strtoupper($item->status) }}
                        </span>
                    </td>
                    <td>{{ number_format($item->fine_amount) }} đ</td>
                    <td>{{ number_format($paid) }} đ</td>
                    <td>{{ number_format($outstanding) }} đ</td>
                    <td>
                        @if($outstanding > 0)
                            <form action="{{ route('muon_tra.thu_phat', [$borrowing, $item]) }}" method="POST" class="row g-2">
                                @csrf
                                <div class="col-4">
                                    <input type="number" name="amount" min="1" step="0.01" max="{{ $outstanding }}" class="form-control form-control-sm" placeholder="Số tiền" required>
                                </div>
                                <div class="col-4">
                                    <input type="date" name="paid_at" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-4 d-grid">
                                    <button class="btn btn-sm btn-success">Nộp</button>
                                </div>
                                <div class="col-12">
                                    <input type="text" name="note" class="form-control form-control-sm" placeholder="Ghi chú (không bắt buộc)">
                                </div>
                            </form>
                        @else
                            <span class="text-muted">Không có khoản cần nộp.</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Phiếu mượn chưa có sách.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(! in_array($borrowing->status, ['returned', 'cancelled'], true))
    <div class="card mb-3">
        <div class="card-header fw-semibold">Cập nhật trả sách</div>
        <div class="card-body">
            <form action="{{ route('muon_tra.tra_sach', $borrowing) }}" method="POST" class="row g-3">
                @csrf

                <div class="col-12 col-md-3">
                    <label class="form-label required">Ngày trả</label>
                    <input type="date" name="return_date" class="form-control" value="{{ old('return_date', now()->format('Y-m-d')) }}" required>
                </div>

                <div class="col-12 col-md-9">
                    <label class="form-label">Ghi chú trả sách</label>
                    <input type="text" name="return_note" class="form-control" value="{{ old('return_note') }}" placeholder="Ghi chú cho lần trả này">
                </div>

                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                            <tr>
                                <th>Sách</th>
                                <th>Đang mượn</th>
                                <th style="width: 220px">Số lượng trả lần này</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($borrowing->items as $item)
                                @php
                                    $remaining = max(0, $item->quantity - $item->returned_quantity);
                                @endphp
                                <tr>
                                    <td>{{ $item->book->title }} ({{ $item->book->code }})</td>
                                    <td>{{ $remaining }}</td>
                                    <td>
                                        <input type="number" class="form-control" name="returns[{{ $item->id }}]" min="0" max="{{ $remaining }}" value="{{ old('returns.'.$item->id, 0) }}">
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Thu phạt ngay (nếu có)</label>
                    <input type="number" min="0" step="0.01" name="payment_amount" class="form-control" value="{{ old('payment_amount') }}">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Ngày thu phạt</label>
                    <input type="date" name="paid_at" class="form-control" value="{{ old('paid_at', now()->format('Y-m-d')) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label">Ghi chú thu phạt</label>
                    <input type="text" name="payment_note" class="form-control" value="{{ old('payment_note') }}">
                </div>

                <div class="col-12">
                    <button class="btn btn-primary">Cập nhật trả sách</button>
                </div>
            </form>
        </div>
    </div>
@endif

@php
    $payments = $borrowing->items
        ->flatMap(fn ($item) => $item->finePayments->map(function ($payment) use ($item) {
            $payment->book_title = $item->book->title;
            return $payment;
        }))
        ->sortByDesc('paid_at');
@endphp

<div class="card">
    <div class="card-header fw-semibold">Lịch sử thanh toán tiền phạt</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Ngày thu</th>
                <th>Sách</th>
                <th>Số tiền</th>
                <th>Người thu</th>
                <th>Ghi chú</th>
            </tr>
            </thead>
            <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->paid_at?->format('d/m/Y') }}</td>
                    <td>{{ $payment->book_title }}</td>
                    <td>{{ number_format($payment->amount) }} đ</td>
                    <td>{{ $payment->paidBy->name ?? '-' }}</td>
                    <td>{{ $payment->note ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Chưa có giao dịch thanh toán phạt.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
