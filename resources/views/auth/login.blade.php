@extends('layouts.app')

@section('title', 'Masuk - GCMS Pemda')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card card-gcms">
                <div class="card-body p-4 p-lg-5">
                    <p class="text-uppercase small text-gcms-teal fw-bold mb-2">Portal Masyarakat</p>
                    <h1 class="h3 fw-bold mb-4">Masuk ke akun Anda</h1>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kata sandi</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                        <button class="btn btn-gcms w-100 py-2" type="submit">Masuk</button>
                    </form>
                    <p class="text-center text-muted mt-4 mb-0">Belum punya akun? <a href="{{ route('register') }}" class="text-gcms-teal fw-semibold">Daftar sekarang</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
