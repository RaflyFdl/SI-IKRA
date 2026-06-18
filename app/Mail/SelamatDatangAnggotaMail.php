<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SelamatDatangAnggotaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;

    // Menangkap nama anggota dari Controller
    public function __construct($nama)
    {
        $this->nama = $nama;
    }

    public function build()
    {
        return $this->subject('Selamat Bergabung di Yayasan IKRA Padjadjaran!')
                    ->view('emails.selamat_datang');
    }
}