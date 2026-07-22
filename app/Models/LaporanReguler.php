<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanReguler extends Model
{
    use HasFactory;

    // Definisikan nama tabel secara eksplisit
    protected $table = 'laporan_reguler';

    // Daftarkan kolom yang diizinkan untuk mengisi data (Mass Assignment)
    protected $fillable = [
        'penyaluran_reguler_id',
        'items_nota',
        'total_pengeluaran',
        'selisih_dana',
        'bukti_pengembalian_sisa',
        'bukti_reimburse',
        'keterangan'
    ];

    /**
     * Casting tipe data kolom tertentu.
     * `items_nota` otomatis dikonversi dari JSON di DB menjadi Array di Laravel secara instan.
     */
    protected $casts = [
        'items_nota' => 'array',
    ];

    /**
     * Relasi Balik (Belongs To) ke tabel utama Penyaluran Reguler
     */
    public function penyaluranReguler()
    {
        // Pastikan nama model target (PenyaluranReguler) sudah sesuai dengan nama model utama kamu
        return $this->belongsTo(PenyaluranReguler::class, 'penyaluran_reguler_id');
    }
}