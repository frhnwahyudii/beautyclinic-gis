@extends('layouts.app')

@section('title', 'Daftar')

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
        max-width: 460px;
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
                        Bergabunglah dan nikmati akses penuh informasi kecantikan.
                    </h2>
                    <p class="text-white-50 mb-4">
                        Buat akun untuk mengelola klinik, memantau status verifikasi, dan memperbarui layanan.
                    </p>

                    <div class="d-flex align-items-center gap-3" style="background: rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.14); border-radius: var(--radius); padding: 1rem;">
                        <div class="medallion medallion-gold" style="width:44px;height:44px;font-size:1.1rem;border-radius:14px;flex-shrink:0;">
                            <i class="bi bi-stars"></i>
                        </div>
                        <div>
                            <div class="text-white fw-bold" style="font-size: .9rem;">Gratis untuk semua pengguna</div>
                            <small class="text-white-50">Registrasi cepat, tanpa biaya tersembunyi.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                <div class="col-lg-6">
            <div class="auth-card page-enter">
                <div class="mb-4 text-center text-lg-start">
                    <span class="section-eyebrow mb-2">Mulai Sekarang</span>
                    <h2 class="section-title mt-2">Buat Akun Baru</h2>
                    <p class="section-sub mt-2" style="font-size:.95rem;">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="fw-bold text-terracotta text-decoration-none">Masuk di sini</a>
                    </p>
                </div>

                <div class="card p-4 p-md-5 shadow-lift border-0">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Anti-bot honeypot: manusia tidak akan mengisi field ini -->
                        <div style="position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;" aria-hidden="true">
                            <label for="company_website">Jangan isi field ini</label>
                            <input type="text" name="company_website" id="company_website" tabindex="-1" autocomplete="off">
                        </div>
                        <input type="hidden" name="form_started_at" id="form_started_at" value="">

                        <div class="mb-4">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-person"></i>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" placeholder="Nama Anda" required autofocus>
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">Alamat Email</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-envelope"></i>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-lock"></i>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" placeholder="Min. 8 karakter" required>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-lock-fill"></i>
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sage btn-lg w-100">
                            <i class="bi bi-person-plus me-2"></i> Daftar Sekarang
                        </button>
                    </form>
                </div>

                <p class="text-center text-muted mt-4 mb-0" style="font-size:.85rem;">
                    <i class="bi bi-shield-check me-1"></i> Data Anda aman dan tidak akan dibagikan.
                </p>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Catat waktu render form untuk anti-bot time-trap
    document.getElementById('form_started_at').value = Math.floor(Date.now() / 1000);
</script>
@endpush

