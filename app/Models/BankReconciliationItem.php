<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class BankReconciliationItem extends Model
{
    protected $fillable = [
        'bank_statement_id',
        'date',
        'description',
        'debit',
        'credit',
        'row_number',
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    // ========================
    // DEBIT/CREDIT MAPPING METHODS
    // ========================

    /**
     * Get transaction direction (user-friendly)
     * Returns: 'masuk' or 'keluar'
     */
    public function getDirectionAttribute(): string
    {
        return $this->debit > 0 ? 'keluar' : 'masuk';
    }

    /**
     * Get direction label (user-friendly)
     * Returns: 'Uang Masuk' or 'Uang Keluar'
     */
    public function getDirectionLabelAttribute(): string
    {
        return $this->debit > 0 ? 'Uang Keluar' : 'Uang Masuk';
    }

    /**
     * Get transaction amount (regardless of debit/credit)
     */
    public function getAmountAttribute(): float
    {
        return $this->debit > 0 ? $this->debit : $this->credit;
    }

    /**
     * Get formatted amount with direction
     */
    public function getFormattedAmountAttribute(): string
    {
        $amount = number_format($this->amount, 0, ',', '.');
        $prefix = $this->debit > 0 ? '- Rp' : '+ Rp';

        return $prefix.' '.$amount;
    }

    /**
     * Get bank terminology (technical)
     */
    public function getBankTermAttribute(): string
    {
        return $this->debit > 0 ? 'Debit' : 'Credit';
    }

    /**
     * Check if this is a debit transaction
     */
    public function getIsDebitAttribute(): bool
    {
        return $this->debit > 0;
    }

    /**
     * Check if this is a credit transaction
     */
    public function getIsCreditAttribute(): bool
    {
        return $this->credit > 0;
    }

    // Main relationship to BankStatement (new integrated approach)
    public function bankStatement(): BelongsTo
    {
        return $this->belongsTo(BankStatement::class, 'bank_statement_id', 'id');
    }

    // Get the parent record
    public function getParentRecord()
    {
        return $this->bankStatement;
    }

    // Dynamic relationship - works with both new and legacy data
    public function parent()
    {
        return $this->getParentRecord();
    }
}
