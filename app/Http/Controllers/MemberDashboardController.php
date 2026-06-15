<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;

class MemberDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard khusus portal anggota (Member) - Tab 1
     */
    public function index()
    {
        // Mencari data user login (menggunakan fallback Auth jika sewaktu-waktu session disinkronkan)
        $user = Auth::user(); 

        if ($user) {
            $member = Member::where('email', $user->email)->first();
        } else {
            // Jika Auth::user() null karena bypass demo, ambil data member pertama yang tersedia di database
            $member = Member::first();
        }

        // Antisipasi jika data member benar-benar kosong di database agar tidak error 500
        if (!$member) {
            return "Data profil anggota Anda belum terdaftar di database. Silakan hubungi admin.";
        }

        return view('member.dashboard', compact('member'));
    }

    /**
     * FUNGSI BARU: Menampilkan halaman pengaturan profil anggota - Tab 3
     * Disesuaikan agar aman dari error "Attempt to read property email on null"
     */
    public function profil()
    {
        // 1. Cek apakah ada session user dari Auth Laravel
        $user = Auth::user(); 

        if ($user) {
            // 2. Jika ada, cari berdasarkan email user tersebut
            $member = Member::where('email', $user->email)->first();
        } else {
            // 3. Jika null (karena sistem bypass login demo), langsung ambil data member aktif pertama di database
            $member = Member::first();
        }

        // Antisipasi jika data profil tidak ditemukan sama sekali di database
        if (!$member) {
            return "Data profil anggota Anda belum terdaftar di database. Silakan hubungi admin.";
        }

        // 4. Lempar data member yang valid ke halaman profil.blade.php
        return view('member.profil', compact('member'));
    }
}