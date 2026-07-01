<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\MemberDashboardController; // Tetap di-import untuk tab profil
use App\Http\Controllers\PaymentSimulationController; // Controller simulasi pembayaran
use App\Http\Controllers\ExtraProgramController; // ✅ IMPORT CONTROLLER BARU
use App\Http\Controllers\PenyaluranEkstraController; // ✅ IMPORT CONTROLLER PENYALURAN BARU
use App\Http\Controllers\PenyaluranRegulerController; // ✅ IMPORT CONTROLLER PENYALURAN REGULER BARU
use App\Http\Controllers\PodcastController; // ✅ IMPORT CONTROLLER PODCAST BARU
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

// 🛠️ FIX EROR: Menambahkan rute penolakan pendaftaran berkas anggota baru oleh Admin
Route::post('/admin/member/reject/{id}', [AuthController::class, 'rejectMember'])->name('admin.member.reject');

// Kelola Program
Route::get('/admin/programs', [ProgramController::class, 'index'])->name('admin.programs.index');
Route::post('/admin/programs', [ProgramController::class, 'store'])->name('admin.programs.store');


// ==========================================
// 4. JALUR KHUSUS KEUANGAN
// ==========================================
Route::get('/keuangan/dashboard', [TransactionController::class, 'keuanganDashboard'])->name('keuangan.dashboard');

Route::get('/keuangan/infak-ekstra', [TransactionController::class, 'infakEkstraDashboard'])->name('keuangan.infak-ekstra');

Route::get('/keuangan/operasional', [TransactionController::class, 'operasionalDashboard'])->name('keuangan.operasional');

// 🚀 ROUTE BARU KEUANGAN: Memproses cairkan uang tekor (reimburse)
Route::post('/keuangan/penyaluran-ekstra/reimburse/{pengajuanId}', [PenyaluranEkstraController::class, 'prosesReimburse'])->name('keuangan.reimburse.proses');

// 💰 ROUTE TAMBAHAN KEUANGAN: Memproses konfirmasi pencairan modal awal kerja (Upload Bukti Transfer)
Route::post('/keuangan/penyaluran-ekstra/cairkan/{pengajuanId}', [PenyaluranEkstraController::class, 'prosesCairkanAwal'])->name('keuangan.cairkan.proses');

// 💵 ROUTE BARU KEUANGAN (INFAK REGULER): Memproses unggah bukti transfer pencairan infak reguler
Route::post('/keuangan/penyaluran-reguler/cairkan/{id}', [PenyaluranRegulerController::class, 'prosesCairkanKeuangan'])->name('keuangan.penyaluran-reguler.cairkan');


// ==========================================
// 4.5 JALUR KHUSUS OPERASIONAL
// ==========================================
Route::get('/operasional/dashboard', [ProgramController::class, 'operationalDashboard'])->name('operational.dashboard');

Route::get('/operasional/jadwal', [ProgramController::class, 'operationalSchedule'])->name('operational.schedule');

Route::post('/operasional/program/{id}/update-date', [ProgramController::class, 'updateExecutionDate'])->name('operational.update-date');

Route::post('/operasional/program/{id}/complete', [ProgramController::class, 'completeProgram'])->name('operational.complete');

// 🛠️ TAMBAHAN ROUTE BARU OPERASIONAL: HALAMAN PENCAIRAN DANA EKSTRA (AWAL ALUR)
Route::get('/operasional/pencairan-ekstra', [PenyaluranEkstraController::class, 'pencairanEkstra'])->name('operational.pencairan');
Route::post('/operasional/pencairan-ekstra/store', [PenyaluranEkstraController::class, 'storePencairan'])->name('operational.pencairan.store');

// 🚀 ROUTE BARU OPERASIONAL: Menampilkan halaman form input nota belanja
Route::get('/operasional/penyaluran-ekstra/laporan/{pengajuanId}', [PenyaluranEkstraController::class, 'showLaporanForm'])->name('operational.laporan.form');

// 🚀 ROUTE BARU OPERASIONAL: Menginput/menyimpan laporan nota pengeluaran belanja dana ekstra
Route::post('/operasional/penyaluran-ekstra/laporan/{pengajuanId}', [PenyaluranEkstraController::class, 'simpanLaporan'])->name('operational.laporan.store');

// 💳 ROUTE BARU OPERASIONAL: Mengelola Penyaluran Infak Reguler
Route::get('/operasional/penyaluran-reguler', [PenyaluranRegulerController::class, 'indeksOperasional'])->name('operational.penyaluran-reguler.index');
Route::post('/operasional/penyaluran-reguler/store', [PenyaluranRegulerController::class, 'simpanPengajuan'])->name('operational.penyaluran-reguler.store');

// 📝 ROUTE BARU OPERASIONAL (INFAK REGULER): Form Input & Simpan Nota Belanja Kegiatan Reguler
Route::get('/operasional/penyaluran-reguler/laporan/{id}', [PenyaluranRegulerController::class, 'showLaporanForm'])->name('operational.penyaluran-reguler.laporan.form');
Route::post('/operasional/penyaluran-reguler/laporan/{id}', [PenyaluranRegulerController::class, 'simpanLaporan'])->name('operational.penyaluran-reguler.laporan.store');

// 📅 ROUTE BARU OPERASIONAL (JSON API): Jalur data kalender dinamis untuk dashboard operasional
Route::get('/operasional/calendar-events', [ProgramController::class, 'getCalendarEvents'])->name('operational.calendar.events');

// 🎙️ ROUTE BARU OPERASIONAL (PODCAST SYSTEM): Modul Kreatif & Pengajuan Dana Konten
Route::get('/operasional/podcast/create', [PodcastController::class, 'create'])->name('operational.podcast.create');
Route::post('/operasional/podcast/store', [PodcastController::class, 'store'])->name('operational.podcast.store');


// ==========================================
// 4.8 JALUR KHUSUS PEMBINA (TAMBAHAN BARU)
// ==========================================
// 👑 ROUTE BARU PEMBINA: Menampilkan halaman dashboard utama Pembina
Route::get('/pembina/dashboard', [AuthController::class, 'pembinaDashboard'])->name('pembina.dashboard');

// 👑 ROUTE BARU PEMBINA (INFAK REGULER): Menangani aksi setuju/tolak proposal reguler
Route::post('/pembina/penyaluran-reguler/approve/{id}', [PenyaluranRegulerController::class, 'prosesApprovalPembina'])->name('pembina.penyaluran-reguler.approve');


// ==========================================
// 5. WEBHOOK GATEWAY XENDIT
// ==========================================
Route::post('/webhook/xendit', [TransactionController::class, 'handleCallback']);


// ==========================================
// 6. SIMULASI PEMBAYARAN (KHUSUS TEST MODE)
// ==========================================
Route::post('/simulate-payment/{memberId}', [PaymentSimulationController::class, 'simulateRegularPayment'])
    ->name('simulation.regular');


// ==========================================
// 7. JALUR BACKDOOR SIMULASI (UNTUK SIDANG)
// ==========================================
// 🎯 DIUBAH SIKIT: Menyelaraskan nama parameter URL dengan variabel $vaNumber pada Controller baru
Route::get('/member/extra/backdoor/{vaNumber}', [ExtraProgramController::class, 'backdoorSimulate'])
    ->name('member.extra.backdoor');

// 🔍 ROUTE AUTO-CHECK: Mengecek status transaksi secara real-time dari script Blade
Route::get('/infak-ekstra/check-status/{transaction_id}', function($transaction_id) {
    $trx = DB::table('transactions')->where('id', $transaction_id)->first();
    return response()->json(['status' => $trx && $trx->payment_id ? 'lunas' : 'pending']);
})->name('member.extra.check-status');