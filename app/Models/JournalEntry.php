<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'transaction_date',
        'reference_number',
        'description',
        'debit_amount',
        'credit_amount',
        'journal_batch_id',
        'reference_type',
        'reference_id',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    // Relationships
    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    // Alias untuk compatibility
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function journalBatch(): BelongsTo
    {
        return $this->belongsTo(JournalBatch::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Polymorphic relationship untuk reference
    public function referenceable()
    {
        return $this->morphTo('reference');
    }

    // Scopes
    public function scopeDebit($query)
    {
        return $query->where('debit_amount', '>', 0);
    }

    public function scopeCredit($query)
    {
        return $query->where('credit_amount', '>', 0);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeByAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    // Methods
    public function isDebit(): bool
    {
        return $this->debit_amount > 0;
    }

    public function isCredit(): bool
    {
        return $this->credit_amount > 0;
    }

    public function getAmountAttribute(): float
    {
        return $this->debit_amount ?: $this->credit_amount;
    }

    public function getTypeAttribute(): string
    {
        return $this->isDebit() ? 'debit' : 'credit';
    }
}
