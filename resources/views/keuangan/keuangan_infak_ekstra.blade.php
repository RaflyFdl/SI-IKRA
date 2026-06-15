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
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Total Dana Ekstra Masuk</span>
                    <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                        Rp {{ number_format($totalEkstra, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl font-bold text-xs uppercase tracking-wider">
                    🔄 Realtime
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block">Program Publikasi Aktif</span>
                    <h3 class="text-3xl font-extrabold text-indigo-600 tracking-tight mt-1">
                        {{ count($daftarProgram) }} Program
                    </h3>
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl font-bold text-xs uppercase tracking-wider">
                    Analis Data
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-slate-50/50">
                <h2 class="font-bold text-gray-900 text-base tracking-tight">Status Capaian Dana Kelompok Program</h2>
                <p class="text-xs text-gray-500 mt-0.5">Akumulasi persentase kas terkumpul dibandingkan dengan target batas penggalangan dana.</p>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($daftarProgram as $prog)
                    <div class="p-4 rounded-xl border border-gray-100 bg-slate-50/30 space-y-3 shadow-inner">
                        <div class="flex justify-between items-start gap-2">
                            <h4 class="font-bold text-gray-800 text-sm leading-tight">
                                {{ $prog->name ?? ($prog->title ?? 'Program Infak Ekstra') }}
                            </h4>
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-600 uppercase tracking-wider shrink-0">
                                {{ $prog->category ?? 'Ekstra' }}
                            </span>
                        </div>
                        <div class="flex justify-between text-xs font-medium text-slate-500">
                            <span>Terkumpul: <b class="text-gray-700">Rp {{ number_format($prog->current_amount, 0, ',', '.') }}</b></span>
                            <span>Target: Rp {{ number_format($prog->target_amount, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            @php 
                                $persen = $prog->target_amount > 0 ? ($prog->current_amount / $prog->target_amount) * 100 : 0;
                                $persen = $persen > 100 ? 100 : $persen;
                            @endphp
                            <div class="bg-[#0b6e3f] h-2 rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                                {{ round($persen, 1) }}% Tercapai
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-6 text-xs font-medium text-gray-400">
                        Belum ada data target capaian program yang terdaftar di database.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="font-bold text-gray-900 text-base tracking-tight">Riwayat Transaksi Masuk (Mutasi Ekstra)</h2>
                <span class="text-xs bg-gray-100 text-gray-600 font-medium px-2.5 py-1 rounded-lg">
                    Total: {{ count($transactions) }} Mutasi
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/70 border-b border-gray-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <th class="p-4">Tanggal Masuk</th>
                            <th class="p-4">Target Program Donasi</th>
                            <th class="p-4">ID Referensi Xendit</th>
                            <th class="p-4">Nomor VA Tujuan</th>
                            <th class="p-4">Nominal</th>
                            <th class="p-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100 bg-white">
                        @forelse($transactions as $trx)
                            <tr class="hover:bg-slate-50/80 transition-colors duration-150">
                                <td class="p-4 text-gray-500 whitespace-nowrap text-xs">
                                    {{ is_string($trx->created_at) ? $trx->created_at : $trx->created_at->format('d M Y, H:i') }} WIB
                                </td>
                                
                                <td class="p-4">
                                    <span class="font-semibold text-gray-900 block text-sm">
                                        {{ $trx->extraProgram->name ?? ($trx->extraProgram->title ?? 'Program Infak Ekstra') }}
                                    </span>
                                </td>
                                
                                <td class="p-4 font-mono text-xs text-gray-400">
                                    {{ $trx->payment_id ?? $trx->external_id }}
                                </td>
                                
                                <td class="p-4 font-mono text-xs text-slate-600 tracking-wider">
                                    {{ $trx->account_number }}
                                </td>
                                
                                <td class="p-4 font-bold text-amber-600 whitespace-nowrap">
                                    + Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </td>
                                
                                <td class="p-4 text-center">
                                    <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-wide inline-block">
                                        Sukses
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-gray-400 text-sm bg-slate-50/30">
                                    <div class="text-2xl mb-1">📊</div>
                                    <p class="font-medium text-gray-500">Belum Ada Transaksi</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Belum ada riwayat dana masuk untuk program infak ekstra saat ini.</p>
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