@extends('bo_cuc.ung_dung')

@section('title', 'Thêm sách')
@section('page-title', 'Thêm sách mới')
@section('page-subtitle', 'Tạo đầu sách mới với đầy đủ thông tin biên mục, tồn kho và liên kết tác giả.')

@section('content')
<div class="card">
    <div class="card-body p-4">
        <form action="{{ route('sach.store') }}" method="POST" class="row g-3">
            @csrf
            @include('sach._bieu_mau')

            <div class="col-12 d-flex flex-wrap gap-2">
                <button class="btn btn-primary">
                    <i class="bi bi-floppy"></i> Lưu sách
                </button>
                <a href="{{ route('sach.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh mục
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
