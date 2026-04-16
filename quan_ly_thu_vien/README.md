# Quan Ly Thu Vien (Laravel 12 + MySQL)

He thong quan ly thu vien da duoc xay dung theo huong ung dung noi bo, phuc vu nghiep vu van hanh thu vien:

- Dang nhap / dang xuat (phan quyen `admin`, `librarian`)
- Quen mat khau / dat lai mat khau qua email
- Quan ly the loai, tac gia, nha xuat ban
- Quan ly sach (nhieu tac gia, ton kho tong / san co)
- Quan ly doc gia
- Quan ly phieu muon, tra sach theo tung dau sach
- Tu dong tinh qua han, tinh tien phat theo ngay
- Thu tien phat (toan bo hoac theo tung sach)
- Bao cao tong hop: qua han, top sach muon, phat da thu/chua thu

## 1) Cau hinh MySQL

Cap nhat `.env` (da duoc set san mau):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quan_ly_thu_vien
DB_USERNAME=root
DB_PASSWORD=
```

Tao database `quan_ly_thu_vien` trong MySQL truoc khi migrate.

## 2) Cai dat va khoi tao du lieu

```bash
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Truy cap: `http://127.0.0.1:8000`

## 3) Tai khoan mac dinh

- Admin: `admin@library.local` / `admin123`
- Thu thu: `thuthu@library.local` / `thuthu123`

## 4) Quen mat khau

- Truy cap `/quen_mat_khau`
- He thong se gui link reset vao email (dang cau hinh `MAIL_MAILER=log` mac dinh)
- Link reset se co dang `/dat_lai_mat_khau/{token}?email=...`

## 5) Cac route quan trong

- `/dang_nhap`
- `/quen_mat_khau`
- `/tong_quan`
- `/sach`
- `/doc_gia`
- `/muon_tra`
- `/bao_cao`
- `/nhan_vien` (chi admin)

## 6) Quy tac tinh phat

- Muc phat mac dinh: `5000` / ngay / moi cuon tra tre
- Duoc tinh khi cap nhat tra sach trong phieu muon
- Co the thu phat ngay khi tra hoac thu rieng tren man hinh chi tiet phieu

## 7) Nen project de chuyen sang may khac

### Truoc khi nen (may cu)

1. Khuyen nghi xoa cac thu muc co the tai lai de file zip nhe hon:

```powershell
Remove-Item -Recurse -Force vendor,node_modules -ErrorAction SilentlyContinue
Remove-Item -Force bootstrap\cache\*.php -ErrorAction SilentlyContinue
```

2. Nen toan bo source:

```powershell
Compress-Archive -Path * -DestinationPath ..\quan_ly_thu_vien.zip -Force
```

3. Khuyen nghi khong gui file `.env` neu co thong tin nhay cam (DB password, mail password).
   Su dung `.env.example` va cau hinh lai tren may moi.

### Chay project o may moi

1. Giai nen va vao thu muc project:

```bash
cd quan_ly_thu_vien
```

2. Cai dependency:

```bash
composer install
```

Neu project co su dung build frontend thi chay them:

```bash
npm install
npm run build
```

3. Tao file `.env`:

```bash
cp .env.example .env
```

Neu dung Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

4. Cap nhat `.env` (DB, MAIL, APP_URL) theo may moi.

5. Tao key + migrate:

```bash
php artisan key:generate
php artisan migrate --seed
php artisan optimize:clear
```

6. Chay app:

```bash
php artisan serve
```

### Neu muon giu du lieu that (khong seed moi)

May cu (export DB):

```bash
mysqldump -u root -p quan_ly_thu_vien > quan_ly_thu_vien.sql
```

May moi (import DB):

```bash
mysql -u root -p quan_ly_thu_vien < quan_ly_thu_vien.sql
```

Sau do khong can `migrate:fresh --seed`, chi can:

```bash
php artisan migrate
```

### Neu co file upload (anh bia, tai lieu...)

- Copy them thu muc `storage/app/public` tu may cu sang may moi.
- Chay:

```bash
php artisan storage:link
```

## 8) Nghiep vu su dung he thong (SOP van hanh)

### 8.1 Vai tro va pham vi su dung

- `admin`:
  - Quan ly tai khoan nhan vien (`/nhan_vien`)
  - Su dung toan bo chuc nang nghiep vu khac
- `librarian` (thu thu):
  - Quan ly du lieu thu vien, muon/tra, thu phat, bao cao
  - Khong duoc quan ly tai khoan nhan vien

### 8.2 Quy trinh khoi tao du lieu ban dau

1. Dang nhap bang tai khoan `admin`.
2. Tao tai khoan nhan vien trong muc `Nhan vien` (`/nhan_vien`) neu can cap them tai khoan thu thu.
3. Tao du lieu danh muc nen:
   - The loai (`/the_loai`)
   - Tac gia (`/tac_gia`)
   - Nha xuat ban (`/nha_xuat_ban`)
4. Tao sach (`/sach`):
   - Gan the loai, tac gia, NXB
   - Nhap `total_copies` va `available_copies`
5. Tao doc gia (`/doc_gia`):
   - Ma the, thong tin lien he
   - Trang thai the (`active/inactive/blocked`)

### 8.3 Quy trinh muon sach

1. Vao `Muon tra` -> `Tao phieu muon` (`/muon_tra/create`).
2. Chon doc gia:
   - Chi chap nhan doc gia `active`
   - Tu choi neu the het han
3. Nhap ngay muon + han tra (han tra phai >= ngay muon).
4. Chon sach va so luong muon:
   - Moi sach phai con `available_copies`
   - Neu khong du so luong thi he thong bao loi
5. Luu phieu:
   - Tao ma phieu muon
   - Tu dong tru ton `available_copies`

### 8.4 Quy trinh tra sach

1. Mo chi tiet phieu muon (`/muon_tra/{id}`).
2. Nhap ngay tra va so luong tra cho tung dau sach.
3. He thong xu ly:
   - Tang lai `available_copies` theo so luong da tra
   - Tinh phat neu tra tre:
     - `fine = so_ngay_tre * 5000 * so_luong_tra_tre`
4. Trang thai phieu tu dong cap nhat:
   - `borrowed` / `overdue` / `returned`
5. Co the thu phat ngay trong luc tra.

### 8.5 Thu phat bo sung

- Tai trang chi tiet phieu muon:
  - Co the thu phat theo tung dong sach
  - Khong duoc thu vuot qua so tien phat con lai
- Lich su thu phat duoc luu de doi soat.

### 8.6 Bao cao va doi soat cuoi ngay

Tai `/bao_cao`:
- Theo doi:
  - So phieu muon/trong thang
  - So phieu qua han
  - Tong phat, da thu, con phai thu
- Kiem tra danh sach qua han de nhac doc gia.
- Kiem tra top sach muon nhieu de bo sung ton kho.

### 8.7 Quy dinh xoa/sua du lieu de tranh sai lech nghiep vu

- Khong xoa duoc:
  - The loai neu da co sach
  - Tac gia/NXB neu da gan voi sach
  - Sach neu da phat sinh giao dich muon
  - Doc gia neu dang co phieu `borrowed`/`overdue`
  - Nhan vien neu da xu ly phieu muon
- `admin` khong the tu xoa tai khoan dang dang nhap.

### 8.8 Quen mat khau va khoi phuc tai khoan

1. Nguoi dung vao `/quen_mat_khau`.
2. Nhap email da dang ky.
3. Mo link reset trong email va dat lai mat khau.
4. Dang nhap lai bang mat khau moi.

Luu y:
- De gui email that, can cau hinh SMTP trong `.env` (khong dung `MAIL_MAILER=log`).
- He thong khong mo dang ky cong khai; tai khoan nhan vien moi duoc tao boi `admin`.
