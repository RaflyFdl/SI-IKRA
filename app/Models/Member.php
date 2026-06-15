<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara manual karena kita pakai nama 'members'
    protected $table = 'members';

    // Kolom apa saja yang boleh diisi oleh sistem saat form disubmit
    protected $fillable = [
        'nama',
        'angkatan',
        'no_wa',
        'email',
        'password',
        'bukti_pendukung',
        'status',
        'va_muamalat'
    ];

    // Menyembunyikan password saat data dipanggil demi keamanan
    protected $hidden = [
        'password',
    ];

    /**
     * Relasi: 1 Anggota bisa memiliki banyak riwayat transaksi infak reguler
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'member_id');
    }
}