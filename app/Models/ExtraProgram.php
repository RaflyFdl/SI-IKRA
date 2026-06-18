<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}