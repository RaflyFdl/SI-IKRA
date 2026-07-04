<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{
    public function index()
    {
        $members = DB::table('members')
            ->select('id', 'nama', 'angkatan', 'no_wa', 'email', 'va_muamalat', 'status')
            ->orderBy('created_at', 'desc')
            ->get();

        // SEKARANG DIARAHKAN KE FOLDER ADMIN -> MEMBER -> INDEX
        return view('admin.member.index', compact('members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'             => 'required|string|max:255',
            'angkatan'         => 'required|numeric',
            'no_wa'            => 'required|string',
            'email'            => 'required|email|unique:members,email',
            'password'         => 'required|string|min:6',
            'bukti_pendukung'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        // Handle upload bukti pendukung jika ada file yang dikirim
        $namaBukti = '';
        if ($request->hasFile('bukti_pendukung')) {
            $file      = $request->file('bukti_pendukung');
            $namaBukti = time() . '_admin_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $namaBukti);
        }

        // 1. Simpan anggota terlebih dahulu untuk mendapatkan ID-nya
        $memberId = DB::table('members')->insertGetId([
            'nama'             => $request->nama,
            'angkatan'         => $request->angkatan,
            'no_wa'            => $request->no_wa,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'bukti_pendukung'  => $namaBukti, // File jika ada, string kosong jika tidak
            'status'           => 'active', // Langsung aktif saat ditambah manual oleh admin
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // 2. Generate VA Muamalat via Xendit API (sama persis dengan alur approveMember)
        $apiKey = env('XENDIT_SECRET_KEY');

        $response = Http::withoutVerifying()
            ->withBasicAuth($apiKey, '')
            ->post('https://api.xendit.co/callback_virtual_accounts', [
                'external_id' => 'va_member_' . $memberId,
                'bank_code'   => 'MUAMALAT',
                'name'        => $request->nama,
                'is_closed'   => false,
            ]);

        if ($response->successful()) {
            $vaResmi = $response->json()['account_number'];

            // 3. Update kolom va_muamalat dengan nomor VA resmi dari Xendit
            DB::table('members')->where('id', $memberId)->update([
                'va_muamalat' => $vaResmi,
                'updated_at'  => now(),
            ]);

            return redirect()->back()->with('success', 'Anggota baru berhasil ditambahkan & VA Muamalat berhasil digenerate.');
        }

        // Jika Xendit gagal, anggota tetap tersimpan namun VA belum ada
        Log::error('Xendit Error saat generate VA manual: ' . json_encode($response->json()));

        return redirect()->back()->with('warning', 'Anggota berhasil ditambahkan, namun VA Muamalat gagal digenerate. Silakan isi nomor VA secara manual melalui tombol Edit.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'angkatan' => 'required|numeric',
            'no_wa' => 'required|string',
            'email' => 'required|email',
            'va_muamalat' => 'nullable|string',
            'status' => 'required|string'
        ]);

        DB::table('members')->where('id', $id)->update([
            'nama' => $request->nama,
            'angkatan' => $request->angkatan,
            'no_wa' => $request->no_wa,
            'email' => $request->email,
            'va_muamalat' => $request->va_muamalat,
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Data jemaah/anggota berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::table('members')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Anggota berhasil dihapus dari master database.');
    }
}