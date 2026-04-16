<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class TacGia extends Model
{
    use HasFactory;

    protected $table = 'authors';

    protected $fillable = [
        'name',
        'slug',
        'date_of_birth',
        'nationality',
        'biography',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Sach::class, 'author_book', 'author_id', 'book_id')->withTimestamps();
    }
}
