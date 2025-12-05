<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

class BankStatement extends Model
{
    protected $fillable = [
        'payment_method_id', // Changed to match migration and resource
        'period_start',
        'period_end',
        'file_path',
        'original_filename',
        'source_type',
        'status',
        'uploaded_at',
        'processed_at',

        'branch', // Cabang pembuka rekening
        'opening_balance', // Saldo awal rekening
        'closing_balance', // Saldo akhir rekening
        'no_of_debit', // Total number of debit transactions
        'tot_debit', // Total debit amount
        'no_of_credit', // Total number of credit transactions
        'tot_credit', // Total credit amount

        // Bank reconciliation fields
        'title',
        'description',
        'reconciliation_file',
        'reconciliation_original_filename',
        'total_records',
        'total_debit_reconciliation',
        'total_credit_reconciliation',
        'reconciliation_status',
        'uploaded_by',
        'last_edited_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'uploaded_at' => 'datetime',
        'processed_at' => 'datetime',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'tot_debit' => 'decimal:2',
        'tot_credit' => 'decimal:2',
        'total_debit_reconciliation' => 'decimal:2',
        'total_credit_reconciliation' => 'decimal:2',
    ];

    public function paymentMethod(): BelongsTo // Corrected typo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function lastEditedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    public function bankReconciliationItems(): HasMany
    {
        return $this->hasMany(BankReconciliationItem::class, 'bank_reconciliation_id', 'id');
    }

    // Alias for better readability
    public function reconciliationItems(): HasMany
    {
        return $this->bankReconciliationItems();
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'parsed' => 'Parsed',
            'failed' => 'Failed',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return Arr::get(self::getStatusOptions(), $this->status, $this->status);
    }

    public static function getSourceTypeOptions(): array
    {
        return [
            'pdf' => 'PDF',
            'excel' => 'Excel',
            'manual_input' => 'Manual Input',
        ];
    }

    public static function getReconciliationStatusOptions(): array
    {
        return [
            'uploaded' => 'Uploaded',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
        ];
    }
}
