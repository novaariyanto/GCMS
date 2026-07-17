@extends('layouts.app')

@section('title', 'Pelacakan '.$complaint->ticket_number.' - GCMS Pemda')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-gcms mb-4">
                <div class="card-body p-4 p-lg-5">
                    <p class="text-uppercase small text-gcms-teal fw-bold mb-1">Pelacakan Publik</p>
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                        <div>
                            <h1 class="h3 fw-bold mb-2">{{ $complaint->title }}</h1>
                            <div class="text-muted">Nomor tiket: <strong>{{ $complaint->ticket_number }}</strong></div>
                        </div>
                        <span class="badge rounded-pill align-self-start" style="background: {{ $complaint->status?->color ?? '#0f766e' }}">{{ $complaint->status?->name }}</span>
                    </div>
                    <hr class="my-4">
                    <div class="row g-3">
                        <div class="col-md-4"><span class="text-muted d-block">Kategori</span><strong>{{ $complaint->category?->name ?? '-' }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block">Prioritas</span><strong>{{ $complaint->priority?->name ?? '-' }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block">OPD Saat Ini</span><strong>{{ $complaint->currentOpd?->short_name ?? $complaint->currentOpd?->name ?? '-' }}</strong></div>
                    </div>
                </div>
            </div>

            <div class="card card-gcms">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h5 fw-bold mb-4">Linimasa Pengaduan</h2>
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
                        <p class="text-muted mb-0">Belum ada linimasa untuk tiket ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
