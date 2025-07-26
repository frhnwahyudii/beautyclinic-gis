@extends('layouts.app')

@section('title', 'Edit Klinik - ' . $klinik->nama)

@push('styles')
<style>
    #map {
        height: 400px;
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Edit Klinik</h5>
                <form action="{{ route('admin.kliniks.update', $klinik) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Klinik *</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama', $klinik->nama) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat Lengkap *</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required>{{ old('alamat', $klinik->alamat) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto Klinik</label>
                                @if($klinik->foto)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/klinik_photos/' . $klinik->foto) }}" alt="{{ $klinik->nama }}"
                                             class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto</small>
                            </div>

                            <div class="mb-3">
                                <label for="jam_operasional" class="form-label">Jam Operasional *</label>
                                <input type="text" class="form-control" id="jam_operasional" name="jam_operasional"
                                       value="{{ old('jam_operasional', $klinik->jam_operasional) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Lokasi di Peta *</label>
                                <div id="map"></div>
                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $klinik->latitude) }}" required>
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $klinik->longitude) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telepon" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="telepon" name="telepon" value="{{ old('telepon', $klinik->telepon) }}">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $klinik->email) }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="instagram" class="form-label">Instagram</label>
                                <input type="text" class="form-control" id="instagram" name="instagram"
                                       value="{{ old('instagram', $klinik->instagram) }}" placeholder="Username Instagram">
                            </div>

                            <div class="mb-3">
                                <label for="facebook" class="form-label">Facebook</label>
                                <input type="text" class="form-control" id="facebook" name="facebook"
                                       value="{{ old('facebook', $klinik->facebook) }}" placeholder="Username Facebook">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="twitter" class="form-label">Twitter</label>
                                <input type="text" class="form-control" id="twitter" name="twitter"
                                       value="{{ old('twitter', $klinik->twitter) }}" placeholder="Username Twitter">
                            </div>

                            <div class="mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="website" name="website"
                                       value="{{ old('website', $klinik->website) }}" placeholder="https://www.example.com">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" {{ $klinik->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $klinik->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $klinik->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.kliniks.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi peta
    var map = L.map('map').setView([{{ $klinik->latitude }}, {{ $klinik->longitude }}], 13);
    var marker = L.marker([{{ $klinik->latitude }}, {{ $klinik->longitude }}]).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Fungsi untuk memindahkan marker
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
    });
});
</script>
@endpush
