@extends('layouts.app')

@php
    // ── SEO: metadata dinamis per klinik ──
    $seoIndexStatus = $klinik->status === 'approved';
    $seoCanonicalUrl = route('klinik.show', $klinik);

    $seoDescRaw = trim((string) preg_replace('/\s+/', ' ', strip_tags((string) $klinik->deskripsi)));
    if ($seoDescRaw === '') {
        $seoDescRaw = trim((string) preg_replace('/\s+/', ' ', strip_tags((string) $klinik->alamat)));
    }
    $seoMetaDescription = $seoDescRaw !== '' ? mb_strimwidth($seoDescRaw, 0, 155, '…') : '';

    $seoSameAs = [];
    if (! empty($klinik->website)) {
        $seoSameAs[] = preg_match('#^https?://#i', (string) $klinik->website)
            ? (string) $klinik->website
            : 'https://'.ltrim((string) $klinik->website, '/');
    }
    if (! empty($klinik->instagram)) { $seoSameAs[] = 'https://www.instagram.com/'.ltrim((string) $klinik->instagram, '@'); }
    if (! empty($klinik->facebook))  { $seoSameAs[] = 'https://www.facebook.com/'.ltrim((string) $klinik->facebook, '@'); }
    if (! empty($klinik->twitter))   { $seoSameAs[] = 'https://x.com/'.ltrim((string) $klinik->twitter, '@'); }

    $seoPhoneDigits = (string) preg_replace('/\D/', '', (string) $klinik->telepon);
    $seoPhone = $seoPhoneDigits === ''
        ? ''
        : (str_starts_with($seoPhoneDigits, '62')
            ? '+'.$seoPhoneDigits
            : '+62'.ltrim($seoPhoneDigits, '0'));

    // Skema JSON-LD LocalBusiness (hanya klinik yang disetujui).
    $seoJsonLd = $seoIndexStatus ? [
        '@context' => 'https://schema.org',
        '@type' => 'BeautySalon',
        '@id' => $seoCanonicalUrl.'#klinik',
        'name' => $klinik->nama,
        'url' => $seoCanonicalUrl,
        'image' => $klinik->foto_url,
        'description' => $seoDescRaw !== '' ? mb_strimwidth($seoDescRaw, 0, 300, '…') : null,
        'telephone' => $seoPhone !== '' ? $seoPhone : null,
        'priceRange' => $klinik->price_range_display,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $klinik->alamat,
            'addressLocality' => 'Kota Jambi',
            'addressRegion' => 'Jambi',
            'addressCountry' => 'ID',
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => (float) $klinik->latitude,
            'longitude' => (float) $klinik->longitude,
        ],
        'sameAs' => $seoSameAs,
        'currenciesAccepted' => 'IDR',
    ] : null;
@endphp

@section('title', $klinik->nama)
@section('meta_description', $seoMetaDescription)
@section('canonical', $seoCanonicalUrl)
@section('robots', $seoIndexStatus ? 'index, follow' : 'noindex, follow')
@section('og_image', (string) $klinik->foto_url)
@section('og_type', 'website')

@if($seoIndexStatus)
@push('seo-jsonld')
<script type="application/ld+json">
{!! json_encode($seoJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_PRETTY_PRINT) !!}
</script>
@endpush
@endif

@push('styles')
<style>
    .detail-cover-wrap {
        position: relative;
        border-radius: var(--radius-arch);
        overflow: hidden;
        box-shadow: var(--shadow);
        margin-top: 1.5rem;
    }

    .detail-cover-wrap .cover-img {
        width: 100%;
        height: 340px;
        object-fit: cover;
    }

    .detail-cover-fallback {
        height: 340px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--sage-100), var(--sage-200));
        color: var(--sage-600);
        font-size: 3.4rem;
    }

    .detail-profile-card {
        margin-top: -64px;
        position: relative;
        z-index: 2;
    }

    .detail-profile-inner {
        background: var(--white);
        border: 1px solid rgba(46, 58, 41, 0.07);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        padding: 1.5rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .detail-avatar {
        width: 84px;
        height: 84px;
        border-radius: 28px;
        object-fit: cover;
        border: 4px solid var(--white);
        box-shadow: var(--shadow);
    }

    .detail-avatar-fallback {
        width: 84px;
        height: 84px;
        border-radius: 28px;
        background: linear-gradient(135deg, var(--sage-600), var(--sage-500));
        color: #fff;
        font-size: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid var(--white);
        box-shadow: var(--shadow);
    }

    .info-block {
        background: var(--white);
        border: 1px solid rgba(46, 58, 41, 0.07);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        padding: 1.25rem 1.4rem;
        margin-bottom: 1.5rem;
    }

    .info-block .info-title {
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--sage-700);
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1rem;
    }

    .info-block .info-title i { color: var(--terracotta); }

    /* Service price rows */
    .service-category {
        margin-bottom: 1.75rem;
    }

    .service-category:last-child { margin-bottom: 0; }

    .service-category-title {
        font-family: var(--font-display);
        font-weight: 600;
        font-size: 1.05rem;
        color: var(--sage-900);
        padding-bottom: 0.6rem;
        margin-bottom: 0.85rem;
        border-bottom: 1px dashed var(--sage-300);
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .service-category-title i { color: var(--terracotta); font-size: 0.95rem; }

    .service-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 0.7rem 0.9rem;
        border-radius: var(--radius);
        transition: all var(--transition);
        margin-bottom: 0.4rem;
    }

    .service-item:hover {
        background: var(--sage-050);
        transform: translateX(3px);
    }

    .service-item .service-name {
        font-weight: 600;
        color: var(--ink);
        font-size: 0.95rem;
    }

    .service-item .service-price {
        background: var(--terracotta-tint);
        color: var(--terracotta-strong);
        font-weight: 700;
        padding: 0.45rem 1rem;
        border-radius: var(--radius-pill);
        font-size: 0.85rem;
        white-space: nowrap;
        border: 1px solid var(--terracotta-soft);
    }

    .contact-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--sage-050);
        color: var(--sage-800);
        border: 1px solid var(--sage-200);
        border-radius: var(--radius-pill);
        padding: 0.55rem 1.1rem;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        transition: all var(--transition);
    }

    .contact-chip:hover {
        background: var(--sage-700);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(84, 104, 74, 0.3);
    }

    .social-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        border-radius: 16px;
        background: var(--white);
        border: 1px solid var(--sage-200);
        color: var(--sage-700);
        font-size: 1.15rem;
        transition: all var(--transition);
        text-decoration: none;
    }

    .social-chip:hover {
        background: var(--terracotta);
        border-color: var(--terracotta);
        color: #fff;
        transform: translateY(-3px);
        box-shadow: var(--shadow-terra);
    }

    #map {
        height: 300px;
        width: 100%;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--sage-700);
        font-weight: 600;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all var(--transition);
    }

    .back-link:hover { color: var(--terracotta); gap: 0.75rem; }

    /* ── Mobile responsive (detail-specific) ── */
    @media (max-width: 767.98px) {
        .detail-cover-wrap .cover-img,
        .detail-cover-fallback { height: 260px; }
        .detail-profile-card { margin-top: -48px; }
        .detail-profile-inner { flex-direction: column; align-items: flex-start; }
    }

    @media (max-width: 575.98px) {
        .detail-cover-wrap .cover-img,
        .detail-cover-fallback { height: 210px; }
        .detail-cover-fallback { font-size: 2.4rem; }
        .detail-profile-card { margin-top: -40px; }
        .detail-profile-inner { padding: 1.1rem 1.25rem; }
        .detail-profile-inner h1 { font-size: 1.25rem; }
        .detail-avatar, .detail-avatar-fallback { width: 64px; height: 64px; border-radius: 20px; font-size: 1.5rem; }
        .info-block { padding: 1rem 1.05rem; margin-bottom: 1.1rem; }
        .info-block .info-title { font-size: 0.72rem; }
        .service-item { padding: 0.55rem 0.7rem; }
        .service-item .service-name { font-size: 0.86rem; }
        .service-item .service-price { font-size: 0.78rem; padding: 0.35rem 0.7rem; }
        .service-category-title { font-size: 0.98rem; }
        #map { height: 260px; }
    }
</style>
@endpush
@section('content')
<div class="container py-4">
    <a href="{{ route('klinik.map') }}" class="back-link mb-4">
        <i class="bi bi-arrow-left"></i> Kembali ke Peta Klinik
    </a>

    <!-- Cover -->
    <div class="detail-cover-wrap">
        @if($klinik->foto_url)
            <img src="{{ $klinik->foto_url }}" class="cover-img" alt="{{ $klinik->nama }}">
        @else
            <div class="detail-cover-fallback">
                <i class="bi bi-image"></i>
            </div>
        @endif
    </div>

    <!-- Profile card -->
    <div class="detail-profile-card">
        <div class="detail-profile-inner">
            <div class="d-flex align-items-center gap-3">
                @if($klinik->foto_url)
                    <img src="{{ $klinik->foto_url }}" class="detail-avatar" alt="{{ $klinik->nama }}">
                @else
                    <div class="detail-avatar-fallback"><i class="bi bi-buildings"></i></div>
                @endif
                <div>
                    <h1 class="mb-1" style="font-family: var(--font-display); font-size: 1.6rem; color: var(--sage-900);">
                        {{ $klinik->nama }}
                    </h1>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="status-pill {{ $klinik->status == 'approved' ? 'status-approved' : ($klinik->status == 'pending' ? 'status-pending' : 'status-rejected') }}">
                            {{ $klinik->status == 'approved' ? 'Terverifikasi' : ($klinik->status == 'pending' ? 'Menunggu' : 'Ditolak') }}
                        </span>
                        @if($klinik->min_price || $klinik->max_price)
                            <span class="badge bg-terracotta-soft">
                                <i class="bi bi-tag me-1"></i>{{ $klinik->price_range_display }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="#layanan" class="btn btn-sage btn-sm px-3">
                    <i class="bi bi-list-check me-1"></i> Lihat Layanan
                </a>
                <a href="https://www.google.com/maps/search/?api=1&query={{ $klinik->latitude }},{{ $klinik->longitude }}" target="_blank"
                   class="btn btn-outline-sage btn-sm px-3">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Petunjuk Arah
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-lg-8">
            @if($klinik->deskripsi)
            <div class="info-block" data-reveal>
                <div class="info-title"><i class="bi bi-info-circle"></i> Tentang Klinik</div>
                <p class="mb-0 text-ink-soft">{{ $klinik->deskripsi }}</p>
            </div>
            @endif

            <div class="info-block" data-reveal>
                <div class="info-title"><i class="bi bi-geo-alt"></i> Alamat</div>
                <p class="mb-0 text-ink-soft">{{ $klinik->alamat }}</p>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="info-block mb-0" data-reveal>
                        <div class="info-title"><i class="bi bi-clock"></i> Jam Operasional</div>
                        <p class="mb-0 fw-semibold">{{ $klinik->jam_operasional }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-block mb-0" data-reveal style="--reveal-delay: 0.08s;">
                        <div class="info-title"><i class="bi bi-tag"></i> Rentang Harga</div>
                        @if($klinik->min_price || $klinik->max_price)
                            <p class="mb-0 fw-bold" style="color: var(--terracotta-strong);">{{ $klinik->price_range_display }}</p>
                        @else
                            <p class="mb-0 text-muted">Harga belum tersedia</p>
                        @endif
                    </div>
                </div>
            </div>
                        <!-- Services & Prices -->
            @if($klinik->services && count($klinik->services) > 0)
            <div class="info-block" id="layanan" data-reveal>
                <div class="info-title"><i class="bi bi-list-check"></i> Layanan & Harga</div>
                @php
                    $services = collect($klinik->services);
                    $facialServices = $services->intersect(['facial_basic', 'facial_acne', 'facial_brightening', 'blackhead_removal', 'hydrafacial']);
                    $peelingServices = $services->intersect(['chemical_peel', 'carbon_peel', 'milk_peel']);
                    $laserServices = $services->intersect(['laser_rejuvenation', 'laser_acne', 'ipl_photorejuvenation', 'laser_hair_removal', 'co2_laser']);
                    $injectionServices = $services->intersect(['botox', 'filler', 'skinbooster', 'vitamin_injection', 'whitening_injection']);
                    $advancedServices = $services->intersect(['microneedling', 'rf_microneedling', 'hifu']);
                    $specializedServices = $services->intersect(['prp_therapy', 'thread_lift', 'cryotherapy', 'sclerotherapy']);
                    $bodyServices = $services->intersect(['body_contouring', 'cavitation', 'radiofrequency', 'coolsculpting']);
                @endphp

                @if($facialServices->isNotEmpty())
                <div class="service-category">
                    <div class="service-category-title"><i class="bi bi-flower1"></i> Perawatan Wajah Dasar</div>
                    @foreach($facialServices as $service)
                    <div class="service-item">
                        <span class="service-name">{{ \App\Models\Klinik::SERVICE_NAMES[$service] }}</span>
                        <span class="service-price">Rp {{ number_format($klinik->service_prices[$service] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($peelingServices->isNotEmpty())
                <div class="service-category">
                    <div class="service-category-title"><i class="bi bi-droplet"></i> Peeling Treatment</div>
                    @foreach($peelingServices as $service)
                    <div class="service-item">
                        <span class="service-name">{{ \App\Models\Klinik::SERVICE_NAMES[$service] }}</span>
                        <span class="service-price">Rp {{ number_format($klinik->service_prices[$service] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($laserServices->isNotEmpty())
                <div class="service-category">
                    <div class="service-category-title"><i class="bi bi-stars"></i> Laser Treatment</div>
                    @foreach($laserServices as $service)
                    <div class="service-item">
                        <span class="service-name">{{ \App\Models\Klinik::SERVICE_NAMES[$service] }}</span>
                        <span class="service-price">Rp {{ number_format($klinik->service_prices[$service] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
                                @if($injectionServices->isNotEmpty())
                <div class="service-category">
                    <div class="service-category-title"><i class="bi bi-syringe"></i> Injeksi & Estetik</div>
                    @foreach($injectionServices as $service)
                    <div class="service-item">
                        <span class="service-name">{{ \App\Models\Klinik::SERVICE_NAMES[$service] }}</span>
                        <span class="service-price">Rp {{ number_format($klinik->service_prices[$service] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($advancedServices->isNotEmpty())
                <div class="service-category">
                    <div class="service-category-title"><i class="bi bi-magic"></i> Advanced Treatment</div>
                    @foreach($advancedServices as $service)
                    <div class="service-item">
                        <span class="service-name">{{ \App\Models\Klinik::SERVICE_NAMES[$service] }}</span>
                        <span class="service-price">Rp {{ number_format($klinik->service_prices[$service] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($specializedServices->isNotEmpty())
                <div class="service-category">
                    <div class="service-category-title"><i class="bi bi-gem"></i> Specialized Treatment</div>
                    @foreach($specializedServices as $service)
                    <div class="service-item">
                        <span class="service-name">{{ \App\Models\Klinik::SERVICE_NAMES[$service] }}</span>
                        <span class="service-price">Rp {{ number_format($klinik->service_prices[$service] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($bodyServices->isNotEmpty())
                <div class="service-category">
                    <div class="service-category-title"><i class="bi bi-activity"></i> Body Treatment</div>
                    @foreach($bodyServices as $service)
                    <div class="service-item">
                        <span class="service-name">{{ \App\Models\Klinik::SERVICE_NAMES[$service] }}</span>
                        <span class="service-price">Rp {{ number_format($klinik->service_prices[$service] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif
        </div>
                <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="info-block" data-reveal>
                <div class="info-title"><i class="bi bi-geo-alt"></i> Lokasi Klinik</div>
                <div id="map" class="rounded-4"></div>
                <p class="text-muted small mt-3 mb-0">
                    <i class="bi bi-geo-alt me-1 text-terracotta"></i>{{ $klinik->latitude }}, {{ $klinik->longitude }}
                </p>
            </div>

            <div class="info-block" data-reveal style="--reveal-delay: 0.08s;">
                <div class="info-title"><i class="bi bi-person-lines-fill"></i> Kontak</div>
                <div class="d-flex flex-column gap-2">
                    @if($klinik->telepon)
                        <a href="tel:{{ $klinik->telepon }}" class="contact-chip justify-content-center">
                            <i class="bi bi-telephone"></i> {{ $klinik->telepon }}
                        </a>
                    @endif
                    @if($klinik->email)
                        <a href="mailto:{{ $klinik->email }}" class="contact-chip justify-content-center">
                            <i class="bi bi-envelope"></i> {{ $klinik->email }}
                        </a>
                    @endif
                    @if($klinik->website)
                        <a href="{{ $klinik->website }}" target="_blank" class="contact-chip justify-content-center">
                            <i class="bi bi-globe"></i> {{ str_replace(['https://','http://','www.'], '', $klinik->website) }}
                        </a>
                    @endif
                </div>
            </div>

            @if($klinik->instagram || $klinik->facebook || $klinik->twitter)
            <div class="info-block" data-reveal style="--reveal-delay: 0.16s;">
                <div class="info-title"><i class="bi bi-share"></i> Media Sosial</div>
                <div class="d-flex gap-2">
                    @if($klinik->instagram)
                        <a href="https://instagram.com/{{ $klinik->instagram }}" target="_blank" class="social-chip" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                    @endif
                    @if($klinik->facebook)
                        <a href="https://facebook.com/{{ $klinik->facebook }}" target="_blank" class="social-chip" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                    @endif
                    @if($klinik->twitter)
                        <a href="https://twitter.com/{{ $klinik->twitter }}" target="_blank" class="social-chip" title="Twitter / X">
                            <i class="bi bi-twitter"></i>
                        </a>
                    @endif
                </div>
            </div>
            @endif

            <div class="card card-arch p-4 text-center" data-reveal>
                <div class="medallion medallion-terra mx-auto mb-3">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <h5 class="fw-bold" style="font-size: 1.1rem;">Punya Klinik Kecantikan?</h5>
                <p class="text-ink-soft small">Daftarkan klinik Anda dan jangkau lebih banyak pelanggan di Kota Jambi.</p>
                <a href="{{ route('klinik.create') }}" class="btn btn-terracotta w-100">
                    <i class="bi bi-plus-circle me-2"></i> Daftarkan Klinik
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
@php
    // Kategori harga untuk warna marker (konsisten dengan peta):
    // Hemat <= 1jt, Menengah 1jt-2.5jt, Premium > 2.5jt
    $detailEffectiveMax = $klinik->max_price ?: ($klinik->min_price ?: 0);
    if ($detailEffectiveMax > 0 && $detailEffectiveMax <= 1000000) {
        $markerCategory = 'murah';
    } elseif ($detailEffectiveMax > 2500000) {
        $markerCategory = 'mahal';
    } else {
        $markerCategory = 'menengah';
    }
@endphp
<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([{{ $klinik->latitude }}, {{ $klinik->longitude }}], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var icon = L.divIcon({
        className: 'botan-marker marker-{{ $markerCategory }}',
        html: '<div class="marker-pin"><i class="bi bi-flower2"></i></div>',
        iconSize: [38, 44],
        iconAnchor: [19, 40],
        popupAnchor: [0, -42]
    });

    L.marker([{{ $klinik->latitude }}, {{ $klinik->longitude }}], { icon: icon })
        .addTo(map)
        .bindPopup("<div class='popup-title text-center'>{{ $klinik->nama }}</div>");
});
</script>
@endpush

