<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use Illuminate\Support\Facades\Mail;
use App\Mail\PengingatInfakRegulerMail;
use Illuminate\Support\Facades\Log;

class SendMonthlyInfakReminder extends Command
{
    // Ini nama perintah yang nanti dipanggil oleh sistem cron job
    protected $signature = 'infak:send-reminder';

    // Deskripsi singkat kegunaan perintah
    protected $description = 'Mengirimkan email pengingat infak reguler bulanan secara otomatis kepada semua anggota aktif';

    public function handle()
    {
        // Ambil semua anggota yang statusnya sudah aktif
        $activeMembers = Member::where('status', 'active')->get();

        if ($activeMembers->isEmpty()) {
            $this->info('Tidak ada anggota aktif yang perlu dikirimi email.');
            return 0;
        }

        $this->info('Sedang mengirim email pengingat infak bulanan...');

        foreach ($activeMembers ?? [] as $member) {
            try {
                Mail::to($member->email)->send(new PengingatInfakRegulerMail($member->nama));
            } catch (\Exception $e) {
                Log::error("Gagal mengirim email pengingat bulanan ke {$member->email}: " . $e->getMessage());
            }
        }

        $this->info('Semua email pengingat awal bulan berhasil diproses!');
        return 0;
    }
}