<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\NhaXuatBan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NhaXuatBanController extends BoDieuKhien
{
    public function index(Request $request): View
    {
        $query = NhaXuatBan::query()
            ->withCount('books')
            ->withSum('books as inventory_total', 'total_copies')
            ->withSum('books as inventory_available', 'available_copies');

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('website', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        match ((string) $request->input('status')) {
            'active' => $query->where('is_active', true),
            'inactive' => $query->where('is_active', false),
            default => null,
        };

        match ((string) $request->input('usage')) {
            'linked' => $query->has('books'),
            'empty' => $query->doesntHave('books'),
            default => null,
        };

        $this->applySorting($query, (string) $request->input('sort', 'latest'));

        $publishers = $query->paginate(10)->withQueryString();

        return view('nha_xuat_ban.danh_sach', [
            'publishers' => $publishers,
            'publisherStats' => $this->publisherStats(),
        ]);
    }

    public function create(): View
    {
        return view('nha_xuat_ban.tao', $this->formViewData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->buildUniqueSlug($data['name']);

        NhaXuatBan::create($data);

        return redirect()->route('nha_xuat_ban.index')->with('success', 'Đã thêm nhà xuất bản.');
    }

    public function edit(NhaXuatBan $publisher): View
    {
        return view('nha_xuat_ban.sua', $this->formViewData($publisher));
    }

    public function update(Request $request, NhaXuatBan $publisher): RedirectResponse
    {
        $data = $this->validatedData($request, $publisher->id);
        $data['slug'] = $this->buildUniqueSlug($data['name'], $publisher->id);

        $publisher->update($data);

        return redirect()->route('nha_xuat_ban.index')->with('success', 'Cập nhật nhà xuất bản thành công.');
    }

    public function destroy(NhaXuatBan $publisher): RedirectResponse
    {
        if ($publisher->books()->exists()) {
            return back()->with('error', 'Không thể xóa nhà xuất bản đã gắn với sách.');
        }

        $publisher->delete();

        return redirect()->route('nha_xuat_ban.index')->with('success', 'Đã xóa nhà xuất bản.');
    }

    private function validatedData(Request $request, ?int $publisherId = null): array
    {
        $request->merge([
            'website' => $this->normalizeWebsite($request->input('website')),
        ]);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('publishers', 'name')->ignore($publisherId),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['name'] = Str::squish((string) $data['name']);
        $data['phone'] = $this->nullableString($data['phone'] ?? null);
        $data['email'] = $this->nullableString($data['email'] ?? null);
        $data['website'] = $this->nullableString($data['website'] ?? null);
        $data['address'] = $this->nullableString($data['address'] ?? null);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function buildUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (NhaXuatBan::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function formViewData(?NhaXuatBan $publisher = null): array
    {
        $recentBooks = collect();
        $publisherInsights = [
            'books_count' => 0,
            'inventory_total' => 0,
            'inventory_available' => 0,
        ];

        if ($publisher) {
            $publisherInsights = [
                'books_count' => $publisher->books()->count(),
                'inventory_total' => (int) $publisher->books()->sum('total_copies'),
                'inventory_available' => (int) $publisher->books()->sum('available_copies'),
            ];

            $recentBooks = $publisher->books()
                ->with('category')
                ->latest()
                ->limit(5)
                ->get();
        }

        return [
            'publisher' => $publisher,
            'publisherInsights' => $publisherInsights,
            'recentBooks' => $recentBooks,
        ];
    }

    private function publisherStats(): array
    {
        return [
            'total_publishers' => NhaXuatBan::query()->count(),
            'active_publishers' => NhaXuatBan::query()->where('is_active', true)->count(),
            'linked_publishers' => NhaXuatBan::query()->has('books')->count(),
            'total_titles' => Sach::query()->count(),
        ];
    }

    private function applySorting(Builder $query, string $sort): void
    {
        match ($sort) {
            'name_asc' => $query->orderBy('name'),
            'books_desc' => $query->orderByDesc('books_count')->orderBy('name'),
            'books_asc' => $query->orderBy('books_count')->orderBy('name'),
            default => $query->latest(),
        };
    }

    private function normalizeWebsite(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $website = trim((string) $value);

        if ($website === '') {
            return null;
        }

        if (! Str::startsWith($website, ['http://', 'https://'])) {
            $website = 'https://'.$website;
        }

        return $website;
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
