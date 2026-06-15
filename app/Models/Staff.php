<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara spesifik
    protected $table = 'staffs';

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
    ];

    // Menyembunyikan password saat data di-convert ke array/json (keamanan)
    protected $hidden = [
        'password',
    ];
}