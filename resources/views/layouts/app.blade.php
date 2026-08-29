<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIG Klinik Kecantikan Kota Jambi')</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- No Cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Botanical Calm Design System -->
    <link href="{{ asset('css/beauty.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
    <div class="grain-overlay" aria-hidden="true"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg botan-nav sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <span class="brand-mark"><i class="bi bi-flower1"></i></span>
                <span class="d-none d-sm-inline">SIG Klinik Kecantikan</span>
                <span class="d-inline d-sm-none">SIG Klinik</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('klinik/map') ? 'active' : '' }}" href="{{ route('klinik.map') }}">
                            <i class="bi bi-map"></i> Peta Klinik
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('klinik/create') ? 'active' : '' }}" href="{{ route('klinik.create') }}">
                            <i class="bi bi-plus-circle"></i> Daftar Klinik
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

        <main>
        @if(session('success'))
            <div class="container mt-4">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="container mt-4">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="bi bi-exclamation-triangle me-2"></i>Periksa kembali isian Anda:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="botan-footer pt-5 pb-4 mt-5">
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
                        <a href="{{ route('home') }}" class="mb-2"><i class="bi bi-chevron-right me-2" style="font-size:.7rem;"></i>Beranda</a>
                        <a href="{{ route('klinik.map') }}" class="mb-2"><i class="bi bi-chevron-right me-2" style="font-size:.7rem;"></i>Peta Klinik</a>
                        <a href="{{ route('klinik.create') }}" class="mb-2"><i class="bi bi-chevron-right me-2" style="font-size:.7rem;"></i>Daftarkan Klinik</a>
                        @guest
                            <a href="{{ route('login') }}"><i class="bi bi-chevron-right me-2" style="font-size:.7rem;"></i>Masuk</a>
                        @endguest
                    </div>
                </div>
            </div>

            <hr class="my-4 opacity-25">

            <div class="text-center">
                <small>&copy; {{ date('Y') }} SIG Klinik Kecantikan Kota Jambi. Seluruh hak cipta dilindungi.</small>
            </div>
        </div>
    </footer>

        <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Global map configuration (pages may override)
        window.mapConfig = {
            defaultZoom: 10,
            maxZoom: 18,
            minZoom: 9
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Reveal-on-scroll animation
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

            // Safety fallback: force-show anything still hidden after a short delay
            setTimeout(function () {
                revealEls.forEach(function (el) { el.classList.add('is-visible'); });
            }, 2000);
        });
    </script>

    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('error') }}"
        });
    </script>
    @endif

    @stack('scripts')
</body>
</html>


