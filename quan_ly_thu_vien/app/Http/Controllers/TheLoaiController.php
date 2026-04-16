<?php

namespace App\Http\Controllers;

use App\Models\TheLoai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TheLoaiController extends BoDieuKhien
{
    public function index(Request $request): View
    {
        $query = TheLoai::query()->withCount('books')->latest();

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->input('status') === 'active') {
            $query->where('is_active', true);
        }

        if ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        }

        $categories = $query->paginate(10)->withQueryString();

        return view('the_loai.danh_sach', compact('categories'));
    }

    public function create(): View
    {
        return view('the_loai.tao');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = Str::slug($data['name']);

        TheLoai::create($data);

        return redirect()->route('the_loai.index')->with('success', 'Đã thêm thể loại mới.');
    }

    public function edit(TheLoai $category): View
    {
        return view('the_loai.sua', compact('category'));
    }

    public function update(Request $request, TheLoai $category): RedirectResponse
    {
        $data = $this->validatedData($request, $category->id);
        $data['slug'] = Str::slug($data['name']);

        $category->update($data);

        return redirect()->route('the_loai.index')->with('success', 'Cập nhật thể loại thành công.');
    }

    public function destroy(TheLoai $category): RedirectResponse
    {
        if ($category->books()->exists()) {
            return back()->with('error', 'Không thể xóa thể loại đang có sách.');
        }

        $category->delete();

        return redirect()->route('the_loai.index')->with('success', 'Đã xóa thể loại.');
    }

    private function validatedData(Request $request, ?int $categoryId = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
