@extends('bo_cuc.ung_dung')

@section('title', 'Thêm độc giả')
@section('page-title', 'Thêm độc giả mới')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('doc_gia.store') }}" method="POST" class="row g-3">
            @csrf
            @include('doc_gia._bieu_mau')

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary">Lưu</button>
                <a href="{{ route('doc_gia.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>
@endsection
