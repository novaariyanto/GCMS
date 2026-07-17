@extends('layouts.app')

@section('title', 'Pengaduan Saya - GCMS Pemda')

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <p class="text-uppercase small text-gcms-teal fw-bold mb-1">Portal Masyarakat</p>
            <h1 class="h3 fw-bold mb-0">Pengaduan Saya</h1>
        </div>
        <a href="{{ route('portal.complaints.create') }}" class="btn btn-gcms mt-3 mt-md-0">Buat Pengaduan</a>
    </div>

    <div class="card card-gcms">
        <div class="card-body p-4">
            <form class="row g-2 mb-4" method="GET">
                <div class="col-md-10">
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nomor tiket atau judul pengaduan">
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Tiket</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($complaints as $complaint)
                        <tr>
                            <td class="fw-semibold">{{ $complaint->ticket_number }}</td>
                            <td>{{ $complaint->title }}</td>
                            <td>{{ $complaint->category?->name ?? '-' }}</td>
                            <td><span class="badge rounded-pill" style="background: {{ $complaint->status?->color ?? '#0f766e' }}">{{ $complaint->status?->name }}</span></td>
                            <td>{{ $complaint->created_at?->format('d M Y') }}</td>
                            <td class="text-end"><a href="{{ route('portal.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-secondary">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">Belum ada pengaduan.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $complaints->links() }}
        </div>
    </div>
</div>
@endsection
