<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta (area admin: jangan pernah diindeks) -->
    @php
        $seoTitle = trim((string) ($__env->yieldContent('title') ?: 'Admin Panel — SIG Klinik Kecantikan'));
        $seoDescription = trim((string) $__env->yieldContent('meta_description'));
        $seoRobots = 'noindex, nofollow';
        $seoCanonical = '';
        $seoImage = '';
        $seoType = 'website';
    @endphp
    @include('partials.seo-head')

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous">

    <link href="{{ asset('css/beauty.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="admin-body">
    <div class="grain-overlay" aria-hidden="true"></div>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <a href="{{ url('/admin') }}" class="sidebar-brand">
            <div class="brand-mark"><i class="fas fa-leaf"></i></div>
            <div class="brand-text">
                SIG Klinik Kecantikan
                <small>Panel Admin</small>
            </div>
        </a>

        <div class="sidebar-heading">Menu Utama</div>
        <nav class="nav flex-column">
            <a class="nav-link {{ Request::is('admin') ? 'active' : '' }}" href="{{ url('/admin') }}">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a class="nav-link {{ Request::is('admin/kliniks*') ? 'active' : '' }}" href="{{ route('admin.kliniks.index') }}">
                <i class="fas fa-clinic-medical"></i> Manajemen Klinik
            </a>
        </nav>

        <div class="sidebar-heading">Akses Cepat</div>
        <nav class="nav flex-column">
            <a class="nav-link" href="{{ route('home') }}" target="_blank">
                <i class="fas fa-globe"></i> Lihat Website
            </a>
            <a class="nav-link" href="{{ route('klinik.create') }}" target="_blank">
                <i class="fas fa-plus-circle"></i> Tambah Klinik
            </a>
        </nav>

        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="flex-grow-1 min-width-0">
                <div class="text-white fw-bold text-truncate" style="font-size: .9rem;">{{ Auth::user()->name }}</div>
                <small style="color: rgba(255,255,255,0.5);">Administrator</small>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn p-0 border-0 bg-transparent text-white-50" title="Logout" style="font-size: 1rem;">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Sidebar backdrop (mobile) -->
    <div class="admin-sidebar-backdrop" id="sidebarBackdrop"></div>
        <!-- Main -->
    <div class="admin-main">
        <div class="admin-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-sage d-lg-none" id="sidebarToggle" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="d-none d-md-block">
                    <div class="admin-page-title">@yield('page-title', 'Dashboard')</div>
                    <small class="admin-page-sub">@yield('page-sub')</small>
                </div>
                <span class="d-md-none fw-bold text-sage" style="font-family: var(--font-display); font-size: 1.05rem;">
                    @yield('page-title', 'Dashboard')
                </span>
            </div>

            <div class="dropdown">
                <button class="btn btn-outline-sage d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle" style="font-size: 1.2rem;"></i>
                    <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down" style="font-size: .7rem;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lift">
                    <li>
                        <a class="dropdown-item" href="{{ route('home') }}" target="_blank">
                            <i class="fas fa-globe me-2"></i> Lihat Website
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <main class="admin-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
        <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Mobile sidebar toggle
            $('#sidebarToggle').on('click', function() {
                $('#adminSidebar').toggleClass('open');
                $('#sidebarBackdrop').toggleClass('show');
            });
            $('#sidebarBackdrop').on('click', function() {
                $('#adminSidebar').removeClass('open');
                $(this).removeClass('show');
            });

            // Reveal-on-scroll animation (ensures [data-reveal] content becomes visible)
            var revealEls = document.querySelectorAll('[data-reveal]');
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            io.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.08 });
                revealEls.forEach(function (el) { io.observe(el); });
            } else {
                revealEls.forEach(function (el) { el.classList.add('is-visible'); });
            }

            // Safety fallback: force-show anything still hidden after a short delay
            setTimeout(function() {
                revealEls.forEach(function (el) { el.classList.add('is-visible'); });
            }, 2000);

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>

    @if(session('success'))
    <script>
        $(document).ready(function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        $(document).ready(function() {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
                confirmButtonColor: '#C0534B'
            });
        });
    </script>
    @endif

    @stack('scripts')
</body>
</html>


