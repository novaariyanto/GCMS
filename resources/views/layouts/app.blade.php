<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'GCMS Pemda')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --gcms-navy: #0b1f3a;
            --gcms-teal: #0f766e;
            --gcms-teal-soft: #ccfbf1;
            --gcms-sky: #e0f2fe;
        }
        body {
            background: #f5f8fb;
            color: #172033;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .navbar-gcms {
            background: rgba(11, 31, 58, .96);
            backdrop-filter: blur(10px);
        }
        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--gcms-teal), #22d3ee);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
        }
        .btn-gcms {
            --bs-btn-bg: var(--gcms-teal);
            --bs-btn-border-color: var(--gcms-teal);
            --bs-btn-hover-bg: #115e59;
            --bs-btn-hover-border-color: #115e59;
            --bs-btn-color: #fff;
        }
        .text-gcms-teal { color: var(--gcms-teal); }
        .card-gcms {
            border: 0;
            border-radius: 1.25rem;
            box-shadow: 0 18px 45px rgba(11, 31, 58, .08);
        }
        .page-shell {
            padding-top: 5.5rem;
            padding-bottom: 3rem;
        }
        .badge-soft {
            background: var(--gcms-teal-soft);
            color: #115e59;
        }
        .timeline-dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: var(--gcms-teal);
            margin-top: .4rem;
            flex: 0 0 auto;
        }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-gcms fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('landing') }}">
            <span class="brand-mark">G</span>
            <span>GCMS Pemda</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('portal.complaints.index') }}">Pengaduan Saya</a></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-light" type="submit">Keluar</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Masuk</a></li>
                    <li class="nav-item"><a class="btn btn-sm btn-gcms" href="{{ route('register') }}">Daftar</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="@yield('main_class', 'page-shell')">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            confirmButtonColor: '#0f766e'
        });
    </script>
@endif
@if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Periksa kembali',
            text: 'Ada data yang belum sesuai. Silakan cek formulir.',
            confirmButtonColor: '#0f766e'
        });
    </script>
@endif
@stack('scripts')
</body>
</html>
