<?php

namespace Tests\Feature;

use App\Models\TacGia;
use App\Models\Sach;
use App\Models\TheLoai;
use App\Models\NhaXuatBan;
use App\Models\NhanVien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuanLySachTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_view_keeps_inactive_related_records_available_for_existing_book(): void
    {
        $user = NhanVien::factory()->create(['role' => 'librarian']);

        $category = TheLoai::create([
            'name' => 'Kho lưu',
            'slug' => 'kho-luu',
            'is_active' => false,
        ]);

        $publisher = NhaXuatBan::create([
            'name' => 'NXB Lưu Trữ',
            'slug' => 'nxb-luu-tru',
            'is_active' => false,
        ]);

        $author = TacGia::create([
            'name' => 'Tác giả cũ',
            'slug' => 'tac-gia-cu',
            'is_active' => false,
        ]);

        $book = Sach::create([
            'code' => 'BK-001',
            'title' => 'Dữ liệu di sản',
            'slug' => 'du-lieu-di-san-bk-001',
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'total_copies' => 5,
            'available_copies' => 5,
            'is_active' => true,
        ]);
        $book->authors()->attach($author->id);

        $response = $this->actingAs($user)->get(route('sach.edit', $book));

        $response->assertOk();
        $response->assertViewHas('categories', fn ($categories) => $categories->contains('id', $category->id));
        $response->assertViewHas('publishers', fn ($publishers) => $publishers->contains('id', $publisher->id));
        $response->assertViewHas('authors', fn ($authors) => $authors->contains('id', $author->id));
    }

    public function test_books_index_can_search_related_data_and_filter_low_stock(): void
    {
        $user = NhanVien::factory()->create(['role' => 'librarian']);

        $category = TheLoai::create([
            'name' => 'Công nghệ',
            'slug' => 'cong-nghe',
            'is_active' => true,
        ]);

        $matchingPublisher = NhaXuatBan::create([
            'name' => 'Alpha House',
            'slug' => 'alpha-house',
            'is_active' => true,
        ]);

        $otherPublisher = NhaXuatBan::create([
            'name' => 'Beta House',
            'slug' => 'beta-house',
            'is_active' => true,
        ]);

        $matchingAuthor = TacGia::create([
            'name' => 'Nguyen Alpha',
            'slug' => 'nguyen-alpha',
            'is_active' => true,
        ]);

        $otherAuthor = TacGia::create([
            'name' => 'Tran Beta',
            'slug' => 'tran-beta',
            'is_active' => true,
        ]);

        $matchingBook = Sach::create([
            'code' => 'BK-LOW',
            'title' => 'Kho hàng số',
            'slug' => 'kho-hang-so-bk-low',
            'category_id' => $category->id,
            'publisher_id' => $matchingPublisher->id,
            'total_copies' => 6,
            'available_copies' => 2,
            'is_active' => true,
        ]);
        $matchingBook->authors()->attach($matchingAuthor->id);

        $otherBook = Sach::create([
            'code' => 'BK-HIGH',
            'title' => 'Dữ liệu lớn',
            'slug' => 'du-lieu-lon-bk-high',
            'category_id' => $category->id,
            'publisher_id' => $otherPublisher->id,
            'total_copies' => 10,
            'available_copies' => 8,
            'is_active' => true,
        ]);
        $otherBook->authors()->attach($otherAuthor->id);

        $response = $this->actingAs($user)->get(route('sach.index', [
            'q' => 'Alpha House',
            'stock' => 'low',
        ]));

        $response->assertOk();
        $response->assertSee('Kho hàng số');
        $response->assertDontSee('Dữ liệu lớn');
    }
}
