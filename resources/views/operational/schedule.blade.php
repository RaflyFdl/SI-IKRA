<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Penjadwalan Operasional - IKRA</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 font-sans text-slate-800">

    <div class="flex min-h-screen">
        <div class="w-64 bg-slate-900 text-white p-6 space-y-6 flex flex-col justify-between shrink-0">
            <div class="space-y-6">
                <div class="flex items-center space-x-3 border-b border-slate-700 pb-4">
                    <div class="bg-emerald-500 p-2 rounded-lg text-slate-900 font-bold">IK</div>
                    <div>
                        <h1 class="font-bold text-lg leading-tight">IKRA System</h1>
                        <p class="text-xs text-slate-400">Tim Operasional</p>
                    </div>
                </div>
                
                <nav class="space-y-2">
                    <a href="{{ route('operational.dashboard') }}" class="flex items-center space-x-3 {{ request()->routeIs('operational.dashboard') ? 'bg-emerald-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-3 rounded-lg transition">
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
            <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Pusat Penjadwalan & Operasional Program</h1>
                    <p class="text-sm text-slate-500 mt-1">Kelola tanggal pelaksanaan, pantau progress pendanaan, dan unggah dokumentasi penyelesaian program kerja secara berkala.</p>
                </div>
                @if($activeTab === 'podcast')
                    <a href="{{ route('operational.podcast.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-plus"></i> Buat Jadwal Podcast
                    </a>
                @endif
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2 shadow-sm mb-6">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            <div class="border-b border-slate-200 bg-white p-2 rounded-xl flex flex-wrap md:flex-nowrap gap-2 shadow-sm mb-6">
                <a href="{{ route('operational.schedule', ['tab' => 'donasi']) }}" 
                   class="flex-1 text-center py-2.5 text-sm font-bold rounded-lg transition-all duration-200 {{ $activeTab === 'donasi' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                    🎁 Donasi Umum ({{ $donasiPrograms->count() }})
                </a>
                <a href="{{ route('operational.schedule', ['tab' => 'podcast']) }}" 
                   class="flex-1 text-center py-2.5 text-sm font-bold rounded-lg transition-all duration-200 {{ $activeTab === 'podcast' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                    🎙️ Program Podcast ({{ $podcastPrograms->count() }})
                </a>
                <a href="{{ route('operational.schedule', ['tab' => 'cinema']) }}" 
                   class="flex-1 text-center py-2.5 text-sm font-bold rounded-lg transition-all duration-200 {{ $activeTab === 'cinema' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                    🎬 Cinema Edukasi ({{ $cinemaPrograms->count() }})
                </a>
                <a href="{{ route('operational.schedule', ['tab' => 'reguler']) }}" 
                   class="flex-1 text-center py-2.5 text-sm font-bold rounded-lg transition-all duration-200 {{ $activeTab === 'reguler' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                    📋 Program Reguler ({{ $regulerPrograms->count() }})
                </a>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                
                @php
                    if ($activeTab === 'podcast') {
                        $currentPrograms = $podcastPrograms;
                        $tabTitle = 'Program Podcast';
                        $tabIcon = '🎙️';
                    } elseif ($activeTab === 'cinema') {
                        $currentPrograms = $cinemaPrograms;
                        $tabTitle = 'Cinema Edukasi';
                        $tabIcon = '🎬';
                    } elseif ($activeTab === 'reguler') {
                        $currentPrograms = $regulerPrograms;
                        $tabTitle = 'Program Reguler';
                        $tabIcon = '📋';
                    } else {
                        $currentPrograms = $donasiPrograms;
                        $tabTitle = 'Donasi Umum';
                        $tabIcon = '🎁';
                    }
                @endphp

                <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-base font-bold text-slate-800">Daftar Agenda Kerja: {{ $tabTitle }}</h2>
                </div>
                
                <div class="p-6">
                    @if($currentPrograms->isEmpty())
                        <div class="text-center py-12 text-slate-400 text-sm">
                            <span class="text-3xl block mb-2">{{ $tabIcon }}</span>
                            Belum ada database program untuk kategori {{ $tabTitle }}.
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-6">
                            @foreach($currentPrograms as $program)
                                
                                @if($activeTab === 'reguler')
                                    <div class="border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition duration-200 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-white shadow-sm">
                                        <div class="space-y-3 flex-1">
                                            <div class="flex flex-wrap items-center gap-3">
                                                {{-- 🛠️ PERBAIKAN LOGIK DI SINI --}}
                                                @if($program->status === 'disetujui')
                                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-amber-50 text-amber-700 uppercase border border-amber-200">🟢 Disetujui</span>
                                                @elseif($program->status === 'dicairkan')
                                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-blue-50 text-blue-700 uppercase border border-blue-200">💸 Dana Dicairkan</span>
                                                @else
                                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-emerald-50 text-emerald-700 uppercase border border-emerald-200">🏁 Selesai Lapangan</span>
                                                @endif
                                                <h3 class="text-lg font-bold text-slate-800">{{ $program->nama_program }}</h3>
                                            </div>
                                            <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">{{ $program->rincian_detail ?? 'Tidak ada deskripsi tambahan.' }}</p>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 text-xs">
                                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                                    <span class="text-slate-400 block font-medium uppercase text-[9px] tracking-wider">Anggaran Alokasi</span>
                                                    <span class="font-bold text-slate-700 block">Rp {{ number_format($program->nominal_diajukan, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                                    <span class="text-slate-400 block font-medium uppercase text-[9px] tracking-wider">Target Penerima Manfaat</span>
                                                    <span class="font-bold text-slate-600 block pt-0.5">{{ $program->penerima_manfaat }}</span>
                                                </div>
                                                <div class="bg-emerald-50/50 p-2.5 rounded-lg border border-emerald-100 text-slate-900">
                                                    <span class="text-emerald-700 block font-bold uppercase text-[9px] tracking-wider">Tanggal Kerja Pelaksanaan</span>
                                                    <span class="font-bold block pt-0.5">📅 {{ $program->tanggal_pelaksanaan ? \Carbon\Carbon::parse($program->tanggal_pelaksanaan)->translatedFormat('d F Y') : 'Belum Dijadwalkan' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w-full lg:w-auto shrink-0 border-t lg:border-t-0 pt-4 lg:pt-0 flex flex-col gap-3 min-w-[260px]">
                                            @if($program->status !== 'dilaporkan')
                                                <form action="{{ route('operational.update-date', $program->id) }}" method="POST" class="space-y-1">
                                                    @csrf
                                                    <input type="hidden" name="is_reguler" value="true">
                                                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Set / Ubah Tanggal Kerja</label>
                                                    <div class="flex gap-2">
                                                        <input type="date" name="execution_date" value="{{ $program->tanggal_pelaksanaan }}" class="p-2 border border-slate-200 rounded-lg text-xs w-full focus:outline-none focus:border-emerald-500" required>
                                                        <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold px-3 py-2 rounded-lg transition shadow-sm shrink-0 cursor-pointer">Simpan</button>
                                                    </div>
                                                </form>
                                            @else
                                                <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-center">
                                                    <span class="text-xs font-bold text-emerald-800 block">🏆 PROGRAM SELESAI TERLAKSANA</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                @elseif($activeTab === 'podcast')
                                    <div class="border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition duration-200 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-white shadow-sm">
                                        <div class="space-y-3 flex-1">
                                            <div class="flex flex-wrap items-center gap-3">
                                                @if($program->pengairanKeuangan && $program->pengairanKeuangan->status === 'DICAIRKAN')
                                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-emerald-50 text-emerald-700 uppercase border border-emerald-200">💸 Dana Dicairkan</span>
                                                @elseif($program->pengairanKeuangan && $program->pengairanKeuangan->status === 'SELESAI')
                                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-blue-50 text-blue-700 uppercase border border-blue-200">🎬 Selesai Tayang</span>
                                                @else
                                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-amber-50 text-amber-700 uppercase border border-amber-200">⏳ Pengajuan Dana</span>
                                                @endif
                                                <h3 class="text-lg font-bold text-slate-800">{{ $program->title }}</h3>
                                            </div>
                                            <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">
                                                <strong class="text-slate-700">Topik Pembahasan:</strong> {{ $program->topic }}
                                            </p>
                                            
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2 text-xs">
                                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                                    <span class="text-slate-400 block font-medium uppercase text-[9px] tracking-wider">Host</span>
                                                    <span class="font-bold text-slate-700 block">{{ $program->host }}</span>
                                                </div>
                                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                                    <span class="text-slate-400 block font-medium uppercase text-[9px] tracking-wider">Narasumber</span>
                                                    <span class="font-bold text-slate-700 block">{{ $program->guest_star }}</span>
                                                </div>
                                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                                    <span class="text-slate-400 block font-medium uppercase text-[9px] tracking-wider">Estimasi Anggaran</span>
                                                    <span class="font-bold text-emerald-600 block">Rp {{ number_format($program->amount_requested, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="bg-emerald-50/50 p-2.5 rounded-lg border border-emerald-100 text-slate-900">
                                                    <span class="text-emerald-700 block font-bold uppercase text-[9px] tracking-wider">Jadwal Taping</span>
                                                    <span class="font-bold block pt-0.5">🎥 {{ $program->taping_date ? $program->taping_date->translatedFormat('d M Y - H:i') : '-' }} WIB</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-full lg:w-auto shrink-0 border-t lg:border-t-0 pt-4 lg:pt-0 text-right text-xs text-slate-400">
                                            Tayang: {{ $program->airing_date ? $program->airing_date->translatedFormat('d M Y') : 'Belum ditentukan' }}
                                        </div>
                                    </div>

                                @else
                                    @php
                                        $percentage = $program->target_amount > 0 ? min(round(($program->current_amount / $program->target_amount) * 100), 100) : 0;
                                    @endphp
                                    <div class="border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition duration-200 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-white shadow-sm">
                                        <div class="space-y-3 flex-1">
                                            <div class="flex flex-wrap items-center gap-3">
                                                @if($program->status === 'active')
                                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-amber-50 text-amber-700 uppercase border border-amber-200">🔴 Sedang Berjalan</span>
                                                @else
                                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-emerald-50 text-emerald-700 uppercase border border-emerald-200">🏁 Selesai & Diarsipkan</span>
                                                @endif
                                                <h3 class="text-lg font-bold text-slate-800">{{ $program->name }}</h3>
                                            </div>
                                            <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">{{ $program->description }}</p>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2 text-xs">
                                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                                    <span class="text-slate-400 block font-medium uppercase text-[9px] tracking-wider">Dana Terkumpul</span>
                                                    <span class="font-bold text-slate-700 block">Rp {{ number_format($program->current_amount, 0, ',', '.') }}</span>
                                                    <span class="text-[10px] text-emerald-600 font-semibold block mt-0.5">({{ $percentage }}% Terpenuhi)</span>
                                                </div>
                                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                                    <span class="text-slate-400 block font-medium uppercase text-[9px] tracking-wider">Target Pendanaan</span>
                                                    <span class="font-bold text-slate-600 block pt-0.5">Rp {{ number_format($program->target_amount, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                                    <span class="text-slate-400 block font-medium uppercase text-[9px] tracking-wider">Batas Donasi</span>
                                                    <span class="font-bold text-slate-600 block pt-0.5">📅 {{ $program->end_date ? \Carbon\Carbon::parse($program->end_date)->translatedFormat('d M Y') : 'Terbuka' }}</span>
                                                </div>
                                                <div class="bg-emerald-50/50 p-2.5 rounded-lg border border-emerald-100 text-slate-900">
                                                    <span class="text-emerald-700 block font-bold uppercase text-[9px] tracking-wider">Tanggal Kerja Pelaksanaan</span>
                                                    <span class="font-bold block pt-0.5">📅 {{ $program->execution_date ? \Carbon\Carbon::parse($program->execution_date)->translatedFormat('d F Y') : 'Belum Dijadwalkan' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w-full lg:w-auto shrink-0 border-t lg:border-t-0 pt-4 lg:pt-0 flex flex-col gap-3 min-w-[260px]">
                                            @if($program->status === 'active')
                                                <form action="{{ route('operational.update-date', $program->id) }}" method="POST" class="space-y-1">
                                                    @csrf
                                                    <div class="flex gap-2">
                                                        <input type="date" name="execution_date" value="{{ $program->execution_date }}" class="p-2 border border-slate-200 rounded-lg text-xs w-full focus:outline-none focus:border-emerald-500" required>
                                                        <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold px-3 py-2 rounded-lg transition shadow-sm shrink-0 cursor-pointer">Simpan</button>
                                                    </div>
                                                </form>

                                                @if($program->execution_date)
                                                    <div class="pt-2 border-t border-slate-100">
                                                        <form action="{{ route('operational.complete', $program->id) }}" method="POST" enctype="multipart/form-data" class="space-y-1">
                                                            @csrf
                                                            <label class="text-[10px] font-bold uppercase tracking-wider text-rose-500 block">Selesaikan Program & Upload Dokumentasi</label>
                                                            <div class="flex flex-col gap-1.5">
                                                                <input type="file" name="documentation" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer" required>
                                                                <button type="submit" onclick="return confirm('Apakah Anda yakin program ini sudah sukses terlaksana?')" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 rounded-lg transition shadow-sm text-center cursor-pointer">
                                                                    🏁 Program Selesai (Arsipkan)
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-center space-y-2">
                                                    <span class="text-xs font-bold text-emerald-800 block">🏆 PROGRAM DINYATAKAN SELESAI</span>
                                                    @if($program->documentation_path)
                                                        <a href="{{ asset('storage/' . $program->documentation_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:text-emerald-900 hover:underline">
                                                            🖼️ Lihat Bukti Dokumentasi Lapangan
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

</body>
</html>