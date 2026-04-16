<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ChiTietPhieuMuon extends Model
{
    use HasFactory;

    protected $table = 'borrowing_items';

    protected $fillable = [
        'borrowing_id',
        'book_id',
        'quantity',
        'returned_quantity',
        'due_date',
        'return_date',
        'fine_amount',
        'status',
        'note',
    ];

    protected $casts = [
        'due_date' => 'date',
        'return_date' => 'date',
        'fine_amount' => 'decimal:2',
    ];

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(PhieuMuon::class, 'borrowing_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Sach::class, 'book_id');
    }

    public function finePayments(): HasMany
    {
        return $this->hasMany(ThanhToanPhat::class, 'borrowing_item_id');
    }

    public function getRemainingQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->returned_quantity);
    }

    public function getPaidFineAttribute(): float
    {
        return (float) $this->finePayments()->sum('amount');
    }

    public function getOutstandingFineAttribute(): float
    {
        return max(0, (float) $this->fine_amount - $this->paid_fine);
    }
}
