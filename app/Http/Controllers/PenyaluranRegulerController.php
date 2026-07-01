<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenyaluranReguler;
use App\Models\LaporanReguler; // ✅ IMPORT MODEL LAPORAN BARU BERHASIL
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
            ->whereIn('status', ['dicairkan', 'dilaporkan'])
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

        // ✅ KITA UBAH DISINI: Dari 'operasional' menjadi 'operational' sesuai nama folder sistem kamu
        return view('operational.penyaluran_reguler.index', compact(
            'semuaPengajuan',
            'periodeDipilih',
            'sisaDanaSiapSalur',
            'totalMasukPeriode',
            'daftarPeriode'
        ));
    }

    public function simpanPengajuan(Request $request)
    {
        $request->validate([
            'nama_program' => 'required|string|max:255',
            'nominal_diajukan' => 'required|numeric|min:1',
            'rincian_detail' => 'required|string',
            'penerima_manfaat' => 'required|string',
            'tanggal_pelaksanaan' => 'required|date',
            'periode_bulan' => 'required|string|size:7',
        ]);

        PenyaluranReguler::create([
            'nama_program' => $request->nama_program,
            'nominal_diajukan' => $request->nominal_diajukan,
            'rincian_detail' => $request->rincian_detail,
            'penerima_manfaat' => $request->penerima_manfaat,
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
            'periode_bulan' => $request->periode_bulan,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('sukses', 'Rencana penyaluran berhasil diajukan ke Pembina!');
    }

    /**
     * 🟢 LOGIKA BARU 1: PROSES APPROVAL SISI PEMBINA
     * Berfungsi menangani ketika Pembina klik Setujui atau Tolak
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
     * 🟢 LOGIKA BARU 2: PROSES PENCAIRAN SISI KEUANGAN
     * Sinkron dengan form view dashboard keuangan dan struktur database asli
     */
    public function prosesCairkanKeuangan(Request $request, $id)
    {
        $request->validate([
            // Menyesuaikan name="bukti_transfer" yang dikirim dari form blade
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $pengajuan = PenyaluranReguler::findOrFail($id);

        if ($request->hasFile('bukti_transfer')) {
            // Menggunakan penyimpanan public_path agar link 👁️ Lihat Bukti Transfer langsung tembus tanpa symlink
            $folderTujuan = public_path('uploads/bukti_transfer');
            if (!file_exists($folderTujuan)) {
                mkdir($folderTujuan, 0777, true);
            }

            $namaFile = time() . '_trf_' . $request->file('bukti_transfer')->getClientOriginalName();
            $request->file('bukti_transfer')->move($folderTujuan, $namaFile);

            // Cek nama kolom yang tersedia di tabel database Anda (bukti_transfer atau bukti_transfer_path)
            // Di sini kita update kedua kolom tersebut untuk mengantisipasi ketidakcocokan skema migrasi Anda
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
     * 🚀 LOGIKA OPRASIONAL BARU 3: MENAMPILKAN FORM LAPORAN NOTA BELANJA
     * Mengambil entitas data reguler untuk dibaca di halaman form operational
     */
    public function showLaporanForm($id)
    {
        $program = PenyaluranReguler::findOrFail($id);
        return view('operational.penyaluran_reguler.laporan', compact('program'));
    }

    /**
     * 🚀 LOGIKA OPRASIONAL BARU 4: MENYIMPAN DATA LAPORAN & NOTA TRANSAKSI RIIL (DI-UPDATE FOR MULTI-NOTA ARRAY)
     * Mengamankan berkas pengeluaran serta mengunci status program menjadi 'dilaporkan'
     */
    public function simpanLaporan(Request $request, $id)
    {
        // 1. Validasi data bertipe array multi-nota dari HTML5
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

        // 2. Lakukan perulangan untuk memproses berkas file fisik nota dan kalkulasi angka belanja riil
        foreach ($request->nota as $index => $item) {
            $nominalItem = (int)$item['nominal'];
            $totalPengeluaran += $nominalItem;

            $pathBukti = null;
            if (isset($item['bukti_nota'])) {
                // Menyimpan ke folder storage/app/public/nota_reguler
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

        // 3. Proses simpan berkas bukti transfer sisa kas apabila dana bernilai sisa (Kelebihan Dana)
        $pathSisaDana = null;
        if ($selisihDana > 0 && $request->hasFile('bukti_pengembalian_sisa')) {
            $pathSisaDana = $request->file('bukti_pengembalian_sisa')->store('bukti_sisa_reguler', 'public');
        }

        // 4. Catat arsip pengeluaran dana riil lapangan ke tabel laporan_reguler dalam struktur JSON
        LaporanReguler::create([
            'penyaluran_reguler_id' => $program->id,
            'items_nota' => $itemsNotaData, // Masuk dalam bentuk array, otomatis dikonversi jadi JSON di database oleh Eloquent Casting
            'total_pengeluaran' => $totalPengeluaran,
            'selisih_dana' => $selisihDana,
            'bukti_pengembalian_sisa' => $pathSisaDana,
            'keterangan' => $request->keterangan
        ]);

        // 5. Kunci status program kerja reguler menjadi 'dilaporkan' agar alur selesai sempurna
        $program->update([
            'status' => 'dilaporkan'
        ]);

        return redirect()->route('operational.penyaluran-reguler.index')
            ->with('sukses', 'Laporan nota pengeluaran dana reguler berhasil disubmit dan diarsipkan ke database!');
    }
}