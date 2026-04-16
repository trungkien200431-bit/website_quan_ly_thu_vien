<?php

namespace Tests\Feature;

use App\Models\DocGia;
use App\Models\NhanVien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DieuHuongTrangChuTest extends TestCase
{
    use RefreshDatabase;

    public function test_trang_chu_chuyen_huong_nguoi_chua_dang_nhap_ve_trang_dang_nhap(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('dang_nhap'));
    }

    public function test_trang_chu_chuyen_huong_nhan_vien_da_dang_nhap_ve_tong_quan_noi_bo(): void
    {
        $user = NhanVien::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('tong_quan'));
    }

    public function test_trang_chu_chuyen_huong_khach_hang_da_dang_nhap_ve_cong_khach_hang(): void
    {
        $account = NhanVien::factory()->create([
            'role' => 'customer',
            'is_active' => true,
        ]);

        DocGia::create([
            'user_id' => $account->id,
            'card_number' => 'DG-001',
            'full_name' => 'Nguyen Doc Gia',
            'email' => $account->email,
            'membership_date' => now()->toDateString(),
            'expiry_date' => now()->addYear()->toDateString(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($account)->get('/');

        $response->assertRedirect(route('khach_hang.tong_quan'));
    }
}
