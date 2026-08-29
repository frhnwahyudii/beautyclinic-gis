@php
    // Live stats with safe fallback
    try {
        $landingTotal = \App\Models\Klinik::count();
        $landingApproved = \App\Models\Klinik::where('status', 'approved')->count();
    } catch (\Throwable $e) {
        $landingTotal = 0;
        $landingApproved = 0;
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIG Klinik Kecantikan Kota Jambi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/beauty.css') }}" rel="stylesheet">

    <style>
        .landing-nav {
            background: rgba(250, 246, 237, 0.8);
        }

        .hero-stage {
            min-height: calc(100vh - var(--nav-height));
            display: flex;
            align-items: center;
        }

        .hero-leaf-1 { top: 12%; left: 6%; width: 110px; animation-duration: 9s; }
        .hero-leaf-2 { top: 20%; right: 4%; width: 80px; animation-duration: 7s; animation-delay: 1s; }
        .hero-leaf-3 { bottom: 14%; left: 38%; width: 60px; animation-duration: 8s; animation-delay: 2s; }
        .hero-leaf-4 { top: 8%; right: 34%; width: 46px; animation-duration: 6s; }

        .blob-a { top: -90px; right: -80px; width: 340px; height: 340px; }
        .blob-b { bottom: -110px; left: -60px; width: 300px; height: 300px; }
        .blob-c { top: 38%; right: 32%; width: 180px; height: 180px; }

        .hero-arch-panel {
            background: linear-gradient(150deg, #E3EAD9 0%, #C8D6BC 55%, #B9CBA9 100%);
            border-radius: 999px 999px 32px 32px;
            min-height: 460px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .hero-arch-panel .arch-inner {
            position: absolute;
            inset: 22px 22px 0;
            border-radius: 999px 999px 24px 24px;
            background:
                radial-gradient(circle at 30% 20%, rgba(255,255,255,0.5), transparent 45%),
                linear-gradient(160deg, #F4EFE3 0%, #E8E1CF 100%);
            overflow: hidden;
        }

        .hero-arch-panel .dot-grid {
            position: absolute;
            inset: 0;
            opacity: 0.35;
            background-image: radial-gradient(circle, var(--sage-500) 1.4px, transparent 1.4px);
            background-size: 26px 26px;
        }

        .hero-arch-panel .route-path {
            position: absolute;
            left: 10%;
            top: 28%;
            width: 80%;
            height: 55%;
        }

        .hero-arch-panel .route-path svg { width: 100%; height: 100%; }

        .float-chip {
            position: absolute;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 0.8rem 1.1rem;
            z-index: 2;
            animation: floatY 6s ease-in-out infinite;
        }

        .float-chip .chip-value {
            font-family: var(--font-display);
            font-size: 1.35rem;
            font-weight: 600;
            color: var(--sage-900);
            line-height: 1;
        }

        .float-chip .chip-label {
            font-size: 0.72rem;
            color: var(--muted);
            font-weight: 600;
            letter-spacing: 0.03em;
        }

        .chip-1 { top: 16%; right: -14px; }
        .chip-2 { bottom: 30%; left: -10px; animation-delay: 1.4s; }
        .chip-3 { bottom: 12%; right: 12%; animation-delay: 2.6s; }
                .step-number {
            font-family: var(--font-display);
            font-size: 3.2rem;
            font-weight: 700;
            color: transparent;
            -webkit-text-stroke: 1.5px var(--sage-300);
            line-height: 1;
        }

        .cta-panel {
            background: linear-gradient(135deg, var(--sage-900) 0%, var(--sage-800) 100%);
            border-radius: var(--radius-xl);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .cta-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='56' height='56'%3E%3Cg fill='none' stroke='%23FFFFFF' stroke-opacity='0.06'%3E%3Cpath d='M28 4c6 8 6 16 0 24S22 52 28 60'/%3E%3Cpath d='M28 4c-6 8-6 16 0 24s6 20 0 28'/%3E%3Ccircle cx='28' cy='30' r='3'/%3E%3C/g%3E%3C/svg%3E");
        }

        .cta-panel .cta-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(50px);
            opacity: 0.25;
        }

        .cta-blob-1 { width: 260px; height: 260px; background: var(--terracotta); top: -80px; right: -60px; }
        .cta-blob-2 { width: 220px; height: 220px; background: var(--gold); bottom: -90px; left: -40px; }

        /* ── Mobile responsive (landing-specific) ── */
        @media (max-width: 575.98px) {
            .hero-stats { gap: 0.9rem !important; }
            .hero-stats > div > div { font-size: 1.15rem !important; }
            .hero-stats .vr { height: 28px !important; }
            .leaf-accent.hero-leaf-1 { width: 70px; }
            .leaf-accent.hero-leaf-2 { width: 55px; }
            .leaf-accent.hero-leaf-3 { width: 45px; }
            .leaf-accent.hero-leaf-4 { width: 36px; }
            .step-number { font-size: 2.4rem; }
        }

        @media (max-width: 991.98px) {
            .hero-stage { padding: 0.5rem 0 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="grain-overlay" aria-hidden="true"></div>
        <!-- Navbar -->
    <nav class="navbar navbar-expand-lg botan-nav landing-nav sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <span class="brand-mark"><i class="bi bi-flower1"></i></span>
                <span class="d-none d-sm-inline">SIG Klinik Kecantikan</span>
                <span class="d-inline d-sm-none">SIG Klinik</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNav" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="landingNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('klinik.map') }}"><i class="bi bi-map"></i> Peta Klinik</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('klinik.create') }}"><i class="bi bi-plus-circle"></i> Daftar Klinik</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
        <!-- Hero -->
    <header class="botan-hero">
        <div class="blob blob-sage blob-a"></div>
        <div class="blob blob-terra blob-b"></div>
        <div class="blob blob-gold blob-c"></div>

        <div class="leaf-accent hero-leaf-1">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M50 5C72 30 72 55 50 80C28 55 28 30 50 5Z" stroke="#54684A" stroke-width="2"/>
                <path d="M50 40C40 32 33 22 30 12M50 40C60 32 67 22 70 12" stroke="#54684A" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="leaf-accent hero-leaf-2">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M50 5C72 30 72 55 50 80C28 55 28 30 50 5Z" stroke="#C97B5A" stroke-width="2"/>
                <path d="M50 40C40 32 33 22 30 12M50 40C60 32 67 22 70 12" stroke="#C97B5A" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="leaf-accent hero-leaf-3">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M50 5C72 30 72 55 50 80C28 55 28 30 50 5Z" stroke="#54684A" stroke-width="2"/>
            </svg>
        </div>
        <div class="leaf-accent hero-leaf-4">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M50 5C72 30 72 55 50 80C28 55 28 30 50 5Z" stroke="#C9A96A" stroke-width="2"/>
            </svg>
        </div>

        <div class="container hero-stage">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-reveal>
                    <div class="hero-badge-pill mb-4">
                        <span class="dot"></span>
                        Sistem Informasi Geografis Kecantikan
                    </div>

                    <h1 class="display-3 fw-bold mb-4" style="font-size: clamp(2.4rem, 4.6vw, 4rem);">
                        Temukan Klinik Kecantikan
                        <span class="text-gradient-sage">Terbaik</span> di Kota Jambi
                    </h1>

                    <p class="lead text-ink-soft mb-5" style="max-width: 520px;">
                        Jelajahi peta interaktif, bandingkan layanan & harga, lalu temukan
                        klinik kecantikan yang paling sesuai dengan kebutuhanmu.
                    </p>

                    <div class="d-flex flex-column flex-sm-row gap-3 mb-5">
                        <a href="{{ route('klinik.map') }}" class="btn btn-terracotta btn-lg px-4">
                            <i class="bi bi-geo-alt me-2"></i> Jelajahi Peta
                        </a>
                        <a href="{{ route('klinik.create') }}" class="btn btn-outline-sage btn-lg px-4">
                            <i class="bi bi-plus-circle me-2"></i> Daftarkan Klinik
                        </a>
                    </div>

                    <div class="d-flex align-items-center hero-stats gap-4">
                        <div>
                            <div class="fw-bold" style="font-family: var(--font-display); font-size: 1.3rem; color: var(--sage-900);">
                                {{ $landingTotal ?: '30+' }}
                            </div>
                            <small class="text-muted">Total Klinik</small>
                        </div>
                        <div class="vr" style="height: 34px; opacity: .3;"></div>
                        <div>
                            <div class="fw-bold" style="font-family: var(--font-display); font-size: 1.3rem; color: var(--sage-900);">
                                {{ $landingApproved ?: '20+' }}
                            </div>
                            <small class="text-muted">Terverifikasi</small>
                        </div>
                        <div class="vr" style="height: 34px; opacity: .3;"></div>
                        <div>
                            <div class="fw-bold" style="font-family: var(--font-display); font-size: 1.3rem; color: var(--sage-900);">
                                30+
                            </div>
                            <small class="text-muted">Jenis Layanan</small>
                        </div>
                    </div>
                </div>
                                <div class="col-lg-6 d-none d-lg-block" data-reveal style="--reveal-delay: 0.15s;">
                    <div class="hero-arch-panel">
                        <div class="arch-inner">
                            <div class="dot-grid"></div>

                            <div class="route-path">
                                <svg viewBox="0 0 400 260" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M40 210 C120 80, 220 60, 300 120 S 380 40, 360 30" stroke="#8BA07E" stroke-width="3" stroke-dasharray="8 8" stroke-linecap="round"/>
                                    <circle cx="40" cy="210" r="8" fill="#C97B5A" stroke="#fff" stroke-width="3"/>
                                    <circle cx="300" cy="120" r="7" fill="#54684A" stroke="#fff" stroke-width="3"/>
                                    <circle cx="360" cy="30" r="7" fill="#C9A96A" stroke="#fff" stroke-width="3"/>
                                </svg>
                            </div>

                            <div class="float-chip chip-1">
                                <div class="chip-value">{{ $landingTotal ?: '30' }}+ <i class="bi bi-buildings text-terracotta" style="font-size:1rem;"></i></div>
                                <div class="chip-label">Klinik Terdata</div>
                            </div>
                            <div class="float-chip chip-2">
                                <div class="chip-value"><i class="bi bi-tags text-sage" style="font-size:1rem;"></i> 30+</div>
                                <div class="chip-label">Layanan Perawatan</div>
                            </div>
                            <div class="float-chip chip-3">
                                <div class="chip-value"><i class="bi bi-star-fill text-gold" style="font-size:1rem;"></i> 4.9</div>
                                <div class="chip-label">Kepuasan Pengguna</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </header>
        <!-- Marquee -->
    <div class="marquee-band" aria-hidden="true">
        <div class="marquee-track">
            <span>Facial</span><span>HydraFacial</span><span>Chemical Peeling</span>
            <span>Laser Rejuvenation</span><span>Botox</span><span>HIFU</span>
            <span>Microneedling</span><span>PRP Therapy</span><span>Body Contouring</span>
            <span>Facial</span><span>HydraFacial</span><span>Chemical Peeling</span>
            <span>Laser Rejuvenation</span><span>Botox</span><span>HIFU</span>
            <span>Microneedling</span><span>PRP Therapy</span><span>Body Contouring</span>
        </div>
    </div>

    <!-- Features -->
    <section class="py-5">
        <div class="container py-4">
            <div class="row mb-5">
                <div class="col-lg-7 mx-auto text-center" data-reveal>
                    <div class="section-eyebrow mb-3">Fitur Unggulan</div>
                    <h2 class="section-title mb-3">Semua yang Anda Butuhkan<br>dalam Satu Platform</h2>
                    <p class="section-sub mx-auto">
                        Platform terlengkap untuk menemukan dan membandingkan klinik kecantikan di Kota Jambi.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-reveal>
                    <div class="card card-arch card-lift h-100 text-center p-4">
                        <div class="medallion medallion-sage mx-auto mb-4">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="font-size:1.2rem;">Peta Interaktif</h4>
                        <p class="text-ink-soft mb-0">
                            Jelajahi lokasi klinik kecantikan dengan peta interaktif yang mudah digunakan dan akurat.
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-reveal style="--reveal-delay: 0.08s;">
                    <div class="card card-arch card-lift h-100 text-center p-4">
                        <div class="medallion medallion-terra mx-auto mb-4">
                            <i class="bi bi-tags"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="font-size:1.2rem;">Filter & Harga</h4>
                        <p class="text-ink-soft mb-0">
                            Saring klinik berdasarkan layanan, harga, dan lokasi sesuai kebutuhan Anda.
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-reveal style="--reveal-delay: 0.16s;">
                    <div class="card card-arch card-lift h-100 text-center p-4">
                        <div class="medallion medallion-gold mx-auto mb-4">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="font-size:1.2rem;">Informasi Lengkap</h4>
                        <p class="text-ink-soft mb-0">
                            Dapatkan detail profil, layanan, harga, dan kontak setiap klinik secara transparan.
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-reveal style="--reveal-delay: 0.24s;">
                    <div class="card card-arch card-lift h-100 text-center p-4">
                        <div class="medallion medallion-sage mx-auto mb-4">
                            <i class="bi bi-phone"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="font-size:1.2rem;">Responsif</h4>
                        <p class="text-ink-soft mb-0">
                            Akses mudah dari laptop hingga smartphone dengan desain yang ringan dan responsif.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
        <!-- How it works -->
    <section class="bg-cream-soft py-5">
        <div class="container py-4">
            <div class="row mb-5">
                <div class="col-lg-7 mx-auto text-center" data-reveal>
                    <div class="section-eyebrow mb-3">Cara Kerja</div>
                    <h2 class="section-title mb-3">Mulai dalam Tiga Langkah</h2>
                    <p class="section-sub mx-auto">
                        Dari mencari hingga memilih, semuanya cepat dan mudah.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4" data-reveal>
                    <div class="card h-100 p-4" style="border-radius: var(--radius-lg);">
                        <div class="step-number mb-3">01</div>
                        <div class="medallion medallion-sage mb-3" style="width:48px;height:48px;font-size:1.15rem;border-radius:16px;">
                            <i class="bi bi-map"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Jelajahi Peta</h5>
                        <p class="text-ink-soft mb-0">
                            Buka peta interaktif dan lihat sebaran klinik kecantikan di seluruh Kota Jambi.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4" data-reveal style="--reveal-delay: 0.1s;">
                    <div class="card h-100 p-4" style="border-radius: var(--radius-lg);">
                        <div class="step-number mb-3">02</div>
                        <div class="medallion medallion-terra mb-3" style="width:48px;height:48px;font-size:1.15rem;border-radius:16px;">
                            <i class="bi bi-funnel"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Filter Sesuai Kebutuhan</h5>
                        <p class="text-ink-soft mb-0">
                            Saring berdasarkan layanan favorit, rentang harga, hingga lokasi terdekat.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4" data-reveal style="--reveal-delay: 0.2s;">
                    <div class="card h-100 p-4" style="border-radius: var(--radius-lg);">
                        <div class="step-number mb-3">03</div>
                        <div class="medallion medallion-gold mb-3" style="width:48px;height:48px;font-size:1.15rem;border-radius:16px;">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Kunjungi & Rasakan</h5>
                        <p class="text-ink-soft mb-0">
                            Lihat detail klinik, bandingkan layanan & harga, lalu kunjungi klinik pilihan Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-5">
        <div class="container py-3">
            <div class="cta-panel p-5 text-center" data-reveal>
                <div class="cta-blob cta-blob-1"></div>
                <div class="cta-blob cta-blob-2"></div>
                <div class="position-relative">
                    <h2 class="text-white mb-3" style="font-size: clamp(1.8rem, 3vw, 2.6rem);">
                        Siap Memulai Perjalanan Kecantikan Anda?
                    </h2>
                    <p class="text-white-50 mx-auto mb-4" style="max-width: 560px;">
                        Temukan klinik kecantikan terbaik di Kota Jambi sekarang.
                        Atau daftarkan klinik Anda dan jangkau lebih banyak pelanggan.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="{{ route('klinik.map') }}" class="btn btn-terracotta btn-lg px-4">
                            <i class="bi bi-geo-alt me-2"></i> Mulai Jelajah
                        </a>
                        <a href="{{ route('klinik.create') }}" class="btn btn-ghost-light btn-lg px-4">
                            <i class="bi bi-plus-circle me-2"></i> Daftarkan Klinik
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
        <!-- Footer -->
    <footer class="botan-footer pt-5 pb-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="footer-brand-mark"><i class="bi bi-flower1"></i></div>
                        <div>
                            <h5 class="mb-0">SIG Klinik Kecantikan</h5>
                            <small class="text-white-50">Kota Jambi</small>
                        </div>
                    </div>
                    <p class="mb-3" style="max-width: 330px;">
                        Sistem Informasi Geografis klinik kecantikan di Kota Jambi.
                        Temukan klinik terdekat dengan informasi lengkap dan terpercaya.
                    </p>
                </div>

                <div class="col-lg-3 offset-lg-1">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-envelope me-2 text-gold"></i>hello@frhnwahyudi.xyz</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2 text-gold"></i>081247506528</li>
                        <li><i class="bi bi-geo-alt me-2 text-gold"></i>Kota Jambi, Jambi</li>
                    </ul>
                </div>

                <div class="col-lg-4">
                    <h5>Navigasi</h5>
                    <div class="d-flex flex-column">
                        <a href="{{ route('klinik.map') }}" class="mb-2"><i class="bi bi-chevron-right me-2" style="font-size:.7rem;"></i>Peta Klinik</a>
                        <a href="{{ route('klinik.create') }}" class="mb-2"><i class="bi bi-chevron-right me-2" style="font-size:.7rem;"></i>Daftarkan Klinik</a>
                        @guest
                            <a href="{{ route('login') }}"><i class="bi bi-chevron-right me-2" style="font-size:.7rem;"></i>Masuk</a>
                        @endguest
                    </div>
                    <div class="mt-4 d-flex gap-3">
                        <a href="https://instagram.com/frhnwahyudi" aria-label="Instagram" class="fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="https://wa.me/6281247506528" aria-label="WhatsApp" class="fs-5"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
            </div>

            <hr class="my-4 opacity-25">

            <div class="text-center">
                <small>&copy; {{ date('Y') }} SIG Klinik Kecantikan Kota Jambi. Seluruh hak cipta dilindungi.</small>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var revealEls = document.querySelectorAll('[data-reveal]');
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            io.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12 });
                revealEls.forEach(function (el) { io.observe(el); });
            } else {
                revealEls.forEach(function (el) { el.classList.add('is-visible'); });
            }
        });
    </script>
</body>
</html>






