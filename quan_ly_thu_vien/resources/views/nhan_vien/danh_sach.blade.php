@extends('bo_cuc.ung_dung')

@section('title', 'Nhân viên')
@section('page-title', 'Quản lý tài khoản nhân viên')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label">Từ khóa</label>
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Tên / Email / SĐT">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label">Vai trò</label>
                <select name="role" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                    <option value="librarian" @selected(request('role') === 'librarian')>Thủ thư</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="active" @selected(request('status') === 'active')>Kích hoạt</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Tạm khóa</option>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button class="btn btn-primary">Lọc</button>
                <a href="{{ route('nhan_vien.index') }}" class="btn btn-outline-secondary">Đặt lại</a>
                <a href="{{ route('nhan_vien.create') }}" class="btn btn-success ms-auto">+ Thêm mới</a>
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
                <th>Họ tên</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th style="width: 150px">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge {{ $user->role === 'admin' ? 'text-bg-primary' : 'text-bg-info' }}">
                            {{ strtoupper($user->role) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $user->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                            {{ $user->is_active ? 'Kích hoạt' : 'Tạm khóa' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('nhan_vien.edit', $user) }}" class="btn btn-sm btn-warning">Sửa</a>
                        @if((int) auth()->id() !== (int) $user->id)
                            <form action="{{ route('nhan_vien.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa tài khoản này?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Không có dữ liệu.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $users->links() }}
    </div>
</div>
@endsection
