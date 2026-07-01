<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtraProgram;
use App\Models\Transaction; 
use App\Models\DanaBackup; 
use App\Models\PengajuanPencairanEkstra;
use App\Models\PenyaluranReguler; // Tambahkan import Model PenyaluranReguler
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail; 
use App\Mail\PembayaranEkstraBerhasilMail; 

class TransactionController extends Controller
{
    /**
     * 1. Menerima laporan pembayaran dari Xendit secara Dinamis (Infak Reguler & Ekstra)
     */
    public function handleCallback(Request $request)
    {
        $data = $request->all();
        Log::info('Callback Pembayaran Masuk: ', $data);

        $externalId = $data['external_id'] ?? ''; 

        if (strpos($externalId, 'fixed-va-') !== false || ($data['account_number'] ?? '') == '1001470126') {
            return response()->json([
                'status' => 'success',
                'message' => 'Simulasi Test and Save Xendit Berhasil Ditangkap!'
            ], 200);
        }

        if (str_contains($externalId, 'ext_extra')) {
            $parts = explode('_', $externalId);
            if (count($parts) >= 5) {
                $userId    = $parts[3]; 
                $programId = $parts[4]; 
            } else {
                Log::error('Format External ID Ekstra Tidak Valid: ' . $externalId);
                return response()->json(['status' => 'error', 'message' => 'Format ID salah'], 400);
            }

            $transaction = DB::table('transactions')->where('external_id', $externalId)->first();

            if (!$transaction) {
                Log::error('Callback Webhook: Transaksi ekstra tidak ditemukan untuk ID ' . $externalId);
                return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan'], 404);
            }

            try {
                DB::transaction(function () use ($data, $transaction, $programId, $userId) {
                    DB::table('transactions')
                        ->where('id', $transaction->id)
                        ->update([
                            'payment_id' => $data['id'] ?? 'PAID_' . time(), 
                            'updated_at' => now(),
                        ]);

                    $programModel = ExtraProgram::find($programId);
                    if ($programModel) {
                        $nominalMasuk = $transaction->amount; 
                        $porsiBersih = $nominalMasuk * 0.65;      
                        $porsiOperasional = $nominalMasuk * 0.35; 
                        
                        $programModel->increment('current_amount', $nominalMasuk);
                        $programModel->increment('dana_bersih_ekstra', $porsiBersih);
                        $programModel->increment('dana_operasional_ekstra', $porsiOperasional);
                    }

                    $program = DB::table('extra_programs')->where('id', $programId)->first();
                    $member  = DB::table('members')->where('id', $userId)->first(); 

                    if ($member && !empty($member->email)) {
                        $namaProg = $program->name ?? 'Program Ekstra';
                        Mail::to($member->email)->send(new PembayaranEkstraBerhasilMail($transaction, $namaProg));
                    }
                });

                return response()->json([
                    'status' => 'success', 
                    'message' => 'Pembayaran Infak Ekstra Anggota ID ' . $userId . ' Berhasil Diperbarui & Email Terkirim!'
                ], 200);

            } catch (\Exception $e) {
                Log::error('Gagal Webhook Ekstra: ' . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
        }

        try {
            $userId = filter_var($externalId, FILTER_SANITIZE_NUMBER_INT) ?: 1; 

            DB::table('transactions')->insert([
                'member_id'        => $userId,   
                'external_id'      => $data['external_id'],
                'amount'           => $data['amount'],
                'transaction_type' => 'reguler', 
                'bank_code'        => $data['bank_code'],
                'account_number'   => $data['account_number'],
                'payment_id'       => $data['id'] ?? null, 
                'periode'          => date('Y-m'),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            return response()->json([
                'status' => 'success', 
                'message' => 'Pembayaran Infak Reguler Anggota ID ' . $userId . ' Sukses Dicatat'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gagal Webhook Reguler: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Reguler Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 2. Menampilkan Halaman Dashboard Utama Bagian Keuangan (FIXED: Nama Tabel Tanpa S)
     */
    public function keuanganDashboard(Request $request)
    {
        $totalKeseluruhan = DB::table('transactions')
                            ->where('transaction_type', 'reguler')
                            ->sum('amount') ?? 0;

        $totalEkstra = DB::table('transactions')
                            ->where('transaction_type', 'ekstra')
                            ->sum('amount') ?? 0;

        // 🎯 FIX: Mengubah 'penyaluran_regulers' menjadi 'penyaluran_reguler' sesuai database asli Anda
        $totalPenyaluranRegulerTerpakai = DB::table('penyaluran_reguler')
                            ->whereIn('status', ['dicairkan', 'dilaporkan'])
                            ->sum('nominal_diajukan') ?? 0;

        $operasionalReguler = $totalKeseluruhan * 0.35;
        
        // Potong porsi 65% dengan total pengeluaran rill yang sudah cair
        $siapSalurReguler   = ($totalKeseluruhan * 0.65) - $totalPenyaluranRegulerTerpakai;

        $operasionalEkstra  = $totalEkstra * 0.35;
        $siapSalurEkstra    = $totalEkstra * 0.65;

        $totalKasOperasionalKantor = $operasionalReguler + $operasionalEkstra;

        $periodeDipilih = $request->get('periode', date('Y-m'));

        $totalPerPeriode = DB::table('transactions')
                            ->where('transaction_type', 'reguler')
                            ->where('periode', $periodeDipilih)
                            ->sum('amount') ?? 0;

        // 🎯 FIX: Mengubah 'penyaluran_regulers' menjadi 'penyaluran_reguler' khusus periodik
        $penyaluranRegulerPeriodeTerpakai = DB::table('penyaluran_reguler')
                            ->where('periode_bulan', $periodeDipilih)
                            ->whereIn('status', ['dicairkan', 'dilaporkan'])
                            ->sum('nominal_diajukan') ?? 0;

        $operasionalPerPeriode = $totalPerPeriode * 0.35;
        
        // Potong porsi periodik dengan pengeluaran di periode bersangkutan
        $siapSalurPerPeriode   = ($totalPerPeriode * 0.65) - $penyaluranRegulerPeriodeTerpakai;

        $daftarPeriode = DB::table('transactions')
                            ->where('transaction_type', 'reguler') 
                            ->whereNotNull('periode')
                            ->distinct()
                            ->pluck('periode')
                            ->toArray();

        if (empty($daftarPeriode)) {
            $daftarPeriode = [date('Y-m')];
        }

        $transactions = Transaction::where('transaction_type', 'reguler')
                                    ->orderBy('created_at', 'desc')
                                    ->limit(10)
                                    ->get();
        
        $riwayatTransaksi = $transactions;
        $totalDanaBackup = DanaBackup::whereIn('sumber_dana', ['EKSTRA', 'ekstra'])->sum('selisih') ?? 0;

        $pengajuanReguler = PenyaluranReguler::whereIn('status', ['disetujui', 'dicairkan'])
                                            ->orderBy('updated_at', 'desc')
                                            ->get();

        return view('keuangan_dashboard', compact(
            'totalKeseluruhan', 
            'totalEkstra', 
            'riwayatTransaksi',
            'transactions',
            'periodeDipilih',
            'totalPerPeriode',
            'daftarPeriode',
            'operasionalReguler',
            'siapSalurReguler',
            'operasionalEkstra',
            'siapSalurEkstra',
            'totalKasOperasionalKantor',
            'totalDanaBackup',
            'operasionalPerPeriode',
            'siapSalurPerPeriode',
            'pengajuanReguler'
        ));
    }

    /**
     * 3. Menampilkan Laporan Penerimaan Kas Infak Ekstra (DENGAN RINCIAN DETAIL PENGGUNAAN)
     */
    public function infakEkstraDashboard()
    {
        $payments = Transaction::where('transaction_type', 'ekstra')->latest()->get();
        $transactions = $payments;

        $totalEkstra = DB::table('transactions')
                            ->where('transaction_type', 'ekstra')
                            ->sum('amount') ?? 0;

        $daftarProgram = ExtraProgram::get(); 

        $operasionalEkstra = $totalEkstra * 0.35;
        $siapSalurEkstra   = $totalEkstra * 0.65;

        $antreanPencairan = PengajuanPencairanEkstra::with('extraProgram')
                            ->where('status', 'PENDING')
                            ->get();

        $totalDanaBackup = DanaBackup::whereIn('sumber_dana', ['EKSTRA', 'ekstra'])->sum('selisih') ?? 0;

        $riwayatDanaBackup = DanaBackup::whereIn('sumber_dana', ['EKSTRA', 'ekstra'])
                                        ->with(['pengajuan.extraProgram']) 
                                        ->orderBy('created_at', 'desc')
                                        ->get();

        foreach ($riwayatDanaBackup as $backup) {
            if ($backup->pengajuan) {
                $laporan = DB::table('laporan_penggunaan')
                    ->where('pengajuan_id', $backup->pengajuan->id)
                    ->first();

                if ($laporan) {
                    $backup->pengajuan->items = DB::table('laporan_penggunaan_detail')
                        ->where('laporan_penggunaan_id', $laporan->id)
                        ->get();
                } else {
                    $backup->pengajuan->items = collect([]);
                }
            }
        }

        return view('keuangan.keuangan_infak_ekstra', compact(
            'payments', 
            'transactions', 
            'totalEkstra', 
            'daftarProgram',
            'operasionalEkstra',
            'siapSalurEkstra',
            'antreanPencairan',
            'totalDanaBackup',
            'riwayatDanaBackup'
        ));
    }

    /**
     * 4. Menampilkan Halaman Khusus Log Dana Operasional Gabungan
     */
    public function operasionalDashboard()
    {
        $totalReguler = DB::table('transactions')
                            ->where('transaction_type', 'reguler')
                            ->sum('amount') ?? 0;
        $operasionalReguler = $totalReguler * 0.35;

        $totalEkstra = DB::table('transactions')
                            ->where('transaction_type', 'ekstra')
                            ->sum('amount') ?? 0;
        $operasionalEkstra = $totalEkstra * 0.35;

        $totalOperasionalGabungan = $operasionalReguler + $operasionalEkstra;

        return view('keuangan.keuangan_operasional', compact(
            'operasionalReguler',
            'operasionalEkstra',
            'totalOperasionalGabungan'
        ));
    }

    /**
     * 👑 5. PROSES CAIRKAN DANA PROPOSAL REGULER
     */
    public function cairkanPenyaluranReguler(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        $pengajuan = PenyaluranReguler::findOrFail($id);

        if ($request->hasFile('bukti_transfer')) {
            $folderTujuan = public_path('uploads/bukti_transfer');
            if (!file_exists($folderTujuan)) {
                mkdir($folderTujuan, 0777, true);
            }

            $namaFile = time() . '_trf_' . $request->file('bukti_transfer')->getClientOriginalName();
            $request->file('bukti_transfer')->move($folderTujuan, $namaFile);

            $pengajuan->update([
                'status' => 'dicairkan',
                'bukti_transfer' => $namaFile
            ]);

            return redirect()->route('keuangan.dashboard')
                ->with('success', 'Dana Berhasil Dicairkan dan Bukti Transfer Berhasil Disimpan!');
        }

        return back()->with('error', 'Gagal memproses file bukti transfer.');
    }
}