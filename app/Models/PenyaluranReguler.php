<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenyaluranReguler extends Model
{
    use HasFactory;

    // Menghubungkan model ke nama tabel baru kita
    protected $table = 'penyaluran_reguler';

    protected $fillable = [
        'nama_program',
        'nominal_diajukan',
        'rincian_detail',
        'penerima_manfaat',
        'tanggal_pelaksanaan',
        'periode_bulan',
        'status',
        'catatan_pembina',
        'bukti_transfer',
        'bukti_transfer_path',
        'dicairkan_at',
        'disetujui_pembina_at',
        'rincian_realisasi',
        'file_nota',
        'laporan_diterima_keuangan_at'
    ];

    /**
     * Relasi One-to-One ke Laporan Realisasi Reguler
     */
    public function laporan()
    {
        return $this->hasOne(LaporanReguler::class, 'penyaluran_reguler_id');
    }
}