<?php

namespace Tests\Feature;

use App\Models\ChiTietPhieuMuon;
use App\Models\DocGia;
use App\Models\NhanVien;
use App\Models\NhaXuatBan;
use App\Models\PhieuMuon;
use App\Models\Sach;
use App\Models\TacGia;
use App\Models\TheLoai;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KhachHangSuDungUngDungTest extends TestCase
{
    use RefreshDatabase;

    public function test_khach_hang_co_the_dang_ky_va_duoc_dang_nhap_ngay(): void
    {
        $response = $this->post(route('dang_ky.thuc_hien'), [
            'full_name' => 'Le Thi Ban Doc',
            'email' => 'bandoc@example.com',
            'phone' => '0909123456',
            'gender' => 'female',
            'date_of_birth' => '2000-01-01',
            'address' => 'Can Tho',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'remember' => '1',
        ]);

        $account = NhanVien::query()->where('email', 'bandoc@example.com')->first();
        $reader = DocGia::query()->where('email', 'bandoc@example.com')->first();

        $response->assertRedirect(route('khach_hang.tong_quan'));
        $this->assertNotNull($account);
        $this->assertNotNull($reader);
        $this->assertAuthenticatedAs($account);
        $this->assertSame('customer', $account->role);
        $this->assertSame($account->id, $reader->user_id);
        $this->assertSame('active', $reader->status);
        $this->assertNotNull($reader->card_number);
    }

    public function test_khach_hang_chi_xem_duoc_lich_su_muon_cua_chinh_minh(): void
    {
        $staff = NhanVien::factory()->create(['role' => 'librarian']);

        $category = TheLoai::create([
            'name' => 'Van hoc',
            'slug' => 'van-hoc',
            'is_active' => true,
        ]);

        $publisher = NhaXuatBan::create([
            'name' => 'NXB Tre',
            'slug' => 'nxb-tre',
            'is_active' => true,
        ]);

        $author = TacGia::create([
            'name' => 'Tac Gia A',
            'slug' => 'tac-gia-a',
            'is_active' => true,
        ]);

        $book = Sach::create([
            'code' => 'KH-001',
            'title' => 'Sach Cho Ban Doc',
            'slug' => 'sach-cho-ban-doc-kh-001',
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'total_copies' => 5,
            'available_copies' => 4,
            'is_active' => true,
        ]);
        $book->authors()->attach($author->id);

        $customerAccount = NhanVien::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email' => 'mot@example.com',
            'name' => 'Ban Doc Mot',
        ]);

        $otherCustomerAccount = NhanVien::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email' => 'hai@example.com',
            'name' => 'Ban Doc Hai',
        ]);

        $reader = DocGia::create([
            'user_id' => $customerAccount->id,
            'card_number' => 'DG-100',
            'full_name' => 'Ban Doc Mot',
            'email' => 'mot@example.com',
            'membership_date' => now()->toDateString(),
            'expiry_date' => now()->addYear()->toDateString(),
            'status' => 'active',
        ]);

        $otherReader = DocGia::create([
            'user_id' => $otherCustomerAccount->id,
            'card_number' => 'DG-200',
            'full_name' => 'Ban Doc Hai',
            'email' => 'hai@example.com',
            'membership_date' => now()->toDateString(),
            'expiry_date' => now()->addYear()->toDateString(),
            'status' => 'active',
        ]);

        $ownBorrowing = PhieuMuon::create([
            'code' => 'PM-OWN-001',
            'reader_id' => $reader->id,
            'processed_by' => $staff->id,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'borrowed',
            'fine_total' => 0,
        ]);

        ChiTietPhieuMuon::create([
            'borrowing_id' => $ownBorrowing->id,
            'book_id' => $book->id,
            'quantity' => 1,
            'returned_quantity' => 0,
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'borrowed',
        ]);

        $otherBorrowing = PhieuMuon::create([
            'code' => 'PM-OTHER-001',
            'reader_id' => $otherReader->id,
            'processed_by' => $staff->id,
            'borrow_date' => now()->subDay()->toDateString(),
            'due_date' => now()->addDays(6)->toDateString(),
            'status' => 'borrowed',
            'fine_total' => 0,
        ]);

        ChiTietPhieuMuon::create([
            'borrowing_id' => $otherBorrowing->id,
            'book_id' => $book->id,
            'quantity' => 1,
            'returned_quantity' => 0,
            'due_date' => now()->addDays(6)->toDateString(),
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($customerAccount)->get(route('khach_hang.lich_su_muon'));

        $response->assertOk();
        $response->assertSee('PM-OWN-001');
        $response->assertDontSee('PM-OTHER-001');
    }

    public function test_khach_hang_co_the_cap_nhat_ho_so_va_mat_khau(): void
    {
        $account = NhanVien::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'name' => 'Ban Doc Cu',
            'email' => 'bandoccu@example.com',
            'phone' => '0909000000',
            'address' => 'Can Tho',
            'password' => 'secret123',
        ]);

        $reader = DocGia::create([
            'user_id' => $account->id,
            'card_number' => 'DG-300',
            'full_name' => 'Ban Doc Cu',
            'email' => 'bandoccu@example.com',
            'phone' => '0909000000',
            'membership_date' => now()->toDateString(),
            'expiry_date' => now()->addYear()->toDateString(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($account)->put(route('khach_hang.ho_so.cap_nhat'), [
            'full_name' => 'Ban Doc Moi',
            'email' => 'bandocmoi@example.com',
            'phone' => '0911222333',
            'gender' => 'other',
            'date_of_birth' => '1999-12-31',
            'address' => 'Soc Trang',
            'current_password' => 'secret123',
            'new_password' => 'secret456',
            'new_password_confirmation' => 'secret456',
        ]);

        $response->assertRedirect();
        $account->refresh();
        $reader->refresh();

        $this->assertSame('Ban Doc Moi', $account->name);
        $this->assertSame('bandocmoi@example.com', $account->email);
        $this->assertSame('0911222333', $account->phone);
        $this->assertSame('Ban Doc Moi', $reader->full_name);
        $this->assertSame('bandocmoi@example.com', $reader->email);
        $this->assertSame('0911222333', $reader->phone);
        $this->assertTrue(Hash::check('secret456', $account->password));
    }
}
