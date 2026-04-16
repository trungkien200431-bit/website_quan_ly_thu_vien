<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label required">Tên tác giả</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $author->name ?? '') }}" required>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Ngày sinh</label>
        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', isset($author) && $author->date_of_birth ? $author->date_of_birth->format('Y-m-d') : '') }}">
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Quốc tịch</label>
        <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $author->nationality ?? '') }}">
    </div>

    <div class="col-12 col-md-6 form-check ms-1 mt-4">
        <input type="checkbox" class="form-check-input" name="is_active" id="author_is_active" value="1"
            {{ old('is_active', $author->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="author_is_active">Kích hoạt</label>
    </div>

    <div class="col-12">
        <label class="form-label">Tiểu sử</label>
        <textarea name="biography" rows="4" class="form-control">{{ old('biography', $author->biography ?? '') }}</textarea>
    </div>
</div>
