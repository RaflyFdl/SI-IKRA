<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Anggota - SI Infak IKRA Padjadjaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .card-register { border-top: 5px solid #006644; } /* Warna hijau khas islami/Muamalat */
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm card-register">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-success">YAYASAN WAKAF IKRA PADJADJARAN</h3>
                        <p class="text-muted">Pendaftaran Anggota Alumni FK Unpad</p>
                    </div>

                    <hr>

                    @if(session('success'))
                        <!-- Modal Sukses Pendaftaran -->
                        <div class="modal fade" id="successRegisterModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="successRegisterModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                    <div class="modal-body text-center p-5">
                                        <div class="mb-4">
                                            <span class="display-3 text-success"><i class="fa-solid fa-circle-check animate-pulse"></i></span>
                                        </div>
                                        <h4 class="fw-bold text-success mb-3">Pendaftaran Berhasil!</h4>
                                        <p class="text-muted text-sm mb-4 leading-relaxed">
                                            Akun Anda telah berhasil dibuat. Silakan lakukan <strong>verifikasi akun melalui email Anda</strong> terlebih dahulu sebelum dapat masuk ke sistem.
                                        </p>
                                        <div class="d-grid">
                                            <a href="{{ url('/') }}" class="btn btn-success btn-lg" style="border-radius: 12px; background-color: #006644; border-color: #006644; font-size: 15px; font-weight: bold;">
                                                <i class="fa-solid fa-house me-2"></i> Kembali ke Beranda
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                var myModal = new bootstrap.Modal(document.getElementById('successRegisterModal'), {});
                                myModal.show();
                            });
                        </script>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="nama" class="form-label fw-semibold">Nama Lengkap & Gelar</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Contoh: dr. Ahmad Fauzi, Sp.PD" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="angkatan" class="form-label fw-semibold">Tahun Angkatan FK Unpad</label>
                                <input type="number" class="form-control" id="angkatan" name="angkatan" value="{{ old('angkatan') }}" placeholder="Contoh: 2016" min="1950" max="{{ date('Y') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="no_wa" class="form-label fw-semibold">Nomor WhatsApp Actif</label>
                                <input type="text" class="form-control" id="no_wa" name="no_wa" value="{{ old('no_wa') }}" placeholder="Contoh: 08123456789" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Alamat Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 6 karakter" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="bukti_pendukung" class="form-label fw-semibold">Bukti Pendukung (Ijazah / KTM / Kartu Anggota Alumni)</label>
                            <input type="file" class="form-control" id="bukti_pendukung" name="bukti_pendukung" accept="image/*" required>
                            <div class="form-text">Format berupa gambar (JPG, JPEG, PNG). Maksimal ukuran file 2MB.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Daftar Sebagai Anggota</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>