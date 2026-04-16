<?php

namespace App\Http\Controllers;

use App\Models\DocGia;
use App\Models\NhanVien;
use App\Models\PhieuMuon;
use App\Models\Sach;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class XacThucController extends BoDieuKhien
{
    public function showLoginForm(): View
    {
        return view('xac_thuc.dang_nhap', $this->authShowcaseData());
    }

    public function showRegisterForm(): View
    {
        return view('xac_thuc.dang_ky', $this->authShowcaseData());
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials['email'] = Str::lower(Str::squish((string) $credentials['email']));

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        /** @var NhanVien|null $user */
        $user = Auth::user();

        if (! $user?->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Tài khoản của bạn đã bị khóa.'])
                ->onlyInput('email');
        }

        if ($user->isCustomer() && ! $user->readerProfile) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Tài khoản khách hàng chưa được liên kết hồ sơ độc giả.'])
                ->onlyInput('email');
        }

        if ($user->isCustomer() && $user->readerProfile?->status === 'blocked') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Hồ sơ độc giả của bạn đang bị khóa. Vui lòng liên hệ thư viện để được hỗ trợ.'])
                ->onlyInput('email');
        }

        return redirect()->intended($this->redirectPathForUser($user));
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $email = Str::lower(Str::squish((string) $data['email']));
        $existingUser = NhanVien::query()->where('email', $email)->first();

        if ($existingUser) {
            $message = $existingUser->role === 'customer'
                ? 'Email này đã có tài khoản. Vui lòng đăng nhập.'
                : 'Email này đang được sử dụng trong hệ thống nội bộ.';

            return back()
                ->withErrors(['email' => $message])
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        $existingReader = DocGia::query()->where('email', $email)->first();

        if ($existingReader?->status === 'blocked') {
            return back()
                ->withErrors(['email' => 'Hồ sơ độc giả này đang bị khóa và chưa thể đăng ký trực tuyến.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        $account = DB::transaction(function () use ($data, $email, $existingReader): NhanVien {
            $account = NhanVien::query()->create([
                'name' => Str::squish((string) $data['full_name']),
                'email' => $email,
                'phone' => $this->nullableString($data['phone'] ?? null),
                'address' => $this->nullableString($data['address'] ?? null),
                'role' => 'customer',
                'is_active' => true,
                'password' => Hash::make((string) $data['password']),
            ]);

            $readerPayload = [
                'user_id' => $account->id,
                'full_name' => Str::squish((string) $data['full_name']),
                'email' => $email,
                'phone' => $this->nullableString($data['phone'] ?? null),
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'address' => $this->nullableString($data['address'] ?? null),
            ];

            if ($existingReader) {
                $existingReader->update($readerPayload);
            } else {
                DocGia::query()->create($readerPayload + [
                    'card_number' => $this->generateCardNumber(),
                    'membership_date' => now()->toDateString(),
                    'expiry_date' => now()->addYear()->toDateString(),
                    'status' => 'active',
                ]);
            }

            return $account;
        });

        Auth::login($account, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()
            ->route('khach_hang.tong_quan')
            ->with('success', 'Đăng ký tài khoản khách hàng thành công.');
    }

    public function showForgotPasswordForm(): View
    {
        return view('xac_thuc.quen_mat_khau', $this->authShowcaseData());
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'Nếu email tồn tại trong hệ thống, liên kết đặt lại mật khẩu đã được gửi.');
    }

    public function showResetPasswordForm(Request $request, string $token): View
    {
        return view('xac_thuc.dat_lai_mat_khau', array_merge([
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ], $this->authShowcaseData()));
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('dang_nhap')->with('status', 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập lại.');
        }

        return back()
            ->withErrors(['email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.'])
            ->withInput($request->only('email'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dang_nhap');
    }

    private function authShowcaseData(): array
    {
        return [
            'authMetrics' => [
                'catalog_titles' => Sach::count(),
                'available_copies' => (int) Sach::sum('available_copies'),
                'active_readers' => DocGia::where('status', 'active')->count(),
                'open_borrowings' => PhieuMuon::whereIn('status', ['borrowed', 'overdue'])->count(),
            ],
        ];
    }

    private function redirectPathForUser(NhanVien $user): string
    {
        return $user->isCustomer()
            ? route('khach_hang.tong_quan')
            : route('tong_quan');
    }

    private function generateCardNumber(): string
    {
        do {
            $cardNumber = 'DG-'.now()->format('Ymd').'-'.random_int(1000, 9999);
        } while (DocGia::query()->where('card_number', $cardNumber)->exists());

        return $cardNumber;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = Str::squish((string) $value);

        return $normalized === '' ? null : $normalized;
    }
}
