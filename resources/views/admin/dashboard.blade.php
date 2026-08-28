@extends('layouts.admin')

@section('title', 'Dashboard — SIG Klinik Kecantikan')
@section('page-title', 'Dashboard')
@section('page-sub', 'Ringkasan data klinik kecantikan')

@section('content')
<!-- Stat cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4" data-reveal>
        <div class="card stat-card stat-sage h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 fw-semibold" style="font-size: .85rem;">Total Klinik</div>
                    <div class="stat-value mt-1">{{ \App\Models\Klinik::count() }}</div>
                    <small class="text-white-50">Semua data klinik</small>
                </div>
                <div class="stat-icon-medallion"><i class="fas fa-clinic-medical"></i></div>
            </div>
            <a href="{{ route('admin.kliniks.index') }}" class="stretched-link text-decoration-none" style="color: inherit;"></a>
        </div>
    </div>

    <div class="col-md-4" data-reveal style="--reveal-delay: 0.08s;">
        <div class="card stat-card stat-terra h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 fw-semibold" style="font-size: .85rem;">Menunggu Verifikasi</div>
                    <div class="stat-value mt-1">{{ \App\Models\Klinik::where('status', 'pending')->count() }}</div>
                    <small class="text-white-50">Perlu tindakan Anda</small>
                </div>
                <div class="stat-icon-medallion"><i class="fas fa-clock"></i></div>
            </div>
            <a href="{{ route('admin.kliniks.index', ['status' => 'pending']) }}" class="stretched-link text-decoration-none" style="color: inherit;"></a>
        </div>
    </div>

    <div class="col-md-4" data-reveal style="--reveal-delay: 0.16s;">
        <div class="card stat-card stat-deep h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-white-50 fw-semibold" style="font-size: .85rem;">Aktif / Terverifikasi</div>
                    <div class="stat-value mt-1">{{ \App\Models\Klinik::where('status', 'approved')->count() }}</div>
                    <small class="text-white-50">Tampil di peta publik</small>
                </div>
                <div class="stat-icon-medallion"><i class="fas fa-check-circle"></i></div>
            </div>
            <a href="{{ route('admin.kliniks.index', ['status' => 'approved']) }}" class="stretched-link text-decoration-none" style="color: inherit;"></a>
        </div>
    </div>
</div>
<div class="row g-4">
    <!-- Status chart -->
    <div class="col-lg-6" data-reveal>
        <div class="card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="mb-1" style="font-family: var(--font-display); color: var(--sage-900);">Status Klinik</h5>
                        <small class="text-muted">Distribusi status seluruh klinik</small>
                    </div>
                    <div class="medallion medallion-gold" style="width:46px;height:46px;font-size:1.1rem;border-radius:15px;">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
                <div style="height: 280px;">
                    <canvas id="statusChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent activity -->
    <div class="col-lg-6" data-reveal style="--reveal-delay: 0.1s;">
        <div class="card h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="mb-1" style="font-family: var(--font-display); color: var(--sage-900);">Aktivitas Terbaru</h5>
                        <small class="text-muted">Klinik yang baru terdaftar</small>
                    </div>
                    <div class="medallion medallion-terra" style="width:46px;height:46px;font-size:1.1rem;border-radius:15px;">
                        <i class="fas fa-history"></i>
                    </div>
                </div>
                <div class="timeline">
                    @foreach(\App\Models\Klinik::latest()->take(8)->get() as $activity)
                    <div class="timeline-item">
                        <div class="timeline-date">{{ $activity->created_at->diffForHumans() }}</div>
                        <div class="timeline-content">
                            <strong>{{ $activity->nama }}</strong> telah mendaftar
                            <span class="badge ms-1 {{ $activity->status == 'approved' ? 'bg-success-soft' : ($activity->status == 'pending' ? 'bg-warning-soft' : 'bg-danger-soft') }}">
                                {{ ucfirst($activity->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const approved = {{ \App\Models\Klinik::where('status', 'approved')->count() }};
    const pending = {{ \App\Models\Klinik::where('status', 'pending')->count() }};
    const rejected = {{ \App\Models\Klinik::where('status', 'rejected')->count() }};

    const ctx = document.getElementById('statusChart');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Disetujui', 'Pending', 'Ditolak'],
            datasets: [{
                data: [approved, pending, rejected],
                backgroundColor: ['#5B8A5B', '#C98A3D', '#C0534B'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { family: 'Manrope', size: 13, weight: 600 }
                    }
                },
                tooltip: {
                    backgroundColor: '#2E3A29',
                    padding: 12,
                    cornerRadius: 12,
                    titleFont: { family: 'Manrope' },
                    bodyFont: { family: 'Manrope' }
                }
            }
        }
    });
});
</script>
@endpush


