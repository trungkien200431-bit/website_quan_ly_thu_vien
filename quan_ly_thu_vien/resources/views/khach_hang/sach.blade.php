@extends('bo_cuc.khach_hang')

@section('title', 'Tra cứu sách | Quản Lý Thư Viện')
@section('page-title', 'Tra cứu sách')
@section('page-subtitle', 'Xem nhanh đầu sách, tác giả, tình trạng sẵn có và gửi yêu cầu mượn ngay khi tìm được sách phù hợp.')

@section('content')
<div class="section-card">
    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
        <div class="muted-copy">Khách hàng có thể gửi yêu cầu mượn trực tiếp từ từng đầu sách, sau đó thư viện sẽ duyệt và tạo phiếu mượn chính thức.</div>
        <a href="{{ route('khach_hang.yeu_cau_muon') }}" class="btn btn-outline-secondary">Xem yêu cầu mượn của tôi</a>
    </div>

    <form method="GET" action="{{ route('khach_hang.sach') }}" class="row g-3">
        <div class="col-lg-4">
            <label class="form-label" for="q">Từ khóa</label>
            <input id="q" type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Tên sách, mã sách, tác giả, NXB">
        </div>
        <div class="col-lg-3">
            <label class="form-label" for="category_id">Thể loại</label>
            <select id="category_id" name="category_id" class="form-select">
                <option value="">Tất cả thể loại</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) request('category_id') === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2">
            <label class="form-label" for="availability">Khả dụng</label>
            <select id="availability" name="availability" class="form-select">
                <option value="">Tất cả</option>
                <option value="available" @selected(request('availability') === 'available')>Còn sách</option>
                <option value="out" @selected(request('availability') === 'out')>Hết sách</option>
            </select>
        </div>
        <div class="col-lg-2">
            <label class="form-label" for="sort">Sắp xếp</label>
            <select id="sort" name="sort" class="form-select">
                <option value="latest" @selected(request('sort') === 'latest')>Mới cập nhật</option>
                <option value="title_asc" @selected(request('sort') === 'title_asc')>Tên A-Z</option>
                <option value="stock_desc" @selected(request('sort') === 'stock_desc')>Nhiều bản nhất</option>
                <option value="year_desc" @selected(request('sort') === 'year_desc')>Năm xuất bản mới</option>
            </select>
        </div>
        <div class="col-lg-1 d-grid align-items-end">
            <button type="submit" class="btn btn-primary">Lọc</button>
        </div>
    </form>
</div>

<div class="row g-3 mt-1">
    @forelse($books as $book)
        <div class="col-md-6 col-xl-4">
            <article class="book-card">
                <div class="d-flex justify-content-between gap-2 align-items-start">
                    <div class="book-title">{{ $book->title }}</div>
                    <span class="availability-pill {{ $book->available_copies > 0 ? 'ok' : 'neutral' }}">
                        {{ $book->available_copies > 0 ? 'Còn '.$book->available_copies.' bản' : 'Hết bản' }}
                    </span>
                </div>
                <div class="book-meta mt-2">
                    Mã sách: {{ $book->code }}<br>
                    Tác giả: {{ $book->authors->pluck('name')->join(', ') ?: 'Đang cập nhật' }}<br>
                    Thể loại: {{ $book->category?->name ?? 'Đang cập nhật' }}<br>
                    Nhà xuất bản: {{ $book->publisher?->name ?? 'Đang cập nhật' }}<br>
                    Năm XB: {{ $book->published_year ?? 'Chưa cập nhật' }}
                </div>
                @if($book->description)
                    <div class="muted-copy mt-3">{{ \Illuminate\Support\Str::limit($book->description, 140) }}</div>
                @endif

                <div class="mt-3">
                    @if($book->available_copies > 0)
                        @if(in_array($book->id, $pendingRequestBookIds ?? [], true))
                            <div class="muted-copy">Bạn đã có yêu cầu đang chờ xử lý cho đầu sách này.</div>
                        @else
                            <form action="{{ route('khach_hang.yeu_cau_muon.tao') }}" method="POST" class="row g-2">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                                <div class="col-5">
                                    <input
                                        type="number"
                                        name="quantity"
                                        class="form-control"
                                        min="1"
                                        max="{{ $book->available_copies }}"
                                        value="1"
                                    >
                                </div>
                                <div class="col-7 d-grid">
                                    <button type="submit" class="btn btn-primary">Yêu cầu mượn</button>
                                </div>
                            </form>
                        @endif
                    @else
                        <div class="muted-copy">Đầu sách này hiện chưa còn bản sẵn có để gửi yêu cầu mượn.</div>
                    @endif
                </div>
            </article>
        </div>
    @empty
        <div class="col-12">
            <div class="section-card muted-copy">Không tìm thấy đầu sách phù hợp với điều kiện bạn đã chọn.</div>
        </div>
    @endforelse
</div>

<div class="mt-3">
    {{ $books->links() }}
</div>
@endsection
