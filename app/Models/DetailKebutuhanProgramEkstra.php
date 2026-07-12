<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailKebutuhanProgramEkstra extends Model
{
    protected $table = 'detail_kebutuhan_programekstra';

    protected $fillable = [
        'extra_program_id',
        'nama_barang',
        'jumlah',
        'satuan',
        'harga'
    ];

    public function extraProgram()
    {
        return $this->belongsTo(ExtraProgram::class, 'extra_program_id');
    }
}
