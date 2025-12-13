<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VendorPriceHistory extends Model
{
    protected $fillable = [
        'vendor_id',
        'harga_publish',
        'harga_vendor',
        'profit_amount',
        'profit_margin',
        'effective_from',
        'effective_to',
        'status',
        'kontrak',
        'description',
    ];

    protected $casts = [
        'harga_publish' => 'decimal:2',
        'harga_vendor' => 'decimal:2',
        'profit_amount' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'effective_from' => 'datetime',
        'effective_to' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $history): void {
            $hp = (float) ($history->harga_publish ?? 0);
            $hv = (float) ($history->harga_vendor ?? 0);
            $profit = $hp - $hv;
            $history->profit_amount = $profit;
            $history->profit_margin = $hp > 0 ? round(($profit / $hp) * 100, 2) : 0;

            // Ensure only one 'active' status per vendor (block save if more than one)
            if (Schema::hasColumn('vendor_price_histories', 'status')) {
                $status = $history->status ?? null;
                if ($status === 'active' && $history->vendor_id) {
                    $existsOtherActive = DB::table('vendor_price_histories')
                        ->where('vendor_id', $history->vendor_id)
                        ->where('id', '!=', $history->id ?? 0)
                        ->where('status', 'active')
                        ->exists();

                    if ($existsOtherActive) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'priceHistories' => 'Hanya satu riwayat harga dapat berstatus active untuk setiap vendor.'
                        ]);
                    }
                }
            }
        });

        static::retrieved(function (self $history): void {
            $hp = (float) ($history->harga_publish ?? 0);
            $hv = (float) ($history->harga_vendor ?? 0);
            $profit = $hp - $hv;
            $margin = $hp > 0 ? round(($profit / $hp) * 100, 2) : 0;

            $needsUpdate = ($history->profit_amount === null) || ($history->profit_margin === null) ||
                ((float) $history->profit_amount !== (float) $profit) ||
                ((float) $history->profit_margin !== (float) $margin);

            if ($needsUpdate) {
                $history->profit_amount = $profit;
                $history->profit_margin = $margin;
                try {
                    $history->saveQuietly();
                } catch (\Throwable $e) {
                }
            }
        });
    }

    public function calculateProfitAmount(): void
    {
        $this->profit_amount = $this->harga_publish - $this->harga_vendor;
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

}
