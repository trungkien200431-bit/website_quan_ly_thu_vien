<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietYeuCauMuon extends Model
{
    use HasFactory;

    protected $table = 'borrowing_request_items';

    protected $fillable = [
        'borrowing_request_id',
        'book_id',
        'quantity',
        'note',
    ];

    public function borrowingRequest(): BelongsTo
    {
        return $this->belongsTo(YeuCauMuon::class, 'borrowing_request_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Sach::class, 'book_id');
    }
}
