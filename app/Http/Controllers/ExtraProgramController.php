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
        // Validasi input form
        $validator = \Validator::make($request->all(), [
            'nominal' => 'required|numeric|min:10000',
            'bank_code' => 'required|string|in:BCA,MANDIRI,BRI,BNI,PERMATA,MUAMALAT',
        ]);

        if ($validator->fails()) {
            dd([
                'PESAN_ERROR' => 'Validasi Form Gagal! Ada input yang tidak sesuai aturan.',
                'input_dari_form' => $request->all(),
                'list_kesalahan' => $validator->errors()->all()
            ]);
        }

        // Ambil session email yang disesuaikan (Opsi session: 'email')
        $sessionEmail = session('email') ?? session('logged_in_email');
        $member = DB::table('members')->where('email', $sessionEmail)->first();
        $program = DB::table('extra_programs')->where('id', $id)->first();

        // Cek data member dan program di database
        if (!$member || !$program) {
            dd([
                'PESAN_ERROR' => 'Gagal lolos pengecekan Member atau Program di database!',
                'email_di_session' => $sessionEmail,
                'data_member_ditemukan' => $member,
                'data_program_ditemukan' => $program
            ]);
        }

        $apiKey = env('XENDIT_SECRET_KEY');
        $externalId = 'ext_extra_' . time() . '_' . $member->id . '_' . $id;

        // 🎯 DISESUAIKAN: Menggunakan Endpoint Closed VA Universal agar Sinkron dengan is_closed
        $response = Http::withBasicAuth($apiKey, '')
            ->withoutVerifying()
            ->post('https://api.xendit.co/callback_virtual_accounts', [
                'external_id' => $externalId,
                'bank_code' => $request->bank_code,
                'name' => 'IKRA - ' . Str::limit($member->nama, 10),
                'is_closed' => true, 
                'expected_amount' => (int) $request->nominal,
                'expiration_date' => now()->addHours(2)->toIso8601String()
            ]);

        // Jika API Xendit menolak atau gagal merespon
        if (!$response->successful()) {
            dd([
                'PESAN_ERROR' => 'API Xendit menolak request pembuatan Virtual Account!',
                'status_code_xendit' => $response->status(),
                'detail_error_dari_xendit' => json_decode($response->body(), true) ?? $response->body(),
                'api_key_yang_dipakai' => substr($apiKey, 0, 15) . '...'
            ]);
        }

        $resData = $response->json();

        // Menyimpan riwayat transaksi ke tabel lokal jika sukses
        $transactionId = DB::table('transactions')->insertGetId([
            'transaction_type' => 'ekstra',
            'member_id' => $member->id,
            'extra_program_id' => $program->id,
            'external_id' => $externalId,
            'amount' => $request->nominal,
            'bank_code' => $request->bank_code,
            'account_number' => $resData['account_number'],
            'payment_id' => null,
            'periode' => now()->format('Y-m'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Diarahkan ke halaman instruksi invoice pembayaran
        return redirect()->route('member.extra.invoice', $transactionId);
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

    // 4. Jalur rahasia backdoor menggunakan NOMOR VA untuk simulasi langsung dari browser
    public function backdoorSimulate($vaNumber)
    {
        $trx = DB::table('transactions')->where('account_number', $vaNumber)->first();
        
        if (!$trx) {
            return "Waduh, nomor Virtual Account " . $vaNumber . " tidak ditemukan di database lokal.";
        }

        $apiKey = env('XENDIT_SECRET_KEY');

        $response = Http::withBasicAuth($apiKey, '')
            ->withoutVerifying()
            ->post("https://api.xendit.co/callback_virtual_accounts/external_id={$trx->external_id}/simulate_payment", [
                'amount' => (int) $trx->amount
            ]);

        if ($response->successful()) {
            return "
                <script>
                    alert('Simulasi pembayaran untuk VA " . $trx->account_number . " sebesar Rp " . number_format($trx->amount, 0, ',', '.') . " berhasil terkirim ke Xendit!');
                    window.close();
                </script>
                <p>Pembayaran sukses disimulasikan. Silakan tutup tab ini dan refresh halaman invoice Anda.</p>
            ";
        }

        return 'Gagal melakukan simulasi pembayaran ke Xendit: ' . $response->body();
    }
}