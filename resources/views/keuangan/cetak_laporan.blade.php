<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Realisasi Dana - {{ $program->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: white;
                color: black;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
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
                <p class="text-gray-600 mt-1">Laporan Pertanggungjawaban Realisasi Penggunaan Dana Infak Ekstra</p>
            </div>
            <div class="text-right text-xs text-gray-500 space-y-1">
                <p>Tanggal Cetak: {{ now()->format('d M Y, H:i') }}</p>
                <p>Status: Laporan Terselesaikan</p>
            </div>
        </div>

        <button onclick="window.print()" class="no-print mb-6 bg-emerald-600 text-white px-4 py-2 rounded shadow hover:bg-emerald-700">
            🖨️ Cetak Laporan (PDF)
        </button>

        <!-- Program Info -->
        <div class="mb-8">
            <h2 class="text-lg font-bold text-gray-800 mb-2 border-b border-gray-200 pb-1">Informasi Program</h2>
            <table class="w-full text-sm">
                <tr>
                    <td class="w-48 py-1 text-gray-500 font-semibold">Nama Program</td>
                    <td class="py-1 font-bold">: {{ $program->name }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-500 font-semibold">Kategori Program</td>
                    <td class="py-1 font-bold">: {{ $program->category ?? 'Umum' }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-gray-500 font-semibold">Total Dana Dicairkan</td>
                    <td class="py-1 font-bold">: Rp {{ number_format($pengajuan->nominal_diminta, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Rencana Kebutuhan Awal -->
        <div class="mb-8">
            <h2 class="text-lg font-bold text-gray-800 mb-3 border-b border-gray-200 pb-1">1. Rencana Kebutuhan Dana Awal</h2>
            @if($program->detailKebutuhan && $program->detailKebutuhan->count() > 0)
            <table class="w-full text-left border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="border border-gray-300 p-2 text-center w-12">No</th>
                        <th class="border border-gray-300 p-2">Deskripsi Kebutuhan</th>
                        <th class="border border-gray-300 p-2 text-center w-24">Jumlah</th>
                        <th class="border border-gray-300 p-2 text-right">Estimasi Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalRencana = 0; @endphp
                    @foreach($program->detailKebutuhan as $index => $detail)
                        @php $totalRencana += $detail->harga; @endphp
                        <tr>
                            <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 p-2">{{ $detail->nama_barang }}</td>
                            <td class="border border-gray-300 p-2 text-center">{{ $detail->jumlah }} {{ $detail->satuan }}</td>
                            <td class="border border-gray-300 p-2 text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-bold">
                        <td colspan="3" class="border border-gray-300 p-2 text-right">Total Estimasi Awal</td>
                        <td class="border border-gray-300 p-2 text-right">Rp {{ number_format($totalRencana, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
            @else
            <p class="text-gray-500 italic">Tidak ada rincian rencana kebutuhan awal yang disubmit.</p>
            @endif
        </div>

        <!-- Realisasi Penggunaan Dana -->
        <div class="mb-8">
            <h2 class="text-lg font-bold text-gray-800 mb-3 border-b border-gray-200 pb-1">2. Realisasi Penggunaan Dana (Nota Lapangan)</h2>
            <table class="w-full text-left border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="border border-gray-300 p-2 text-center w-12">No</th>
                        <th class="border border-gray-300 p-2 w-32">Tanggal Nota</th>
                        <th class="border border-gray-300 p-2">Uraian / Deskripsi Realisasi</th>
                        <th class="border border-gray-300 p-2 text-right">Nominal Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalRealisasi = 0; @endphp
                    @foreach($laporan->details as $index => $detail)
                        @php $totalRealisasi += $detail->nominal; @endphp
                        <tr>
                            <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 p-2">{{ \Carbon\Carbon::parse($detail->tanggal)->format('d M Y') }}</td>
                            <td class="border border-gray-300 p-2">{{ $detail->uraian }}</td>
                            <td class="border border-gray-300 p-2 text-right">Rp {{ number_format($detail->nominal, 0, ',', '.') }}</td>
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
        <div class="mb-8 p-4 border rounded-lg {{ $laporan->selisih > 0 ? 'bg-emerald-50 border-emerald-200' : ($laporan->selisih < 0 ? 'bg-rose-50 border-rose-200' : 'bg-gray-50 border-gray-200') }}">
            <h3 class="font-bold text-gray-800 mb-1">Kesimpulan Pelaporan</h3>
            <div class="grid grid-cols-2 gap-4 mt-3 text-sm">
                <div>Dana Awal Dicairkan: <strong class="text-gray-900">Rp {{ number_format($pengajuan->nominal_diminta, 0, ',', '.') }}</strong></div>
                <div>Total Realisasi (Nota): <strong class="text-gray-900">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</strong></div>
            </div>
            <div class="mt-4 pt-3 border-t {{ $laporan->selisih > 0 ? 'border-emerald-200' : ($laporan->selisih < 0 ? 'border-rose-200' : 'border-gray-200') }} font-bold">
                Status Akhir: 
                @if($laporan->selisih > 0)
                    <span class="text-emerald-700">Terdapat Kelebihan Dana (Sisa) sebesar Rp {{ number_format($laporan->selisih, 0, ',', '.') }} yang dikembalikan.</span>
                @elseif($laporan->selisih < 0)
                    <span class="text-rose-700">Terdapat Kekurangan Dana (Reimburse) sebesar Rp {{ number_format(abs($laporan->selisih), 0, ',', '.') }}.</span>
                @else
                    <span class="text-gray-700">Anggaran Pas (Tidak ada sisa atau kurang).</span>
                @endif
            </div>
        </div>

        <!-- Page Break untuk Lampiran jika mau print PDF agar lebih rapi -->
        <div class="page-break"></div>

        <!-- Lampiran Nota -->
        <div class="mt-8">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-200 pb-1">3. Lampiran Bukti Fisik</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($laporan->details as $index => $detail)
                    <div class="border border-gray-200 p-2 rounded text-center">
                        <p class="font-semibold text-xs mb-2">Nota #{{ $index + 1 }}: {{ $detail->uraian }}</p>
                        <img src="{{ asset('storage/' . $detail->bukti_nota) }}" alt="Bukti Nota {{ $index+1 }}" class="max-h-64 mx-auto object-contain">
                    </div>
                @endforeach

                @if($laporan->bukti_transfer_pencairan)
                    <div class="border border-gray-200 p-2 rounded text-center bg-gray-50">
                        <p class="font-bold text-xs mb-2 text-emerald-700">Bukti Transfer (Sisa/Refund)</p>
                        <img src="{{ asset('storage/' . $laporan->bukti_transfer_pencairan) }}" alt="Bukti Refund" class="max-h-64 mx-auto object-contain">
                    </div>
                @endif
            </div>
        </div>

    </div>

</body>
</html>
