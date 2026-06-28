<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanaBackup extends Model
{
    use HasFactory;

    // Menghubungkan ke tabel asli di database kamu
    protected $table = 'laporan_penggunaan';

    // Kolom-kolom asli di database kamu
    protected $fillable = [
        'sumber_dana',
        'pengajuan_id',
        'total_terpakai',
        'selisih',
        'bukti_pengembalian',
    ];

    // Relasi ke tabel pengajuan untuk mengambil nama program
    public function pengajuan()
    {
        return $this->belongsTo(PengajuanPencairanEkstra::class, 'pengajuan_id');
    }
}