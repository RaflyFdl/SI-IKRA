<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| File ini mendefinisikan semua perintah eksekusi kustom berbasis terminal.
| Di Laravel 11, file ini juga berfungsi sebagai tempat pendaftaran
| Task Scheduling (penjadwalan otomatis di latar belakang).
|
*/

// Perintah bawaan Laravel untuk memunculkan kutipan motivasi
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * LOGIKA AUTOMATION SKRIPSI: Task Scheduler Pengingat Infak Reguler
 * Mengatur agar perintah 'infak:reminder' berjalan otomatis tanpa intervensi manusia
 * Dieksekusi setiap tanggal 1 awal bulan pada pukul 05:00 subuh.
 */
Schedule::command('infak:reminder')->monthlyOn(1, '05:00');