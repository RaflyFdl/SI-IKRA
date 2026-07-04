<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CinemaEdukasi extends Model
{
    use HasFactory;

    protected $table = 'cinema_edukasi';

    protected $fillable = [
        'nama_materi',
        'pengajar',
        'penerima_manfaat',
        'jadwal_kegiatan',
        'amount_requested',
        'description',
        'status',
        'extra_program_id',
    ];

    protected $casts = [
        'jadwal_kegiatan' => 'datetime',
    ];

    /**
     * Relasi ke program penggalangan dana Cinema di extra_programs
     */
    public function extraProgram()
    {
        return $this->belongsTo(ExtraProgram::class, 'extra_program_id');
    }

    /**
     * Relasi ke pengajuan pencairan keuangan (1 jadwal = 1 pengajuan pencairan)
     */
    public function pengajuanPencairan()
    {
        return $this->hasOne(PengajuanPencairanEkstra::class, 'cinema_edukasi_id');
    }
}
