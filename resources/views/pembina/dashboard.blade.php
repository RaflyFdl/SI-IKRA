<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pembina - IKRA System</title>
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

        <div class="flex-1 p-10">
            <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm mb-6">
                <h1 class="text-2xl font-bold text-slate-900">Selamat Datang</h1>
            </div>

            @if(session('sukses'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2 shadow-sm mb-6">
                    <span>✅</span> {{ session('sukses') }}
                </div>
            @endif

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-base font-bold text-slate-800">Evaluasi Proposal Penyaluran Infak Reguler</h2>
                </div>

                <div class="p-6">
                    @if($pengajuanMasuk->isEmpty())
                        <div class="text-center py-12 text-slate-400 text-sm">
                            <span class="text-3xl block mb-2">📋</span>
                            Belum ada pengajuan proposal masuk dari Tim Operasional.
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($pengajuanMasuk as $item)
                                <div class="border border-slate-200 rounded-xl p-5 bg-white shadow-sm flex flex-col md:flex-row justify-between gap-6">
                                    <div class="space-y-2 flex-1">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-bold text-slate-800">{{ $item->nama_program }}</h3>
                                            @if($item->status === 'pending')
                                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded bg-amber-50 text-amber-700 border border-amber-200 uppercase">Menunggu Validasi</span>
                                            @elseif($item->status === 'disetujui')
                                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded bg-blue-50 text-blue-700 border border-blue-200 uppercase">Disetujui Pembina</span>
                                            @elseif($item->status === 'dicairkan')
                                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">Dana Cair Keuangan</span>
                                            @else
                                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded bg-rose-50 text-rose-700 border border-rose-200 uppercase">Ditolak</span>
                                            @endif
                                        </div>
                                        
                                        <p class="text-sm text-slate-600 font-medium">
                                            Nominal Diajukan: <span class="text-indigo-600 font-bold">Rp {{ number_format($item->nominal_diajukan, 0, ',', '.') }}</span>
                                        </p>
                                        
                                        <div class="text-xs text-slate-500 space-y-2 bg-slate-50 p-3 rounded-lg border border-slate-100">
                                            <div class="font-bold text-slate-600 uppercase tracking-wider text-[10px] mb-2">📋 Rincian Kebutuhan Anggaran</div>
                                            @php
                                                $rincianData = json_decode($item->rincian_detail, true);
                                                $isJson = is_array($rincianData) && count($rincianData) > 0;
                                            @endphp
                                            @if($isJson)
                                                <table class="w-full text-[11px]">
                                                    <thead>
                                                        <tr class="border-b border-slate-200 text-slate-400 font-bold uppercase">
                                                            <th class="text-left pb-1.5 pr-2">Nama</th>
                                                            <th class="text-center pb-1.5 px-2">Qty</th>
                                                            <th class="text-center pb-1.5 px-2">Satuan</th>
                                                            <th class="text-right pb-1.5 pl-2">Harga</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-slate-100">
                                                        @foreach($rincianData as $baris)
                                                            <tr class="text-slate-600">
                                                                <td class="py-1.5 pr-2 font-medium">{{ $baris['nama'] ?? ($baris['uraian'] ?? '-') }}</td>
                                                                <td class="py-1.5 px-2 text-center">{{ $baris['qty'] ?? ($baris['kuantitas'] ?? '-') }}</td>
                                                                <td class="py-1.5 px-2 text-center">{{ $baris['satuan'] ?? '-' }}</td>
                                                                <td class="py-1.5 pl-2 text-right font-mono font-bold text-slate-700">Rp {{ number_format($baris['harga'] ?? ($baris['harga_satuan'] ?? 0), 0, ',', '.') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="border-t-2 border-indigo-200 bg-indigo-50/50">
                                                            <td colspan="3" class="pt-2 pr-2 font-bold text-indigo-700 uppercase text-[10px]">Total Anggaran</td>
                                                            <td class="pt-2 pl-2 text-right font-black text-indigo-700 font-mono">
                                                                Rp {{ number_format(collect($rincianData)->sum(fn($b) => $b['harga'] ?? ($b['harga_satuan'] ?? 0)), 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            @else
                                                {{-- Fallback untuk data lama yang masih berupa teks biasa --}}
                                                <div>{{ $item->rincian_detail }}</div>
                                            @endif
                                            <div class="pt-1 border-t border-slate-200 mt-1"><strong>Penerima Manfaat:</strong> {{ $item->penerima_manfaat }}</div>
                                            <div><strong>Rencana Pelaksanaan:</strong> {{ \Carbon\Carbon::parse($item->tanggal_pelaksanaan)->translatedFormat('d F Y') }}</div>
                                        </div>

                                        @if($item->catatan_pembina)
                                            <div class="text-xs bg-amber-50 text-amber-800 p-2.5 rounded-lg border border-amber-100">
                                                <strong>Catatan Pembina:</strong> {{ $item->catatan_pembina }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="w-full md:w-64 shrink-0 border-t md:border-t-0 pt-4 md:pt-0 flex flex-col justify-center">
                                        @if($item->status === 'pending')
                                            <form action="{{ route('pembina.penyaluran-reguler.approve', $item->id) }}" method="POST" class="space-y-3">
                                                @csrf
                                                <div>
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Catatan Tambahan (Opsional)</label>
                                                    <textarea name="catatan_pembina" rows="2" class="w-full p-2 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500" placeholder="Berikan saran/alasan..."></textarea>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <button type="submit" name="tindakan" value="ditolak" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold py-2 rounded-lg transition text-center cursor-pointer">
                                                        ❌ Tolak
                                                    </button>
                                                    <button type="submit" name="tindakan" value="disetujui" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2 rounded-lg transition text-center shadow-sm cursor-pointer">
                                                        ✅ Setujui
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="text-center py-4 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-500">
                                                🔒 Keputusan Selesai
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
    </div>

</body>
</html>