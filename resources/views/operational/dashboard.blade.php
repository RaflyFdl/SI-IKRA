<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Jadwal Operasional - IKRA</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 font-sans text-slate-800">

    @php
        $activeTab = request()->query('tab', $activeTab ?? 'donasi');
    @endphp

    <div class="flex min-h-screen">
        <div class="w-64 bg-slate-900 text-white p-6 space-y-6 flex flex-col justify-between">
            <div class="space-y-6">
                <div class="flex items-center space-x-3 border-b border-slate-700 pb-4">
                    <div class="bg-emerald-500 p-2 rounded-lg text-slate-900 font-bold">IK</div>
                    <div>
                        <h1 class="font-bold text-lg leading-tight">IKRA System</h1>
                        <p class="text-xs text-slate-400">Tim Operasional</p>
                    </div>
                </div>
                
                <nav class="space-y-2">
                    <a href="{{ route('operational.dashboard') }}" class="flex items-center space-x-3 {{ request()->routeIs('operational.dashboard') && !request()->routeIs('operational.pencairan') ? 'bg-emerald-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-3 rounded-lg transition">
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
                    <h2 class="text-3xl font-bold text-slate-900">Pusat Kendali & Penjadwalan Kerja</h2>
                    <p class="text-slate-500 mt-1">Atur tanggal pelaksanaan program infak ekstra dan unggah dokumentasi lapangan.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center space-x-3 shadow-sm">
                    <span>✅</span>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <div class="flex bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm mb-6 max-w-md">
                <a href="?tab=donasi" class="flex-1 text-center py-2.5 rounded-lg text-sm font-bold transition {{ $activeTab === 'donasi' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                    Donasi Umum
                </a>
                <a href="?tab=podcast" class="flex-1 text-center py-2.5 rounded-lg text-sm font-bold transition {{ $activeTab === 'podcast' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                    Podcast
                </a>
                <a href="?tab=cinema" class="flex-1 text-center py-2.5 rounded-lg text-sm font-bold transition {{ $activeTab === 'cinema' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                    Cinema Edukasi
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-slate-900 uppercase text-xs tracking-wider">
                        Daftar Program Kerja (Kategori: {{ $activeTab == 'donasi' ? 'Donasi Umum' : ($activeTab == 'podcast' ? 'Medical Podcast' : 'Cinema Edukasi') }})
                    </h3>
                </div>

                @php
                    $currentPrograms = $activeTab === 'donasi' ? ($donasiPrograms ?? collect()) : ($activeTab === 'podcast' ? ($podcastPrograms ?? collect()) : ($cinemaPrograms ?? collect()));
                @endphp

                @if($currentPrograms->isEmpty())
                    <div class="text-center py-16 px-4 space-y-3">
                        <span class="text-4xl block">📂</span>
                        <p class="text-slate-400 text-sm font-medium">Belum ada program infak ekstra di kategori ini.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($currentPrograms as $program)
                            <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-6 transition hover:bg-slate-50/50">
                                
                                <div class="space-y-2 max-w-xl">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="text-base font-bold text-slate-900">{{ $program->name }}</h4>
                                        @if($program->status === 'completed')
                                            <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-200">SELESAI LULUS</span>
                                        @elseif($program->execution_date)
                                            <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-full border border-blue-200">TERJADWAL</span>
                                        @else
                                            <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200">BUTUH TANGGAL</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $program->description }}</p>
                                    
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400 font-medium pt-1">
                                        <div><i class="fa-solid fa-calendar text-slate-400 mr-1.5"></i>Eksekusi: <span class="text-slate-600 font-bold">{{ $program->execution_date ? date('d M Y', strtotime($program->execution_date)) : 'Belum Atur' }}</span></div>
                                        @if($program->target_amount)
                                            <div><i class="fa-solid fa-bullseye text-slate-400 mr-1.5"></i>Target: <span class="text-slate-600 font-bold">Rp{{ number_format($program->target_amount, 0, ',', '.') }}</span></div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-3">
                                    @if($program->status !== 'completed')
                                        <form action="{{ route('operational.update-date', $program->id) }}" method="POST" class="flex items-center space-x-2 bg-slate-50 p-2 rounded-xl border border-slate-200">
                                            @csrf
                                            <input type="date" name="execution_date" value="{{ $program->execution_date }}" class="bg-white text-xs font-semibold p-2 rounded-lg border border-slate-200 focus:outline-none focus:border-emerald-600" required>
                                            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold px-3 py-2 rounded-lg transition shadow-sm cursor-pointer">
                                                Simpan Hari
                                            </button>
                                        </form>

                                        @if($program->execution_date)
                                            <div class="flex items-center space-x-2 bg-slate-50 p-2 rounded-xl border border-slate-200">
                                                <form action="{{ route('operational.complete', $program->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2">
                                                    @csrf
                                                    <label class="block">
                                                        <span class="sr-only">Pilih documentation</span>
                                                        <input type="file" name="documentation" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer" required>
                                                    </label>
                                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-2 rounded-lg transition shadow-sm cursor-pointer whitespace-nowrap">
                                                        Selesai & Laporkan
                                                    </button>
                                                </form>

                                                @if(isset($program->pengajuan_id))
                                                    <div class="border-l border-slate-300 pl-2">
                                                        <a href="{{ route('operational.laporan.form', $program->pengajuan_id) }}" class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-3 py-2 rounded-lg transition shadow-sm inline-flex items-center space-x-1 whitespace-nowrap">
                                                            <i class="fa-solid fa-file-invoice-dollar mr-1"></i>
                                                            <span>Input Nota Belanja</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <div class="flex items-center space-x-3 bg-emerald-50/50 p-3 rounded-xl border border-emerald-100">
                                            <div class="text-xs font-medium text-emerald-800">
                                                🎉 Program Selesai Dokumentasi Terarsip
                                            </div>
                                            @if($program->documentation_path)
                                                <a href="{{ asset('storage/' . $program->documentation_path) }}" target="_blank" class="text-xs bg-white hover:bg-slate-100 text-slate-700 font-bold px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm transition">
                                                    Lihat Foto 📸
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                             </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

</body>
</html>