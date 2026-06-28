<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;          
use App\Models\ExtraProgram;   
use App\Models\DanaBackup; 
use App\Models\PengajuanPencairanEkstra;

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

        // FIX: Menggunakan kolom 'selisih' dan whereIn untuk tabel laporan_penggunaan
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
     * 🌟 FIX: Fungsi Penampil Halaman Infak Ekstra Khusus
     * Route URL: /keuangan/infak-ekstra
     */
    public function infakEkstra()
    {
        // 1. Verifikasi Sesi
        $sessionEmail = session('logged_in_email');
        $staff = Staff::where('email', $sessionEmail)->where('role', 'keuangan')->first();

        // 2. Ambil Data Program Kerja Aktif untuk Grid Progress Bar
        $daftarProgram = ExtraProgram::all();

        // 3. Hitung Kalkulasi Total Dana Ekstra Masuk Global
        $totalEkstra = ExtraProgram::sum('current_amount');

        // 4. FIX: Hitung Akumulasi dari kolom 'selisih' di database
        $totalDanaBackupEkstra = DanaBackup::whereIn('sumber_dana', ['EKSTRA', 'ekstra'])->sum('selisih');

        // 5. FIX: Ambil Riwayat Pengembalian berdasarkan tabel laporan_penggunaan
        $riwayatDanaBackup = DanaBackup::whereIn('sumber_dana', ['EKSTRA', 'ekstra'])
                                        ->with(['pengajuan.extraProgram'])
                                        ->orderBy('created_at', 'desc')
                                        ->get();

        // 6. Ambil Daftar Antrean Permintaan Pencairan Dana Awal
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
}