@extends('bo_cuc.ung_dung')

@section('title', 'Sửa tác giả')
@section('page-title', 'Cập nhật tác giả')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('tac_gia.update', $author) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')
            @include('tac_gia._bieu_mau')

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('tac_gia.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>
@endsection
