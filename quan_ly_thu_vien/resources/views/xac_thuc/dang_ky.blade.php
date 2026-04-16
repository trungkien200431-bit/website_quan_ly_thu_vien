@extends('bo_cuc.xac_thuc')

@section('title', 'Đăng ký khách hàng | Quản Lý Thư Viện')
@section('hero-kicker', 'Đăng Ký Khách Hàng')
@section('hero-title', 'Tạo tài khoản để sử dụng thư viện thuận tiện hơn')
@section('hero-text', 'Khách hàng đăng ký một lần để tra cứu sách, theo dõi lịch sử mượn trả và quản lý hồ sơ độc giả ngay trên hệ thống.')

@section('content')
<div class="panel-eyebrow"><i class="bi bi-person-plus"></i>Đăng ký trực tuyến</div>
<h2 class="title">Tạo tài khoản khách hàng</h2>
<p class="sub">Chỉ khách hàng đăng ký tại đây. Tài khoản nhân viên vẫn do quản trị viên thư viện cấp và quản lý riêng.</p>

@include('thanh_phan.thong_bao')

<form action="{{ route('dang_ky.thuc_hien') }}" method="POST" class="row g-3">
    @csrf

    <div class="col-md-6">
        <label for="full_name" class="form-label">Họ và tên</label>
        <input id="full_name" type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required>
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label">Số điện thoại</label>
        <input id="phone" type="text" name="phone" class="form-control" value="{{ old('phone') }}">
    </div>

    <div class="col-md-6">
        <label for="gender" class="form-label">Giới tính</label>
        <select id="gender" name="gender" class="form-select">
            <option value="">Chọn giới tính</option>
            <option value="male" @selected(old('gender') === 'male')>Nam</option>
            <option value="female" @selected(old('gender') === 'female')>Nữ</option>
            <option value="other" @selected(old('gender') === 'other')>Khác</option>
        </select>
    </div>

    <div class="col-md-6">
        <label for="date_of_birth" class="form-label">Ngày sinh</label>
        <input id="date_of_birth" type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
    </div>

    <div class="col-md-6">
        <label for="address" class="form-label">Địa chỉ</label>
        <input id="address" type="text" name="address" class="form-control" value="{{ old('address') }}">
    </div>

    <div class="col-md-6">
        <label for="password" class="form-label">Mật khẩu</label>
        <div class="field-shell">
            <i class="bi bi-lock field-icon"></i>
            <input id="password" type="password" name="password" class="form-control" autocomplete="new-password" required>
            <button type="button" class="password-toggle" data-password-toggle="#password" aria-label="Hiện mật khẩu">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <div class="col-md-6">
        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
        <div class="field-shell">
            <i class="bi bi-shield-lock field-icon"></i>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required>
            <button type="button" class="password-toggle" data-password-toggle="#password_confirmation" aria-label="Hiện mật khẩu">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <div class="col-12 inline-links">
        <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">Tự động đăng nhập sau khi tạo tài khoản</label>
        </div>
        <a class="link-auth" href="{{ route('dang_nhap') }}">Đã có tài khoản? Đăng nhập</a>
    </div>

    <div class="col-12 d-grid mt-2">
        <button type="submit" class="btn btn-auth">
            <i class="bi bi-person-check me-2"></i>Tạo tài khoản khách hàng
        </button>
    </div>
</form>

<div class="mini-note">
    Nếu thư viện đã tạo hồ sơ độc giả cho bạn trước đó, hãy đăng ký bằng đúng email đã lưu để hệ thống liên kết hồ sơ hiện có.
</div>
@endsection
