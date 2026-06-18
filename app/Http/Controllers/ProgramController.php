<?php

namespace App\Http\Controllers;

use App\Models\ExtraProgram;
use App\Models\Member; // Import model Member untuk mengambil email anggota
use App\Mail\ProgramEkstraDibuatMail; // Import kelas Mail kustom yang sudah dibuat sebelumnya
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; // Import Facade Mail untuk eksekusi kirim email

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
     * Mengakomodasi aturan dinamis: Donasi Umum wajib target/batas waktu, Podcast & Cinema bebas/terbuka.
     */
    public function store(Request $request)
    {
        // Validasi input fleksibel (target_amount, end_date, dan execution_date diubah jadi nullable agar aman)
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'target_amount' => 'nullable|numeric|min:1000',
            'end_date' => 'nullable|date',
            'execution_date' => 'nullable|date', // Validasi tanggal pelaksanaan opsional di awal
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Batasi gambar maks 2MB
        ]);

        // Proses upload foto jika ada file yang diunggah
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('programs', 'public');
        }

        // Tentukan nilai berdasarkan kategori pilihan
        $targetAmount = $request->target_amount;
        $endDate = $request->end_date;

        if (in_array($request->category, ['Podcast', 'Cinema Edukasi'])) {
            $targetAmount = null; // Tidak terbatas dana untuk media operasional
            $endDate = null;      // Terbuka terus tanpa batasan waktu penggalangan
        }

        // Simpan data program ke database
        $program = ExtraProgram::create([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'target_amount' => $targetAmount,
            'end_date' => $endDate,
            'execution_date' => $request->execution_date, 
            'image_path' => $imagePath,
            'status' => 'active'
        ]);

        // --- FITUR AUTOMATION REAL-TIME EMAIL NOTIFIKASI ANGGOTA ---
        // Ambil data semua anggota yang status akunnya sudah aktif resmi
        $activeMembers = Member::where('status', 'active')->get();

        if ($activeMembers->isNotEmpty()) {
            foreach ($activeMembers ?? [] as $member) {
                try {
                    // Kirim email massal dengan menyertakan objek data $program yang baru saja dibuat
                    Mail::to($member->email)->send(new ProgramEkstraDibuatMail($program));
                } catch (\Exception $e) {
                    // Jika ada satu email bermasalah, catat di log dan abaikan agar proses sisa anggota tidak terhenti
                    Log::error("Gagal mengirim notifikasi program ekstra baru ke {$member->email}: " . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('success', 'Program Infak Ekstra Berhasil Dipublikasikan dan email notifikasi telah disebarkan ke seluruh anggota resmi!');
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
        $user = auth()->user(); 
        
        $userId = $user ? $user->id : (session('user_id') ?? session('auth_id') ?? session('id') ?? 1);
        $userName = $user ? $user->name : (session('user_name') ?? session('name') ?? session('username') ?? 'Anggota IKRA');

        // Ambil Secret Key Xendit dari berkas .env
        $secretKey = env('XENDIT_SECRET_KEY');

        // Lakukan perulangan untuk membuatkan VA khusus anggota di setiap program
        foreach ($programs as $program) {
            
            // Format external_id unik kombinasi ID Program dan ID Anggota
            $externalId = 'program_' . $program->id . '_user_' . $userId;

            // Tembak API Xendit untuk membuat Open Virtual Account (VA Muamalat)
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($secretKey . ':')
                ])->post('https://api.xendit.co/callback_virtual_accounts', [
                    'external_id' => $externalId,
                    'bank_code' => 'MUAMALAT',
                    'name' => 'IKRA - ' . substr($userName, 0, 15), 
                ]);

            if ($response->successful()) {
                $xenditData = $response->json();
                $program->dynamic_va = $xenditData['account_number'];
            } else {
                Log::error('Gagal membuat VA dinamis untuk User ID ' . $userId . ' di Program ' . $program->id . ': ' . $response->body());
                $program->dynamic_va = 'Gagal Memuat VA';
            }
        }

        return view('member.programs', compact('programs'));
    }

    // ==========================================
    // LOGIKA & FITUR KHUSUS TIM OPERASIONAL
    // ==========================================

    /**
     * 4. Menampilkan Dashboard Ringkasan Kerja Operasional (Sudah disesuaikan)
     */
    public function operationalDashboard()
    {
        // Hitung total program kerja berdasarkan 3 kategori utama
        $totalDonasi = ExtraProgram::where('category', 'Donasi Umum')->count();
        $totalPodcast = ExtraProgram::where('category', 'Podcast')->count();
        $totalCinema = ExtraProgram::where('category', 'Cinema Edukasi')->count();
        
        // Hitung semua program aktif yang tanggal pelaksanaannya belum dijadwalkan oleh operasional
        $jadwalPending = ExtraProgram::where('status', 'active')
                                     ->whereNull('execution_date')
                                     ->count();

        return view('operational.dashboard', compact('totalDonasi', 'totalPodcast', 'totalCinema', 'jadwalPending'));
    }

    /**
     * 5. Menampilkan Halaman Pusat Jadwal Kerja Terintegrasi (3 Pilihan Kategori / Tab)
     */
    public function operationalSchedule(Request $request)
    {
        // Menangkap tab aktif dari request link navigasi, set default ke 'donasi' jika kosong
        $activeTab = $request->get('tab', 'donasi');

        // Mengambil data program lengkap dari database dipisahkan berdasarkan kategorinya
        $donasiPrograms = ExtraProgram::where('category', 'Donasi Umum')
                                      ->orderBy('execution_date', 'asc')
                                      ->get();

        $podcastPrograms = ExtraProgram::where('category', 'Podcast')
                                       ->orderBy('execution_date', 'asc')
                                       ->get();

        $cinemaPrograms = ExtraProgram::where('category', 'Cinema Edukasi')
                                      ->orderBy('execution_date', 'asc')
                                      ->get();

        return view('operational.schedule', compact(
            'donasiPrograms', 
            'podcastPrograms', 
            'cinemaPrograms', 
            'activeTab'
        ));
    }

    /**
     * 6. Memproses Pengaturan atau Perubahan Tanggal Pelaksanaan (Aksi Operasional)
     */
    public function updateExecutionDate(Request $request, $id)
    {
        $request->validate([
            'execution_date' => 'required|date',
        ]);

        $program = ExtraProgram::findOrFail($id);
        $program->update([
            'execution_date' => $request->execution_date
        ]);

        return redirect()->back()->with('success', 'Tanggal pelaksanaan kerja program berhasil diperbarui!');
    }

    /**
     * 7. Memproses Selesai Program & Simpan Upload Bukti Dokumentasi Lapangan (Aksi Operasional)
     */
    public function completeProgram(Request $request, $id)
    {
        // Validasi file bukti wajib berupa gambar dan maksimal ukuran 2MB
        $request->validate([
            'documentation' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $program = ExtraProgram::findOrFail($id);

        // Proses penyimpanan file gambar ke dalam folder storage/app/public/documentations
        $docPath = null;
        if ($request->hasFile('documentation')) {
            $docPath = $request->file('documentation')->store('documentations', 'public');
        }

        // Ubah status program menjadi 'completed' dan isi field documentation_path
        $program->update([
            'status' => 'completed',
            'documentation_path' => $docPath
        ]);

        return redirect()->back()->with('success', 'Selamat! Program berhasil diselesaikan dan bukti dokumentasi lapangan telah diarsipkan.');
    }
}