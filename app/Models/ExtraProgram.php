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

    /**
     * Mengambil daftar tanggal yang sudah terpakai/dipesan oleh program apa pun di database.
     * Mengembalikan array format string ['YYYY-MM-DD', ...]
     */
    public static function getBusyDates()
    {
        $dates = [];

        // 1. Tanggal Donasi Umum (execution_date)
        $donasiDates = DB::table('extra_programs')
            ->where('category', 'Donasi Umum')
            ->whereNotNull('execution_date')
            ->pluck('execution_date')
            ->toArray();
        $dates = array_merge($dates, $donasiDates);

        // 2. Tanggal Podcast (taping_date)
        $podcastDates = DB::table('podcasts')
            ->whereNotNull('taping_date')
            ->selectRaw('DATE(taping_date) as date_only')
            ->pluck('date_only')
            ->toArray();
        $dates = array_merge($dates, $podcastDates);

        // 3. Tanggal Cinema Edukasi (jadwal_kegiatan)
        $cinemaDates = DB::table('cinema_edukasi')
            ->whereNotNull('jadwal_kegiatan')
            ->selectRaw('DATE(jadwal_kegiatan) as date_only')
            ->pluck('date_only')
            ->toArray();
        $dates = array_merge($dates, $cinemaDates);

        // 4. Tanggal Penyaluran Reguler (tanggal_pelaksanaan)
        $regulerDates = DB::table('penyaluran_reguler')
            ->whereNotNull('tanggal_pelaksanaan')
            ->pluck('tanggal_pelaksanaan')
            ->toArray();
        $dates = array_merge($dates, $regulerDates);

        // Bersihkan duplikasi dan kembalikan array unik terindeks
        return array_values(array_unique(array_filter($dates)));
    }
}