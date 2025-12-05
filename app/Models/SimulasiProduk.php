<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SimulasiProduk extends Model
{
    protected $fillable = [
        'prospect_id',
        'product_id',
        'slug',
        'total_price',
        'user_id',
        'promo',
        'penambahan',
        'pengurangan',
        'grand_total',
        'notes',
    ];

    protected $table = 'simulasi_produks';

    public function getGrandTotalAttribute()
    {
        return $this->total_price + $this->penambahan - $this->promo - $this->pengurangan;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class);
    }

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }
}
