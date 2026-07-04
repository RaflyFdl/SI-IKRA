<?php

namespace App\Http\Controllers;

use App\Models\OperationalRequest;
use Illuminate\Http\Request;

class PembinaOperasionalController extends Controller
{
    // 1. Menampilkan daftar antrean pengajuan yang butuh persetujuan
    public function index()
    {
        // Ambil pengajuan yang masih pending di meja Pembina
        $antrean = OperationalRequest::with('items')
                    ->where('status_pembina', 'pending')
                    ->latest()
                    ->get();

        // Ambil riwayat yang sudah pernah diputuskan (opsional untuk monitoring)
        $riwayat = OperationalRequest::with('items')
                    ->where('status_pembina', '!=', 'pending')
                    ->latest()
                    ->take(10)
                    ->get();

        return view('pembina.operasional_review.index', compact('antrean', 'riwayat'));
    }

    // 2. Proses Persetujuan
    public function approve($id)
    {
        $request = OperationalRequest::findOrFail($id);
        $request->update(['status_pembina' => 'approved_pembina']);

        return redirect()->back()->with('success', 'Pengajuan operasional telah disetujui dan diteruskan ke Keuangan.');
    }

    // 3. Proses Penolakan
    public function reject($id)
    {
        $request = OperationalRequest::findOrFail($id);
        $request->update(['status_pembina' => 'rejected_pembina']);

        return redirect()->back()->with('error', 'Pengajuan operasional telah ditolak.');
    }
}