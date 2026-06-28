<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Laporan Penggunaan Dana - IKRA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 font-weight-bold">Input Laporan Penggunaan Dana</h5>
                    <span class="badge bg-light text-primary fs-6" id="danaDicairkanBadge" data-nominal="{{ $pengajuan->nominal_diminta }}">
                        Dana Dicairkan: Rp {{ number_format($pengajuan->nominal_diminta, 0, ',', '.') }}
                    </span>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('operational.laporan.store', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle" id="notaTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 20%;">Tanggal Nota</th>
                                        <th style="width: 35%;">Uraian / Keterangan</th>
                                        <th style="width: 20%;">Nominal (Rp)</th>
                                        <th style="width: 20%;">Bukti penggunaan dana</th>
                                        <th style="width: 5%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="date" name="nota[0][tanggal]" class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="text" name="nota[0][uraian]" class="form-control" placeholder="Contoh: Beli Sembako Paket A" required>
                                        </td>
                                        <td>
                                            <input type="number" name="nota[0][nominal]" class="form-control nominal-input" placeholder="0" required>
                                        </td>
                                        <td>
                                            <input type="file" name="nota[0][bukti_nota]" class="form-control" accept="image/*" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger disabled"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button type="button" class="btn btn-success btn-sm mb-4" id="addBtn">
                            <i class="bi bi-plus-circle"></i> Tambah Baris Nota
                        </button>

                        <div class="card bg-light border-0 p-3 mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Belanja Nota:</span>
                                <strong id="txtTotalBelanja">Rp 0</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Status Selisih Dana:</span>
                                <strong id="txtStatusSelisih" class="text-secondary">Seimbang</strong>
                            </div>
                            
                            <div id="sectionBuktiPengembalian" style="display: none;" class="mt-3 border-top pt-3">
                                <label class="form-label text-success fw-bold">
                                    <i class="bi bi-cloud-arrow-up-fill"></i> Upload Bukti Transfer Pengembalian Sisa Dana:
                                </label>
                                <input type="file" name="bukti_pengembalian_sisa" id="inputBuktiSisa" class="form-control border-success" accept="image/*">
                                <small class="text-muted">Dana sisa wajib ditransfer balik ke rekening utama yayasan. Silakan unggah struknya di sini.</small>
                            </div>
                        </div>
                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('operational.dashboard') }}" class="btn btn-secondary px-4">Kembali</a>
                            <button type="submit" class="btn btn-primary px-5">Kirim Laporan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let rowIdx = 1;
    const totalDanaAwal = parseInt(document.getElementById('danaDicairkanBadge').getAttribute('data-nominal'));

    // Fungsi Hitung Otomatis Total & Selisih Dana
    function hitungSelisihOtomatis() {
        let totalBelanja = 0;
        document.querySelectorAll('.nominal-input').forEach(input => {
            let val = parseInt(input.value);
            if (!isNaN(val)) totalBelanja += val;
        });

        document.getElementById('txtTotalBelanja').innerText = 'Rp ' + totalBelanja.toLocaleString('id-ID');
        
        const selisih = totalDanaAwal - totalBelanja;
        const divBuktiSisa = document.getElementById('sectionBuktiPengembalian');
        const inputBuktiSisa = document.getElementById('inputBuktiSisa');
        const txtStatus = document.getElementById('txtStatusSelisih');

        if (selisih > 0) {
            // Skenario Kelebihan Uang (Poin 1)
            txtStatus.innerHTML = `<span class="text-success">Sisa Dana Rp ${selisih.toLocaleString('id-ID')} (Wajib Dikembalikan)</span>`;
            divBuktiSisa.style.display = 'block';
            inputBuktiSisa.setAttribute('required', 'required');
        } else if (selisih < 0) {
            // Skenario Nombok / Kurang Dana (Poin 2)
            txtStatus.innerHTML = `<span class="text-danger">Kurang Dana / Reimburse Rp ${Math.abs(selisih).toLocaleString('id-ID')}</span>`;
            divBuktiSisa.style.display = 'none';
            inputBuktiSisa.removeAttribute('required');
        } else {
            // Pas / Seimbang
            txtStatus.innerHTML = `<span class="text-secondary">Pas / Seimbang</span>`;
            divBuktiSisa.style.display = 'none';
            inputBuktiSisa.removeAttribute('required');
        }
    }

    // Event Listener untuk menghitung saat input nominal berubah
    document.getElementById('notaTable').addEventListener('input', function(e) {
        if (e.target.classList.contains('nominal-input')) {
            hitungSelisihOtomatis();
        }
    });

    // Fungsi Tambah Baris
    document.getElementById('addBtn').addEventListener('click', function() {
        const tbody = document.querySelector('#notaTable tbody');
        const newRow = document.createElement('tr');
        
        newRow.innerHTML = `
            <td><input type="date" name="nota[${rowIdx}][tanggal]" class="form-control" required></td>
            <td><input type="text" name="nota[${rowIdx}][uraian]" class="form-control" placeholder="Keterangan belanja" required></td>
            <td><input type="number" name="nota[${rowIdx}][nominal]" class="form-control nominal-input" placeholder="0" required></td>
            <td><input type="file" name="nota[${rowIdx}][bukti_nota]" class="form-control" accept="image/*" required></td>
            <td><button type="button" class="btn btn-sm btn-danger removeBtn"><i class="bi bi-trash"></i></button></td>
        `;
        
        tbody.appendChild(newRow);
        rowIdx++;
        hitungSelisihOtomatis();
    });

    // Fungsi Hapus Baris
    document.querySelector('#notaTable tbody').addEventListener('click', function(e) {
        if (e.target.classList.contains('removeBtn') || e.target.closest('.removeBtn')) {
            const btn = e.target.classList.contains('removeBtn') ? e.target : e.target.closest('.removeBtn');
            btn.closest('tr').remove();
            hitungSelisihOtomatis();
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>