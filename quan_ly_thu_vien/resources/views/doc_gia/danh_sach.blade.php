@extends('bo_cuc.ung_dung')

@section('title', 'Độc giả')
@section('page-title', 'Quản lý độc giả')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label">Từ khóa</label>
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Mã thẻ / tên / email / SĐT">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="active" @selected(request('status') === 'active')>Hoạt động</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Tạm ngưng</option>
                    <option value="blocked" @selected(request('status') === 'blocked')>Khóa</option>
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button class="btn btn-primary">Lọc</button>
                <a href="{{ route('doc_gia.index') }}" class="btn btn-outline-secondary">Đặt lại</a>
                <a href="{{ route('doc_gia.create') }}" class="btn btn-success ms-auto">+ Thêm mới</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
            <tr>
                <th>Mã thẻ</th>
                <th>Họ tên</th>
                <th>Liên hệ</th>
                <th>Hạn thẻ</th>
                <th>Trạng thái</th>
                <th style="width: 150px">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @forelse($readers as $reader)
                <tr>
                    <td>{{ $reader->card_number }}</td>
                    <td>{{ $reader->full_name }}</td>
                    <td>
                        {{ $reader->phone ?: '-' }}<br>
                        <small class="text-muted">{{ $reader->email ?: '-' }}</small>
                    </td>
                    <td>{{ $reader->expiry_date?->format('d/m/Y') ?: '-' }}</td>
                    <td>
                        <span class="badge {{ $reader->status === 'active' ? 'text-bg-success' : ($reader->status === 'inactive' ? 'text-bg-secondary' : 'text-bg-danger') }}">
                            {{ strtoupper($reader->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('doc_gia.edit', $reader) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('doc_gia.destroy', $reader) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa độc giả này?')">
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

        {{ $readers->links() }}
    </div>
</div>
@endsection
