@extends('bo_cuc.xac_thuc')

@section('title', 'Đăng nhập | Quản Lý Thư Viện')

@section('content')
<div class="panel-eyebrow"><i class="bi bi-person-lock"></i>Truy cập hệ thống</div>
<h2 class="title">Đăng nhập</h2>
<p class="sub">Sử dụng tài khoản của bạn để truy cập hệ thống. Nhân viên do quản trị viên cấp tài khoản, còn khách hàng có thể tự đăng ký trực tuyến.</p>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<form action="{{ route('dang_nhap.thuc_hien') }}" method="POST" class="row g-3">
    @csrf

    <div class="col-12">
        <label for="email" class="form-label">Email</label>
        <div class="field-shell">
            <i class="bi bi-envelope field-icon"></i>
            <input
                id="email"
                type="email"
                name="email"
                class="form-control"
                value="{{ old('email') }}"
                placeholder="ban@thuvien.vn"
                autocomplete="email"
                required
                autofocus
            >
        </div>
    </div>

    <div class="col-12">
        <label for="password" class="form-label">Mật khẩu</label>
        <div class="field-shell">
            <i class="bi bi-lock field-icon"></i>
            <input
                id="password"
                type="password"
                name="password"
                class="form-control"
                placeholder="Nhập mật khẩu của bạn"
                autocomplete="current-password"
                required
            >
            <button type="button" class="password-toggle" data-password-toggle="#password" aria-label="Hiện mật khẩu">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <div class="col-12 inline-links">
        <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">Giữ đăng nhập trên thiết bị này</label>
        </div>
        <a class="link-auth" href="{{ route('mat_khau.yeu_cau') }}">Quên mật khẩu?</a>
    </div>

    <div class="col-12 d-grid mt-2">
        <button type="submit" class="btn btn-auth">
            <i class="bi bi-arrow-up-right-circle me-2"></i>Đăng nhập vào hệ thống
        </button>
    </div>
</form>

<div class="soft-card mt-4">
    <h3 class="soft-card-title">Ghi chú vận hành</h3>
    <div class="helper-list">
        <div class="helper-item">
            <span class="helper-icon"><i class="bi bi-people"></i></span>
            <div class="helper-copy">Khách hàng và nhân viên dùng chung một hệ đăng nhập, nhưng quyền truy cập sẽ được phân theo vai trò tài khoản.</div>
        </div>
        <div class="helper-item">
            <span class="helper-icon"><i class="bi bi-person-badge"></i></span>
            <div class="helper-copy">Tài khoản nhân viên do quản trị viên tạo trong mục nhân viên. Tài khoản khách hàng có thể tự đăng ký trực tuyến.</div>
        </div>
    </div>
</div>

<div class="mini-note">
    Chưa có tài khoản khách hàng? <a class="link-auth" href="{{ route('dang_ky') }}">Đăng ký ngay</a>
</div>
@endsection
