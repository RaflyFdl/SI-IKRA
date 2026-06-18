<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Dana Operasional - IKRA Padjadjaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <header class="bg-[#0b6e3f] text-white py-6 px-8 shadow-md">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Portal Keuangan Yayasan IKRA</h1>
                <p class="text-xs text-emerald-200 mt-0.5">Manajemen dan Pemantauan Khusus Kantong Dana Operasional Internal</p>
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
            <a href="{{ route('keuangan.infak-ekstra') }}" class="py-3 px-6 font-semibold text-sm text-gray-400 hover:text-gray-700 transition-all">
                🌟 Infak Ekstra (Khusus Program)
            </a>
            <a href="{{ route('keuangan.operasional') }}" class="py-3 px-6 font-semibold text-sm text-gray-400 hover:text-gray-700 transition-all">
                💼 Dana Operasional Kantor
            </a>
        </div>

        <div class="bg-gradient-to-r from-slate-800 to-slate-900 p-8 rounded-2xl shadow-lg text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400 block">Total Brankas Operasional Yayasan (35%)</span>
                <h3 class="text-4xl font-extrabold tracking-tight mt-1 text-amber-400">
                    Rp {{ number_format($totalOperasionalGabungan, 0, ',', '.') }}
                </h3>
                <p class="text-xs text-slate-400 mt-2 max-w-xl">
                    Akumulasi dana murni yang bersumber dari potongan sah 35% seluruh pemasukan (Infak Reguler & Infak Ekstra) untuk membiayai kebutuhan kesekretariatan, listrik, maintenance sistem, dan operasional harian kantor.
                </p>
            </div>
            <div class="bg-slate-700/50 border border-slate-600/50 p-4 rounded-xl text-center shrink-0 w-full md:w-auto">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Status Saldo</span>
                <div class="text-emerald-400 font-bold text-sm mt-1 flex items-center justify-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span> Ready to Use
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/80 flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Porsi Operasional Reguler</span>
                        <span class="text-[10px] bg-blue-50 text-blue-600 font-extrabold px-2 py-0.5 rounded text-right">35% CUT</span>
                    </div>
                    <h4 class="text-2xl font-extrabold text-gray-900 mt-2">
                        Rp {{ number_format($operasionalReguler, 0, ',', '.') }}
                    </h4>
                </div>
                <p class="text-xs text-gray-400 pt-3 border-t border-gray-100">
                    Disuplai otomatis dari pemotongan rutin infak bulanan/reguler para jemaah/member IKRA.
                </p>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200/80 flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Porsi Operasional Ekstra</span>
                        <span class="text-[10px] bg-amber-50 text-amber-600 font-extrabold px-2 py-0.5 rounded text-right">35% CUT</span>
                    </div>
                    <h4 class="text-2xl font-extrabold text-gray-900 mt-2">
                        Rp {{ number_format($operasionalEkstra, 0, ',', '.') }}
                    </h4>
                </div>
                <p class="text-xs text-gray-400 pt-3 border-t border-gray-100">
                    Disuplai dari potongan tiap program penggalangan dana temporer (Infak Ekstra) yang berjalan.
                </p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-slate-50/50">
                <h2 class="font-bold text-gray-900 text-base tracking-tight">Pemberitahuan Sistem Otomasi</h2>
                <p class="text-xs text-gray-500 mt-0.5">Regulasi pencatatan arus keluar masuk akun operasional internal yayasan.</p>
            </div>
            <div class="p-6 text-center py-12 text-gray-400 text-sm">
                <div class="text-3xl mb-2">🔒</div>
                <p class="font-semibold text-gray-700">Fitur Pencatatan Pengeluaran (Beban Operasional)</p>
                <p class="text-xs text-gray-400 max-w-md mx-auto mt-1">
                    Halaman ini dikunci sementara untuk proses *read-only* kalkulasi masuk. Form input pengeluaran kwitansi/nota operasional bisa kamu hubungkan ke tabel pengeluaran kas di tahap inkremen berikutnya.
                </p>
            </div>
        </div>

    </main>

</body>
</html>