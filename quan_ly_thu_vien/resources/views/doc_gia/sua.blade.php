@extends('bo_cuc.ung_dung')

@section('title', 'Sửa độc giả')
@section('page-title', 'Cập nhật độc giả')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('doc_gia.update', $reader) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')
            @include('doc_gia._bieu_mau')

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('doc_gia.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            </div>
        </form>
    </div>
</div>
@endsection
