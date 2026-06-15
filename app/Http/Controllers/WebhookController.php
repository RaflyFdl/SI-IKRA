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
                    
                    // 1. Simpan data transaksi menggunakan Query Builder (Menerobos isu kolom tak tersedia)
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
            // Mengambil angka ID member dari external_id reguler (Contoh dari: "va_member_1" diambil angka 1)
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
     * 2. Menampilkan Halaman Dashboard Utama Bagian Keuangan
     */
    public function keuanganDashboard()
    {
        // 1. Hitung total uang yang masuk dari infak REGULER di tabel transactions
        $totalKeseluruhan = DB::table('transactions')
                            ->where('transaction_type', 'reguler')
                            ->sum('amount');

        // 2. Jaga-jaga jika dashboard keuanganmu juga butuh total infak EKSTRA
        $totalEkstra = DB::table('transactions')
                            ->where('transaction_type', 'ekstra')
                            ->sum('amount');

        // 3. Ambil data 10 riwayat transaksi terakhir untuk dipajang di tabel dashboard keuangan
        $riwayatTransaksi = DB::table('transactions')
                            ->orderBy('created_at', 'desc')
                            ->limit(10)
                            ->get();

        // Kirim semua variabel ke file blade keuangan_dashboard
        return view('keuangan_dashboard', compact('totalKeseluruhan', 'totalEkstra', 'riwayatTransaksi'));
    }

    /**
     * 3. Menampilkan Laporan Penerimaan Kas Infak Ekstra
     */
    public function infakEkstraDashboard()
    {
        // Mengambil data transaksi tipe 'ekstra' untuk dilaporkan ke bendahara
        $payments = Transaction::where('transaction_type', 'ekstra')->latest()->get();
        return view('keuangan.infak-ekstra', compact('payments'));
    }
}