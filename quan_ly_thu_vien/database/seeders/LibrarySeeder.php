<?php

namespace Database\Seeders;

use App\Models\TacGia;
use App\Models\Sach;
use App\Models\TheLoai;
use App\Models\NhaXuatBan;
use App\Models\DocGia;
use App\Models\NhanVien;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        NhanVien::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Quản trị hệ thống',
                'phone' => '0900000001',
                'address' => 'TP.HCM',
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make('admin123'),
            ]
        );

        NhanVien::updateOrCreate(
            ['email' => 'thuthu@gmail.com'],
            [
                'name' => 'Thủ thư',
                'phone' => '0900000002',
                'address' => 'TP.HCM',
                'role' => 'librarian',
                'is_active' => true,
                'password' => Hash::make('thuthu123'),
            ]
        );

        $categoryNames = [
            'Văn học',
            'Khoa học',
            'Công nghệ thông tin',
            'Kinh doanh',
            'Thiếu nhi',
        ];

        $categories = collect($categoryNames)->mapWithKeys(function (string $name): array {
            $category = TheLoai::updateOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'description' => 'Danh mục '.$name,
                    'is_active' => true,
                ]
            );

            return [$name => $category];
        });

        $authorNames = [
            'Nguyễn Nhật Ánh',
            'Tô Hoài',
            'Martin Fowler',
            'Robert C. Martin',
            'Stephen Hawking',
        ];

        $authors = collect($authorNames)->mapWithKeys(function (string $name): array {
            $author = TacGia::updateOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name.'-'.md5($name)),
                    'nationality' => 'Việt Nam',
                    'is_active' => true,
                ]
            );

            return [$name => $author];
        });

        $publisherNames = [
            'NXB Trẻ',
            'NXB Kim Đồng',
            "O'Reilly Media",
        ];

        $publishers = collect($publisherNames)->mapWithKeys(function (string $name): array {
            $publisher = NhaXuatBan::updateOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'email' => Str::slug($name).'@example.com',
                    'phone' => '02812345678',
                    'address' => 'Việt Nam',
                    'is_active' => true,
                ]
            );

            return [$name => $publisher];
        });

        $books = [
            [
                'code' => 'BK001',
                'title' => 'Cho Tôi Xin Một Vé Đi Tuổi Thơ',
                'isbn' => '9786041000011',
                'category' => 'Văn học',
                'publisher' => 'NXB Trẻ',
                'authors' => ['Nguyễn Nhật Ánh'],
                'published_year' => 2008,
                'total_copies' => 20,
                'available_copies' => 20,
                'shelf_location' => 'A1',
            ],
            [
                'code' => 'BK002',
                'title' => 'Dế Mèn Phiêu Lưu Ký',
                'isbn' => '9786041000028',
                'category' => 'Thiếu nhi',
                'publisher' => 'NXB Kim Đồng',
                'authors' => ['Tô Hoài'],
                'published_year' => 1941,
                'total_copies' => 15,
                'available_copies' => 15,
                'shelf_location' => 'A2',
            ],
            [
                'code' => 'BK003',
                'title' => 'Refactoring',
                'isbn' => '9780134757599',
                'category' => 'Công nghệ thông tin',
                'publisher' => "O'Reilly Media",
                'authors' => ['Martin Fowler'],
                'published_year' => 2018,
                'total_copies' => 12,
                'available_copies' => 12,
                'shelf_location' => 'B1',
            ],
            [
                'code' => 'BK004',
                'title' => 'Clean Code',
                'isbn' => '9780132350884',
                'category' => 'Công nghệ thông tin',
                'publisher' => "O'Reilly Media",
                'authors' => ['Robert C. Martin'],
                'published_year' => 2008,
                'total_copies' => 10,
                'available_copies' => 10,
                'shelf_location' => 'B2',
            ],
        ];

        foreach ($books as $bookData) {
            $book = Sach::updateOrCreate(
                ['code' => $bookData['code']],
                [
                    'title' => $bookData['title'],
                    'slug' => Str::slug($bookData['title'].'-'.$bookData['code']),
                    'isbn' => $bookData['isbn'],
                    'category_id' => $categories[$bookData['category']]->id,
                    'publisher_id' => $publishers[$bookData['publisher']]->id,
                    'published_year' => $bookData['published_year'],
                    'total_copies' => $bookData['total_copies'],
                    'available_copies' => $bookData['available_copies'],
                    'shelf_location' => $bookData['shelf_location'],
                    'is_active' => true,
                ]
            );

            $book->authors()->sync(
                collect($bookData['authors'])
                    ->map(fn (string $authorName) => $authors[$authorName]->id)
                    ->all()
            );
        }

        $readers = [
            [
                'card_number' => 'DG0001',
                'full_name' => 'Nguyễn Văn A',
                'email' => 'nguyenvana@example.com',
                'phone' => '0911111111',
                'status' => 'active',
            ],
            [
                'card_number' => 'DG0002',
                'full_name' => 'Trần Thị B',
                'email' => 'tranthib@example.com',
                'phone' => '0922222222',
                'status' => 'active',
            ],
        ];

        foreach ($readers as $readerData) {
            DocGia::updateOrCreate(
                ['card_number' => $readerData['card_number']],
                [
                    'full_name' => $readerData['full_name'],
                    'email' => $readerData['email'],
                    'phone' => $readerData['phone'],
                    'membership_date' => now()->subMonths(2)->toDateString(),
                    'expiry_date' => now()->addYear()->toDateString(),
                    'status' => $readerData['status'],
                ]
            );
        }
    }
}
