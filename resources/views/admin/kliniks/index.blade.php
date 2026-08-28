@extends('layouts.admin')

@section('title', 'Manajemen Klinik — SIG Klinik Kecantikan')
@section('page-title', 'Manajemen Klinik')
@section('page-sub', 'Kelola, verifikasi, dan perbarui data klinik')

@push('styles')
<style>
    @media (max-width: 575.98px) {
        .admin-toolbar-search { flex-direction: column; align-items: stretch !important; }
        .admin-toolbar-search .form-select { max-width: 100% !important; }
        .admin-toolbar-search .input-icon-wrap { min-width: 100% !important; }
        .admin-toolbar-search .btn { width: 100%; }
        .admin-index-header .btn { width: 100%; }
    }
</style>
@endpush

@section('content')
<!-- Toolbar -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 admin-index-header">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h1 class="admin-page-title">Data Klinik Kecantikan</h1>
            <span class="badge bg-sage-soft">{{ $kliniks->total() ?? 0 }} Total</span>
        </div>
        <p class="admin-page-sub mb-0 mt-1">Daftar seluruh klinik beserta status verifikasinya</p>
    </div>
    <a href="{{ route('klinik.create') }}" class="btn btn-terracotta">
        <i class="fas fa-plus me-2"></i> Tambah Klinik Baru
    </a>
</div>

<div id="loading-indicator" class="text-center py-4" style="display: none;">
    <div class="spinner-border text-sage" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-2 text-muted">Memuat data klinik...</p>
</div>

<!-- Search & Filter -->
<div class="card shadow-soft border-0 mb-4" data-reveal>
    <div class="card-body p-3">
        <form method="GET" class="d-flex flex-wrap gap-2 align-items-center admin-toolbar-search">
            <div class="input-icon-wrap flex-grow-1" style="min-width: 220px;">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" placeholder="Cari nama, alamat, telepon, email..." name="search" value="{{ request('search') }}">
            </div>
            <select name="status" class="form-select" style="max-width: 180px;">
                <option value="">Semua Status</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button class="btn btn-sage px-3" type="submit">
                <i class="fas fa-search me-1"></i> Cari
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('admin.kliniks.index') }}" class="btn btn-outline-sage">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </form>
    </div>
</div>

@if(request('search') || request('status'))
<div class="card shadow-soft border-0 mb-4">
    <div class="card-body py-3">
        <small class="text-muted">
            <i class="fas fa-filter me-1 text-terracotta"></i> Filter aktif:
            @if(request('search'))
                <span class="badge bg-sage-soft ms-1">Pencarian: "{{ request('search') }}"</span>
            @endif
            @if(request('status'))
                <span class="badge bg-warning-soft ms-1">Status: {{ ucfirst(request('status')) }}</span>
            @endif
            <span class="ms-2">Menampilkan {{ $kliniks->count() }} dari {{ $kliniks->total() }} hasil</span>
        </small>
    </div>
</div>
@endif
<div id="data-container">
    @if($kliniks && $kliniks->count() > 0)
    <div class="card shadow-soft border-0" data-reveal>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col" class="text-center">No</th>
                        <th scope="col">Klinik</th>
                        <th scope="col">Lokasi</th>
                        <th scope="col">Layanan</th>
                        <th scope="col">Harga</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-center">Tanggal</th>
                        <th scope="col" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kliniks as $index => $klinik)
                    <tr>
                        <td class="text-center">
                            <span class="fw-bold text-sage">{{ $kliniks->firstItem() + $index }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($klinik->foto)
                                    <img src="{{ $klinik->foto_url }}" alt="Foto {{ $klinik->nama }}" class="avatar-thumb">
                                @else
                                    <div class="avatar-fallback">
                                        <i class="fas fa-clinic-medical"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark">{{ $klinik->nama }}</div>
                                    @if($klinik->telepon)
                                        <small class="text-muted d-block">
                                            <i class="fas fa-phone fa-xs me-1"></i>{{ $klinik->telepon }}
                                        </small>
                                    @endif
                                    @if($klinik->email)
                                        <small class="text-muted d-block">
                                            <i class="fas fa-envelope fa-xs me-1"></i>{{ Str::limit($klinik->email, 22) }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="text-dark">{{ Str::limit($klinik->alamat, 32) }}</div>
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt fa-xs me-1"></i>
                                    {{ number_format($klinik->latitude, 3) }}, {{ number_format($klinik->longitude, 3) }}
                                </small>
                            </div>
                        </td>
                                                <td>
                            @if($klinik->services && count($klinik->services) > 0)
                                @php
                                    $services = collect($klinik->services);
                                    $totalServices = $services->count();
                                    $firstService = $services->first();
                                @endphp
                                <div>
                                    <div class="fw-semibold" style="font-size: .9rem;">
                                        {{ \App\Models\Klinik::SERVICE_NAMES[$firstService] ?? str_replace('_', ' ', Str::title($firstService)) }}
                                        @if($totalServices > 1)
                                            <span class="text-muted">+{{ $totalServices - 1 }} lainnya</span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        @php
                                            $categoryKeys = ['facial_basic' => 'Facial', 'chemical_peel' => 'Peeling', 'laser_rejuvenation' => 'Laser', 'botox' => 'Injeksi', 'microneedling' => 'Advanced', 'body_contouring' => 'Body'];
                                            $activeCats = collect($categoryKeys)->filter(function($label, $key) use ($services) {
                                                return $services->contains($key);
                                            })->take(2);
                                        @endphp
                                        @foreach($activeCats as $label)
                                            <span class="badge bg-sage-soft">{{ $label }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <small class="text-muted">Belum ada layanan</small>
                            @endif
                        </td>
                        <td>
                            @if($klinik->min_price)
                                <div class="fw-bold" style="color: var(--terracotta-strong);">
                                    Rp {{ number_format($klinik->min_price, 0, ',', '.') }}
                                </div>
                                @if($klinik->max_price && $klinik->max_price != $klinik->min_price)
                                    <small class="text-muted">s/d Rp {{ number_format($klinik->max_price, 0, ',', '.') }}</small>
                                @endif
                            @else
                                <small class="text-muted">Belum ada harga</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($klinik->status == 'pending')
                                <div class="dropdown">
                                    <button type="button" class="status-pill status-pending border-0 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        Pending
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lift">
                                        <li>
                                            <form action="{{ route('admin.kliniks.update-status', $klinik) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="dropdown-item text-success">
                                                    <i class="fas fa-check fa-sm me-2"></i> Setujui
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.kliniks.update-status', $klinik) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-times fa-sm me-2"></i> Tolak
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @elseif($klinik->status == 'approved')
                                <span class="status-pill status-approved">Disetujui</span>
                            @else
                                <span class="status-pill status-rejected">Ditolak</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div>
                                <small class="d-block fw-bold">{{ $klinik->created_at->format('d M Y') }}</small>
                                <small class="text-muted">{{ $klinik->created_at->format('H:i') }}</small>
                            </div>
                        </td>
                                                <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('klinik.show', $klinik) }}"
                                   class="btn btn-sm btn-outline-sage"
                                   title="Lihat Detail"
                                   target="_blank">
                                    <i class="fas fa-eye fa-xs"></i>
                                </a>
                                <a href="{{ route('admin.kliniks.edit', $klinik) }}"
                                   class="btn btn-sm btn-outline-terracotta"
                                   title="Edit">
                                    <i class="fas fa-edit fa-xs"></i>
                                </a>
                                <form action="{{ route('admin.kliniks.destroy', $klinik) }}"
                                      method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="fas fa-trash fa-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($kliniks->hasPages())
        <div class="card-footer bg-transparent border-top-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        Menampilkan {{ $kliniks->firstItem() ?? 0 }} - {{ $kliniks->lastItem() ?? 0 }} dari {{ $kliniks->total() }} klinik
                    </small>
                </div>
                <div class="col-md-6 mt-2 mt-md-0">
                    {{ $kliniks->appends(request()->query())->links('custom.pagination') }}
                </div>
            </div>
        </div>
        @endif
    </div>
    @else
    <!-- Empty State -->
    <div class="card border-0 shadow-soft" data-reveal>
        <div class="card-body text-center py-5">
            <div class="medallion medallion-sage mx-auto mb-4" style="width:76px;height:76px;font-size:1.9rem;border-radius:26px;">
                <i class="fas fa-clinic-medical"></i>
            </div>
            <h4 class="fw-bold mb-2" style="font-family: var(--font-display); color: var(--sage-900);">
                Belum Ada Data Klinik
            </h4>
            <p class="text-muted mb-4" style="max-width: 420px; margin: 0 auto;">
                @if(request('search') || request('status'))
                    Tidak ada klinik yang sesuai dengan filter pencarian Anda.
                @else
                    Silakan tambah klinik baru untuk mulai mengelola data klinik kecantikan.
                @endif
            </p>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.kliniks.index') }}" class="btn btn-outline-sage">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke semua data
                </a>
            @else
                <a href="{{ route('klinik.create') }}" class="btn btn-terracotta">
                    <i class="fas fa-plus me-1"></i> Tambah Klinik Pertama
                </a>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function() {
    // Hide loading indicator when page loads
    $('#loading-indicator').hide();
    $('#data-container').fadeIn();

    // Delete confirmation
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data klinik akan dihapus permanen dan tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#C0534B',
            cancelButtonColor: '#8A8F82',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            background: '#FAF6ED',
            color: '#2E3429'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Sedang memproses penghapusan data',
                    allowOutsideClick: false,
                    background: '#FAF6ED',
                    color: '#2E3429',
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    });

    // Status update confirmation
    $('form[action*="status"]').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const status = $(this).find('input[name="status"]').val();
        const statusText = status === 'approved' ? 'disetujui' : 'ditolak';

        Swal.fire({
            title: 'Konfirmasi Status',
            text: `Apakah Anda yakin ingin mengubah status menjadi "${statusText}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: status === 'approved' ? '#5B8A5B' : '#C0534B',
            cancelButtonColor: '#8A8F82',
            confirmButtonText: 'Ya, ubah status',
            cancelButtonText: 'Batal',
            background: '#FAF6ED',
            color: '#2E3429'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush




