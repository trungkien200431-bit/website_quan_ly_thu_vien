<?php

namespace Tests\Feature;

use App\Models\ChiTietPhieuMuon;
use App\Models\ChiTietYeuCauMuon;
use App\Models\DocGia;
use App\Models\NhanVien;
use App\Models\NhaXuatBan;
use App\Models\PhieuMuon;
use App\Models\Sach;
use App\Models\TacGia;
use App\Models\TheLoai;
use App\Models\YeuCauMuon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class YeuCauMuonSachTest extends TestCase
{
    use RefreshDatabase;

    public function test_khach_hang_co_the_gui_yeu_cau_muon_tu_trang_tra_cuu(): void
    {
        [$account, $reader] = $this->taoKhachHang('bandoc1@example.com', 'Bạn đọc Một');
        $book = $this->taoSach(availableCopies: 4);

        $response = $this->actingAs($account)
            ->from(route('khach_hang.sach'))
            ->post(route('khach_hang.yeu_cau_muon.tao'), [
                'book_id' => $book->id,
                'quantity' => 2,
                'note' => 'Cần cho bài nghiên cứu.',
            ]);

        $response->assertRedirect(route('khach_hang.sach'));

        $borrowingRequest = YeuCauMuon::query()->first();

        $this->assertNotNull($borrowingRequest);
        $this->assertSame($reader->id, $borrowingRequest->reader_id);
        $this->assertSame($account->id, $borrowingRequest->requested_by);
        $this->assertSame('pending', $borrowingRequest->status);
        $this->assertDatabaseHas('borrowing_request_items', [
            'borrowing_request_id' => $borrowingRequest->id,
            'book_id' => $book->id,
            'quantity' => 2,
        ]);

        $book->refresh();
        $this->assertSame(4, $book->available_copies);
    }

    public function test_khach_hang_khong_the_gui_yeu_cau_moi_khi_dang_co_sach_qua_han(): void
    {
        [$account, $reader] = $this->taoKhachHang('bandoc2@example.com', 'Bạn đọc Hai');
        $staff = NhanVien::factory()->create(['role' => 'librarian']);
        $book = $this->taoSach(code: 'S-002', title: 'Sách quá hạn', availableCopies: 3);

        $borrowing = PhieuMuon::create([
            'code' => 'PM-OVERDUE-001',
            'reader_id' => $reader->id,
            'processed_by' => $staff->id,
            'borrow_date' => now()->subDays(15)->toDateString(),
            'due_date' => now()->subDay()->toDateString(),
            'status' => 'borrowed',
            'fine_total' => 0,
        ]);

        ChiTietPhieuMuon::create([
            'borrowing_id' => $borrowing->id,
            'book_id' => $book->id,
            'quantity' => 1,
            'returned_quantity' => 0,
            'due_date' => now()->subDay()->toDateString(),
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($account)
            ->from(route('khach_hang.sach'))
            ->post(route('khach_hang.yeu_cau_muon.tao'), [
                'book_id' => $book->id,
                'quantity' => 1,
            ]);

        $response->assertRedirect(route('khach_hang.sach'));
        $response->assertSessionHasErrors('book_id');
        $this->assertDatabaseCount('borrowing_requests', 0);
    }

    public function test_khach_hang_co_the_huy_yeu_cau_muon_dang_cho_duyet(): void
    {
        [$account, $reader] = $this->taoKhachHang('bandoc3@example.com', 'Bạn đọc Ba');
        $book = $this->taoSach(code: 'S-003', title: 'Sách cần hủy', availableCopies: 5);
        $borrowingRequest = YeuCauMuon::create([
            'code' => 'YC-TEST-001',
            'reader_id' => $reader->id,
            'requested_by' => $account->id,
            'request_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        ChiTietYeuCauMuon::create([
            'borrowing_request_id' => $borrowingRequest->id,
            'book_id' => $book->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($account)
            ->from(route('khach_hang.yeu_cau_muon'))
            ->post(route('khach_hang.yeu_cau_muon.huy', $borrowingRequest), [
                'cancel_note' => 'Tôi chưa cần mượn nữa.',
            ]);

        $response->assertRedirect(route('khach_hang.yeu_cau_muon'));

        $borrowingRequest->refresh();
        $this->assertSame('cancelled', $borrowingRequest->status);
        $this->assertSame($account->id, $borrowingRequest->processed_by);
        $this->assertSame('Tôi chưa cần mượn nữa.', $borrowingRequest->processed_note);
    }

    public function test_nhan_vien_co_the_duyet_yeu_cau_va_tao_phieu_muon(): void
    {
        [$customerAccount, $reader] = $this->taoKhachHang('bandoc4@example.com', 'Bạn đọc Bốn');
        $staff = NhanVien::factory()->create([
            'role' => 'librarian',
            'name' => 'Thủ thư Duyệt',
        ]);
        $book = $this->taoSach(code: 'S-004', title: 'Sách chờ duyệt', availableCopies: 3);
        $borrowingRequest = YeuCauMuon::create([
            'code' => 'YC-TEST-002',
            'reader_id' => $reader->id,
            'requested_by' => $customerAccount->id,
            'request_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        ChiTietYeuCauMuon::create([
            'borrowing_request_id' => $borrowingRequest->id,
            'book_id' => $book->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($staff)->post(route('muon_tra.yeu_cau.duyet', $borrowingRequest), [
            'due_date' => now()->addDays(10)->toDateString(),
            'process_note' => 'Duyệt cho đợt mượn nghiên cứu.',
        ]);

        $response->assertRedirect(route('muon_tra.yeu_cau.chi_tiet', $borrowingRequest));

        $borrowingRequest->refresh();
        $book->refresh();

        $this->assertSame('approved', $borrowingRequest->status);
        $this->assertSame($staff->id, $borrowingRequest->processed_by);
        $this->assertNotNull($borrowingRequest->approved_borrowing_id);
        $this->assertSame(1, $book->available_copies);

        $borrowing = PhieuMuon::query()->find($borrowingRequest->approved_borrowing_id);

        $this->assertNotNull($borrowing);
        $this->assertSame($reader->id, $borrowing->reader_id);
        $this->assertSame($staff->id, $borrowing->processed_by);
        $this->assertSame('borrowed', $borrowing->status);
        $this->assertDatabaseHas('borrowing_items', [
            'borrowing_id' => $borrowing->id,
            'book_id' => $book->id,
            'quantity' => 2,
            'status' => 'borrowed',
        ]);
    }

    public function test_nhan_vien_co_the_tu_choi_yeu_cau_muon(): void
    {
        [$customerAccount, $reader] = $this->taoKhachHang('bandoc5@example.com', 'Bạn đọc Năm');
        $staff = NhanVien::factory()->create(['role' => 'librarian']);
        $book = $this->taoSach(code: 'S-005', title: 'Sách bị từ chối', availableCopies: 2);
        $borrowingRequest = YeuCauMuon::create([
            'code' => 'YC-TEST-003',
            'reader_id' => $reader->id,
            'requested_by' => $customerAccount->id,
            'request_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        ChiTietYeuCauMuon::create([
            'borrowing_request_id' => $borrowingRequest->id,
            'book_id' => $book->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($staff)->post(route('muon_tra.yeu_cau.tu_choi', $borrowingRequest), [
            'process_note' => 'Độc giả cần cập nhật lại hồ sơ trước khi mượn.',
        ]);

        $response->assertRedirect();

        $borrowingRequest->refresh();
        $this->assertSame('rejected', $borrowingRequest->status);
        $this->assertSame($staff->id, $borrowingRequest->processed_by);
        $this->assertSame('Độc giả cần cập nhật lại hồ sơ trước khi mượn.', $borrowingRequest->processed_note);
        $this->assertNull($borrowingRequest->approved_borrowing_id);
    }

    private function taoKhachHang(string $email, string $name): array
    {
        $account = NhanVien::factory()->create([
            'role' => 'customer',
            'is_active' => true,
            'email' => $email,
            'name' => $name,
        ]);

        $reader = DocGia::create([
            'user_id' => $account->id,
            'card_number' => 'DG-'.str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT),
            'full_name' => $name,
            'email' => $email,
            'membership_date' => now()->toDateString(),
            'expiry_date' => now()->addYear()->toDateString(),
            'status' => 'active',
        ]);

        return [$account, $reader];
    }

    private function taoSach(string $code = 'S-001', string $title = 'Sách tham khảo', int $availableCopies = 5): Sach
    {
        $category = TheLoai::create([
            'name' => 'Kỹ năng '.Str::upper(Str::random(5)),
            'slug' => 'ky-nang-'.Str::lower(Str::random(5)),
            'is_active' => true,
        ]);

        $publisher = NhaXuatBan::create([
            'name' => 'NXB '.Str::upper(Str::random(4)),
            'slug' => 'nxb-'.Str::lower(Str::random(4)),
            'is_active' => true,
        ]);

        $author = TacGia::create([
            'name' => 'Tác giả '.Str::upper(Str::random(3)),
            'slug' => 'tac-gia-'.Str::lower(Str::random(5)),
            'is_active' => true,
        ]);

        $book = Sach::create([
            'code' => $code,
            'title' => $title,
            'slug' => Str::slug($title.'-'.$code),
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'total_copies' => max($availableCopies, 1),
            'available_copies' => $availableCopies,
            'is_active' => true,
        ]);

        $book->authors()->attach($author->id);

        return $book;
    }
}
