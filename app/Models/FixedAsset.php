<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class FixedAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_code',
        'asset_name',
        'category',
        'purchase_date',
        'purchase_price',
        'accumulated_depreciation',
        'depreciation_method',
        'useful_life_years',
        'useful_life_months',
        'salvage_value',
        'current_book_value',
        'location',
        'condition',
        'supplier',
        'invoice_number',
        'warranty_expiry',
        'notes',
        'chart_of_account_id',
        'depreciation_account_id',
        'is_active',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_price' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'current_book_value' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'useful_life_years' => 'integer',
        'useful_life_months' => 'integer',
        'is_active' => 'boolean',
    ];

    // Asset Categories
    const CATEGORIES = [
        'BUILDING' => 'Bangunan',
        'EQUIPMENT' => 'Peralatan',
        'FURNITURE' => 'Furniture & Fixtures',
        'VEHICLE' => 'Kendaraan',
        'COMPUTER' => 'Komputer & IT Equipment',
        'OTHER' => 'Lainnya',
    ];

    // Depreciation Methods
    const DEPRECIATION_METHODS = [
        'STRAIGHT_LINE' => 'Garis Lurus',
        'DECLINING_BALANCE' => 'Saldo Menurun',
        'UNITS_OF_PRODUCTION' => 'Unit Produksi',
    ];

    // Asset Conditions
    const CONDITIONS = [
        'EXCELLENT' => 'Sangat Baik',
        'GOOD' => 'Baik',
        'FAIR' => 'Cukup',
        'POOR' => 'Buruk',
        'DAMAGED' => 'Rusak',
    ];

    // Relationships
    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function depreciationAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'depreciation_account_id');
    }

    // public function journalBatches(): HasMany
    // {
    //     return $this->hasMany(JournalBatch::class, 'reference_id')
    //                ->where('reference_type', 'like', '%Asset%');
    // }

    public function depreciations(): HasMany
    {
        return $this->hasMany(AssetDepreciation::class);
    }

    // Methods
    public function calculateMonthlyDepreciation(): float
    {
        if ($this->depreciation_method === 'STRAIGHT_LINE') {
            $depreciableAmount = $this->purchase_price - $this->salvage_value;
            $totalMonths = ($this->useful_life_years * 12) + $this->useful_life_months;

            return $totalMonths > 0 ? $depreciableAmount / $totalMonths : 0;
        }

        // Add other depreciation methods as needed
        return 0;
    }

    public function updateBookValue(): void
    {
        $this->current_book_value = $this->purchase_price - $this->accumulated_depreciation;
        $this->save();
    }

    public function isFullyDepreciated(): bool
    {
        return $this->current_book_value <= $this->salvage_value;
    }

    public function getRemainingLifeAttribute(): array
    {
        $totalMonths = ($this->useful_life_years * 12) + $this->useful_life_months;
        $monthsSincePurchase = $this->purchase_date->diffInMonths(now());
        $remainingMonths = max(0, $totalMonths - $monthsSincePurchase);

        return [
            'years' => intval($remainingMonths / 12),
            'months' => $remainingMonths % 12,
            'total_months' => $remainingMonths,
        ];
    }

    // Static methods
    public static function generateAssetCode($category = null): string
    {
        $prefix = match ($category) {
            'BUILDING' => 'BLD',
            'EQUIPMENT' => 'EQP',
            'FURNITURE' => 'FRN',
            'VEHICLE' => 'VHC',
            'COMPUTER' => 'CMP',
            default => 'AST'
        };

        $year = date('Y');
        $lastAsset = self::where('asset_code', 'like', "{$prefix}/{$year}/%")
            ->orderBy('asset_code', 'desc')
            ->first();

        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->asset_code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s/%s/%04d', $prefix, $year, $newNumber);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeNeedsMaintenance($query)
    {
        return $query->whereIn('condition', ['FAIR', 'POOR']);
    }

    // Journal Entry Automation
    public function createPurchaseJournalEntry(): ?JournalBatch
    {
        // Check if journal entry already exists for this asset
        $existingJournal = JournalBatch::where('reference_type', 'Fixed Asset Purchase')
            ->where('reference_id', $this->id)
            ->first();

        if ($existingJournal) {
            return $existingJournal; // Already has journal entry
        }

        // Create journal batch for asset purchase
        $batch = JournalBatch::create([
            'batch_number' => JournalBatch::generateBatchNumber(),
            'transaction_date' => $this->purchase_date,
            'description' => "Pembelian aset tetap: {$this->asset_name} ({$this->asset_code})",
            'status' => 'draft',
            'reference_type' => 'Fixed Asset Purchase',
            'reference_id' => $this->id,
            'created_by' => Auth::id() ?? 1,
            'total_debit' => $this->purchase_price,
            'total_credit' => $this->purchase_price,
        ]);

        // Create journal entries
        // Debit: Asset Account
        JournalEntry::create([
            'journal_batch_id' => $batch->id,
            'account_id' => $this->chart_of_account_id,
            'transaction_date' => $this->purchase_date,
            'description' => "Pembelian {$this->asset_name}",
            'debit_amount' => $this->purchase_price,
            'credit_amount' => 0,
            'reference_type' => 'Fixed Asset Purchase',
            'reference_id' => $this->id,
            'created_by' => Auth::id() ?? 1,
        ]);

        // Credit: Cash or Accounts Payable (default to cash for now)
        $cashAccount = ChartOfAccount::where('account_code', '111000000')->first(); // Kas

        JournalEntry::create([
            'journal_batch_id' => $batch->id,
            'account_id' => $cashAccount->id,
            'transaction_date' => $this->purchase_date,
            'description' => "Pembayaran pembelian {$this->asset_name}",
            'debit_amount' => 0,
            'credit_amount' => $this->purchase_price,
            'reference_type' => 'Fixed Asset Purchase',
            'reference_id' => $this->id,
            'created_by' => Auth::id() ?? 1,
        ]);

        return $batch;
    }

    public function createDepreciationJournalEntry($amount = null): ?JournalBatch
    {
        $depreciationAmount = $amount ?? $this->calculateMonthlyDepreciation();

        if ($depreciationAmount <= 0) {
            return null; // No depreciation to record
        }

        // Create journal batch for depreciation
        $batch = JournalBatch::create([
            'batch_number' => JournalBatch::generateBatchNumber(),
            'transaction_date' => now(),
            'description' => "Penyusutan bulanan aset: {$this->asset_name} ({$this->asset_code})",
            'status' => 'draft',
            'reference_type' => 'Asset Depreciation',
            'reference_id' => $this->id,
            'created_by' => Auth::id() ?? 1,
            'total_debit' => $depreciationAmount,
            'total_credit' => $depreciationAmount,
        ]);

        // Get or create depreciation expense account
        $depreciationExpenseAccount = ChartOfAccount::where('account_code', 'like', '521%')
            ->where('account_name', 'like', '%penyusutan%')
            ->first();

        if (! $depreciationExpenseAccount) {
            // Create depreciation expense account if not exists
            $depreciationExpenseAccount = ChartOfAccount::create([
                'account_code' => '521000000',
                'account_name' => 'Beban Penyusutan Aset Tetap',
                'account_type' => 'BEBAN_OPERASIONAL',
                'normal_balance' => 'debit',
                'is_active' => true,
                'description' => 'Beban penyusutan untuk aset tetap',
            ]);
        }

        // Create journal entries
        // Debit: Depreciation Expense
        JournalEntry::create([
            'journal_batch_id' => $batch->id,
            'account_id' => $depreciationExpenseAccount->id,
            'transaction_date' => now(),
            'description' => "Beban penyusutan {$this->asset_name}",
            'debit_amount' => $depreciationAmount,
            'credit_amount' => 0,
            'reference_type' => 'Asset Depreciation',
            'reference_id' => $this->id,
            'created_by' => Auth::id() ?? 1,
        ]);

        // Credit: Accumulated Depreciation
        JournalEntry::create([
            'journal_batch_id' => $batch->id,
            'account_id' => $this->depreciation_account_id,
            'transaction_date' => now(),
            'description' => "Akumulasi penyusutan {$this->asset_name}",
            'debit_amount' => 0,
            'credit_amount' => $depreciationAmount,
            'reference_type' => 'Asset Depreciation',
            'reference_id' => $this->id,
            'created_by' => Auth::id() ?? 1,
        ]);

        return $batch;
    }

    public function createDisposalJournalEntry($disposalPrice = 0, $disposalDate = null): ?JournalBatch
    {
        $disposalDate = $disposalDate ?? now();
        $bookValue = $this->current_book_value;
        $gainLoss = $disposalPrice - $bookValue;

        // Create journal batch for disposal
        $batch = JournalBatch::create([
            'batch_number' => JournalBatch::generateBatchNumber(),
            'transaction_date' => $disposalDate,
            'description' => "Pelepasan aset tetap: {$this->asset_name} ({$this->asset_code})",
            'status' => 'draft',
            'reference_type' => 'Asset Disposal',
            'reference_id' => $this->id,
            'created_by' => Auth::id() ?? 1,
            'total_debit' => max($disposalPrice + $this->accumulated_depreciation, $this->purchase_price),
            'total_credit' => max($disposalPrice + $this->accumulated_depreciation, $this->purchase_price),
        ]);

        $cashAccount = ChartOfAccount::where('account_code', '111000000')->first(); // Kas

        // Create journal entries
        // If there's cash received
        if ($disposalPrice > 0) {
            JournalEntry::create([
                'journal_batch_id' => $batch->id,
                'account_id' => $cashAccount->id,
                'transaction_date' => $disposalDate,
                'description' => "Penerimaan kas dari penjualan {$this->asset_name}",
                'debit_amount' => $disposalPrice,
                'credit_amount' => 0,
                'reference_type' => 'Asset Disposal',
                'reference_id' => $this->id,
                'created_by' => Auth::id() ?? 1,
            ]);
        }

        // Debit: Accumulated Depreciation
        JournalEntry::create([
            'journal_batch_id' => $batch->id,
            'account_id' => $this->depreciation_account_id,
            'transaction_date' => $disposalDate,
            'description' => "Eliminasi akumulasi penyusutan {$this->asset_name}",
            'debit_amount' => $this->accumulated_depreciation,
            'credit_amount' => 0,
            'reference_type' => 'Asset Disposal',
            'reference_id' => $this->id,
            'created_by' => Auth::id() ?? 1,
        ]);

        // Handle gain or loss
        if ($gainLoss != 0) {
            $gainLossAccount = ChartOfAccount::where('account_name', 'like', '%laba rugi%pelepasan%')
                ->orWhere('account_name', 'like', '%gain loss%disposal%')
                ->first();

            if (! $gainLossAccount) {
                // Create gain/loss account if not exists
                $gainLossAccount = ChartOfAccount::create([
                    'account_code' => '590000000',
                    'account_name' => 'Laba Rugi Pelepasan Aset Tetap',
                    'account_type' => 'PENDAPATAN_LAIN',
                    'normal_balance' => 'credit',
                    'is_active' => true,
                    'description' => 'Laba atau rugi dari pelepasan aset tetap',
                ]);
            }

            JournalEntry::create([
                'journal_batch_id' => $batch->id,
                'account_id' => $gainLossAccount->id,
                'transaction_date' => $disposalDate,
                'description' => ($gainLoss > 0 ? 'Laba' : 'Rugi')." pelepasan {$this->asset_name}",
                'debit_amount' => $gainLoss < 0 ? abs($gainLoss) : 0,
                'credit_amount' => $gainLoss > 0 ? $gainLoss : 0,
                'reference_type' => 'Asset Disposal',
                'reference_id' => $this->id,
                'created_by' => Auth::id() ?? 1,
            ]);
        }

        // Credit: Asset Account (remove the asset)
        JournalEntry::create([
            'journal_batch_id' => $batch->id,
            'account_id' => $this->chart_of_account_id,
            'transaction_date' => $disposalDate,
            'description' => "Eliminasi aset {$this->asset_name}",
            'debit_amount' => 0,
            'credit_amount' => $this->purchase_price,
            'reference_type' => 'Asset Disposal',
            'reference_id' => $this->id,
            'created_by' => Auth::id() ?? 1,
        ]);

        return $batch;
    }
}
