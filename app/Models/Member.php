<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara manual karena kita pakai nama 'members'
    protected $table = 'members';

    // Kolom apa saja yang boleh diisi oleh sistem saat form disubmit (Mass Assignment)
    protected $fillable = [
        'nama',
        'angkatan',
        'no_wa',
        'email',
        'password',
        'bukti_pendukung',
        'status',
        'va_muamalat',
        'verification_token', // [BARU] Mengizinkan pengisian token verifikasi email
        'email_verified_at'   // [BARU] Mengizinkan pengisian tanggal verifikasi email
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