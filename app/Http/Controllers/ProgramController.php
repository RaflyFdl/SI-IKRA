<?php

namespace App\Http\Controllers;

use App\Models\ExtraProgram;
use App\Models\DetailKebutuhanProgramEkstra;
use App\Models\Member;
use App\Models\Podcast;
use App\Models\CinemaEdukasi;
use App\Mail\ProgramEkstraDibuatMail;
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
        $busyDates = ExtraProgram::getBusyDates();
        return view('admin.programs.index', compact('programs', 'busyDates'));
    }

    /**
     * 2. Memproses simpan program baru dari Admin (Sisi Admin)
     * Mengakomodasi aturan dinamis: Donasi Umum wajib target/batas waktu, Podcast & Cinema bebas/terbuka.
     */
    public function store(Request $request)
    {
        // Validasi input fleksibel 
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'target_amount' => 'nullable|numeric|min:0',
            'end_date' => 'nullable|date',
            'execution_date' => 'nullable|date', // Validasi tanggal pelaksanaan opsional di awal
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Batasi gambar maks 2MB
            'nama_barang' => 'nullable|array',
            'nama_barang.*' => 'required|string',
            'jumlah' => 'nullable|array',
            'jumlah.*' => 'required|integer|min:1',
            'satuan' => 'nullable|array',
            'satuan.*' => 'nullable|string',
            'harga' => 'nullable|array',
            'harga.*' => 'required|numeric|min:0',
        ]);

        // Proses upload foto jika ada file yang diunggah
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('programs', 'public');
        }

        // Tentukan nilai berdasarkan kategori pilihan
        $targetAmount = $request->target_amount;
        $endDate = $request->end_date;
        $executionDate = $request->execution_date;

        if (in_array($request->category, ['Podcast', 'Cinema Edukasi'])) {
            // Program berkelanjutan: target dan tanggal tidak diperlukan, biarkan NULL
            $targetAmount = 0;
            $endDate      = '2099-12-31'; // Placeholder agar kolom NOT NULL terpenuhi
            $executionDate = null;         // Jadwal diatur via sistem penjadwalan, bukan dari sini
        }

        // Simpan data program ke database
        $program = ExtraProgram::create([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'target_amount' => $targetAmount,
            'end_date' => $endDate,
            'execution_date' => $executionDate, 
            'image_path' => $imagePath,
            'status' => 'active'
        ]);

        // Simpan rincian kebutuhan dana jika ada
        if ($request->has('nama_barang') && is_array($request->nama_barang)) {
            foreach ($request->nama_barang as $key => $namaBarang) {
                if (!empty($namaBarang)) {
                    DetailKebutuhanProgramEkstra::create([
                        'extra_program_id' => $program->id,
                        'nama_barang' => $namaBarang,
                        'jumlah' => $request->jumlah[$key] ?? 1,
                        'satuan' => $request->satuan[$key] ?? null,
                        'harga' => $request->harga[$key] ?? 0,
                    ]);
                }
            }
        }

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
     * 4. Menampilkan Dashboard Ringkasan Kerja Operasional (SINKRON DENGAN MODEL PODCAST)
     */
    public function operationalDashboard()
    {
        // Hitung total program kerja berdasarkan kategori masing-masing
        $totalDonasi = ExtraProgram::where('category', 'Donasi Umum')->count();
        $totalCinema = ExtraProgram::where('category', 'Cinema Edukasi')->count();
        
        // Hitung jumlah data asli dari model/tabel Podcast
        $totalPodcast = Podcast::count(); 
        
        // Hitung semua program aktif yang tanggal pelaksanaannya belum dijadwalkan oleh operasional
        $jadwalPending = ExtraProgram::where('status', 'active')
                                     ->whereNull('execution_date')
                                     ->count();

        return view('operational.dashboard', compact('totalDonasi', 'totalPodcast', 'totalCinema', 'jadwalPending'));
    }

    /**
     * 5. Menampilkan Halaman Pusat Jadwal Kerja Terintegrasi (SINKRON DENGAN MODEL PODCAST)
     */
    public function operationalSchedule(Request $request)
    {
        // Menangkap tab aktif dari request link navigasi, set default ke 'donasi' jika kosong
        $activeTab = $request->get('tab', 'donasi');

        // Mengambil data program lengkap dari database dipisahkan berdasarkan kategorinya
        $donasiPrograms = ExtraProgram::where('category', 'Donasi Umum')
                                      ->orderBy('execution_date', 'asc')
                                      ->get();

        // Mengambil data jadwal langsung dari model Podcast, bukan ExtraProgram
        $podcastPrograms = Podcast::orderBy('taping_date', 'asc')
                                  ->get();

        // Mengambil data jadwal Cinema Edukasi dari tabel cinema_edukasi (multi-jadwal)
        $cinemaPrograms = CinemaEdukasi::orderBy('jadwal_kegiatan', 'asc')
                                       ->get();

        // 🎯 GABUNGAN OPERASIONAL: Mengambil data Penyaluran Reguler yang siap dijalankan tim operasional
        $regulerPrograms = \App\Models\PenyaluranReguler::whereIn('status', ['disetujui', 'dicairkan', 'dilaporkan'])
                                      ->orderBy('tanggal_pelaksanaan', 'asc')
                                      ->get();

        return view('operational.schedule', compact(
            'donasiPrograms', 
            'podcastPrograms', 
            'cinemaPrograms', 
            'regulerPrograms', // Data terkirim ke view
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

    /**
     * 8. Menyediakan data JSON Jadwal Kegiatan untuk Kalender Dashboard (SINKRON DENGAN MODEL PODCAST)
     */
    public function getCalendarEvents()
    {
        $events = [];

        // 1. Ambil data dari Program Infak Ekstra (Hanya Donasi Umum) yang sudah ditentukan tanggal pelaksanaannya
        $ekstraPrograms = ExtraProgram::where('category', 'Donasi Umum')
                                      ->whereNotNull('execution_date')
                                      ->get();
        foreach ($ekstraPrograms as $program) {
            // Tentukan warna latar belakang berdasarkan kategori program
            $color = '#0f172a'; // Default Slate 900 (Donasi Umum)
            if ($program->category === 'Cinema Edukasi') $color = '#3b82f6'; // Biru 500

            $events[] = [
                'title' => '[' . $program->category . '] ' . $program->name,
                'start' => $program->execution_date,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'tipe' => 'Ekstra',
                    'detail' => $program->description
                ]
            ];
        }

        // Ambil data dari model Podcast asli untuk dirender ke Kalender sistem
        $podcastPrograms = Podcast::whereNotNull('taping_date')->get();
        foreach ($podcastPrograms as $podcast) {
            $color = '#a855f7';

            $events[] = [
                'title' => '[Podcast] ' . $podcast->title,
                'start' => is_string($podcast->taping_date) ? $podcast->taping_date : $podcast->taping_date->format('Y-m-d H:i:s'),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'tipe' => 'Podcast',
                    'detail' => 'Topic: ' . $podcast->topic . ' | Host: ' . $podcast->host . ' | Guest: ' . $podcast->guest_star
                ]
            ];
        }

        // Ambil data dari model CinemaEdukasi untuk kalender
        $cinemaJadwal = CinemaEdukasi::whereNotNull('jadwal_kegiatan')->get();
        foreach ($cinemaJadwal as $cinema) {
            $events[] = [
                'title' => '[Cinema] ' . $cinema->nama_materi,
                'start' => is_string($cinema->jadwal_kegiatan) ? $cinema->jadwal_kegiatan : $cinema->jadwal_kegiatan->format('Y-m-d H:i:s'),
                'backgroundColor' => '#8b5cf6',
                'borderColor' => '#8b5cf6',
                'extendedProps' => [
                    'tipe' => 'Cinema Edukasi',
                    'detail' => 'Pengajar: ' . $cinema->pengajar . ' | Peserta: ' . $cinema->penerima_manfaat
                ]
            ];
        }

        // 2. Ambil data dari Program Infak Reguler yang siap jalan
        $regulerPrograms = \App\Models\PenyaluranReguler::whereIn('status', ['disetujui', 'dicairkan', 'dilaporkan'])
            ->whereNotNull('tanggal_pelaksanaan')
            ->get();

        foreach ($regulerPrograms as $reguler) {
            $events[] = [
                'title' => '[Reguler] ' . $reguler->nama_program,
                'start' => $reguler->tanggal_pelaksanaan,
                'backgroundColor' => '#10b981', // Emerald 500 (Hijau)
                'borderColor' => '#10b981',
                'extendedProps' => [
                    'tipe' => 'Reguler',
                    'detail' => 'Target Penerima: ' . $reguler->penerima_manfaat
                ]
            ];
        }

        return response()->json($events);
    }
}