<?php

namespace App\Http\Controllers;

use App\Models\OperationalRequest;
use App\Models\OperationalRequestItem;
use Illuminate\Http\Request;

class PenyaluranOperasionalController extends Controller
{
    // 1. Menampilkan daftar riwayat pengajuan operasional
    public function index()
    {
        $requests = OperationalRequest::with('items')->latest()->get();
        return view('operational.operasional_internal.index', compact('requests'));
    }

    // 2. Menampilkan form pengajuan baru
    public function create()
    {
        return view('operational.operasional_internal.create');
    }

    // 3. Menyimpan pengajuan multi-item
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'descriptions' => 'required|array|min:1',
            'descriptions.*' => 'required|string|max:255',
            'amounts' => 'required|array|min:1',
            'amounts.*' => 'required|numeric|min:0',
        ]);

        $totalAmount = array_sum($request->amounts);

        $operationalRequest = OperationalRequest::create([
            'title' => $request->title,
            'total_amount' => $totalAmount,
            'status_pembina' => 'pending',
            'status_keuangan' => 'pending',
        ]);

        foreach ($request->descriptions as $index => $description) {
            OperationalRequestItem::create([
                'operational_request_id' => $operationalRequest->id,
                'description' => $description,
                'amount' => $request->amounts[$index],
            ]);
        }

        return redirect()->route('operational.operasional.index')->with('success', 'Pengajuan operasional berhasil dikirim ke Pembina!');
    }

    // 4. Menyimpan rincian nota belanja dari lapangan (Tanpa Kuantitas)
    public function laporkan(Request $request, $id)
    {
        $request->validate([
            'realization_report' => 'nullable|string',
            'nota_date'          => 'required|array',
            'nota_date.*'        => 'required|date',
            'nota_description'   => 'required|array',
            'nota_description.*' => 'required|string|max:255',
            'nota_amount'        => 'required|array',
            'nota_amount.*'      => 'required|numeric|min:0',
            'refund_proof'       => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        ]);

        $req = OperationalRequest::findOrFail($id);

        $totalPengeluaranNota = array_sum($request->nota_amount);
        $modalAwal = $req->total_amount;
        $selisih = $modalAwal - $totalPengeluaranNota;

        \DB::beginTransaction();
        try {
            $pathRefund = null;
            if ($selisih > 0) {
                if (!$request->hasFile('refund_proof')) {
                    return redirect()->back()->with('error', 'Wajib melampirkan bukti transfer pengembalian sisa dana (Refund).');
                }
                $pathRefund = $request->file('refund_proof')->store('nota_refund', 'public');
                $req->realization_proof_path = $pathRefund; 
            }

            // 1. BUAT DATA INDUK LAPORAN
            $reportId = \DB::table('operational_reports')->insertGetId([
                'operational_request_id' => $req->id,
                'total_realization'      => $totalPengeluaranNota,
                'selisih'                => $selisih,
                'status_keuangan'        => 'dilaporkan',
                'nota_global'            => $request->realization_report,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);

            // 2. MASUKKAN RINCIAN NOTA BELANJA
            foreach ($request->nota_description as $index => $desc) {
                \DB::table('operational_report_details')->insert([
                    'operational_report_id' => $reportId,
                    'description'           => $desc,
                    'amount_realization'    => $request->nota_amount[$index],
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);
            }

            // 3. UPDATE STATUS PADA TABEL REQUEST UTAMA
            $req->realization_report = $request->realization_report;
            $req->status_keuangan = 'dilaporkan';
            $req->reported_at = now();
            $req->save();

            \DB::commit();
            return redirect()->back()->with('success', 'Laporan rincian nota belanja berhasil dikirim!');

        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan laporan: ' . $e->getMessage());
        }
    }
}