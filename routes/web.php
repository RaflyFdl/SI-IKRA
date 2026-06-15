<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\MemberDashboardController; // Tetap di-import untuk tab profil
use Illuminate\Support\Facades\DB;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Infak IKRA
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. JALUR UMUM & OTENTIKASI (PUBLIK)
// ==========================================
Route::get('/', function () {
    // 1. Menghitung data asli untuk Stats Bar di Home Page
    $anggotaAktif = DB::table('members')->count() ?? 0; 
    
    // Menghitung total dana dari semua transaksi (Reguler + Ekstra)
    $totalDanaTerkumpul = DB::table('transactions')->sum('amount') ?? 0; 
    
    // Mengubah format uang jeti agar ringkas (Misal: 6.2Jt) seperti di gambar
    if ($totalDanaTerkumpul >= 1000000) {
        $totalDanaFormat = round($totalDanaTerkumpul / 1000000, 1) . 'Jt';
    } else {
        $totalDanaFormat = number_format($totalDanaTerkumpul, 0, ',', '.');
    }

    // Menghitung berapa banyak program infak ekstra yang terdaftar
    $programAktif = DB::table('extra_programs')->count();
    $programTersalurkan = 3; // Sementara di-hardcode sesuai indikator awal gambar

    // 2. Mengambil data program ekstra untuk ditampilkan di bagian bawah
    $daftarProgramEkstra = DB::table('extra_programs')->get();

    // 3. Mengirimkan data asli ini ke dalam file welcome.blade.php
    return view('welcome', compact(
        'anggotaAktif', 
        'programAktif', 
        'totalDanaFormat', 
        'programTersalurkan', 
        'daftarProgramEkstra'
    ));
});

// Route untuk login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'processLogin']);

// Route untuk pendaftaran anggota baru
Route::get('/daftar', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/daftar', [AuthController::class, 'register'])->name('register.store');


// ==========================================
// 2. JALUR KHUSUS ANGGOTA (Bypass Temp demi Demo - DIKEMBALIKAN KE ASLINYA)
// ==========================================
// Kunci utama: Menghubungkan halaman utama anggota KEMBALI ke AuthController persis seperti kode awalmu agar bisa login
Route::get('/dashboard', [AuthController::class, 'memberDashboard'])->name('member.dashboard');
Route::get('/dashboard/infak-ekstra', [ProgramController::class, 'memberIndex'])->name('member.programs.index');

// HALAMAN BARU: Menggunakan fungsi profil namun tanpa kurungan middleware ketat demi demo kelancaran login
Route::get('/dashboard/profil', [MemberDashboardController::class, 'profil'])->name('member.profil');

// PROSES LOGOUT: Diubah mengarah ke Homepage ('/') setelah berhasil hapus sesi
Route::post('/logout', function() {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/'); // <- Mengarah ke landing page utama sistem infak
})->name('logout');


// ==========================================
// 3. JALUR KHUSUS ADMINISTRATOR (ADMIN)
// ==========================================
Route::get('/admin', [AuthController::class, 'adminDashboard'])->name('admin.dashboard');
Route::post('/admin/approve/{id}', [AuthController::class, 'approveMember'])->name('admin.approve');

// Kelola Program Infak Ekstra (Sisi Admin)
Route::get('/admin/programs', [ProgramController::class, 'index'])->name('admin.programs.index');
Route::post('/admin/programs', [ProgramController::class, 'store'])->name('admin.programs.store');


// ==========================================
// 4. JALUR KHUSUS KEUANGAN (FINANCE)
// ==========================================
Route::get('/keuangan/dashboard', [TransactionController::class, 'keuanganDashboard'])->name('keuangan.dashboard');

// Halaman Khusus Laporan Penerimaan Infak Ekstra (Sisi Keuangan)
Route::get('/keuangan/infak-ekstra', [TransactionController::class, 'infakEkstraDashboard'])->name('keuangan.infak-ekstra');


// ==========================================
// 5. WEBHOOK GATEWAY (XENDIT EXTERNAL API)
// ==========================================
// Webhook tunggal dinamis (Bisa memproses Infak Reguler & Ekstra sekaligus secara otomatis)
Route::post('/webhook/xendit', [TransactionController::class, 'handleCallback']);