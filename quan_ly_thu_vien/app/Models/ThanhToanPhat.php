<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ThanhToanPhat extends Model
{
    use HasFactory;

    protected $table = 'fine_payments';

    protected $fillable = [
        'borrowing_item_id',
        'amount',
        'paid_at',
        'paid_by',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function borrowingItem(): BelongsTo
    {
        return $this->belongsTo(ChiTietPhieuMuon::class, 'borrowing_item_id');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'paid_by');
    }
}
