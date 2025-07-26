@extends('layouts.app')

@section('title', $klinik->nama)

@push('styles')
<style>
    #map {
        height: 300px;
        width: 100%;
    }
    .social-links a {
        text-decoration: none;
        margin-right: 10px;
        color: #0d6efd;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <img src="{{ asset('storage/klinik_photos/' . $klinik->foto) }}" class="card-img-top" alt="{{ $klinik->nama }}">
            <div class="card-body">
                <h4 class="card-title">{{ $klinik->nama }}</h4>

                <div class="mb-3">
                    <h6 class="fw-bold">Alamat:</h6>
                    <p>{{ $klinik->alamat }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold">Jam Operasional:</h6>
                    <p>{{ $klinik->jam_operasional }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold">Kontak:</h6>
                    @if($klinik->telepon)
                        <p>
                            <i class="bi bi-telephone"></i>
                            <a href="tel:{{ $klinik->telepon }}">{{ $klinik->telepon }}</a>
                        </p>
                    @endif

                    @if($klinik->email)
                        <p>
                            <i class="bi bi-envelope"></i>
                            <a href="mailto:{{ $klinik->email }}">{{ $klinik->email }}</a>
                        </p>
                    @endif
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold">Media Sosial:</h6>
                    <div class="social-links">
                        @if($klinik->instagram)
                            <a href="https://instagram.com/{{ $klinik->instagram }}" target="_blank">
                                <i class="bi bi-instagram"></i> Instagram
                            </a>
                        @endif

                        @if($klinik->facebook)
                            <a href="https://facebook.com/{{ $klinik->facebook }}" target="_blank">
                                <i class="bi bi-facebook"></i> Facebook
                            </a>
                        @endif

                        @if($klinik->twitter)
                            <a href="https://twitter.com/{{ $klinik->twitter }}" target="_blank">
                                <i class="bi bi-twitter"></i> Twitter
                            </a>
                        @endif

                        @if($klinik->website)
                            <a href="{{ $klinik->website }}" target="_blank">
                                <i class="bi bi-globe"></i> Website
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Lokasi</h5>
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi peta
    var map = L.map('map').setView([{{ $klinik->latitude }}, {{ $klinik->longitude }}], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Tambah marker
    L.marker([{{ $klinik->latitude }}, {{ $klinik->longitude }}])
        .addTo(map)
        .bindPopup("{{ $klinik->nama }}");
});
</script>
@endpush
