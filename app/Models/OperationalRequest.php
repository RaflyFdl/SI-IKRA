<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalRequest extends Model
{
    protected $fillable = [
        'title', 'total_amount', 'status_pembina', 'status_keuangan', 
        'realization_report', 'realization_proof_path', 'reported_at'
    ];

    // Relasi ke item-item kebutuhan di bawahnya
    public function items()
    {
        return $this->hasMany(OperationalRequestItem::class);
    }
}