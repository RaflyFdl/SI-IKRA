<?php

namespace App\Http\Controllers;

use App\Models\Podcast; // Pastikan nama model Anda sesuai, misal: Podcast atau ProgramPodcast
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
     * Menyimpan data jadwal & anggaran podcast ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Form
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'topic'            => 'required|string',
            'host'             => 'required|string|max:255',
            'guest_star'       => 'required|string|max:255',
            'amount_requested' => 'required|numeric|min:0',
            'taping_date'      => 'required|date',
            'airing_date'      => 'nullable|date|after_or_equal:taping_date',
        ]);

        // 2. Simpan ke Database
        // Catatan: sesuaikan nama model & kolom database Anda jika berbeda
        Podcast::create([
            'title'            => $validated['title'],
            'topic'            => $validated['topic'],
            'host'             => $validated['host'],
            'guest_star'       => $validated['guest_star'],
            'amount_requested' => $validated['amount_requested'],
            'taping_date'      => $validated['taping_date'],
            'airing_date'      => $validated['airing_date'],
            'status'           => 'requested', // Status awal saat baru dibuat
        ]);

        // 3. Redirect kembali ke halaman jadwal dengan pesan sukses
        return redirect()->route('operational.schedule', ['tab' => 'podcast'])
                         ->with('success', 'Jadwal dan anggaran podcast berhasil diajukan!');
    }
}