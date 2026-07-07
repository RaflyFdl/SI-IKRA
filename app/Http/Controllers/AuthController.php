<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Staff;
use App\Models\ExtraProgram;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifikasiPendaftaranMail;
use App\Mail\SelamatDatangAnggotaMail;
use App\Mail\PendaftaranDitolakMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
        $request->validate([
            'nama' => 'required|string|max:255',
            'angkatan' => 'required|string|size:4',
            'no_wa' => 'required|string|max:15',
            'email' => 'required|string|email|max:255|unique:members',
            'password' => 'required|string|min:6|confirmed',
            'bukti_pendukung' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $fileName = time() . '_' . $request->file('bukti_pendukung')->getClientOriginalName();
        $request->file('bukti_pendukung')->move(public_path('uploads'), $fileName);

        $tokenVerifikasi = Str::random(64);

        $member = Member::create([
            'nama' => $request->nama,
            'angkatan' => $request->angkatan,
            'no_wa' => $request->no_wa,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'bukti_pendukung' => $fileName,
            'verification_token' => $tokenVerifikasi,
            'status' => 'pending_verification'
        ]);

        $linkVerifikasi = route('register.verify', $tokenVerifikasi);

        try {
            Mail::to($member->email)->send(new VerifikasiPendaftaranMail($linkVerifikasi));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email verifikasi pendaftaran: ' . $e->getMessage());
        }

        return redirect()->route('register')
            ->with('success', 'Pendaftaran berhasil! Silakan verifikasi email.');
    }

    // 3. Verifikasi email
    public function verifyEmail($token)
    {
        $cleanToken = trim($token);

        $member = Member::where('verification_token', $cleanToken)->first();

        if (!$member) {
            return redirect()->route('login')->with('error', 'Token tidak valid.');
        }

        $member->update([
            'verification_token' => null,
            'status' => 'pending',
            'email_verified_at' => now()
        ]);

        return redirect()->route('login')->with('success', 'Email berhasil diverifikasi.');
    }

    // 4. Login
    public function showLogin()
    {
        return view('login');
    }

    // 🎯 MODIFIKASI: Menambahkan validasi & pencocokan password pada proses Login
    public function processLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required', // Tambahkan validasi wajib diisi
        ]);

        // Cek apakah akun merupakan Staff terlebih dahulu
        $staff = Staff::where('email', $request->email)->first();

        // Jika Staff ditemukan, cek kecocokan password-nya
        if ($staff && Hash::check($request->password, $staff->password)) {
            session(['logged_in_email' => $staff->email]);

            switch ($staff->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'keuangan':
                    return redirect()->route('keuangan.dashboard');
                case 'operasional':
                    return redirect()->route('operational.dashboard');
                case 'pembina':
                    // ✅ DIUBAH: Sekarang mengarah ke route dashboard pembina sendiri
                    return redirect()->route('pembina.dashboard');
            }
        }

        // Jika bukan staff, cek apakah akun merupakan Member
        $member = Member::where('email', $request->email)->first();

        // Jika Member ditemukan, cek kecocokan password-nya
        if ($member && Hash::check($request->password, $member->password)) {
            if ($member->status === 'active') {
                session(['logged_in_email' => $member->email]);
                return redirect()->route('member.dashboard');
            } else {
                return back()->with('error', 'Akun Anda belum aktif atau sedang dalam peninjauan.');
            }
        }

        // Jika email salah, password salah, atau akun tidak terdaftar
        return back()->with('error', 'Email atau password yang Anda masukkan salah.');
    }

    // 5. Dashboard member
    public function memberDashboard()
    {
        $sessionEmail = session('logged_in_email');

        if (!$sessionEmail) {
            return redirect()->route('login');
        }

        $member = Member::where('email', $sessionEmail)->first();

        if (!$member) {
            session()->forget('logged_in_email');
            return redirect()->route('login')->with('error', 'Member tidak ditemukan.');
        }

        $sudahBayarBulanIni = DB::table('transactions')
            ->where('member_id', $member->id)
            ->where('transaction_type', 'reguler') 
            ->whereNotNull('payment_id') 
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->exists();

        $transactions = DB::table('transactions')
            ->where('member_id', $member->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('member.dashboard', compact('member', 'sudahBayarBulanIni', 'transactions'));
    }

    // 6. Admin dashboard
    public function adminDashboard()
    {
        $members = Member::orderBy('created_at', 'desc')->get();
        return view('admin_dashboard', compact('members'));
    }

    // 7. APPROVE MEMBER (STRATEGI TITIP ID KE KOLOM VERIFICATION_TOKEN)
    public function approveMember($id)
    {
        $member = Member::findOrFail($id);

        $nomorUrut = str_pad($member->id, 4, '0', STR_PAD_LEFT);
        $noVaKustom = "808701002016" . $nomorUrut;

        $apiKey = config('services.xendit.secret_key');

        $response = Http::withoutVerifying()
            ->withBasicAuth($apiKey, '')
            ->post('https://api.xendit.co/callback_virtual_accounts', [
                'external_id' => 'va_member_' . $member->id,
                'bank_code' => 'MUAMALAT',
                'name' => $member->nama,
                'is_closed' => false
            ]);

        if ($response->successful()) {
            $responseData = $response->json();
            $vaResmi = $responseData['account_number'];
            $xenditVaId = $responseData['id']; // Ambil ID Virtual Account dari response Xendit

            // ✅ DIUBAH: Menyimpan ID Xendit ke 'verification_token' agar terhindar dari Error Undefined Column
            $member->update([
                'status' => 'active',
                'va_muamalat' => $vaResmi,
                'verification_token' => $xenditVaId 
            ]);

            try {
                Mail::to($member->email)->send(new SelamatDatangAnggotaMail($member->nama));
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }

            return redirect()->route('admin.dashboard')
                ->with('success', 'Member berhasil diapprove & VA aktif.');
        }

        $errorData = $response->json();
        Log::error('Xendit Error: ' . json_encode($errorData));
        dd('Xendit gagal', $errorData);
    }

    // 8. reject member
    public function rejectMember(Request $request, $id)
    {
        $request->validate(['alasan' => 'required']);

        $member = Member::findOrFail($id);

        Mail::to($member->email)->send(new PendaftaranDitolakMail($member->nama, $request->alasan));

        $member->delete();

        return back()->with('success', 'Pendaftaran ditolak.');
    }

    // 9. operational dashboard
    public function operationalDashboard(Request $request)
    {
        $sessionEmail = session('logged_in_email');

        $staff = Staff::where('email', $sessionEmail)->first();

        if (!$staff) return redirect()->route('login');

        return view('operational.dashboard');
    }

    /**
     * 👑 10. PEMBINA DASHBOARD (TAMBAHAN BARU)
     * Mengatur hak akses masuk ke halaman dashboard utama Pembina
     */
    public function pembinaDashboard(Request $request)
    {
        $sessionEmail = session('logged_in_email');

        $staff = Staff::where('email', $sessionEmail)->first();

        if (!$staff || $staff->role !== 'pembina') {
            return redirect()->route('login');
        }

        // Ambil data rencana penyaluran reguler yang butuh diverifikasi oleh Pembina
        $pengajuanMasuk = \App\Models\PenyaluranReguler::orderBy('created_at', 'desc')->get();

        // ✅ DIUBAH: Mengarah ke view di dalam folder resources/views/pembina/dashboard.blade.php
        return view('pembina.dashboard', compact('staff', 'pengajuanMasuk'));
    }
}