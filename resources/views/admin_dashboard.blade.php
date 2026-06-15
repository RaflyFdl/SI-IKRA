<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SI Infak IKRA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Dashboard Admin</h2>
            <p class="text-muted mb-0">Verifikasi Anggota & Pembuatan VA Otomatis - Yayasan IKRA Padjadjaran</p>
        </div>
        <a href="{{ route('register') }}" class="btn btn-outline-secondary" target="_blank">Buka Form Daftar ↗</a>
    </div>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active fw-semibold text-dark" aria-current="page" href="{{ route('admin.dashboard') }}">
                👥 Verifikasi Pendaftar (Rutin)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('admin.programs.index') }}">
                📢 Publikasi Program Infak Ekstra
            </a>
        </li>
    </ul>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Nama</th>
                            <th>Angkatan</th>
                            <th>No. WhatsApp</th>
                            <th>Bukti</th>
                            <th>Status Akun</th>
                            <th>Virtual Account Muamalat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $m)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $m->nama }}</div>
                                <small class="text-muted">{{ $m->email }}</small>
                            </td>
                            <td><span class="badge bg-secondary">{{ $m->angkatan }}</span></td>
                            <td>{{ $m->no_wa }}</td>
                            <td>
                                <a href="{{ asset('uploads/' . $m->bukti_pendukung) }}" target="_blank" class="btn btn-sm btn-info text-white">Lihat Bukti</a>
                            </td>
                            <td>
                                @if($m->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($m->status == 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if($m->va_muamalat)
                                    <strong class="text-success font-monospace">{{ $m->va_muamalat }}</strong>
                                @else
                                    <span class="text-muted small">Belum Tergenerate</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($m->status == 'pending')
                                    <form action="{{ route('admin.approve', $m->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success px-3">Setujui & Buat VA</button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-light text-muted" disabled>Selesai</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada alumni yang mendaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>