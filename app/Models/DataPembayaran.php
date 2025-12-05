<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataPembayaran extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'nominal',
        'image',
        'payment_method_id',
        'tgl_bayar',
        'keterangan',
        'kategori_transaksi',
        'reconciliation_status',
        'matched_bank_item_id',
        'match_confidence',
        'reconciliation_notes',
    ];

    protected $casts = [
        'tgl_bayar' => 'date',
        'kategori_transaksi' => 'string',
    ];

    // public function nominal(): BelongsTo
    // {
    //     return $this->belongsTo(Order::class);
    // }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
