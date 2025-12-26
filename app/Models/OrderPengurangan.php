<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPengurangan extends Model
{
    //
    protected $fillable = [
        'order_id',
        'total_pengurangan',
        'description',
        'notes',
    ];
}
