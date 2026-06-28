<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanPencairanEkstra;
use App\Models\LaporanPenggunaan;
use App\Models\LaporanPenggunaanDetail;
use App\Models\ExtraProgram;
use App\Models\DanaBackup; // 👈 Menghubungkan model Dana Backup Global baru
use Illuminate\Support\Facades\DB;

class PenyaluranEkstraController extends Controller
{
    /**
     * 🛠️ OPERASIONAL: Menampilkan halaman utama pilihan pencairan dana ekstra
     */
    public function pencairanEkstra()
    {
        // 1. Ambil program yang BELUM PERNAH diajukan pencairan sama sekali
        $programs = ExtraProgram::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('pengajuan_pencairan_ekstra') 
                  ->whereRaw('pengajuan_pencairan_ekstra.extra_program_id = extra_programs.id');
        })->get();

        // 2. 🚀 AMBIL DATA RIWAYAT STATUS PENCAIRAN UNTUK MONITORING OPERASIONAL
        $riwayatPengajuan = PengajuanPencairanEkstra::with('extraProgram')
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('operational.pencairan_ekstra', compact('programs', 'riwayatPengajuan'));
    }

    /**
     * 🛠️ OPERASIONAL: Menyimpan data rekening & membuat draf permintaan pencairan
     * 🔒 DIBATASI: Hanya porsi dana bersih (65%) yang dapat diajukan oleh operasional.
     */
    public function storePencairan(Request $request)
    {
        $request->validate([
            'program_id'     => 'required|exists:extra_programs,id',
            'bank_name'      => 'required|string|max:50',
            'account_number' => 'required|string|max:50',
            'account_name'   => 'required|string|max:100', 
        ]);

        $program = ExtraProgram::findOrFail($request->program_id);

        // 🎯 POIN 6: Otomatis memotong 35% untuk operasional kantor di awal
        // Hanya porsi Dana Terikat (65%) yang dilepaskan sebagai dana bersih program
        $danaBersihPenyaluran = $program->current_amount * 0.65;

        if ($danaBersihPenyaluran <= 0) {
            return redirect()->back()->with('error', 'Dana bersih untuk program ekstra tidak mencukupi untuk dicairkan.');
        }

        // ✅ AMAN: Mengamankan ID staff agar tidak null jika user logout/testing tanpa session
        $staffId = auth()->id() ?? (auth()->user()?->id ?? 1);

        PengajuanPencairanEkstra::create([
            'extra_program_id' => $program->id,
            'staff_id'         => $staffId,                  
            'nominal_diminta'  => $danaBersihPenyaluran, // Otomatis mengunci nominal 65% (Misal: Rp 1.300.000)
            'nama_bank'        => $request->bank_name,       
            'nomor_rekening'   => $request->account_number,  
            'status'           => 'PENDING',                 
        ]);

        return redirect()->route('operational.pencairan')->with('success', 'Permintaan pencairan dana bersih (65%) berhasil dikirim! Silakan tunggu pihak keuangan mentransfer modal kerja.');
    }

    /**
     * OPERASIONAL: Menampilkan halaman form input nota
     */
    public function showLaporanForm($pengajuanId)
    {
        $pengajuan = PengajuanPencairanEkstra::findOrFail($pengajuanId);
        return view('operational.laporan', compact('pengajuan'));
    }

    /**
     * OPERASIONAL: Menginput laporan nota penggunaan dana beserta file bukti pengembalian
     */
    public function simpanLaporan(Request $request, $pengajuanId)
    {
        $request->validate([
            'nota.*.tanggal' => 'required|date',
            'nota.*.uraian' => 'required|string',
            'nota.*.nominal' => 'required|integer',
            'nota.*.bukti_nota' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'bukti_pengembalian_sisa' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Penampung bukti transfer sisa uang
        ]);

        $pengajuan = PengajuanPencairanEkstra::findOrFail($pengajuanId);
        $nominalDicairkan = $pengajuan->nominal_diminta;

        DB::beginTransaction();

        try {
            // Hitung total penggunaan dari nota terlebih dahulu
            $totalTerpakai = 0;
            foreach ($request->nota as $item) {
                $totalTerpakai += $item['nominal'];
            }

            $selisih = $nominalDicairkan - $totalTerpakai;
            $pathBuktiSisa = null;

            // ⚠️ POIN 1: Validasi file wajib upload pengembalian jika ada sisa dana lebih
            if ($selisih > 0) {
                if (!$request->hasFile('bukti_pengembalian_sisa')) {
                    return redirect()->back()->with('error', 'Ada sisa dana sebesar Rp ' . number_format($selisih, 0, ',', '.') . '. Anda wajib mengunggah bukti transfer pengembalian sisa uang!')->withInput();
                }
                $pathBuktiSisa = $request->file('bukti_pengembalian_sisa')->store('bukti_pengembalian', 'public');
            }

            // Simpan data induk laporan penggunaan
            $laporan = LaporanPenggunaan::create([
                'sumber_dana' => 'EKSTRA',
                'pengajuan_id' => $pengajuan->id,
                'total_terpakai' => $totalTerpakai, 
                'selisih' => $selisih,
                'bukti_transfer_pencairan' => $pathBuktiSisa, // Mencatat path pengembalian sisa uang
            ]);

            // Simpan setiap item rincian nota belanja
            foreach ($request->nota as $item) {
                $pathNota = $item['bukti_nota']->store('bukti_nota', 'public');

                LaporanPenggunaanDetail::create([
                    'laporan_penggunaan_id' => $laporan->id,
                    'tanggal' => $item['tanggal'],
                    'uraian' => $item['uraian'],
                    'nominal' => $item['nominal'],
                    'bukti_nota' => $pathNota,
                ]);
            }

            // Penentuan alokasi status akhir pengajuan & pengaliran sisa dana
            if ($selisih > 0) {
                // ⚠️ POIN 5: Sisa uang otomatis dialokasikan dan dicatat ke tabel Dana Backup Global yayasan
                DanaBackup::create([
                    'sumber_dana' => 'EKSTRA',
                    'pengajuan_id' => $pengajuan->id,
                    'nominal' => $selisih,
                    'bukti_transfer' => $pathBuktiSisa,
                ]);

                $pengajuan->update(['status' => 'SELESAI']);
            } else if ($selisih < 0) {
                // ⚠️ POIN 2: Jika kurang, otomatis meminta status reimburse pending keuangan
                $pengajuan->update(['status' => 'REIMBURSE_PENDING']);
            } else {
                $pengajuan->update(['status' => 'SELESAI']);
            }

            DB::commit();

            return redirect()->route('operational.pencairan')->with('success', 'Laporan penggunaan dana berhasil disimpan otomatis!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan laporan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * KEUANGAN: Memproses Cairkan Uang Tekor (Reimburse)
     */
    public function prosesReimburse(Request $request, $pengajuanId)
    {
        $request->validate([
            'sumber_potong' => 'required|in:current_amount,sisa_dana,dana_bersih_ekstra', 
            'bukti_transfer_pencairan' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $pengajuan = PengajuanPencairanEkstra::findOrFail($pengajuanId);
        $laporan = LaporanPenggunaan::where('pengajuan_id', $pengajuan->id)->firstOrFail();
        $nominalTekor = abs($laporan->selisih); 

        if ($pengajuan->status !== 'REIMBURSE_PENDING') {
            return response()->json(['message' => 'Pengajuan ini tidak sedang membutuhkan reimburse.'], 400);
        }

        DB::beginTransaction();

        try {
            $program = ExtraProgram::findOrFail($pengajuan->extra_program_id);
            $sumber = $request->sumber_potong;

            if ($program->$sumber < $nominalTekor) {
                return response()->json(['message' => 'Saldo di ' . $sumber . ' tidak mencukupi.'], 400);
            }

            $program->decrement($sumber, $nominalTekor);
            $pathBukti = $request->file('bukti_transfer_pencairan')->store('bukti_reimburse', 'public');

            $pengajuan->update([
                'status' => 'SELESAI',
                'bukti_transfer_pencairan' => $pathBukti 
            ]);

            DB::commit();
            return response()->json(['message' => 'Reimburse berhasil dicairkan!'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memproses reimburse: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 💰 KEUANGAN: Memproses konfirmasi pencairan dana awal
     */
    public function prosesCairkanAwal(Request $request, $pengajuanId)
    {
        $request->validate([
            'bukti_transfer_pencairan' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $pengajuan = PengajuanPencairanEkstra::findOrFail($pengajuanId);

        if ($pengajuan->status !== 'PENDING') {
            return redirect()->back()->with('error', 'Pengajuan ini tidak sedang dalam antrean.');
        }

        DB::beginTransaction();

        try {
            $program = ExtraProgram::findOrFail($pengajuan->extra_program_id);

            // ⚠️ FIX: Cek ketersediaan berdasarkan saldo kantong bersih 65% (dana_bersih_ekstra)
            if ($program->dana_bersih_ekstra < $pengajuan->nominal_diminta) {
                return redirect()->back()->with('error', 'Saldo dana siap salur program tidak mencukupi untuk pencairan ini.');
            }

            $pathBukti = $request->file('bukti_transfer_pencairan')->store('bukti_pencairan_awal', 'public');

            // ⚠️ FIX: Kurangi kantong bersih (dana_bersih_ekstra) agar dashboard menjadi Rp 0. 
            // Kolom current_amount dibiarkan utuh (tetap Rp 2.000.000) agar history donatur & diagram capaian tidak rusak.
            $program->decrement('dana_bersih_ekstra', $pengajuan->nominal_diminta);

            $pengajuan->update([
                'status' => 'DICAIRKAN',
                'bukti_transfer_pencairan' => $pathBukti
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Dana modal awal bersih berhasil dicairkan dan saldo siap salur program dipotong!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pencairan: ' . $e->getMessage());
        }
    }
}