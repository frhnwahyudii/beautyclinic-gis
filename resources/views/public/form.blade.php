@extends('layouts.app')

@section('title', 'Daftarkan Klinik Kecantikan Baru di Kota Jambi')
@section('robots', 'noindex, follow')

@push('styles')
<style>
    #map {
        height: 360px;
        width: 100%;
        border-radius: var(--radius);
    }

    .form-page-hero {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(ellipse 55% 60% at 88% 15%, rgba(201, 123, 90, 0.12), transparent 60%),
            radial-gradient(ellipse 45% 50% at 6% 90%, rgba(139, 160, 126, 0.18), transparent 60%),
            linear-gradient(160deg, var(--cream) 0%, #F3EEDF 100%);
        padding: 3rem 0;
        border-bottom: 1px solid rgba(46, 58, 41, 0.06);
    }

    .form-page-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='56' height='56'%3E%3Cg fill='none' stroke='%2354684A' stroke-opacity='0.06'%3E%3Cpath d='M28 4c6 8 6 16 0 24S22 52 28 60'/%3E%3Cpath d='M28 4c-6 8-6 16 0 24s6 20 0 28'/%3E%3Ccircle cx='28' cy='30' r='3'/%3E%3C/g%3E%3C/svg%3E");
    }

    .form-page-hero .container { position: relative; z-index: 2; }

    .price-prefix {
        color: var(--muted);
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
    }

    .sticky-submit-bar {
        position: sticky;
        bottom: 0;
        z-index: 1020;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border-top: 1px solid rgba(46, 58, 41, 0.08);
        padding: 1rem 0;
        box-shadow: 0 -8px 30px rgba(46, 58, 41, 0.08);
    }

    .file-upload-box {
        border: 2px dashed var(--sage-300);
        border-radius: var(--radius);
        background: var(--sage-050);
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all var(--transition);
    }

    .file-upload-box:hover {
        border-color: var(--sage-600);
        background: var(--sage-100);
    }

    .coord-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: var(--sage-100);
        color: var(--sage-800);
        border-radius: var(--radius-pill);
        padding: 0.35rem 0.9rem;
        font-size: 0.82rem;
        font-weight: 600;
    }

    /* ── Mobile responsive (form-specific) ── */
    @media (max-width: 767.98px) {
        #map { height: 320px; }
        .file-upload-box { padding: 1.5rem; }
    }

    @media (max-width: 575.98px) {
        #map { height: 300px; }
        .file-upload-box { padding: 1.25rem; }
        .file-upload-box .fw-bold { font-size: 0.9rem; }
        .coord-badge { font-size: 0.74rem; padding: 0.3rem 0.75rem; }
    }
</style>
@endpush
@section('content')
<!-- Hero -->
<section class="form-page-hero">
    <div class="container">
        <div class="col-lg-8" data-reveal>
            <a href="{{ route('klinik.map') }}" class="back-link mb-3">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <span class="section-eyebrow mb-3 d-block">Pendaftaran Klinik</span>
            <h1 class="section-title mb-3">Daftarkan Klinik Kecantikan Anda</h1>
            <p class="section-sub mb-0">
                Lengkapi data klinik Anda. Setelah disubmit, data akan ditinjau oleh admin sebelum tampil di peta.
            </p>
        </div>
    </div>
</section>

<div class="container py-4">
    <form action="{{ route('klinik.store') }}" method="POST" enctype="multipart/form-data" id="clinicForm">
        @csrf

        <!-- Anti-bot honeypot: manusia tidak akan mengisi field ini -->
        <div style="position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;" aria-hidden="true">
            <label for="company_website">Jangan isi field ini</label>
            <input type="text" name="company_website" id="company_website" tabindex="-1" autocomplete="off">
        </div>
        <div style="position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;" aria-hidden="true">
            <label for="fax_number">Jangan isi field ini</label>
            <input type="text" name="fax_number" id="fax_number" tabindex="-1" autocomplete="off">
        </div>
        <input type="hidden" name="form_started_at" id="form_started_at" value="">

        <!-- Section 1: Informasi Klinik -->
        <div class="form-section mb-4" data-reveal>
            <div class="form-section-header">
                <div class="form-section-number">1</div>
                <div>
                    <h2 class="form-section-title">Informasi Klinik</h2>
                    <p class="form-section-desc">Data dasar yang akan ditampilkan kepada pengunjung.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <label for="nama" class="form-label">Nama Klinik <span class="text-terracotta">*</span></label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-buildings"></i>
                        <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama') }}" placeholder="cth: Klinik Cantik Jambi" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="jam_operasional" class="form-label">Jam Operasional <span class="text-terracotta">*</span></label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-clock"></i>
                        <input type="text" class="form-control" id="jam_operasional" name="jam_operasional" value="{{ old('jam_operasional') }}"
                               placeholder="Contoh: Senin-Jumat: 08:00-17:00" required>
                    </div>
                </div>

                <div class="col-12">
                    <label for="alamat" class="form-label">Alamat Lengkap <span class="text-terracotta">*</span></label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-geo-alt"></i>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Tulis alamat lengkap klinik" required>{{ old('alamat') }}</textarea>
                    </div>
                </div>

                <div class="col-12">
                    <label for="deskripsi" class="form-label">Deskripsi Klinik</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-card-text"></i>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi singkat tentang klinik Anda">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>
                                <div class="col-12">
                    <label class="form-label d-block">Foto Klinik <span class="text-terracotta">*</span></label>
                    <label class="file-upload-box w-100" for="foto">
                        <i class="bi bi-image text-sage" style="font-size: 2.2rem;"></i>
                        <div class="fw-bold text-sage mt-2">Klik untuk mengunggah foto klinik</div>
                        <small class="text-muted">Format: JPG / PNG, maksimal 2MB</small>
                        <input type="file" class="d-none" id="foto" name="foto" accept="image/*" required onchange="updateFileName(this)">
                    </label>
                    <div id="fotoName" class="form-text mt-2 d-none">
                        <i class="bi bi-check-circle text-success me-1"></i><span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Lokasi & Harga -->
        <div class="form-section mb-4" data-reveal>
            <div class="form-section-header">
                <div class="form-section-number">2</div>
                <div>
                    <h2 class="form-section-title">Lokasi & Rentang Harga</h2>
                    <p class="form-section-desc">Klik pada peta untuk menandai lokasi klinik, lalu tentukan rentang harga layanan.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <label class="form-label">Tandai Lokasi di Peta <span class="text-terracotta">*</span></label>
                    <div id="map"></div>
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <span class="coord-badge"><i class="bi bi-geo-alt"></i> Lat: <span id="latDisplay">-</span></span>
                        <span class="coord-badge"><i class="bi bi-geo-alt-fill"></i> Lng: <span id="lngDisplay">-</span></span>
                        <small class="text-muted align-self-center">Klik peta untuk menempatkan penanda lokasi</small>
                    </div>
                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}" required>
                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}" required>
                </div>

                <div class="col-md-6">
                    <label for="min_price" class="form-label">Harga Minimum (Rp) <span class="text-terracotta">*</span></label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-currency-dollar"></i>
                        <input type="number" class="form-control" id="min_price" name="min_price"
                               value="{{ old('min_price') }}" min="0" step="1000" placeholder="100000" required>
                    </div>
                    <div class="form-text">Harga terendah untuk layanan yang tersedia</div>
                </div>

                <div class="col-md-6">
                    <label for="max_price" class="form-label">Harga Maksimum (Rp)</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-currency-dollar"></i>
                        <input type="number" class="form-control" id="max_price" name="max_price"
                               value="{{ old('max_price') }}" min="0" step="1000" placeholder="5000000">
                    </div>
                    <div class="form-text">Harga tertinggi (opsional jika harga tetap)</div>
                </div>
            </div>
        </div>
                <!-- Section 3: Layanan & Harga -->
        <div class="form-section mb-4" data-reveal>
            <div class="form-section-header">
                <div class="form-section-number">3</div>
                <div>
                    <h2 class="form-section-title">Layanan & Harga</h2>
                    <p class="form-section-desc">Centang layanan yang tersedia dan isi harga per layanan (dalam Rupiah).</p>
                </div>
            </div>

            <div class="mb-4">
                <div class="service-category-title mb-3" style="font-family: var(--font-display); font-weight: 600; font-size: 1.05rem; color: var(--sage-900);">
                    <i class="bi bi-flower1 text-terracotta"></i> Perawatan Wajah Dasar
                </div>
                <div class="row g-2">
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="facial_basic" id="facial_basic"
                                {{ in_array('facial_basic', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Facial Basic</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[facial_basic]" placeholder="Harga" value="{{ old('prices.facial_basic') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="facial_acne" id="facial_acne"
                                {{ in_array('facial_acne', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Facial Acne</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[facial_acne]" placeholder="Harga" value="{{ old('prices.facial_acne') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="facial_brightening" id="facial_brightening"
                                {{ in_array('facial_brightening', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Facial Brightening</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[facial_brightening]" placeholder="Harga" value="{{ old('prices.facial_brightening') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="blackhead_removal" id="blackhead_removal"
                                {{ in_array('blackhead_removal', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Blackhead Removal</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[blackhead_removal]" placeholder="Harga" value="{{ old('prices.blackhead_removal') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="hydrafacial" id="hydrafacial"
                                {{ in_array('hydrafacial', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">HydraFacial</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[hydrafacial]" placeholder="Harga" value="{{ old('prices.hydrafacial') }}">
                        </label>
                    </div>
                </div>
            </div>
                        <div class="mb-4">
                <div class="service-category-title mb-3" style="font-family: var(--font-display); font-weight: 600; font-size: 1.05rem; color: var(--sage-900);">
                    <i class="bi bi-droplet text-terracotta"></i> Peeling Treatment
                </div>
                <div class="row g-2">
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="chemical_peel" id="chemical_peel"
                                {{ in_array('chemical_peel', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Chemical Peeling</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[chemical_peel]" placeholder="Harga" value="{{ old('prices.chemical_peel') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="carbon_peel" id="carbon_peel"
                                {{ in_array('carbon_peel', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Carbon Peel</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[carbon_peel]" placeholder="Harga" value="{{ old('prices.carbon_peel') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="milk_peel" id="milk_peel"
                                {{ in_array('milk_peel', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Milk Peel</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[milk_peel]" placeholder="Harga" value="{{ old('prices.milk_peel') }}">
                        </label>
                    </div>
                </div>
            </div>
                        <div class="mb-4">
                <div class="service-category-title mb-3" style="font-family: var(--font-display); font-weight: 600; font-size: 1.05rem; color: var(--sage-900);">
                    <i class="bi bi-stars text-terracotta"></i> Laser Treatment
                </div>
                <div class="row g-2">
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="laser_rejuvenation" id="laser_rejuvenation"
                                {{ in_array('laser_rejuvenation', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Laser Rejuvenation</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[laser_rejuvenation]" placeholder="Harga" value="{{ old('prices.laser_rejuvenation') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="laser_acne" id="laser_acne"
                                {{ in_array('laser_acne', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Laser Acne</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[laser_acne]" placeholder="Harga" value="{{ old('prices.laser_acne') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="ipl_photorejuvenation" id="ipl_photorejuvenation"
                                {{ in_array('ipl_photorejuvenation', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">IPL Photorejuvenation</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[ipl_photorejuvenation]" placeholder="Harga" value="{{ old('prices.ipl_photorejuvenation') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="laser_hair_removal" id="laser_hair_removal"
                                {{ in_array('laser_hair_removal', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Laser Hair Removal</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[laser_hair_removal]" placeholder="Harga" value="{{ old('prices.laser_hair_removal') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="co2_laser" id="co2_laser"
                                {{ in_array('co2_laser', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">CO2 Laser</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[co2_laser]" placeholder="Harga" value="{{ old('prices.co2_laser') }}">
                        </label>
                    </div>
                </div>
            </div>
                        <div class="mb-4">
                <div class="service-category-title mb-3" style="font-family: var(--font-display); font-weight: 600; font-size: 1.05rem; color: var(--sage-900);">
                    <i class="bi bi-syringe text-terracotta"></i> Injeksi & Estetik
                </div>
                <div class="row g-2">
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="botox" id="botox"
                                {{ in_array('botox', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Botox</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[botox]" placeholder="Harga" value="{{ old('prices.botox') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="filler" id="filler"
                                {{ in_array('filler', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Dermal Filler</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[filler]" placeholder="Harga" value="{{ old('prices.filler') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="skinbooster" id="skinbooster"
                                {{ in_array('skinbooster', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Skin Booster</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[skinbooster]" placeholder="Harga" value="{{ old('prices.skinbooster') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="vitamin_injection" id="vitamin_injection"
                                {{ in_array('vitamin_injection', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Vitamin Injection</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[vitamin_injection]" placeholder="Harga" value="{{ old('prices.vitamin_injection') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="whitening_injection" id="whitening_injection"
                                {{ in_array('whitening_injection', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Whitening Injection</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[whitening_injection]" placeholder="Harga" value="{{ old('prices.whitening_injection') }}">
                        </label>
                    </div>
                </div>
            </div>
                        <div class="mb-4">
                <div class="service-category-title mb-3" style="font-family: var(--font-display); font-weight: 600; font-size: 1.05rem; color: var(--sage-900);">
                    <i class="bi bi-magic text-terracotta"></i> Advanced Treatment
                </div>
                <div class="row g-2">
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="microneedling" id="microneedling"
                                {{ in_array('microneedling', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Microneedling</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[microneedling]" placeholder="Harga" value="{{ old('prices.microneedling') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="rf_microneedling" id="rf_microneedling"
                                {{ in_array('rf_microneedling', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">RF Microneedling</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[rf_microneedling]" placeholder="Harga" value="{{ old('prices.rf_microneedling') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="hifu" id="hifu"
                                {{ in_array('hifu', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">HIFU</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[hifu]" placeholder="Harga" value="{{ old('prices.hifu') }}">
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="service-category-title mb-3" style="font-family: var(--font-display); font-weight: 600; font-size: 1.05rem; color: var(--sage-900);">
                    <i class="bi bi-gem text-terracotta"></i> Specialized Treatment
                </div>
                <div class="row g-2">
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="prp_therapy" id="prp_therapy"
                                {{ in_array('prp_therapy', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">PRP Therapy</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[prp_therapy]" placeholder="Harga" value="{{ old('prices.prp_therapy') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="thread_lift" id="thread_lift"
                                {{ in_array('thread_lift', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Thread Lift</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[thread_lift]" placeholder="Harga" value="{{ old('prices.thread_lift') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="cryotherapy" id="cryotherapy"
                                {{ in_array('cryotherapy', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Cryotherapy</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[cryotherapy]" placeholder="Harga" value="{{ old('prices.cryotherapy') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="sclerotherapy" id="sclerotherapy"
                                {{ in_array('sclerotherapy', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Sclerotherapy</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[sclerotherapy]" placeholder="Harga" value="{{ old('prices.sclerotherapy') }}">
                        </label>
                    </div>
                </div>
            </div>
                        <div class="mb-4">
                <div class="service-category-title mb-3" style="font-family: var(--font-display); font-weight: 600; font-size: 1.05rem; color: var(--sage-900);">
                    <i class="bi bi-activity text-terracotta"></i> Body Treatment
                </div>
                <div class="row g-2">
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="body_contouring" id="body_contouring"
                                {{ in_array('body_contouring', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Body Contouring</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[body_contouring]" placeholder="Harga" value="{{ old('prices.body_contouring') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="cavitation" id="cavitation"
                                {{ in_array('cavitation', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Cavitation</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[cavitation]" placeholder="Harga" value="{{ old('prices.cavitation') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="radiofrequency" id="radiofrequency"
                                {{ in_array('radiofrequency', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">Radiofrequency</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[radiofrequency]" placeholder="Harga" value="{{ old('prices.radiofrequency') }}">
                        </label>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12">
                        <label class="service-tile">
                            <input class="form-check-input" type="checkbox" name="services[]" value="coolsculpting" id="coolsculpting"
                                {{ in_array('coolsculpting', old('services', [])) ? 'checked' : '' }}>
                            <span class="service-tile-name">CoolSculpting</span>
                            <span class="price-prefix">Rp</span>
                            <input type="number" class="price-input" name="prices[coolsculpting]" placeholder="Harga" value="{{ old('prices.coolsculpting') }}">
                        </label>
                    </div>
                </div>
            </div>
        </div>
                <!-- Section 4: Kontak & Media Sosial -->
        <div class="form-section mb-4" data-reveal>
            <div class="form-section-header">
                <div class="form-section-number">4</div>
                <div>
                    <h2 class="form-section-title">Kontak & Media Sosial</h2>
                    <p class="form-section-desc">Informasi agar pengunjung dapat menghubungi klinik Anda (opsional).</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <label for="telepon" class="form-label">Nomor Telepon</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-telephone"></i>
                        <input type="text" class="form-control" id="telepon" name="telepon" value="{{ old('telepon') }}"
                               placeholder="Contoh: 0741-xxxxxx">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                               placeholder="contoh@email.com">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="website" class="form-label">Website</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-globe"></i>
                        <input type="url" class="form-control" id="website" name="website" value="{{ old('website') }}"
                               placeholder="https://www.example.com">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="instagram" class="form-label">Instagram</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-instagram"></i>
                        <input type="text" class="form-control" id="instagram" name="instagram" value="{{ old('instagram') }}"
                               placeholder="@username">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="facebook" class="form-label">Facebook</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-facebook"></i>
                        <input type="text" class="form-control" id="facebook" name="facebook" value="{{ old('facebook') }}"
                               placeholder="Username Facebook">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="twitter" class="form-label">Twitter</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-twitter"></i>
                        <input type="text" class="form-control" id="twitter" name="twitter" value="{{ old('twitter') }}"
                               placeholder="@username">
                    </div>
                </div>
            </div>
        </div>
                <!-- Submit -->
        <div class="sticky-submit-bar mt-4">
            <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
                <small class="text-muted">
                    <i class="bi bi-shield-check text-sage me-1"></i> Data akan ditinjau admin sebelum ditampilkan
                </small>
                <div class="d-flex gap-2">
                    <button type="reset" class="btn btn-outline-sage">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-terracotta btn-lg px-5">
                        <i class="bi bi-send me-2"></i> Kirim Data Klinik
                    </button>
                </div>
            </div>
        </div>
        @if(config('services.turnstile.site_key'))
            <div class="text-center mb-3">
                <div class="cf-turnstile d-inline-block" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="light"></div>
            </div>
        @endif
    </form>
</div>
@endsection

@push('scripts')
@if(config('services.turnstile.site_key'))
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Catat waktu render form untuk anti-bot time-trap
    document.getElementById('form_started_at').value = Math.floor(Date.now() / 1000);

    // Highlight selected service tiles
    document.querySelectorAll('.service-tile').forEach(function(tile) {
        var checkbox = tile.querySelector('input[type="checkbox"]');
        var sync = function() {
            if (checkbox.checked) {
                tile.classList.add('has-checked');
            } else {
                tile.classList.remove('has-checked');
            }
        };
        sync();
        checkbox.addEventListener('change', sync);
    });

    // Show selected file name
    var fotoInput = document.getElementById('foto');
    var fotoNameBox = document.getElementById('fotoName');
    if (fotoInput && fotoNameBox) {
        fotoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fotoNameBox.classList.remove('d-none');
                fotoNameBox.querySelector('span').textContent = this.files[0].name;
            }
        });
    }

    // Inisialisasi peta
    var map = L.map('map').setView([-1.6096639, 103.6131639], 13);
    var marker = null;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var pinIcon = L.divIcon({
        className: 'botan-marker marker-menengah',
        html: '<div class="marker-pin"><i class="bi bi-flower2"></i></div>',
        iconSize: [38, 44],
        iconAnchor: [19, 40],
        popupAnchor: [0, -42]
    });

    function setCoords(latlng) {
        document.getElementById('latitude').value = latlng.lat.toFixed(6);
        document.getElementById('longitude').value = latlng.lng.toFixed(6);
        document.getElementById('latDisplay').textContent = latlng.lat.toFixed(6);
        document.getElementById('lngDisplay').textContent = latlng.lng.toFixed(6);
    }

    // Fungsi untuk menambah/memindahkan marker
    map.on('click', function(e) {
        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng, { icon: pinIcon, draggable: true }).addTo(map);
            marker.on('dragend', function(ev) {
                setCoords(ev.target.getLatLng());
            });
        }
        setCoords(e.latlng);
    });

    // Restore old values if validation fails
    var oldLat = document.getElementById('latitude').value;
    var oldLng = document.getElementById('longitude').value;
    if (oldLat && oldLng) {
        var saved = L.latLng(parseFloat(oldLat), parseFloat(oldLng));
        marker = L.marker(saved, { icon: pinIcon, draggable: true }).addTo(map);
        map.setView(saved, 15);
        marker.on('dragend', function(ev) {
            setCoords(ev.target.getLatLng());
        });
        setCoords(saved);
    }
});
</script>
@endpush









            </div>
        </div>

