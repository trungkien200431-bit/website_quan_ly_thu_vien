<?php

namespace Tests\Feature;

use App\Models\Sach;
use App\Models\TheLoai;
use App\Models\NhaXuatBan;
use App\Models\NhanVien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuanLyNhaXuatBanTest extends TestCase
{
    use RefreshDatabase;

    public function test_publishers_index_can_filter_only_linked_publishers(): void
    {
        $user = NhanVien::factory()->create(['role' => 'librarian']);

        $category = TheLoai::create([
            'name' => 'Kinh doanh',
            'slug' => 'kinh-doanh',
            'is_active' => true,
        ]);

        $linkedPublisher = NhaXuatBan::create([
            'name' => 'Lien ket Alpha',
            'slug' => 'linked-publisher',
            'is_active' => true,
        ]);

        $emptyPublisher = NhaXuatBan::create([
            'name' => 'Trong Beta',
            'slug' => 'empty-publisher',
            'is_active' => true,
        ]);

        Sach::create([
            'code' => 'BK-LINK',
            'title' => 'Sách liên kết',
            'slug' => 'sach-lien-ket-bk-link',
            'category_id' => $category->id,
            'publisher_id' => $linkedPublisher->id,
            'total_copies' => 4,
            'available_copies' => 3,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('nha_xuat_ban.index', [
            'usage' => 'linked',
        ]));

        $response->assertOk();
        $response->assertSee('Lien ket Alpha');
        $response->assertDontSee('Trong Beta');
    }

    public function test_store_normalizes_website_and_generates_unique_slug(): void
    {
        $user = NhanVien::factory()->create(['role' => 'librarian']);

        NhaXuatBan::create([
            'name' => 'A-B Thu Vien',
            'slug' => 'a-b-thu-vien',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('nha_xuat_ban.store'), [
            'name' => 'A B Thu Vien',
            'website' => 'example.com',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('nha_xuat_ban.index'));
        $response->assertSessionHas('success', 'Đã thêm nhà xuất bản.');

        $this->assertDatabaseHas('publishers', [
            'name' => 'A B Thu Vien',
            'website' => 'https://example.com',
            'slug' => 'a-b-thu-vien-1',
            'is_active' => true,
        ]);
    }
}
