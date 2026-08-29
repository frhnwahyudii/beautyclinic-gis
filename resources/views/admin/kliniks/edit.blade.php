@extends('layouts.admin')

@section('title', 'Edit Klinik — SIG Klinik Kecantikan')
@section('page-title', 'Edit Klinik')
@section('page-sub', 'Perbarui data klinik: {{ $klinik->nama }}')

@push('styles')
<style>
    #map {
        height: 360px;
        width: 100%;
        border-radius: var(--radius);
    }

    .img-preview {
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        margin-bottom: 0.75rem;
    }

    .price-prefix {
        color: var(--muted);
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
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

    /* ── Mobile responsive (admin edit) ── */
    @media (max-width: 575.98px) {
        .admin-edit-header .btn { width: 100%; }
        .admin-edit-actions { flex-direction: column-reverse; }
        .admin-edit-actions .btn { width: 100%; }
        #map { height: 300px; }
        .coord-badge { font-size: 0.74rem; padding: 0.3rem 0.75rem; }
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 admin-edit-header">
    <div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h1 class="admin-page-title">Edit Klinik</h1>
            @if($klinik->status == 'approved')
                <span class="status-pill status-approved">Disetujui</span>
            @elseif($klinik->status == 'pending')
                <span class="status-pill status-pending">Pending</span>
            @else
                <span class="status-pill status-rejected">Ditolak</span>
            @endif
        </div>
        <p class="admin-page-sub mb-0 mt-1">{{ $klinik->nama }}</p>
    </div>
    <a href="{{ route('admin.kliniks.index') }}" class="btn btn-outline-sage">
        <i class="fas fa-arrow-left me-2"></i> Kembali ke Data Klinik
    </a>
</div>

<form action="{{ route('admin.kliniks.update', $klinik) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- Section 1: Informasi Umum -->
    <div class="form-section mb-4" data-reveal>
        <div class="form-section-header">
            <div class="form-section-number">1</div>
            <div>
                <h2 class="form-section-title">Informasi Umum</h2>
                <p class="form-section-desc">Data dasar dan status verifikasi klinik.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <label for="nama" class="form-label">Nama Klinik <span class="text-terracotta">*</span></label>
                <div class="input-icon-wrap">
                    <i class="fas fa-clinic-medical"></i>
                    <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama', $klinik->nama) }}" required>
                </div>
            </div>

            <div class="col-md-6">
                <label for="jam_operasional" class="form-label">Jam Operasional <span class="text-terracotta">*</span></label>
                <div class="input-icon-wrap">
                    <i class="fas fa-clock"></i>
                    <input type="text" class="form-control" id="jam_operasional" name="jam_operasional"
                           value="{{ old('jam_operasional', $klinik->jam_operasional) }}" required
                           placeholder="Contoh: Senin-Jumat, 08:00-17:00">
                </div>
            </div>

            <div class="col-12">
                <label for="alamat" class="form-label">Alamat Lengkap <span class="text-terracotta">*</span></label>
                <div class="input-icon-wrap">
                    <i class="fas fa-map-marker-alt"></i>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required>{{ old('alamat', $klinik->alamat) }}</textarea>
                </div>
            </div>

            <div class="col-12">
                <label for="deskripsi" class="form-label">Deskripsi Klinik</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-align-left"></i>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
                              placeholder="Deskripsi singkat tentang klinik dan layanan yang ditawarkan">{{ old('deskripsi', $klinik->deskripsi) }}</textarea>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Foto Klinik</label>
                @if($klinik->foto_url)
                    <div class="img-preview">
                        <img src="{{ $klinik->foto_url }}" alt="{{ $klinik->nama }}" class="img-fluid w-100" style="max-height: 200px; object-fit: cover;">
                    </div>
                @endif
                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i> Biarkan kosong jika tidak ingin mengubah foto
                </div>
            </div>

            <div class="col-md-6">
                <label for="status" class="form-label">Status Klinik <span class="text-terracotta">*</span></label>
                <select class="form-select" id="status" name="status" required>
                    <option value="pending" {{ old('status', $klinik->status) == 'pending' ? 'selected' : '' }}>Pending (Menunggu Review)</option>
                    <option value="approved" {{ old('status', $klinik->status) == 'approved' ? 'selected' : '' }}>Approved (Disetujui)</option>
                    <option value="rejected" {{ old('status', $klinik->status) == 'rejected' ? 'selected' : '' }}>Rejected (Ditolak)</option>
                </select>
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i> Ubah status sesuai hasil review klinik
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
                <p class="form-section-desc">Klik peta untuk menyesuaikan posisi klinik dan perbarui rentang harga.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <label class="form-label">Lokasi di Peta <span class="text-terracotta">*</span></label>
                <div id="map"></div>
                <div class="d-flex gap-2 flex-wrap mt-3">
                    <span class="coord-badge"><i class="fas fa-map-marker-alt"></i> Lat: <span id="latDisplay">{{ $klinik->latitude }}</span></span>
                    <span class="coord-badge"><i class="fas fa-map-marker"></i> Lng: <span id="lngDisplay">{{ $klinik->longitude }}</span></span>
                    <small class="text-muted align-self-center">Geser penanda atau klik peta untuk mengubah lokasi</small>
                </div>
                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $klinik->latitude) }}" required>
                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $klinik->longitude) }}" required>
            </div>

            <div class="col-md-6">
                <label for="min_price" class="form-label">Harga Minimum (Rp) <span class="text-terracotta">*</span></label>
                <div class="input-icon-wrap">
                    <i class="fas fa-currency-dollar"></i>
                    <input type="number" class="form-control" id="min_price" name="min_price"
                           value="{{ old('min_price', $klinik->min_price) }}" min="0" step="1000"
                           placeholder="100000" required>
                </div>
                <div class="form-text">Harga terendah untuk layanan yang tersedia</div>
            </div>

            <div class="col-md-6">
                <label for="max_price" class="form-label">Harga Maksimum (Rp)</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-currency-dollar"></i>
                    <input type="number" class="form-control" id="max_price" name="max_price"
                           value="{{ old('max_price', $klinik->max_price) }}" min="0" step="1000"
                           placeholder="5000000">
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
                <p class="form-section-desc">Perbarui layanan yang tersedia beserta harga per layanan.</p>
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
                            {{ in_array('facial_basic', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Facial Basic</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[facial_basic]" placeholder="Harga" value="{{ old('prices.facial_basic', $klinik->service_prices['facial_basic'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="facial_acne" id="facial_acne"
                            {{ in_array('facial_acne', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Facial Acne</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[facial_acne]" placeholder="Harga" value="{{ old('prices.facial_acne', $klinik->service_prices['facial_acne'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="facial_brightening" id="facial_brightening"
                            {{ in_array('facial_brightening', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Facial Brightening</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[facial_brightening]" placeholder="Harga" value="{{ old('prices.facial_brightening', $klinik->service_prices['facial_brightening'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="blackhead_removal" id="blackhead_removal"
                            {{ in_array('blackhead_removal', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Blackhead Removal</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[blackhead_removal]" placeholder="Harga" value="{{ old('prices.blackhead_removal', $klinik->service_prices['blackhead_removal'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="hydrafacial" id="hydrafacial"
                            {{ in_array('hydrafacial', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">HydraFacial</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[hydrafacial]" placeholder="Harga" value="{{ old('prices.hydrafacial', $klinik->service_prices['hydrafacial'] ?? '') }}">
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
                            {{ in_array('chemical_peel', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Chemical Peeling</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[chemical_peel]" placeholder="Harga" value="{{ old('prices.chemical_peel', $klinik->service_prices['chemical_peel'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="carbon_peel" id="carbon_peel"
                            {{ in_array('carbon_peel', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Carbon Peel</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[carbon_peel]" placeholder="Harga" value="{{ old('prices.carbon_peel', $klinik->service_prices['carbon_peel'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="milk_peel" id="milk_peel"
                            {{ in_array('milk_peel', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Milk Peel</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[milk_peel]" placeholder="Harga" value="{{ old('prices.milk_peel', $klinik->service_prices['milk_peel'] ?? '') }}">
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
                            {{ in_array('laser_rejuvenation', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Laser Rejuvenation</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[laser_rejuvenation]" placeholder="Harga" value="{{ old('prices.laser_rejuvenation', $klinik->service_prices['laser_rejuvenation'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="laser_acne" id="laser_acne"
                            {{ in_array('laser_acne', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Laser Acne</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[laser_acne]" placeholder="Harga" value="{{ old('prices.laser_acne', $klinik->service_prices['laser_acne'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="ipl_photorejuvenation" id="ipl_photorejuvenation"
                            {{ in_array('ipl_photorejuvenation', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">IPL Photorejuvenation</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[ipl_photorejuvenation]" placeholder="Harga" value="{{ old('prices.ipl_photorejuvenation', $klinik->service_prices['ipl_photorejuvenation'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="laser_hair_removal" id="laser_hair_removal"
                            {{ in_array('laser_hair_removal', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Laser Hair Removal</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[laser_hair_removal]" placeholder="Harga" value="{{ old('prices.laser_hair_removal', $klinik->service_prices['laser_hair_removal'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="co2_laser" id="co2_laser"
                            {{ in_array('co2_laser', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">CO2 Laser</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[co2_laser]" placeholder="Harga" value="{{ old('prices.co2_laser', $klinik->service_prices['co2_laser'] ?? '') }}">
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
                            {{ in_array('botox', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Botox</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[botox]" placeholder="Harga" value="{{ old('prices.botox', $klinik->service_prices['botox'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="filler" id="filler"
                            {{ in_array('filler', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Dermal Filler</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[filler]" placeholder="Harga" value="{{ old('prices.filler', $klinik->service_prices['filler'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="skinbooster" id="skinbooster"
                            {{ in_array('skinbooster', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Skin Booster</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[skinbooster]" placeholder="Harga" value="{{ old('prices.skinbooster', $klinik->service_prices['skinbooster'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="vitamin_injection" id="vitamin_injection"
                            {{ in_array('vitamin_injection', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Vitamin Injection</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[vitamin_injection]" placeholder="Harga" value="{{ old('prices.vitamin_injection', $klinik->service_prices['vitamin_injection'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="whitening_injection" id="whitening_injection"
                            {{ in_array('whitening_injection', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Whitening Injection</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[whitening_injection]" placeholder="Harga" value="{{ old('prices.whitening_injection', $klinik->service_prices['whitening_injection'] ?? '') }}">
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
                            {{ in_array('microneedling', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Microneedling</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[microneedling]" placeholder="Harga" value="{{ old('prices.microneedling', $klinik->service_prices['microneedling'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="rf_microneedling" id="rf_microneedling"
                            {{ in_array('rf_microneedling', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">RF Microneedling</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[rf_microneedling]" placeholder="Harga" value="{{ old('prices.rf_microneedling', $klinik->service_prices['rf_microneedling'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="hifu" id="hifu"
                            {{ in_array('hifu', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">HIFU</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[hifu]" placeholder="Harga" value="{{ old('prices.hifu', $klinik->service_prices['hifu'] ?? '') }}">
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
                            {{ in_array('prp_therapy', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">PRP Therapy</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[prp_therapy]" placeholder="Harga" value="{{ old('prices.prp_therapy', $klinik->service_prices['prp_therapy'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="thread_lift" id="thread_lift"
                            {{ in_array('thread_lift', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Thread Lift</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[thread_lift]" placeholder="Harga" value="{{ old('prices.thread_lift', $klinik->service_prices['thread_lift'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="cryotherapy" id="cryotherapy"
                            {{ in_array('cryotherapy', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Cryotherapy</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[cryotherapy]" placeholder="Harga" value="{{ old('prices.cryotherapy', $klinik->service_prices['cryotherapy'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="sclerotherapy" id="sclerotherapy"
                            {{ in_array('sclerotherapy', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Sclerotherapy</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[sclerotherapy]" placeholder="Harga" value="{{ old('prices.sclerotherapy', $klinik->service_prices['sclerotherapy'] ?? '') }}">
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
                            {{ in_array('body_contouring', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Body Contouring</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[body_contouring]" placeholder="Harga" value="{{ old('prices.body_contouring', $klinik->service_prices['body_contouring'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="cavitation" id="cavitation"
                            {{ in_array('cavitation', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Cavitation</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[cavitation]" placeholder="Harga" value="{{ old('prices.cavitation', $klinik->service_prices['cavitation'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="radiofrequency" id="radiofrequency"
                            {{ in_array('radiofrequency', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">Radiofrequency</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[radiofrequency]" placeholder="Harga" value="{{ old('prices.radiofrequency', $klinik->service_prices['radiofrequency'] ?? '') }}">
                    </label>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <label class="service-tile">
                        <input class="form-check-input" type="checkbox" name="services[]" value="coolsculpting" id="coolsculpting"
                            {{ in_array('coolsculpting', old('services', $klinik->services ?? [])) ? 'checked' : '' }}>
                        <span class="service-tile-name">CoolSculpting</span>
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="price-input" name="prices[coolsculpting]" placeholder="Harga" value="{{ old('prices.coolsculpting', $klinik->service_prices['coolsculpting'] ?? '') }}">
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
                <p class="form-section-desc">Informasi kontak klinik yang ditampilkan di halaman detail.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <label for="telepon" class="form-label">Nomor Telepon</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-phone"></i>
                    <input type="text" class="form-control" id="telepon" name="telepon" value="{{ old('telepon', $klinik->telepon) }}"
                           placeholder="Contoh: 0741-xxxxxx">
                </div>
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $klinik->email) }}"
                           placeholder="contoh@email.com">
                </div>
            </div>

            <div class="col-md-6">
                <label for="website" class="form-label">Website</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-globe"></i>
                    <input type="url" class="form-control" id="website" name="website" value="{{ old('website', $klinik->website) }}"
                           placeholder="https://www.example.com">
                </div>
            </div>

            <div class="col-md-6">
                <label for="instagram" class="form-label">Instagram</label>
                <div class="input-icon-wrap">
                    <i class="fab fa-instagram"></i>
                    <input type="text" class="form-control" id="instagram" name="instagram"
                           value="{{ old('instagram', $klinik->instagram) }}" placeholder="@username">
                </div>
            </div>

            <div class="col-md-6">
                <label for="facebook" class="form-label">Facebook</label>
                <div class="input-icon-wrap">
                    <i class="fab fa-facebook"></i>
                    <input type="text" class="form-control" id="facebook" name="facebook"
                           value="{{ old('facebook', $klinik->facebook) }}" placeholder="Username Facebook">
                </div>
            </div>

            <div class="col-md-6">
                <label for="twitter" class="form-label">Twitter</label>
                <div class="input-icon-wrap">
                    <i class="fab fa-twitter"></i>
                    <input type="text" class="form-control" id="twitter" name="twitter" value="{{ old('twitter', $klinik->twitter) }}"
                           placeholder="@username">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4 admin-edit-actions">
        <a href="{{ route('admin.kliniks.index') }}" class="btn btn-outline-sage">
            <i class="fas fa-arrow-left me-2"></i> Batal
        </a>
        <button type="submit" class="btn btn-sage btn-lg px-5">
            <i class="fas fa-save me-2"></i> Simpan Perubahan
        </button>
    </div>
</form>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Highlight selected service tiles
    document.querySelectorAll('.service-tile').forEach(function(tile) {
        var checkbox = tile.querySelector('input[type="checkbox"]');
        var sync = function() {
            tile.classList.toggle('has-checked', checkbox.checked);
        };
        sync();
        checkbox.addEventListener('change', sync);
    });

    // Inisialisasi peta
    var startLat = {{ $klinik->latitude }};
    var startLng = {{ $klinik->longitude }};

    var map = L.map('map').setView([startLat, startLng], 14);

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

    // Existing marker (draggable)
    var marker = L.marker([startLat, startLng], { icon: pinIcon, draggable: true }).addTo(map);
    marker.on('dragend', function(ev) {
        setCoords(ev.target.getLatLng());
    });

    // Fungsi untuk memindahkan marker
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        setCoords(e.latlng);
    });

    setCoords(marker.getLatLng());
});
</script>
@endpush









