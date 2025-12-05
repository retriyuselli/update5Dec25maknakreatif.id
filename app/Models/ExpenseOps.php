<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class ExpenseOps extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'amount',
        'payment_method_id',
        'date_expense',
        'image',
        'no_nd',
        'note',
        'kategori_transaksi',
        // NotaDinas integration fields
        'nota_dinas_id',
        'nota_dinas_detail_id',
        'vendor_id',
        'bank_name',
        'account_holder',
        'bank_account',
        'tanggal_transfer',
        // Reconciliation fields
        'reconciliation_status',
        'matched_bank_item_id',
        'match_confidence',
        'reconciliation_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_expense' => 'date',
        'tanggal_transfer' => 'date',
        'amount' => 'decimal:2',
    ];

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    /**
     * Get the formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date_expense->format('d F Y');
    }

    /**
     * Custom attributes for export/display
     */
    public function toArray()
    {
        $attributes = parent::toArray();
        $attributes['formatted_amount'] = $this->formatted_amount;
        $attributes['formatted_date'] = $this->formatted_date;

        return $attributes;
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    // NotaDinas integration relationships
    public function notaDinas()
    {
        return $this->belongsTo(NotaDinas::class, 'nota_dinas_id');
    }

    public function notaDinasDetail()
    {
        return $this->belongsTo(NotaDinasDetail::class, 'nota_dinas_detail_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    // Query scope for filtering by amount range
    public function scopeAmountRange($query, $range)
    {
        return match ($range) {
            'low' => $query->where('amount', '<', 1000000),
            'medium' => $query->whereBetween('amount', [1000000, 5000000]),
            'high' => $query->where('amount', '>', 5000000),
            default => $query,
        };
    }

    // Query scope for filtering by date range
    public function scopeDateRange($query, $dateFrom, $dateUntil)
    {
        return
            $query
                ->when($dateFrom, fn ($q) => $q->whereDate('date_expense', '>=', $dateFrom))
                ->when($dateUntil, fn ($q) => $q->whereDate('date_expense', '<=', $dateUntil));
    }

    /**
     * Boot method to add validation and events
     */
    protected static function boot()
    {
        parent::boot();

        // Add validation before saving
        static::saving(function ($expenseOps) {
            if ($expenseOps->nota_dinas_detail_id) {
                // First, cleanup any soft deleted records that might cause constraint conflicts
                static::onlyTrashed()
                    ->where('nota_dinas_detail_id', $expenseOps->nota_dinas_detail_id)
                    ->forceDelete();

                // Check if nota_dinas_detail_id already exists (excluding current record)
                $existingExpenseOps = static::where('nota_dinas_detail_id', $expenseOps->nota_dinas_detail_id)
                    ->when($expenseOps->exists, function ($query) use ($expenseOps) {
                        return $query->where('id', '!=', $expenseOps->id);
                    })
                    ->first();

                if ($existingExpenseOps) {
                    $notaDinasDetail = NotaDinasDetail::with('vendor')->find($expenseOps->nota_dinas_detail_id);
                    $vendorName = $notaDinasDetail?->vendor?->name ?? 'Vendor ini';
                    $keperluan = $notaDinasDetail?->keperluan ?? 'item ini';
                    throw new Exception("Detail Nota Dinas untuk vendor {$vendorName} (keperluan: {$keperluan}) sudah memiliki ExpenseOps record. Silakan pilih detail nota dinas yang berbeda.");
                }
            }
        });

        // Clean up soft deleted records periodically when new records are created
        static::created(function ($expenseOps) {
            // Clean up old soft deleted records to prevent constraint conflicts
            $cleanupCount = static::onlyTrashed()
                ->where('created_at', '<', now()->subDays(7)) // Clean records older than 7 days
                ->forceDelete();

            if ($cleanupCount > 0) {
                Log::info("Cleaned up {$cleanupCount} old soft deleted ExpenseOps records");
            }
        });
    }
}
