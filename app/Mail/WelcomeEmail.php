<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    // Kita siapkan variabel $user agar data pendaftar bisa dibaca di dalam email
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope (Mengatur Judul Email).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat Datang di Portal Anggota IKRA!',
        );
    }

    /**
     * Get the message content definition (Menghubungkan ke Template HTML kita).
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.user_welcome', // Mengarah ke folder emails file user_welcome.blade.php
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}