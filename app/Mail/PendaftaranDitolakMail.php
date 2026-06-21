<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PendaftaranDitolakMail extends Mailable
{
    use Queueable, SerializesModels;

    public $namaPendaftar;
    public $alasan;

    public function __construct($namaPendaftar, $alasan)
    {
        $this->namaPendaftar = $namaPendaftar;
        $this->alasan = $alasan;
    }

    public function build()
    {
        return $this->subject("Pendaftaran Anggota IKRA Ditolak")
                    ->view('emails.pendaftaran_ditolak');
    }
}