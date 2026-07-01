<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Podcast extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi massal
    protected $fillable = [
        'title',
        'topic',
        'guest_star',
        'host',
        'taping_date',
        'airing_date',
        'amount_requested',
        'amount_approved',
        'disbursement_proof',
        'status',
    ];

    // Otomatis mengubah kolom string tanggal menjadi objek Carbon/Datetime di Laravel
    protected $casts = [
        'taping_date' => 'datetime',
        'airing_date' => 'datetime',
    ];

    /**
     * Relasi ke tabel nota belanja khusus podcast.
     * Satu podcast bisa memiliki banyak nota belanja (One-to-Many).
     */
    public function notas()
    {
        return $this->hasMany(PodcastNota::class, 'podcast_id');
    }
}