@php
    $selectedAuthors = collect(old('author_ids', isset($book) ? $book->authors->pluck('id')->all() : []))
        ->map(fn ($id) => (int) $id)
        ->all();
    $coverImage = old('cover_image', $book->cover_image ?? '');
@endphp

@once
    @push('styles')
    <style>
        .book-form-shell .section-card {
            border: 1px solid #e1d4bf;
            border-radius: 18px;
            padding: 18px;
            background: rgba(255, 252, 246, 0.72);
        }

        .book-form-shell .section-title {
            margin-bottom: 2px;
            font-family: 'Fraunces', serif;
            font-size: 1.02rem;
            color: #30484b;
        }

        .book-form-shell .section-note {
            margin-bottom: 16px;
            color: #72695c;
            font-size: 0.88rem;
        }

        .book-form-shell .assist-card {
            border: 1px solid #d8c8b0;
            border-radius: 18px;
            padding: 18px;
            background: linear-gradient(160deg, rgba(247, 241, 230, 0.95), rgba(240, 246, 243, 0.88));
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
        }

        .book-form-shell .assist-card + .assist-card {
            margin-top: 14px;
        }

        .book-form-shell .assist-label {
            color: #6d6457;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.77rem;
            font-weight: 800;
        }

        .book-form-shell .assist-value {
            margin-top: 6px;
            font-family: 'Fraunces', serif;
            font-size: 1.55rem;
            color: #2e4c4f;
        }

        .book-form-shell .cover-preview {
            border-radius: 20px;
            border: 1px dashed #ceb99f;
            min-height: 240px;
            background: linear-gradient(160deg, #fbf5eb, #efe3cf);
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .book-form-shell .cover-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .book-form-shell .cover-placeholder {
            text-align: center;
            color: #826c54;
            padding: 24px;
        }

        .book-form-shell .cover-placeholder i {
            font-size: 2rem;
            display: block;
            margin-bottom: 10px;
        }

        .book-form-shell .inline-hint {
            color: #70695e;
            font-size: 0.84rem;
            margin-top: 6px;
        }

        .book-form-shell .author-counter {
            margin-top: 10px;
            color: #2f5a5d;
            font-weight: 700;
            font-size: 0.86rem;
        }

        .book-form-shell textarea.form-control {
            min-height: 124px;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('[data-book-form]');

            if (!form) {
                return;
            }

            const totalInput = form.querySelector('[data-book-total]');
            const availableInput = form.querySelector('[data-book-available]');
            const coverInput = form.querySelector('[data-book-cover-input]');
            const coverPreview = form.querySelector('[data-book-cover-preview]');
            const authorSelect = form.querySelector('[data-book-authors]');
            const authorCounter = form.querySelector('[data-book-author-count]');
            const availableNote = form.querySelector('[data-book-available-note]');
            const borrowedCount = Number(form.dataset.borrowed || 0);

            let availableTouched = false;

            const renderCover = () => {
                if (!coverPreview) {
                    return;
                }

                const value = (coverInput?.value || '').trim();

                if (value !== '') {
                    coverPreview.innerHTML = '<img src="' + value.replace(/"/g, '&quot;') + '" alt="Xem trước ảnh bìa">';
                    return;
                }

                coverPreview.innerHTML = `
                    <div class="cover-placeholder">
                        <i class="bi bi-image"></i>
                        <div class="fw-semibold mb-1">Chưa có ảnh bìa</div>
                        <div class="small">Nhập URL ảnh để xem trước nhanh tại đây.</div>
                    </div>
                `;
            };

            const renderAuthors = () => {
                if (!authorSelect || !authorCounter) {
                    return;
                }

                const selectedCount = Array.from(authorSelect.selectedOptions).length;
                authorCounter.textContent = selectedCount > 0
                    ? 'Đã chọn ' + selectedCount + ' tác giả'
                    : 'Chưa chọn tác giả nào';
            };

            const renderInventory = () => {
                if (!availableNote || !totalInput || !availableInput) {
                    return;
                }

                const totalCopies = Number(totalInput.value || 0);
                const availableCopies = Number(availableInput.value || 0);
                const maxAvailable = Math.max(totalCopies - borrowedCount, 0);

                availableNote.textContent = borrowedCount > 0
                    ? 'Hiện có ' + borrowedCount + ' bản đang được mượn. Số bản sẵn có tối đa có thể nhập là ' + maxAvailable + '.'
                    : 'Số bản sẵn có nên phản ánh lượng sách hiện đang nằm trong kho.';
            };

            if (coverInput) {
                coverInput.addEventListener('input', renderCover);
            }

            if (authorSelect) {
                authorSelect.addEventListener('change', renderAuthors);
            }

            if (availableInput) {
                availableInput.addEventListener('input', function () {
                    availableTouched = true;
                    renderInventory();
                });
            }

            if (totalInput) {
                totalInput.addEventListener('input', function () {
                    if (!availableTouched && borrowedCount === 0 && availableInput) {
                        availableInput.value = totalInput.value;
                    }

                    renderInventory();
                });
            }

            renderCover();
            renderAuthors();
            renderInventory();
        });
    </script>
    @endpush
@endonce

<div class="col-12">
    <div class="row g-4 book-form-shell" data-book-form data-borrowed="{{ $currentBorrowedCount ?? 0 }}">
        <div class="col-12 col-xl-8">
            <div class="section-card mb-4">
                <div class="section-title">Thông tin nhận diện</div>
                <div class="section-note">Thiết lập mã quản lý, tên hiển thị và ảnh bìa để nhân viên tra cứu nhanh hơn.</div>

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label required" for="book_code">Mã sách</label>
                        <input
                            id="book_code"
                            type="text"
                            name="code"
                            class="form-control @error('code') is-invalid @enderror"
                            value="{{ old('code', $book->code ?? '') }}"
                            placeholder="VD: BK-001"
                            required
                        >
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-8">
                        <label class="form-label required" for="book_title">Tên sách</label>
                        <input
                            id="book_title"
                            type="text"
                            name="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $book->title ?? '') }}"
                            placeholder="Nhập tên sách đầy đủ"
                            required
                        >
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="book_isbn">ISBN</label>
                        <input
                            id="book_isbn"
                            type="text"
                            name="isbn"
                            class="form-control @error('isbn') is-invalid @enderror"
                            value="{{ old('isbn', $book->isbn ?? '') }}"
                            placeholder="Nhập ISBN nếu có"
                        >
                        @error('isbn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="book_cover_image">URL ảnh bìa</label>
                        <input
                            id="book_cover_image"
                            type="text"
                            name="cover_image"
                            class="form-control @error('cover_image') is-invalid @enderror"
                            value="{{ $coverImage }}"
                            placeholder="https://example.com/cover.jpg"
                            data-book-cover-input
                        >
                        @error('cover_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="section-card mb-4">
                <div class="section-title">Phân loại và liên kết</div>
                <div class="section-note">Ghép sách với thể loại, nhà xuất bản và các tác giả để giữ dữ liệu nhất quán.</div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label required" for="book_category_id">Thể loại</label>
                        <select id="book_category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Chọn thể loại</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) old('category_id', $book->category_id ?? 0) === $category->id)>
                                    {{ $category->name }}{{ $category->is_active ? '' : ' (tạm khóa)' }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="book_publisher_id">Nhà xuất bản</label>
                        <select id="book_publisher_id" name="publisher_id" class="form-select @error('publisher_id') is-invalid @enderror">
                            <option value="">Chưa gán nhà xuất bản</option>
                            @foreach($publishers as $publisher)
                                <option value="{{ $publisher->id }}" @selected((int) old('publisher_id', $book->publisher_id ?? 0) === $publisher->id)>
                                    {{ $publisher->name }}{{ $publisher->is_active ? '' : ' (tạm khóa)' }}
                                </option>
                            @endforeach
                        </select>
                        @error('publisher_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label required" for="book_author_ids">Tác giả</label>
                        <select
                            id="book_author_ids"
                            name="author_ids[]"
                            class="form-select @error('author_ids') is-invalid @enderror @error('author_ids.*') is-invalid @enderror"
                            multiple
                            size="7"
                            data-book-authors
                            required
                        >
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" @selected(in_array($author->id, $selectedAuthors, true))>
                                    {{ $author->name }}{{ $author->is_active ? '' : ' (tạm khóa)' }}
                                </option>
                            @endforeach
                        </select>
                        @error('author_ids')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('author_ids.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="inline-hint">Có thể giữ nguyên tác giả đang gắn, kể cả khi hồ sơ tác giả đã tạm khóa.</div>
                        <div class="author-counter" data-book-author-count></div>
                    </div>
                </div>
            </div>

            <div class="section-card mb-4">
                <div class="section-title">Tồn kho và vị trí</div>
                <div class="section-note">Theo dõi số lượng bản, vị trí kệ và giá tham khảo để hỗ trợ nhập kho và cho mượn.</div>

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="book_published_year">Năm xuất bản</label>
                        <input
                            id="book_published_year"
                            type="number"
                            name="published_year"
                            class="form-control @error('published_year') is-invalid @enderror"
                            value="{{ old('published_year', $book->published_year ?? '') }}"
                            placeholder="VD: 2024"
                        >
                        @error('published_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label required" for="book_total_copies">Tổng bản sao</label>
                        <input
                            id="book_total_copies"
                            type="number"
                            name="total_copies"
                            class="form-control @error('total_copies') is-invalid @enderror"
                            value="{{ old('total_copies', $book->total_copies ?? 0) }}"
                            min="0"
                            data-book-total
                            required
                        >
                        @error('total_copies')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label required" for="book_available_copies">Bản sao sẵn có</label>
                        <input
                            id="book_available_copies"
                            type="number"
                            name="available_copies"
                            class="form-control @error('available_copies') is-invalid @enderror"
                            value="{{ old('available_copies', $book->available_copies ?? 0) }}"
                            min="0"
                            data-book-available
                            required
                        >
                        @error('available_copies')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="inline-hint" data-book-available-note></div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="book_shelf_location">Vị trí kệ</label>
                        <input
                            id="book_shelf_location"
                            type="text"
                            name="shelf_location"
                            class="form-control @error('shelf_location') is-invalid @enderror"
                            value="{{ old('shelf_location', $book->shelf_location ?? '') }}"
                            placeholder="Ví dụ: Kệ A2 - Tầng 3"
                        >
                        @error('shelf_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="book_price">Giá bìa</label>
                        <input
                            id="book_price"
                            type="number"
                            step="0.01"
                            min="0"
                            name="price"
                            class="form-control @error('price') is-invalid @enderror"
                            value="{{ old('price', $book->price ?? '') }}"
                            placeholder="Nhập giá tham khảo"
                        >
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="section-card">
                <div class="section-title">Mô tả và khả dụng</div>
                <div class="section-note">Ghi chú nhanh về nội dung, phiên bản hoặc tình trạng khai thác của sách.</div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="book_description">Mô tả</label>
                        <textarea
                            id="book_description"
                            name="description"
                            rows="5"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Tóm tắt ngắn, ghi chú phiên bản hoặc thông tin cần lưu ý..."
                        >{{ old('description', $book->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                name="is_active"
                                id="book_is_active"
                                value="1"
                                {{ old('is_active', $book->is_active ?? true) ? 'checked' : '' }}
                            >
                            <label class="form-check-label fw-semibold" for="book_is_active">Cho phép sách hiển thị trong danh mục hoạt động</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="assist-card">
                <div class="assist-label">Xem trước ảnh bìa</div>
                <div class="cover-preview mt-3" data-book-cover-preview></div>
            </div>

            <div class="assist-card">
                <div class="assist-label">Tình trạng lưu thông</div>
                <div class="assist-value">{{ number_format($currentBorrowedCount ?? 0) }}</div>
                <div class="inline-hint">Bản đang được mượn ở thời điểm hiện tại.</div>
            </div>

            <div class="assist-card">
                <div class="assist-label">Gợi ý nhập liệu</div>
                <div class="inline-hint mt-2">Mã sách nên ngắn gọn và thống nhất theo quy ước nội bộ để tra cứu nhanh.</div>
                <div class="inline-hint">Nếu sách còn bản đang mượn, không nên nhập số bản sẵn có vượt quá lượng thực tế còn trong kho.</div>
                <div class="inline-hint">Ảnh bìa giúp màn hình danh mục trực quan hơn, nhất là khi thư viện có nhiều đầu sách gần tên nhau.</div>
            </div>
        </div>
    </div>
</div>
