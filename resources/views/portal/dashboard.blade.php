@extends('layouts.app')

@section('title', 'Dashboard Portal - GCMS Pemda')

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <p class="text-uppercase small text-gcms-teal fw-bold mb-1">Dashboard Masyarakat</p>
            <h1 class="h3 fw-bold mb-0">Selamat datang, {{ auth()->user()->name }}</h1>
        </div>
        <a href="{{ route('portal.complaints.create') }}" class="btn btn-gcms mt-3 mt-md-0">Buat Pengaduan Baru</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-gcms"><div class="card-body"><span class="text-muted">Total Pengaduan</span><h2 class="fw-bold mb-0">{{ $totalComplaints }}</h2></div></div>
        </div>
        <div class="col-md-4">
            <div class="card card-gcms"><div class="card-body"><span class="text-muted">Sedang Diproses</span><h2 class="fw-bold mb-0">{{ $openComplaints }}</h2></div></div>
        </div>
        <div class="col-md-4">
            <div class="card card-gcms"><div class="card-body"><span class="text-muted">Selesai</span><h2 class="fw-bold mb-0">{{ $resolvedComplaints }}</h2></div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card card-gcms h-100">
                <div class="card-body">
                    <h2 class="h5 fw-bold mb-3">Komposisi Pengaduan</h2>
                    <canvas id="complaintChart" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card card-gcms h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-bold mb-0">Pengaduan Terbaru</h2>
                        <a href="{{ route('portal.complaints.index') }}" class="small text-gcms-teal fw-semibold">Lihat semua</a>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse ($recentComplaints as $complaint)
                            <a href="{{ route('portal.complaints.show', $complaint) }}" class="list-group-item list-group-item-action px-0">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <strong>{{ $complaint->title }}</strong>
                                        <div class="small text-muted">{{ $complaint->ticket_number }} · {{ $complaint->category?->name }}</div>
                                    </div>
                                    <span class="badge rounded-pill" style="background: {{ $complaint->status?->color ?? '#0f766e' }}">{{ $complaint->status?->name }}</span>
                                </div>
                            </a>
                        @empty
                            <p class="text-muted mb-0">Belum ada pengaduan. Mulai buat laporan pertama Anda.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('complaintChart'), {
        type: 'doughnut',
        data: {
            labels: ['Diproses', 'Selesai'],
            datasets: [{
                data: [{{ $openComplaints }}, {{ $resolvedComplaints }}],
                backgroundColor: ['#0f766e', '#0b1f3a'],
                borderWidth: 0
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endpush
