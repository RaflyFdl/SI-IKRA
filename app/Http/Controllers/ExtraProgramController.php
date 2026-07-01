<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ExtraProgramController extends Controller
{
    // 1. Menampilkan halaman detail informasi program
    public function show($id)
    {
        $program = DB::table('extra_programs')->where('id', $id)->first();

        if (!$program) {
            abort(404);
        }

        return view('member.extra.show', compact('program'));
    }

    // 2. Memproses pembuatan VA Polos yang Stabil
    public function checkout(Request $request, $id)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:10000',
            'bank_code' => 'required|string|in:BCA,MANDIRI,BRI,BNI,PERMATA,MUAMALAT',
        ]);

        $sessionEmail = session('email') ?? session('logged_in_email');
        $member = DB::table('members')->where('email', $sessionEmail)->first();
        $program = DB::table('extra_programs')->where('id', $id)->first();

        if (!$member || !$program) {
            return back()->with('error', 'Data Anggota atau Program tidak ditemukan.');
        }

        $apiKey = env('XENDIT_SECRET_KEY');
        $externalId = 'ext_extra_' . time() . '_' . $member->id . '_' . $id;

        // Menembak ke API Virtual Account Polos
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

        if ($response->successful()) {
            $resData = $response->json();

            $transactionId = DB::table('transactions')->insertGetId([
                'transaction_type' => 'ekstra',
                'member_id' => $member->id,
                'extra_program_id' => $program->id,
                'external_id' => $externalId,
                'amount' => $request->nominal,
                'bank_code' => $request->bank_code,
                'account_number' => $resData['account_number'], // Menyimpan nomor VA asli 16 digit
                'payment_id' => null,
                'periode' => now()->format('Y-m'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('member.extra.invoice', $transactionId);
        }

        return back()->with('error', 'Gagal membuat Virtual Account. Silakan coba lagi.');
    }

    // 3. Menampilkan halaman Invoice lokal (Hijau)
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

    // 4. 🚀 JALUR LINK SIMULATOR RAHASIA (Tanpa Tombol di Halaman Web)
    public function backdoorSimulate($vaNumber)
    {
        $trx = DB::table('transactions')->where('account_number', $vaNumber)->first();
        
        if (!$trx) {
            return "Nomor Virtual Account " . $vaNumber . " tidak ditemukan di database.";
        }

        $apiKey = env('XENDIT_SECRET_KEY');

        // Mengirim request ke simulator resmi Xendit
        $response = Http::withBasicAuth($apiKey, '')
            ->withoutVerifying()
            ->post("https://api.xendit.co/callback_virtual_accounts/external_id={$trx->external_id}/simulate_payment", [
                'amount' => (int) $trx->amount
            ]);

        if ($response->successful()) {
            return "
                <script>
                    alert('Simulasi SUKSES! VA " . $trx->account_number . " telah dilunasi.');
                    window.location.href = '" . route('member.extra.invoice', $trx->id) . "';
                </script>
            ";
        }

        return 'Gagal melakukan simulasi: ' . $response->body();
    }
}