<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Keuangan - IKRA Padjadjaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <header class="bg-[#0b6e3f] text-white py-6 px-8 shadow-md">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Portal Keuangan Yayasan IKRA</h1>
                <p class="text-xs text-emerald-200 mt-0.5">Rekapitulasi Arus Penerimaan Infak Reguler Anggota</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="bg-emerald-800 text-emerald-100 text-xs font-semibold px-3 py-1.5 rounded-md border border-emerald-700">
                    Role: Staf Keuangan
                </span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-white hover:text-emerald-200 underline transition-all bg-transparent border-0 cursor-pointer">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-10 space-y-8">
        
        <div class="flex border-b border-gray-200">
            <a href="#" class="py-3 px-6 font-bold text-sm text-[#0b6e3f] border-b-2 border-[#0b6e3f] transition-all">
                💳 Infak Reguler (Bulanan)
            </a>
            <a href="{{ route('keuangan.infak-ekstra') }}" class="py-3 px-6 font-semibold text-sm text-gray-400 hover:text-gray-700 transition-all">
                🌟 Infak Ekstra (Khusus Program)
            </a>
            <a href="{{ route('keuangan.operasional') }}" class="py-3 px-6 font-semibold text-sm text-gray-400 hover:text-gray-700 transition-all">
                💼 Dana Operasional Kantor
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Total Pemasukan Reguler</span>
                    <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                        Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
                    </h3>
                    <span class="text-[11px] text-emerald-600 bg-emerald-50 font-medium px-2 py-0.5 rounded inline-block mt-1">
                        Semua Transaksi Sukses
                    </span>
                </div>
                <div class="bg-emerald-50 text-emerald-600 text-3xl w-14 h-14 flex items-center justify-center rounded-xl shadow-inner">
                    💰
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div class="space-y-1 flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Pemasukan Periode</span>
                        
                        <form method="GET" action="{{ route('keuangan.dashboard') }}" class="inline-block">
                            <select name="periode" onchange="this.form.submit()" class="bg-slate-100 text-xs font-bold border border-slate-200 text-gray-700 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                                <option value="{{ now()->format('Y-m') }}">{{ now()->format('Y-m') }} (Bulan Ini)</option>
                                @foreach($daftarPeriode as $p)
                                    @if($p !== now()->format('Y-m'))
                                        <option value="{{ $p }}" {{ $periodeDipilih == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <h3 class="text-3xl font-extrabold text-[#0b6e3f] tracking-tight pt-1">
                        Rp {{ number_format($totalPerPeriode, 0, ',', '.') }}
                    </h3>
                    <span class="text-[11px] text-slate-500 font-medium block mt-1">
                        Periode aktif terpilih: <strong class="text-gray-800">{{ $periodeDipilih }}</strong>
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/80 space-y-4">
            <div>
                <h2 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                    <span>🏢</span> Logika Alokasi Aturan Dana IKRA (Khusus Infak Reguler)
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Pemisahan otomatis berdasarkan persentase 35% kebutuhan operasional internal kantor dan 65% dana siap salur program reguler.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wide block">Alokasi Operasional Kantor (35%)</span>
                        <h4 class="text-xl font-bold text-slate-700 mt-1">
                            Rp {{ number_format($operasionalReguler, 0, ',', '.') }}
                        </h4>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-3 border-t border-slate-200/60 pt-2">
                        *Diambil dari total kumulatif dana masuk reguler untuk kebutuhan operasional kesekretariatan.
                    </p>
                </div>

                <div class="bg-emerald-50/60 border border-emerald-100 p-4 rounded-xl flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-[#0b6e3f] tracking-wide block">Dana Siap Salur Program Reguler (65%)</span>
                        <h4 class="text-xl font-bold text-[#0b6e3f] mt-1">
                            Rp {{ number_format($siapSalurReguler, 0, ',', '.') }}
                        </h4>
                    </div>
                    <p class="text-[11px] text-emerald-600/70 mt-3 border-t border-emerald-100 pt-2">
                        *Maksimal dana murni yang sah dan siap dicairkan oleh tim operasional untuk program kerja reguler.
                    </p>
                </div>
            </div>

            <div class="bg-blue-50/30 border border-blue-100 p-4 rounded-xl text-xs text-slate-600 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <span>📌 Breakdown Pembagian Khusus Periode <strong class="text-slate-800">{{ $periodeDipilih }}</strong>:</span>
                </div>
                <div class="flex flex-wrap gap-4 font-medium">
                    <div>Operasional (35%): <strong class="text-slate-800">Rp {{ number_format($operasionalPerPeriode, 0, ',', '.') }}</strong></div>
                    <div class="hidden sm:block text-slate-300">|</div>
                    <div>Siap Salur (65%): <strong class="text-emerald-700">Rp {{ number_format($siapSalurPerPeriode, 0, ',', '.') }}</strong></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-amber-50/40">
                <h2 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                    <span>💵</span> Permintaan Pencairan Dana Reguler (Validasi Pembina)
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Daftar proposal penyaluran dari divisi operasional yang telah disetujui Pembina dan siap ditransfer.</p>
            </div>
            
            <div class="p-6">
                @if(!isset($pengajuanReguler) || $pengajuanReguler->isEmpty())
                    <div class="text-center py-8 text-gray-400 text-sm">
                        <span class="text-2xl block mb-1">🕊️</span>
                        Belum ada proposal reguler disetujui yang menunggu pencairan.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-gray-200 text-slate-500 text-xs font-bold uppercase">
                                    <th class="py-3 px-4">Nama Program</th>
                                    <th class="py-3 px-4">Target Penerima</th>
                                    <th class="py-3 px-4">Nominal</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-center">Aksi Pencairan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach($pengajuanReguler as $p)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-4 px-4 font-semibold text-gray-900 align-top">
                                            <div class="font-bold text-slate-800">{{ $p->nama_program }}</div>
                                            @php
                                                $rincianData = json_decode($p->rincian_detail, true);
                                                $isJson = is_array($rincianData) && count($rincianData) > 0;
                                            @endphp
                                            @if($isJson)
                                                <div class="mt-2 border border-slate-200 rounded-lg overflow-hidden text-[10px]">
                                                    <table class="w-full">
                                                        <thead class="bg-slate-100 text-slate-500 font-bold uppercase">
                                                            <tr>
                                                                <th class="py-1.5 px-2 text-left">Nama</th>
                                                                <th class="py-1.5 px-1 text-center">Qty</th>
                                                                <th class="py-1.5 px-1 text-center">Satuan</th>
                                                                <th class="py-1.5 px-2 text-right">Harga</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-slate-100">
                                                            @foreach($rincianData as $baris)
                                                                <tr class="text-slate-600">
                                                                    <td class="py-1 px-2 font-medium">{{ $baris['nama'] ?? ($baris['uraian'] ?? '-') }}</td>
                                                                    <td class="py-1 px-1 text-center">{{ $baris['qty'] ?? ($baris['kuantitas'] ?? '-') }}</td>
                                                                    <td class="py-1 px-1 text-center">{{ $baris['satuan'] ?? '-' }}</td>
                                                                    <td class="py-1 px-2 text-right font-mono font-bold text-slate-700">Rp {{ number_format($baris['harga'] ?? ($baris['harga_satuan'] ?? 0), 0, ',', '.') }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot class="bg-emerald-50 border-t-2 border-emerald-200">
                                                            <tr>
                                                                <td colspan="3" class="py-1.5 px-2 font-bold text-emerald-800 uppercase tracking-wider">Total</td>
                                                                <td class="py-1.5 px-2 text-right font-black text-emerald-700 font-mono">
                                                                    Rp {{ number_format(collect($rincianData)->sum(fn($b) => $b['harga'] ?? ($b['harga_satuan'] ?? 0)), 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            @else
                                                <span class="block text-[11px] font-normal text-gray-500 mt-0.5">{{ $p->rincian_detail }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-gray-600 text-xs">{{ $p->penerima_manfaat }}</td>
                                        <td class="py-4 px-4 font-bold text-indigo-700">Rp {{ number_format($p->nominal_diajukan, 0, ',', '.') }}</td>
                                        <td class="py-4 px-4">
                                            @if($p->status === 'disetujui')
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-blue-50 text-blue-700 border border-blue-200 uppercase">Siap Cair</span>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">Selesai Ditransfer</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 w-64">
                                            @if($p->status === 'disetujui')
                                                <form action="{{ route('keuangan.penyaluran-reguler.cairkan', $p->id) }}" method="POST" enctype="multipart/form-data" class="space-y-1.5 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                                    @csrf
                                                    <label class="text-[10px] font-bold text-gray-400 uppercase block">Upload Bukti Transfer:</label>
                                                    <input type="file" name="bukti_transfer" required class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                                                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-1.5 px-3 rounded shadow-sm transition-all cursor-pointer">
                                                        Confirm Kirim Dana
                                                    </button>
                                                </form>
                                            @else
                                                <div class="text-center">
                                                    <a href="{{ asset('uploads/bukti_transfer/' . $p->bukti_transfer) }}" target="_blank" class="text-xs text-emerald-600 font-medium hover:underline flex items-center justify-center gap-1">
                                                        👁️ Lihat Bukti Transfer
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-slate-50/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">Riwayat Transaksi Real-Time (Reguler)</h2>
                    <p class="text-xs text-gray-500">Mutasi otomatis pelaporan webhook dari jaringan Sandbox Xendit khusus tipe reguler</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/70 border-b border-gray-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <th class="py-4 px-6">Nama Anggota</th>
                            <th class="py-4 px-6">No. VA Muamalat</th>
                            <th class="py-4 px-6">Nominal Infak</th>
                            <th class="py-4 px-6">Periode Tagihan</th>
                            <th class="py-4 px-6">Tanggal Sukses</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-semibold text-gray-900">
                                {{ $trx->member->nama ?? 'Nama Tidak Terdeteksi' }}
                            </td>
                            <td class="py-4 px-6 font-mono text-xs text-slate-600">
                                {{ $trx->account_number }}
                            </td>
                            <td class="py-4 px-6 font-bold text-emerald-700">
                                Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                    {{ $trx->periode }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs text-gray-500">
                                {{ is_string($trx->created_at) ? $trx->created_at : $trx->created_at->format('d M Y - H:i') }} WIB
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400 font-medium bg-slate-50/30">
                                <span class="text-2xl block mb-2">📁</span>
                                Belum ada mutasi infak reguler yang masuk di database.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===================================================
             SECTION BARU: ANTREAN REIMBURSE DANA REGULER
             Tampil jika ada program yg kurang dana & butuh reimburse
             =================================================== --}}
        @if(isset($antreanRegulerReimburse) && $antreanRegulerReimburse->count() > 0)
        <div class="bg-white rounded-2xl border border-rose-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-rose-100 bg-rose-50/40">
                <h2 class="font-bold text-rose-800 text-base tracking-tight flex items-center gap-2">
                    <span>🚨</span> Antrean Reimburse Dana Reguler
                </h2>
                <p class="text-xs text-rose-600 mt-0.5">Program berikut mengalami kekurangan dana di lapangan. Silakan transfer kekurangan dana dan upload bukti untuk menyelesaikannya.</p>
            </div>
            <div class="divide-y divide-rose-50">
                @foreach($antreanRegulerReimburse as $prog)
                <div class="p-6 flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                    <div class="space-y-2 flex-1">
                        <h4 class="text-sm font-bold text-gray-900">{{ $prog->nama_program }}</h4>
                        <div class="text-xs text-gray-500">
                            <span class="font-semibold text-gray-700">Periode:</span> {{ $prog->periode_bulan }} &nbsp;|&nbsp;
                            <span class="font-semibold text-gray-700">Penerima:</span> {{ $prog->penerima_manfaat }}
                        </div>
                        <div class="flex flex-wrap gap-3 text-xs font-medium">
                            <div class="bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-lg">
                                Dana Dicairkan: <strong class="text-slate-800">Rp {{ number_format($prog->nominal_diajukan, 0, ',', '.') }}</strong>
                            </div>
                            @if($prog->laporan)
                            <div class="bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-lg">
                                Realisasi Terpakai: <strong class="text-blue-800">Rp {{ number_format($prog->laporan->total_pengeluaran, 0, ',', '.') }}</strong>
                            </div>
                            <div class="bg-rose-50 border border-rose-200 px-3 py-1.5 rounded-lg">
                                Kekurangan Dana: <strong class="text-rose-800">Rp {{ number_format(abs($prog->laporan->selisih_dana), 0, ',', '.') }}</strong>
                            </div>
                            @endif
                        </div>
                        <p class="text-[11px] text-rose-600 bg-rose-50 border border-rose-100 px-2.5 py-1.5 rounded-lg inline-block font-medium">
                            ⚠️ Dana reguler bulan <strong>{{ $prog->periode_bulan }}</strong> akan dipotong sebesar nominal kekurangan di atas.
                        </p>
                    </div>

                    <form action="{{ route('keuangan.penyaluran-reguler.reimburse', $prog->id) }}" method="POST" enctype="multipart/form-data"
                          class="flex flex-col sm:flex-row sm:items-end gap-3 border border-dashed border-rose-200 p-4 rounded-xl bg-rose-50/20 min-w-0 lg:min-w-[320px]">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-[10px] font-bold text-rose-600 uppercase tracking-wider mb-1">Upload Bukti Transfer Reimburse</label>
                            <input type="file" name="bukti_transfer_reimburse" required
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-rose-700 file:text-white hover:file:bg-rose-800 transition cursor-pointer">
                        </div>
                        <button type="submit"
                                class="bg-rose-700 hover:bg-rose-800 text-white text-xs font-bold px-4 py-2 rounded-lg transition shadow-sm cursor-pointer whitespace-nowrap h-9">
                            Konfirmasi Reimburse 💸
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ===================================================
             SECTION BARU: LAPORAN REALISASI PENGGUNAAN DANA REGULER
             Menampilkan semua LPJ yang sudah disubmit oleh operasional
             =================================================== --}}
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-slate-50/50">
                <h2 class="font-bold text-gray-900 text-base tracking-tight flex items-center gap-2">
                    <span>🔄</span> Laporan Realisasi Penggunaan Dana Reguler
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Rekap perbandingan dana awal yang dicairkan vs. realisasi penggunaan dana lapangan dari tim operasional.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase">
                            <th class="p-4">Tgl. Laporan</th>
                            <th class="p-4">Nama Program / Periode</th>
                            <th class="p-4">Dana Awal (Cair)</th>
                            <th class="p-4">Realisasi Terpakai</th>
                            <th class="p-4">Sisa / Kurang</th>
                            <th class="p-4 text-center">Rincian Nota</th>
                            <th class="p-4 text-center">Bukti Sisa</th>
                            <th class="p-4 text-center">Cetak Laporan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(isset($riwayatLaporanReguler) && $riwayatLaporanReguler->count() > 0)
                            @foreach($riwayatLaporanReguler as $lpj)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-4 text-gray-600 font-medium text-xs">
                                    {{ $lpj->created_at ? $lpj->created_at->format('d M Y') : '-' }}
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-gray-900 text-sm">{{ $lpj->penyaluranReguler->nama_program ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Periode: {{ $lpj->penyaluranReguler->periode_bulan ?? '-' }}
                                        @if($lpj->penyaluranReguler && $lpj->penyaluranReguler->status === 'reimburse_pending')
                                            <span class="ml-1 bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded text-[10px] font-bold">Reimburse Pending</span>
                                        @elseif($lpj->penyaluranReguler && $lpj->penyaluranReguler->status === 'dilaporkan')
                                            <span class="ml-1 bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded text-[10px] font-bold">✅ Selesai</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4 text-slate-600 font-semibold text-sm">
                                    Rp {{ number_format($lpj->penyaluranReguler->nominal_diajukan ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="p-4 text-blue-600 font-semibold text-sm">
                                    Rp {{ number_format($lpj->total_pengeluaran, 0, ',', '.') }}
                                </td>
                                <td class="p-4 font-bold text-sm">
                                    @if($lpj->selisih_dana > 0)
                                        <span class="text-emerald-600">+Rp {{ number_format($lpj->selisih_dana, 0, ',', '.') }}</span>
                                        <div class="text-[10px] text-emerald-500 font-normal">Kelebihan (Dikembalikan)</div>
                                    @elseif($lpj->selisih_dana < 0)
                                        <span class="text-rose-600">-Rp {{ number_format(abs($lpj->selisih_dana), 0, ',', '.') }}</span>
                                        <div class="text-[10px] text-rose-500 font-normal">Kurang Dana</div>
                                    @else
                                        <span class="text-slate-500">Pas / Seimbang</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <button onclick="bukaModalRincianReguler('{{ addslashes($lpj->penyaluranReguler->nama_program ?? 'Program') }}', '{{ json_encode($lpj->items_nota ?? []) }}')"
                                            class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3 py-1.5 rounded-lg border border-slate-300 transition shadow-sm cursor-pointer">
                                        📋 Lihat Rincian
                                    </button>
                                </td>
                                <td class="p-4 text-center">
                                    @if($lpj->bukti_pengembalian_sisa)
                                        <a href="{{ asset('storage/' . $lpj->bukti_pengembalian_sisa) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1.5 rounded-lg transition shadow-sm border border-indigo-100">
                                            🔍 Bukti Sisa
                                        </a>
                                    @elseif($lpj->bukti_reimburse)
                                        <a href="{{ asset('storage/' . $lpj->bukti_reimburse) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold px-3 py-1.5 rounded-lg transition shadow-sm border border-rose-100">
                                            🧾 Bukti Reimburse
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic text-xs">—</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <a href="{{ route('keuangan.penyaluran-reguler.cetak', $lpj->id) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm transition cursor-pointer">
                                        🖨️ Cetak Draft
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-10 text-gray-400 text-sm">
                                    📭 Belum ada laporan realisasi penggunaan dana reguler dari tim operasional.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    {{-- Modal Rincian Nota Reguler --}}
    <div id="modalRincianReguler" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden z-50 items-center justify-center p-4 transition-opacity">
        <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-gray-100 flex flex-col max-h-[85vh]">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-slate-50 rounded-t-2xl">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Breakdown Detail Penggunaan Dana</h3>
                    <p id="modalNamaProgramReguler" class="text-xs text-emerald-700 font-semibold mt-0.5">Nama Program</p>
                </div>
                <button onclick="tutupModalRincianReguler()" class="text-gray-400 hover:text-gray-600 text-2xl font-semibold p-1 cursor-pointer">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-400 font-bold uppercase tracking-wider">
                            <th class="pb-3 w-1/12 text-center">No</th>
                            <th class="pb-3 w-5/12">Tanggal Nota</th>
                            <th class="pb-3 w-4/12">Uraian Belanja</th>
                            <th class="pb-3 w-2/12 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody id="modalBodyReguler" class="divide-y divide-gray-100 text-gray-700">
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-slate-50 border-t border-gray-100 text-right rounded-b-2xl">
                <button onclick="tutupModalRincianReguler()" class="bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold px-4 py-2 rounded-lg transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function bukaModalRincianReguler(namaProgram, itemsJson) {
            document.getElementById('modalNamaProgramReguler').innerText = "📌 " + namaProgram;
            const items = JSON.parse(itemsJson);
            const body = document.getElementById('modalBodyReguler');
            body.innerHTML = "";

            if (!items || items.length === 0) {
                body.innerHTML = `<tr><td colspan="4" class="text-center py-6 text-gray-400 italic">Tidak ada catatan rincian nota belanja.</td></tr>`;
            } else {
                items.forEach((item, index) => {
                    const row = `
                        <tr class="hover:bg-slate-50/40">
                            <td class="py-3 text-center text-gray-400 font-medium">${index + 1}</td>
                            <td class="py-3 font-mono text-gray-600">${item.tanggal ?? '-'}</td>
                            <td class="py-3 font-semibold text-gray-900">${item.uraian ?? '-'}</td>
                            <td class="py-3 text-right font-bold text-slate-900">Rp ${Number(item.nominal ?? 0).toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                    body.innerHTML += row;
                });
            }

            const modal = document.getElementById('modalRincianReguler');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function tutupModalRincianReguler() {
            const modal = document.getElementById('modalRincianReguler');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>

</body>
</html>