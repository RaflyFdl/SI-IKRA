<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifikasiPendaftaranMail extends Mailable
{
    use Queueable, SerializesModels;

    public $linkVerifikasi;

    // Menangkap link verifikasi dari Controller
    public function __construct($linkVerifikasi)
    {
        $this->linkVerifikasi = $linkVerifikasi;
    }

    public function build()
    {
        return $this->subject('Verifikasi Email Pendaftaran Anggota IKRA')
                    ->view('emails.verifikasi_pendaftaran');
    }
}