<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Staff;
use App\Models\ExtraProgram; // Di-import agar fungsi hitung summary program operasional berjalan lancar
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
// --- TAMBAHAN KODE DI SINI UNTUK LAYANAN EMAIL ---
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifikasiPendaftaranMail;
use App\Mail\SelamatDatangAnggotaMail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // 1. Fungsi untuk menampilkan halaman form pendaftaran
    public function showRegisterForm()
    {
        return view('register');
    }

    // 2. Fungsi untuk memproses data dari form pendaftaran
    public function register(Request $request)
    {
        // Validasi inputan form dari user agar data yang masuk rapi dan aman
        $request->validate([
            'nama' => 'required|string|max:255',
            'angkatan' => 'required|string|size:4',
            'no_wa' => 'required|string|max:15',
            'email' => 'required|string|email|max:255|unique:members',
            'password' => 'required|string|min:6|confirmed',
            'bukti_pendukung' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maksimal file 2MB berbentuk gambar
        ]);

        // Process upload file bukti pendukung (ijazah/KTM) ke folder 'public/uploads'
        $fileName = time() . '_' . $request->file('bukti_pendukung')->getClientOriginalName();
        $request->file('bukti_pendukung')->move(public_path('uploads'), $fileName);

        // Membuat token acak unik untuk verifikasi email
        $tokenVerifikasi = Str::random(64);

        // Simpan data ke database tabel 'members'
        // Status otomatis 'pending_verification' agar tidak bisa login/di-approve admin sebelum verifikasi email
        $member = Member::create([
            'nama' => $request->nama,
            'angkatan' => $request->angkatan,
            'no_wa' => $request->no_wa,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password di-enkripsi agar aman
            'bukti_pendukung' => $fileName,
            'verification_token' => $tokenVerifikasi, // Pastikan kolom ini ada di database tabel members kamu
            'status' => 'pending_verification' 
        ]);

        // Menyusun URL verifikasi untuk diklik pendaftar di dalam emailnya
        $linkVerifikasi = route('register.verify', $tokenVerifikasi);
        
        try {
            // Mengirimkan email verifikasi menggunakan class VerifikasiPendaftaranMail yang sudah kita buat
            Mail::to($member->email)->send(new VerifikasiPendaftaranMail($linkVerifikasi));
        } catch (\Exception $e) {
            // Jika email gagal terkirim karena setelan lokal, sistem tidak akan crash
            Log::error('Gagal mengirim email verifikasi pendaftaran: ' . $e->getMessage());
        }

        // Setelah sukses, kembalikan ke halaman form dengan pesan instruksi verifikasi email
        return redirect()->route('register')->with('success', 'Pendaftaran berhasil! Silakan periksa kotak masuk atau folder spam email Anda untuk melakukan verifikasi akun.');
    }

    // --- FUNGSI BARU: MENANGKAP KLIK LINK VERIFIKASI DARI EMAIL USER (SUDAH DIOPTIMALKAN) ---
    public function verifyEmail($token)
    {
        // Hilangkan kemungkinan adanya karakter spasi/newline bawaan pembaca email HTML
        $cleanToken = trim($token);

        // Cari member berdasarkan token unik yang dikirimkan
        $member = Member::where('verification_token', $cleanToken)->first();

        // Solusi jika token tidak valid/tidak ditemukan
        if (!$member) {
            return redirect()->route('login')->with('error', 'Token verifikasi tidak valid atau sudah kedaluwarsa.');
        }

        // Ubah status token menjadi null dan naikkan status menjadi 'pending' (menunggu verifikasi admin alumni)
        $member->update([
            'verification_token' => null,
            'status' => 'pending',
            'email_verified_at' => now() // Menandai bahwa email pendaftar sudah valid murni milik mereka
        ]);

        return redirect()->route('login')->with('success', 'Email Anda berhasil diverifikasi! Pendaftaran Anda kini sedang diajukan ke Admin Yayasan untuk diperiksa status alumninya.');
    }

    // 3. Fungsi untuk menampilkan halaman login
    public function showLogin()
    {
        return view('login');
    }

    // 4. Fungsi untuk memproses login dan mendeteksi hak akses secara otomatis (Multi-Role)
    public function processLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Cek pertama kali ke tabel Staffs (Admin, Keuangan, Operasional, Pembina)
        $staff = Staff::where('email', $request->email)->first();
        
        if ($staff) {
            // Simpan email staff yang login ke dalam session agar bisa dilacak halaman lain jika butuh
            session(['logged_in_email' => $staff->email]);

            switch ($staff->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard')->with('success', 'Selamat datang Admin, ' . $staff->nama);
                
                case 'keuangan':
                    return redirect()->route('keuangan.dashboard')->with('success', 'Selamat datang Bagian Keuangan, ' . $staff->nama);
                
                case 'operasional':
                    // --- SEKARANG SUDAH DIARAHKAN KE DASHBOARD OPERASIONAL ---
                    return redirect()->route('operational.dashboard')->with('success', 'Selamat datang Tim Operasional Lapangan, ' . $staff->nama);
                
                case 'pembina':
                    return redirect()->route('admin.dashboard')->with('success', 'Selamat datang Pembina Yayasan, ' . $staff->nama);
                
                default:
                    return redirect()->back()->with('error', 'Role staff tidak dikenali.');
            }
        }

        // Jika tidak ada di tabel Staffs, barulah cek ke tabel Anggota (Members)
        $member = Member::where('email', $request->email)->first();
        if ($member) {
            if ($member->status === 'active') {
                
                // KUNCI AMAN: Simpan email Anggota yang sedang login ke dalam Session lokal
                session(['logged_in_email' => $member->email]);
                
                return redirect()->route('member.dashboard')->with('success', 'Selamat datang Anggota, ' . $member->nama . '!');
            } elseif ($member->status === 'pending_verification') {
                return redirect()->back()->with('error', 'Akun Anda belum diverifikasi. Silakan klik link verifikasi yang dikirimkan ke email Anda terlebih dahulu.');
            } else {
                return redirect()->back()->with('error', 'Akun Anggota Anda belum disetujui oleh Admin. Mohon tunggu aktivasi Virtual Account.');
            }
        }

        // Jika email benar-benar tidak ditemukan di kedua tabel
        return redirect()->back()->with('error', 'Alamat email tidak terdaftar dalam sistem.');
    }

    // 5. SOLUSI FIX DASHBOARD: Membaca murni dari email yang disimpan saat klik tombol login
    public function memberDashboard()
    {
        // 1. Ambil email dari akun yang baru saja disubmit di form login
        $sessionEmail = session('logged_in_email');

        // Jika tidak ada riwayat email login di session, kunci dan lempar balik ke login
        if (!$sessionEmail) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Cari data member di database yang email-nya COCOK dengan yang sedang login tersebut
        // Jika Ahmad Fauzi yang login, maka baris data Ahmad Fauzi yang ditarik secara eksklusif!
        $member = Member::where('email', $sessionEmail)->first();

        // Antisipasi pelindung data
        if (!$member) {
            return "Profil anggota Anda tidak ditemukan di database. Hubungi pihak yayasan.";
        }

        // 3. Kembalikan ke halaman dashboard dengan melemparkan data asli pemilik akun
        return view('member.dashboard', compact('member'));
    }

    // 6. Fungsi untuk menampilkan halaman dashboard pengurus/admin
    public function adminDashboard()
    {
        // Mengambil semua data member yang mendaftar, diurutkan dari yang terbaru
        $members = Member::orderBy('created_at', 'desc')->get();
        
        return view('admin_dashboard', compact('members'));
    }

    // 7. Fungsi untuk menyetujui anggota & generate VA otomatis (Real-Time Xendit Sandbox)
    public function approveMember($id)
    {
        $member = Member::findOrFail($id);

        // --- FORMULASI NOMOR VA BARU ---
        $nomorUrut = str_pad($member->id, 4, '0', STR_PAD_LEFT);
        $noVaKustom = "808701002016" . $nomorUrut;

        // --- MENEMBAK API XENDIT SANDBOX ---
        $apiKey = env('XENDIT_SECRET_KEY');
        
        // Menggunakan Http::withoutVerifying() untuk bypass error SSL di Laragon/Windows
        $response = Http::withoutVerifying()
        ->withHeaders([
            'Authorization' => 'Basic ' . base64_encode($apiKey . ':'),
            'Content-Type' => 'application/json'
        ])->post('https://api.xendit.co/callback_virtual_accounts', [
            'external_id' => 'va_member_' . $member->id,
            'bank_code' => 'MUAMALAT', // Tetap mengunci ke Bank Muamalat
            'name' => $member->nama,
            'is_closed' => false 
        ]);

        // Cek apakah server Xendit sukses memproses
        if ($response->successful()) {
            $responseData = $response->json();
            $vaResmi = $responseData['account_number'];

            // Sukses -> Update database lokal tabel members
            $member->update([
                'status' => 'active',
                'va_muamalat' => $vaResmi
            ]);

            // --- KIRIM EMAIL SELAMAT DATANG SETELAH DI-APPROVE ADMIN ---
            try {
                Mail::to($member->email)->send(new SelamatDatangAnggotaMail($member->nama));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim email selamat datang anggota resmi: ' . $e->getMessage());
            }

            return redirect()->route('admin.dashboard')->with('success', 'Anggota ' . $member->nama . ' berhasil disetujui! VA Real-Time aktif dan Email Selamat datang telah terkirim.');
        } else {
            // JIKA GAGAL: Tangkap pesan error dari Xendit dan tampilkan langsung di layar
            $errorData = $response->json();
            $pesanError = $errorData['message'] ?? 'Terjadi kesalahan sistem luar.';
            
            // Log error secara detail ke storage/logs/laravel.log
            Log::error('Gagal Xendit: ' . json_encode($errorData));

            // Kembalikan ke halaman dengan pesan merah menggunakan dd() agar error terbaca jelas
            dd('Xendit menolak request! Alasan dari Xendit: ' . $pesanError, 'Data yang dikirim:', [
                'name' => $member->nama,
                'va_requested' => $noVaKustom
            ]);
        }
    }

    /**
     * 8. Menampilkan Dashboard Khusus Tim Operasional (Ambil murni dari session login staff)
     */
    public function operationalDashboard(Request $request)
    {
        $sessionEmail = session('logged_in_email');

        if (!$sessionEmail) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cari data staff berdasarkan email login dan kunci rolenya wajib 'operasional'
        $staff = Staff::where('email', $sessionEmail)->where('role', 'operasional')->first();

        if (!$staff) {
            return redirect()->route('login')->with('error', 'Akses ditolak! Halaman ini khusus untuk Tim Operasional.');
        }

        // Ambil parameter tab aktif dari URL request (default ke 'donasi')
        $activeTab = $request->query('tab', 'donasi');

        // Mengambil kumpulan data program infak ekstra per kategori agar tabel di view tidak kosong/eror
        $donasiPrograms = ExtraProgram::where('category', 'Donasi Umum')->orderBy('execution_date', 'asc')->get();
        $podcastPrograms = ExtraProgram::where('category', 'Podcast')->orderBy('execution_date', 'asc')->get();
        $cinemaPrograms = ExtraProgram::where('category', 'Cinema Edukasi')->orderBy('execution_date', 'asc')->get();

        // Hitung ringkasan status operasional program infak ekstra
        $totalDonasi = ExtraProgram::where('category', 'Donasi Umum')->count();
        $totalPodcast = ExtraProgram::where('category', 'Podcast')->count();
        $totalCinema = ExtraProgram::where('category', 'Cinema Edukasi')->count();
        
        $jadwalPending = ExtraProgram::where('status', 'active')
                                     ->whereNull('execution_date')
                                     ->count();

        // Mengirimkan seluruh variabel data ringkasan beserta data array program ke dashboard.blade.php
        return view('operational.dashboard', compact(
            'totalDonasi', 
            'totalPodcast', 
            'totalCinema', 
            'jadwalPending',
            'activeTab',
            'donasiPrograms',
            'podcastPrograms',
            'cinemaPrograms'
        ));
    }
}