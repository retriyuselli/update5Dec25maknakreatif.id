<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bank_statement_id',
        'transaction_date',
        'value_date',
        'description',
        'reference_number',
        'debit_amount',
        'credit_amount',
        'balance',
        'transaction_type',
        'category',
        'is_matched',
        'matched_with_transaction_id',
        'matching_confidence',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'value_date' => 'date',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'is_matched' => 'boolean',
        'matching_confidence' => 'decimal:2',
    ];

    public function bankStatement(): BelongsTo
    {
        return $this->belongsTo(BankStatement::class);
    }

    public function matchedTransaction(): BelongsTo
    {
        // Return relationship to itself for now since Transaction model doesn't exist
        return $this->belongsTo(BankTransaction::class, 'matched_with_transaction_id');
    }

    public static function getTransactionTypes(): array
    {
        return [
            'debit' => 'Debit - Uang Keluar',
            'credit' => 'Credit - Uang Masuk',
        ];
    }

    public static function getCategories(): array
    {
        return [
            'transfer' => 'Transfer',
            'deposit' => 'Setoran',
            'withdrawal' => 'Penarikan',
            'fee' => 'Biaya Admin',
            'interest' => 'Bunga',
            'charge' => 'Biaya Lainnya',
            'correction' => 'Koreksi',
            'other' => 'Lainnya',
        ];
    }

    // Get the amount (debit or credit)
    public function getAmountAttribute(): float
    {
        return $this->debit_amount ?: $this->credit_amount;
    }

    // Get the net amount (credit positive, debit negative)
    public function getNetAmountAttribute(): float
    {
        return $this->credit_amount - $this->debit_amount;
    }

    // Check if this is a debit transaction
    public function getIsDebitAttribute(): bool
    {
        return $this->debit_amount > 0;
    }

    // Check if this is a credit transaction
    public function getIsCreditAttribute(): bool
    {
        return $this->credit_amount > 0;
    }

    // Get transaction direction for display
    public function getDirectionAttribute(): string
    {
        return $this->is_debit ? 'Keluar' : 'Masuk';
    }

    // Get formatted amount with direction
    public function getFormattedAmountAttribute(): string
    {
        $amount = number_format($this->amount, 0, ',', '.');
        $prefix = $this->is_debit ? '-' : '+';

        return $prefix.'Rp '.$amount;
    }

    // Scope for unmatched transactions
    public function scopeUnmatched($query)
    {
        return $query->where('is_matched', false);
    }

    // Scope for matched transactions
    public function scopeMatched($query)
    {
        return $query->where('is_matched', true);
    }

    // Scope for debit transactions
    public function scopeDebits($query)
    {
        return $query->where('debit_amount', '>', 0);
    }

    // Scope for credit transactions
    public function scopeCredits($query)
    {
        return $query->where('credit_amount', '>', 0);
    }

    // Scope for transactions in date range
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    // Mark as matched with a system transaction
    public function markAsMatched($transactionId, $confidence = 100.0, $notes = null): void
    {
        $this->update([
            'is_matched' => true,
            'matched_with_transaction_id' => $transactionId,
            'matching_confidence' => $confidence,
            'notes' => $notes,
        ]);
    }

    // Unmark as matched
    public function unmarkAsMatched(): void
    {
        $this->update([
            'is_matched' => false,
            'matched_with_transaction_id' => null,
            'matching_confidence' => null,
            'notes' => null,
        ]);
    }
}
