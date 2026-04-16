<?php

namespace App\Http\Controllers;

use App\Models\DocGia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocGiaController extends BoDieuKhien
{
    public function index(Request $request): View
    {
        $query = DocGia::query()->latest('membership_date');

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('card_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $readers = $query->paginate(10)->withQueryString();

        return view('doc_gia.danh_sach', compact('readers'));
    }

    public function create(): View
    {
        return view('doc_gia.tao');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        DocGia::create($data);

        return redirect()->route('doc_gia.index')->with('success', 'Đã thêm độc giả mới.');
    }

    public function edit(DocGia $reader): View
    {
        return view('doc_gia.sua', compact('reader'));
    }

    public function update(Request $request, DocGia $reader): RedirectResponse
    {
        $data = $this->validatedData($request, $reader->id);

        $reader->update($data);

        if ($reader->user) {
            $reader->user->update([
                'name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'is_active' => $data['status'] !== 'blocked',
            ]);
        }

        return redirect()->route('doc_gia.index')->with('success', 'Cập nhật độc giả thành công.');
    }

    public function destroy(DocGia $reader): RedirectResponse
    {
        if ($reader->borrowings()->whereIn('status', ['borrowed', 'overdue'])->exists()) {
            return back()->with('error', 'Không thể xóa độc giả đang có phiếu mượn chưa hoàn tất.');
        }

        $reader->user?->delete();
        $reader->delete();

        return redirect()->route('doc_gia.index')->with('success', 'Đã xóa độc giả.');
    }

    private function validatedData(Request $request, ?int $readerId = null): array
    {
        /** @var DocGia|null $reader */
        $reader = $request->route('reader');
        $emailRules = $reader?->user
            ? ['required', 'email', 'max:255']
            : ['nullable', 'email', 'max:255'];

        return $request->validate([
            'card_number' => ['required', 'string', 'max:50', Rule::unique('readers', 'card_number')->ignore($readerId)],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                ...$emailRules,
                Rule::unique('readers', 'email')->ignore($readerId),
                Rule::unique('users', 'email')->ignore($reader?->user_id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:255'],
            'membership_date' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:membership_date'],
            'status' => ['required', Rule::in(['active', 'inactive', 'blocked'])],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
