<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentSimulationController extends Controller
{
    public function simulateRegularPayment($memberId)
    {
        $member = DB::table('members')->where('id', $memberId)->first();

        if (!$member) {
            return back()->with('error', 'Member tidak ditemukan.');
        }

        if (empty($member->va_muamalat)) {
            return back()->with('error', 'VA belum tersedia.');
        }

        $xenditVaId = $member->verification_token;

        if (empty($xenditVaId)) {
            return back()->with('error', 'ID Virtual Account belum tersinkronisasi.');
        }

        $secretKey = env('XENDIT_SECRET_KEY');

        // ✅ PERBAIKAN: Menggunakan endpoint simulasi khusus FVA UUID terbaru
        $response = Http::withoutVerifying()
            ->withBasicAuth($secretKey, '')
            ->post("https://api.xendit.co/callback_virtual_accounts/external_id=va_member_{$memberId}/simulate_payment", [
                'amount' => 100000 // Berupa integer untuk endpoint ini
            ]);

        // Jika endpoint di atas masih strict, kita punya opsi cadangan universal dari Xendit:
        if (!$response->successful()) {
            $response = Http::withoutVerifying()
                ->withBasicAuth($secretKey, '')
                ->post("https://api.xendit.co/virtual_accounts/{$xenditVaId}/payments", [
                    'amount' => 100000
                ]);
        }

        // Jika kedua skenario simulasi di atas gagal, muntahkan error-nya agar tidak freeze lagi
        if (!$response->successful()) {
            $errorResponse = $response->json();
            return back()->with('error', 'Gagal simulasi ke Xendit: ' . ($errorResponse['message'] ?? $response->body()));
        }

        return redirect()->route('member.dashboard')->with('success', 'Sinyal simulasi pembayaran Rp 100.000 berhasil diterima Xendit Sandbox! Menunggu Webhook mengubah status...');
    }
}