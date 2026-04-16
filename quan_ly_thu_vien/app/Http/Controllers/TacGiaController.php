<?php

namespace App\Http\Controllers;

use App\Models\TacGia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TacGiaController extends BoDieuKhien
{
    public function index(Request $request): View
    {
        $query = TacGia::query()->withCount('books')->latest();

        if ($search = trim((string) $request->input('q'))) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->input('status') === 'active') {
            $query->where('is_active', true);
        }

        if ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        }

        $authors = $query->paginate(10)->withQueryString();

        return view('tac_gia.danh_sach', compact('authors'));
    }

    public function create(): View
    {
        return view('tac_gia.tao');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = Str::slug($data['name'].'-'.uniqid());

        TacGia::create($data);

        return redirect()->route('tac_gia.index')->with('success', 'Đã thêm tác giả mới.');
    }

    public function edit(TacGia $author): View
    {
        return view('tac_gia.sua', compact('author'));
    }

    public function update(Request $request, TacGia $author): RedirectResponse
    {
        $data = $this->validatedData($request, $author->id);
        $data['slug'] = Str::slug($data['name'].'-'.$author->id);

        $author->update($data);

        return redirect()->route('tac_gia.index')->with('success', 'Cập nhật tác giả thành công.');
    }

    public function destroy(TacGia $author): RedirectResponse
    {
        if ($author->books()->exists()) {
            return back()->with('error', 'Không thể xóa tác giả đã gắn với sách.');
        }

        $author->delete();

        return redirect()->route('tac_gia.index')->with('success', 'Đã xóa tác giả.');
    }

    private function validatedData(Request $request, ?int $authorId = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('authors', 'name')->ignore($authorId),
            ],
            'date_of_birth' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
