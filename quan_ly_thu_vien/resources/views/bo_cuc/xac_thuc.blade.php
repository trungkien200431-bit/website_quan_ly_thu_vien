<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản Lý Thư Viện')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --ink: #173039;
            --ink-soft: #657980;
            --ink-muted: #8b979b;
            --deep: #0f2a33;
            --deep-2: #133844;
            --gold: #c58b59;
            --gold-soft: rgba(197, 139, 89, 0.18);
            --paper: #f7f2e8;
            --paper-2: #fffdf8;
            --line: rgba(95, 88, 73, 0.18);
            --shadow-xl: 0 32px 90px rgba(18, 24, 28, 0.24);
            --shadow-md: 0 16px 32px rgba(33, 39, 44, 0.12);
            --radius-xl: 32px;
            --radius-lg: 22px;
            --radius-md: 16px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 24px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(42rem 42rem at -10% -8%, rgba(214, 191, 160, 0.48) 0%, transparent 52%),
                radial-gradient(32rem 32rem at 108% 8%, rgba(156, 190, 185, 0.42) 0%, transparent 50%),
                linear-gradient(145deg, #efe5d6 0%, #faf6ef 46%, #edf5f2 100%);
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.22) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.18) 1px, transparent 1px);
            background-size: 26px 26px;
            mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), transparent 72%);
        }

        .auth-shell {
            width: min(1180px, 100%);
            min-height: calc(100vh - 48px);
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.08fr 0.92fr;
            border-radius: var(--radius-xl);
            overflow: hidden;
            border: 1px solid rgba(53, 48, 41, 0.12);
            background: rgba(255, 255, 255, 0.76);
            backdrop-filter: blur(16px);
            box-shadow: var(--shadow-xl);
        }

        .hero-panel {
            position: relative;
            overflow: hidden;
            padding: 40px 38px 34px;
            color: #f7f2e7;
            background:
                radial-gradient(circle at 18% 18%, rgba(237, 192, 134, 0.2), transparent 18rem),
                radial-gradient(circle at 80% 20%, rgba(107, 152, 158, 0.24), transparent 20rem),
                linear-gradient(155deg, rgba(14, 39, 49, 0.98), rgba(22, 58, 71, 0.96) 56%, rgba(29, 68, 73, 0.94));
        }

        .hero-panel::before,
        .hero-panel::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
            filter: blur(2px);
        }

        .hero-panel::before {
            width: 26rem;
            height: 26rem;
            right: -9rem;
            top: -6rem;
            border: 1px solid rgba(255, 241, 218, 0.16);
            background: radial-gradient(circle, rgba(255, 228, 188, 0.14), transparent 62%);
        }

        .hero-panel::after {
            width: 18rem;
            height: 18rem;
            left: -6rem;
            bottom: -5rem;
            border: 1px solid rgba(140, 196, 187, 0.14);
            background: radial-gradient(circle, rgba(140, 196, 187, 0.12), transparent 60%);
        }

        .hero-panel > * {
            position: relative;
            z-index: 1;
        }

        .hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(255, 241, 221, 0.86);
        }

        .hero-kicker::before {
            content: '';
            width: 11px;
            height: 11px;
            border-radius: 999px;
            background: #f0bf88;
            box-shadow: 0 0 0 6px rgba(240, 191, 136, 0.14);
        }

        .hero-title {
            margin: 22px 0 12px;
            max-width: 14ch;
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(3rem, 5vw, 4.35rem);
            line-height: 0.93;
            letter-spacing: 0.01em;
        }

        .hero-text {
            max-width: 34rem;
            margin: 0;
            color: rgba(245, 237, 222, 0.84);
            line-height: 1.78;
            font-size: 0.97rem;
        }

        .hero-grid {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .hero-card {
            border: 1px solid rgba(255, 240, 215, 0.14);
            border-radius: 18px;
            padding: 16px 16px 15px;
            background:
                linear-gradient(160deg, rgba(255, 255, 255, 0.09), rgba(255, 255, 255, 0.04));
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .hero-card-label {
            color: rgba(240, 229, 210, 0.68);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .hero-card-value {
            margin-top: 8px;
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .hero-card-note {
            margin-top: 4px;
            color: rgba(241, 230, 211, 0.72);
            font-size: 0.82rem;
        }

        .hero-strip {
            margin-top: 22px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(248, 230, 199, 0.18);
            background: rgba(255, 244, 223, 0.08);
            color: rgba(248, 239, 223, 0.86);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .hero-chip i {
            color: #f0c490;
        }

        .content-panel {
            padding: 38px 36px 32px;
            background:
                radial-gradient(circle at 100% 0%, rgba(197, 139, 89, 0.08), transparent 18rem),
                linear-gradient(180deg, rgba(255, 252, 245, 0.96), rgba(248, 243, 235, 0.94));
        }

        .panel-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            background: var(--gold-soft);
            color: #855a37;
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .panel-eyebrow i {
            font-size: 0.9rem;
        }

        .title {
            margin: 18px 0 8px;
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.15rem, 3vw, 2.8rem);
            line-height: 0.98;
            color: #1d3941;
        }

        .sub {
            margin: 0 0 24px;
            color: var(--ink-soft);
            line-height: 1.75;
        }

        .alert {
            border-radius: 16px;
            border-width: 1px;
            padding: 14px 16px;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 700;
            color: #445055;
            margin-bottom: 8px;
        }

        .field-shell {
            position: relative;
        }

        .field-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #8f7a64;
            font-size: 1rem;
            pointer-events: none;
        }

        .form-control,
        .form-select {
            min-height: 52px;
            border-radius: 16px;
            border-color: rgba(118, 107, 89, 0.2);
            background: rgba(255, 255, 255, 0.88);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.58);
        }

        .field-shell .form-control {
            padding-left: 46px;
        }

        .password-toggle {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #80644f;
            width: 40px;
            height: 40px;
            border-radius: 12px;
        }

        .password-toggle:hover {
            background: rgba(197, 139, 89, 0.1);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: rgba(197, 139, 89, 0.68);
            box-shadow: 0 0 0 0.24rem rgba(197, 139, 89, 0.14);
        }

        .form-check-input {
            border-color: rgba(118, 107, 89, 0.28);
        }

        .form-check-input:checked {
            background-color: #244955;
            border-color: #244955;
        }

        .btn-auth {
            min-height: 52px;
            border: 1px solid #173843;
            border-radius: 16px;
            background: linear-gradient(135deg, #204653 0%, #14343e 100%);
            color: #fff;
            font-weight: 800;
            letter-spacing: 0.02em;
            box-shadow: 0 14px 24px rgba(25, 57, 66, 0.2);
        }

        .btn-auth:hover {
            color: #fff;
            background: linear-gradient(135deg, #183b45 0%, #102e36 100%);
        }

        .btn-soft {
            min-height: 48px;
            border: 1px solid rgba(118, 107, 89, 0.18);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.72);
            color: #36505a;
            font-weight: 700;
        }

        .btn-soft:hover {
            color: #243b43;
            background: rgba(255, 255, 255, 0.9);
        }

        .link-auth {
            color: #8a5838;
            font-weight: 700;
            text-decoration: none;
        }

        .link-auth:hover {
            color: #6f4429;
            text-decoration: underline;
        }

        .soft-card {
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.62);
            box-shadow: var(--shadow-md);
        }

        .soft-card-title {
            margin: 0 0 8px;
            font-size: 0.95rem;
            font-weight: 800;
            color: #29434c;
        }

        .soft-card-text {
            margin: 0;
            color: var(--ink-soft);
            line-height: 1.7;
            font-size: 0.9rem;
        }

        .step-list,
        .helper-list {
            display: grid;
            gap: 12px;
        }

        .step-item,
        .helper-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .step-number,
        .helper-icon {
            width: 32px;
            height: 32px;
            flex: 0 0 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(197, 139, 89, 0.13);
            color: #8a5d3b;
            font-weight: 800;
        }

        .step-copy,
        .helper-copy {
            color: #53656b;
            line-height: 1.6;
            font-size: 0.9rem;
        }

        .mini-note {
            margin-top: 18px;
            border-radius: 18px;
            border: 1px solid rgba(118, 107, 89, 0.18);
            background: rgba(255, 255, 255, 0.58);
            padding: 14px 16px;
            color: #5d625f;
            font-size: 0.88rem;
            line-height: 1.7;
        }

        .inline-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        code {
            font-family: Consolas, Monaco, monospace;
            color: #214651;
            background: rgba(33, 70, 81, 0.08);
            padding: 2px 6px;
            border-radius: 8px;
        }

        @@media (max-width: 1080px) {
            body {
                padding: 16px;
            }

            .auth-shell {
                min-height: auto;
                grid-template-columns: 1fr;
            }

            .hero-panel,
            .content-panel {
                padding: 28px 22px;
            }

            .hero-title {
                max-width: 100%;
                font-size: clamp(2.6rem, 9vw, 3.5rem);
            }
        }

        @@media (max-width: 640px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }

            .inline-links {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
@php
    $metrics = $authMetrics ?? [];
@endphp

<div class="auth-shell">
    <section class="hero-panel">
        <div class="hero-kicker">@yield('hero-kicker', 'Quản Lý Thư Viện')</div>
        @hasSection('hero-title')
            <h1 class="hero-title">@yield('hero-title')</h1>
        @endif
        @hasSection('hero-text')
            <p class="hero-text">@yield('hero-text')</p>
        @endif

        <div class="hero-grid">
            @hasSection('hero-grid')
                @yield('hero-grid')
            @else
                <article class="hero-card">
                    <div class="hero-card-label">Đầu sách</div>
                    <div class="hero-card-value">{{ number_format($metrics['catalog_titles'] ?? 0) }}</div>
                    <div class="hero-card-note">Danh mục đang được quản lý</div>
                </article>
                <article class="hero-card">
                    <div class="hero-card-label">Bản sẵn có</div>
                    <div class="hero-card-value">{{ number_format($metrics['available_copies'] ?? 0) }}</div>
                    <div class="hero-card-note">Sẵn sàng phục vụ độc giả</div>
                </article>
                <article class="hero-card">
                    <div class="hero-card-label">Độc giả hoạt động</div>
                    <div class="hero-card-value">{{ number_format($metrics['active_readers'] ?? 0) }}</div>
                    <div class="hero-card-note">Tài khoản đang còn hiệu lực</div>
                </article>
                <article class="hero-card">
                    <div class="hero-card-label">Phiếu đang mở</div>
                    <div class="hero-card-value">{{ number_format($metrics['open_borrowings'] ?? 0) }}</div>
                    <div class="hero-card-note">Giao dịch cần tiếp tục theo dõi</div>
                </article>
            @endif
        </div>

        @hasSection('hero-strip')
            <div class="hero-strip">
                @yield('hero-strip')
            </div>
        @endif
    </section>

    <section class="content-panel">
        @yield('content')
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            var target = document.querySelector(this.getAttribute('data-password-toggle'));

            if (! target) {
                return;
            }

            var nextType = target.type === 'password' ? 'text' : 'password';
            var icon = this.querySelector('i');

            target.type = nextType;
            this.setAttribute('aria-label', nextType === 'password' ? 'Hiện mật khẩu' : 'Ẩn mật khẩu');

            if (icon) {
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            }
        });
    });
</script>
@stack('scripts')
</body>
</html>
