<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // ✅ TAMBAHAN: Wajib diimpor untuk menjalankan query DB

class ExtraProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',         // <-- Tambahkan ini untuk membedakan Podcast / Cinema
        'description',
        'target_amount',
        'current_amount',
        'end_date',
        'execution_date',    // <-- Tambahkan ini untuk menyimpan tanggal pelaksanaan/tayang
        'image_path',
        'va_number',
        'external_id',
        'status',
        'documentation_path',
    ];

    /**
     * STANDAR INDUSTRI: Eloquent Accessor untuk menghitung dana terkumpul secara dinamis.
     * Fungsi ini otomatis berjalan saat kamu memanggil `$program->current_amount`.
     */
    public function getCurrentAmountAttribute()
    {
        // Menghitung total dari transaksi tipe 'ekstra' yang sukses (payment_id TIDAK NULL)
        // dan format external_id nya diakhiri dengan "_ID-PROGRAM-INI"
        return DB::table('transactions')
            ->where('transaction_type', 'ekstra')
            ->whereNotNull('payment_id')
            ->where('external_id', 'like', "%_{$this->id}")
            ->sum('amount') ?? 0;
    }
}