<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publikasi Program Infak Ekstra - SI Infak IKRA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800">

<div class="p-8">
    <div class="max-w-6xl mx-auto">
        
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Publikasi Program Infak Ekstra</h1>
                <p class="text-sm text-slate-500">Buat program baru dan generate Virtual Account secara otomatis.</p>
            </div>
            <div>
                <a href="{{ route('register') }}" class="inline-block border border-slate-300 text-slate-600 bg-white hover:bg-slate-50 font-medium text-xs px-4 py-2.5 rounded-xl transition shadow-sm" target="_blank">
                    Buka Form Daftar ↗
                </a>
            </div>
        </div>

        <div class="flex gap-6 mb-8 border-b border-slate-200 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="font-medium text-slate-400 hover:text-slate-600 pb-3 transition">
                👥 Verifikasi Pendaftar (Rutin)
            </a>
            <a href="{{ route('admin.programs.index') }}" class="font-bold text-slate-900 border-b-2 border-slate-900 pb-3">
                📢 Publikasi Program Infak Ekstra
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-semibold shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm h-fit">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Input Program Baru</h2>
                <form action="{{ route('admin.programs.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Nama Program</label>
                            <input type="text" name="name" required class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-slate-900 text-slate-800" placeholder="Contoh: Wakaf Masjid Al-Afiati">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Deskripsi Program</label>
                            <textarea name="description" rows="4" required class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-slate-900 text-slate-800" placeholder="Tuliskan detail peruntukan dana..."></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Target Pendanaan (Rp)</label>
                            <input type="number" name="target_amount" required class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-slate-900 text-slate-800" placeholder="100000000">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Batas Waktu (End Date)</label>
                            <input type="date" name="end_date" required class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-slate-900 text-slate-800">
                        </div>
                        <button type="submit" class="w-full bg-slate-950 hover:bg-slate-800 text-white font-bold text-sm py-3 rounded-xl transition mt-2 shadow-sm cursor-pointer">
                            Simpan & Buat VA Otomatis
                        </button>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Daftar Program Saat Ini</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                <th class="p-4 w-2/5">Nama Program</th>
                                <th class="p-4 w-1/5">Target Dana</th>
                                <th class="p-4 w-1/5">Nomor VA Xendit</th>
                                <th class="p-4 w-1/5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($programs as $program)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="p-4">
                                        <span class="font-bold text-slate-800 block text-sm">{{ $program->name }}</span>
                                        <span class="text-xs text-slate-400">Hingga: {{ \Carbon\Carbon::parse($program->end_date)->format('d M Y') }}</span>
                                    </td>
                                    <td class="p-4 text-sm font-semibold text-slate-600">
                                        Rp {{ number_format($program->target_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 font-mono text-xs text-indigo-600 font-bold tracking-wider">
                                        {{ $program->va_number ?? 'Sedang diproses...' }}
                                    </td>
                                    <td class="p-4">
                                        @if($program->status == 'active')
                                            <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-wide">Aktif</span>
                                        @else
                                            <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase tracking-wide">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-slate-400 text-sm">Belum ada program infak ekstra yang dibuat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>

</body>
</html>