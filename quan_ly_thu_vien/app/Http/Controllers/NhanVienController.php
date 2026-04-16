<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NhanVienController extends BoDieuKhien
{
    public function index(Request $request): View
    {
        $query = NhanVien::query()
            ->whereIn('role', ['admin', 'librarian'])
            ->latest();

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($request->input('status') === 'active') {
            $query->where('is_active', true);
        }

        if ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        }

        $users = $query->paginate(10)->withQueryString();

        return view('nhan_vien.danh_sach', compact('users'));
    }

    public function create(): View
    {
        return view('nhan_vien.tao');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['password'] = Hash::make($data['password']);

        NhanVien::create($data);

        return redirect()->route('nhan_vien.index')->with('success', 'Đã tạo tài khoản nhân viên.');
    }

    public function edit(NhanVien $user): View
    {
        $this->ensureStaffAccount($user);

        return view('nhan_vien.sua', compact('user'));
    }

    public function update(Request $request, NhanVien $user): RedirectResponse
    {
        $this->ensureStaffAccount($user);

        $data = $this->validatedData($request, $user->id);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('nhan_vien.index')->with('success', 'Cập nhật tài khoản thành công.');
    }

    public function destroy(NhanVien $user): RedirectResponse
    {
        $this->ensureStaffAccount($user);

        if ((int) auth()->id() === (int) $user->id) {
            return back()->with('error', 'Bạn không thể tự xóa tài khoản của mình.');
        }

        if ($user->processedBorrowings()->exists()) {
            return back()->with('error', 'Không thể xóa tài khoản đã phát sinh giao dịch mượn.');
        }

        $user->delete();

        return redirect()->route('nhan_vien.index')->with('success', 'Đã xóa tài khoản.');
    }

    private function validatedData(Request $request, ?int $userId = null): array
    {
        $passwordRule = $userId
            ? ['nullable', 'string', 'min:6', 'confirmed']
            : ['required', 'string', 'min:6', 'confirmed'];

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'role' => ['required', Rule::in(['admin', 'librarian'])],
            'is_active' => ['nullable', 'boolean'],
            'password' => $passwordRule,
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function ensureStaffAccount(NhanVien $user): void
    {
        abort_unless($user->isStaff(), 404);
    }
}
