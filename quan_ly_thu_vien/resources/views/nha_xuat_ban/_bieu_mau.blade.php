@once
    @push('styles')
    <style>
        .publisher-form-shell .section-card {
            border: 1px solid #e1d4bf;
            border-radius: 18px;
            padding: 18px;
            background: rgba(255, 252, 246, 0.72);
        }

        .publisher-form-shell .section-title {
            margin-bottom: 2px;
            font-family: 'Fraunces', serif;
            font-size: 1.02rem;
            color: #30484b;
        }

        .publisher-form-shell .section-note,
        .publisher-form-shell .inline-hint {
            color: #72695c;
            font-size: 0.88rem;
        }

        .publisher-form-shell .section-note {
            margin-bottom: 16px;
        }

        .publisher-form-shell .assist-card {
            border: 1px solid #d8c8b0;
            border-radius: 18px;
            padding: 18px;
            background: linear-gradient(160deg, rgba(247, 241, 230, 0.95), rgba(240, 246, 243, 0.88));
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
        }

        .publisher-form-shell .assist-card + .assist-card {
            margin-top: 14px;
        }

        .publisher-form-shell .assist-label {
            color: #6d6457;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.77rem;
            font-weight: 800;
        }

        .publisher-form-shell .assist-value {
            margin-top: 6px;
            font-family: 'Fraunces', serif;
            font-size: 1.55rem;
            color: #2e4c4f;
        }

        .publisher-form-shell textarea.form-control {
            min-height: 110px;
        }

        .publisher-form-shell .book-link-list {
            margin: 12px 0 0;
            padding-left: 18px;
            color: #625c54;
            font-size: 0.88rem;
        }

        .publisher-form-shell .book-link-list li + li {
            margin-top: 8px;
        }
    </style>
    @endpush
@endonce

<div class="col-12">
    <div class="row g-4 publisher-form-shell">
        <div class="col-12 col-xl-8">
            <div class="section-card mb-4">
                <div class="section-title">Thông tin nhà xuất bản</div>
                <div class="section-note">Thiết lập hồ sơ nhận diện và phương thức liên hệ để dễ quản lý đối tác xuất bản.</div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label required" for="publisher_name">Tên nhà xuất bản</label>
                        <input
                            id="publisher_name"
                            type="text"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $publisher->name ?? '') }}"
                            placeholder="Ví dụ: NXB Trẻ"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="publisher_phone">Số điện thoại</label>
                        <input
                            id="publisher_phone"
                            type="text"
                            name="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $publisher->phone ?? '') }}"
                            placeholder="Nhập số điện thoại liên hệ"
                        >
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="publisher_email">Email</label>
                        <input
                            id="publisher_email"
                            type="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $publisher->email ?? '') }}"
                            placeholder="contact@example.com"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="publisher_website">Website</label>
                        <input
                            id="publisher_website"
                            type="text"
                            name="website"
                            class="form-control @error('website') is-invalid @enderror"
                            value="{{ old('website', $publisher->website ?? '') }}"
                            placeholder="example.com hoặc https://example.com"
                        >
                        @error('website')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="inline-hint mt-2">Nếu chỉ nhập tên miền, hệ thống sẽ tự chuẩn hóa thành địa chỉ `https://` hợp lệ.</div>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <div class="section-title">Địa chỉ và trạng thái sử dụng</div>
                <div class="section-note">Giữ lại thông tin địa chỉ để tiện đối chiếu chứng từ, nhập kho hoặc liên hệ phát hành.</div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="publisher_address">Địa chỉ</label>
                        <textarea
                            id="publisher_address"
                            name="address"
                            rows="4"
                            class="form-control @error('address') is-invalid @enderror"
                            placeholder="Nhập địa chỉ trụ sở hoặc văn phòng giao dịch..."
                        >{{ old('address', $publisher->address ?? '') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                name="is_active"
                                id="publisher_is_active"
                                value="1"
                                {{ old('is_active', $publisher->is_active ?? true) ? 'checked' : '' }}
                            >
                            <label class="form-check-label fw-semibold" for="publisher_is_active">Tiếp tục cho phép chọn nhà xuất bản này trong dữ liệu mới</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="assist-card">
                <div class="assist-label">Đầu sách liên kết</div>
                <div class="assist-value">{{ number_format($publisherInsights['books_count'] ?? 0) }}</div>
                <div class="inline-hint mt-1">Số lượng đầu sách hiện đang gắn với nhà xuất bản này.</div>
            </div>

            <div class="assist-card">
                <div class="assist-label">Tồn bản sách</div>
                <div class="assist-value">{{ number_format($publisherInsights['inventory_total'] ?? 0) }}</div>
                <div class="inline-hint mt-1">Tổng bản đã nhập. Sẵn có hiện tại: {{ number_format($publisherInsights['inventory_available'] ?? 0) }} bản.</div>
            </div>

            <div class="assist-card">
                <div class="assist-label">Sách gần đây</div>
                @if(($recentBooks ?? collect())->isNotEmpty())
                    <ul class="book-link-list">
                        @foreach($recentBooks as $linkedBook)
                            <li>
                                <div class="fw-semibold">{{ $linkedBook->title }}</div>
                                <div class="inline-hint">{{ $linkedBook->category?->name ?? 'Chưa có thể loại' }} • {{ $linkedBook->code }}</div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="inline-hint mt-2">Nhà xuất bản này chưa liên kết đầu sách nào hoặc bạn đang tạo hồ sơ mới.</div>
                @endif
            </div>
        </div>
    </div>
</div>
