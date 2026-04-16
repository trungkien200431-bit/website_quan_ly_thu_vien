<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class NhaXuatBan extends Model
{
    use HasFactory;

    protected $table = 'publishers';

    protected $fillable = [
        'name',
        'slug',
        'phone',
        'email',
        'website',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Sach::class, 'publisher_id');
    }
}
