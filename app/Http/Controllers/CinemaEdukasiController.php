<?php

namespace App\Http\Controllers;

use App\Models\CinemaEdukasi;
use App\Models\PengajuanPencairanEkstra;
use App\Models\ExtraProgram;
use Illuminate\Http\Request;

class CinemaEdukasiController extends Controller
{
    /**
     * Menampilkan halaman form input jadwal & dana Cinema Edukasi.
     */
    public function create()
    {
        $programCinema = ExtraProgram::where('category', 'Cinema Edukasi')
                                     ->where('status', 'active')
                                     ->first();
        $danaTersedia = $programCinema ? $programCinema->dana_bersih_ekstra : 0;
        $busyDates = ExtraProgram::getBusyDates();
        return view('operational.cinema.create', compact('danaTersedia', 'busyDates'));
    }

    /**
     * Menyimpan data jadwal Cinema Edukasi & otomatis mengirim pengajuan ke Keuangan.
     * Alur identik dengan PodcastController::store() — 1 jadwal = 1 pengajuan pencairan.
     */
    public function store(Request $request)
    {
        // 1. Validasi input form
        $validated = $request->validate([
            'nama_materi'       => 'required|string|max:255',
            'pengajar'          => 'required|string|max:255',
            'penerima_manfaat'  => 'required|string|max:255',
            'jadwal_kegiatan'   => 'required|date',
            'amount_requested'  => 'required|numeric|min:0',
            'description'       => 'required|string',
        ]);

        // 2. Cari ID program penggalangan dana Cinema Edukasi di tabel extra_programs
        $programCinema = ExtraProgram::where('category', 'Cinema Edukasi')
                                     ->where('status', 'active')
                                     ->first();

        if (!$programCinema) {
            return redirect()->back()->withInput()->with('error', 'Program penggalangan dana Cinema Edukasi tidak ditemukan atau belum aktif di database.');
        }

        // Validasi Saldo: Bandingkan nominal pengajuan dengan dana bersih terkumpul (65%)
        if ($validated['amount_requested'] > $programCinema->dana_bersih_ekstra) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengajukan! Permintaan anggaran (Rp ' . number_format($validated['amount_requested'], 0, ',', '.') . ') melebihi ketersediaan dana bersih terkumpul program (Rp ' . number_format($programCinema->dana_bersih_ekstra, 0, ',', '.') . ').');
        }

        $programId = $programCinema->id;

        // 3. Ambil ID staff operasional yang sedang login
        $staffId = auth()->id() ?? (auth()->user()?->id ?? 1);

        // 4. Simpan jadwal Cinema Edukasi ke tabel cinema_edukasi
        $cinema = CinemaEdukasi::create([
            'nama_materi'      => $validated['nama_materi'],
            'pengajar'         => $validated['pengajar'],
            'penerima_manfaat' => $validated['penerima_manfaat'],
            'jadwal_kegiatan'  => $validated['jadwal_kegiatan'],
            'amount_requested' => $validated['amount_requested'],
            'description'      => $validated['description'],
            'status'           => 'requested',
            'extra_program_id' => $programId,
        ]);

        // 5. Otomatis buat antrean pengajuan ke keuangan (1 jadwal = 1 pengajuan)
        PengajuanPencairanEkstra::create([
            'extra_program_id'  => $programId ?? 1,
            'cinema_edukasi_id' => $cinema->id, // Referensi ke jadwal spesifik
            'staff_id'          => $staffId,
            'nominal_diminta'   => $validated['amount_requested'],
            'nama_bank'         => 'Bank Operasional (Cinema Edukasi)',
            'nomor_rekening'    => 'Internal Sistem',
            'status'            => 'PENDING',
        ]);

        // 6. Redirect ke jadwal dengan tab cinema aktif
        return redirect()->route('operational.schedule', ['tab' => 'cinema'])
                         ->with('success', 'Jadwal Cinema Edukasi berhasil disimpan & pengajuan dana telah dikirim ke bagian keuangan!');
    }
}
