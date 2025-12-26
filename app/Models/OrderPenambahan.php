<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPenambahan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'vendor_id',
        'name',
        'description',
        'harga_publish',
        'harga_vendor',
    ];

    protected $casts = [
        'harga_publish' => 'decimal:2',
        'harga_vendor' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
