<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    public function handleVirtualAccountPaid(Request $request)
    {
        // 1. Ambil data callback dari Xendit
        $callbackData = $request->all();

        // Ambil external_id dan status pembayaran dari json Xendit
        $externalId = $callbackData['external_id'] ?? null;
        $paymentId = $callbackData['payment_id'] ?? null;

        // Log data yang masuk untuk memastikan callback berhasil diterima (bisa dicek di storage/logs/laravel.log)
        Log::info('Xendit VA Callback Received:', $callbackData);

        if ($externalId) {
            // 2. Cari transaksi di database berdasarkan external_id
            $transaction = DB::table('transactions')->where('external_id', $externalId)->first();

            if ($transaction) {
                // 3. Update status transaksi menjadi lunas / sukses (sesuaikan dengan nama kolom status di tabel Anda)
                DB::table('transactions')
                    ->where('external_id', $externalId)
                    ->update([
                        'payment_id' => $paymentId,
                        'updated_at' => now()
                        // Jika Anda memiliki kolom 'status', tambahkan misalnya: 'status' => 'PAID' atau 'SUCCESS'
                    ]);

                return response()->json(['status' => 'success', 'message' => 'Transaction updated successfully'], 200);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
    }
}