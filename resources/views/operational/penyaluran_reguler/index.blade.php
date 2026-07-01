<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyaluran Infak Reguler - IKRA</title>
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
                    <a href="{{ route('operational.dashboard') }}" class="flex items-center space-x-3 {{ request()->routeIs('operational.dashboard') && !request()->routeIs('operational.pencairan') && !request()->routeIs('operational.penyaluran-reguler.index') ? 'bg-emerald-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-3 rounded-lg transition">
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
            
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900">Perencanaan Penyaluran Infak Reguler</h2>
                    <p class="text-slate-500 mt-1">Kelola batas aman alokasi program kerja murni berbasis hak porsi 65% dana jamaah.</p>
                </div>
            </div>

            @if(session('sukses'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center space-x-3 shadow-sm">
                    <span>✅</span>
                    <p class="text-sm font-medium">{{ session('sukses') }}</p>
                </div>
            @endif

            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-6 items-center mb-8">
                <div class="space-y-1.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Pilih Periode Acuan</span>
                    <form method="GET" action="{{ route('operational.penyaluran-reguler.index') }}">
                        <select name="periode" onchange="this.form.submit()" class="bg-slate-50 text-sm font-bold border border-slate-200 text-slate-700 rounded-lg px-3 py-2 focus:outline-none focus:border-emerald-600 cursor-pointer w-full max-w-xs">
                            @foreach($daftarPeriode as $p)
                                <option value="{{ $p }}" {{ $periodeDipilih == $p ? 'selected' : '' }}>{{ $p }} {{ $p == now()->format('Y-m') ? '(Bulan Ini)' : '' }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Pemasukan Reguler Periode (100%)</span>
                    <h3 class="text-2xl font-extrabold text-slate-800">Rp {{ number_format($totalMasukPeriode, 0, ',', '.') }}</h3>
                </div>

                <div class="bg-emerald-50/50 border border-emerald-100 p-4 rounded-xl space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 block">Sisa Dana Siap Salur (65%)</span>
                    <h3 class="text-2xl font-black text-emerald-700">Rp {{ number_format($sisaDanaSiapSalur, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-emerald-600 font-medium">*Batas aman nominal penyusunan rencana program.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-4 h-fit">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-file-signature text-emerald-600"></i> Form Ajuan Penyaluran
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Kirim berkas ke Pembina untuk mendapatkan pengesahan sistem.</p>
                    </div>
                    
                    <form action="{{ route('operational.penyaluran-reguler.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="periode_bulan" value="{{ $periodeDipilih }}">

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Program Kerja</label>
                            <input type="text" name="nama_program" required placeholder="Contoh: Santunan Beras Yatim Dhuafa" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-emerald-600 font-medium">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nominal Yang Dibutuhkan</label>
                            <input type="number" name="nominal_diajukan" max="{{ $sisaDanaSiapSalur }}" required placeholder="Maksimal Rp {{ number_format($sisaDanaSiapSalur, 0, '', '') }}" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 font-mono font-bold text-emerald-700 focus:outline-none focus:border-emerald-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Target Penerima Manfaat</label>
                            <input type="text" name="penerima_manfaat" required placeholder="Contoh: 50 KK Mustahik Coblong" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-emerald-600 font-medium">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Pelaksanaan</label>
                            <input type="date" name="tanggal_pelaksanaan" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-emerald-600 font-medium">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Rincian Anggaran Detail</label>
                            <textarea name="rincian_detail" rows="4" required placeholder="Tuliskan rincian detail belanja..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-emerald-600 font-medium"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-3 rounded-lg shadow-sm transition cursor-pointer">
                            Kirim Pengajuan Ke Pembina
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="font-bold text-slate-900 uppercase text-xs tracking-wider">Status Pelacakan Alur Pengajuan</h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 font-bold uppercase tracking-wider">
                                        <th class="py-3.5 px-6">Nama Program / Periode</th>
                                        <th class="py-3.5 px-4">Nominal</th>
                                        <th class="py-3.5 px-4">Target Penerima</th>
                                        <th class="py-3.5 px-6 text-center">Status Alur / Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    @forelse($semuaPengajuan as $sp)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-4 px-6">
                                            <div class="font-bold text-slate-900 text-sm">{{ $sp->nama_program }}</div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">Periode Dana: {{ $sp->periode_bulan }} | Eksekusi: {{ \Carbon\Carbon::parse($sp->tanggal_pelaksanaan)->format('d M Y') }}</div>
                                        </td>
                                        <td class="py-4 px-4 font-mono font-bold text-slate-700">
                                            Rp{{ number_format($sp->nominal_diajukan, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-4 text-slate-500">
                                            {{ $sp->penerima_manfaat }}
                                        </td>
                                        <td class="py-4 px-6 text-center space-y-2">
                                            @if($sp->status == 'pending')
                                                <span class="inline-block px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold rounded-full">⏳ Menunggu Pembina</span>
                                            @elseif($sp->status == 'disetujui' || $sp->status == 'disetujui_pembina')
                                                <span class="inline-block px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold rounded-full">✔️ Disetujui Pembina</span>
                                            @elseif($sp->status == 'dicairkan')
                                                <div class="flex flex-col items-center gap-1.5">
                                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold rounded-full">💰 Dana Cair (Keuangan)</span>
                                                    <a href="{{ route('operational.penyaluran-reguler.laporan.form', $sp->id) }}" class="inline-flex items-center gap-1 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-black px-2.5 py-1 rounded shadow-sm transition">
                                                        <i class="fa-solid fa-file-pen"></i> Isi Laporan Belanja
                                                    </a>
                                                </div>
                                            @elseif($sp->status == 'dilaporkan')
                                                <span class="inline-block px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold rounded-full">📋 LPJ Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="py-16 text-center text-slate-400 font-medium space-y-3">
                                            <span class="text-4xl block">📂</span>
                                            <p class="text-sm">Belum ada rekaman rencana pengajuan penyaluran reguler.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>