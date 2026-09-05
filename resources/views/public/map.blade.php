@extends('layouts.app')

@section('title', 'Peta Klinik Kecantikan Kota Jambi')
@section('meta_description', 'Peta interaktif dan direktori klinik kecantikan di Kota Jambi. Cari dan filter klinik berdasarkan nama, harga perawatan, serta jenis layanan untuk menemukan klinik yang tepat.')
@section('canonical', route('klinik.map'))

@push('styles')
<style>
    /* Map page hero */
    .map-hero {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(ellipse 55% 60% at 90% 15%, rgba(201, 123, 90, 0.12), transparent 60%),
            radial-gradient(ellipse 45% 50% at 5% 90%, rgba(139, 160, 126, 0.18), transparent 60%),
            linear-gradient(160deg, var(--cream) 0%, #F3EEDF 100%);
        padding: 3.2rem 0 3rem;
        border-bottom: 1px solid rgba(46, 58, 41, 0.06);
    }

    .map-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='56' height='56'%3E%3Cg fill='none' stroke='%2354684A' stroke-opacity='0.06'%3E%3Cpath d='M28 4c6 8 6 16 0 24S22 52 28 60'/%3E%3Cpath d='M28 4c-6 8-6 16 0 24s6 20 0 28'/%3E%3Ccircle cx='28' cy='30' r='3'/%3E%3C/g%3E%3C/svg%3E");
    }

    .map-hero .container { position: relative; z-index: 2; }

    .map-stat-chip {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(46, 58, 41, 0.07);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        padding: 1rem 1.2rem;
        text-align: center;
        transition: all var(--transition);
    }

    .map-stat-chip:hover { transform: translateY(-3px); box-shadow: var(--shadow); }

    .map-stat-chip .stat-num {
        font-family: var(--font-display);
        font-size: 1.7rem;
        font-weight: 600;
        color: var(--sage-900);
        line-height: 1.1;
    }

    .map-stat-chip .stat-lbl {
        font-size: 0.78rem;
        color: var(--muted);
        font-weight: 600;
        letter-spacing: 0.03em;
    }

    /* Filter panel */
    .filter-panel {
        border-radius: var(--radius-lg);
        border: 1px solid rgba(46, 58, 41, 0.06);
        box-shadow: var(--shadow);
        background: var(--white);
        padding: 1.75rem;
    }

    .filter-panel .filter-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--sage-900);
        letter-spacing: 0.01em;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-panel .filter-title i { color: var(--terracotta); }

    .price-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-top: 0.6rem;
    }

    .price-chip {
        border-radius: var(--radius-pill);
        padding: 0.4rem 0.85rem;
        font-size: 0.8rem;
        border: 1.5px solid var(--sage-200);
        background: var(--white);
        color: var(--ink-soft);
        transition: all var(--transition);
        white-space: nowrap;
        font-weight: 600;
    }

    .price-chip:hover {
        border-color: var(--sage-500);
        color: var(--sage-700);
        background: var(--sage-050);
        transform: translateY(-1px);
    }

    .price-chip.active {
        background: var(--sage-700);
        border-color: var(--sage-700);
        color: #fff;
        box-shadow: 0 4px 12px rgba(84, 104, 74, 0.3);
    }

    /* Map */
    #map {
        height: 560px;
        width: 100%;
        z-index: 1;
    }

    .map-tiles { filter: saturate(0.9) contrast(1.02); }

    /* Warna marker per kategori diambil dari design system (beauty.css) */

    /* Popup */
    .leaflet-popup-content { margin: 14px 18px; }

    .popup-title {
        font-family: var(--font-display);
        font-weight: 600;
        font-size: 1.05rem;
        color: var(--sage-900);
    }

    .price-badge {
        display: inline-block;
        background: var(--terracotta-tint);
        color: var(--terracotta-strong);
        border: 1px solid var(--terracotta-soft);
        border-radius: var(--radius-pill);
        padding: 0.35rem 0.9rem;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .category-chip {
        display: inline-block;
        color: #fff;
        border-radius: var(--radius-pill);
        padding: 0.35rem 0.9rem;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .service-tag {
        display: inline-block;
        background: var(--sage-100);
        color: var(--sage-800);
        border-radius: var(--radius-pill);
        padding: 0.25rem 0.7rem;
        font-size: 0.72rem;
        font-weight: 600;
        margin: 0.15rem;
    }

    .results-banner {
        border-radius: var(--radius);
        background: var(--sage-100);
        color: var(--sage-800);
        font-weight: 600;
        padding: 0.75rem 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    /* ── Mobile responsive (map-specific) ── */
    @media (max-width: 767.98px) {
        .map-hero { padding: 2.2rem 0 2rem; }
        #map { height: 460px; }
    }

    @media (max-width: 575.98px) {
        .map-hero { padding: 1.75rem 0; }
        #map { height: 380px; }
        .map-stat-chip { padding: 0.65rem 0.5rem; }
        .map-stat-chip .stat-num { font-size: 1.3rem; }
        .map-stat-chip .stat-lbl { font-size: 0.62rem; letter-spacing: 0; }
        .price-chip { font-size: 0.74rem; padding: 0.35rem 0.7rem; }
        .filter-title { font-size: 0.8rem; }
        .results-banner { padding: 0.6rem 0.9rem; font-size: 0.85rem; }
    }
</style>
@endpush
@section('content')
<!-- Hero -->
<section class="map-hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7" data-reveal>
                <span class="section-eyebrow mb-3">Eksplorasi Klinik</span>
                <h1 class="section-title mb-3" style="font-size: clamp(1.9rem, 3.6vw, 3rem);">
                    Peta Klinik Kecantikan
                    <span class="text-gradient-sage">Kota Jambi</span>
                </h1>
                <p class="section-sub mb-0">
                    Jelajahi sebaran klinik kecantikan, saring berdasarkan layanan dan harga,
                    lalu temukan yang paling sesuai untuk Anda.
                </p>
            </div>

            <div class="col-lg-5" data-reveal style="--reveal-delay: 0.12s;">
                <div class="row g-3">
                    <div class="col-4">
                        <div class="map-stat-chip">
                            <div class="stat-num">{{ $kliniks->count() }}</div>
                            <div class="stat-lbl">Total Klinik</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="map-stat-chip">
                            <div class="stat-num">{{ $kliniks->where('status', 'approved')->count() }}</div>
                            <div class="stat-lbl">Terverifikasi</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="map-stat-chip">
                            <div class="stat-num">{{ \App\Models\Klinik::where('status', 'pending')->count() }}</div>
                            <div class="stat-lbl">Menunggu</div>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <a href="{{ route('klinik.create') }}" class="btn btn-terracotta btn-lg w-100">
                            <i class="bi bi-plus-circle me-2"></i> Daftarkan Klinik Anda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Filter Section -->
<div class="container mt-4 mb-4" data-reveal>
    <div class="filter-panel">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="filter-title mb-3"><i class="bi bi-search"></i> Pencarian Klinik</div>
                <div class="input-icon-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari nama klinik atau alamat...">
                </div>
            </div>

            <div class="col-lg-4">
                <div class="filter-title mb-3"><i class="bi bi-currency-dollar"></i> Rentang Harga</div>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="number" class="form-control form-control-sm" id="minPrice" placeholder="Min (Rp)" min="0" step="1000">
                    </div>
                    <div class="col-6">
                        <input type="number" class="form-control form-control-sm" id="maxPrice" placeholder="Max (Rp)" min="0" step="1000">
                    </div>
                </div>
                <div class="price-chips">
                    <button type="button" class="price-chip" data-min="0" data-max="500000">≤ 500rb</button>
                    <button type="button" class="price-chip" data-min="500000" data-max="1500000">500rb - 1.5jt</button>
                    <button type="button" class="price-chip" data-min="1500000" data-max="3000000">1.5jt - 3jt</button>
                    <button type="button" class="price-chip" data-min="3000000" data-max="">≥ 3jt</button>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="filter-title mb-3"><i class="bi bi-tags"></i> Layanan</div>
                <select class="form-select" id="serviceFilter">
                    <option value="">Semua Layanan</option>
                    <optgroup label="Perawatan Wajah Dasar">
                        <option value="facial_acne">Facial Acne</option>
                        <option value="facial_whitening">Facial Whitening</option>
                        <option value="facial_rejuvenation">Facial Rejuvenation</option>
                        <option value="facial_oxygen">Facial Oxygen</option>
                        <option value="hydrafacial">HydraFacial</option>
                    </optgroup>
                    <optgroup label="Peeling Treatment">
                        <option value="chemical_peeling">Chemical Peeling</option>
                        <option value="mandelic_peel">Mandelic Peel</option>
                        <option value="botanical_peeling">Botanical Peeling</option>
                    </optgroup>
                    <optgroup label="Laser Treatment">
                        <option value="laser_co2">Laser CO2</option>
                        <option value="ipl_treatment">IPL Treatment</option>
                        <option value="picosure_laser">Picosure Laser</option>
                        <option value="qswitch_laser">Q-Switch Laser</option>
                        <option value="laser_melasma">Laser Melasma</option>
                    </optgroup>
                    <optgroup label="Injeksi & Estetik">
                        <option value="botox_injection">Botox Injection</option>
                        <option value="dermal_filler">Dermal Filler</option>
                        <option value="skin_booster">Skin Booster</option>
                        <option value="mesotherapy">Mesotherapy</option>
                        <option value="vitamin_injection">Vitamin Injection</option>
                    </optgroup>
                    <optgroup label="Advanced Treatment">
                        <option value="hifu_treatment">HIFU Treatment</option>
                        <option value="radiofrequency">Radio Frequency</option>
                        <option value="thread_lift">Thread Lift</option>
                        <option value="microneedling">Microneedling</option>
                        <option value="prp_treatment">PRP Treatment</option>
                    </optgroup>
                    <optgroup label="Specialized Treatment">
                        <option value="acne_scar_treatment">Acne Scar Treatment</option>
                        <option value="pigmentation_removal">Pigmentation Removal</option>
                        <option value="hair_removal">Hair Removal</option>
                        <option value="tattoo_removal">Tattoo Removal</option>
                    </optgroup>
                    <optgroup label="Body Treatment">
                        <option value="cryolipolysis">Cryolipolysis</option>
                        <option value="slimming_treatment">Slimming Treatment</option>
                        <option value="body_contouring">Body Contouring</option>
                        <option value="body_whitening">Body Whitening</option>
                    </optgroup>
                </select>
            </div>
        </div>
                <div class="row mt-4 g-3 align-items-center">
            <div class="col-12 col-md-auto">
                <button class="btn btn-sage px-4" id="applyFilters">
                    <i class="bi bi-funnel"></i> Terapkan Filter
                </button>
                <button class="btn btn-outline-sage ms-2" id="resetFilters">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </button>
            </div>
            <div class="col-12 col-md results-banner" id="filterResults" style="display: none;">
                <i class="bi bi-info-circle"></i>
                <span id="resultsText">Menampilkan semua klinik</span>
            </div>
        </div>
    </div>
</div>

<!-- Map Section -->
<div class="container mb-5" data-reveal style="--reveal-delay: 0.1s;">
    <div class="card map-card border-0 shadow-lift">
        <div class="card-body p-0">
            <div class="d-flex align-items-center justify-content-between p-4 pb-3 flex-wrap gap-2">
                <div>
                    <h3 class="mb-1" style="font-family: var(--font-display); font-size: 1.4rem; color: var(--sage-900);">
                        <i class="bi bi-map text-terracotta me-2"></i>Peta Sebaran Klinik
                    </h3>
                    <small class="text-muted">Klik penanda untuk melihat detail klinik</small>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="d-inline-flex align-items-center gap-1" style="font-size:.8rem; color: var(--ink-soft);">
                        <span style="width:12px;height:12px;border-radius:50%;background:linear-gradient(135deg,var(--terracotta),var(--terracotta-strong));display:inline-block;"></span>
                        Hemat
                    </span>
                    <span class="d-inline-flex align-items-center gap-1" style="font-size:.8rem; color: var(--ink-soft);">
                        <span style="width:12px;height:12px;border-radius:50%;background:linear-gradient(135deg,var(--sage-700),var(--sage-500));display:inline-block;"></span>
                        Menengah
                    </span>
                    <span class="d-inline-flex align-items-center gap-1" style="font-size:.8rem; color: var(--ink-soft);">
                        <span style="width:12px;height:12px;border-radius:50%;background:linear-gradient(135deg,var(--gold),#A9823F);display:inline-block;"></span>
                        Premium
                    </span>
                </div>
            </div>
            <div id="map"></div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
let map;
let userMarker;
let markers = [];
let allKliniks = [];
let filteredMarkers = [];

// Initialize map configuration
window.mapConfig = {
    defaultZoom: 13,
    minZoom: 10,
    maxZoom: 18
};

document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
    setupFilterEventListeners();
});

function initializeMap() {
    // Inisialisasi peta
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

    // Tambahkan tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
        className: 'map-tiles'
    }).addTo(map);

    // Data klinik dari controller
    allKliniks = @json($kliniks);

    // Load all markers initially
    loadMarkers(allKliniks);
}

function loadMarkers(kliniks) {
    // Clear existing markers
    clearMarkers();

    kliniks.forEach(function(klinik) {
        // Kategorikan berdasarkan harga tertinggi yang tersedia (max_price, fallback min_price)
        //   Hemat    : <= 1.000.000
        //   Menengah : 1.000.001 - 2.500.000
        //   Premium  : > 2.500.000
        let priceCategory = 'menengah'; // default
        const effectiveMax = klinik.max_price || klinik.min_price || 0;
        if (effectiveMax > 0 && effectiveMax <= 1000000) {
            priceCategory = 'murah';
        } else if (effectiveMax > 2500000) {
            priceCategory = 'mahal';
        }

        const markerClass = `marker-${priceCategory}`;

        // Meta untuk label kategori (ditampilkan di popup)
        const categoryMeta = {
            murah:    { label: 'Hemat',    color: 'var(--terracotta)' },
            menengah: { label: 'Menengah', color: 'var(--sage-700)' },
            mahal:    { label: 'Premium',  color: '#A9823F' }
        };
        const catMeta = categoryMeta[priceCategory];

        var klinikIcon = L.divIcon({
            className: `botan-marker ${markerClass}`,
            html: '<div class="marker-pin"><i class="bi bi-flower2"></i></div>',
            iconSize: [38, 44],
            iconAnchor: [19, 40],
            popupAnchor: [0, -42]
        });

        var marker = L.marker([klinik.latitude, klinik.longitude], {
            icon: klinikIcon
        }).addTo(map);

        // Enhanced popup content with price and services from database
        const services = klinik.services || [];
        const priceDisplay = getPriceRangeDisplay(klinik.min_price, klinik.max_price);

        var popupContent = `
            <div class="text-center p-1">
                ${klinik.foto_url ? `<img src="${klinik.foto_url}" class="img-fluid rounded-4 mb-2" style="max-height: 100px; width: 100%; object-fit: cover;">` : ''}
                <div class="popup-title mb-1">${klinik.nama}</div>
                <p class="small text-muted mb-2"><i class="bi bi-geo-alt"></i> ${klinik.alamat}</p>
                ${klinik.deskripsi ? `<p class="small text-muted mb-2">${klinik.deskripsi}</p>` : ''}
                <p class="small mb-2"><i class="bi bi-clock"></i> ${klinik.jam_operasional}</p>
                <div class="mb-2 d-flex justify-content-center align-items-center gap-2 flex-wrap">
                    <span class="category-chip" style="background:${catMeta.color};">${catMeta.label}</span>
                    <span class="price-badge">${priceDisplay}</span>
                </div>
                <div class="mb-3">
                    ${services.map(service => `<span class="service-tag">${getServiceDisplay(service)}</span>`).join('')}
                </div>
                <a href="/klinik/${klinik.id}" class="btn btn-sage btn-sm w-100">
                    <i class="bi bi-info-circle"></i> Lihat Detail
                </a>
            </div>
        `;

        marker.bindPopup(popupContent, { closeButton: true });

        // Store additional data with marker
        marker.klinikData = {
            ...klinik,
            priceCategory: priceCategory,
            services: services
        };

        markers.push(marker);
    });

    // Set default zoom view untuk seluruh kota
    map.setZoom(12);

    // Fit bounds jika ada marker
    if (markers.length > 0) {
        let group = new L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.5));
    }

    updateFilterResults(markers.length, allKliniks.length);
}

function clearMarkers() {
    markers.forEach(marker => {
        map.removeLayer(marker);
    });
    markers = [];
}

function setupFilterEventListeners() {
    // Apply filters button
    document.getElementById('applyFilters').addEventListener('click', applyFilters);

    // Reset filters button
    document.getElementById('resetFilters').addEventListener('click', resetFilters);

    // Price chips quick-select
    document.querySelectorAll('.price-chip').forEach(function(chip) {
        chip.addEventListener('click', function() {
            document.querySelectorAll('.price-chip').forEach(function(c) { c.classList.remove('active'); });
            chip.classList.add('active');
            document.getElementById('minPrice').value = chip.dataset.min;
            document.getElementById('maxPrice').value = chip.dataset.max;
            applyFilters();
        });
    });

    // Live search on Enter
    document.getElementById('searchInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            applyFilters();
        }
    });
}

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const minPrice = parseInt(document.getElementById('minPrice').value) || 0;
    const maxPrice = parseInt(document.getElementById('maxPrice').value) || Number.MAX_SAFE_INTEGER;
    const serviceFilter = document.getElementById('serviceFilter').value;

    let filteredKliniks = allKliniks.filter(klinik => {
        // Search filter - search in name, address, and description
        const matchesSearch = !searchTerm ||
            klinik.nama.toLowerCase().includes(searchTerm) ||
            klinik.alamat.toLowerCase().includes(searchTerm) ||
            (klinik.deskripsi && klinik.deskripsi.toLowerCase().includes(searchTerm));

        // Price filter - check if klinik price range overlaps with filter range
        const klinikMinPrice = klinik.min_price || 0;
        const klinikMaxPrice = klinik.max_price || klinik.min_price || Number.MAX_SAFE_INTEGER;
        const matchesPrice = (minPrice === 0 && maxPrice === Number.MAX_SAFE_INTEGER) ||
                           (klinikMinPrice <= maxPrice && klinikMaxPrice >= minPrice);

        // Service filter - use actual services array from database
        const klinikServices_array = klinik.services || [];
        const matchesService = !serviceFilter || klinikServices_array.includes(serviceFilter);

        return matchesSearch && matchesPrice && matchesService;
    });

    loadMarkers(filteredKliniks);
    updateFilterResults(filteredKliniks.length, allKliniks.length);
}

function resetFilters() {
    document.querySelectorAll('.price-chip').forEach(function(c) { c.classList.remove('active'); });

    document.getElementById('searchInput').value = '';
    document.getElementById('minPrice').value = '';
    document.getElementById('maxPrice').value = '';
    document.getElementById('serviceFilter').value = '';

    loadMarkers(allKliniks);
}

function updateFilterResults(shown, total) {
    const banner = document.getElementById('filterResults');
    const resultsText = document.getElementById('resultsText');
    if (shown === total) {
        resultsText.textContent = `Menampilkan semua ${total} klinik`;
    } else {
        resultsText.textContent = `Menampilkan ${shown} dari ${total} klinik`;
    }
    banner.style.display = shown === total && total > 0 ? 'none' : 'flex';
}

function getPriceRangeDisplay(minPrice, maxPrice) {
    if (minPrice && maxPrice) {
        return `Rp ${formatPrice(minPrice)} - Rp ${formatPrice(maxPrice)}`;
    } else if (minPrice) {
        return `Mulai dari Rp ${formatPrice(minPrice)}`;
    }
    return 'Harga belum tersedia';
}

function formatPrice(price) {
    return new Intl.NumberFormat('id-ID').format(price);
}

function getServiceDisplay(service) {
    const displays = {
        // Perawatan Wajah Dasar
        'facial_basic': 'Facial Basic',
        'facial_acne': 'Facial Acne',
        'facial_brightening': 'Facial Brightening',
        'blackhead_removal': 'Blackhead Removal',
        'hydrafacial': 'HydraFacial',

        // Peeling Treatment
        'chemical_peel': 'Chemical Peeling',
        'carbon_peel': 'Carbon Peel',
        'milk_peel': 'Milk Peel',

        // Laser Treatment
        'laser_rejuvenation': 'Laser Rejuvenation',
        'laser_acne': 'Laser Acne',
        'ipl_photorejuvenation': 'IPL Photorejuvenation',
        'laser_hair_removal': 'Laser Hair Removal',
        'co2_laser': 'CO2 Laser',

        // Injeksi & Estetik
        'botox': 'Botox',
        'filler': 'Dermal Filler',
        'skinbooster': 'Skin Booster',
        'vitamin_injection': 'Vitamin Injection',
        'whitening_injection': 'Whitening Injection',

        // Advanced Treatment
        'microneedling': 'Microneedling',
        'rf_microneedling': 'RF Microneedling',
        'hifu': 'HIFU',

        // Specialized Treatment
        'prp_therapy': 'PRP Therapy',
        'thread_lift': 'Thread Lift',
        'cryotherapy': 'Cryotherapy',
        'sclerotherapy': 'Sclerotherapy',

        // Body Treatment
        'body_contouring': 'Body Contouring',
        'cavitation': 'Cavitation',
        'radiofrequency': 'Radiofrequency',
        'coolsculpting': 'CoolSculpting'
    };

    return displays[service] || service.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}
</script>
@endpush



    </div>
</div>


