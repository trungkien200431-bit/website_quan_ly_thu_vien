<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class YeuCauMuon extends Model
{
    use HasFactory;

    protected $table = 'borrowing_requests';

    protected $fillable = [
        'code',
        'reader_id',
        'requested_by',
        'processed_by',
        'approved_borrowing_id',
        'request_date',
        'due_date',
        'status',
        'note',
        'processed_note',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'due_date' => 'date',
            'processed_at' => 'datetime',
        ];
    }

    public function reader(): BelongsTo
    {
        return $this->belongsTo(DocGia::class, 'reader_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'requested_by');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'processed_by');
    }

    public function approvedBorrowing(): BelongsTo
    {
        return $this->belongsTo(PhieuMuon::class, 'approved_borrowing_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChiTietYeuCauMuon::class, 'borrowing_request_id');
    }
}
