@extends('bo_cuc.ung_dung')

@section('title', 'Nhà xuất bản')
@section('page-title', 'Quản lý nhà xuất bản')
@section('page-subtitle', 'Theo dõi hồ sơ liên hệ, mức độ sử dụng và liên kết đầu sách của từng nhà xuất bản.')

@push('styles')
<style>
    .publisher-stat {
        height: 100%;
        overflow: hidden;
        position: relative;
    }

    .publisher-stat::before {
        content: '';
        position: absolute;
        inset: 12px 12px auto auto;
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: rgba(47, 90, 93, 0.08);
    }

    .publisher-stat .stat-note {
        margin-top: 8px;
        color: #6b655d;
        font-size: 0.86rem;
    }

    .publisher-filter-meta {
        border-radius: 999px;
        padding: 0.55rem 0.9rem;
        background: rgba(177, 109, 66, 0.1);
        color: #935c37;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .publisher-table .publisher-main {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .publisher-table .publisher-avatar {
        width: 52px;
        height: 52px;
        border-radius: 18px;
        background: linear-gradient(145deg, #f1e4cd, #e5d0ae);
        color: #734d2e;
        display: grid;
        place-items: center;
        font-weight: 800;
        flex-shrink: 0;
    }

    .publisher-table .publisher-name {
        font-weight: 800;
        color: #264043;
        margin-bottom: 4px;
    }

    .publisher-table .support-line {
        color: #6f675d;
        font-size: 0.86rem;
    }

    .publisher-table .metric-block strong {
        display: block;
        color: #2c4f52;
        font-size: 1.02rem;
    }

    .publisher-table .action-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .publisher-empty {
        text-align: center;
        padding: 42px 20px;
        color: #6d655b;
    }

    .publisher-empty i {
        display: inline-grid;
        place-items: center;
        width: 60px;
        height: 60px;
        border-radius: 18px;
        margin-bottom: 12px;
        background: linear-gradient(145deg, #f6ede0, #efdfc6);
        color: #8b6849;
        font-size: 1.4rem;
    }
</style>
@endpush

@section('content')
<div class="row g-3 mb-3">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card publisher-stat">
            <div class="stat-label">Tổng NXB</div>
            <div class="stat-value">{{ number_format($publisherStats['total_publishers']) }}</div>
            <div class="stat-note">Toàn bộ hồ sơ nhà xuất bản đang được lưu trong hệ thống.</div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card publisher-stat">
            <div class="stat-label">Đang hoạt động</div>
            <div class="stat-value">{{ number_format($publisherStats['active_publishers']) }}</div>
            <div class="stat-note">Những nhà xuất bản có thể tiếp tục dùng cho dữ liệu mới.</div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card publisher-stat">
            <div class="stat-label">Đã liên kết sách</div>
            <div class="stat-value">{{ number_format($publisherStats['linked_publishers']) }}</div>
            <div class="stat-note">Các nhà xuất bản hiện đã gắn ít nhất một đầu sách.</div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card publisher-stat">
            <div class="stat-label">Đầu sách liên quan</div>
            <div class="stat-value">{{ number_format($publisherStats['total_titles']) }}</div>
            <div class="stat-note">Tổng đầu sách đang được phân bổ về các nhà xuất bản.</div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <div class="fw-semibold">Bộ lọc nhà xuất bản</div>
                <div class="text-muted small">Tra cứu theo tên, địa chỉ, website hoặc trạng thái đang sử dụng.</div>
            </div>
            <div class="publisher-filter-meta">{{ number_format($publishers->total()) }} kết quả</div>
        </div>

        <form method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-xl-4">
                <label class="form-label">Từ khóa</label>
                <input
                    type="text"
                    name="q"
                    class="form-control"
                    value="{{ request('q') }}"
                    placeholder="Tên, email, SĐT, website..."
                >
            </div>

            <div class="col-12 col-md-4 col-xl-2">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="active" @selected(request('status') === 'active')>Kích hoạt</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Tạm khóa</option>
                </select>
            </div>

            <div class="col-12 col-md-4 col-xl-2">
                <label class="form-label">Mức sử dụng</label>
                <select name="usage" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="linked" @selected(request('usage') === 'linked')>Đã gắn sách</option>
                    <option value="empty" @selected(request('usage') === 'empty')>Chưa gắn sách</option>
                </select>
            </div>

            <div class="col-12 col-md-4 col-xl-2">
                <label class="form-label">Sắp xếp</label>
                <select name="sort" class="form-select">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>Mới cập nhật</option>
                    <option value="name_asc" @selected(request('sort') === 'name_asc')>Tên A-Z</option>
                    <option value="books_desc" @selected(request('sort') === 'books_desc')>Nhiều sách nhất</option>
                    <option value="books_asc" @selected(request('sort') === 'books_asc')>Ít sách nhất</option>
                </select>
            </div>

            <div class="col-12 col-xl-2 d-flex flex-wrap gap-2 justify-content-xl-end">
                <button class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Lọc
                </button>
                <a href="{{ route('nha_xuat_ban.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                </a>
                <a href="{{ route('nha_xuat_ban.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Thêm NXB
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table align-middle publisher-table">
            <thead>
            <tr>
                <th>Nhà xuất bản</th>
                <th>Liên hệ</th>
                <th>Phạm vi sách</th>
                <th>Địa chỉ</th>
                <th>Trạng thái</th>
                <th style="width: 170px">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @forelse($publishers as $publisher)
                @php
                    $initials = collect(explode(' ', $publisher->name))
                        ->filter()
                        ->take(2)
                        ->map(fn ($part) => mb_substr($part, 0, 1))
                        ->implode('');
                @endphp
                <tr>
                    <td>
                        <div class="publisher-main">
                            <div class="publisher-avatar">{{ $initials ?: 'NXB' }}</div>
                            <div>
                                <div class="publisher-name">{{ $publisher->name }}</div>
                                <div class="support-line">Slug: {{ $publisher->slug }}</div>
                                <div class="support-line">Cập nhật: {{ $publisher->updated_at->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="support-line">{{ $publisher->phone ?: 'Chưa có số điện thoại' }}</div>
                        <div class="support-line">{{ $publisher->email ?: 'Chưa có email' }}</div>
                        <div class="support-line">
                            @if($publisher->website)
                                <a href="{{ $publisher->website }}" target="_blank" rel="noopener noreferrer">{{ $publisher->website }}</a>
                            @else
                                Chưa có website
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="metric-block">
                            <strong>{{ number_format($publisher->books_count) }} đầu sách</strong>
                            <div class="support-line">Tổng bản: {{ number_format((int) ($publisher->inventory_total ?? 0)) }}</div>
                            <div class="support-line">Sẵn có: {{ number_format((int) ($publisher->inventory_available ?? 0)) }}</div>
                        </div>
                    </td>
                    <td>{{ $publisher->address ?: 'Chưa cập nhật địa chỉ' }}</td>
                    <td>
                        <span class="badge {{ $publisher->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                            {{ $publisher->is_active ? 'Kích hoạt' : 'Tạm khóa' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-row">
                            <a href="{{ route('nha_xuat_ban.edit', $publisher) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil-square"></i> Sửa
                            </a>
                            <form action="{{ route('nha_xuat_ban.destroy', $publisher) }}" method="POST" onsubmit="return confirm('Xóa nhà xuất bản này?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash3"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="publisher-empty">
                            <i class="bi bi-building-x"></i>
                            <div class="fw-semibold mb-1">Chưa có nhà xuất bản phù hợp</div>
                            <div>Hãy thay đổi điều kiện lọc hoặc thêm hồ sơ nhà xuất bản mới để tiếp tục.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($publishers->count())
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
                <div class="text-muted small">
                    Hiển thị {{ number_format($publishers->firstItem()) }}-{{ number_format($publishers->lastItem()) }}
                    trên {{ number_format($publishers->total()) }} nhà xuất bản.
                </div>
                {{ $publishers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
