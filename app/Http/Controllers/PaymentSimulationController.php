<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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

    /**
     * Menampilkan Halaman Simulator Pembayaran Mock ATM / m-Banking
     */
    public function showSimulator()
    {
        return view('simulator');
    }

    /**
     * Mencari detail Virtual Account di database secara AJAX
     */
    public function searchVa(Request $request)
    {
        $vaNumber = trim($request->get('va_number'));

        if (empty($vaNumber)) {
            return response()->json(['exists' => false, 'message' => 'Nomor VA kosong']);
        }

        // 1. Cari di data member (Infak Reguler)
        $member = DB::table('members')
            ->where('va_muamalat', $vaNumber)
            ->first();

        if ($member) {
            return response()->json([
                'exists' => true,
                'type' => 'reguler',
                'name' => $member->nama,
                'bank' => 'MUAMALAT',
                'amount' => null, // Bebas diinput oleh pengguna
                'details' => 'Infak Reguler Anggota IKRA',
                'is_paid' => false // Reguler bisa berkali-kali bayar
            ]);
        }

        // 2. Cari di data transaksi (Infak Ekstra)
        $transaction = DB::table('transactions')
            ->where('account_number', $vaNumber)
            ->first();

        if ($transaction) {
            $memberEkstra = DB::table('members')->where('id', $transaction->member_id)->first();
            $program = DB::table('extra_programs')->where('id', $transaction->extra_program_id)->first();
            
            $namaMember = $memberEkstra ? $memberEkstra->nama : 'Jemaah';
            $namaProgram = $program ? $program->name : 'Program Ekstra';

            return response()->json([
                'exists' => true,
                'type' => 'ekstra',
                'name' => 'IKRA - ' . Str::limit($namaMember, 15),
                'bank' => $transaction->bank_code,
                'amount' => (int) $transaction->amount, // Diisi otomatis & dikunci
                'details' => $namaProgram,
                'is_paid' => !empty($transaction->payment_id)
            ]);
        }

        return response()->json(['exists' => false, 'message' => 'Nomor Virtual Account tidak terdaftar di database lokal.']);
    }

    /**
     * Memproses pembayaran simulasi dengan menembak API Sandbox Xendit
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'va_number' => 'required|string',
            'amount' => 'required|numeric|min:10000',
            'type' => 'required|string|in:reguler,ekstra'
        ]);

        $vaNumber = trim($request->va_number);
        $amount = (int) $request->amount;
        $type = $request->type;
        $secretKey = env('XENDIT_SECRET_KEY');

        if ($type === 'reguler') {
            $member = DB::table('members')->where('va_muamalat', $vaNumber)->first();
            if (!$member) {
                return response()->json(['status' => 'error', 'message' => 'Member dengan VA tersebut tidak ditemukan.'], 404);
            }

            $externalId = 'va_member_' . $member->id;

            // Kirim request simulate_payment ke Xendit
            $response = Http::withoutVerifying()
                ->withBasicAuth($secretKey, '')
                ->post("https://api.xendit.co/callback_virtual_accounts/external_id={$externalId}/simulate_payment", [
                    'amount' => $amount
                ]);

            // Skenario cadangan
            if (!$response->successful()) {
                $xenditVaId = $member->verification_token;
                if (!empty($xenditVaId)) {
                    $response = Http::withoutVerifying()
                        ->withBasicAuth($secretKey, '')
                        ->post("https://api.xendit.co/virtual_accounts/{$xenditVaId}/payments", [
                            'amount' => $amount
                        ]);
                }
            }

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Simulasi pembayaran Infak Reguler berhasil terkirim ke Xendit Sandbox! Tunggu beberapa detik, lalu refresh dashboard anggota untuk melihat pembaruan status.'
                ]);
            }

            $errorResponse = $response->json();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal simulasi ke Xendit: ' . ($errorResponse['message'] ?? $response->body())
            ], 500);

        } else {
            // Infak Ekstra
            $transaction = DB::table('transactions')->where('account_number', $vaNumber)->first();
            if (!$transaction) {
                return response()->json(['status' => 'error', 'message' => 'Transaksi ekstra dengan VA tersebut tidak ditemukan.'], 404);
            }

            if (!empty($transaction->payment_id)) {
                return response()->json(['status' => 'error', 'message' => 'Transaksi ini sudah lunas sebelumnya.'], 400);
            }

            // Kirim request simulate_payment ke Xendit
            $response = Http::withoutVerifying()
                ->withBasicAuth($secretKey, '')
                ->post("https://api.xendit.co/callback_virtual_accounts/external_id={$transaction->external_id}/simulate_payment", [
                    'amount' => $amount
                ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Simulasi pembayaran Infak Ekstra berhasil dikirim ke Xendit Sandbox! Halaman invoice akan ter-update otomatis menjadi lunas.'
                ]);
            }

            $errorResponse = $response->json();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal simulasi ke Xendit: ' . ($errorResponse['message'] ?? $response->body())
            ], 500);
        }
    }
}