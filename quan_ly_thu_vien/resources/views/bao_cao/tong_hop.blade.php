@extends('bo_cuc.ung_dung')

@section('title', 'Báo cáo')
@section('page-title', 'Báo cáo điều hành')

@section('content')
@php
    $maxBorrowings = max(1, collect($monthlyBorrowings)->max('total'));
    $statusTotal = max(1, collect($statusBreakdown)->sum('value'));
    $reportDate = $insights['report_date'];
@endphp

<style>
    .report-stage {
        display: grid;
        gap: 18px;
    }

    .report-hero {
        position: relative;
        overflow: hidden;
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(300px, 0.8fr);
        gap: 16px;
        padding: 24px;
        border: 1px solid rgba(54, 71, 74, 0.12);
        border-radius: 24px;
        background:
            radial-gradient(circle at 0% 0%, rgba(225, 193, 145, 0.24), transparent 18rem),
            radial-gradient(circle at 100% 0%, rgba(136, 180, 177, 0.2), transparent 18rem),
            linear-gradient(145deg, #fefaf2, #f4eee2 55%, #edf6f3 100%);
        box-shadow: 0 20px 44px rgba(53, 50, 43, 0.1);
    }

    .report-hero::after {
        content: '';
        position: absolute;
        inset: auto -100px -120px auto;
        width: 280px;
        height: 280px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(35, 73, 82, 0.12), transparent 68%);
        pointer-events: none;
    }

    .report-hero-main {
        display: flex;
        align-items: center;
    }

    .report-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .report-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 14px;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 700;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .report-action:hover {
        transform: translateY(-1px);
    }

    .report-action.primary {
        color: #fff;
        background: linear-gradient(135deg, #2f5a5d, #244447);
        box-shadow: 0 14px 24px rgba(36, 68, 71, 0.18);
    }

    .report-action.secondary {
        color: #355359;
        border: 1px solid rgba(83, 96, 99, 0.14);
        background: rgba(255, 255, 255, 0.66);
    }

    .report-board {
        display: grid;
        gap: 12px;
        align-content: start;
    }

    .signal-card {
        border: 1px solid rgba(53, 71, 74, 0.12);
        border-radius: 18px;
        padding: 16px;
        background: rgba(255, 255, 255, 0.72);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.74);
    }

    .signal-card .signal-label {
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #7d705d;
    }

    .signal-card .signal-value {
        margin-top: 8px;
        font-family: 'Fraunces', serif;
        font-size: 1.7rem;
        color: #26454c;
    }

    .signal-card .signal-note {
        margin-top: 5px;
        color: #617074;
        font-size: 0.88rem;
        line-height: 1.6;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
    }

    .metric-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(77, 70, 57, 0.12);
        border-radius: 20px;
        padding: 16px;
        background: linear-gradient(180deg, rgba(255, 253, 248, 0.98), rgba(248, 242, 232, 0.92));
        box-shadow: 0 16px 28px rgba(62, 54, 43, 0.08);
    }

    .metric-card::after {
        content: '';
        position: absolute;
        inset: auto -26px -38px auto;
        width: 96px;
        height: 96px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.42), transparent 65%);
    }

    .metric-label {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #7b6b57;
    }

    .metric-value {
        margin-top: 8px;
        font-family: 'Fraunces', serif;
        font-size: 1.7rem;
        line-height: 1.05;
        color: #244447;
    }

    .metric-note,
    .metric-trend {
        margin-top: 6px;
        color: #607073;
        font-size: 0.85rem;
    }

    .metric-trend.up {
        color: #1e6a4c;
    }

    .metric-trend.down {
        color: #9b4d3d;
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(320px, 0.9fr);
        gap: 16px;
    }

    .report-card {
        border: 1px solid rgba(77, 70, 57, 0.12);
        border-radius: 22px;
        background: linear-gradient(180deg, rgba(255, 253, 248, 0.98), rgba(248, 242, 232, 0.94));
        box-shadow: 0 16px 28px rgba(62, 54, 43, 0.08);
        overflow: hidden;
    }

    .report-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 18px 18px 0;
        flex-wrap: wrap;
    }

    .report-card-title {
        margin: 0;
        font-family: 'Fraunces', serif;
        font-size: 1.24rem;
        color: #2d4145;
    }

    .report-card-sub {
        margin: 6px 0 0;
        color: #677579;
        font-size: 0.9rem;
    }

    .report-card-body {
        padding: 18px;
    }

    .chart-wrap {
        display: grid;
        gap: 18px;
    }

    .flow-chart {
        min-height: 260px;
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        align-items: end;
        gap: 10px;
        padding: 14px 0 6px;
    }

    .flow-column {
        display: grid;
        justify-items: center;
        gap: 8px;
    }

    .flow-bar {
        position: relative;
        width: 100%;
        max-width: 42px;
        min-height: 10px;
        border-radius: 14px 14px 10px 10px;
        background: linear-gradient(180deg, #2f5a5d, #5f7d7e 78%, #c5d9d4 100%);
        box-shadow: 0 14px 24px rgba(47, 90, 93, 0.22);
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 8px;
    }

    .flow-bar span {
        position: absolute;
        top: -28px;
        font-size: 0.74rem;
        font-weight: 700;
        color: #506367;
    }

    .flow-label {
        font-size: 0.76rem;
        font-weight: 700;
        color: #6d665a;
    }

    .chart-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .summary-card {
        border-radius: 16px;
        border: 1px solid rgba(83, 96, 99, 0.1);
        background: rgba(255, 255, 255, 0.64);
        padding: 14px;
    }

    .summary-label {
        color: #776c5c;
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .summary-value {
        margin-top: 6px;
        font-family: 'Fraunces', serif;
        font-size: 1.26rem;
        color: #28484e;
    }

    .summary-note {
        margin-top: 4px;
        color: #617074;
        font-size: 0.83rem;
    }

    .gauge-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .gauge-card {
        border-radius: 18px;
        border: 1px solid rgba(83, 96, 99, 0.1);
        background: rgba(255, 255, 255, 0.64);
        padding: 14px;
        text-align: center;
    }

    .gauge-ring {
        --value: 0;
        --tone: #2f5a5d;
        width: 92px;
        height: 92px;
        margin: 0 auto 12px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        background: conic-gradient(var(--tone) 0 calc(var(--value) * 1%), rgba(213, 219, 215, 0.85) calc(var(--value) * 1%) 100%);
        position: relative;
    }

    .gauge-ring::after {
        content: '';
        position: absolute;
        inset: 10px;
        border-radius: 999px;
        background: #fcfbf7;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
    }

    .gauge-value {
        position: relative;
        z-index: 1;
        font-size: 1.18rem;
        font-weight: 800;
        color: #29484e;
    }

    .gauge-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #5c6d72;
    }

    .gauge-note {
        margin-top: 4px;
        font-size: 0.78rem;
        color: #7d898d;
    }

    .status-stack {
        display: grid;
        gap: 12px;
        margin-top: 18px;
    }

    .status-row {
        display: grid;
        gap: 8px;
    }

    .status-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        font-size: 0.9rem;
        color: #47575c;
    }

    .status-head strong {
        font-size: 0.95rem;
        color: #263f45;
    }

    .status-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
    }

    .status-dot.borrowed {
        background: #2f5a5d;
    }

    .status-dot.overdue {
        background: #c57f47;
    }

    .status-dot.returned {
        background: #4d7b56;
    }

    .status-track {
        height: 10px;
        border-radius: 999px;
        background: rgba(217, 207, 188, 0.58);
        overflow: hidden;
    }

    .status-fill {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #2f5a5d, #6a8b8b);
    }

    .split-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(300px, 0.85fr);
        gap: 16px;
    }

    .ranking-list,
    .reader-list {
        display: grid;
        gap: 14px;
    }

    .ranking-item,
    .reader-item {
        border-radius: 18px;
        border: 1px solid rgba(83, 96, 99, 0.1);
        background: rgba(255, 255, 255, 0.64);
        padding: 14px;
    }

    .ranking-head,
    .reader-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .rank-badge {
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #26454c;
        background: rgba(177, 109, 66, 0.12);
    }

    .ranking-book strong,
    .reader-name {
        display: block;
        color: #29444c;
        font-size: 0.96rem;
    }

    .ranking-book span,
    .reader-code {
        color: #7a878b;
        font-size: 0.82rem;
    }

    .ranking-total,
    .reader-total {
        color: #26454c;
        font-weight: 800;
        white-space: nowrap;
    }

    .ranking-track {
        margin-top: 12px;
        height: 10px;
        border-radius: 999px;
        background: rgba(217, 207, 188, 0.58);
        overflow: hidden;
    }

    .ranking-track span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #b16d42, #d0a16d);
    }

    .reader-metrics {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 12px;
    }

    .reader-metric {
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 0.78rem;
        font-weight: 700;
        background: rgba(47, 90, 93, 0.1);
        color: #35555a;
    }

    .empty-state {
        border-radius: 18px;
        border: 1px dashed rgba(83, 96, 99, 0.26);
        padding: 20px;
        text-align: center;
        color: #6b7b7e;
        background: rgba(255, 255, 255, 0.44);
    }

    .report-table thead th {
        white-space: nowrap;
    }

    .due-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        background: rgba(197, 127, 71, 0.12);
        color: #91552d;
    }

    .money-stack {
        display: grid;
        gap: 2px;
    }

    .money-stack .sub-money {
        font-size: 0.78rem;
        color: #7a878b;
    }

    @@media (max-width: 1399px) {
        .metrics-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @@media (max-width: 1199px) {
        .report-hero,
        .analytics-grid,
        .split-grid {
            grid-template-columns: 1fr;
        }
    }

    @@media (max-width: 767px) {
        .metrics-grid,
        .chart-summary,
        .gauge-grid {
            grid-template-columns: 1fr;
        }

        .report-hero,
        .report-card-body,
        .report-card-head {
            padding-left: 14px;
            padding-right: 14px;
        }

        .flow-chart {
            gap: 8px;
        }

        .flow-bar {
            max-width: 100%;
        }
    }
</style>

<div class="report-stage">
    <section class="report-hero">
        <div>
            <div class="report-hero-main">
                <div class="report-actions">
                    <a href="{{ route('tong_quan') }}" class="report-action primary">
                        <i class="bi bi-stars"></i>Về tổng quan
                    </a>
                    <a href="{{ route('muon_tra.index') }}" class="report-action secondary">
                        <i class="bi bi-arrow-left-right"></i>Mở danh sách mượn trả
                    </a>
                </div>
            </div>
        </div>

        <div class="report-board">
            <article class="signal-card">
                <div class="signal-label">Đỉnh hoạt động trong năm</div>
                <div class="signal-value">{{ $insights['peak_month']['short_label'] }}</div>
            </article>
            <article class="signal-card">
                <div class="signal-label">Giao dịch đang mở</div>
                <div class="signal-value">{{ number_format($insights['open_borrowings']) }}</div>
            </article>
            <article class="signal-card">
                <div class="signal-label">Ngày lập báo cáo</div>
                <div class="signal-value">{{ $reportDate->format('d/m/Y') }}</div>
            </article>
        </div>
    </section>

    <section class="metrics-grid">
        <article class="metric-card">
            <div class="metric-label">Mượn trong tháng</div>
            <div class="metric-value">{{ number_format($stats['borrowings_this_month']) }}</div>
            <div class="metric-trend {{ $comparison['borrowings']['direction'] }}">
                @if($comparison['borrowings']['direction'] === 'up')
                    Tăng {{ $comparison['borrowings']['percentage'] }}% so với tháng trước
                @elseif($comparison['borrowings']['direction'] === 'down')
                    Giảm {{ $comparison['borrowings']['percentage'] }}% so với tháng trước
                @else
                    Giữ nguyên so với tháng trước
                @endif
            </div>
        </article>

        <article class="metric-card">
            <div class="metric-label">Trả trong tháng</div>
            <div class="metric-value">{{ number_format($stats['returns_this_month']) }}</div>
            <div class="metric-trend {{ $comparison['returns']['direction'] }}">
                @if($comparison['returns']['direction'] === 'up')
                    Tăng {{ $comparison['returns']['percentage'] }}% so với tháng trước
                @elseif($comparison['returns']['direction'] === 'down')
                    Giảm {{ $comparison['returns']['percentage'] }}% so với tháng trước
                @else
                    Giữ nguyên so với tháng trước
                @endif
            </div>
        </article>

        <article class="metric-card">
            <div class="metric-label">Phiếu quá hạn</div>
            <div class="metric-value">{{ number_format($stats['overdue_borrowings']) }}</div>
            <div class="metric-note">Ưu tiên xử lý để giảm áp lực vận hành cuối ngày.</div>
        </article>

        <article class="metric-card">
            <div class="metric-label">Độc giả hoạt động</div>
            <div class="metric-value">{{ number_format($stats['active_readers']) }}</div>
            <div class="metric-note">{{ number_format($stats['catalog_titles']) }} đầu sách đang phục vụ.</div>
        </article>

        <article class="metric-card">
            <div class="metric-label">Tổng tiền phạt</div>
            <div class="metric-value">{{ number_format($stats['total_fines']) }} đ</div>
            <div class="metric-note">Đã thu {{ number_format($stats['paid_fines']) }} đ từ các khoản phát sinh.</div>
        </article>

        <article class="metric-card">
            <div class="metric-label">Còn phải thu</div>
            <div class="metric-value">{{ number_format($stats['outstanding_fines']) }} đ</div>
            <div class="metric-note">{{ number_format($stats['inventory_in_use']) }} bản sách hiện đang ở ngoài kho.</div>
        </article>
    </section>

    <section class="analytics-grid">
        <article class="report-card">
            <div class="report-card-head">
                <div>
                    <h4 class="report-card-title">Nhịp mượn theo tháng</h4>
                </div>
            </div>
            <div class="report-card-body chart-wrap">
                <div class="flow-chart">
                    @foreach($monthlyBorrowings as $month)
                        @php
                            $height = $month['total'] > 0 ? max(12, (int) round(($month['total'] / $maxBorrowings) * 190)) : 10;
                        @endphp
                        <div class="flow-column">
                            <div class="flow-bar" style="height: {{ $height }}px;">
                                <span>{{ number_format($month['total']) }}</span>
                            </div>
                            <div class="flow-label">{{ $month['short_label'] }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="chart-summary">
                    <div class="summary-card">
                        <div class="summary-label">Cao điểm năm</div>
                        <div class="summary-value">{{ $insights['peak_month']['label'] }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Tháng hiện tại</div>
                        <div class="summary-value">{{ number_format($stats['borrowings_this_month']) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Giao dịch mở</div>
                        <div class="summary-value">{{ number_format($insights['open_borrowings']) }}</div>
                    </div>
                </div>
            </div>
        </article>

        <article class="report-card">
            <div class="report-card-head">
                <div>
                    <h4 class="report-card-title">Sức khỏe vận hành</h4>
                </div>
            </div>
            <div class="report-card-body">
                <div class="gauge-grid">
                    <div class="gauge-card">
                        <div class="gauge-ring" style="--value: {{ $health['fine_collection_rate'] }}; --tone: #b16d42;">
                            <div class="gauge-value">{{ $health['fine_collection_rate'] }}%</div>
                        </div>
                        <div class="gauge-label">Tỷ lệ thu phạt</div>
                    </div>
                    <div class="gauge-card">
                        <div class="gauge-ring" style="--value: {{ $health['on_time_return_rate'] }}; --tone: #4d7b56;">
                            <div class="gauge-value">{{ $health['on_time_return_rate'] }}%</div>
                        </div>
                        <div class="gauge-label">Trả đúng hạn</div>
                    </div>
                    <div class="gauge-card">
                        <div class="gauge-ring" style="--value: {{ $health['inventory_utilization'] }}; --tone: #2f5a5d;">
                            <div class="gauge-value">{{ $health['inventory_utilization'] }}%</div>
                        </div>
                        <div class="gauge-label">Mức sử dụng kho</div>
                    </div>
                </div>

                <div class="status-stack">
                    @foreach($statusBreakdown as $status)
                        @php
                            $fill = $statusTotal > 0 ? round(($status['value'] / $statusTotal) * 100) : 0;
                        @endphp
                        <div class="status-row">
                            <div class="status-head">
                                <span class="status-label">
                                    <span class="status-dot {{ $status['tone'] }}"></span>{{ $status['label'] }}
                                </span>
                                <strong>{{ number_format($status['value']) }}</strong>
                            </div>
                            <div class="status-track">
                                <span class="status-fill" style="width: {{ $fill }}%;"></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>
    </section>

    <section class="split-grid">
        <article class="report-card">
            <div class="report-card-head">
                <div>
                    <h4 class="report-card-title">Top sách được mượn nhiều</h4>
                </div>
            </div>
            <div class="report-card-body">
                @if($topBorrowedBooks->isNotEmpty())
                    <div class="ranking-list">
                        @foreach($topBorrowedBooks as $book)
                            <div class="ranking-item">
                                <div class="ranking-head">
                                    <div class="d-flex gap-3">
                                        <span class="rank-badge">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        <div class="ranking-book">
                                            <strong>{{ $book->title }}</strong>
                                            <span>{{ $book->code }}</span>
                                        </div>
                                    </div>
                                    <div class="ranking-total">{{ number_format($book->total_borrowed) }} lượt</div>
                                </div>
                                <div class="ranking-track">
                                    <span style="width: {{ $book->share }}%;"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">Chưa có dữ liệu mượn để xếp hạng đầu sách.</div>
                @endif
            </div>
        </article>

        <article class="report-card">
            <div class="report-card-head">
                <div>
                    <h4 class="report-card-title">Điểm nóng độc giả quá hạn</h4>
                </div>
            </div>
            <div class="report-card-body">
                @if($overdueReaderInsights->isNotEmpty())
                    <div class="reader-list">
                        @foreach($overdueReaderInsights as $readerInsight)
                            <div class="reader-item">
                                <div class="reader-head">
                                    <div>
                                        <span class="reader-name">{{ $readerInsight['reader']->full_name }}</span>
                                        <span class="reader-code">{{ $readerInsight['reader']->card_number }}</span>
                                    </div>
                                    <div class="reader-total">{{ number_format($readerInsight['outstanding_fine']) }} đ</div>
                                </div>
                                <div class="reader-metrics">
                                    <span class="reader-metric">{{ number_format($readerInsight['overdue_titles']) }} đầu sách</span>
                                    <span class="reader-metric">{{ number_format($readerInsight['outstanding_units']) }} bản chưa trả đủ</span>
                                    <span class="reader-metric">Cần nhắc nhở ưu tiên</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">Hiện chưa có độc giả nào rơi vào vùng quá hạn cần cảnh báo.</div>
                @endif
            </div>
        </article>
    </section>

    <article class="report-card">
        <div class="report-card-head">
            <div>
                <h4 class="report-card-title">Danh sách sách quá hạn chưa hoàn tất</h4>
            </div>
        </div>
        <div class="report-card-body table-responsive">
            <table class="table table-bordered align-middle report-table mb-0">
                <thead>
                <tr>
                    <th>Phiếu mượn</th>
                    <th>Độc giả</th>
                    <th>Sách</th>
                    <th>Quá hạn</th>
                    <th>Còn lại</th>
                    <th>Đã thu</th>
                    <th>Còn phải thu</th>
                </tr>
                </thead>
                <tbody>
                @forelse($overdueItems as $item)
                    @php
                        $daysOverdue = $item->due_date ? max(0, $item->due_date->diffInDays($reportDate)) : 0;
                        $outstandingFine = max(0, (float) $item->fine_amount - (float) ($item->paid_fine ?? 0));
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('muon_tra.show', $item->borrowing) }}">{{ $item->borrowing->code }}</a>
                        </td>
                        <td>{{ $item->borrowing->reader->full_name }}</td>
                        <td>{{ $item->book->title }}</td>
                        <td>
                            <span class="due-chip">
                                <i class="bi bi-clock-history"></i>{{ $daysOverdue }} ngày
                            </span>
                        </td>
                        <td>{{ number_format($item->remaining_quantity) }}</td>
                        <td>
                            <div class="money-stack">
                                <strong>{{ number_format($item->paid_fine ?? 0) }} đ</strong>
                                <span class="sub-money">Hạn trả: {{ $item->due_date?->format('d/m/Y') }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="money-stack">
                                <strong>{{ number_format($outstandingFine) }} đ</strong>
                                <span class="sub-money">Phạt gốc: {{ number_format($item->fine_amount) }} đ</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Không có dữ liệu quá hạn cần theo dõi.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $overdueItems->links() }}
            </div>
        </div>
    </article>
</div>
@endsection
