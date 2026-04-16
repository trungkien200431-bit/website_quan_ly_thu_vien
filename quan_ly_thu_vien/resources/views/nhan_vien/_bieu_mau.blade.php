<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label required">Họ tên</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label required">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Số điện thoại</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}">
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Địa chỉ</label>
        <input type="text" name="address" class="form-control" value="{{ old('address', $user->address ?? '') }}">
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label required">Vai trò</label>
        <select name="role" class="form-select" required>
            <option value="admin" @selected(old('role', $user->role ?? 'librarian') === 'admin')>Admin</option>
            <option value="librarian" @selected(old('role', $user->role ?? 'librarian') === 'librarian')>Thủ thư</option>
        </select>
    </div>

    <div class="col-12 col-md-6 form-check ms-1 mt-4">
        <input type="checkbox" class="form-check-input" name="is_active" id="user_is_active" value="1"
            {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="user_is_active">Kích hoạt tài khoản</label>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label {{ isset($user) ? '' : 'required' }}">Mật khẩu {{ isset($user) ? '(để trống nếu không đổi)' : '' }}</label>
        <input type="password" name="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label {{ isset($user) ? '' : 'required' }}">Nhập lại mật khẩu</label>
        <input type="password" name="password_confirmation" class="form-control" {{ isset($user) ? '' : 'required' }}>
    </div>
</div>
