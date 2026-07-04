<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtraProgram;
use App\Models\Transaction; 
use App\Models\DanaBackup; 
use App\Models\PengajuanPencairanEkstra;
use App\Models\PenyaluranReguler; // Tambahkan import Model PenyaluranReguler
use App\Models\OperationalRequest; // ✅ IMPORT MODEL OPERASIONAL BARU
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

        // 1. Total alokasi kotor sebelum pemotongan pengeluaran operasional
        $brankasAwalGabungan = $operasionalReguler + $operasionalEkstra;

        // 2. Hitung total dana operasional internal rill yang sudah dicairkan / dilaporkan
        $totalOperasionalTerpakai = OperationalRequest::whereIn('status_keuangan', ['dicairkan', 'dilaporkan'])
                                    ->sum('total_amount') ?? 0;

        // 🎯 LOGIKA DINAMIS REFUND & REIMBURSE OPERASIONAL:
        // Menghitung uang sisa yang kembali (+) dan uang tekor penambah pengeluaran (-) ke dalam perhitungan saldo
        $totalRefundMasuk = DB::table('operational_reports')->where('status_keuangan', 'selesai_refund')->sum('selisih') ?? 0;
        $totalReimburseKeluar = DB::table('operational_reports')->where('status_keuangan', 'selesai_reimburse')->sum('selisih') ?? 0;

        // 3. Saldo bersih otomatis berkurang & bertambah secara dinamis
        $totalOperasionalGabungan = $brankasAwalGabungan - $totalOperasionalTerpakai + $totalRefundMasuk - abs($totalReimburseKeluar);

        // 4. Ambil data antrean untuk ditampilkan di Halaman Keuangan
        $antreanPencairan = OperationalRequest::with('items')
                            ->where('status_pembina', 'approved_pembina')
                            ->where('status_keuangan', 'pending')
                            ->latest()
                            ->get();

        // 🎯 Ambil data antrean Verifikasi Bukti Nota Realisasi Belanja dari Tim Operasional Lapangan
        $antreanNotaRealisasi = DB::table('operational_reports')
                                ->join('operational_requests', 'operational_reports.operational_request_id', '=', 'operational_requests.id')
                                ->where('operational_reports.status_keuangan', 'pending')
                                ->select('operational_reports.*', 'operational_requests.title', 'operational_requests.total_amount as modal_awal')
                                ->get();

        $riwayatPencairan = OperationalRequest::with('items')
                            ->where('status_keuangan', '!=', 'pending')
                            ->latest()
                            ->get();

        return view('keuangan.keuangan_operasional', compact(
            'operasionalReguler',
            'operasionalEkstra',
            'totalOperasionalGabungan',
            'antreanPencairan',
            'antreanNotaRealisasi', // Kirim data antrean nota ke view
            'riwayatPencairan'
        ));
    }

    /**
     * 👑 4.5 PROSES CAIRKAN DANA OPERASIONAL INTERNAL KANTOR
     */
    public function prosesCairkanOperasional($id)
    {
        $operationalRequest = OperationalRequest::findOrFail($id);

        // Ubah status kas menjadi dicairkan, otomatis memotong saldo di dashboard
        $operationalRequest->update([
            'status_keuangan' => 'dicairkan'
        ]);

        return redirect()->back()->with('success', 'Dana operasional "' . $operationalRequest->title . '" sebesar Rp ' . number_format($operationalRequest->total_amount, 0, ',', '.') . ' berhasil dicairkan!');
    }

    /**
     * 🎯 4.6 PROSES KONFIRMASI NOTA REALISASI (REFUND / REIMBURSE OPERASIONAL)
     */
    public function konfirmasiNotaOperasional($id)
    {
        $report = DB::table('operational_reports')->where('id', $id)->first();
        if (!$report) {
            return redirect()->back()->with('error', 'Laporan realisasi nota tidak ditemukan.');
        }

        if ($report->selisih >= 0) {
            // Kasus Kelebihan Uang: Sisa dana dikembalikan ke kas operasional (Refund)
            DB::table('operational_reports')->where('id', $id)->update(['status_keuangan' => 'selesai_refund']);
            $pesan = "Sisa dana pengembalian (refund) operasional berhasil diterima masuk kembali ke Brankas!";
        } else {
            // Kasus Kekurangan Uang: Sisa tekor dibayarkan oleh Bendahara (Reimburse)
            DB::table('operational_reports')->where('id', $id)->update(['status_keuangan' => 'selesai_reimburse']);
            $pesan = "Dana klaim tekor (reimburse) operasional berhasil dibayarkan kepada staf lapangan!";
        }

        // Kunci status master pengajuan menjadi dilaporkan penuh
        OperationalRequest::where('id', $report->operational_request_id)->update(['status_keuangan' => 'dilaporkan']);

        return redirect()->back()->with('success', $pesan);
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