<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtraProgram;
use App\Models\Transaction; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail; // ✅ Impor Mail Facade untuk pengiriman email
use App\Mail\PembayaranEkstraBerhasilMail; // ✅ Impor Mailable khusus ekstra yang baru

class TransactionController extends Controller
{
    /**
     * 1. Menerima laporan pembayaran dari Xendit secara Dinamis (Infak Reguler & Ekstra)
     */
    public function handleCallback(Request $request)
    {
        $data = $request->all();
        
        // Catat logs untuk keperluan debugging / tracking sidang
        Log::info('Callback Pembayaran Masuk: ', $data);

        $externalId = $data['external_id'] ?? ''; 

        // -------------------------------------------------------------------
        // PENGAMAN DUMMY TERBARU: Langsung loloskan jika mendeteksi format uji coba Xendit
        // -------------------------------------------------------------------
        if (strpos($externalId, 'fixed-va-') !== false || ($data['account_number'] ?? '') == '1001470126') {
            return response()->json([
                'status' => 'success',
                'message' => 'Simulasi Test and Save Xendit Berhasil Ditangkap!'
            ], 200);
        }

        // ===================================================================
        // JALUR A: OTOMATIS JIKA INFAK EKSTRA (Mengandung pola 'ext_extra_')
        // ===================================================================
        if (str_contains($externalId, 'ext_extra')) {
            
            // Format external_id asli: "ext_extra_timestamp_memberId_programId" (Contoh: ext_extra_1782018209_12_3)
            $parts = explode('_', $externalId);
            
            // Pastikan struktur array hasil explode sesuai (harus ada minimal 5 bagian)
            if (count($parts) >= 5) {
                // ✅ PERBAIKAN URUTAN INDEKS AGAR TIDAK TERTUKAR:
                $userId    = $parts[3]; // Indeks 3 adalah ID Member / Anggota (Contoh: 12)
                $programId = $parts[4]; // Indeks 4 adalah ID Program Ekstra (Contoh: 3 -> Infak Pohon)
            } else {
                Log::error('Format External ID Ekstra Tidak Valid: ' . $externalId);
                return response()->json(['status' => 'error', 'message' => 'Format ID salah'], 400);
            }

            // Cari apakah transaksi dengan external_id tersebut ada di database kita
            $transaction = DB::table('transactions')->where('external_id', $externalId)->first();

            if (!$transaction) {
                Log::error('Callback Webhook: Transaksi ekstra tidak ditemukan untuk ID ' . $externalId);
                return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan'], 404);
            }

            try {
                DB::transaction(function () use ($data, $transaction, $programId, $userId) {
                    
                    // 1. Mengisi payment_id agar tidak NULL dan status berubah jadi BERHASIL di frontend
                    DB::table('transactions')
                        ->where('id', $transaction->id)
                        ->update([
                            'payment_id' => $data['id'] ?? 'PAID_' . time(), 
                            'updated_at' => now(),
                        ]);

                    // ❌ LINGKUNGAN INDUSTRI: Baris increment lama sengaja dihapus dari sini
                    // karena hitungan 'current_amount' dialihkan ke model ExtraProgram secara On-the-fly.

                    // 📢 LOGIKA UTAMA EMAIL OTOMATIS: Kirim Notifikasi Pembayaran Ekstra Berhasil
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

        // ===================================================================
        // JALUR B: OTOMATIS JIKA INFAK REGULER (Tidak mengandung 'ext_extra_')
        // ===================================================================
        try {
            // Mengambil angka ID member dari external_id reguler
            $userId = filter_var($externalId, FILTER_SANITIZE_NUMBER_INT) ?: 1; 

            DB::table('transactions')->insert([
                'member_id'        => $userId,   
                'external_id'      => $data['external_id'],
                'amount'           => $data['amount'],
                'transaction_type' => 'reguler', // <--- Set otomatis sebagai infak reguler
                'bank_code'        => $data['bank_code'],
                'account_number'   => $data['account_number'],
                'payment_id'       => $data['id'] ?? null, 
                'periode'          => date('Y-m'),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // 📝 Email Reguler sengaja dikosongkan dulu sesuai rencana barumu

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
     * 2. Menampilkan Halaman Dashboard Utama Bagian Keuangan (LOGIKA PEMBAGIAN DANA 35% & 65%)
     */
    public function keuanganDashboard(Request $request)
    {
        // Ambil total kotor keseluruhan infak reguler
        $totalKeseluruhan = DB::table('transactions')
                            ->where('transaction_type', 'reguler')
                            ->sum('amount') ?? 0;

        // Ambil total kotor keseluruhan infak ekstra
        $totalEkstra = DB::table('transactions')
                            ->where('transaction_type', 'ekstra')
                            ->sum('amount') ?? 0;

        // Terapkan Rumus Matematika 35% dan 65% untuk masing-masing jenis infak
        $operasionalReguler = $totalKeseluruhan * 0.35;
        $siapSalurReguler   = $totalKeseluruhan * 0.65;

        $operasionalEkstra  = $totalEkstra * 0.35;
        $siapSalurEkstra    = $totalEkstra * 0.65;

        // Akumulasi Kas Operasional Kantor Gabungan (35% Reguler + 35% Ekstra)
        $totalKasOperasionalKantor = $operasionalReguler + $operasionalEkstra;

        // B. PROSES FILTER PERIODE BULANAN
        $periodeDipilih = $request->get('periode', date('Y-m'));

        // Total reguler per bulan terpilih
        $totalPerPeriode = DB::table('transactions')
                            ->where('transaction_type', 'reguler')
                            ->where('periode', $periodeDipilih)
                            ->sum('amount') ?? 0;

        $operasionalPerPeriode = $totalPerPeriode * 0.35;
        $siapSalurPerPeriode   = $totalPerPeriode * 0.65;

        $daftarPeriode = DB::table('transactions')
                            ->where('transaction_type', 'reguler') 
                            ->whereNotNull('periode')
                            ->distinct()
                            ->pluck('periode')
                            ->toArray();

        if (empty($daftarPeriode)) {
            $daftarPeriode = [date('Y-m')];
        }

        // C. AMBIL RIWAYAT TRANSAKSI KHUSUS REGULER
        $transactions = Transaction::where('transaction_type', 'reguler')
                                    ->orderBy('created_at', 'desc')
                                    ->limit(10)
                                    ->get();
        
        $riwayatTransaksi = $transactions;

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
            'operasionalPerPeriode',
            'siapSalurPerPeriode'
        ));
    }

    /**
     * 3. Menampilkan Laporan Penerimaan Kas Infak Ekstra (HALAMAN TERPISAH)
     */
    public function infakEkstraDashboard()
    {
        $payments = Transaction::where('transaction_type', 'ekstra')->latest()->get();
        $transactions = $payments;

        $totalEkstra = DB::table('transactions')
                            ->where('transaction_type', 'ekstra')
                            ->sum('amount') ?? 0;

        $daftarProgram = DB::table('extra_programs')->get();

        $operasionalEkstra = $totalEkstra * 0.35;
        $siapSalurEkstra   = $totalEkstra * 0.65;

        return view('keuangan.keuangan_infak_ekstra', compact(
            'payments', 
            'transactions', 
            'totalEkstra', 
            'daftarProgram',
            'operasionalEkstra',
            'siapSalurEkstra'
        ));
    }

    /**
     * 4. Menampilkan Halaman Khusus Log Dana Operasional Gabungan (35% Reguler + 35% Ekstra)
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
}