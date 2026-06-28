<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPenggunaan extends Model
{
    use HasFactory;

    protected $table = 'laporan_penggunaan';

    protected $fillable = [
        'sumber_dana',
        'pengajuan_id',
        'total_terpakai',
        'selisih',
        'bukti_pengembalian',
    ];

    // Relasi ke tabel detail (Satu laporan memiliki banyak detail rincian nota)
    public function details()
    {
        return $this->hasMany(LaporanPenggunaanDetail::class, 'laporan_penggunaan_id');
    }

    // Relasi dinamis ke pengajuan terkait berdasarkan jenis sumber dananya
    public function pengajuanEkstra()
    {
        return $this->belongsTo(PengajuanPencairanEkstra::class, 'pengajuan_id');
    }
}