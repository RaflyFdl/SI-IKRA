<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengeluaran Ekstra - IKRA</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 font-sans text-slate-800">

    <div class="flex min-h-screen">
        <div class="w-64 bg-slate-900 text-white p-6 space-y-6 flex flex-col justify-between hidden md:flex shrink-0">
            <div class="space-y-6">
                <div class="flex items-center space-x-3 border-b border-slate-700 pb-4">
                    <div class="bg-emerald-500 p-2 rounded-lg text-slate-900 font-bold">IK</div>
                    <div>
                        <h1 class="font-bold text-lg leading-tight">IKRA System</h1>
                        <p class="text-xs text-slate-400">Tim Operasional</p>
                    </div>
                </div>
                <nav class="space-y-2">
                    <a href="{{ route('operational.dashboard') }}" class="flex items-center space-x-3 text-slate-300 hover:bg-slate-800 hover:text-white p-3 rounded-lg transition">
                        <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="{{ route('operational.schedule') }}" class="flex items-center space-x-3 text-slate-300 hover:bg-slate-800 hover:text-white p-3 rounded-lg transition">
                        <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                        <span>Agenda & Jadwal</span>
                    </a>

                    <a href="{{ route('operational.pencairan') }}" class="flex items-center space-x-3 bg-emerald-600 text-white font-medium p-3 rounded-lg transition">
                        <i class="fa-solid fa-hand-holding-dollar w-5 text-center"></i>
                        <span>Pencairan Dana Ekstra</span>
                    </a>

                    <a href="{{ route('operational.penyaluran-reguler.index') }}" class="flex items-center space-x-3 text-slate-300 hover:bg-slate-800 hover:text-white p-3 rounded-lg transition">
                        <i class="fa-solid fa-heart-circle-check w-5 text-center"></i>
                        <span>Penyaluran Infak Reguler</span>
                    </a>
                </nav>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="border-t border-slate-700 pt-4">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 text-red-400 hover:bg-slate-800 p-3 rounded-lg transition text-left cursor-pointer text-sm font-medium">
                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>

        <div class="flex-1 p-10 max-w-6xl mx-auto">
            <div class="mb-6">
                <a href="{{ route('operational.dashboard') }}" class="text-xs font-bold text-emerald-600 hover:underline">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Dashboard Operasional
                </a>
                <h1 class="text-2xl font-black text-slate-900 mt-2">Form Multi-Nota Rincian Penggunaan Dana Ekstra</h1>
                <p class="text-xs text-slate-500 mt-1">Masukkan rincian nota belanja lapangan secara detail. Sistem akan menghitung otomatis selisih dana anggaran.</p>
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex items-start space-x-3 shadow-sm">
                    <span class="mt-0.5">❌</span>
                    <ul class="text-sm font-medium list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl flex items-center space-x-3 shadow-sm">
                    <span>❌</span>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <span class="text-slate-400 block font-bold uppercase tracking-wider text-[10px]">Nama Program Kerja (Ekstra)</span>
                    <span class="font-bold text-slate-800 text-sm block mt-0.5">{{ $pengajuan->extraProgram->name ?? 'Program Tidak Diketahui' }}</span>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 p-3 rounded-xl">
                    <span class="text-emerald-800 block font-bold uppercase tracking-wider text-[10px]">Dana Dicairkan Keuangan</span>
                    <span id="danaDicairkanBadge" data-nominal="{{ $pengajuan->nominal_diminta }}" class="font-black text-emerald-700 text-base block mt-0.5">
                        Rp {{ number_format($pengajuan->nominal_diminta, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            
            @if(isset($pengajuan->extraProgram) && $pengajuan->extraProgram->detailKebutuhan->count() > 0)
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-6">
                <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-emerald-600"></i> Acuan Rencana Kebutuhan Dana Awal
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 border-b border-slate-200">
                                <th class="p-3 font-bold w-12 text-center">No</th>
                                <th class="p-3 font-bold">Kebutuhan / Barang</th>
                                <th class="p-3 font-bold text-center">Jumlah</th>
                                <th class="p-3 font-bold text-right">Estimasi Harga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($pengajuan->extraProgram->detailKebutuhan as $index => $detail)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="p-3 text-center text-slate-500">{{ $index + 1 }}</td>
                                    <td class="p-3 font-medium">{{ $detail->nama_barang }}</td>
                                    <td class="p-3 text-center bg-slate-50 border-x border-slate-100">{{ $detail->jumlah }} {{ $detail->satuan }}</td>
                                    <td class="p-3 text-right font-medium text-slate-800">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <form action="{{ route('operational.laporan.store', $pengajuan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs" id="notaTable">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4 w-[15%]">Tanggal Nota</th>
                                    <th class="py-3 px-4 w-[32%]">Uraian / Keterangan Belanja</th>
                                    <th class="py-3 px-4 w-[13%]">Kuantitas</th>
                                    <th class="py-3 px-4 w-[18%]">Nominal (Rp)</th>
                                    <th class="py-3 px-4 w-[17%]">Bukti Nota</th>
                                    <th class="py-3 px-2 w-[5%] text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                <tr>
                                    <td class="py-3 px-2">
                                        <input type="date" name="nota[0][tanggal]" class="w-full p-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-emerald-500 font-semibold" required>
                                    </td>
                                    <td class="py-3 px-2">
                                        <input type="text" name="nota[0][uraian]" class="w-full p-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-emerald-500" placeholder="Contoh: Belanja Keperluan Logistik Bencana" required>
                                    </td>
                                    <td class="py-3 px-2">
                                        <input type="text" name="nota[0][kuantitas]" class="w-full p-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-emerald-500" placeholder="Contoh: 50 Karung" required>
                                    </td>
                                    <td class="py-3 px-2">
                                        <input type="number" name="nota[0][nominal]" class="w-full p-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-emerald-500 font-mono font-bold text-slate-700 nominal-input" placeholder="0" required>
                                    </td>
                                    <td class="py-3 px-2">
                                        <input type="file" name="nota[0][bukti_nota]" class="w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer" accept="image/*" required>
                                    </td>
                                    <td class="py-3 px-2 text-center">
                                        <button type="button" class="text-slate-300 cursor-not-allowed text-sm" disabled>
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" id="addBtn" class="inline-flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold px-4 py-2 rounded-xl transition cursor-pointer shadow-sm border border-emerald-200">
                        <i class="fa-solid fa-circle-plus"></i> Tambah Item Nota Belanja
                    </button>

                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 space-y-2.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Accumulated / Total Pengeluaran Nota:</span>
                            <span id="txtTotalBelanja" class="font-bold text-slate-800 text-sm font-mono">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-slate-200 pt-2.5">
                            <span class="text-slate-500 font-medium">Status & Balansasi Kas Lapangan:</span>
                            <span id="txtStatusSelisih" class="font-bold text-slate-600">Pas / Seimbang</span>
                        </div>

                        <div id="sectionBuktiPengembalian" style="display: none;" class="mt-3 border-t border-emerald-200 pt-3 space-y-2">
                            <label class="block text-xs font-bold text-emerald-800 uppercase flex items-center gap-1">
                                <i class="fa-solid fa-cloud-arrow-up text-sm"></i> Upload Bukti Transfer Pengembalian Sisa Dana
                            </label>
                            <input type="file" name="bukti_pengembalian_sisa" id="inputBuktiSisa" class="w-full text-xs text-slate-500 border border-emerald-300 bg-emerald-50/30 p-2 rounded-xl file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer" accept="image/*">
                            <p class="text-[10px] text-emerald-600 font-medium leading-relaxed">*Dana lebih wajib ditransfer kembali ke rekening utama yayasan IKRA. Mohon lampirkan struk buktinya.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Catatan Evaluasi / Dokumentasi Kegiatan</label>
                        <textarea name="keterangan" rows="3" class="w-full p-3 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs" placeholder="Tulis catatan pelaksanaan program atau kendala lapangan jika ada..."></textarea>
                    </div>

                    <div class="flex justify-between items-center border-t border-slate-100 pt-4">
                        <a href="{{ route('operational.dashboard') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold px-5 py-2.5 rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-6 py-2.5 rounded-xl shadow-md transition cursor-pointer">
                            <i class="fa-solid fa-paper-plane mr-1"></i> Kirim Laporan Pertanggungjawaban
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let rowIdx = 1;
        const totalDanaAwal = parseInt(document.getElementById('danaDicairkanBadge').getAttribute('data-nominal'));

        // Fungsi Hitung Otomatis Total Pengeluaran & Selisih Anggaran
        function hitungSelisihOtomatis() {
            let totalBelanja = 0;
            document.querySelectorAll('.nominal-input').forEach(input => {
                let val = parseInt(input.value);
                if (!isNaN(val)) totalBelanja += val;
            });

            // Update Teks Total Pengeluaran
            document.getElementById('txtTotalBelanja').innerText = 'Rp ' + totalBelanja.toLocaleString('id-ID');
            
            const selisih = totalDanaAwal - totalBelanja;
            const divBuktiSisa = document.getElementById('sectionBuktiPengembalian');
            const inputBuktiSisa = document.getElementById('inputBuktiSisa');
            const txtStatus = document.getElementById('txtStatusSelisih');

            if (selisih > 0) {
                // Skenario Kelebihan Uang / Sisa Dana
                txtStatus.innerHTML = `<span class="text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded border border-emerald-200 font-bold"><i class="fa-solid fa-circle-dollar-to-slot mr-1"></i> Kelebihan Dana: Rp ${selisih.toLocaleString('id-ID')} (Wajib Dikembalikan)</span>`;
                divBuktiSisa.style.display = 'block';
                inputBuktiSisa.setAttribute('required', 'required');
            } else if (selisih < 0) {
                // Skenario Kurang Dana / Nombok (Reimburse)
                txtStatus.innerHTML = `<span class="text-rose-600 bg-rose-50 px-2.5 py-0.5 rounded border border-rose-200 font-bold"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Kurang Dana / Reimburse: Rp ${Math.abs(selisih).toLocaleString('id-ID')}</span>`;
                divBuktiSisa.style.display = 'none';
                inputBuktiSisa.removeAttribute('required');
            } else {
                // Pas / Seimbang
                txtStatus.innerHTML = `<span class="text-slate-500 bg-slate-50 px-2.5 py-0.5 rounded border border-slate-200 font-bold"><i class="fa-solid fa-scale-balanced mr-1"></i> Anggaran Pas / Seimbang</span>`;
                divBuktiSisa.style.display = 'none';
                inputBuktiSisa.removeAttribute('required');
            }
        }

        // Jalankan kalkulasi setiap kali ada perubahan angka di kolom nominal
        document.getElementById('notaTable').addEventListener('input', function(e) {
            if (e.target.classList.contains('nominal-input')) {
                hitungSelisihOtomatis();
            }
        });

        // Event Aksi Tambah Baris Nota
        document.getElementById('addBtn').addEventListener('click', function() {
            const tbody = document.querySelector('#notaTable tbody');
            const newRow = document.createElement('tr');
            newRow.className = "hover:bg-slate-50/50 transition";
            
            newRow.innerHTML = `
                <td class="py-3 px-2">
                    <input type="date" name="nota[${rowIdx}][tanggal]" class="w-full p-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-emerald-500 font-semibold" required>
                </td>
                <td class="py-3 px-2">
                    <input type="text" name="nota[${rowIdx}][uraian]" class="w-full p-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-emerald-500" placeholder="Keterangan belanja barang" required>
                </td>
                <td class="py-3 px-2">
                    <input type="text" name="nota[${rowIdx}][kuantitas]" class="w-full p-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-emerald-500" placeholder="Contoh: 10 Pcs" required>
                </td>
                <td class="py-3 px-2">
                    <input type="number" name="nota[${rowIdx}][nominal]" class="w-full p-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-emerald-500 font-mono font-bold text-slate-700 nominal-input" placeholder="0" required>
                </td>
                <td class="py-3 px-2">
                    <input type="file" name="nota[${rowIdx}][bukti_nota]" class="w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer" accept="image/*" required>
                </td>
                <td class="py-3 px-2 text-center">
                    <button type="button" class="text-rose-500 hover:text-rose-700 text-sm removeBtn cursor-pointer transition">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;
            
            tbody.appendChild(newRow);
            rowIdx++;
            hitungSelisihOtomatis();
        });

        // Event Aksi Hapus Baris Nota
        document.querySelector('#notaTable tbody').addEventListener('click', function(e) {
            if (e.target.classList.contains('removeBtn') || e.target.closest('.removeBtn')) {
                const btn = e.target.classList.contains('removeBtn') ? e.target : e.target.closest('.removeBtn');
                btn.closest('tr').remove();
                hitungSelisihOtomatis();
            }
        });
    </script>
</body>
</html>