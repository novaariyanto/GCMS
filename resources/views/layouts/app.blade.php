<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'GCMS'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/modernize/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/modernize/css/styles.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/modernize/css/icons/tabler-icons/tabler-icons.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --gcms-ink: #102a43;
            --gcms-teal: #0f766e;
            --gcms-teal-deep: #115e59;
            --gcms-sand: #f3f1ea;
            --gcms-mist: #e8f1f5;
            --gcms-accent: #c56a1a;
        }
        body {
            margin: 0;
            color: var(--gcms-ink);
            font-family: "Source Sans 3", "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 12% 18%, rgba(15, 118, 110, .14), transparent 34%),
                radial-gradient(circle at 88% 0%, rgba(197, 106, 26, .12), transparent 28%),
                linear-gradient(180deg, #f7fafc 0%, #eef4f7 100%);
        }
        .display-font { font-family: Fraunces, Georgia, serif; }
        .navbar-gcms {
            background: rgba(16, 42, 67, .92);
            backdrop-filter: blur(12px);
        }
        .brand-mark {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: inline-grid;
            place-items: center;
            color: #fff;
            font-weight: 700;
            background: linear-gradient(145deg, var(--gcms-teal), #2dd4bf);
        }
        .btn-gcms {
            --bs-btn-bg: var(--gcms-teal);
            --bs-btn-border-color: var(--gcms-teal);
            --bs-btn-hover-bg: var(--gcms-teal-deep);
            --bs-btn-hover-border-color: var(--gcms-teal-deep);
            --bs-btn-color: #fff;
        }
        .btn-gcms-outline {
            --bs-btn-color: #fff;
            --bs-btn-border-color: rgba(255,255,255,.7);
            --bs-btn-hover-bg: rgba(255,255,255,.12);
            --bs-btn-hover-border-color: #fff;
            --bs-btn-hover-color: #fff;
        }
        .page-shell { padding-top: 5.5rem; padding-bottom: 3rem; }
        .text-gcms-teal { color: var(--gcms-teal); }
        .timeline-dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: var(--gcms-teal);
            margin-top: .4rem;
            flex: 0 0 auto;
        }
        .hero-gcms {
            min-height: 100vh;
            display: flex;
            align-items: flex-end;
            position: relative;
            overflow: hidden;
            color: #fff;
            background:
                linear-gradient(115deg, rgba(16, 42, 67, .9) 12%, rgba(15, 118, 110, .62) 68%, rgba(197, 106, 26, .35) 100%),
                url('{{ asset('vendor/modernize/images/backgrounds/rocket.png') }}') right 8% center / min(48vw, 560px) no-repeat,
                radial-gradient(circle at 80% 20%, rgba(45, 212, 191, .35), transparent 30%),
                #102a43;
        }
        .hero-gcms::after {
            content: "";
            position: absolute;
            inset: auto 0 0 0;
            height: 28%;
            background: linear-gradient(180deg, transparent, rgba(16, 42, 67, .35));
            pointer-events: none;
        }
        .hero-content {
            position: relative;
            z-index: 1;
            padding: 7rem 0 4.5rem;
            max-width: 40rem;
            animation: heroRise .9s ease both;
        }
        .hero-actions { animation: heroRise 1.1s ease both; }
        .hero-brand {
            font-size: clamp(2.8rem, 7vw, 5rem);
            line-height: .95;
            letter-spacing: -.03em;
            margin: 0 0 1rem;
            animation: heroRise .7s ease both;
        }
        @keyframes heroRise {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 767.98px) {
            .hero-content { padding-top: 6.5rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
@unless(request()->routeIs('landing'))
<nav class="navbar navbar-expand-lg navbar-dark navbar-gcms fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold" href="{{ route('landing') }}">
            <span class="brand-mark">G</span>
            <span>{{ config('app.name', 'GCMS') }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="{{ route('landing') }}">Beranda</a></li>
                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('portal.dashboard') }}">Portal</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('portal.complaints.index') }}">Pengaduan</a></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-light rounded-3">Keluar</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Masuk</a></li>
                    <li class="nav-item"><a class="btn btn-gcms btn-sm rounded-3 px-3" href="{{ route('register') }}">Daftar</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
@endunless

<main class="@yield('main_class', 'page-shell')">
    @if (session('success'))
        <div class="container"><div class="alert alert-success">{{ session('success') }}</div></div>
    @endif
    @if (session('error'))
        <div class="container"><div class="alert alert-danger">{{ session('error') }}</div></div>
    @endif
    @yield('content')
</main>

<script src="{{ asset('vendor/modernize/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
