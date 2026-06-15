<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMonthlyReminder extends Command
{
    /**
     * Nama perintah yang akan dipanggil di terminal
     */
    protected $signature = 'infak:reminder';

    /**
     * Deskripsi singkat kegunaan perintah (Bagus untuk dokumentasi skripsi)
     */
    protected $shadow = 'Mengirimkan email dan notifikasi WhatsApp reminder infak reguler Rp 100.000 ke setiap anggota aktif';

    public function handle()
    {
        $this->info('Memulai proses scanning anggota aktif...');

        // 1. Ambil semua anggota yang statusnya 'active'
        $members = Member::where('status', 'active')->get();
        $periodeBulan Ini = now()->format('F Y'); // Contoh: June 2026

        if ($members->isEmpty()) {
            $this->warn('Tidak ada anggota aktif yang ditemukan dalam sistem.');
            return Command::SUCCESS;
        }

        foreach ($members as $member) {
            // --- SIMULASI WHATSAPP GATEWAY (MOCKING LOG) ---
            $pesanWa = "Assalamu'alaikum Wr. Wb. Halo {$member->nama}, ini adalah pengingat amanah Infak Reguler Yayasan IKRA Padjadjaran untuk periode {$periodeBulanIni} sebesar Rp 100.000. Silakan salurkan kontribusi terbaik Anda melalui Virtual Account Bank Muamalat Anda: {$member->va_muamalat}. Terima kasih.";
            
            // Mencatat aktivitas kirim WA ke file storage/logs/laravel.log
            Log::info("WA GATEWAY SENT -> No: {$member->no_wa} | Isi Pesan: {$pesanWa}");


            // --- SIMULASI EMAIL BLAST VIA LARAVEL MAIL ---
            try {
                Mail::raw($pesanWa, function ($message) use ($member, $periodeBulanIni) {
                    $message->to($member->email)
                            ->subject("Pengingat Infak Reguler IKRA - Periode {$periodeBulanIni}");
                });
                Log::info("EMAIL NOTIFICATION SENT -> Ke: {$member->email}");
            } catch (\Exception $e) {
                Log::error("Gagal mengirim email ke {$member->email}: " . $e->getMessage());
            }

            $this->info("Reminder berhasil diproses untuk anggota: {$member->nama}");
        }

        $this->info('Seluruh reminder awal bulan telah sukses dikirimkan!');
        return Command::SUCCESS;
    }
}