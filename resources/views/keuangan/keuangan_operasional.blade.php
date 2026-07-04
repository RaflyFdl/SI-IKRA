<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Dana Operasional - IKRA Padjadjaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-white hover:text-emerald-200 underline transition-all bg-transparent border-0 cursor-pointer">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-10 space-y-8">
        
        @if(session('success'))
            <div class="p-4 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-xl font-bold text-sm shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-100 border border-rose-200 text-rose-700 rounded-xl font-bold text-sm shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-xmark text-rose-600"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="flex border-b border-gray-200">
            <a href="{{ route('keuangan.dashboard') }}" class="py-3 px-6 font-semibold text-sm text-gray-400 hover:text-gray-700 transition-all">
                | Infak Reguler (Bulanan)
            </a>
            <a href="{{ route('keuangan.infak-ekstra') }}" class="py-3 px-6 font-semibold text-sm text-gray-400 hover:text-gray-700 transition-all">
                | Infak Ekstra (Khusus Program)
            </a>
            <a href="{{ route('keuangan.operasional') }}" class="py-3 px-6 font-bold text-sm text-[#0b6e3f] border-b-2 border-[#0b6e3f] transition-all">
                | Dana Operasional Kantor
            </a>
        </div>

        <div class="bg-gradient-to-r from-slate-800 to-slate-900 p-8 rounded-2xl shadow-lg text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400 block">Sisa Saldo Kas Operasional (Dinamis)</span>
                <h3 class="text-4xl font-extrabold tracking-tight mt-1 text-amber-400">
                    Rp {{ number_format($totalOperasionalGabungan, 0, ',', '.') }}
                </h3>
                <p class="text-xs text-slate-400 mt-2 max-w-xl">
                    Akumulasi dana murni yang bersumber dari potongan sah 35% seluruh pemasukan lalu otomatis dikurangi secara berkala oleh penyerahan dana rill bulanan yang disetujui Pembina dan dicairkan Keuangan.
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

        <div class="space-y-4">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-hourglass-half text-amber-500"></i> Antrean Verifikasi Pencairan Keuangan ({{ $antreanPencairan->count() }})
            </h2>

            @if($antreanPencairan->isEmpty())
                <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center shadow-sm">
                    <p class="text-gray-400 text-sm">Tidak ada antrean pencairan dana operasional dari Pembina saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($antreanPencairan as $antrean)
                        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex flex-col justify-between">
                            <div class="p-5 border-b border-gray-100 bg-slate-50/50 flex justify-between items-start">
                                <div>
                                    <h3 class="font-bold text-gray-800 text-base mb-1">{{ $antrean->title }}</h3>
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-extrabold rounded text-[9px] uppercase tracking-wide border border-emerald-200">Disetujui Pembina</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-base font-black text-gray-900 block">Rp {{ number_format($antrean->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="p-5 flex-1">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-2">Rincian Komponen Kebutuhan:</span>
                                <ul class="space-y-1.5 text-xs text-gray-600">
                                    @foreach($antrean->items as $subItem)
                                        <li class="flex justify-between border-b border-dashed border-gray-100 pb-1">
                                            <span>• {{ $subItem->description }}</span>
                                            <span class="font-bold text-gray-800">Rp {{ number_format($subItem->amount, 0, ',', '.') }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="p-5 bg-slate-50 border-t border-gray-100">
                                <form action="{{ route('keuangan.operasional.cairkan', $antrean->id) }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Apakah kas operasional sebesar Rp {{ number_format($antrean->total_amount, 0, ',', '.') }} benar-benar telah diserahkan rill beserta bukti transfer?')">
                                    @csrf
                                    
                                    <div class="mb-4">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1.5 tracking-wide">
                                            <i class="fa-solid fa-paperclip text-[#0b6e3f] mr-1"></i> Lampirkan Bukti Transfer <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="flex items-center justify-center w-full">
                                            <label class="flex flex-col items-center justify-center w-full h-20 border border-dashed border-gray-300 rounded-xl cursor-pointer bg-white hover:bg-gray-50 transition-colors">
                                                <div class="flex flex-col items-center justify-center pt-2 pb-2 text-center px-2">
                                                    <i class="fa-solid fa-cloud-arrow-up text-base text-gray-400 mb-0.5"></i>
                                                    <p class="text-[11px] text-gray-500 font-medium">Klik untuk upload bukti transfer</p>
                                                    <p class="text-[9px] text-gray-400">PNG, JPG, JPEG, atau PDF (Maks 2MB)</p>
                                                </div>
                                                <input type="file" name="bukti_transfer" class="hidden" required accept="image/*,.pdf" />
                                            </label>
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all cursor-pointer flex items-center justify-center gap-1.5">
                                            <i class="fa-solid fa-money-bill-wave"></i> Cairkan Kas Operasional
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-receipt text-emerald-500"></i> Antrean Verifikasi Nota & Sisa Belanja ({{ count($antreanNotaRealisasi) }})
            </h2>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                @if(empty($antreanNotaRealisasi))
                    <div class="p-8 text-center text-gray-400 text-sm py-12">Tidak ada berkas nota belanja operasional yang menunggu konfirmasi keuangan saat ini.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-[10px] uppercase tracking-wider text-gray-400 bg-slate-50 border-b border-gray-100">
                                    <th class="py-3 px-5">Nama Kegiatan Internal</th>
                                    <th class="py-3 px-5 text-right">Modal Awal Kerja</th>
                                    <th class="py-3 px-5 text-right">Realisasi Nota</th>
                                    <th class="py-3 px-5 text-center">Selisih Kas</th>
                                    <th class="py-3 px-5 text-center">Nota Fisik</th>
                                    <th class="py-3 px-5 text-center">Aksi Konfirmasi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 divide-y divide-gray-100">
                                @foreach($antreanNotaRealisasi as $nota)
                                    <tr class="hover:bg-slate-50/30 transition-colors">
                                        <td class="py-4 px-5">
                                            <span class="font-bold text-gray-800 block">{{ $nota->nama_kegiatan }}</span>
                                            <span class="text-[9px] font-mono text-gray-400">ID Pengajuan: #{{ $nota->operational_request_id }}</span>
                                        </td>
                                        <td class="py-4 px-5 text-right font-semibold text-slate-700">
                                            Rp {{ number_format($nota->modal_awal, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-5 text-right font-semibold text-slate-900">
                                            Rp {{ number_format($nota->realisasi_nota, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            @if($nota->selisih_kas >= 0)
                                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-extrabold rounded-md text-[10px] border border-emerald-200 block w-fit mx-auto">
                                                    <i class="fa-solid fa-arrow-down-long"></i> Refund: Rp {{ number_format($nota->selisih_kas, 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-extrabold rounded-md text-[10px] border border-amber-200 block w-fit mx-auto">
                                                    <i class="fa-solid fa-arrow-up-long"></i> Tekor: Rp {{ number_format(abs($nota->selisih_kas), 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            @if($nota->nota_fisik)
                                                <span class="text-xs text-gray-700 font-medium block max-w-xs truncate mx-auto italic">
                                                    "{{ $nota->nota_fisik }}"
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Tanpa Catatan</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            <form action="{{ route('keuangan.operasional.konfirmasi-nota', $nota->report_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin telah memverifikasi kesesuaian fisik berkas pengeluaran ini?')">
                                                @csrf
                                                @if($nota->selisih_kas >= 0)
                                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] rounded-lg shadow-sm transition-all cursor-pointer">
                                                        <i class="fa-solid fa-right-to-bracket"></i> Terima Refund
                                                    </button>
                                                @else
                                                    <button type="submit" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-[11px] rounded-lg shadow-sm transition-all cursor-pointer">
                                                        <i class="fa-solid fa-hand-holding-dollar"></i> Bayar Klaim Tekor
                                                    </button>
                                                @endif
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-gray-400"></i> Riwayat Penyerahan Arus Kas Keluar Resmi
            </h2>

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                @if($riwayatPencairan->isEmpty())
                    <div class="p-8 text-center text-gray-400 text-sm py-12">Belum ada dana operasional internal yang berhasil dicairkan sebelumnya.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-[10px] uppercase tracking-wider text-gray-400 bg-slate-50 border-b border-gray-100">
                                    <th class="py-3 px-5">Nama Pengajuan Kegiatan / Kebutuhan</th>
                                    <th class="py-3 px-5">Tanggal Penyerahan Uang</th>
                                    <th class="py-3 px-5 text-center">Status Keuangan</th>
                                    <th class="py-3 px-5 text-center">Bukti Transfer</th>
                                    <th class="py-3 px-5 text-right">Total Anggaran Keluar</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 divide-y divide-gray-100">
                                @foreach($riwayatPencairan as $riwayat)
                                    <tr class="hover:bg-slate-50/30 transition-colors">
                                        <td class="py-4 px-5">
                                            <span class="font-bold text-gray-800 block">{{ $riwayat->title }}</span>
                                            <span class="text-[10px] text-gray-400 flex items-center gap-1">
                                                <i class="fa-solid fa-list-check"></i> {{ $riwayat->items->count() }} deskripsi kebutuhan internal
                                            </span>
                                        </td>
                                        <td class="py-4 px-5 text-xs text-gray-500">
                                            {{ $riwayat->updated_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 font-extrabold rounded-full text-[10px] uppercase border border-emerald-200">
                                                {{ $riwayat->status_keuangan }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            @if($riwayat->bukti_transfer)
                                                <a href="{{ asset('storage/' . $riwayat->bukti_transfer) }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-bold underline">
                                                    <i class="fa-solid fa-receipt"></i> Lihat Bukti
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5 text-right font-black text-gray-900">
                                            Rp {{ number_format($riwayat->total_amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </main>

</body>
</html>