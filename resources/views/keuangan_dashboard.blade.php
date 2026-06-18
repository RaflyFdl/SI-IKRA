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
                <a href="/" class="text-sm font-medium text-white hover:text-emerald-200 underline transition-all">Logout</a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-10 space-y-8">
        
        <!-- NAVIGASI TAB MENU 3 PILIHAN SINKRON -->
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
                <h2 class="text-base font-bold text-gray-990 tracking-tight flex items-center gap-2">
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

    </main>

</body>
</html>