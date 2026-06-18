<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PengingatInfakRegulerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $namaAnggota;
    public $bulanTahun;

    public function __construct($namaAnggota)
    {
        $this->namaAnggota = $namaAnggota;
        // Menghasilkan nama bulan dan tahun saat ini (Contoh: "Juni 2026")
        $this->bulanTahun = now()->translatedFormat('F Y');
    }

    public function build()
    {
        return $this->subject("Tagihan Infak Reguler Periode Baru - {$this->bulanTahun}")
                    ->view('emails.pengingat_infak');
    }
}