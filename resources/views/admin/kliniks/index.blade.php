@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manajemen Data Klinik</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('klinik.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Tambah Klinik Baru
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-center">#</th>
                                <th scope="col">Foto</th>
                                <th scope="col">Nama Klinik</th>
                                <th scope="col">Lokasi</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Tanggal Daftar</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kliniks as $index => $klinik)
                            <tr>
                                <td class="text-center">{{ $kliniks->firstItem() + $index }}</td>
                                <td>
                                    @if($klinik->foto)
                                        <img src="{{ $klinik->foto_url }}"
                                             alt="Foto {{ $klinik->nama }}"
                                             class="img-thumbnail"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $klinik->nama }}</strong>
                                    @if($klinik->telepon)
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-phone"></i> {{ $klinik->telepon }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    {{ Str::limit($klinik->alamat, 50) }}
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ number_format($klinik->latitude, 6) }}, {{ number_format($klinik->longitude, 6) }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    @if($klinik->status == 'pending')
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-warning dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                                                <span class="badge bg-warning text-dark">Pending</span>
                                                <span class="visually-hidden">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form action="{{ route('admin.kliniks.update-status', $klinik) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="fas fa-check"></i> Setujui
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.kliniks.update-status', $klinik) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-times"></i> Tolak
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    @elseif($klinik->status == 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column">
                                        <span>{{ $klinik->created_at->format('d M Y') }}</span>
                                        <small class="text-muted">{{ $klinik->created_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('klinik.show', $klinik) }}"
                                           class="btn btn-sm btn-info"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.kliniks.edit', $klinik) }}"
                                           class="btn btn-sm btn-warning"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.kliniks.destroy', $klinik) }}"
                                              method="POST"
                                              class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Belum ada data klinik</h5>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>

                    @if($kliniks->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $kliniks->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data klinik akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>
@endpush
