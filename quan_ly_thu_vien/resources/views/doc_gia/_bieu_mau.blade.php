<div class="row g-3">
    <div class="col-12 col-md-4">
        <label class="form-label required">Mã thẻ</label>
        <input type="text" name="card_number" class="form-control" value="{{ old('card_number', $reader->card_number ?? '') }}" required>
    </div>

    <div class="col-12 col-md-8">
        <label class="form-label required">Họ tên</label>
        <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $reader->full_name ?? '') }}" required>
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $reader->email ?? '') }}">
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Số điện thoại</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $reader->phone ?? '') }}">
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Giới tính</label>
        <select name="gender" class="form-select">
            <option value="">-- Chọn --</option>
            <option value="male" @selected(old('gender', $reader->gender ?? '') === 'male')>Nam</option>
            <option value="female" @selected(old('gender', $reader->gender ?? '') === 'female')>Nữ</option>
            <option value="other" @selected(old('gender', $reader->gender ?? '') === 'other')>Khác</option>
        </select>
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Ngày sinh</label>
        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', isset($reader) && $reader->date_of_birth ? $reader->date_of_birth->format('Y-m-d') : '') }}">
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label required">Ngày cấp thẻ</label>
        <input type="date" name="membership_date" class="form-control" value="{{ old('membership_date', isset($reader) && $reader->membership_date ? $reader->membership_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label">Ngày hết hạn</label>
        <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date', isset($reader) && $reader->expiry_date ? $reader->expiry_date->format('Y-m-d') : '') }}">
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Địa chỉ</label>
        <input type="text" name="address" class="form-control" value="{{ old('address', $reader->address ?? '') }}">
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label required">Trạng thái</label>
        <select name="status" class="form-select" required>
            <option value="active" @selected(old('status', $reader->status ?? 'active') === 'active')>Hoạt động</option>
            <option value="inactive" @selected(old('status', $reader->status ?? 'active') === 'inactive')>Tạm ngưng</option>
            <option value="blocked" @selected(old('status', $reader->status ?? 'active') === 'blocked')>Khóa</option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">Ghi chú</label>
        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $reader->notes ?? '') }}</textarea>
    </div>
</div>
