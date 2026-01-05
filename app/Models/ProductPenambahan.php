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
        'harga_publish',
        'harga_vendor',
        'kategori_transaksi',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'integer',
        'harga_publish' => 'integer',
        'harga_vendor' => 'integer',
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
