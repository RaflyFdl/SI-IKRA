<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Daftarkan kolom-kolom yang diizinkan untuk diisi data (Mass Assignment)
    protected $fillable = [
        'transaction_type', // Ditambahkan: 'reguler' atau 'ekstra'
        'member_id',
        'extra_program_id', // Ditambahkan: Menyimpan ID program ekstra
        'external_id',
        'amount',
        'bank_code',
        'account_number',
        'payment_id',
        'periode'
    ];

    /**
     * Relasi Balik: 1 Transaksi ini murni dimiliki oleh 1 Anggota (Member)
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    /**
     * DITAMBAHKAN: Relasi Balik ke Tabel Program Ekstra
     * 1 Transaksi infak ekstra murni dialokasikan untuk 1 Program Ekstra
     */
    public function extraProgram()
    {
        return $this->belongsTo(ExtraProgram::class, 'extra_program_id');
    }
}