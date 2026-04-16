@extends('bo_cuc.xac_thuc')

@section('title', 'Đặt lại mật khẩu | Quản Lý Thư Viện')

@section('content')
<div class="panel-eyebrow"><i class="bi bi-arrow-repeat"></i>Cập nhật bảo mật</div>
<h2 class="title">Đặt lại mật khẩu</h2>
<p class="sub">Thiết lập mật khẩu mới cho tài khoản nhân viên của bạn.</p>

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('mat_khau.cap_nhat') }}" class="row g-3">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="col-12">
        <label for="email" class="form-label">Email</label>
        <div class="field-shell">
            <i class="bi bi-envelope field-icon"></i>
            <input
                id="email"
                type="email"
                name="email"
                class="form-control"
                value="{{ old('email', $email) }}"
                autocomplete="email"
                required
                autofocus
            >
        </div>
    </div>

    <div class="col-12 col-md-6">
        <label for="password" class="form-label">Mật khẩu mới</label>
        <div class="field-shell">
            <i class="bi bi-lock field-icon"></i>
            <input
                id="password"
                type="password"
                name="password"
                class="form-control"
                autocomplete="new-password"
                required
            >
            <button type="button" class="password-toggle" data-password-toggle="#password" aria-label="Hiện mật khẩu">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <label for="password_confirmation" class="form-label">Nhập lại mật khẩu mới</label>
        <div class="field-shell">
            <i class="bi bi-shield-lock field-icon"></i>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="form-control"
                autocomplete="new-password"
                required
            >
            <button type="button" class="password-toggle" data-password-toggle="#password_confirmation" aria-label="Hiện mật khẩu">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <div class="col-12 d-grid mt-2">
        <button type="submit" class="btn btn-auth">
            <i class="bi bi-check2-circle me-2"></i>Cập nhật mật khẩu
        </button>
    </div>

    <div class="col-12 inline-links pt-1">
        <span class="text-muted small">Sau khi đổi mật khẩu, hãy đăng nhập lại để tiếp tục làm việc.</span>
        <a href="{{ route('dang_nhap') }}" class="link-auth">Quay lại đăng nhập</a>
    </div>
</form>
@endsection
