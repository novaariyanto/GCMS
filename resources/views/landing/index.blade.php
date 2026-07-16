@extends('layouts.app')

@section('title', 'GCMS Pemda - Layanan Pengaduan Terpadu')
@section('main_class', '')

@section('content')
<section class="min-vh-100 d-flex align-items-center" style="background: linear-gradient(120deg, rgba(11,31,58,.94), rgba(15,118,110,.86)), url('https://images.unsplash.com/photo-1541872705-1f73c6400ec9?auto=format&fit=crop&w=1800&q=80') center/cover;">
    <div class="container py-5">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-7 text-white">
                <span class="badge rounded-pill badge-soft px-3 py-2 mb-4">Layanan Pengaduan Pemerintah Daerah</span>
                <h1 class="display-3 fw-bold mb-4">Sampaikan aspirasi warga, pantau tindak lanjutnya dengan jelas.</h1>
                <p class="lead mb-5 text-white-50">GCMS Pemda membantu masyarakat melaporkan keluhan layanan publik, meneruskan ke OPD terkait, dan memantau progres penyelesaian lewat nomor tiket.</p>
                <a href="{{ auth()->check() ? route('portal.complaints.create') : route('register') }}" class="btn btn-gcms btn-lg px-5 py-3 rounded-pill fw-semibold">Buat Pengaduan</a>
            </div>
            <div class="col-lg-4 offset-lg-1 mt-5 mt-lg-0">
                <div class="card card-gcms bg-white bg-opacity-95">
                    <div class="card-body p-4">
                        <p class="text-uppercase small text-gcms-teal fw-bold mb-2">Ringkasan Layanan</p>
                        <h2 class="fw-bold text-dark mb-4">Transparan dan responsif untuk warga.</h2>
                        <div class="d-flex justify-content-between border-bottom py-3">
                            <span>Total pengaduan</span>
                            <strong>{{ number_format($totalComplaints) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-3">
                            <span>Sudah diselesaikan</span>
                            <strong>{{ number_format($resolvedComplaints) }}</strong>
                        </div>
                        <div class="alert alert-info mt-4 mb-0">Simpan nomor tiket Anda untuk pelacakan publik melalui alamat <strong>/tracking/{tiket}</strong>.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
