@extends('bo_cuc.ung_dung')

@section('title', 'Sách')
@section('page-title', 'Quản lý sách')
@section('page-subtitle', 'Theo dõi danh mục, tồn kho và khả năng phục vụ của từng đầu sách trong thư viện.')

@push('styles')
<style>
    .catalog-stat {
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .catalog-stat::after {
        content: '';
        position: absolute;
        inset: auto -28px -34px auto;
        width: 108px;
        height: 108px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(177, 109, 66, 0.18), transparent 68%);
    }

    .catalog-stat .stat-note {
        margin-top: 8px;
        color: #6b655d;
        font-size: 0.86rem;
    }

    .catalog-filter-card {
        border-style: solid;
    }

    .catalog-result-pill {
        border-radius: 999px;
        padding: 0.55rem 0.9rem;
        background: rgba(47, 90, 93, 0.08);
        color: #2f5a5d;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .book-table .book-main {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        min-width: 250px;
    }

    .book-table .book-cover {
        width: 58px;
        height: 78px;
        border-radius: 16px;
        border: 1px solid rgba(142, 113, 82, 0.2);
        background: linear-gradient(180deg, #f8f1e5, #efe0c9);
        display: grid;
        place-items: center;
        overflow: hidden;
        flex-shrink: 0;
        color: #7a614d;
        font-size: 1.2rem;
    }

    .book-table .book-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .book-table .book-title {
        font-weight: 800;
        color: #263e42;
        margin-bottom: 4px;
    }

    .book-table .book-meta,
    .book-table .subtle-line {
        color: #6f675d;
        font-size: 0.86rem;
    }

    .book-table .inventory-value {
        font-weight: 800;
        color: #2b4e50;
    }

    .book-table .inventory-progress {
        height: 8px;
        border-radius: 999px;
        background: #eee4d4;
        overflow: hidden;
        margin: 8px 0 6px;
    }

    .book-table .inventory-progress span {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #3c6f72, #7eb0a3);
    }

    .book-table .inventory-progress span.low {
        background: linear-gradient(90deg, #ca8b3d, #efbf6a);
    }

    .book-table .inventory-progress span.out {
        background: linear-gradient(90deg, #b85142, #de8b80);
    }

    .book-table .action-stack {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .book-table .badge-soft {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.36rem 0.7rem;
        border-radius: 999px;
        background: rgba(47, 90, 93, 0.1);
        color: #2f5a5d;
        font-weight: 700;
        font-size: 0.78rem;
    }

    .book-empty {
        padding: 42px 20px;
        text-align: center;
        color: #6e665a;
    }

    .book-empty i {
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
        <div class="stat-card catalog-stat">
            <div class="stat-label">Tổng đầu sách</div>
            <div class="stat-value">{{ number_format($bookStats['total_titles']) }}</div>
            <div class="stat-note">Toàn bộ đầu sách đang được quản lý trong hệ thống.</div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card catalog-stat">
            <div class="stat-label">Đang hoạt động</div>
            <div class="stat-value">{{ number_format($bookStats['active_titles']) }}</div>
            <div class="stat-note">Những đầu sách đang mở cho mượn và khai thác.</div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card catalog-stat">
            <div class="stat-label">Hết sách sẵn có</div>
            <div class="stat-value">{{ number_format($bookStats['out_of_stock']) }}</div>
            <div class="stat-note">Cần bổ sung hoặc chờ hoàn trả để tiếp tục phục vụ.</div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card catalog-stat">
            <div class="stat-label">Bản đang mượn</div>
            <div class="stat-value">{{ number_format($bookStats['borrowed_copies']) }}</div>
            <div class="stat-note">Số bản hiện đang ở ngoài kho và chưa hoàn tất trả.</div>
        </div>
    </div>
</div>

<div class="card mb-3 catalog-filter-card">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <div class="fw-semibold">Bộ lọc danh mục</div>
                <div class="text-muted small">Tìm nhanh theo tên sách, mã, ISBN, tác giả, thể loại hoặc nhà xuất bản.</div>
            </div>
            <div class="catalog-result-pill">
                {{ number_format($books->total()) }} kết quả
            </div>
        </div>

        <form method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-xl-4">
                <label class="form-label">Từ khóa</label>
                <input
                    type="text"
                    name="q"
                    class="form-control"
                    value="{{ request('q') }}"
                    placeholder="Tên sách, mã, ISBN, tác giả..."
                >
            </div>

            <div class="col-12 col-md-6 col-xl-2">
                <label class="form-label">Thể loại</label>
                <select name="category_id" class="form-select">
                    <option value="">Tất cả thể loại</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) request('category_id') === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6 col-xl-2">
                <label class="form-label">Nhà xuất bản</label>
                <select name="publisher_id" class="form-select">
                    <option value="">Tất cả NXB</option>
                    @foreach($publishers as $publisher)
                        <option value="{{ $publisher->id }}" @selected((int) request('publisher_id') === $publisher->id)>{{ $publisher->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6 col-xl-2">
                <label class="form-label">Tồn kho</label>
                <select name="stock" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="available" @selected(request('stock') === 'available')>Còn sách</option>
                    <option value="low" @selected(request('stock') === 'low')>Sắp hết</option>
                    <option value="out" @selected(request('stock') === 'out')>Hết sách</option>
                    <option value="borrowed" @selected(request('stock') === 'borrowed')>Đang có bản được mượn</option>
                </select>
            </div>

            <div class="col-12 col-md-6 col-xl-2">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="active" @selected(request('status') === 'active')>Kích hoạt</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Tạm khóa</option>
                </select>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <label class="form-label">Sắp xếp</label>
                <select name="sort" class="form-select">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>Mới cập nhật</option>
                    <option value="title_asc" @selected(request('sort') === 'title_asc')>Tên sách A-Z</option>
                    <option value="code_asc" @selected(request('sort') === 'code_asc')>Mã sách A-Z</option>
                    <option value="year_desc" @selected(request('sort') === 'year_desc')>Năm xuất bản mới nhất</option>
                    <option value="stock_low" @selected(request('sort') === 'stock_low')>Tồn kho thấp nhất</option>
                    <option value="stock_high" @selected(request('sort') === 'stock_high')>Tồn kho cao nhất</option>
                </select>
            </div>

            <div class="col-12 col-xl-9 d-flex flex-wrap gap-2 justify-content-xl-end">
                <button class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Lọc danh mục
                </button>
                <a href="{{ route('sach.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                </a>
                <a href="{{ route('sach.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Thêm sách mới
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table align-middle book-table">
            <thead>
            <tr>
                <th>Sách</th>
                <th>Phân loại</th>
                <th>Tác giả</th>
                <th>Tồn kho</th>
                <th>Vị trí và giá</th>
                <th>Trạng thái</th>
                <th style="width: 170px">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @forelse($books as $book)
                @php
                    $availableRatio = $book->total_copies > 0 ? (int) round(($book->available_copies / $book->total_copies) * 100) : 0;
                    $borrowedCopies = max(0, $book->total_copies - $book->available_copies);
                    $stockState = $book->available_copies === 0 ? 'out' : ($book->available_copies <= $lowStockThreshold ? 'low' : 'ready');
                @endphp
                <tr>
                    <td>
                        <div class="book-main">
                            <div class="book-cover">
                                @if($book->cover_image)
                                    <img src="{{ $book->cover_image }}" alt="Ảnh bìa {{ $book->title }}">
                                @else
                                    <i class="bi bi-book"></i>
                                @endif
                            </div>
                            <div>
                                <div class="book-title">{{ $book->title }}</div>
                                <div class="book-meta">Mã sách: <strong>{{ $book->code }}</strong></div>
                                <div class="subtle-line">ISBN: {{ $book->isbn ?: 'Chưa cập nhật' }}</div>
                                <div class="subtle-line">Năm XB: {{ $book->published_year ?: 'Chưa rõ' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $book->category->name }}</div>
                        <div class="subtle-line">{{ $book->publisher?->name ?? 'Chưa gắn nhà xuất bản' }}</div>
                    </td>
                    <td>{{ $book->authors->pluck('name')->join(', ') }}</td>
                    <td>
                        <div class="inventory-value">{{ number_format($book->available_copies) }}/{{ number_format($book->total_copies) }} bản sẵn có</div>
                        <div class="inventory-progress">
                            <span class="{{ $stockState }}" style="width: {{ $availableRatio }}%"></span>
                        </div>
                        <div class="subtle-line">
                            Đang mượn: {{ number_format($borrowedCopies) }} bản
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $book->shelf_location ?: 'Chưa gán kệ' }}</div>
                        <div class="subtle-line">
                            Giá bìa:
                            {{ $book->price !== null ? number_format((float) $book->price, 0, ',', '.') . ' đ' : 'Chưa cập nhật' }}
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-2">
                            <span class="badge {{ $book->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $book->is_active ? 'Kích hoạt' : 'Tạm khóa' }}
                            </span>
                            <span class="badge-soft">
                                <i class="bi bi-box-seam"></i>
                                @if($book->available_copies === 0)
                                    Hết sách
                                @elseif($book->available_copies <= $lowStockThreshold)
                                    Tồn thấp
                                @else
                                    Sẵn sàng phục vụ
                                @endif
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="action-stack">
                            <a href="{{ route('sach.edit', $book) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil-square"></i> Sửa
                            </a>
                            <form action="{{ route('sach.destroy', $book) }}" method="POST" onsubmit="return confirm('Xóa sách này?')">
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
                    <td colspan="7">
                        <div class="book-empty">
                            <i class="bi bi-journal-x"></i>
                            <div class="fw-semibold mb-1">Chưa tìm thấy đầu sách phù hợp</div>
                            <div>Hãy thử nới bộ lọc hoặc thêm đầu sách mới để bắt đầu quản lý danh mục.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($books->count())
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
                <div class="text-muted small">
                    Hiển thị {{ number_format($books->firstItem()) }}-{{ number_format($books->lastItem()) }}
                    trên {{ number_format($books->total()) }} đầu sách.
                </div>
                {{ $books->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
