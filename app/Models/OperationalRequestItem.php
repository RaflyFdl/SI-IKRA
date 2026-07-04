<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalRequestItem extends Model
{
    protected $fillable = ['operational_request_id', 'description', 'amount'];

    public function request()
    {
        return $this->belongsTo(OperationalRequest::class, 'operational_request_id');
    }
}