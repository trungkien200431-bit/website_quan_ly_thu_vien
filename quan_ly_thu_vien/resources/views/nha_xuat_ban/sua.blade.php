@extends('bo_cuc.ung_dung')

@section('title', 'Cập nhật nhà xuất bản')
@section('page-title', 'Cập nhật nhà xuất bản')
@section('page-subtitle', 'Điều chỉnh thông tin liên hệ và rà soát phạm vi sách đã gắn với nhà xuất bản này.')

@section('content')
<div class="card">
    <div class="card-body p-4">
        <form action="{{ route('nha_xuat_ban.update', $publisher) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')
            @include('nha_xuat_ban._bieu_mau')

            <div class="col-12 d-flex flex-wrap gap-2">
                <button class="btn btn-primary">
                    <i class="bi bi-save"></i> Cập nhật nhà xuất bản
                </button>
                <a href="{{ route('nha_xuat_ban.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
