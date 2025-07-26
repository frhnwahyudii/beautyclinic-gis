@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Statistik Klinik</h1>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <!-- Area Chart -->
            <div class="card card-dashboard shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Trend Pendaftaran Klinik</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="trendDropdown" data-bs-toggle="dropdown">
                            Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-filter="week">Minggu Ini</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="month">Bulan Ini</a></li>
                            <li><a class="dropdown-item" href="#" data-filter="year">Tahun Ini</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Status Distribution -->
            <div class="card card-dashboard shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Status Klinik</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <!-- Location Stats -->
            <div class="card card-dashboard shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Lokasi</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Kota</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($locationStats as $stat)
                                <tr>
                                    <td>{{ $stat->city }}</td>
                                    <td>{{ $stat->count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card card-dashboard shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($recentActivities as $activity)
                        <div class="timeline-item">
                            <div class="timeline-date text-muted">{{ $activity->created_at->diffForHumans() }}</div>
                            <div class="timeline-content">
                                {{ $activity->description }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Trend Chart
const trendCtx = document.getElementById('trendChart');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($trendData->pluck('date')) !!},
        datasets: [{
            label: 'Pendaftaran Klinik',
            data: {!! json_encode($trendData->pluck('count')) !!},
            borderColor: '#4e73df',
            tension: 0.3,
            fill: true,
            backgroundColor: 'rgba(78, 115, 223, 0.05)'
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10
                }
            },
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7,
                    padding: 10
                }
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Approved', 'Rejected'],
        datasets: [{
            data: [
                {{ $statusData['pending'] }},
                {{ $statusData['approved'] }},
                {{ $statusData['rejected'] }}
            ],
            backgroundColor: ['#f6c23e', '#1cc88a', '#e74a3b'],
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        cutout: '70%'
    }
});
</script>
@endpush

@push('styles')
<style>
.chart-area {
    position: relative;
    height: 320px;
    width: 100%;
}
.chart-pie {
    position: relative;
    height: 300px;
    width: 100%;
}
.timeline {
    position: relative;
    padding: 20px 0;
}
.timeline-item {
    padding-left: 24px;
    padding-bottom: 20px;
    border-left: 2px solid #e3e6f0;
    position: relative;
}
.timeline-item:last-child {
    border-left: none;
}
.timeline-date {
    font-size: 0.875rem;
    margin-bottom: 4px;
}
.timeline-content {
    background: #f8f9fc;
    padding: 10px;
    border-radius: 4px;
    font-size: 0.875rem;
}
</style>
@endpush
