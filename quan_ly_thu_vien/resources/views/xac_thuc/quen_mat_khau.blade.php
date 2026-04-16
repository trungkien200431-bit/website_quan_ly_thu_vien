@extends('bo_cuc.xac_thuc')

@section('title', 'Quên mật khẩu | Quản Lý Thư Viện')

@section('content')
<div class="panel-eyebrow"><i class="bi bi-key"></i>Khôi phục truy cập</div>
<h2 class="title">Quên mật khẩu</h2>
<p class="sub">Nhập email nhân viên đã đăng ký để nhận liên kết đặt lại mật khẩu.</p>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('mat_khau.gui_email') }}" class="row g-3">
    @csrf

    <div class="col-12">
        <label for="email" class="form-label">Email đăng ký</label>
        <div class="field-shell">
            <i class="bi bi-envelope-paper field-icon"></i>
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

    <div class="col-12 d-grid mt-2">
        <button type="submit" class="btn btn-auth">
            <i class="bi bi-send-check me-2"></i>Gửi liên kết đặt lại mật khẩu
        </button>
    </div>

    <div class="col-12 inline-links pt-1">
        <span class="text-muted small">Không tự tạo tài khoản từ màn hình này.</span>
        <a href="{{ route('dang_nhap') }}" class="link-auth">Quay lại đăng nhập</a>
    </div>
</form>
@endsection
