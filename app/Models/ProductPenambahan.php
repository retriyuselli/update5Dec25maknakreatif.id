<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPenambahan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'vendor_id',
        'description',
        // 'amount',
        'harga_publish',
        'harga_vendor',
        'kategori_transaksi',
        'notes', // Optional: if you added notes in the repeater
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2', // Cast amount to a decimal with 2 places
        'harga_publish' => 'decimal:2',
        'harga_vendor' => 'decimal:2',
        'kategori_transaksi' => 'string',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
