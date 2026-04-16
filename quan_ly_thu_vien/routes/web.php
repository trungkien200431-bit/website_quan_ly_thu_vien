<?php

use App\Http\Controllers\BaoCaoController;
use App\Http\Controllers\DocGiaController;
use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\MuonTraController;
use App\Http\Controllers\NhaXuatBanController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\SachController;
use App\Http\Controllers\TacGiaController;
use App\Http\Controllers\TheLoaiController;
use App\Http\Controllers\TongQuanController;
use App\Http\Controllers\XacThucController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('dang_nhap');
    }

    if (auth()->user()?->role === 'customer') {
        return redirect()->route('khach_hang.tong_quan');
    }

    return redirect()->route('tong_quan');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/dang_nhap', [XacThucController::class, 'showLoginForm'])->name('dang_nhap');
    Route::post('/dang_nhap', [XacThucController::class, 'login'])->name('dang_nhap.thuc_hien');
    Route::get('/dang_ky', [XacThucController::class, 'showRegisterForm'])->name('dang_ky');
    Route::post('/dang_ky', [XacThucController::class, 'register'])->name('dang_ky.thuc_hien');
    Route::get('/quen_mat_khau', [XacThucController::class, 'showForgotPasswordForm'])->name('mat_khau.yeu_cau');
    Route::post('/quen_mat_khau', [XacThucController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:6,1')
        ->name('mat_khau.gui_email');
    Route::get('/dat_lai_mat_khau/{token}', [XacThucController::class, 'showResetPasswordForm'])->name('mat_khau.dat_lai');
    Route::post('/dat_lai_mat_khau', [XacThucController::class, 'resetPassword'])->name('mat_khau.cap_nhat');
});

Route::prefix('khach_hang')->name('khach_hang.')->group(function (): void {
    Route::middleware(['auth', 'role:customer'])->group(function (): void {
        Route::get('/', [KhachHangController::class, 'index'])->name('tong_quan');
        Route::get('/sach', [KhachHangController::class, 'books'])->name('sach');
        Route::get('/yeu_cau_muon', [KhachHangController::class, 'borrowingRequests'])->name('yeu_cau_muon');
        Route::post('/yeu_cau_muon', [KhachHangController::class, 'storeBorrowingRequest'])->name('yeu_cau_muon.tao');
        Route::post('/yeu_cau_muon/{borrowingRequest}/huy', [KhachHangController::class, 'cancelBorrowingRequest'])
            ->name('yeu_cau_muon.huy');
        Route::get('/lich_su_muon', [KhachHangController::class, 'borrowings'])->name('lich_su_muon');
        Route::get('/ho_so', [KhachHangController::class, 'profile'])->name('ho_so');
        Route::put('/ho_so', [KhachHangController::class, 'updateProfile'])->name('ho_so.cap_nhat');
    });
});

Route::match(['get', 'post'], '/dang_xuat', [XacThucController::class, 'logout'])->name('dang_xuat');

Route::middleware('auth')->group(function (): void {
    Route::get('/tong_quan', [TongQuanController::class, 'index'])->name('tong_quan');

    Route::middleware('role:admin,librarian')->group(function (): void {
        Route::resource('the_loai', TheLoaiController::class)
            ->parameters(['the_loai' => 'category'])
            ->names('the_loai')
            ->except(['show']);

        Route::resource('tac_gia', TacGiaController::class)
            ->parameters(['tac_gia' => 'author'])
            ->names('tac_gia')
            ->except(['show']);

        Route::resource('nha_xuat_ban', NhaXuatBanController::class)
            ->parameters(['nha_xuat_ban' => 'publisher'])
            ->names('nha_xuat_ban')
            ->except(['show']);

        Route::resource('sach', SachController::class)
            ->parameters(['sach' => 'book'])
            ->names('sach')
            ->except(['show']);

        Route::resource('doc_gia', DocGiaController::class)
            ->parameters(['doc_gia' => 'reader'])
            ->names('doc_gia')
            ->except(['show']);

        Route::get('/muon_tra/yeu_cau', [MuonTraController::class, 'requestIndex'])->name('muon_tra.yeu_cau');
        Route::get('/muon_tra/yeu_cau/{borrowingRequest}', [MuonTraController::class, 'requestShow'])->name('muon_tra.yeu_cau.chi_tiet');
        Route::post('/muon_tra/yeu_cau/{borrowingRequest}/duyet', [MuonTraController::class, 'approveBorrowingRequest'])
            ->name('muon_tra.yeu_cau.duyet');
        Route::post('/muon_tra/yeu_cau/{borrowingRequest}/tu_choi', [MuonTraController::class, 'rejectBorrowingRequest'])
            ->name('muon_tra.yeu_cau.tu_choi');

        Route::resource('muon_tra', MuonTraController::class)
            ->parameters(['muon_tra' => 'borrowing'])
            ->names('muon_tra')
            ->only(['index', 'create', 'store', 'show']);

        Route::post('/muon_tra/{borrowing}/tra_sach', [MuonTraController::class, 'processReturn'])
            ->name('muon_tra.tra_sach');

        Route::post('/muon_tra/{borrowing}/muc/{item}/thu_phat', [MuonTraController::class, 'payFine'])
            ->name('muon_tra.thu_phat');

        Route::get('/bao_cao', [BaoCaoController::class, 'index'])->name('bao_cao.tong_hop');
    });

    Route::middleware('role:admin')->group(function (): void {
        Route::resource('nhan_vien', NhanVienController::class)
            ->parameters(['nhan_vien' => 'user'])
            ->names('nhan_vien')
            ->except(['show']);
    });
});
