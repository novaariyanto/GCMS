@extends('layouts.app')

@section('title', $complaint->ticket_number.' - GCMS Pemda')

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <p class="text-uppercase small text-gcms-teal fw-bold mb-1">Detail Pengaduan</p>
            <h1 class="h3 fw-bold mb-1">{{ $complaint->title }}</h1>
            <div class="text-muted">Nomor tiket: <strong>{{ $complaint->ticket_number }}</strong></div>
        </div>
        <a href="{{ route('portal.complaints.index') }}" class="btn btn-outline-secondary mt-3 mt-md-0">Kembali</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-gcms mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge rounded-pill" style="background: {{ $complaint->status?->color ?? '#0f766e' }}">{{ $complaint->status?->name }}</span>
                        <span class="badge rounded-pill text-bg-light">{{ $complaint->category?->name }}</span>
                        <span class="badge rounded-pill text-bg-light">{{ $complaint->priority?->name }}</span>
                    </div>
                    <h2 class="h5 fw-bold">Uraian Pengaduan</h2>
                    <p class="mb-4" style="white-space: pre-line">{{ $complaint->description }}</p>
                    <h2 class="h5 fw-bold">Lokasi</h2>
                    <p class="mb-0 text-muted">{{ $complaint->address ?: 'Tidak diisi' }}</p>
                </div>
            </div>

            <div class="card card-gcms">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Riwayat Tindak Lanjut</h2>
                    @forelse ($complaint->timelines->sortByDesc('occurred_at') as $timeline)
                        <div class="d-flex gap-3 pb-3 mb-3 border-bottom">
                            <span class="timeline-dot"></span>
                            <div>
                                <div class="fw-semibold">{{ $timeline->title }}</div>
                                <div class="small text-muted">{{ $timeline->occurred_at?->format('d M Y H:i') }} · {{ $timeline->user?->name ?? 'Sistem' }}</div>
                                @if ($timeline->description)
                                    <p class="mb-0 mt-2">{{ $timeline->description }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada riwayat.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-gcms">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Informasi Proses</h2>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span>OPD Saat Ini</span><strong class="text-end">{{ $complaint->currentOpd?->short_name ?? $complaint->currentOpd?->name ?? '-' }}</strong></div>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span>Dibuat</span><strong>{{ $complaint->created_at?->format('d M Y') }}</strong></div>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span>Batas SLA</span><strong>{{ $complaint->sla_due_at?->format('d M Y H:i') ?? '-' }}</strong></div>
                    <div class="d-flex justify-content-between py-2"><span>Pelapor</span><strong>{{ $complaint->is_anonymous ? 'Anonim' : $complaint->reporter_name }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
