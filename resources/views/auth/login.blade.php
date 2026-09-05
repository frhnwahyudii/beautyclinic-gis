@extends('layouts.app')

@section('title', 'Masuk')
@section('robots', 'noindex, follow')

@push('styles')
<style>
    .auth-wrap {
        min-height: calc(100vh - var(--nav-height));
        display: flex;
        align-items: center;
    }

    .auth-visual {
        background: linear-gradient(160deg, var(--sage-900) 0%, var(--sage-800) 60%, var(--sage-700) 100%);
        border-radius: var(--radius-xl);
        min-height: 560px;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 3rem;
        box-shadow: var(--shadow-lg);
    }

    .auth-visual::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='56' height='56'%3E%3Cg fill='none' stroke='%23FFFFFF' stroke-opacity='0.06'%3E%3Cpath d='M28 4c6 8 6 16 0 24S22 52 28 60'/%3E%3Cpath d='M28 4c-6 8-6 16 0 24s6 20 0 28'/%3E%3Ccircle cx='28' cy='30' r='3'/%3E%3C/g%3E%3C/svg%3E");
    }

    .auth-visual .vis-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: 0.3;
    }

    .vis-blob-1 { width: 280px; height: 280px; background: var(--terracotta); top: -70px; right: -60px; }
    .vis-blob-2 { width: 240px; height: 240px; background: var(--gold); bottom: -80px; left: -50px; }

    .auth-visual .vis-content { position: relative; z-index: 2; }

    .auth-card {
        max-width: 440px;
        width: 100%;
        margin: 0 auto;
    }
</style>
@endpush
@section('content')
<div class="container auth-wrap py-5">
    <div class="row g-5 align-items-center w-100">
        <div class="col-lg-6 d-none d-lg-flex">
            <div class="auth-visual w-100">
                <div class="vis-blob vis-blob-1"></div>
                <div class="vis-blob vis-blob-2"></div>

                <div class="vis-content d-flex align-items-center gap-3">
                    <div class="footer-brand-mark" style="width:54px;height:54px;font-size:1.4rem;">
                        <i class="bi bi-flower1"></i>
                    </div>
                    <div>
                        <h4 class="text-white mb-0" style="font-family: var(--font-display);">SIG Klinik Kecantikan</h4>
                        <small class="text-white-50">Kota Jambi</small>
                    </div>
                </div>

                <div class="vis-content">
                    <h2 class="text-white mb-3" style="font-size: 1.9rem; line-height: 1.25;">
                        Kelola dan temukan klinik kecantikan dalam satu ekosistem yang rapi.
                    </h2>
                    <p class="text-white-50 mb-4">
                        Masuk ke panel admin untuk memverifikasi, mengelola, dan memperbarui data klinik kecantikan.
                    </p>

                    <div class="d-flex align-items-center gap-3" style="background: rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.14); border-radius: var(--radius); padding: 1rem;">
                        <div class="medallion medallion-terra" style="width:44px;height:44px;font-size:1.1rem;border-radius:14px;flex-shrink:0;">
                            <i class="bi bi-patch-check"></i>
                        </div>
                        <div>
                            <div class="text-white fw-bold" style="font-size: .9rem;">Verifikasi data klinik lebih cepat</div>
                            <small class="text-white-50">Setujui, perbarui, dan pantau status kapan saja.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="auth-card page-enter">
                <div class="mb-4 text-center text-lg-start">
                    <span class="section-eyebrow mb-2">Selamat Datang Kembali</span>
                    <h2 class="section-title mt-2">Masuk ke Akun Anda</h2>
                    <p class="section-sub mt-2" style="font-size:.95rem;">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="fw-bold text-terracotta text-decoration-none">Daftar di sini</a>
                    </p>
                </div>

                <div class="card p-4 p-md-5 shadow-lift border-0">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label">Alamat Email</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-envelope"></i>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-lock"></i>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" placeholder="••••••••" required>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember" style="font-size:.9rem;">Ingat Saya</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sage btn-lg w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
                        </button>
                    </form>
                </div>

                <p class="text-center text-muted mt-4 mb-0" style="font-size:.85rem;">
                    <i class="bi bi-shield-check me-1"></i> Akun Anda dilindungi dan aman.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

