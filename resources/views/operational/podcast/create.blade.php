<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjadwalan Podcast Baru - IKRA</title>
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
                    <a href="{{ route('operational.schedule') }}" class="flex items-center space-x-3 bg-emerald-600 text-white font-medium p-3 rounded-lg transition">
                        <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                        <span>Agenda & Jadwal</span>
                    </a>
                    <a href="{{ route('operational.pencairan') }}" class="flex items-center space-x-3 text-slate-300 hover:bg-slate-800 hover:text-white p-3 rounded-lg transition">
                        <i class="fa-solid fa-hand-holding-dollar w-5 text-center"></i>
                        <span>Pencairan Dana Ekstra</span>
                    </a>
                </nav>
            </div>
        </div>

        <div class="flex-1 p-10 max-w-5xl mx-auto">
            <div class="mb-8">
                <a href="{{ route('operational.schedule') }}" class="text-xs font-bold text-emerald-600 hover:underline">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Kalender Agenda
                </a>
                <h1 class="text-2xl font-black text-slate-900 mt-2">Penjadwalan & Pengajuan Dana Podcast</h1>
                <p class="text-xs text-slate-500 mt-1">Input rincian produksi konten kreatif beserta estimasi anggaran awal kerja.</p>
            </div>

            @if(session('success'))
                <div class="bg-emerald-100 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 text-sm font-bold">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('operational.podcast.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-5">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-50 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-microphone-lines text-emerald-500"></i> Detail Informasi Konten
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Judul / Episode Podcast</label>
                            <input type="text" name="title" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition" placeholder="Contoh: Ep. 15 - Literasi Keuangan Syariah" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Narasumber (Guest Star)</label>
                            <input type="text" name="guest_star" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500" placeholder="Nama Narasumber" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Host (Pembawa Acara)</label>
                            <input type="text" name="host" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500" placeholder="Nama Host" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Topik / Sinopsis Singkat Pembahasan</label>
                            <textarea name="topic" rows="3" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-emerald-500" placeholder="Tuliskan poin-poin utama yang akan dibahas..." required></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-5">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-50 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-clock text-emerald-500"></i> Waktu Produksi & Tayang
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Jadwal Rekaman (Taping)</label>
                            <input type="datetime-local" name="taping_date" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-emerald-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Estimasi Jadwal Tayang</label>
                            <input type="datetime-local" name="airing_date" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-emerald-500" required>
                        </div>
                    </div>
                </div>

                <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-6 space-y-5">
                    <h2 class="text-xs font-bold text-emerald-800 uppercase tracking-widest flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-transfer text-emerald-600"></i> Pengajuan Anggaran Produksi
                    </h2>
                    <div class="max-w-md">
                        <label class="block text-xs font-bold text-emerald-900 mb-1.5">Total Nominal Dana yang Diajukan (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-sm font-bold text-emerald-700">Rp</span>
                            <input type="number" name="amount_requested" class="w-full p-3 pl-12 bg-white border border-emerald-200 rounded-xl text-sm font-black text-slate-800 focus:outline-none focus:ring-4 focus:ring-emerald-500/10" placeholder="0" required>
                        </div>
                        <p class="text-[10px] text-emerald-600 mt-2 italic">*Estimasi biaya untuk narasumber, konsumsi tim, dan operasional lapangan.</p>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                    <a href="{{ route('operational.schedule') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold px-6 py-3 rounded-xl transition">
                        Batal
                    </a>
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-8 py-3 rounded-xl shadow-lg transition flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane text-[10px]"></i> Simpan & Ajukan ke Keuangan
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>