<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class PhieuMuon extends Model
{
    use HasFactory;

    protected $table = 'borrowings';

    protected $fillable = [
        'code',
        'reader_id',
        'processed_by',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'fine_total',
        'note',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'fine_total' => 'decimal:2',
    ];

    public function reader(): BelongsTo
    {
        return $this->belongsTo(DocGia::class, 'reader_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'processed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChiTietPhieuMuon::class, 'borrowing_id');
    }

    public function approvedRequest(): HasOne
    {
        return $this->hasOne(YeuCauMuon::class, 'approved_borrowing_id');
    }
}
