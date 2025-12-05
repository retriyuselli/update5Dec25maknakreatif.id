<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'note',
        'date_expense',
        'amount',
        'vendor_id',
        'payment_method_id',
        'no_nd',
        'image',
        'kategori_transaksi',

        // Tambahkan atribut lain yang diperlukan
        'nota_dinas_id',
        'nota_dinas_detail_id',
        'payment_stage',
        'account_holder',
        'bank_name',
        'bank_account',

        // Reconciliation fields
        'reconciliation_status',
        'matched_bank_item_id',
        'match_confidence',
        'reconciliation_notes',
    ];

    protected $casts = [
        'date_expense' => 'date', // Atau 'datetime' jika Anda menyimpan waktu juga
        'amount' => 'float', // Pastikan amount juga di-cast jika perlu
    ];

    public function setAmountAttribute($value)
    {
        if (is_string($value)) {
            // Remove any formatting and convert to float
            $value = preg_replace('/[^\d.,]/', '', $value);
            $value = str_replace(',', '', $value);
        }

        $this->attributes['amount'] = floatval($value);
    }

    public function category(): BelongsTo
    {
        // Assumes 'category_id' is the foreign key in the 'expenses' table
        // Assumes your Category model is App\Models\Category
        return $this->belongsTo(Category::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'payment_method_id');
    }

    /**
     * Boot the model to add validation rules
     */
    protected static function boot()
    {
        parent::boot();

        // Add validation before saving
        static::saving(function ($expense) {
            if ($expense->nota_dinas_detail_id && $expense->order_id) {
                // First, cleanup any soft deleted records that might cause constraint conflicts
                static::onlyTrashed()
                    ->where('order_id', $expense->order_id)
                    ->where('nota_dinas_detail_id', $expense->nota_dinas_detail_id)
                    ->forceDelete();

                // Check if nota_dinas_detail_id already exists for this order (excluding current record)
                $existingExpense = static::where('order_id', $expense->order_id)
                    ->where('nota_dinas_detail_id', $expense->nota_dinas_detail_id)
                    ->when($expense->exists, function ($query) use ($expense) {
                        return $query->where('id', '!=', $expense->id);
                    })
                    ->first();

                if ($existingExpense) {
                    $notaDinasDetail = NotaDinasDetail::with('vendor')->find($expense->nota_dinas_detail_id);
                    $vendorName = $notaDinasDetail?->vendor?->name ?? 'Vendor ini';
                    $paymentStage = $notaDinasDetail?->payment_stage ?? 'tahap ini';
                    throw new Exception("Detail Nota Dinas untuk vendor {$vendorName} (tahap: {$paymentStage}) sudah memiliki expense pada order ini. Silakan pilih detail nota dinas yang berbeda.");
                }
            }
        });

        // Clean up soft deleted records periodically when new records are created
        static::created(function ($expense) {
            // Clean up old soft deleted records for the same order to prevent constraint conflicts
            if ($expense->order_id) {
                $cleanupCount = static::onlyTrashed()
                    ->where('order_id', $expense->order_id)
                    ->where('created_at', '<', now()->subDays(7)) // Clean records older than 7 days
                    ->forceDelete();

                if ($cleanupCount > 0) {
                    Log::info("Cleaned up {$cleanupCount} old soft deleted expense records for order {$expense->order_id}");
                }
            }
        });
    }

    /**
     * Get payment stage label
     */
    public static function getPaymentStageLabel($stage)
    {
        $labels = [
            'down_payment' => 'Down Payment (DP)',
            'payment_1' => 'Pembayaran 1',
            'payment_2' => 'Pembayaran 2',
            'payment_3' => 'Pembayaran 3',
            'final_payment' => 'Pelunasan',
            'additional' => 'Tambahan/Lainnya',
        ];

        return $labels[$stage] ?? $stage;
    }

    public function notaDinas()
    {
        return $this->belongsTo(NotaDinas::class);
    }

    public function notaDinasDetail()
    {
        return $this->belongsTo(NotaDinasDetail::class);
    }

    /**
     * Get the journal batches for this expense.
     */
    public function journalBatches()
    {
        return $this->hasMany(JournalBatch::class, 'reference_id')
            ->whereIn('reference_type', ['expense', 'expense_reversal']);
    }
}
