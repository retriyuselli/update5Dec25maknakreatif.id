<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

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
        'total_simulation',
        'notes',
        'customer_name',
        'customer_email',
        'customer_phone',
        'payment_dp_amount',
        'payment_term2_amount',
        'payment_term3_amount',
        'payment_term4_amount',
        'payment_simulation',
        'last_edited_by',
        'name_ttd',
        'title_ttd',
    ];

    protected $casts = [
        'payment_simulation' => 'array',
    ];

    protected $table = 'simulasi_produks';

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->user_id) {
                $model->user_id = Auth::id();
            }
        });

        static::updating(function ($model) {
            $model->last_edited_by = Auth::id();
        });
    }

    public function lastEditedBy()
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    // public function getGrandTotalAttribute()
    // {
    //     return $this->total_price + $this->penambahan - $this->promo - $this->pengurangan;
    // }

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
