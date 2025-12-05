<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class JournalBatch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'batch_number',
        'transaction_date',
        'description',
        'total_debit',
        'total_credit',
        'status',
        'reference_type',
        'reference_id',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';

    const STATUS_POSTED = 'posted';

    const STATUS_REVERSED = 'reversed';

    // Relationships
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Polymorphic relationship untuk reference
    public function referenceable()
    {
        return $this->morphTo('reference');
    }

    // Scopes
    public function scopePosted($query)
    {
        return $query->where('status', self::STATUS_POSTED);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    // Methods
    public function isBalanced(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    public function canBePosted(): bool
    {
        return $this->status === self::STATUS_DRAFT && $this->isBalanced();
    }

    public function post(): bool
    {
        if (! $this->canBePosted()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_POSTED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return true;
    }

    public function calculateTotals(): void
    {
        $this->total_debit = $this->journalEntries()->sum('debit_amount');
        $this->total_credit = $this->journalEntries()->sum('credit_amount');
        $this->save();
    }

    public static function generateBatchNumber(): string
    {
        $date = now()->format('Ymd');
        $lastBatch = self::where('batch_number', 'like', "JB{$date}%")
            ->orderBy('batch_number', 'desc')
            ->first();

        if ($lastBatch) {
            $lastNumber = (int) substr($lastBatch->batch_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "JB{$date}".str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
