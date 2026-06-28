<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanPencairanEkstra extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara manual karena nama tabel kita pakai snake_case jamak versi Indonesia
    protected $table = 'pengajuan_pencairan_ekstra';

    // Daftarkan kolom yang boleh diisi massal
    protected $fillable = [
        'extra_program_id',
        'staff_id',
        'nominal_diminta',
        'nama_bank',
        'nomor_rekening',
        'status',
        'bukti_transfer_pencairan',
    ];

    // Relasi balik ke Program Ekstra (Satu pengajuan memiliki satu program)
    public function extraProgram()
    {
        return $this->belongsTo(ExtraProgram::class, 'extra_program_id');
    }

    // Relasi balik ke Staff (Satu pengajuan diajukan oleh satu staf)
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}