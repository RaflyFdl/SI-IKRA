<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenyaluranReguler;
use App\Models\LaporanReguler;
use Illuminate\Support\Facades\DB;

class PenyaluranRegulerController extends Controller
{
    public function indeksOperasional(Request $request)
    {
        // 1. Filter Periode Bulan (Default: Bulan Ini)
        $periodeDipilih = $request->get('periode', now()->format('Y-m'));

        // 2. Hitung total pemasukan MURNI INFAK REGULER memakai kolom 'transaction_type'
        $totalMasukPeriode = DB::table('transactions')
            ->where('transaction_type', 'reguler')
            ->where('periode', $periodeDipilih)
            ->sum('amount') ?? 0;

        // 3. Hak Program murni adalah 65% dari total pemasukan
        $danaSiapSalurMurni = $totalMasukPeriode * 0.65;

        // 4. Hitung berapa dana reguler periode ini yang sudah terpakai/dicairkan keuangan
        $danaTerpakai = PenyaluranReguler::where('periode_bulan', $periodeDipilih)
            ->whereIn('status', ['dicairkan', 'dilaporkan', 'reimburse_pending'])
            ->sum('nominal_diajukan') ?? 0;

        // 5. Sisa saldo riil yang benar-benar tersisa untuk disalurkan
        $sisaDanaSiapSalur = $danaSiapSalurMurni - $danaTerpakai;

        // 6. Ambil semua list riwayat penyaluran reguler
        $semuaPengajuan = PenyaluranReguler::orderBy('created_at', 'desc')->get();

        // 7. Ambil daftar periode unik khusus transaksi reguler untuk dropdown filter
        $daftarPeriode = DB::table('transactions')
            ->where('transaction_type', 'reguler')
            ->distinct()
            ->pluck('periode')
            ->toArray();

        if (!in_array(now()->format('Y-m'), $daftarPeriode)) {
            array_unshift($daftarPeriode, now()->format('Y-m'));
        }

        $busyDates = \App\Models\ExtraProgram::getBusyDates();

        return view('operational.penyaluran_reguler.index', compact(
            'semuaPengajuan',
            'periodeDipilih',
            'sisaDanaSiapSalur',
            'totalMasukPeriode',
            'daftarPeriode',
            'busyDates'
        ));
    }

    public function simpanPengajuan(Request $request)
    {
        $request->validate([
            'nama_program'       => 'required|string|max:255',
            'nominal_diajukan'   => 'required|numeric|min:1',
            'rincian_detail'     => 'required|string',
            'penerima_manfaat'   => 'required|string',
            'tanggal_pelaksanaan'=> 'required|date',
            'periode_bulan'      => 'required|string|size:7',
        ]);

        $rincianRaw = $request->rincian_detail;
        $decoded = json_decode($rincianRaw, true);
        $rincianFinal = (json_last_error() === JSON_ERROR_NONE) ? $rincianRaw : $rincianRaw;

        PenyaluranReguler::create([
            'nama_program'        => $request->nama_program,
            'nominal_diajukan'    => $request->nominal_diajukan,
            'rincian_detail'      => $rincianFinal,
            'penerima_manfaat'    => $request->penerima_manfaat,
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
            'periode_bulan'       => $request->periode_bulan,
            'status'              => 'pending',
        ]);

        return redirect()->back()->with('sukses', 'Rencana penyaluran berhasil diajukan ke Pembina!');
    }

    /**
     * PROSES APPROVAL SISI PEMBINA
     */
    public function prosesApprovalPembina(Request $request, $id)
    {
        $request->validate([
            'tindakan' => 'required|in:disetujui,ditolak',
            'catatan_pembina' => 'nullable|string'
        ]);

        $pengajuan = PenyaluranReguler::findOrFail($id);
        $pengajuan->update([
            'status' => $request->tindakan,
            'catatan_pembina' => $request->catatan_pembina,
        ]);

        $pesan = $request->tindakan === 'disetujui' 
            ? 'Rencana penyaluran berhasil disetujui! Data diteruskan ke keuangan.' 
            : 'Rencana penyaluran telah ditolak.';

        return redirect()->back()->with('sukses', $pesan);
    }

    /**
     * PROSES PENCAIRAN SISI KEUANGAN
     */
    public function prosesCairkanKeuangan(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $pengajuan = PenyaluranReguler::findOrFail($id);

        if ($request->hasFile('bukti_transfer')) {
            $folderTujuan = public_path('uploads/bukti_transfer');
            if (!file_exists($folderTujuan)) {
                mkdir($folderTujuan, 0777, true);
            }

            $namaFile = time() . '_trf_' . $request->file('bukti_transfer')->getClientOriginalName();
            $request->file('bukti_transfer')->move($folderTujuan, $namaFile);

            $pengajuan->update([
                'status' => 'dicairkan',
                'bukti_transfer' => $namaFile,
                'bukti_transfer_path' => 'uploads/bukti_transfer/' . $namaFile,
                'dicairkan_at' => now(),
            ]);

            return redirect()->route('keuangan.dashboard')
                ->with('success', '💰 Dana sukses dicairkan dan bukti transfer berhasil disimpan!');
        }

        return back()->with('error', 'Gagal memproses file bukti transfer.');
    }

    /**
     * MENAMPILKAN FORM LAPORAN NOTA BELANJA
     */
    public function showLaporanForm($id)
    {
        $program = PenyaluranReguler::findOrFail($id);
        return view('operational.penyaluran_reguler.laporan', compact('program'));
    }

    /**
     * MENYIMPAN DATA LAPORAN NOTA TRANSAKSI RIIL
     * - Jika sisa dana (kelebihan): status 'dilaporkan', catat sisa yang dikembalikan
     * - Jika kurang dana (reimburse): status 'reimburse_pending', tunggu keuangan cairkan
     * - Jika pas: status 'dilaporkan'
     */
    public function simpanLaporan(Request $request, $id)
    {
        $request->validate([
            'nota' => 'required|array|min:1',
            'nota.*.tanggal' => 'required|date',
            'nota.*.uraian' => 'required|string|max:255',
            'nota.*.nominal' => 'required|numeric|min:0',
            'nota.*.bukti_nota' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'bukti_pengembalian_sisa' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string'
        ]);

        $program = PenyaluranReguler::findOrFail($id);
        $totalDanaAwal = $program->nominal_diajukan;

        $itemsNotaData = [];
        $totalPengeluaran = 0;

        DB::beginTransaction();

        try {
            // Proses setiap nota
            foreach ($request->nota as $index => $item) {
                $nominalItem = (int)$item['nominal'];
                $totalPengeluaran += $nominalItem;

                $pathBukti = null;
                if (isset($item['bukti_nota'])) {
                    $pathBukti = $item['bukti_nota']->store('nota_reguler', 'public');
                }

                $itemsNotaData[] = [
                    'tanggal' => $item['tanggal'],
                    'uraian' => $item['uraian'],
                    'nominal' => $nominalItem,
                    'bukti_nota' => $pathBukti
                ];
            }

            $selisihDana = $totalDanaAwal - $totalPengeluaran;

            // Proses bukti pengembalian sisa jika ada kelebihan dana
            $pathSisaDana = null;
            if ($selisihDana > 0) {
                if (!$request->hasFile('bukti_pengembalian_sisa')) {
                    return redirect()->back()
                        ->with('error', 'Ada sisa dana sebesar Rp ' . number_format($selisihDana, 0, ',', '.') . '. Anda wajib mengunggah bukti transfer pengembalian sisa uang!')
                        ->withInput();
                }
                $pathSisaDana = $request->file('bukti_pengembalian_sisa')->store('bukti_sisa_reguler', 'public');
            }

            // Simpan laporan ke tabel laporan_reguler
            LaporanReguler::create([
                'penyaluran_reguler_id' => $program->id,
                'items_nota'            => $itemsNotaData,
                'total_pengeluaran'     => $totalPengeluaran,
                'selisih_dana'          => $selisihDana,
                'bukti_pengembalian_sisa' => $pathSisaDana,
                'keterangan'            => $request->keterangan
            ]);

            // Tentukan status akhir program berdasarkan selisih
            if ($selisihDana < 0) {
                // Kurang dana: butuh reimburse dari keuangan
                $program->update(['status' => 'reimburse_pending']);
                $pesanStatus = 'Laporan tersubmit! Status: Kurang Dana (Rp ' . number_format(abs($selisihDana), 0, ',', '.') . '). Menunggu konfirmasi reimburse dari bagian keuangan.';
            } else {
                // Pas atau kelebihan (kelebihan sudah dikembalikan via bukti transfer)
                $program->update(['status' => 'dilaporkan']);
                $pesanStatus = 'Laporan nota pengeluaran dana reguler berhasil disubmit dan diarsipkan!';
            }

            DB::commit();

            return redirect()->route('operational.penyaluran-reguler.index')
                ->with('sukses', $pesanStatus);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyimpan laporan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * KEUANGAN: Memproses Reimburse Dana Reguler (Kurang Dana)
     * Dana reimburse diambil dari saldo siap salur reguler bulan yang bersangkutan.
     */
    public function prosesRegulerReimburse(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer_reimburse' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $program = PenyaluranReguler::with('laporan')->findOrFail($id);

        if ($program->status !== 'reimburse_pending') {
            return redirect()->back()->with('error', 'Program ini tidak sedang dalam status menunggu reimburse.');
        }

        if (!$program->laporan) {
            return redirect()->back()->with('error', 'Laporan realisasi tidak ditemukan.');
        }

        $nominalTekor = abs($program->laporan->selisih_dana);

        DB::beginTransaction();
        try {
            $pathBukti = $request->file('bukti_transfer_reimburse')->store('bukti_reimburse_reguler', 'public');

            // Simpan bukti reimburse di laporan
            $program->laporan->update([
                'bukti_reimburse' => $pathBukti,
            ]);

            // Update status program menjadi dilaporkan (selesai)
            $program->update([
                'status' => 'dilaporkan',
            ]);

            DB::commit();
            return redirect()->route('keuangan.dashboard')
                ->with('success', '✅ Reimburse dana reguler sebesar Rp ' . number_format($nominalTekor, 0, ',', '.') . ' berhasil dikonfirmasi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses reimburse: ' . $e->getMessage());
        }
    }

    /**
     * KEUANGAN: Menampilkan halaman cetak laporan realisasi reguler
     */
    public function cetakLaporanReguler($laporanId)
    {
        $laporan = LaporanReguler::with('penyaluranReguler')->findOrFail($laporanId);
        $program = $laporan->penyaluranReguler;

        return view('keuangan.cetak_laporan_reguler', compact('laporan', 'program'));
    }
}
