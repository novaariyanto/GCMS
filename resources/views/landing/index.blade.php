@extends('layouts.app')

@section('title', config('app.name', 'GCMS').' — Pengaduan Masyarakat')
@section('main_class', '')

@section('content')
<section class="hero-gcms">
    <div class="container">
        <div class="hero-content">
            <p class="hero-brand display-font">{{ $settings->app_name ?? config('app.name', 'GCMS') }}</p>
            <h1 class="display-font fs-2 fw-semibold mb-3">Pengaduan warga yang terstruktur, transparan, dan terukur.</h1>
            <p class="fs-5 mb-4 text-white-50">
                {{ $settings->government_name ?? 'Pemerintah Daerah' }} menerima, meneruskan, dan menyelesaikan laporan layanan publik melalui satu kanal resmi.
            </p>
            <div class="hero-actions d-flex flex-wrap gap-3">
                <a href="{{ auth()->check() ? route('portal.complaints.create') : route('register') }}" class="btn btn-gcms btn-lg px-4 py-3 rounded-3 fw-semibold">
                    Buat Pengaduan
                </a>
                <a href="{{ route('login') }}" class="btn btn-gcms-outline btn-lg px-4 py-3 rounded-3">
                    Masuk Portal
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
