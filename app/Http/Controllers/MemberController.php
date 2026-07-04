<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'nama' => 'required|string|max:255',
            'angkatan' => 'required|numeric',
            'no_wa' => 'required|string',
            'email' => 'required|email',
        ]);

        // Opsional: Logika generate otomatis nomor VA Muamalat simulasi sederhana
        $va_otomatis = '7072' . rand(10000000, 99999999);

        DB::table('members')->insert([
            'nama' => $request->nama,
            'angkatan' => $request->angkatan,
            'no_wa' => $request->no_wa,
            'email' => $request->email,
            'va_muamalat' => $va_otomatis,
            'status' => 'active', // Otomatis aktif saat diinput manual oleh admin
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Anggota baru berhasil ditambahkan secara manual.');
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