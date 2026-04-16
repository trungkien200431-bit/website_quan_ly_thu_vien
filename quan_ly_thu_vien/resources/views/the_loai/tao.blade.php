@extends('bo_cuc.ung_dung')

@section('title', 'Thêm thể loại')
@section('page-title', 'Thêm thể loại mới')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('the_loai.store') }}" method="POST" class="row g-3">
            @csrf
            @include('the_loai._bieu_mau')

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary">Lưu</button>
                <a href="{{ route('the_loai.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>
@endsection
