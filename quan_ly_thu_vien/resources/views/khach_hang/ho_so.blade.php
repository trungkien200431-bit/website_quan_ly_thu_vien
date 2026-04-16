@extends('bo_cuc.khach_hang')

@section('title', 'Hồ sơ khách hàng | Quản Lý Thư Viện')
@section('page-title', 'Hồ sơ cá nhân')
@section('page-subtitle', 'Cập nhật thông tin liên hệ và thay đổi mật khẩu để quản lý tài khoản độc giả của bạn an toàn hơn.')

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="section-card">
            <h3 class="section-title">Thông tin tài khoản</h3>

            <form action="{{ route('khach_hang.ho_so.cap_nhat') }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label for="full_name" class="form-label">Họ và tên</label>
                    <input id="full_name" type="text" name="full_name" class="form-control" value="{{ old('full_name', $reader->full_name) }}" required>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $reader->email) }}" required>
                </div>

                <div class="col-md-6">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input id="phone" type="text" name="phone" class="form-control" value="{{ old('phone', $reader->phone) }}">
                </div>

                <div class="col-md-6">
                    <label for="gender" class="form-label">Giới tính</label>
                    <select id="gender" name="gender" class="form-select">
                        <option value="">Chọn giới tính</option>
                        <option value="male" @selected(old('gender', $reader->gender) === 'male')>Nam</option>
                        <option value="female" @selected(old('gender', $reader->gender) === 'female')>Nữ</option>
                        <option value="other" @selected(old('gender', $reader->gender) === 'other')>Khác</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="date_of_birth" class="form-label">Ngày sinh</label>
                    <input id="date_of_birth" type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', optional($reader->date_of_birth)->format('Y-m-d')) }}">
                </div>

                <div class="col-md-6">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <input id="address" type="text" name="address" class="form-control" value="{{ old('address', $reader->address) }}">
                </div>

                <div class="col-12"><hr></div>

                <div class="col-md-4">
                    <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                    <input id="current_password" type="password" name="current_password" class="form-control" autocomplete="current-password">
                </div>

                <div class="col-md-4">
                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                    <input id="new_password" type="password" name="new_password" class="form-control" autocomplete="new-password">
                </div>

                <div class="col-md-4">
                    <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                    <input id="new_password_confirmation" type="password" name="new_password_confirmation" class="form-control" autocomplete="new-password">
                </div>

                <div class="col-12 d-grid d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary px-4">Lưu hồ sơ</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="section-card">
            <h3 class="section-title">Thông tin thẻ độc giả</h3>
            <div class="muted-copy">
                Mã thẻ: <strong>{{ $reader->card_number }}</strong><br>
                Trạng thái: {{ match($reader->status) {
                    'active' => 'Đang hoạt động',
                    'inactive' => 'Tạm ngưng',
                    'blocked' => 'Bị khóa',
                    default => $reader->status,
                } }}<br>
                Ngày tham gia: {{ optional($reader->membership_date)->format('d/m/Y') ?? 'Chưa cập nhật' }}<br>
                Ngày hết hạn: {{ optional($reader->expiry_date)->format('d/m/Y') ?? 'Chưa cập nhật' }}
            </div>
        </div>
    </div>
</div>
@endsection
