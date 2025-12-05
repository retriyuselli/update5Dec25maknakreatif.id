<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetDepreciation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fixed_asset_id',
        'depreciation_date',
        'depreciation_amount',
        'accumulated_depreciation_before',
        'accumulated_depreciation_after',
        'book_value_before',
        'book_value_after',
        'journal_batch_id',
        'notes',
        'is_adjustment',
    ];

    protected $casts = [
        'depreciation_date' => 'date',
        'depreciation_amount' => 'decimal:2',
        'accumulated_depreciation_before' => 'decimal:2',
        'accumulated_depreciation_after' => 'decimal:2',
        'book_value_before' => 'decimal:2',
        'book_value_after' => 'decimal:2',
        'is_adjustment' => 'boolean',
    ];

    // Relationships
    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }

    public function journalBatch(): BelongsTo
    {
        return $this->belongsTo(JournalBatch::class);
    }

    // Methods
    public function createJournalEntry(): void
    {
        // This will be implemented when we create the double-entry system
        // For now, just placeholder
    }

    // Scopes
    public function scopeByAsset($query, $assetId)
    {
        return $query->where('fixed_asset_id', $assetId);
    }

    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('depreciation_date', $year)
            ->whereMonth('depreciation_date', $month);
    }

    public function scopeAdjustments($query)
    {
        return $query->where('is_adjustment', true);
    }

    public function scopeRegular($query)
    {
        return $query->where('is_adjustment', false);
    }
}
