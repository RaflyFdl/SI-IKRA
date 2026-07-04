<?php

namespace App\Http\Controllers;

use App\Models\Podcast; 
use App\Models\PengajuanPencairanEkstra; // 👈 PENTING: Hubungkan model pencairan keuangan
use App\Models\ExtraProgram; // 👈 PENTING: Untuk mencari ID program penggalangan dana podcast
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    /**
     * Menampilkan halaman form input jadwal & dana podcast.
     */
    public function create()
    {
        return view('operational.podcast.create'); 
    }

    /**
     * Menyimpan data jadwal & anggaran podcast ke database dan langsung mengirim ke Keuangan.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Form (Termasuk description/rincian biaya baru)
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'topic'            => 'required|string',
            'host'             => 'required|string|max:255',
            'guest_star'       => 'required|string|max:255',
            'amount_requested' => 'required|numeric|min:0',
            'description'      => 'required|string', 
            'taping_date'      => 'required|date',
            'airing_date'      => 'nullable|date|after_or_equal:taping_date',
        ]);

        // 2. Cari ID Program Penggalangan Dana Podcast di tabel extra_programs
        // Kita cari program yang namanya mengandung kata 'podcast' (insensitf huruf besar/kecil)
        $programPodcast = ExtraProgram::where('name', 'LIKE', '%podcast%')->first();

        // Antisipasi jika nama program podcast di database Anda berbeda, ambil ID pertama atau default 1
        $programId = $programPodcast ? $programPodcast->id : 1; 

        // 3. Ambil ID staff operasional yang sedang login (aman untuk testing)
        $staffId = auth()->id() ?? (auth()->user()?->id ?? 1);

        // 4. Simpan ke Database internal tabel podcasts
        $podcast = Podcast::create([
            'title'            => $validated['title'],
            'topic'            => $validated['topic'],
            'host'             => $validated['host'],
            'guest_star'       => $validated['guest_star'],
            'amount_requested' => $validated['amount_requested'],
            'description'      => $validated['description'], 
            'taping_date'      => $validated['taping_date'],
            'airing_date'      => $validated['airing_date'],
            'status'           => 'requested', 
        ]);

        // 5. 🔥 ALUR KEUANGAN SINKRON: Buat antrean otomatis ke bagian keuangan
        // Menyisipkan data pengajuan ke tabel pengajuan_pencairan_ekstra dengan status PENDING
        PengajuanPencairanEkstra::create([
            'extra_program_id' => $programId, // Terikat ke dana program penggalangan podcast
            'staff_id'         => $staffId,
            'nominal_diminta'  => $validated['amount_requested'], // Menggunakan nominal yang diisi di form podcast
            'nama_bank'        => 'Bank Operasional (Podcast)', // Penanda otomatis untuk keuangan
            'nomor_rekening'   => 'Internal Sistem',
            'status'           => 'PENDING', // Langsung masuk ke antrean dashboard keuangan
        ]);

        // 6. Redirect kembali ke halaman jadwal dengan pesan sukses
        return redirect()->route('operational.schedule', ['tab' => 'podcast'])
                         ->with('success', 'Jadwal podcast berhasil disimpan & rincian biaya berhasil diajukan ke bagian keuangan!');
    }
}