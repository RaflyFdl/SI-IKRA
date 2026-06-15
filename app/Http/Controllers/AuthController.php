<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
// --- TAMBAHAN KODE DI SINI UNTUK LAYANAN EMAIL ---
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

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

        // Simpan data ke database tabel 'members'
        // Kita simpan ke dalam variabel $member baru agar bisa dibaca datanya oleh email
        $member = Member::create([
            'nama' => $request->nama,
            'angkatan' => $request->angkatan,
            'no_wa' => $request->no_wa,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password di-enkripsi agar aman
            'bukti_pendukung' => $fileName,
            'status' => 'pending' // Status otomatis pending, nunggu diapprove admin
        ]);

        // --- SELEPAN KODE OTOMATIS KIRIM EMAIL ---
        // Penjelasan: Kita ambil email si pendaftar ($member->email), lalu kirimkan surat WelcomeEmail yang diisi data si pendaftar.
        // Karena di template html kita manggil $user->name, di sini kita manipulasi objeknya sebagai 'user' agar klop.
        $userObject = (object) [
            'name' => $member->nama,
            'email' => $member->email
        ];
        
        try {
            Mail::to($member->email)->send(new WelcomeEmail($userObject));
        } catch (\Exception $e) {
            // Jika email gagal terkirim karena setelan lokal, sistem tidak akan crash
            Log::error('Gagal mengirim email pendaftaran: ' . $e->getMessage());
        }
        // --- SELESAI SELEPAN EMAIL ---

        // Setelah sukses, kembalikan ke halaman form dengan pesan sukses
        return redirect()->route('register')->with('success', 'Pendaftaran berhasil! Akun Anda sedang diperiksa oleh admin untuk pembuatan Virtual Account Muamalat.');
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
                    return redirect()->route('admin.dashboard')->with('success', 'Selamat datang Tim Operasional Lapangan, ' . $staff->nama);
                
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
            } else {
                return redirect()->back()->with('error', 'Akun Anggota Anda belum disetujui. Mohon tunggu aktivasi Virtual Account.');
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

            return redirect()->route('admin.dashboard')->with('success', 'Anggota ' . $member->nama . ' berhasil disetujui! VA Real-Time aktif: ' . $vaResmi);
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
}