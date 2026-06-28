<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPenggunaanDetail extends Model
{
    use HasFactory;

    protected $table = 'laporan_penggunaan_detail';

    protected $fillable = [
        'laporan_penggunaan_id',
        'tanggal',
        'uraian',
        'nominal',
        'bukti_nota',
    ];

    // Relasi balik ke tabel induk laporan_penggunaan
    public function laporanPenggunaan()
    {
        return $this->belongsTo(LaporanPenggunaan::class, 'laporan_penggunaan_id');
    }
}