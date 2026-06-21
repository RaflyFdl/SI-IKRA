<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ExtraProgramController extends Controller
{
    // 1. Fungsi menampilkan halaman detail informasi program
    public function show($id)
    {
        $program = DB::table('extra_programs')->where('id', $id)->first();

        if (!$program) {
            abort(404);
        }

        return view('member.extra.show', compact('program'));
    }

    // 2. Fungsi memproses pembuatan Closed VA Xendit (Valid 2 Jam)
    public function checkout(Request $request, $id)
    {
        // Validasi minimal input nominal
        $request->validate([
            'nominal' => 'required|numeric|min:10000',
        ], [
            'nominal.min' => 'Minimal infak adalah Rp 10.000'
        ]);

        // Mengambil data email dari session login member aplikasi Anda
        $sessionEmail = session('logged_in_email');
        $member = DB::table('members')->where('email', $sessionEmail)->first();
        $program = DB::table('extra_programs')->where('id', $id)->first();

        if (!$member || !$program) {
            return back()->with('error', 'Data Anggota atau Program tidak ditemukan.');
        }

        $apiKey = env('XENDIT_SECRET_KEY');
        $externalId = 'ext_extra_' . time() . '_' . $member->id . '_' . $id;

        // Hit API Xendit untuk membuat CLOSED VIRTUAL ACCOUNT Bank Muamalat
        $response = Http::withBasicAuth($apiKey, '')
            ->withoutVerifying()
            ->post('https://api.xendit.co/callback_virtual_accounts', [
                'external_id' => $externalId,
                'bank_code' => 'MUAMALAT',
                'name' => 'IKRA - ' . Str::limit($member->nama, 10),
                'is_closed' => true, // Mengunci nominal pembayaran agar pas otomatis
                'expected_amount' => (int) $request->nominal,
                'expiration_date' => now()->addHours(2)->toIso8601String() // Kedaluwarsa dalam 2 Jam
            ]);

        if ($response->successful()) {
            $resData = $response->json();

            // Menyimpan riwayat transaksi ke tabel lokal dengan tipe 'ekstra'
            $transactionId = DB::table('transactions')->insertGetId([
                'transaction_type' => 'ekstra', // Menandakan infak ekstra
                'member_id' => $member->id,
                'extra_program_id' => $program->id,
                'external_id' => $externalId,
                'amount' => $request->nominal,
                'bank_code' => 'MUAMALAT',
                'account_number' => $resData['account_number'],
                'payment_id' => null, // Diisi nanti setelah sukses lewat Webhook
                'periode' => now()->format('Y-m'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Diarahkan ke halaman instruksi invoice pembayaran
            return redirect()->route('member.extra.invoice', $transactionId);
        }

        return back()->with('error', 'Gagal terhubung dengan server Xendit. Silakan coba lagi.');
    }

    // 3. Fungsi menampilkan halaman Invoice / Instruksi Pembayaran
    public function invoice($transactionId)
    {
        $transaction = DB::table('transactions')
            ->join('extra_programs', 'transactions.extra_program_id', '=', 'extra_programs.id')
            ->select('transactions.*', 'extra_programs.name as program_name')
            ->where('transactions.id', $transactionId)
            ->first();

        if (!$transaction) {
            abort(404);
        }

        return view('member.extra.invoice', compact('transaction'));
    }

    // 4. Fungsi Demo Pembayaran Instan (Hit Simulator Xendit)
    public function simulatePayment($transactionId)
    {
        $trx = DB::table('transactions')->where('id', $transactionId)->first();
        
        if (!$trx) {
            return back()->with('error', 'Transaksi tidak ditemukan.');
        }

        $apiKey = env('XENDIT_SECRET_KEY');

        // Mengirim instruksi ke simulator Xendit seolah-olah user bayar dari ATM/M-Banking
        $response = Http::withBasicAuth($apiKey, '')
            ->withoutVerifying()
            ->post("https://api.xendit.co/callback_virtual_accounts/external_id={$trx->external_id}/simulate_payment", [
                'amount' => (int) $trx->amount
            ]);

        if ($response->successful()) {
            return redirect()->route('member.dashboard')
                ->with('success', 'Demo Pembayaran Sukses! Server Xendit akan segera mengirimkan data ke sistem.');
        }

        return back()->with('error', 'Gagal melakukan simulasi pembayaran: ' . $response->body());
    }
}