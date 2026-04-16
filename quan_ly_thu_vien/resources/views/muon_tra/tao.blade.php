@extends('bo_cuc.ung_dung')

@section('title', 'Tạo phiếu mượn')
@section('page-title', 'Tạo phiếu mượn mới')

@section('content')
<form action="{{ route('muon_tra.store') }}" method="POST">
    @csrf

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label required">Độc giả</label>
                    <select name="reader_id" class="form-select" required>
                        <option value="">-- Chọn độc giả --</option>
                        @foreach($readers as $reader)
                            <option value="{{ $reader->id }}" @selected((int) old('reader_id') === $reader->id)>
                                {{ $reader->card_number }} - {{ $reader->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label required">Ngày mượn</label>
                    <input type="date" name="borrow_date" class="form-control" value="{{ old('borrow_date', now()->format('Y-m-d')) }}" required>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label required">Hạn trả</label>
                    <input type="date" name="due_date" class="form-control" value="{{ old('due_date', now()->addDays(14)->format('Y-m-d')) }}" required>
                </div>

                <div class="col-12 col-md-12">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="note" rows="2" class="form-control">{{ old('note') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header fw-semibold">Danh sách sách mượn</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                <tr>
                    <th>Mã sách</th>
                    <th>Tên sách</th>
                    <th>Tác giả</th>
                    <th>Có sẵn</th>
                    <th style="width: 180px">Số lượng mượn</th>
                </tr>
                </thead>
                <tbody>
                @forelse($books as $book)
                    <tr>
                        <td>{{ $book->code }}</td>
                        <td>{{ $book->title }}</td>
                        <td>{{ $book->authors->pluck('name')->join(', ') }}</td>
                        <td>{{ $book->available_copies }}</td>
                        <td>
                            <input type="number"
                                   name="items[{{ $book->id }}]"
                                   min="0"
                                   max="{{ $book->available_copies }}"
                                   value="{{ old('items.'.$book->id, 0) }}"
                                   class="form-control"
                                   placeholder="0">
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Không còn sách khả dụng để mượn.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary">Lưu phiếu mượn</button>
        <a href="{{ route('muon_tra.index') }}" class="btn btn-outline-secondary">Quay lại</a>
    </div>
</form>
@endsection
