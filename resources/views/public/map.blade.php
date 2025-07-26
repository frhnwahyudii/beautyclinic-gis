@extends('layouts.app')

@section('title', 'Peta Klinik Kecantikan Kota Jambi')

@push('styles')
<style>
    #map {
        height: 500px;
        width: 100%;
        border-radius: 0.5rem;
    }

    .map-tiles {
        filter: saturate(1.2) contrast(1.1);
    }

    .custom-marker i {
        position: relative;
        color: #dc3545;
        font-size: 40px;
        filter: drop-shadow(2px 2px 2px rgba(0,0,0,0.2));
    }

    .custom-marker i i {
        position: absolute;
        top: 35%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 14px;
        color: white;
    }

    .hero-section {
        position: relative;
        z-index: 1;
    }

    .hero-section .container {
        position: relative;
        z-index: 2;
    }

    .hero-section button,
    .hero-section a {
        position: relative;
        z-index: 3;
    }

    .hero-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
        padding: 4rem 0;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
    }

    .hero-pattern {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0.1;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M54.627 0l.83.828-1.415 1.415L51.8 0h2.827zM5.373 0l-.83.828L5.96 2.243 8.2 0H5.374zM48.97 0l3.657 3.657-1.414 1.414L46.143 0h2.828zM11.03 0L7.372 3.657 8.787 5.07 13.857 0H11.03zm32.284 0L49.8 6.485 48.384 7.9l-7.9-7.9h2.83zM16.686 0L10.2 6.485 11.616 7.9l7.9-7.9h-2.83zM22.344 0L13.858 8.485 15.272 9.9l7.9-7.9h-.828zm5.656 0L19.515 8.485 17.343 10.657l7.9-7.9h2.757zm5.656 0L24.172 8.485 22 10.657l7.9-7.9h2.756zM38.313 0L29.828 8.485 27.657 10.657l7.9-7.9h2.756zM43.97 0L35.485 8.485 33.313 10.657l7.9-7.9h2.757zM49.626 0L41.142 8.485 38.97 10.657l7.9-7.9h2.756zM55.285 0L46.8 8.485 44.627 10.657l7.9-7.9h2.758zM54.627 5.657L46.142 14.142 43.97 16.314l7.9-7.9h2.757zm5.656 0L51.8 14.142l-2.172 2.172 7.9-7.9h2.756zM5.373 5.657L13.858 14.142 16.03 16.314l-7.9-7.9H5.374zm5.656 0L19.514 14.142l2.172 2.172-7.9-7.9h-2.757zm5.656 0L24.172 14.142l2.172 2.172-7.9-7.9h-2.757zm5.656 0L29.828 14.142 32 16.314l-7.9-7.9h-2.757zm5.657 0L35.485 14.142l2.172 2.172-7.9-7.9h-2.757zm5.656 0L41.142 14.142l2.172 2.172-7.9-7.9h-2.757zm5.656 0L46.8 14.142l2.172 2.172-7.9-7.9h-2.757zm-11.313 0L35.485 8.485 33.313 10.657l7.9-7.9h2.757zM43.97 5.657L35.485 14.142 33.313 16.314l7.9-7.9h2.757zM49.626 5.657L41.142 14.142 38.97 16.314l7.9-7.9h2.756zM54.627 11.314L46.142 19.8l-2.172 2.172 7.9-7.9h2.757zm5.656 0L51.8 19.8l-2.172 2.172 7.9-7.9h2.756zM5.373 11.314L13.858 19.8l2.172 2.172-7.9-7.9H5.374zm5.656 0L19.514 19.8l2.172 2.172-7.9-7.9h-2.757zm5.656 0L24.172 19.8l2.172 2.172-7.9-7.9h-2.757zm5.656 0L29.828 19.8 32 21.971l-7.9-7.9h-2.757zm5.657 0L35.485 19.8l2.172 2.172-7.9-7.9h-2.757zm5.656 0L41.142 19.8l2.172 2.172-7.9-7.9h-2.757zm5.656 0L46.8 19.8l2.172 2.172-7.9-7.9h-2.757zm-11.313 0L35.485 14.142 33.313 16.314l7.9-7.9h2.757zM43.97 11.314L35.485 19.8l-2.172 2.172 7.9-7.9h2.757zM49.626 11.314L41.142 19.8l-2.172 2.172 7.9-7.9h2.756zM54.627 16.971L46.142 25.456l-2.172 2.172 7.9-7.9h2.757zm5.656 0L51.8 25.456l-2.172 2.172 7.9-7.9h2.756zM5.373 16.971L13.858 25.456l2.172 2.172-7.9-7.9H5.374zm5.656 0L19.514 25.456l2.172 2.172-7.9-7.9h-2.757zm5.656 0L24.172 25.456l2.172 2.172-7.9-7.9h-2.757zm5.656 0L29.828 25.456 32 27.628l-7.9-7.9h-2.757z' fill='%23ffffff' fill-opacity='0.4' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    .feature-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: transform 0.2s;
    }

    .feature-card:hover {
        transform: translateY(-5px);
    }

    .feature-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        background: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .search-box {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        padding: 2rem;
        margin-top: 2rem;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="hero-section text-white">
    <div class="hero-pattern"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Temukan Klinik Kecantikan di Kota Jambi</h1>
                <p class="lead mb-4">Sistem Informasi Geografis untuk memudahkan Anda menemukan klinik kecantikan terdekat dengan informasi lengkap.</p>
                <a href="{{ route('klinik.create') }}" class="btn btn-outline-light btn-lg px-4">
                    <i class="bi bi-plus-circle"></i> Daftarkan Klinik
                </a>
            </div>
            <div class="col-lg-6">
                <div class="search-box">
                    <h5 class="text-white mb-3">Statistik Klinik</h5>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="text-center">
                                <h3 class="mb-0">{{ $kliniks->count() }}</h3>
                                <small>Total Klinik</small>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-center">
                                <h3 class="mb-0">{{ $kliniks->where('status', 'approved')->count() }}</h3>
                                <small>Terverifikasi</small>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-center">
                                <h3 class="mb-0">{{ \App\Models\Klinik::where('status', 'pending')->count() }}</h3>
                                <small>Menunggu</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map Section -->
<div class="container mb-5">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h3 class="card-title mb-4">Peta Sebaran Klinik Kecantikan</h3>
            <div id="map"></div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="container mb-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card feature-card">
                <div class="card-body text-center p-4">
                    <div class="feature-icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <h5>Pencarian Mudah</h5>
                    <p class="text-muted">Temukan klinik kecantikan terdekat dengan sistem pencarian yang mudah dan interaktif.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card">
                <div class="card-body text-center p-4">
                    <div class="feature-icon">
                        <i class="bi bi-geo"></i>
                    </div>
                    <h5>Lokasi Akurat</h5>
                    <p class="text-muted">Dapatkan informasi lokasi yang akurat dengan bantuan peta interaktif.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card">
                <div class="card-body text-center p-4">
                    <div class="feature-icon">
                        <i class="bi bi-info-circle"></i>
                    </div>
                    <h5>Informasi Lengkap</h5>
                    <p class="text-muted">Akses informasi lengkap tentang layanan, jam operasional, dan kontak klinik.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let map;
let userMarker;
let markers = [];

document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi peta dengan zoom yang lebih jauh
    map = L.map('map', {
        center: [-1.6096639, 103.6131639],
        zoom: window.mapConfig.defaultZoom,
        minZoom: window.mapConfig.minZoom,
        maxZoom: window.mapConfig.maxZoom,
        zoomControl: true,
        scrollWheelZoom: true
    });

    // Force set zoom setelah inisialisasi
    setTimeout(() => {
        map.setZoom(window.mapConfig.defaultZoom);
    }, 100);

    // Tambahkan tile layer dengan tampilan yang lebih detail
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
        className: 'map-tiles'
    }).addTo(map);

    // Data klinik dari controller
    var kliniks = @json($kliniks);

    // Custom icon untuk marker klinik kecantikan
    var klinikIcon = L.divIcon({
        className: 'custom-marker',
        html: '<i class="bi bi-geo-alt-fill"><i class="bi bi-hospital"></i></i>',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
    });

    // Tambahkan marker untuk setiap klinik
    kliniks.forEach(function(klinik) {
        var marker = L.marker([klinik.latitude, klinik.longitude], {
            icon: klinikIcon
        }).addTo(map);

        var popupContent = `
            <div class="popup-content text-center p-2">
                ${klinik.foto ? `<img src="${klinik.foto_url}" class="img-fluid rounded mb-2" style="max-height: 100px;">` : ''}
                <h6 class="mb-2">${klinik.nama}</h6>
                <p class="small mb-2">${klinik.alamat}</p>
                <p class="small mb-2"><i class="bi bi-clock"></i> ${klinik.jam_operasional}</p>
                <a href="/klinik/${klinik.id}" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-info-circle"></i> Lihat Detail
                </a>
            </div>
        `;

        marker.bindPopup(popupContent);
        markers.push(marker);
    });

    // Set default zoom view untuk seluruh kota
    map.setZoom(12);

    // Fit bounds jika ada marker
    if (markers.length > 0) {
        let group = new L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.5));
    }
});

// No location finding functionality needed
</script>
@endpush
