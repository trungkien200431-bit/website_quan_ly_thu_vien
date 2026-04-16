@extends('bo_cuc.ung_dung')

@section('title', 'Thêm tác giả')
@section('page-title', 'Thêm tác giả mới')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('tac_gia.store') }}" method="POST" class="row g-3">
            @csrf
            @include('tac_gia._bieu_mau')

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary">Lưu</button>
                <a href="{{ route('tac_gia.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>
@endsection
