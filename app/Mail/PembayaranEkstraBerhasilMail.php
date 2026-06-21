<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PembayaranEkstraBerhasilMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;
    public $namaProgram;

    public function __construct($transaction, $namaProgram)
    {
        $this->transaction = $transaction;
        $this->namaProgram = $namaProgram;
    }

    public function build()
    {
        return $this->subject('Alhamdulillah! Pembayaran Infak Ekstra Anda Berhasil Diterima')
                    ->view('emails.pembayaran_ekstra_berhasil');
    }
}