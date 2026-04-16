@extends('bo_cuc.ung_dung')

@section('title', 'Sửa nhân viên')
@section('page-title', 'Cập nhật tài khoản nhân viên')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('nhan_vien.update', $user) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')
            @include('nhan_vien._bieu_mau')

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('nhan_vien.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>
@endsection
