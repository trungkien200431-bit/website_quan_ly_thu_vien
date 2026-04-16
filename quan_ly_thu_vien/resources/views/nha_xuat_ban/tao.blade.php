@extends('bo_cuc.ung_dung')

@section('title', 'Thêm nhà xuất bản')
@section('page-title', 'Thêm nhà xuất bản')
@section('page-subtitle', 'Tạo hồ sơ nhà xuất bản mới để chuẩn hóa dữ liệu sách và thông tin liên hệ.')

@section('content')
<div class="card">
    <div class="card-body p-4">
        <form action="{{ route('nha_xuat_ban.store') }}" method="POST" class="row g-3">
            @csrf
            @include('nha_xuat_ban._bieu_mau')

            <div class="col-12 d-flex flex-wrap gap-2">
                <button class="btn btn-primary">
                    <i class="bi bi-floppy"></i> Lưu nhà xuất bản
                </button>
                <a href="{{ route('nha_xuat_ban.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
