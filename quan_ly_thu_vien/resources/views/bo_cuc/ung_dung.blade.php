<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản Lý Thư Viện')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --paper: #f4efe2;
            --paper-2: #faf6ee;
            --paper-3: #f0e7d6;
            --mist: #eef5f3;
            --ink: #1d2a2d;
            --ink-soft: #536164;
            --ink-muted: #6d7b7e;
            --moss: #2e5960;
            --moss-deep: #23454b;
            --clay: #a7653e;
            --olive: #65724f;
            --line: #ddcfb6;
            --line-strong: #cdbb98;
            --success-soft: #edf6f0;
            --warning-soft: #fbf2e7;
            --danger-soft: #fbebea;
            --info-soft: #ecf4f8;
            --shadow: 0 24px 60px rgba(39, 33, 25, 0.12);
            --shadow-soft: 0 14px 34px rgba(57, 49, 40, 0.08);
            --radius-xl: 22px;
            --radius-lg: 16px;
            --radius-md: 12px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: var(--ink);
            font-family: 'Manrope', sans-serif;
            background:
                radial-gradient(42rem 42rem at -8% -10%, #e8ddc5 0%, transparent 50%),
                radial-gradient(35rem 35rem at 105% 5%, #d8e7e1 0%, transparent 52%),
                linear-gradient(120deg, #f1ecdf, #f8f5ee 40%, #f2f7f5 100%);
        }

        .atelier-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 300px 1fr;
        }

        .rail {
            border-right: 1px solid rgba(71, 63, 52, 0.12);
            padding: 22px 16px 16px;
            background:
                linear-gradient(180deg, rgba(244, 239, 226, 0.97), rgba(244, 237, 222, 0.95));
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .rail-brand {
            border: 1px solid var(--line);
            background:
                linear-gradient(145deg, rgba(248, 243, 231, 0.98), rgba(240, 229, 208, 0.96));
            border-radius: 18px;
            padding: 14px;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.72),
                0 10px 26px rgba(83, 69, 49, 0.08);
            margin-bottom: 16px;
        }

        .rail-brand-title {
            margin: 0;
            font-family: 'Fraunces', serif;
            font-size: 1.45rem;
            line-height: 1.1;
            color: #2c3f42;
            letter-spacing: 0.2px;
        }

        .rail-section {
            margin: 16px 0 10px;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #847c70;
            padding: 0 8px;
            font-weight: 800;
        }

        .rail .nav-link {
            border: 1px solid transparent;
            border-radius: 14px;
            color: #365058;
            font-weight: 700;
            padding: 11px 12px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 9px;
            transition: all 0.22s ease;
        }

        .rail .nav-link i {
            width: 18px;
            color: #705f4d;
            font-size: 1rem;
        }

        .rail .nav-link:hover {
            border-color: #d8c7ac;
            background: linear-gradient(140deg, #f7efe2, #f5f2e9);
            transform: translateX(2px);
        }

        .rail .nav-link.active {
            color: #f7f2e8;
            border-color: #294a4d;
            background: linear-gradient(145deg, #2f575b, #244447);
            box-shadow: 0 10px 24px rgba(36, 68, 71, 0.28);
        }

        .rail .nav-link.active i {
            color: #f0d8b4;
        }

        .rail-foot {
            margin-top: 14px;
            border: 1px dashed #d3c5ab;
            background: rgba(255, 255, 255, 0.58);
            border-radius: 14px;
            padding: 12px;
            font-size: 0.83rem;
            color: #625f57;
        }

        .workspace {
            padding: 20px 24px 28px;
        }

        .headline {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(217, 203, 177, 0.78);
            background:
                linear-gradient(145deg, rgba(249, 244, 232, 0.94), rgba(239, 246, 243, 0.76));
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
            padding: 18px 18px 18px 22px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .headline::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 7px;
            background: linear-gradient(180deg, #2b5960, #467078);
        }

        .headline-title {
            margin: 0;
            font-family: 'Fraunces', serif;
            font-size: 1.48rem;
            color: #2d4145;
        }

        .headline-sub {
            margin: 6px 0 0;
            color: var(--ink-soft);
            font-size: 0.92rem;
            max-width: 780px;
        }

        .chip {
            border-radius: 999px;
            border: 1px solid #d7c9af;
            background: linear-gradient(140deg, #fcf7ee, #f5ede1);
            color: #5d5345;
            padding: 8px 12px;
            font-size: 0.81rem;
            font-weight: 700;
        }

        .panel-stage > * {
            animation: stageRise 0.45s ease both;
        }

        .panel-stage > *:nth-child(2) { animation-delay: 0.04s; }
        .panel-stage > *:nth-child(3) { animation-delay: 0.08s; }
        .panel-stage > *:nth-child(4) { animation-delay: 0.12s; }
        .panel-stage > *:nth-child(5) { animation-delay: 0.16s; }

        @keyframes stageRise {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            border: 1px solid var(--line-strong);
            border-radius: var(--radius-lg);
            background:
                linear-gradient(180deg, rgba(255, 253, 248, 0.98), rgba(252, 248, 239, 0.95));
            box-shadow: var(--shadow-soft);
            overflow: hidden;
        }

        .card-header {
            padding: 0.95rem 1.1rem;
            background:
                linear-gradient(180deg, #fbf3e5, #f0e3cb);
            border-bottom: 1px solid var(--line-strong);
            font-family: 'Fraunces', serif;
            font-size: 1.02rem;
            color: #3b3e35;
        }

        .card-body {
            padding: 1rem 1.1rem;
        }

        .card-footer {
            border-top: 1px solid rgba(205, 187, 152, 0.6);
            background: rgba(250, 246, 237, 0.82);
        }

        .table {
            --bs-table-bg: transparent;
            --bs-border-color: #e1d4bf;
            margin-bottom: 0;
        }

        .table > :not(caption) > * > * {
            padding: 0.84rem 0.78rem;
            vertical-align: middle;
        }

        .table thead th {
            background: linear-gradient(180deg, #f7efe2, #ecdfc8);
            color: #4c4a44;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            white-space: nowrap;
            border-bottom-width: 1px;
        }

        .table tbody td {
            color: #314448;
        }

        .table tbody tr:nth-child(even) {
            background: rgba(248, 241, 227, 0.44);
        }

        .table tbody tr:hover {
            background: rgba(44, 84, 90, 0.06);
        }

        .table-responsive {
            border-radius: 14px;
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
            border-color: #d9cab2;
            min-height: 44px;
            background-color: #fffdf9;
            color: #203237;
        }

        textarea.form-control {
            min-height: 120px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #9d7a58;
            box-shadow: 0 0 0 0.2rem rgba(177, 109, 66, 0.16);
        }

        .btn {
            border-radius: 12px;
            font-weight: 700;
            padding-inline: 0.95rem;
        }

        .btn-primary {
            --bs-btn-bg: #2f5a5d;
            --bs-btn-border-color: #2f5a5d;
            --bs-btn-hover-bg: #264a4d;
            --bs-btn-hover-border-color: #264a4d;
        }

        .btn-success {
            --bs-btn-bg: #a7623f;
            --bs-btn-border-color: #a7623f;
            --bs-btn-hover-bg: #8f5334;
            --bs-btn-hover-border-color: #8f5334;
        }

        .btn-outline-primary {
            --bs-btn-color: #29545b;
            --bs-btn-border-color: rgba(41, 84, 91, 0.28);
            --bs-btn-hover-bg: #29545b;
            --bs-btn-hover-border-color: #29545b;
        }

        .btn-outline-secondary {
            --bs-btn-color: #5e615b;
            --bs-btn-border-color: rgba(110, 104, 90, 0.24);
            --bs-btn-hover-bg: #60594f;
            --bs-btn-hover-border-color: #60594f;
        }

        .btn-outline-danger {
            --bs-btn-color: #93433c;
            --bs-btn-border-color: rgba(147, 67, 60, 0.26);
            --bs-btn-hover-bg: #93433c;
            --bs-btn-hover-border-color: #93433c;
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

        .badge {
            border-radius: 999px;
            padding: 0.5rem 0.72rem;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.04em;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            border: 1px solid #d8c5a7;
            border-radius: var(--radius-lg);
            padding: 16px;
            background:
                linear-gradient(160deg, rgba(255, 251, 242, 0.98), rgba(245, 239, 224, 0.92));
            box-shadow: 0 10px 24px rgba(73, 63, 50, 0.08);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0 auto auto 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #2d5860, #b06c42);
            opacity: 0.92;
        }

        .stat-card.accent-success::before {
            background: linear-gradient(90deg, #366a55, #7ea784);
        }

        .stat-card.accent-warning::before {
            background: linear-gradient(90deg, #a7623f, #d3a062);
        }

        .stat-card.accent-danger::before {
            background: linear-gradient(90deg, #994844, #d28781);
        }

        .stat-card.accent-info::before {
            background: linear-gradient(90deg, #2d5969, #75a6b1);
        }

        .stat-label {
            color: #6f6452;
            font-size: 0.77rem;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            font-weight: 800;
            margin-top: 6px;
        }

        .stat-value {
            margin-top: 8px;
            font-family: 'Fraunces', serif;
            font-size: 1.68rem;
            line-height: 1.15;
            color: #2e4b4d;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }

        .info-item {
            border: 1px solid rgba(205, 187, 152, 0.72);
            border-radius: 14px;
            padding: 12px 14px;
            background: linear-gradient(180deg, rgba(255, 253, 248, 0.98), rgba(247, 241, 230, 0.88));
        }

        .info-item.full {
            grid-column: 1 / -1;
        }

        .info-label {
            display: block;
            margin-bottom: 5px;
            font-size: 0.77rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #7a6c58;
        }

        .info-value {
            color: #274046;
            font-weight: 700;
            line-height: 1.6;
        }

        .surface-note {
            border: 1px dashed rgba(122, 108, 88, 0.32);
            border-radius: 14px;
            padding: 12px 14px;
            background: rgba(252, 248, 239, 0.8);
            color: #5d615b;
        }

        .action-panel {
            border-radius: var(--radius-lg);
            border: 1px solid transparent;
            padding: 1rem 1.1rem;
            height: 100%;
        }

        .action-panel.success {
            border-color: rgba(67, 120, 93, 0.2);
            background: linear-gradient(180deg, #f5fbf7, #ebf5ef);
        }

        .action-panel.warning {
            border-color: rgba(177, 109, 66, 0.22);
            background: linear-gradient(180deg, #fdf7f0, #f7ede2);
        }

        .action-panel.danger {
            border-color: rgba(155, 59, 52, 0.18);
            background: linear-gradient(180deg, #fdf6f5, #faecea);
        }

        .required::after {
            content: ' *';
            color: #b54f3b;
        }

        @@media (max-width: 1100px) {
            .atelier-shell {
                grid-template-columns: 264px 1fr;
            }
        }

        @@media (max-width: 991.98px) {
            .atelier-shell {
                grid-template-columns: 1fr;
            }

            .rail {
                position: relative;
                height: auto;
                border-right: 0;
                border-bottom: 1px solid rgba(79, 67, 49, 0.12);
            }

            .workspace {
                padding: 14px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="atelier-shell">
    <aside class="rail">
        <div class="rail-brand">
            <h1 class="rail-brand-title">Quản Lý Thư Viện</h1>
        </div>

        <div class="rail-section">Điều hướng chính</div>
        <nav class="nav flex-column">
            <a href="{{ route('tong_quan') }}" class="nav-link {{ request()->routeIs('tong_quan') ? 'active' : '' }}"><i class="bi bi-stars"></i>Tổng quan</a>
            <a href="{{ route('the_loai.index') }}" class="nav-link {{ request()->routeIs('the_loai.*') ? 'active' : '' }}"><i class="bi bi-grid-3x3-gap"></i>Thể loại</a>
            <a href="{{ route('tac_gia.index') }}" class="nav-link {{ request()->routeIs('tac_gia.*') ? 'active' : '' }}"><i class="bi bi-vector-pen"></i>Tác giả</a>
            <a href="{{ route('nha_xuat_ban.index') }}" class="nav-link {{ request()->routeIs('nha_xuat_ban.*') ? 'active' : '' }}"><i class="bi bi-building"></i>Nhà xuất bản</a>
            <a href="{{ route('sach.index') }}" class="nav-link {{ request()->routeIs('sach.*') ? 'active' : '' }}"><i class="bi bi-book-half"></i>Sách</a>
            <a href="{{ route('doc_gia.index') }}" class="nav-link {{ request()->routeIs('doc_gia.*') ? 'active' : '' }}"><i class="bi bi-people"></i>Độc giả</a>
            <a href="{{ route('muon_tra.index') }}" class="nav-link {{ request()->routeIs('muon_tra.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i>Mượn trả</a>
            <a href="{{ route('bao_cao.tong_hop') }}" class="nav-link {{ request()->routeIs('bao_cao.*') ? 'active' : '' }}"><i class="bi bi-graph-up-arrow"></i>Báo cáo</a>
            @if(auth()->user()?->role === 'admin')
                <a href="{{ route('nhan_vien.index') }}" class="nav-link {{ request()->routeIs('nhan_vien.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i>Nhân viên</a>
            @endif
        </nav>

        <div class="rail-foot">
            <div><strong>{{ auth()->user()?->name }}</strong></div>
            <div class="small">Vai trò: {{ auth()->user()?->role === 'admin' ? 'Admin' : 'Thủ thư' }}</div>
        </div>
    </aside>

    <main class="workspace panel-stage">
        <header class="headline">
            <div>
                <h2 class="headline-title">@yield('page-title', 'Quản lý thư viện')</h2>
                @hasSection('page-subtitle')
                    <p class="headline-sub">@yield('page-subtitle')</p>
                @endif
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="chip"><i class="bi bi-calendar3"></i> {{ now()->format('d/m/Y') }}</span>
                <form action="{{ route('dang_xuat') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Đăng xuất</button>
                </form>
            </div>
        </header>

        @include('thanh_phan.thong_bao')
        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
