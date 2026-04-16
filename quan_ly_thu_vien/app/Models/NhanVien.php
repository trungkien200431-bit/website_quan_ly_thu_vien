<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\NhanVienFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class NhanVien extends Authenticatable
{
    /** @use HasFactory<NhanVienFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'role',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function processedBorrowings(): HasMany
    {
        return $this->hasMany(PhieuMuon::class, 'processed_by');
    }

    public function readerProfile(): HasOne
    {
        return $this->hasOne(DocGia::class, 'user_id');
    }

    public function requestedBorrowingRequests(): HasMany
    {
        return $this->hasMany(YeuCauMuon::class, 'requested_by');
    }

    public function processedBorrowingRequests(): HasMany
    {
        return $this->hasMany(YeuCauMuon::class, 'processed_by');
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'librarian'], true);
    }
}
