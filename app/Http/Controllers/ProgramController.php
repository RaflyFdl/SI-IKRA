<?php

namespace App\Http\Controllers;

use App\Models\ExtraProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProgramController extends Controller
{
    /**
     * 1. Menampilkan halaman form input admin & daftar program (Sisi Admin)
     */
    public function index()
    {
        $programs = ExtraProgram::latest()->get();
        return view('admin.programs.index', compact('programs'));
    }

    /**
     * 2. Memproses simpan program baru dari Admin (Sisi Admin)
     * Sekarang murni menyimpan ke database, karena VA dibuat dinamis di sisi anggota.
     */
    public function store(Request $request)
    {
        // Validasi input form admin
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:1000',
            'end_date' => 'required|date',
        ]);

        // Simpan data program ke database
        ExtraProgram::create([
            'name' => $request->name,
            'description' => $request->description,
            'target_amount' => $request->target_amount,
            'end_date' => $request->end_date,
            'status' => 'active'
        ]);

        return redirect()->back()->with('success', 'Program Infak Ekstra Berhasil Dipublikasikan!');
    }

    /**
     * 3. Menampilkan daftar program infak ekstra aktif (Sisi Anggota)
     * Otomatis membuat / mengambil Virtual Account khusus atas nama Anggota.
     */
    public function memberIndex()
    {
        // Mengambil semua program ekstra yang statusnya masih aktif
        $programs = ExtraProgram::where('status', 'active')->latest()->get();

        // TENTUKAN DATA USER SECARA PINTAR:
        // Cek auth bawaan dulu, jika kosong, ambil langsung dari session kustom login kamu.
        // Jika session juga kosong (misal saat testing manual), kita beri fallback ID 1 agar tidak crash.
        $user = auth()->user(); 
        
        $userId = $user ? $user->id : (session('user_id') ?? session('auth_id') ?? session('id') ?? 1);
        $userName = $user ? $user->name : (session('user_name') ?? session('name') ?? session('username') ?? 'Anggota IKRA');

        // Ambil Secret Key Xendit dari berkas .env
        $secretKey = env('XENDIT_SECRET_KEY');

        // Lakukan perulangan untuk membuatkan VA khusus anggota di setiap program
        foreach ($programs as $program) {
            
            // Format external_id unik kombinasi ID Program dan ID Anggota dari hasil deteksi di atas
            // Contoh hasil: program_2_user_1
            $externalId = 'program_' . $program->id . '_user_' . $userId;

            // Tembak API Xendit untuk membuat Open Virtual Account (VA Muamalat)
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($secretKey . ':')
                ])->post('https://api.xendit.co/callback_virtual_accounts', [
                    'external_id' => $externalId,
                    'bank_code' => 'MUAMALAT',
                    // Nama pemilik VA di ATM/m-Banking diambil dari nama session loginmu
                    'name' => 'IKRA - ' . substr($userName, 0, 15), 
                ]);

            if ($response->successful()) {
                $xenditData = $response->json();
                // Titipkan nomor VA asli dari Xendit ke dalam properti objek program
                $program->dynamic_va = $xenditData['account_number'];
            } else {
                // Catat error ke log jika integrasi Xendit mengalami kendala
                Log::error('Gagal membuat VA dinamis untuk User ID ' . $userId . ' di Program ' . $program->id . ': ' . $response->body());
                $program->dynamic_va = 'Gagal Memuat VA';
            }
        }

        // Kirim data program yang sudah disisipi VA dinamis ke halaman tampilan anggota
        return view('member.programs', compact('programs'));
    }
}