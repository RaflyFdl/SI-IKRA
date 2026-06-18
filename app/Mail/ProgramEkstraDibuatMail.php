<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ExtraProgram;

class ProgramEkstraDibuatMail extends Mailable
{
    use Queueable, SerializesModels;

    // Properti public agar otomatis terbaca di file Blade email
    public $program;

    public function __construct(ExtraProgram $program)
    {
        // Menangkap data program yang baru saja dibuat/dipublikasikan admin
        $this->program = $program;
    }

    public function build()
    {
        return $this->subject("Program Infak Ekstra Baru Dibuka: {$this->program->name}")
                    ->view('emails.program_ekstra_baru');
    }
}