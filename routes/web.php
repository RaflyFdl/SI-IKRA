<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\MemberDashboardController; // Tetap di-import untuk tab profil
use App\Http\Controllers\PaymentSimulationController; // Controller simulasi pembayaran
use App\Http\Controllers\ExtraProgramController; // ✅ IMPORT CONTROLLER BARU
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

    // Mengubah format uang jeti agar ringkas (Misal: 6.2Jt)
    if ($totalDanaTerkumpul >= 1000000) {
        $totalDanaFormat = round($totalDanaTerkumpul / 1000000, 1) . 'Jt';
    } else {
        $totalDanaFormat = number_format($totalDanaTerkumpul, 0, ',', '.');
    }

    // Menghitung berapa banyak program infak ekstra yang terdaftar
    $programAktif = DB::table('extra_programs')->count();
    $programTersalurkan = 3;

    // Mengambil data program ekstra
    $daftarProgramEkstra = DB::table('extra_programs')->get();

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

// Route verifikasi email
Route::get('/register/verify/{token}', [AuthController::class, 'verifyEmail'])->name('register.verify');


// ==========================================
// 2. JALUR KHUSUS ANGGOTA
// ==========================================
Route::get('/dashboard', [AuthController::class, 'memberDashboard'])->name('member.dashboard');
Route::get('/dashboard/infak-ekstra', [ProgramController::class, 'memberIndex'])->name('member.programs.index');

// 🚀 ROUTE BARU: ALUR INFAK EKSTRA ALA KITABISA
Route::get('/infak-ekstra/{id}', [ExtraProgramController::class, 'show'])->name('member.extra.show');
Route::post('/infak-ekstra/{id}/checkout', [ExtraProgramController::class, 'checkout'])->name('member.extra.checkout');
Route::get('/infak-ekstra/invoice/{transaction_id}', [ExtraProgramController::class, 'invoice'])->name('member.extra.invoice');
Route::post('/infak-ekstra/simulasikan/{transaction_id}', [ExtraProgramController::class, 'simulatePayment'])->name('member.extra.simulate');

Route::get('/dashboard/profil', [MemberDashboardController::class, 'profil'])->name('member.profil');

// Logout
Route::post('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');


// ==========================================
// 3. JALUR KHUSUS ADMIN
// ==========================================
Route::get('/admin', [AuthController::class, 'adminDashboard'])->name('admin.dashboard');

Route::post('/admin/approve/{id}', [AuthController::class, 'approveMember'])->name('admin.approve');

Route::post('/admin/reject/{id}', [AuthController::class, 'rejectMember'])->name('admin.member.reject');

// Kelola Program
Route::get('/admin/programs', [ProgramController::class, 'index'])->name('admin.programs.index');
Route::post('/admin/programs', [ProgramController::class, 'store'])->name('admin.programs.store');


// ==========================================
// 4. JALUR KHUSUS KEUANGAN
// ==========================================
Route::get('/keuangan/dashboard', [TransactionController::class, 'keuanganDashboard'])->name('keuangan.dashboard');

Route::get('/keuangan/infak-ekstra', [TransactionController::class, 'infakEkstraDashboard'])->name('keuangan.infak-ekstra');

Route::get('/keuangan/operasional', [TransactionController::class, 'operasionalDashboard'])->name('keuangan.operasional');


// ==========================================
// 4.5 JALUR KHUSUS OPERASIONAL
// ==========================================
Route::get('/operasional/dashboard', [AuthController::class, 'operationalDashboard'])->name('operational.dashboard');

Route::get('/operasional/jadwal', [ProgramController::class, 'operationalSchedule'])->name('operational.schedule');

Route::post('/operasional/program/{id}/update-date', [ProgramController::class, 'updateExecutionDate'])->name('operational.update-date');

Route::post('/operasional/program/{id}/complete', [ProgramController::class, 'completeProgram'])->name('operational.complete');


// ==========================================
// 5. WEBHOOK GATEWAY XENDIT
// ==========================================
Route::post('/webhook/xendit', [TransactionController::class, 'handleCallback']);


// ==========================================
// 6. SIMULASI PEMBAYARAN (KHUSUS TEST MODE)
// ==========================================
Route::post('/simulate-payment/{memberId}', [PaymentSimulationController::class, 'simulateRegularPayment'])
    ->name('simulation.regular');