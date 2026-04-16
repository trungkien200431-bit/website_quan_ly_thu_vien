<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Sach extends Model
{
    use HasFactory;

    protected $table = 'books';

    protected $fillable = [
        'code',
        'title',
        'slug',
        'isbn',
        'category_id',
        'publisher_id',
        'published_year',
        'shelf_location',
        'total_copies',
        'available_copies',
        'price',
        'description',
        'cover_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TheLoai::class, 'category_id');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(NhaXuatBan::class, 'publisher_id');
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(TacGia::class, 'author_book', 'book_id', 'author_id')->withTimestamps();
    }

    public function borrowingItems(): HasMany
    {
        return $this->hasMany(ChiTietPhieuMuon::class, 'book_id');
    }

    public function borrowingRequestItems(): HasMany
    {
        return $this->hasMany(ChiTietYeuCauMuon::class, 'book_id');
    }
}
