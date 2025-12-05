<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'parent_id',
        'level',
        'is_active',
        'description',
        'normal_balance', // debit or credit
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
    ];

    // Account Types berdasarkan gambar COA Anda
    const ACCOUNT_TYPES = [
        'HARTA' => 'Harta (Assets)',
        'KEWAJIBAN' => 'Kewajiban (Liabilities)',
        'MODAL' => 'Modal (Equity)',
        'PENDAPATAN' => 'Pendapatan (Revenue)',
        'BEBAN_ATAS_PENDAPATAN' => 'Beban Atas Pendapatan (COGS)',
        'BEBAN_OPERASIONAL' => 'Beban Operasional (Operating Expenses)',
        'PENDAPATAN_LAIN' => 'Pendapatan Lain (Other Income)',
        'BEBAN_LAIN' => 'Beban Lain (Other Expenses)',
    ];

    const NORMAL_BALANCE = [
        'HARTA' => 'debit',
        'KEWAJIBAN' => 'credit',
        'MODAL' => 'credit',
        'PENDAPATAN' => 'credit',
        'BEBAN_ATAS_PENDAPATAN' => 'debit',
        'BEBAN_OPERASIONAL' => 'debit',
        'PENDAPATAN_LAIN' => 'credit',
        'BEBAN_LAIN' => 'debit',
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    // public function journalEntries(): HasMany
    // {
    //     return $this->hasMany(JournalEntry::class, 'account_id');
    // }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeMainAccounts($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSubAccounts($query)
    {
        return $query->whereNotNull('parent_id');
    }

    // Methods
    public function getFullAccountNameAttribute(): string
    {
        return $this->account_code.' - '.$this->account_name;
    }

    public function getBalance($startDate = null, $endDate = null): float
    {
        $query = $this->journalEntries();

        if ($startDate) {
            $query->where('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('transaction_date', '<=', $endDate);
        }

        $debits = $query->sum('debit_amount');
        $credits = $query->sum('credit_amount');

        // Return balance based on normal balance
        if ($this->normal_balance === 'debit') {
            return $debits - $credits;
        } else {
            return $credits - $debits;
        }
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function isLeafAccount(): bool
    {
        return ! $this->hasChildren();
    }

    // Static methods
    public static function generateAccountCode($parentCode = null, $accountType = null): string
    {
        if ($parentCode) {
            // Generate sub-account code
            $siblings = self::where('parent_id', function ($query) use ($parentCode) {
                $query->select('id')
                    ->from('chart_of_accounts')
                    ->where('account_code', $parentCode)
                    ->limit(1);
            })->count();

            return $parentCode.str_pad($siblings + 1, 2, '0', STR_PAD_LEFT);
        }

        // Generate main account code based on type
        $typePrefixes = [
            'HARTA' => '1',
            'KEWAJIBAN' => '2',
            'MODAL' => '3',
            'PENDAPATAN' => '4',
            'BEBAN_ATAS_PENDAPATAN' => '5',
            'BEBAN_OPERASIONAL' => '6',
            'PENDAPATAN_LAIN' => '8',
            'BEBAN_LAIN' => '9',
        ];

        $prefix = $typePrefixes[$accountType] ?? '1';
        $lastAccount = self::where('account_code', 'like', $prefix.'%')
            ->where('level', 1)
            ->orderBy('account_code', 'desc')
            ->first();

        if ($lastAccount) {
            $lastNumber = (int) substr($lastAccount->account_code, 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix.str_pad($newNumber, 8, '0', STR_PAD_LEFT);
    }
}
