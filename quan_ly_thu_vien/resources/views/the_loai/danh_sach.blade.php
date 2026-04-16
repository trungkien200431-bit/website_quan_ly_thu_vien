@extends('bo_cuc.ung_dung')

@section('title', 'Thể loại')
@section('page-title', 'Quản lý thể loại')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label">Từ khóa</label>
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Tên thể loại">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="active" @selected(request('status') === 'active')>Kích hoạt</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Tạm khóa</option>
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button class="btn btn-primary">Lọc</button>
                <a href="{{ route('the_loai.index') }}" class="btn btn-outline-secondary">Đặt lại</a>
                <a href="{{ route('the_loai.create') }}" class="btn btn-success ms-auto">+ Thêm mới</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>Tên thể loại</th>
                <th>Slug</th>
                <th>Số sách</th>
                <th>Trạng thái</th>
                <th style="width: 150px">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>{{ number_format($category->books_count) }}</td>
                    <td>
                        <span class="badge {{ $category->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                            {{ $category->is_active ? 'Kích hoạt' : 'Tạm khóa' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('the_loai.edit', $category) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('the_loai.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa thể loại này?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Không có dữ liệu.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $categories->links() }}
    </div>
</div>
@endsection
