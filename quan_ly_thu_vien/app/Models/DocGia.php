<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocGia extends Model
{
    use HasFactory;

    protected $table = 'readers';

    protected $fillable = [
        'user_id',
        'card_number',
        'full_name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'address',
        'membership_date',
        'expiry_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'membership_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(NhanVien::class, 'user_id');
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(PhieuMuon::class, 'reader_id');
    }

    public function borrowingRequests(): HasMany
    {
        return $this->hasMany(YeuCauMuon::class, 'reader_id');
    }
}
