<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cổng Khách Hàng | Quản Lý Thư Viện')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sea: #18444e;
            --sea-soft: #2a5b66;
            --sand: #f5efdf;
            --ink: #233338;
            --ink-soft: #607377;
            --ink-muted: #7a8a8f;
            --line: rgba(88, 86, 73, 0.16);
            --line-strong: rgba(109, 99, 80, 0.26);
            --shadow: 0 24px 70px rgba(24, 37, 42, 0.12);
            --shadow-soft: 0 14px 34px rgba(49, 56, 53, 0.08);
            --radius-xl: 26px;
            --radius-lg: 18px;
            --radius-md: 14px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            font-family: 'Manrope', sans-serif;
            background:
                radial-gradient(34rem 34rem at -6% 0%, rgba(189, 145, 94, 0.16), transparent 52%),
                radial-gradient(28rem 28rem at 104% 4%, rgba(74, 127, 132, 0.18), transparent 52%),
                linear-gradient(140deg, #f4ecd9 0%, #f8f5ef 44%, #eef5f2 100%);
        }

        .portal-shell {
            width: min(1340px, calc(100% - 32px));
            margin: 16px auto;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 18px;
            align-items: start;
        }

        .portal-rail,
        .portal-main {
            border: 1px solid rgba(73, 67, 57, 0.1);
            border-radius: var(--radius-xl);
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(14px);
            box-shadow: var(--shadow);
        }

        .portal-rail {
            position: sticky;
            top: 16px;
            overflow: hidden;
        }

        .brand-box {
            padding: 24px 22px 18px;
            color: #f7f3e9;
            background:
                radial-gradient(circle at 18% 18%, rgba(240, 195, 135, 0.18), transparent 18rem),
                linear-gradient(160deg, #173f48, #225760 58%, #28575b);
        }

        .brand-box h1 {
            margin: 0;
            font-family: 'Fraunces', serif;
            font-size: 1.75rem;
            line-height: 1.05;
        }

        .brand-box p {
            margin: 10px 0 0;
            color: rgba(246, 240, 225, 0.84);
            line-height: 1.7;
            font-size: 0.92rem;
        }

        .reader-badge {
            margin: 18px 20px 10px;
            padding: 16px;
            border-radius: 18px;
            background: linear-gradient(160deg, #fffaf1, #f3ede2);
            border: 1px solid rgba(121, 102, 78, 0.16);
            box-shadow: 0 12px 30px rgba(58, 49, 38, 0.07);
        }

        .reader-name {
            font-weight: 800;
            font-size: 1.02rem;
        }

        .reader-meta {
            margin-top: 6px;
            color: var(--ink-soft);
            font-size: 0.88rem;
            line-height: 1.7;
        }

        .nav-shell {
            padding: 8px 14px 20px;
        }

        .nav-label {
            padding: 0 8px;
            margin: 6px 0 8px;
            color: #8a7f6e;
            text-transform: uppercase;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.12em;
        }

        .portal-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 13px;
            margin-bottom: 8px;
            border-radius: 14px;
            text-decoration: none;
            color: #34525a;
            font-weight: 700;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .portal-link:hover {
            color: #23424a;
            border-color: rgba(95, 93, 80, 0.12);
            background: rgba(248, 244, 236, 0.92);
            transform: translateX(2px);
        }

        .portal-link.active {
            color: #f6f3ea;
            background: linear-gradient(145deg, #214d56, #163f48);
            box-shadow: 0 12px 26px rgba(28, 74, 82, 0.22);
        }

        .portal-link i {
            width: 18px;
        }

        .portal-main {
            padding: 18px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.86), rgba(250, 246, 239, 0.9));
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 8px 6px 20px;
            margin-bottom: 6px;
            flex-wrap: wrap;
            border-bottom: 1px solid rgba(107, 98, 80, 0.14);
        }

        .topbar h2 {
            margin: 0;
            font-family: 'Fraunces', serif;
            font-size: 2rem;
            color: #234147;
        }

        .topbar p {
            margin: 8px 0 0;
            color: var(--ink-soft);
            max-width: 760px;
        }

        .topbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .soft-chip {
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid rgba(112, 105, 88, 0.14);
            background: linear-gradient(160deg, #fff9ef, #f6efe3);
            color: #6f604d;
            font-size: 0.86rem;
            font-weight: 700;
        }

        .notice-banner {
            margin-bottom: 18px;
            padding: 15px 16px;
            border-radius: 18px;
            border: 1px solid rgba(183, 116, 79, 0.18);
            background: linear-gradient(180deg, rgba(254, 247, 239, 0.95), rgba(250, 237, 224, 0.88));
            color: #875536;
            box-shadow: 0 12px 26px rgba(183, 116, 79, 0.08);
        }

        .alert {
            border: 1px solid transparent;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(58, 49, 38, 0.08);
            padding: 0.95rem 1rem;
        }

        .alert-success {
            color: #285845;
            border-color: rgba(52, 117, 89, 0.2);
            background: linear-gradient(180deg, #f5fbf7, #ebf5ef);
        }

        .alert-info {
            color: #2d5973;
            border-color: rgba(63, 116, 145, 0.18);
            background: linear-gradient(180deg, #f3f9fc, #e9f3f9);
        }

        .alert-danger {
            color: #8f3f3a;
            border-color: rgba(160, 76, 69, 0.2);
            background: linear-gradient(180deg, #fdf6f5, #faecea);
        }

        .stat-card,
        .section-card,
        .book-card,
        .detail-item,
        .action-card {
            border-radius: var(--radius-lg);
            border: 1px solid var(--line);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.97), rgba(249, 245, 238, 0.92));
            box-shadow: var(--shadow-soft);
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            padding: 18px;
            height: 100%;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0 auto auto 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #21515a, #dfb073);
        }

        .stat-card.stat-accent-teal::before {
            background: linear-gradient(90deg, #1e5058, #4f8a91);
        }

        .stat-card.stat-accent-gold::before {
            background: linear-gradient(90deg, #a86b3b, #d6a561);
        }

        .stat-card.stat-accent-olive::before {
            background: linear-gradient(90deg, #5a6946, #93a46f);
        }

        .stat-card.stat-accent-rose::before {
            background: linear-gradient(90deg, #8f4b47, #d1948f);
        }

        .stat-label {
            color: #7a6c5d;
            font-size: 0.77rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-weight: 800;
            margin-top: 7px;
        }

        .stat-value {
            margin-top: 8px;
            font-family: 'Fraunces', serif;
            font-size: 1.92rem;
            line-height: 1.15;
            color: #1d4046;
        }

        .stat-note {
            margin-top: 6px;
            color: var(--ink-soft);
            font-size: 0.9rem;
        }

        .section-card {
            padding: 18px;
            margin-top: 18px;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            padding-bottom: 14px;
            margin-bottom: 14px;
            border-bottom: 1px solid rgba(109, 99, 80, 0.14);
        }

        .section-title {
            margin: 0;
            font-family: 'Fraunces', serif;
            font-size: 1.3rem;
            color: #26474d;
        }

        .book-card {
            padding: 16px;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .book-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 36px rgba(49, 56, 53, 0.12);
        }

        .book-title {
            font-weight: 800;
            font-size: 1rem;
            color: #233f45;
        }

        .book-meta,
        .muted-copy {
            color: var(--ink-soft);
            font-size: 0.9rem;
            line-height: 1.7;
        }

        .availability-pill,
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 800;
            border: 1px solid transparent;
        }

        .availability-pill.ok,
        .status-pill.ok {
            background: rgba(49, 126, 96, 0.12);
            border-color: rgba(49, 126, 96, 0.18);
            color: #2d6f56;
        }

        .availability-pill.warn,
        .status-pill.warn {
            background: rgba(183, 116, 79, 0.12);
            border-color: rgba(183, 116, 79, 0.2);
            color: #945a3a;
        }

        .availability-pill.neutral,
        .status-pill.neutral {
            background: rgba(99, 116, 122, 0.12);
            border-color: rgba(99, 116, 122, 0.16);
            color: #5a6970;
        }

        .status-pill.danger {
            background: rgba(154, 69, 63, 0.12);
            border-color: rgba(154, 69, 63, 0.18);
            color: #93433c;
        }

        .table {
            --bs-table-bg: transparent;
            --bs-border-color: rgba(99, 97, 85, 0.12);
            margin-bottom: 0;
        }

        .table > :not(caption) > * > * {
            padding: 0.84rem 0.78rem;
            vertical-align: middle;
        }

        .table thead th {
            color: #756858;
            font-size: 0.79rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            white-space: nowrap;
            background: rgba(244, 238, 226, 0.7);
        }

        .table tbody tr:hover {
            background: rgba(31, 79, 88, 0.05);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }

        .detail-item {
            padding: 14px 15px;
        }

        .detail-item.full {
            grid-column: 1 / -1;
        }

        .detail-label {
            display: block;
            margin-bottom: 5px;
            font-size: 0.76rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 800;
            color: #81725d;
        }

        .detail-value {
            color: #214047;
            font-weight: 700;
            line-height: 1.6;
        }

        .action-card {
            padding: 18px;
            height: 100%;
        }

        .action-card.success {
            background: linear-gradient(180deg, #f5fbf7, #ecf6ef);
            border-color: rgba(52, 117, 89, 0.18);
        }

        .action-card.warning {
            background: linear-gradient(180deg, #fdf7f0, #f7ede2);
            border-color: rgba(183, 116, 79, 0.18);
        }

        .action-card.danger {
            background: linear-gradient(180deg, #fdf6f5, #faecea);
            border-color: rgba(160, 76, 69, 0.18);
        }

        .form-label {
            font-weight: 800;
            color: #4a4b45;
            margin-bottom: 0.45rem;
        }

        .form-control,
        .form-select,
        textarea.form-control {
            border-radius: var(--radius-md);
            min-height: 48px;
            border-color: rgba(96, 92, 79, 0.18);
            background: rgba(255, 255, 255, 0.92);
            color: #24353a;
        }

        textarea.form-control {
            min-height: 120px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: rgba(183, 116, 79, 0.48);
            box-shadow: 0 0 0 0.24rem rgba(183, 116, 79, 0.12);
        }

        .btn {
            border-radius: 12px;
            font-weight: 700;
            padding-inline: 0.95rem;
        }

        .btn-primary {
            --bs-btn-bg: #1f4f58;
            --bs-btn-border-color: #1f4f58;
            --bs-btn-hover-bg: #173f47;
            --bs-btn-hover-border-color: #173f47;
        }

        .btn-outline-secondary {
            --bs-btn-color: #4a5e64;
            --bs-btn-border-color: rgba(103, 95, 82, 0.22);
            --bs-btn-hover-bg: #576c72;
            --bs-btn-hover-border-color: #576c72;
        }

        .btn-outline-danger {
            --bs-btn-color: #93433c;
            --bs-btn-border-color: rgba(147, 67, 60, 0.26);
            --bs-btn-hover-bg: #93433c;
            --bs-btn-hover-border-color: #93433c;
        }

        @@media (max-width: 1100px) {
            .portal-shell {
                grid-template-columns: 1fr;
            }

            .portal-rail {
                position: relative;
                top: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
@php
    $account = auth()->user();
    $reader = $account?->readerProfile;
@endphp
<div class="portal-shell">
    <aside class="portal-rail">
        <div class="brand-box">
            <h1>Quản Lý Thư Viện</h1>
            <p>Khu vực dành cho khách hàng tra cứu sách, theo dõi mượn trả và quản lý hồ sơ độc giả.</p>
        </div>

        <div class="reader-badge">
            <div class="reader-name">{{ $reader?->full_name }}</div>
            <div class="reader-meta">
                Mã thẻ: <strong>{{ $reader?->card_number }}</strong><br>
                Email: {{ $reader?->email }}<br>
                Hết hạn: {{ optional($reader?->expiry_date)->format('d/m/Y') ?? 'Chưa cập nhật' }}
            </div>
        </div>

        <div class="nav-shell">
            <div class="nav-label">Khách hàng</div>
            <a href="{{ route('khach_hang.tong_quan') }}" class="portal-link {{ request()->routeIs('khach_hang.tong_quan') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i>Trang tổng quan
            </a>
            <a href="{{ route('khach_hang.sach') }}" class="portal-link {{ request()->routeIs('khach_hang.sach') ? 'active' : '' }}">
                <i class="bi bi-book"></i>Tra cứu sách
            </a>
            <a href="{{ route('khach_hang.yeu_cau_muon') }}" class="portal-link {{ request()->routeIs('khach_hang.yeu_cau_muon*') ? 'active' : '' }}">
                <i class="bi bi-journal-plus"></i>Yêu cầu mượn
            </a>
            <a href="{{ route('khach_hang.lich_su_muon') }}" class="portal-link {{ request()->routeIs('khach_hang.lich_su_muon') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>Lịch sử mượn
            </a>
            <a href="{{ route('khach_hang.ho_so') }}" class="portal-link {{ request()->routeIs('khach_hang.ho_so') ? 'active' : '' }}">
                <i class="bi bi-person-vcard"></i>Hồ sơ cá nhân
            </a>
        </div>
    </aside>

    <main class="portal-main">
        <div class="topbar">
            <div>
                <h2>@yield('page-title', 'Cổng khách hàng')</h2>
                @hasSection('page-subtitle')
                    <p>@yield('page-subtitle')</p>
                @endif
            </div>

            <div class="topbar-actions">
                <span class="soft-chip"><i class="bi bi-calendar3"></i> {{ now()->format('d/m/Y') }}</span>
                <form action="{{ route('dang_xuat') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Đăng xuất</button>
                </form>
            </div>
        </div>

        @if($reader?->expiry_date && $reader->expiry_date->isPast())
            <div class="notice-banner">
                Thẻ độc giả của bạn đã hết hạn từ {{ $reader->expiry_date->format('d/m/Y') }}. Vui lòng liên hệ thư viện để gia hạn trước khi mượn sách mới.
            </div>
        @endif

        @include('thanh_phan.thong_bao')
        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
