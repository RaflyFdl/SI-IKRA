<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtraProgram;
use App\Models\Transaction; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        // JALUR A: OTOMATIS JIKA INFAK EKSTRA (Mengandung pola '_user_')
        // ===================================================================
        if (strpos($externalId, '_user_') !== false) {
            
            // Contoh format: "program_2_user_1" dipecah menjadi ID Program & ID Member
            $cleanString = str_replace('program_', '', $externalId);
            $parts = explode('_user_', $cleanString);
            
            $programId = $parts[0]; // ID Program Ekstra
            $userId = $parts[1];    // ID Anggota / Member

            try {
                DB::transaction(function () use ($data, $programId, $userId) {
                    
                    // 1. Simpan data transaksi menggunakan Query Builder
                    DB::table('transactions')->insert([
                        'member_id'        => $userId,   
                        'external_id'      => $data['external_id'],
                        'amount'           => $data['amount'],
                        'transaction_type' => 'ekstra', // <--- Set otomatis sebagai infak ekstra
                        'bank_code'        => $data['bank_code'],
                        'account_number'   => $data['account_number'],
                        'payment_id'       => $data['id'] ?? null, 
                        'periode'          => date('Y-m'),
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);

                    // 2. Naikkan nominal uang yang terkumpul di tabel program utama (extra_programs)
                    DB::table('extra_programs')
                        ->where('id', $programId)
                        ->increment('current_amount', $data['amount']);
                });

                return response()->json([
                    'status' => 'success', 
                    'message' => 'Pembayaran Infak Ekstra Anggota ID ' . $userId . ' Sukses Dicatat ke Program ID ' . $programId
                ], 200);

            } catch (\Exception $e) {
                Log::error('Gagal Webhook Ekstra: ' . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
        }

        // ===================================================================
        // JALUR B: OTOMATIS JIKA INFAK REGULER (Tidak mengandung '_user_')
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
        // -------------------------------------------------------------------
        // [BARU] AMBIL DATA TOTAL AGREGAT UNTUK DIBAGI SESUAI ATURAN IKRA
        // -------------------------------------------------------------------
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

        // -------------------------------------------------------------------
        // B. PROSES FILTER PERIODE BULANAN (BAWAKAN ASLI KODEMU)
        // -------------------------------------------------------------------
        $periodeDipilih = $request->get('periode', date('Y-m'));

        // Total reguler per bulan terpilih
        $totalPerPeriode = DB::table('transactions')
                            ->where('transaction_type', 'reguler')
                            ->where('periode', $periodeDipilih)
                            ->sum('amount') ?? 0;

        // Rumus 35% & 65% khusus untuk data bulanan yang sedang difilter (opsional untuk visual blade nanti)
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

        // Kirim semua variabel lama + variabel pembagian dana baru ke view keuangan_dashboard
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

        // Total akumulasi dana ekstra
        $totalEkstra = DB::table('transactions')
                            ->where('transaction_type', 'ekstra')
                            ->sum('amount') ?? 0;

        // Mengambil data performa target program dari extra_programs
        $daftarProgram = DB::table('extra_programs')->get();

        // Tambahan pembagian di halaman ekstra agar sinkron jika sewaktu-waktu dipanggil di blade ekstra
        $operasionalEkstra = $totalEkstra * 0.35;
        $siapSalurEkstra   = $totalEkstra * 0.65;

        // FIX JALUR: Mengarah ke folder 'keuangan' dan file 'keuangan_infak_ekstra'
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
        // Ambil total kotor keseluruhan infak reguler dari database untuk dipotong 35%
        $totalReguler = DB::table('transactions')
                            ->where('transaction_type', 'reguler')
                            ->sum('amount') ?? 0;
        $operasionalReguler = $totalReguler * 0.35;

        // Ambil total kotor keseluruhan infak ekstra dari database untuk dipotong 35%
        $totalEkstra = DB::table('transactions')
                            ->where('transaction_type', 'ekstra')
                            ->sum('amount') ?? 0;
        $operasionalEkstra = $totalEkstra * 0.35;

        // Akumulasi Penggabungan Tunggal (Single Pool) Dana Operasional Yayasan
        $totalOperasionalGabungan = $operasionalReguler + $operasionalEkstra;

        // Mengarah ke file view keuangan_operasional di dalam folder keuangan
        return view('keuangan.keuangan_operasional', compact(
            'operasionalReguler',
            'operasionalEkstra',
            'totalOperasionalGabungan'
        ));
    }
}