<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;          // Di-import untuk cek session login keuangan
use App\Models\ExtraProgram;   // Di-import untuk menghitung akumulasi infak ekstra
// use App\Models\RegularTransaction; // SILAKAN DI-UNCOMMENT DAN SESUAIKAN JIKA NAMA MODEL TRANSAKSI REGULER KAMU SUDAH ADA

class KeuanganController extends Controller
{
    /**
     * Menampilkan Dashboard Utama Bagian Keuangan dengan Alokasi Otomatis 35% & 65%
     */
    public function dashboard()
    {
        // Kunci keamanan: Pastikan yang mengakses sudah login sebagai staff keuangan
        $sessionEmail = session('logged_in_email');

        if (!$sessionEmail) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $staff = Staff::where('email', $sessionEmail)->where('role', 'keuangan')->first();

        if (!$staff) {
            return redirect()->route('login')->with('error', 'Akses ditolak! Halaman ini khusus untuk Bagian Keuangan.');
        }

        // ===================================================================
        // LOGIC 1: HITUNG REAL-TIME TOTAL INFAK REGULER (DARI VA PRIBADI ANGGOTA)
        // ===================================================================
        // Sementara kita buat statis/contoh dulu Rp 10.000.000 jika model belum siap. 
        // Nanti jika tabel transaksi reguler sudah ada, tinggal ganti baris bawah ini dengan:
        // $totalRegulerMasuk = RegularTransaction::where('status', 'success')->sum('amount');
        $totalRegulerMasuk = 10000000; 

        // Rumus Alokasi Aturan IKRA (35% dan 65%)
        $operasionalReguler = $totalRegulerMasuk * 0.35;
        $siapSalurReguler   = $totalRegulerMasuk * 0.65;


        // ===================================================================
        // LOGIC 2: HITUNG REAL-TIME TOTAL INFAK EKSTRA (DARI VA PROGRAM EKSTRA)
        // ===================================================================
        // Menghitung total dana kotor yang terkumpul dari seluruh program ekstra di database
        $totalEkstraMasuk = ExtraProgram::sum('current_amount');

        // Rumus Alokasi Aturan IKRA (35% dan 65%)
        $operasionalEkstra = $totalEkstraMasuk * 0.35;
        $siapSalurEkstra   = $totalEkstraMasuk * 0.65;


        // ===================================================================
        // LOGIC 3: GABUNGAN REKAPITULASI KAS OPERASIONAL INTERNAL KANTOR
        // ===================================================================
        $totalKasOperasionalKantor = $operasionalReguler + $operasionalEkstra;

        // Kirim semua hasil perhitungan matematika ke view keuangan
        return view('keuangan.dashboard', compact(
            'staff',
            'totalRegulerMasuk',
            'operasionalReguler',
            'siapSalurReguler',
            'totalEkstraMasuk',
            'operasionalEkstra',
            'siapSalurEkstra',
            'totalKasOperasionalKantor'
        ));
    }
}