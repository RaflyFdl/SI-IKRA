<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;          
use App\Models\ExtraProgram;   
use App\Models\DanaBackup; 
use App\Models\PengajuanPencairanEkstra;
use App\Models\OperationalRequest;

class KeuanganController extends Controller
{
    /**
     * Halaman Dashboard Utama Keuangan
     */
    public function dashboard()
    {
        $sessionEmail = session('logged_in_email');
        if (!$sessionEmail) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $staff = Staff::where('email', $sessionEmail)->where('role', 'keuangan')->first();
        if (!$staff) {
            return redirect()->route('login')->with('error', 'Akses ditolak! Halaman ini khusus untuk Bagian Keuangan.');
        }

        $totalRegulerMasuk = 10000000; 
        $operasionalReguler = $totalRegulerMasuk * 0.35;
        $siapSalurReguler   = $totalRegulerMasuk * 0.65;

        $siapSalurEkstra   = ExtraProgram::sum('dana_bersih_ekstra');
        $operasionalEkstra = ExtraProgram::sum('dana_operasional_ekstra');
        $totalEkstraMasuk  = ExtraProgram::sum('current_amount');
        $totalKasOperasionalKantor = $operasionalReguler + $operasionalEkstra;

        $totalDanaBackupEkstra = DanaBackup::whereIn('sumber_dana', ['EKSTRA', 'ekstra'])->sum('selisih');

        return view('keuangan.dashboard', compact(
            'staff',
            'totalRegulerMasuk',
            'operasionalReguler',
            'siapSalurReguler',
            'totalEkstraMasuk',
            'operasionalEkstra',
            'siapSalurEkstra',
            'totalKasOperasionalKantor',
            'totalDanaBackupEkstra'
        ));
    }

    /**
     * Halaman Infak Ekstra Khusus
     */
    public function infakEkstra()
    {
        $sessionEmail = session('logged_in_email');
        $staff = Staff::where('email', $sessionEmail)->where('role', 'keuangan')->first();

        $daftarProgram = ExtraProgram::all();
        $totalEkstra = ExtraProgram::sum('current_amount');
        $totalDanaBackupEkstra = DanaBackup::whereIn('sumber_dana', ['EKSTRA', 'ekstra'])->sum('selisih');

        $riwayatDanaBackup = DanaBackup::whereIn('sumber_dana', ['EKSTRA', 'ekstra'])
                                        ->with(['pengajuan.extraProgram'])
                                        ->orderBy('created_at', 'desc')
                                        ->get();

        $antreanPencairan = PengajuanPencairanEkstra::where('status', 'PENDING')
                                                    ->with('extraProgram')
                                                    ->get();

        return view('keuangan.keuangan_infak_ekstra', compact(
            'staff',
            'daftarProgram', 
            'totalEkstra', 
            'totalDanaBackupEkstra', 
            'riwayatDanaBackup',
            'antreanPencairan'
        ));
    }

    /**
     * Halaman Penyaluran & Verifikasi Nota Operasional
     */
    public function operasional()
    {
        $sessionEmail = session('logged_in_email');
        $staff = Staff::where('email', $sessionEmail)->where('role', 'keuangan')->first();
        if (!$staff) {
            return redirect()->route('login')->with('error', 'Akses ditolak.');
        }

        // Kalkulasi Saldo Dashboard Atas
        $totalRegulerMasuk = 10000000; 
        $operasionalReguler = $totalRegulerMasuk * 0.35;
        $operasionalEkstra = ExtraProgram::sum('dana_operasional_ekstra');
        $totalOperasionalGabungan = $operasionalReguler + $operasionalEkstra;

        // 1. Antrean Verifikasi Pencairan Dana (Belum dibayar keuangan)
        $antreanPencairan = OperationalRequest::where('status_pembina', 'disetujui')
                                                ->where('status_keuangan', 'pending')
                                                ->get();

        // 2. Antrean Verifikasi Nota & Sisa Belanja
        // FIX FIXED: Memisahkan properti select secara absolut dan mengunci status 'dilaporkan' dari operational_reports
        $antreanNotaRealisasi = \DB::table('operational_reports')
            ->join('operational_requests', 'operational_reports.operational_request_id', '=', 'operational_requests.id')
            ->where('operational_reports.status_keuangan', '=', 'dilaporkan')
            ->select(
                'operational_reports.id as report_id',
                'operational_reports.operational_request_id',
                'operational_reports.nota_global as nota_fisik',
                'operational_reports.total_realization as realisasi_nota',
                'operational_reports.selisih as selisih_kas',
                'operational_reports.status_keuangan as status_report',
                'operational_requests.title as nama_kegiatan', 
                'operational_requests.total_amount as modal_awal'
            )
            ->get();

        // 3. Riwayat Penyerahan Arus Kas Keluar Resmi (Bagian Bawah Dashboard)
        // FIX FIXED: Menggunakan strict comparison WHERE, menjamin data 'dilaporkan' tidak akan bisa tembus ke riwayat bawah
        $riwayatPencairan = OperationalRequest::where('status_keuangan', '=', 'disetujui')
                                                ->orderBy('id', 'desc')
                                                ->get();

        return view('keuangan.keuangan_operasional', compact(
            'staff',
            'operasionalReguler',
            'operasionalEkstra',
            'totalOperasionalGabungan',
            'antreanPencairan',
            'antreanNotaRealisasi',
            'riwayatPencairan'
        ));
    }

    /**
     * LOGIKA VERIFIKASI NOTA & BALANSI KAS OPERASIONAL OTOMATIS
     */
    public function konfirmasiNotaOperasional(Request $request, $id)
    {
        $report = \DB::table('operational_reports')->where('id', $id)->first();
        
        if (!$report) {
            return redirect()->back()->with('error', 'Data laporan operasional tidak ditemukan.');
        }

        $req = OperationalRequest::findOrFail($report->operational_request_id);

        $totalNota = \DB::table('operational_report_details')
                        ->where('operational_report_id', $id) 
                        ->sum('amount_realization');          
                        
        $modalAwal = $req->total_amount;
        $selisih = $modalAwal - $totalNota;

        \DB::beginTransaction();
        try {
            $programEkstra = ExtraProgram::first(); 

            if ($programEkstra) {
                if ($selisih > 0) {
                    $programEkstra->increment('dana_operasional_ekstra', $selisih);
                } elseif ($selisih < 0) {
                    $programEkstra->decrement('dana_operasional_ekstra', abs($selisih));
                }
            }

            // 1. Update status final di tabel induk laporan menjadi 'disetujui'
            \DB::table('operational_reports')->where('id', $id)->update([
                'status_keuangan' => 'disetujui',
                'updated_at'      => now()
            ]);

            // 2. Update status final pada pengajuan utama agar keluar dari antrean dan resmi masuk riwayat
            $req->status_keuangan = 'disetujui'; 
            $req->save();

            \DB::commit();
            return redirect()->back()->with('success', 'Nota belanja berhasil diverifikasi! Uang refund masuk, saldo kas operasional otomatis bertambah.');
            
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memverifikasi nota: ' . $e->getMessage());
        }
    }
}