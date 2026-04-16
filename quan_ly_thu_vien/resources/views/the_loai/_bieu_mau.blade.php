<div class="row g-3">
    <div class="col-12">
        <label class="form-label required">Tên thể loại</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required>
    </div>

    <div class="col-12">
        <label class="form-label">Mô tả</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $category->description ?? '') }}</textarea>
    </div>

    <div class="col-12 form-check ms-1">
        <input type="checkbox" class="form-check-input" name="is_active" id="is_active" value="1"
            {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Kích hoạt</label>
    </div>
</div>
