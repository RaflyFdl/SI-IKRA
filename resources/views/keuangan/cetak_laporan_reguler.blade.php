<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Realisasi Dana Reguler - {{ $program->nama_program }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white; color: black; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            @page { margin: 2cm; }
        }
    </style>
</head>
<body class="bg-slate-100 text-gray-900 font-sans text-sm p-8">

    <div class="max-w-4xl mx-auto bg-white p-10 shadow-lg print:shadow-none print:p-0">

        <!-- Header -->
        <div class="border-b-4 border-emerald-700 pb-6 mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-black text-emerald-800 tracking-tight">YAYASAN IKRA PADJADJARAN</h1>
                <p class="text-gray-600 mt-1">Laporan Pertanggungjawaban Realisasi Penggunaan Dana Infak Reguler</p>
            </div>
            <div class="text-right text-xs text-gray-500 space-y-1">
                <p>Tanggal Cetak: {{ now()->format('d M Y, H:i') }}</p>
                <p>Status:
                    @if($program->status === 'dilaporkan')
                        <span class="text-emerald-700 font-bold">Laporan Terselesaikan</span>
                    @elseif($program->status === 'reimburse_pending')
                        <span class="text-rose-700 font-bold">Menunggu Reimburse</span>
                    @else
                        <span class="font-bold">{{ strtoupper($program->status) }}</span>
                    @endif
                </p>
            </div>
        </div>

        <button onclick="window.print()" class="no-print mb-6 bg-emerald-600 text-white px-4 py-2 rounded shadow hover:bg-emerald-700">
            Cetak Laporan (PDF)
        </button>

        <!-- Informasi Program -->
        <div class="mb-8">
            <h2 class="text-lg font-bold text-gray-800 mb-3 border-b border-gray-200 pb-1">Informasi Program</h2>
            <table class="w-full text-sm">
                <tr>
                    <td class="w-48 py-1.5 text-gray-500 font-semibold">Nama Program</td>
                    <td class="py-1.5 font-bold">: {{ $program->nama_program }}</td>
                </tr>
                <tr>
                    <td class="py-1.5 text-gray-500 font-semibold">Periode Bulan</td>
                    <td class="py-1.5 font-bold">: {{ $program->periode_bulan }}</td>
                </tr>
                <tr>
                    <td class="py-1.5 text-gray-500 font-semibold">Target Penerima Manfaat</td>
                    <td class="py-1.5 font-bold">: {{ $program->penerima_manfaat }}</td>
                </tr>
                <tr>
                    <td class="py-1.5 text-gray-500 font-semibold">Tanggal Pelaksanaan</td>
                    <td class="py-1.5 font-bold">: {{ \Carbon\Carbon::parse($program->tanggal_pelaksanaan)->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td class="py-1.5 text-gray-500 font-semibold">Total Dana Dicairkan</td>
                    <td class="py-1.5 font-bold">: Rp {{ number_format($program->nominal_diajukan, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Rencana Kebutuhan Awal -->
        @php
            $rincianAwal = json_decode($program->rincian_detail, true);
        @endphp
        <div class="mb-8">
            <h2 class="text-lg font-bold text-gray-800 mb-3 border-b border-gray-200 pb-1">1. Rencana Kebutuhan Dana Awal</h2>
            @if(is_array($rincianAwal) && count($rincianAwal) > 0)
            <table class="w-full text-left border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="border border-gray-300 p-2 text-center w-12">No</th>
                        <th class="border border-gray-300 p-2">Nama Kebutuhan</th>
                        <th class="border border-gray-300 p-2 text-center w-20">Qty</th>
                        <th class="border border-gray-300 p-2 text-center w-20">Satuan</th>
                        <th class="border border-gray-300 p-2 text-right">Estimasi Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalRencana = 0; @endphp
                    @foreach($rincianAwal as $index => $item)
                        @php $totalRencana += ($item['harga'] ?? 0); @endphp
                        <tr>
                            <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 p-2">{{ $item['nama'] ?? ($item['uraian'] ?? '-') }}</td>
                            <td class="border border-gray-300 p-2 text-center">{{ $item['qty'] ?? ($item['jumlah'] ?? '-') }}</td>
                            <td class="border border-gray-300 p-2 text-center">{{ $item['satuan'] ?? '-' }}</td>
                            <td class="border border-gray-300 p-2 text-right">Rp {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-bold">
                        <td colspan="4" class="border border-gray-300 p-2 text-right">Total Estimasi Awal</td>
                        <td class="border border-gray-300 p-2 text-right">Rp {{ number_format($totalRencana, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
            @else
            <p class="text-gray-500 italic">Tidak ada rincian rencana kebutuhan awal yang tercatat.</p>
            @endif
        </div>

        <!-- Realisasi Penggunaan Dana -->
        <div class="mb-8">
            <h2 class="text-lg font-bold text-gray-800 mb-3 border-b border-gray-200 pb-1">2. Realisasi Penggunaan Dana (Nota Lapangan)</h2>
            <table class="w-full text-left border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="border border-gray-300 p-2 text-center w-12">No</th>
                        <th class="border border-gray-300 p-2 w-28">Tanggal Nota</th>
                        <th class="border border-gray-300 p-2">Uraian / Deskripsi Realisasi</th>
                        <th class="border border-gray-300 p-2 text-right w-36">Nominal Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalRealisasi = 0; @endphp
                    @foreach($laporan->items_nota as $index => $item)
                        @php $totalRealisasi += ($item['nominal'] ?? 0); @endphp
                        <tr>
                            <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 p-2">{{ \Carbon\Carbon::parse($item['tanggal'])->format('d M Y') }}</td>
                            <td class="border border-gray-300 p-2">{{ $item['uraian'] ?? '-' }}</td>
                            <td class="border border-gray-300 p-2 text-right">Rp {{ number_format($item['nominal'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-bold">
                        <td colspan="3" class="border border-gray-300 p-2 text-right">Total Realisasi Penggunaan</td>
                        <td class="border border-gray-300 p-2 text-right">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Kesimpulan Selisih -->
        @php
            $selisih = $laporan->selisih_dana;
            $bgClass = $selisih > 0 ? 'bg-emerald-50 border-emerald-200' : ($selisih < 0 ? 'bg-rose-50 border-rose-200' : 'bg-gray-50 border-gray-200');
            $borderClass = $selisih > 0 ? 'border-emerald-200' : ($selisih < 0 ? 'border-rose-200' : 'border-gray-200');
        @endphp
        <div class="mb-8 p-4 border rounded-lg {{ $bgClass }}">
            <h3 class="font-bold text-gray-800 mb-1">Kesimpulan Pelaporan</h3>
            <div class="grid grid-cols-2 gap-4 mt-3 text-sm">
                <div>Dana Awal Dicairkan: <strong class="text-gray-900">Rp {{ number_format($program->nominal_diajukan, 0, ',', '.') }}</strong></div>
                <div>Total Realisasi (Nota): <strong class="text-gray-900">Rp {{ number_format($laporan->total_pengeluaran, 0, ',', '.') }}</strong></div>
            </div>
            <div class="mt-4 pt-3 border-t {{ $borderClass }} font-bold">
                Status Akhir:
                @if($selisih > 0)
                    <span class="text-emerald-700">Terdapat Kelebihan Dana (Sisa) sebesar Rp {{ number_format($selisih, 0, ',', '.') }} yang telah dikembalikan ke yayasan.</span>
                @elseif($selisih < 0)
                    <span class="text-rose-700">Terdapat Kekurangan Dana (Reimburse) sebesar Rp {{ number_format(abs($selisih), 0, ',', '.') }}.
                        @if($laporan->bukti_reimburse)
                            (Sudah Direimburse oleh Keuangan)
                        @else
                            (Menunggu Konfirmasi Keuangan)
                        @endif
                    </span>
                @else
                    <span class="text-gray-700">Anggaran Pas (Tidak ada sisa atau kurang).</span>
                @endif
            </div>

            @if($laporan->keterangan)
            <div class="mt-3 pt-3 border-t {{ $borderClass }}">
                <p class="text-xs text-gray-600"><strong>Catatan Tim Operasional:</strong> {{ $laporan->keterangan }}</p>
            </div>
            @endif
        </div>

        <!-- Page Break untuk Lampiran -->
        <div class="page-break"></div>

        <!-- Lampiran Nota -->
        <div class="mt-8">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-200 pb-1">3. Lampiran Bukti Fisik Nota</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($laporan->items_nota as $index => $item)
                    @if(isset($item['bukti_nota']) && $item['bukti_nota'])
                    <div class="border border-gray-200 p-2 rounded text-center">
                        <p class="font-semibold text-xs mb-2">Nota #{{ $index + 1 }}: {{ $item['uraian'] ?? '-' }}</p>
                        <img src="{{ asset('storage/' . $item['bukti_nota']) }}" alt="Bukti Nota {{ $index + 1 }}" class="max-h-64 mx-auto object-contain">
                    </div>
                    @endif
                @endforeach

                @if($laporan->bukti_pengembalian_sisa)
                <div class="border border-emerald-200 p-2 rounded text-center bg-emerald-50">
                    <p class="font-bold text-xs mb-2 text-emerald-700">Bukti Transfer Pengembalian Sisa Dana</p>
                    <img src="{{ asset('storage/' . $laporan->bukti_pengembalian_sisa) }}" alt="Bukti Pengembalian" class="max-h-64 mx-auto object-contain">
                </div>
                @endif

                @if($laporan->bukti_reimburse)
                <div class="border border-rose-200 p-2 rounded text-center bg-rose-50">
                    <p class="font-bold text-xs mb-2 text-rose-700">Bukti Transfer Reimburse dari Keuangan</p>
                    <img src="{{ asset('storage/' . $laporan->bukti_reimburse) }}" alt="Bukti Reimburse" class="max-h-64 mx-auto object-contain">
                </div>
                @endif
            </div>
        </div>

        <!-- Tanda Tangan -->
        <div class="mt-12 grid grid-cols-2 gap-16 text-center text-sm">
            <div>
                <p class="font-semibold text-gray-700">Dibuat oleh,</p>
                <p class="text-gray-500 text-xs mb-12">Tim Operasional IKRA</p>
                <div class="border-b border-gray-400 mx-auto w-48"></div>
                <p class="text-xs text-gray-500 mt-1">( _________________________ )</p>
            </div>
            <div>
                <p class="font-semibold text-gray-700">Diperiksa oleh,</p>
                <p class="text-gray-500 text-xs mb-12">Bagian Keuangan IKRA</p>
                <div class="border-b border-gray-400 mx-auto w-48"></div>
                <p class="text-xs text-gray-500 mt-1">( _________________________ )</p>
            </div>
        </div>

    </div>

</body>
</html>
