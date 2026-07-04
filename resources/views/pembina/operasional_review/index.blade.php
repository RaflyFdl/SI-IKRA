<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Operasional - Pembina IKRA</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 font-sans text-slate-800">

    <div class="flex min-h-screen">
        <div class="w-64 bg-slate-900 text-white p-6 space-y-6 flex flex-col justify-between shrink-0">
            <div class="space-y-6">
                <div class="flex items-center space-x-3 border-b border-slate-700 pb-4">
                    <div class="bg-indigo-500 p-2 rounded-lg text-white font-bold">PB</div>
                    <div>
                        <h1 class="font-bold text-lg leading-tight">IKRA System</h1>
                        <p class="text-xs text-slate-400">Pembina</p>
                    </div>
                </div>
                
                <nav class="space-y-2">
                    <a href="{{ route('pembina.dashboard') }}" class="flex items-center space-x-3 {{ request()->routeIs('pembina.dashboard') ? 'bg-indigo-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-3 rounded-lg transition">
                        <i class="fa-solid fa-file-signature w-5 text-center"></i>
                        <span>Persetujuan Program</span>
                    </a>

                    <a href="{{ route('pembina.operasional.review') }}" class="flex items-center space-x-3 {{ request()->routeIs('pembina.operasional.*') ? 'bg-purple-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-3 rounded-lg transition">
                        <i class="fa-solid fa-building-user w-5 text-center"></i>
                        <span>Persetujuan Operasional</span>
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

        <div class="flex-1 p-10 overflow-y-auto">
            
            <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm mb-8">
                <h1 class="text-2xl font-black text-slate-900">Persetujuan Dana Operasional</h1>
                <p class="text-sm text-slate-500 mt-1">Tinjau rincian kebutuhan internal sebelum dana dicairkan oleh bagian keuangan.</p>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-xl mb-6 font-bold text-sm shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            <div class="space-y-6">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-amber-500"></i> Antrean Persetujuan ({{ $antrean->count() }})
                </h2>

                @if($antrean->isEmpty())
                    <div class="bg-white border border-slate-200 rounded-2xl p-10 text-center shadow-sm">
                        <span class="text-4xl block mb-3">☕</span>
                        <p class="text-slate-400 text-sm">Tidak ada antrean pengajuan saat ini. Semua sudah diproses.</p>
                    </div>
                @else
                    @foreach($antrean as $item)
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                                <div>
                                    <h3 class="font-bold text-slate-800 text-lg">{{ $item->title }}</h3>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight mt-1">Diajukan Tanggal: {{ $item->created_at->format('d M Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block mb-1">Total Pengajuan</span>
                                    <span class="text-xl font-black text-slate-900">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="p-5">
                                <table class="w-full text-sm text-left">
                                    <thead>
                                        <tr class="text-[10px] uppercase text-slate-400 border-b border-slate-100">
                                            <th class="py-2">Uraian Kebutuhan</th>
                                            <th class="py-2 text-right">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-slate-600">
                                        @foreach($item->items as $sub)
                                            <tr class="border-b border-slate-50 last:border-0">
                                                <td class="py-3">{{ $sub->description }}</td>
                                                <td class="py-3 text-right font-bold text-slate-700">Rp {{ number_format($sub->amount, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="mt-6 flex justify-end gap-3 border-t border-slate-100 pt-4">
                                    <form action="{{ route('pembina.operasional.reject', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-6 py-2 border border-rose-200 text-rose-600 font-bold text-xs rounded-xl hover:bg-rose-50 transition cursor-pointer">
                                            Tolak Pengajuan
                                        </button>
                                    </form>
                                    <form action="{{ route('pembina.operasional.approve', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-8 py-2 bg-slate-900 text-white font-bold text-xs rounded-xl hover:bg-slate-800 transition shadow-lg cursor-pointer">
                                            Setujui & Teruskan ke Keuangan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

        </div>
    </div>

</body>
</html>