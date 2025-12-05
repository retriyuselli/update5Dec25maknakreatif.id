<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PendapatanLain extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $fillable = [
        'name',
        'vendor_id',
        'payment_method_id',
        'nominal',
        'image',
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
        'nominal' => 'decimal:2',
        'kategori_transaksi' => 'string',
    ];

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
