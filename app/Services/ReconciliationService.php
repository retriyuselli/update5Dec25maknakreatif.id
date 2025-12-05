<?php

namespace App\Services;

use App\Models\BankReconciliationItem;
use App\Models\BankStatement;
use App\Models\DataPembayaran;
use App\Models\Expense;
use App\Models\ExpenseOps;
use App\Models\PaymentMethod;
use App\Models\PendapatanLain;
use App\Models\PengeluaranLain;
use App\Models\UnifiedTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ReconciliationService
{
    const EXACT_MATCH_CONFIDENCE = 100.00;

    const HIGH_CONFIDENCE = 85.00;

    const MEDIUM_CONFIDENCE = 70.00;

    const LOW_CONFIDENCE = 50.00;

    /**
     * Perform reconciliation between PaymentMethod transactions and BankStatement items
     */
    public function reconcile(int $paymentMethodId, ?string $startDate = null, ?string $endDate = null, bool $saveMatches = true): array
    {
        // Get unified transactions from PaymentMethod
        $appTransactions = UnifiedTransaction::getForPaymentMethod(
            $paymentMethodId,
            $startDate,
            $endDate
        );

        // Get bank statement items for the same PaymentMethod
        $bankItems = $this->getBankItemsForPaymentMethod($paymentMethodId, $startDate, $endDate);

        $results = [
            'matched' => [],
            'unmatched_app' => [],
            'unmatched_bank' => [],
            'disputed' => [],
            'statistics' => [
                'total_app_transactions' => $appTransactions->count(),
                'total_bank_items' => $bankItems->count(),
                'matched_count' => 0,
                'unmatched_app_count' => 0,
                'unmatched_bank_count' => 0,
                'disputed_count' => 0,
            ],
        ];

        $matchedBankIds = [];
        $matchedAppIds = [];

        // Phase 1: Exact matches (Date + Amount)
        foreach ($appTransactions as $appTx) {
            $exactMatch = $this->findExactMatch($appTx, $bankItems, $matchedBankIds);

            if ($exactMatch) {
                // DEBUG: Log exact matches for analysis
                Log::info("EXACT_MATCH_FOUND: App({$appTx->description}) vs Bank({$exactMatch->description}) - AppCredit={$appTx->credit_amount}, BankCredit={$exactMatch->credit}");

                // Save match to database if requested
                if ($saveMatches) {
                    $this->markAsMatched($appTx, $exactMatch, self::EXACT_MATCH_CONFIDENCE, ['date', 'amount']);
                }

                $results['matched'][] = [
                    'app_transaction' => $appTx,
                    'bank_item' => $exactMatch,
                    'confidence' => self::EXACT_MATCH_CONFIDENCE,
                    'match_type' => 'exact',
                    'match_criteria' => ['date', 'amount'],
                ];

                $matchedBankIds[] = $exactMatch->id;
                $matchedAppIds[] = $this->getTransactionKey($appTx);
            }
        }

        // Phase 2: Fuzzy matches (Date range + Amount tolerance + Description similarity)
        foreach ($appTransactions as $appTx) {
            $txKey = $this->getTransactionKey($appTx);
            if (in_array($txKey, $matchedAppIds)) {
                continue; // Already matched
            }

            $fuzzyMatch = $this->findFuzzyMatch($appTx, $bankItems, $matchedBankIds);

            if ($fuzzyMatch['item'] && $fuzzyMatch['confidence'] >= self::LOW_CONFIDENCE) {
                // Save match to database if requested
                if ($saveMatches) {
                    $this->markAsMatched($appTx, $fuzzyMatch['item'], $fuzzyMatch['confidence'], $fuzzyMatch['criteria']);
                }

                $results['matched'][] = [
                    'app_transaction' => $appTx,
                    'bank_item' => $fuzzyMatch['item'],
                    'confidence' => $fuzzyMatch['confidence'],
                    'match_type' => 'fuzzy',
                    'match_criteria' => $fuzzyMatch['criteria'],
                ];

                $matchedBankIds[] = $fuzzyMatch['item']->id;
                $matchedAppIds[] = $txKey;
            }
        }

        // Collect unmatched transactions
        foreach ($appTransactions as $appTx) {
            $txKey = $this->getTransactionKey($appTx);
            if (! in_array($txKey, $matchedAppIds)) {
                $results['unmatched_app'][] = $appTx;
            }
        }

        foreach ($bankItems as $bankItem) {
            if (! in_array($bankItem->id, $matchedBankIds)) {
                $results['unmatched_bank'][] = $bankItem;
            }
        }

        // Update statistics
        $results['statistics']['matched_count'] = count($results['matched']);
        $results['statistics']['unmatched_app_count'] = count($results['unmatched_app']);
        $results['statistics']['unmatched_bank_count'] = count($results['unmatched_bank']);

        // Calculate match percentage
        $totalAppTransactions = $results['statistics']['total_app_transactions'];
        $results['statistics']['match_percentage'] = $totalAppTransactions > 0
            ? round(($results['statistics']['matched_count'] / $totalAppTransactions) * 100, 1)
            : 0;

        // Calculate totals for export
        $results['statistics']['total_app_debit'] = $appTransactions->sum('debit_amount');
        $results['statistics']['total_app_credit'] = $appTransactions->sum('credit_amount');
        $results['statistics']['total_bank_debit'] = $bankItems->sum('debit');
        $results['statistics']['total_bank_credit'] = $bankItems->sum('credit');

        return $results;
    }

    /**
     * Find exact match (same date and amount)
     */
    private function findExactMatch($appTx, Collection $bankItems, array $excludeIds)
    {
        return $bankItems->first(function ($bankItem) use ($appTx, $excludeIds) {
            if (in_array($bankItem->id, $excludeIds)) {
                return false;
            }

            // Check date match
            $dateMatch = $appTx->transaction_date->isSameDay($bankItem->date);

            // Check amount match with zero validation
            $amountMatch = false;
            if ($appTx->is_income) {
                // App transaction is income (credit), should match bank credit
                $appAmount = $appTx->credit_amount ?? 0;
                $bankAmount = $bankItem->credit ?? 0;

                // CRITICAL: Reject if either amount is zero
                if ($appAmount <= 0 || $bankAmount <= 0) {
                    Log::info("EXACT_MATCH_REJECTED: App={$appAmount}, Bank={$bankAmount}, Description: {$appTx->description}");

                    return false;
                }

                $amountMatch = abs($appAmount - $bankAmount) < 0.01;
            } else {
                // App transaction is expense (debit), should match bank debit
                $appAmount = $appTx->debit_amount ?? 0;
                $bankAmount = $bankItem->debit ?? 0;

                // CRITICAL: Reject if either amount is zero
                if ($appAmount <= 0 || $bankAmount <= 0) {
                    Log::info("EXACT_MATCH_REJECTED: App={$appAmount}, Bank={$bankAmount}, Description: {$appTx->description}");

                    return false;
                }

                $amountMatch = abs($appAmount - $bankAmount) < 0.01;
            }

            return $dateMatch && $amountMatch;
        });
    }

    /**
     * Find fuzzy match with confidence scoring
     */
    private function findFuzzyMatch($appTx, Collection $bankItems, array $excludeIds): array
    {
        $bestMatch = null;
        $bestConfidence = 0;
        $bestCriteria = [];

        foreach ($bankItems as $bankItem) {
            if (in_array($bankItem->id, $excludeIds)) {
                continue;
            }

            $confidence = 0;
            $criteria = [];

            // Date proximity (max 3 days tolerance)
            $daysDiff = abs($appTx->transaction_date->diffInDays($bankItem->date));
            if ($daysDiff == 0) {
                $confidence += 40; // Same day
                $criteria[] = 'same_date';
            } elseif ($daysDiff <= 1) {
                $confidence += 30; // 1 day difference
                $criteria[] = 'close_date';
            } elseif ($daysDiff <= 3) {
                $confidence += 15; // 2-3 days difference
                $criteria[] = 'near_date';
            } else {
                continue; // Too far apart
            }

            // Amount match with zero protection
            $amountMatch = false;
            if ($appTx->is_income) {
                $appAmount = $appTx->credit_amount ?? 0;
                $bankAmount = $bankItem->credit ?? 0;

                // CRITICAL: Skip if either amount is zero or null
                if ($appAmount <= 0 || $bankAmount <= 0) {
                    continue; // Skip this bank item
                }

                $amountDiff = abs($appAmount - $bankAmount);
                if ($amountDiff < 0.01) {
                    $confidence += 40; // Exact amount
                    $criteria[] = 'exact_amount';
                    $amountMatch = true;
                } elseif ($amountDiff / $appAmount <= 0.02) {
                    $confidence += 25; // Within 2%
                    $criteria[] = 'close_amount';
                    $amountMatch = true;
                }
            } else {
                $appAmount = $appTx->debit_amount ?? 0;
                $bankAmount = $bankItem->debit ?? 0;

                // CRITICAL: Skip if either amount is zero or null
                if ($appAmount <= 0 || $bankAmount <= 0) {
                    continue; // Skip this bank item
                }

                $amountDiff = abs($appAmount - $bankAmount);
                if ($amountDiff < 0.01) {
                    $confidence += 40; // Exact amount
                    $criteria[] = 'exact_amount';
                    $amountMatch = true;
                } elseif ($amountDiff / $appAmount <= 0.02) {
                    $confidence += 25; // Within 2%
                    $criteria[] = 'close_amount';
                    $amountMatch = true;
                }
            }

            if (! $amountMatch) {
                continue; // Amount too different
            }

            // Description similarity (using Levenshtein distance)
            $similarity = $this->calculateDescriptionSimilarity(
                $appTx->description,
                $bankItem->description
            );

            if ($similarity > 0.8) {
                $confidence += 20; // High similarity
                $criteria[] = 'high_desc_similarity';
            } elseif ($similarity > 0.6) {
                $confidence += 10; // Medium similarity
                $criteria[] = 'medium_desc_similarity';
            } elseif ($similarity > 0.4) {
                $confidence += 5; // Low similarity
                $criteria[] = 'low_desc_similarity';
            }

            if ($confidence > $bestConfidence) {
                $bestConfidence = $confidence;
                $bestMatch = $bankItem;
                $bestCriteria = $criteria;
            }
        }

        return [
            'item' => $bestMatch,
            'confidence' => $bestConfidence,
            'criteria' => $bestCriteria,
        ];
    }

    /**
     * Calculate description similarity using Levenshtein distance
     */
    private function calculateDescriptionSimilarity(string $desc1, string $desc2): float
    {
        $desc1 = strtolower(trim($desc1));
        $desc2 = strtolower(trim($desc2));

        if (empty($desc1) || empty($desc2)) {
            return 0.0;
        }

        $maxLength = max(strlen($desc1), strlen($desc2));
        if ($maxLength == 0) {
            return 1.0;
        }

        $distance = levenshtein($desc1, $desc2);

        return 1 - ($distance / $maxLength);
    }

    /**
     * Get bank reconciliation items for specific PaymentMethod
     */
    private function getBankItemsForPaymentMethod(int $paymentMethodId, ?string $startDate, ?string $endDate): Collection
    {
        // Get BankStatements for this PaymentMethod within the date range
        $bankStatements = BankStatement::where('payment_method_id', $paymentMethodId)
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                // Find statements that overlap with the requested date range
                return $q->where(function ($q) use ($startDate, $endDate) {
                    $q->where('period_start', '<=', $endDate)
                        ->where('period_end', '>=', $startDate);
                });
            })
            ->when($startDate && ! $endDate, fn ($q) => $q->where('period_end', '>=', $startDate))
            ->when(! $startDate && $endDate, fn ($q) => $q->where('period_start', '<=', $endDate))
            ->pluck('id');

        // Get all BankReconciliationItems for these statements
        $bankItems = BankReconciliationItem::whereIn('bank_reconciliation_id', $bankStatements)
            ->when($startDate, fn ($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('date', '<=', $endDate))
            ->orderBy('date')
            ->get();

        return $bankItems;
    }

    /**
     * Get stored matches from database
     */
    public function getStoredMatches(int $paymentMethodId, ?string $startDate = null, ?string $endDate = null): array
    {
        $allAppTransactions = collect();

        // Get from all source tables with their reconciliation status
        $sourceTables = [
            [
                'model' => DataPembayaran::class,
                'table' => 'data_pembayarans',
                'date_field' => 'tgl_bayar',
                'amount_field' => 'nominal',
                'credit' => true,
                'source_type' => 'wedding_payment',
            ],
            [
                'model' => PendapatanLain::class,
                'table' => 'pendapatan_lains',
                'date_field' => 'tgl_bayar',
                'amount_field' => 'nominal',
                'credit' => true,
                'source_type' => 'other_income',
            ],
            [
                'model' => Expense::class,
                'table' => 'expenses',
                'date_field' => 'date_expense',
                'amount_field' => 'amount',
                'credit' => false,
                'source_type' => 'expense',
            ],
            [
                'model' => ExpenseOps::class,
                'table' => 'expense_ops',
                'date_field' => 'date_expense',
                'amount_field' => 'amount',
                'credit' => false,
                'source_type' => 'operational_expense',
            ],
            [
                'model' => PengeluaranLain::class,
                'table' => 'pengeluaran_lains',
                'date_field' => 'date_expense',
                'amount_field' => 'amount',
                'credit' => false,
                'source_type' => 'other_expense',
            ],
        ];

        foreach ($sourceTables as $tableConfig) {
            if (! class_exists($tableConfig['model'])) {
                continue;
            }

            $query = $tableConfig['model']::where('payment_method_id', $paymentMethodId);

            if ($startDate) {
                $query->where($tableConfig['date_field'], '>=', $startDate);
            }
            if ($endDate) {
                $query->where($tableConfig['date_field'], '<=', $endDate);
            }

            $records = $query->get();

            foreach ($records as $record) {
                $unifiedTx = new UnifiedTransaction([
                    'payment_method_id' => $record->payment_method_id,
                    'transaction_date' => Carbon::parse($record->{$tableConfig['date_field']}),
                    'description' => $record->keterangan ?? $record->note ?? $record->name ?? 'Transaction',
                    'debit_amount' => $tableConfig['credit'] ? 0 : ($record->{$tableConfig['amount_field']} ?? 0),
                    'credit_amount' => $tableConfig['credit'] ? ($record->{$tableConfig['amount_field']} ?? 0) : 0,
                    'source_type' => $tableConfig['source_type'],
                    'source_id' => $record->id,
                    'source_table' => $tableConfig['table'],
                    'reconciliation_status' => $record->reconciliation_status ?? 'unmatched',
                    'matched_bank_item_id' => $record->matched_bank_item_id,
                    'match_confidence' => $record->match_confidence,
                ]);

                $allAppTransactions->push($unifiedTx);
            }
        }

        $appTransactions = $allAppTransactions;

        // Get bank items
        $bankItems = $this->getBankItemsForPaymentMethod($paymentMethodId, $startDate, $endDate);

        // Separate matched and unmatched app transactions
        $matchedAppTxs = $appTransactions->filter(function ($tx) {
            return $tx->reconciliation_status === 'matched' && ! empty($tx->matched_bank_item_id);
        });

        $unmatchedAppTxs = $appTransactions->filter(function ($tx) {
            return $tx->reconciliation_status !== 'matched';
        });

        // Get matched bank item IDs
        $matchedBankItemIds = $matchedAppTxs->pluck('matched_bank_item_id')->filter()->unique()->values();

        // Separate matched and unmatched bank items
        $matchedBankItems = $bankItems->whereIn('id', $matchedBankItemIds);
        $unmatchedBankItems = $bankItems->whereNotIn('id', $matchedBankItemIds);

        // Build matches array
        $matches = [];
        foreach ($matchedAppTxs as $appTx) {
            $bankItem = $matchedBankItems->where('id', $appTx->matched_bank_item_id)->first();
            if ($bankItem) {
                $matches[] = [
                    'app_transaction' => $appTx,
                    'bank_item' => $bankItem,
                    'confidence' => $appTx->match_confidence ?? 100,
                    'match_type' => 'stored',
                    'match_criteria' => ['database'],
                ];
            }
        }

        return [
            'matched' => collect($matches),
            'unmatched_app' => $unmatchedAppTxs,
            'unmatched_bank' => $unmatchedBankItems,
            'disputed' => collect([]),
            'statistics' => [
                'total_app_transactions' => $appTransactions->count(),
                'total_bank_items' => $bankItems->count(),
                'matched_count' => count($matches),
                'unmatched_app_count' => $unmatchedAppTxs->count(),
                'unmatched_bank_count' => $unmatchedBankItems->count(),
                'disputed_count' => 0,
                'match_percentage' => $appTransactions->count() > 0
                    ? round((count($matches) / $appTransactions->count()) * 100, 1)
                    : 0,
                'avg_confidence' => count($matches) > 0 ? collect($matches)->avg('confidence') : 0,
                'total_app_debit' => $appTransactions->sum('debit_amount'),
                'total_app_credit' => $appTransactions->sum('credit_amount'),
                'total_bank_debit' => $bankItems->sum('debit'),
                'total_bank_credit' => $bankItems->sum('credit'),
            ],
        ];
    }

    /**
     * Get unique key for app transaction
     */
    private function getTransactionKey($appTx): string
    {
        return $appTx->source_table.'_'.$appTx->source_id;
    }

    /**
     * Mark transactions as matched in database
     */
    public function markAsMatched($appTransaction, $bankItem, float $confidence, array $criteria): void
    {
        // Update the source transaction record
        $model = $this->getSourceModel($appTransaction);
        if ($model) {
            $model->update([
                'reconciliation_status' => 'matched',
                'matched_bank_item_id' => $bankItem->id,
                'match_confidence' => $confidence,
                'reconciliation_notes' => 'Auto-matched: '.implode(', ', $criteria),
            ]);
        }
    }

    /**
     * Get source model instance for app transaction
     */
    private function getSourceModel($appTransaction)
    {
        switch ($appTransaction->source_table) {
            case 'data_pembayarans':
                return DataPembayaran::find($appTransaction->source_id);
            case 'pendapatan_lains':
                return PendapatanLain::find($appTransaction->source_id);
            case 'expenses':
                return Expense::find($appTransaction->source_id);
            case 'expense_ops':
                return ExpenseOps::find($appTransaction->source_id);
            case 'pengeluaran_lains':
                return PengeluaranLain::find($appTransaction->source_id);
            default:
                return null;
        }
    }
}
