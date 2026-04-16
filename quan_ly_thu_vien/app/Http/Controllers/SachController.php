<?php

namespace App\Http\Controllers;

use App\Models\TacGia;
use App\Models\Sach;
use App\Models\TheLoai;
use App\Models\NhaXuatBan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SachController extends BoDieuKhien
{
    private const LOW_STOCK_THRESHOLD = 3;

    public function index(Request $request): View
    {
        $query = Sach::query()->with(['category', 'publisher', 'authors']);

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhereHas('category', function (Builder $categoryQuery) use ($search): void {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('publisher', function (Builder $publisherQuery) use ($search): void {
                        $publisherQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('authors', function (Builder $authorQuery) use ($search): void {
                        $authorQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($categoryId = $request->integer('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($publisherId = $request->integer('publisher_id')) {
            $query->where('publisher_id', $publisherId);
        }

        match ((string) $request->input('stock')) {
            'available' => $query->where('available_copies', '>', 0),
            'out' => $query->where('available_copies', 0),
            'low' => $query->where('available_copies', '>', 0)
                ->where('available_copies', '<=', self::LOW_STOCK_THRESHOLD),
            'borrowed' => $query->whereColumn('available_copies', '<', 'total_copies'),
            default => null,
        };

        match ((string) $request->input('status')) {
            'active' => $query->where('is_active', true),
            'inactive' => $query->where('is_active', false),
            default => null,
        };

        $this->applySorting($query, (string) $request->input('sort', 'latest'));

        $books = $query->paginate(10)->withQueryString();

        return view('sach.danh_sach', [
            'books' => $books,
            'categories' => TheLoai::query()->orderBy('name')->get(),
            'publishers' => NhaXuatBan::query()->orderBy('name')->get(),
            'bookStats' => $this->bookStats(),
            'lowStockThreshold' => self::LOW_STOCK_THRESHOLD,
        ]);
    }

    public function create(): View
    {
        return view('sach.tao', $this->formViewData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $authorIds = $data['author_ids'];
        unset($data['author_ids']);

        $data['slug'] = $this->buildUniqueSlug($data['title'], $data['code']);

        $book = Sach::create($data);
        $book->authors()->sync($authorIds);

        return redirect()->route('sach.index')->with('success', 'Đã thêm sách mới.');
    }

    public function edit(Sach $book): View
    {
        $book->load(['authors', 'category', 'publisher']);

        return view('sach.sua', $this->formViewData($book));
    }

    public function update(Request $request, Sach $book): RedirectResponse
    {
        $data = $this->validatedData($request, $book->id);
        $authorIds = $data['author_ids'];
        unset($data['author_ids']);

        $borrowedQuantity = $this->currentBorrowedQuantity($book);

        if ((int) $data['total_copies'] < $borrowedQuantity) {
            throw ValidationException::withMessages([
                'total_copies' => 'Tổng bản sao phải lớn hơn hoặc bằng số sách đang được mượn ('.$borrowedQuantity.').',
            ]);
        }

        if ((int) $data['available_copies'] > ((int) $data['total_copies'] - $borrowedQuantity)) {
            throw ValidationException::withMessages([
                'available_copies' => 'Số bản có sẵn không hợp lệ so với số sách đang được mượn.',
            ]);
        }

        $data['slug'] = $this->buildUniqueSlug($data['title'], $data['code'], $book->id);

        $book->update($data);
        $book->authors()->sync($authorIds);

        return redirect()->route('sach.index')->with('success', 'Cập nhật sách thành công.');
    }

    public function destroy(Sach $book): RedirectResponse
    {
        if ($book->borrowingItems()->exists()) {
            return back()->with('error', 'Không thể xóa sách đã phát sinh giao dịch mượn.');
        }

        $book->authors()->detach();
        $book->delete();

        return redirect()->route('sach.index')->with('success', 'Đã xóa sách.');
    }

    private function validatedData(Request $request, ?int $bookId = null): array
    {
        $currentYear = (int) now()->format('Y');

        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('books', 'code')->ignore($bookId)],
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:50', Rule::unique('books', 'isbn')->ignore($bookId)],
            'category_id' => ['required', 'exists:categories,id'],
            'publisher_id' => ['nullable', 'exists:publishers,id'],
            'published_year' => ['nullable', 'integer', 'between:1000,'.$currentYear],
            'shelf_location' => ['nullable', 'string', 'max:255'],
            'total_copies' => ['required', 'integer', 'min:0'],
            'available_copies' => ['required', 'integer', 'min:0', 'lte:total_copies'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'author_ids' => ['required', 'array', 'min:1'],
            'author_ids.*' => ['required', 'integer', 'distinct', 'exists:authors,id'],
        ]);

        $data['code'] = Str::upper(Str::squish((string) $data['code']));
        $data['title'] = Str::squish((string) $data['title']);
        $data['isbn'] = $this->nullableString($data['isbn'] ?? null);
        $data['shelf_location'] = $this->nullableString($data['shelf_location'] ?? null);
        $data['description'] = $this->nullableString($data['description'] ?? null);
        $data['cover_image'] = $this->nullableString($data['cover_image'] ?? null);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function buildUniqueSlug(string $title, string $code, ?int $ignoreId = null): string
    {
        $base = Str::slug($title.'-'.$code);
        $slug = $base;
        $counter = 1;

        while (Sach::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function formViewData(?Sach $book = null): array
    {
        $book?->loadMissing(['authors', 'category', 'publisher']);
        $selectedAuthorIds = $book?->authors->pluck('id')->all() ?? [];

        return [
            'book' => $book,
            'categories' => TheLoai::query()
                ->where('is_active', true)
                ->when($book?->category_id, fn (Builder $query, int $categoryId) => $query->orWhere('id', $categoryId))
                ->orderBy('name')
                ->get(),
            'publishers' => NhaXuatBan::query()
                ->where('is_active', true)
                ->when($book?->publisher_id, fn (Builder $query, int $publisherId) => $query->orWhere('id', $publisherId))
                ->orderBy('name')
                ->get(),
            'authors' => TacGia::query()
                ->where('is_active', true)
                ->when($selectedAuthorIds, fn (Builder $query, array $authorIds) => $query->orWhereIn('id', $authorIds))
                ->orderBy('name')
                ->get(),
            'currentBorrowedCount' => $book ? $this->currentBorrowedQuantity($book) : 0,
        ];
    }

    private function currentBorrowedQuantity(Sach $book): int
    {
        return (int) $book->borrowingItems()
            ->selectRaw('COALESCE(SUM(quantity - returned_quantity), 0) as total')
            ->value('total');
    }

    private function bookStats(): array
    {
        $inventoryTotal = (int) Sach::query()->sum('total_copies');
        $inventoryAvailable = (int) Sach::query()->sum('available_copies');

        return [
            'total_titles' => Sach::query()->count(),
            'active_titles' => Sach::query()->where('is_active', true)->count(),
            'out_of_stock' => Sach::query()->where('available_copies', 0)->count(),
            'borrowed_copies' => max(0, $inventoryTotal - $inventoryAvailable),
        ];
    }

    private function applySorting(Builder $query, string $sort): void
    {
        match ($sort) {
            'title_asc' => $query->orderBy('title'),
            'code_asc' => $query->orderBy('code'),
            'year_desc' => $query->orderByDesc('published_year')->orderBy('title'),
            'stock_low' => $query->orderBy('available_copies')->orderBy('title'),
            'stock_high' => $query->orderByDesc('available_copies')->orderBy('title'),
            default => $query->latest(),
        };
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
