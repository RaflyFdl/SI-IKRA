<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyaluran Infak Reguler - IKRA</title>
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
                    <a href="{{ route('operational.dashboard') }}" class="flex items-center space-x-3 {{ request()->routeIs('operational.dashboard') && !request()->routeIs('operational.pencairan') && !request()->routeIs('operational.penyaluran-reguler.index') ? 'bg-emerald-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-3 rounded-lg transition">
                        <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="{{ route('operational.schedule') }}" class="flex items-center space-x-3 {{ request()->routeIs('operational.schedule') ? 'bg-emerald-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-3 rounded-lg transition">
                        <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                        <span>Agenda & Jadwal</span>
                    </a>

                    <a href="{{ route('operational.pencairan') }}" class="flex items-center space-x-3 {{ request()->routeIs('operational.pencairan') ? 'bg-emerald-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-3 rounded-lg transition">
                        <i class="fa-solid fa-hand-holding-dollar w-5 text-center"></i>
                        <span>Pencairan Dana Ekstra</span>
                    </a>

                    <a href="{{ route('operational.penyaluran-reguler.index') }}" class="flex items-center space-x-3 {{ request()->routeIs('operational.penyaluran-reguler.index') ? 'bg-emerald-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-3 rounded-lg transition">
                        <i class="fa-solid fa-heart-circle-check w-5 text-center"></i>
                        <span>Penyaluran Infak Reguler</span>
                    </a>
                </nav>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="border-t border-slate-700 pt-4">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 text-red-400 hover:bg-slate-800 p-3 rounded-lg transition text-left cursor-pointer">
                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>

        <div class="flex-1 p-10">
            
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900">Perencanaan Penyaluran Infak Reguler</h2>
                    <p class="text-slate-500 mt-1">Kelola batas aman alokasi program kerja murni berbasis hak porsi 65% dana jamaah.</p>
                </div>
            </div>

            @if(session('sukses'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center space-x-3 shadow-sm">
                    <span>✅</span>
                    <p class="text-sm font-medium">{{ session('sukses') }}</p>
                </div>
            @endif

            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-6 items-center mb-8">
                <div class="space-y-1.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Pilih Periode Acuan</span>
                    <form method="GET" action="{{ route('operational.penyaluran-reguler.index') }}">
                        <select name="periode" onchange="this.form.submit()" class="bg-slate-50 text-sm font-bold border border-slate-200 text-slate-700 rounded-lg px-3 py-2 focus:outline-none focus:border-emerald-600 cursor-pointer w-full max-w-xs">
                            @foreach($daftarPeriode as $p)
                                <option value="{{ $p }}" {{ $periodeDipilih == $p ? 'selected' : '' }}>{{ $p }} {{ $p == now()->format('Y-m') ? '(Bulan Ini)' : '' }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Pemasukan Reguler Periode (100%)</span>
                    <h3 class="text-2xl font-extrabold text-slate-800">Rp {{ number_format($totalMasukPeriode, 0, ',', '.') }}</h3>
                </div>

                <div class="bg-emerald-50/50 border border-emerald-100 p-4 rounded-xl space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 block">Sisa Dana Siap Salur (65%)</span>
                    <h3 class="text-2xl font-black text-emerald-700">Rp {{ number_format($sisaDanaSiapSalur, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-emerald-600 font-medium">*Batas aman nominal penyusunan rencana program.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-4 h-fit">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-file-signature text-emerald-600"></i> Form Ajuan Penyaluran
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Kirim berkas ke Pembina untuk mendapatkan pengesahan sistem.</p>
                    </div>
                    
                    <form action="{{ route('operational.penyaluran-reguler.store') }}" method="POST" class="space-y-4" id="form-pengajuan-reguler">
                        @csrf
                        <input type="hidden" name="periode_bulan" value="{{ $periodeDipilih }}">

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Program Kerja</label>
                            <input type="text" name="nama_program" required placeholder="Contoh: Santunan Beras Yatim Dhuafa" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-emerald-600 font-medium">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Target Penerima Manfaat</label>
                            <input type="text" name="penerima_manfaat" required placeholder="Contoh: 50 KK Mustahik Coblong" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-emerald-600 font-medium">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Pelaksanaan</label>
                            <input type="date" id="tanggalPelaksanaanInput" name="tanggal_pelaksanaan" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-emerald-600 font-medium">
                            <p id="date_error_message" class="text-[11px] text-rose-600 font-bold mt-1.5 hidden flex items-center gap-1">
                                <i class="fa-solid fa-triangle-exclamation"></i> Jadwal bentrok! Tanggal ini sudah digunakan program lain.
                            </p>
                        </div>

                        {{-- RINCIAN KEBUTUHAN DANA DINAMIS --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase">Rincian Kebutuhan Dana</label>
                                <button type="button" id="btn-tambah-item" onclick="tambahBaris()" class="flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-lg transition cursor-pointer">
                                    <i class="fa-solid fa-plus text-[10px]"></i> Tambah Baris
                                </button>
                            </div>

                            <div class="border border-slate-200 rounded-xl overflow-hidden">
                                <table class="w-full text-xs" id="tabel-kebutuhan">
                                    <thead class="bg-slate-100 text-slate-500 font-bold uppercase tracking-wider">
                                        <tr>
                                            <th class="py-2.5 px-3 text-left w-[35%]">Nama Kebutuhan</th>
                                            <th class="py-2.5 px-2 text-center w-[10%]">Qty</th>
                                            <th class="py-2.5 px-2 text-center w-[15%]">Satuan</th>
                                            <th class="py-2.5 px-2 text-right w-[30%]">Harga (Rp)</th>
                                            <th class="py-2.5 px-2 text-center w-[10%]"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-kebutuhan" class="divide-y divide-slate-100">
                                        {{-- Baris diisi oleh JavaScript --}}
                                    </tbody>
                                    <tfoot class="bg-emerald-50 border-t-2 border-emerald-200">
                                        <tr>
                                            <td colspan="3" class="py-2.5 px-3 font-bold text-emerald-800 uppercase text-[10px] tracking-wider">Total Anggaran Diajukan</td>
                                            <td class="py-2.5 px-2 font-black text-emerald-700 text-right font-mono" id="total-display">Rp 0</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1.5">Total anggaran di atas akan otomatis menjadi nominal yang diajukan.</p>
                        </div>

                        {{-- Hidden input untuk menyimpan data JSON rincian & total --}}
                        <input type="hidden" name="rincian_detail" id="input-rincian-json">
                        <input type="number" name="nominal_diajukan" id="input-nominal-hidden" max="{{ $sisaDanaSiapSalur }}" readonly style="display:none">

                        <button type="submit" id="submit_button" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-3 rounded-lg shadow-sm transition cursor-pointer">
                            Kirim Pengajuan Ke Pembina
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="font-bold text-slate-900 uppercase text-xs tracking-wider">Status Pelacakan Alur Pengajuan</h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 font-bold uppercase tracking-wider">
                                        <th class="py-3.5 px-6">Nama Program / Periode</th>
                                        <th class="py-3.5 px-4">Nominal</th>
                                        <th class="py-3.5 px-4">Target Penerima</th>
                                        <th class="py-3.5 px-6 text-center">Status Alur / Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    @forelse($semuaPengajuan as $sp)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-4 px-6">
                                            <div class="font-bold text-slate-900 text-sm">{{ $sp->nama_program }}</div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">Periode Dana: {{ $sp->periode_bulan }} | Eksekusi: {{ \Carbon\Carbon::parse($sp->tanggal_pelaksanaan)->format('d M Y') }}</div>
                                        </td>
                                        <td class="py-4 px-4 font-mono font-bold text-slate-700">
                                            Rp{{ number_format($sp->nominal_diajukan, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-4 text-slate-500">
                                            {{ $sp->penerima_manfaat }}
                                        </td>
                                        <td class="py-4 px-6 text-center space-y-2">
                                            @if($sp->status == 'pending')
                                                <span class="inline-block px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold rounded-full">⏳ Menunggu Pembina</span>
                                            @elseif($sp->status == 'disetujui' || $sp->status == 'disetujui_pembina')
                                                <span class="inline-block px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold rounded-full">✔️ Disetujui Pembina</span>
                                            @elseif($sp->status == 'dicairkan')
                                                <div class="flex flex-col items-center gap-1.5">
                                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold rounded-full">💰 Dana Cair (Keuangan)</span>
                                                    <a href="{{ route('operational.penyaluran-reguler.laporan.form', $sp->id) }}" class="inline-flex items-center gap-1 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-black px-2.5 py-1 rounded shadow-sm transition">
                                                        <i class="fa-solid fa-file-pen"></i> Isi Laporan Belanja
                                                    </a>
                                                </div>
                                            @elseif($sp->status == 'reimburse_pending')
                                                <div class="flex flex-col items-center gap-1.5">
                                                    <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold rounded-full">🚨 Kurang Dana</span>
                                                    <span class="text-[10px] text-rose-500 font-medium">Menunggu Reimburse Keuangan</span>
                                                </div>
                                            @elseif($sp->status == 'dilaporkan')
                                                <span class="inline-block px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold rounded-full">✅ LPJ Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="py-16 text-center text-slate-400 font-medium space-y-3">
                                            <span class="text-4xl block">📂</span>
                                            <p class="text-sm">Belum ada rekaman rencana pengajuan penyaluran reguler.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // =============================================
        // LOGIKA RINCIAN KEBUTUHAN DANA DINAMIS
        // =============================================
        let barisCounter = 0;

        function formatRupiah(angka) {
            if (!angka || isNaN(angka)) return 'Rp 0';
            return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
        }

        function tambahBaris() {
            const tbody = document.getElementById('tbody-kebutuhan');
            const idx = barisCounter++;
            const row = document.createElement('tr');
            row.id = `baris-${idx}`;
            row.className = 'hover:bg-slate-50/50';
            row.innerHTML = `
                <td class="px-3 py-2">
                    <input type="text" name="kebutuhan[${idx}][nama]" required
                        placeholder="Misal: Beras"
                        class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-medium focus:outline-none focus:border-emerald-500">
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="kebutuhan[${idx}][qty]" required min="0"
                        placeholder="0"
                        class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-medium focus:outline-none focus:border-emerald-500 text-center">
                </td>
                <td class="px-2 py-2">
                    <input type="text" name="kebutuhan[${idx}][satuan]" required
                        placeholder="kg"
                        class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-medium focus:outline-none focus:border-emerald-500 text-center">
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="kebutuhan[${idx}][harga]" required min="0"
                        placeholder="0"
                        class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-mono font-bold text-slate-700 focus:outline-none focus:border-emerald-500 text-right"
                        oninput="hitungTotal()">
                </td>
                <td class="px-2 py-2 text-center">
                    <button type="button" onclick="hapusBaris(${idx})" class="text-rose-400 hover:text-rose-600 transition cursor-pointer">
                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        }

        function hapusBaris(idx) {
            const baris = document.getElementById(`baris-${idx}`);
            if (baris) baris.remove();
            hitungTotal();
        }

        function hitungTotal() {
            const hargaInputs = document.querySelectorAll('#tbody-kebutuhan input[name*="[harga]"]');
            let total = 0;
            hargaInputs.forEach(input => { total += parseFloat(input.value) || 0; });

            document.getElementById('total-display').textContent = formatRupiah(total);
            document.getElementById('input-nominal-hidden').value = total;
        }

        // Serialize kebutuhan rows to JSON before submit
        document.getElementById('form-pengajuan-reguler').addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('#tbody-kebutuhan tr');
            if (rows.length === 0) {
                e.preventDefault();
                alert('Harap tambahkan minimal satu rincian kebutuhan dana.');
                return;
            }
            const kebutuhan = [];
            rows.forEach(row => {
                const namaEl   = row.querySelector('input[name*="[nama]"]');
                const qtyEl    = row.querySelector('input[name*="[qty]"]');
                const satuanEl = row.querySelector('input[name*="[satuan]"]');
                const hargaEl  = row.querySelector('input[name*="[harga]"]');
                if (namaEl && hargaEl) {
                    kebutuhan.push({
                        nama:   namaEl.value,
                        qty:    qtyEl?.value || '',
                        satuan: satuanEl?.value || '',
                        harga:  parseFloat(hargaEl.value) || 0,
                    });
                }
            });
            document.getElementById('input-rincian-json').value = JSON.stringify(kebutuhan);
        });

        // =============================================
        // LOGIKA VALIDASI TANGGAL BENTROK
        // =============================================
        document.addEventListener("DOMContentLoaded", function () {
            const dateInput = document.getElementById("tanggalPelaksanaanInput");
            const dateErrorMessage = document.getElementById("date_error_message");
            const submitBtn = document.getElementById("submit_button");
            
            const busyDates = @json($busyDates ?? []);

            function validateDate() {
                if (dateInput.value) {
                    const selectedDate = dateInput.value;
                    if (busyDates.includes(selectedDate)) {
                        dateErrorMessage.classList.remove("hidden");
                        dateInput.classList.remove("border-slate-200", "focus:border-emerald-600");
                        dateInput.classList.add("border-rose-400", "focus:border-rose-500", "bg-rose-50/30");
                        submitBtn.disabled = true;
                        submitBtn.classList.remove("bg-emerald-600", "hover:bg-emerald-700", "cursor-pointer");
                        submitBtn.classList.add("bg-slate-300", "text-slate-500", "cursor-not-allowed");
                    } else {
                        dateErrorMessage.classList.add("hidden");
                        dateInput.classList.remove("border-rose-400", "focus:border-rose-500", "bg-rose-50/30");
                        dateInput.classList.add("border-slate-200", "focus:border-emerald-600");
                        submitBtn.disabled = false;
                        submitBtn.classList.remove("bg-slate-300", "text-slate-500", "cursor-not-allowed");
                        submitBtn.classList.add("bg-emerald-600", "hover:bg-emerald-700", "cursor-pointer");
                    }
                }
            }

            dateInput.addEventListener("change", validateDate);

            // Tambah satu baris kosong saat halaman pertama kali dibuka
            tambahBaris();
        });
    </script>
</body>
</html>