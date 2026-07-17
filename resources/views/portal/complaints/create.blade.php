@extends('layouts.app')

@section('title', 'Buat Pengaduan - GCMS Pemda')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="mb-4">
                <p class="text-uppercase small text-gcms-teal fw-bold mb-1">Form Pengaduan</p>
                <h1 class="h3 fw-bold mb-0">Ceritakan masalah layanan publik yang Anda alami</h1>
            </div>
            <div class="card card-gcms">
                <div class="card-body p-4 p-lg-5">
                    <form method="POST" action="{{ route('portal.complaints.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kategori</label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">Pilih kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') === $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prioritas</label>
                                <select name="priority_id" class="form-select @error('priority_id') is-invalid @enderror">
                                    <option value="">Otomatis oleh sistem</option>
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->id }}" @selected(old('priority_id') === $priority->id)>{{ $priority->name }}</option>
                                    @endforeach
                                </select>
                                @error('priority_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kecamatan</label>
                                <select name="district_id" class="form-select @error('district_id') is-invalid @enderror">
                                    <option value="">Pilih jika relevan</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}" @selected(old('district_id') === $district->id)>{{ $district->name }}</option>
                                    @endforeach
                                </select>
                                @error('district_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nomor HP pelapor</label>
                                <input type="text" name="reporter_phone" value="{{ old('reporter_phone', auth()->user()->phone) }}" class="form-control @error('reporter_phone') is-invalid @enderror">
                                @error('reporter_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Judul pengaduan</label>
                                <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Uraian lengkap</label>
                                <textarea name="description" rows="6" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat/lokasi kejadian</label>
                                <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="is_anonymous" id="is_anonymous" @checked(old('is_anonymous'))>
                                    <label class="form-check-label" for="is_anonymous">Tampilkan sebagai anonim pada tampilan publik</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('portal.complaints.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button class="btn btn-gcms" type="submit">Kirim Pengaduan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
