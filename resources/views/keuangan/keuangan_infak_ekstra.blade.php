<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kas Infak Ekstra - IKRA Padjadjaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <header class="bg-[#0b6e3f] text-white py-6 px-8 shadow-md">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Portal Keuangan Yayasan IKRA</h1>
                <p class="text-xs text-emerald-200 mt-0.5">Rekapitulasi Arus Penerimaan Kas Infak Ekstra Khusus Program</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="bg-emerald-800 text-emerald-100 text-xs font-semibold px-3 py-1.5 rounded-md border border-emerald-700">
                    Role: Staf Keuangan
                </span>
                <a href="/" class="text-sm font-medium text-white hover:text-emerald-200 underline transition-all">Logout</a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-10 space-y-8">
        
        <div class="flex border-b border-gray-200">
            <a href="{{ route('keuangan.dashboard') }}" class="py-3 px-6 font-semibold text-sm text-gray-400 hover:text-gray-700 transition-all">
                💳 Infak Reguler (Bulanan)
            </a>
            <a href="#" class="py-3 px-6 font-bold text-sm text-[#0b6e3f] border-b-2 border-[#0b6e3f] transition-all">
                🌟 Infak Ekstra (Khusus Program)
            </a>
            <a href="{{ route('keuangan.operasional') }}" class="py-3 px-6 font-semibold text-sm text-gray-400 hover:text-gray-700 transition-all">
                💼 Dana Operasional Kantor
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center space-x-3 shadow-sm">
                <span>✅</span>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center space-x-3 shadow-sm">
                <span>❌</span>
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100 flex items-center justify-between bg-gradient-to-br from-white to-emerald-50/20">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 block">Total Dana Backup</span>
                    <h3 class="text-3xl font-extrabold text-emerald-700 tracking-tight mt-1">
                        Rp {{ number_format($totalDanaBackup ?? 0, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="p-3 bg-emerald-100 text-emerald-800 rounded-xl font-bold text-xs uppercase tracking-wider">
                    📦 Sisa Lapangan
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Program Publikasi Aktif</span>
                    <h3 class="text-3xl font-extrabold text-indigo-600 tracking-tight mt-1">
                        {{ count($daftarProgram) }} Program
                    </h3>
                </div>
               
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/80 space-y-4">
            <div>
                <h2 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                    <span>🌟</span> Alokasi Dana Infak Ekstra
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Pemisahan otomatis berdasarkan persentase 35% kebutuhan operasional internal yayasan dan 65% akumulasi dana siap salur khusus program ekstra.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wide block">Alokasi Operasional Kantor (35%)</span>
                        <h4 class="text-xl font-bold text-slate-700 mt-1">
                            Rp {{ number_format($daftarProgram->sum('dana_operasional_ekstra'), 0, ',', '.') }}
                        </h4>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-3 border-t border-slate-200/60 pt-2">
                        *Dipotong otomatis dari akumulasi dana ekstra masuk untuk menunjang operasional kesekretariatan yayasan.
                    </p>
                </div>

                <div class="bg-amber-50/60 border border-amber-100 p-4 rounded-xl flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-amber-800 tracking-wide block">Dana Siap Salur Program Ekstra (65%)</span>
                        <h4 class="text-xl font-bold text-amber-700 mt-1">
                            Rp {{ number_format($daftarProgram->sum('dana_bersih_ekstra'), 0, ',', '.') }}
                        </h4>
                    </div>
                    <p class="text-[11px] text-amber-600/70 mt-3 border-t border-amber-100 pt-2">
                        *Dana murni pasca potongan yang sah dan siap dicairkan untuk kebutuhan eksekusi program-program kerja ekstra.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-slate-50/50">
                <h2 class="font-bold text-gray-900 text-base tracking-tight">🔄 Laporan Realisasi Penggunaan Dana</h2>
                <p class="text-xs text-gray-500 mt-0.5">Daftar perbandingan laporan dana awal, dana terpakai di lapangan beserta rincian belanjanya.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase">
                            <th class="p-4">Tanggal Laporan</th>
                            <th class="p-4">Nama Program</th>
                            <th class="p-4">Dana Awal (Cair)</th>
                            <th class="p-4">Realisasi Terpakai</th>
                            <th class="p-4">Sisa Pengembalian</th>
                            <th class="p-4 text-center">Rincian Pemakaian</th>
                            <th class="p-4 text-center">Bukti Transfer</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(isset($riwayatDanaBackup) && count($riwayatDanaBackup) > 0)
                            @foreach($riwayatDanaBackup as $backup)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="p-4 text-gray-600 font-medium">
                                        {{ $backup->created_at ? $backup->created_at->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="p-4 font-bold text-gray-900">
                                        {{ $backup->pengajuan->extraProgram->name ?? 'Program Tidak Ditemukan' }}
                                    </td>
                                    <td class="p-4 text-slate-600 font-semibold">
                                        Rp {{ number_format($backup->pengajuan->nominal_diminta ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 text-blue-600 font-semibold">
                                        Rp {{ number_format($backup->total_terpakai ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 text-emerald-600 font-bold">
                                        Rp {{ number_format($backup->selisih ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <button onclick="bukaModalRincian('{{ $backup->pengajuan->extraProgram->name ?? 'Program' }}', '{{ json_encode($backup->pengajuan->items ?? []) }}')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3 py-1.5 rounded-lg border border-slate-300 transition shadow-sm cursor-pointer">
                                            📋 Lihat Rincian
                                        </button>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($backup->bukti_pengembalian)
                                            <a href="{{ asset('storage/' . $backup->bukti_pengembalian) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1.5 rounded-lg transition shadow-sm border border-indigo-100">
                                                🔍 Bukti Sisa
                                            </a>
                                        @else
                                            <span class="text-gray-400 italic text-xs">Tidak ada file</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-10 text-gray-400 text-sm">
                                    📭 Belum ada riwayat laporan realisasi penggunaan dana dari tim operasional.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-slate-50/50">
                <h2 class="font-bold text-gray-900 text-base tracking-tight">Permintaan Dana Awal (Bagian Operasional)</h2>
                <p class="text-xs text-gray-500 mt-0.5">Silakan transfer dana kerja awal ke rekening operasional di bawah ini, lalu upload bukti transfer untuk mencairkan status program.</p>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($antreanPencairan as $item)
                    <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-2">
                            <h4 class="text-sm font-bold text-gray-900">{{ $item->extraProgram->name }}</h4>
                            <div class="p-3 bg-slate-50 rounded-lg text-xs space-y-1 text-slate-600 border border-slate-200/60 max-w-md">
                                <p><strong>🏦 Rekening Tujuan:</strong> {{ $item->nama_bank }} — <span class="font-mono bg-white px-1 py-0.5 border border-gray-200 rounded font-bold">{{ $item->nomor_rekening }}</span></p>
                                <p><strong>👤 Atas Nama:</strong> {{ $item->account_name ?? ($item->staff->name ?? 'Tim Operasional') }}</p>
                            </div>
                            <p class="text-xs font-semibold text-gray-700">
                                Nominal Transfer: <span class="text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-md">Rp{{ number_format($item->nominal_diminta, 0, ',', '.') }}</span>
                            </p>
                        </div>

                        <form action="{{ route('keuangan.cairkan.proses', $item->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row sm:items-end gap-3 border border-dashed border-gray-200 p-4 rounded-xl bg-slate-50/30">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Upload Bukti Transfer</label>
                                <input type="file" name="bukti_transfer_pencairan" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-gray-800 transition cursor-pointer" required>
                            </div>
                            <button type="submit" class="bg-[#0b6e3f] hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition shadow-sm cursor-pointer whitespace-nowrap h-9">
                                Konfirmasi Cair 💰
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400 text-sm">
                        📭 Tidak ada antrean permintaan dana awal dari tim operasional saat ini.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-slate-50/50">
                <h2 class="font-bold text-gray-900 text-base tracking-tight">Status Capaian Dana Kelompok Program</h2>
                <p class="text-xs text-gray-500 mt-0.5">Akumulasi persentase kas terkumpul dibandingkan dengan target batas penggalangan dana beserta rincian alokasi fungsionalnya.</p>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($daftarProgram as $prog)
                    @php 
                        $persen = $prog->target_amount > 0 ? ($prog->current_amount / $prog->target_amount) * 100 : 0;
                        $persen = $persen > 100 ? 100 : $persen;

                        $danaTerikat = $prog->dana_operasional_ekstra;
                        $danaSiapSalur = $prog->dana_bersih_ekstra;
                    @endphp
                    <div class="p-4 rounded-xl border border-gray-100 bg-slate-50/30 space-y-4 shadow-inner flex flex-col justify-between">
                        <div class="space-y-4">
                            <div class="flex justify-between items-start gap-2">
                                <h4 class="font-bold text-gray-800 text-sm leading-tight">
                                    {{ $prog->name ?? ($prog->title ?? 'Program Infak Ekstra') }}
                                </h4>
                                @if($prog->current_amount > 0 && $prog->dana_bersih_ekstra == 0)
                                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 uppercase tracking-wider shrink-0 border border-emerald-200">
                                        ✓ Dana Sudah Dicairkan
                                    </span>
                                @else
                                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-600 uppercase tracking-wider shrink-0">
                                        {{ $prog->category ?? 'Ekstra' }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex justify-between text-xs font-medium text-slate-500 border-b border-gray-100 pb-2">
                                <span>Terkumpul: <b class="text-gray-700">Rp {{ number_format($prog->current_amount, 0, ',', '.') }}</b></span>
                                <span>Target: Rp {{ number_format($prog->target_amount, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="bg-white border border-gray-200/70 p-2.5 rounded-lg">
                                    <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wider">Dana Operasional (35%)</span>
                                    <span class="font-bold text-gray-600 block mt-0.5">
                                        Rp {{ number_format($danaTerikat, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="bg-emerald-50 border border-emerald-100 p-2.5 rounded-lg">
                                    <span class="text-[10px] uppercase font-bold text-emerald-600 block tracking-wider">Dana Terikat (65%)</span>
                                    <span class="font-extrabold text-[#0b6e3f] block mt-0.5">
                                        Rp {{ number_format($danaSiapSalur, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-1.5 pt-1">
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-[#0b6e3f] h-2 rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                                        {{ round($persen, 1) }}% Tercapai
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-400 text-sm">
                        Belum ada program infak ekstra terdaftar.
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <div id="modalRincian" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-50 items-center justify-center p-4 transition-opacity">
        <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-gray-100 flex flex-col max-h-[85vh]">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-slate-50 rounded-t-2xl">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Breakdown Detail Penggunaan Dana</h3>
                    <p id="modalNamaProgram" class="text-xs text-emerald-700 font-semibold mt-0.5">Nama Program</p>
                </div>
                <button onclick="tutupModalRincian()" class="text-gray-400 hover:text-gray-600 text-2xl font-semibold p-1 cursor-pointer">&times;</button>
            </div>
            
            <div class="p-6 overflow-y-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-400 font-bold uppercase tracking-wider">
                            <th class="pb-3 w-1/12 text-center">No</th>
                            <th class="pb-3 w-6/12">Deskripsi Keperluan / Alokasi Item</th>
                            <th class="pb-3 w-2/12 text-center">Kuantitas</th>
                            <th class="pb-3 w-3/12 text-right">Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody id="modalBodyItems" class="divide-y divide-gray-100 text-gray-700">
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-slate-50 border-t border-gray-100 text-right rounded-b-2xl">
                <button onclick="tutupModalRincian()" class="bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold px-4 py-2 rounded-lg transition cursor-pointer">
                    Tutup Laporan
                </button>
            </div>
        </div>
    </div>

    <script>
        function bukaModalRincian(namaProgram, itemsJson) {
            document.getElementById('modalNamaProgram').innerText = "📌 Kebutuhan Lapangan: " + namaProgram;
            const items = JSON.parse(itemsJson);
            const body = document.getElementById('modalBodyItems');
            body.innerHTML = "";

            if (!items || items.length === 0) {
                body.innerHTML = `<tr><td colspan="4" class="text-center py-6 text-gray-400 italic">Tidak ada catatan rincian item belanja fungsional.</td></tr>`;
            } else {
                items.forEach((item, index) => {
                    // 🎯 PERBAIKAN DI SINI: Ditambahkan item.uraian pada urutan paling depan agar memprioritaskan kolom database asli Anda
                    let namaItem = item.uraian || item.nama_item || item.deskripsi || item.keperluan || 'Item Pengeluaran';
                    let qty = item.qty || item.jumlah || 1;
                    let totalBiaya = item.total_harga || item.nominal || item.amount || 0;

                    let row = `
                        <tr class="hover:bg-slate-50/40">
                            <td class="py-3 text-center text-gray-400 font-medium">${index + 1}</td>
                            <td class="py-3 font-semibold text-gray-900">${namaItem}</td>
                            <td class="py-3 text-center font-mono bg-slate-50 rounded border border-gray-100 px-1">${qty}x</td>
                            <td class="py-3 text-right font-bold text-slate-900">Rp ${Number(totalBiaya).toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                    body.innerHTML += row;
                });
            }

            const modal = document.getElementById('modalRincian');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function tutupModalRincian() {
            const modal = document.getElementById('modalRincian');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</body>
</html>