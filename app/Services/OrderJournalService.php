<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\DataPembayaran;
use App\Models\Expense;
use App\Models\ExpenseOps;
use App\Models\JournalBatch;
use App\Models\JournalEntry;
use App\Models\Order;
use App\Models\PendapatanLain;
use App\Models\PengeluaranLain;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderJournalService
{
    /**
     * Generate journal entries for Order revenue recognition
     * Called when Order status changes to 'closed' or specific triggers
     */
    public function generateRevenueRecognitionJournal(Order $order): ?JournalBatch
    {
        try {
            // Only generate if not already generated for this order
            $existingBatch = JournalBatch::where('reference_type', 'order_revenue')
                ->where('reference_id', $order->id)
                ->first();

            if ($existingBatch) {
                Log::info("Revenue recognition journal already exists for Order {$order->id}");

                return $existingBatch;
            }

            // Get required accounts
            $accountsReceivable = ChartOfAccount::where('account_code', '1300')->first(); // Piutang Usaha
            $weddingRevenue = ChartOfAccount::where('account_code', '4100')->first(); // Pendapatan Jasa Wedding

            if (! $accountsReceivable || ! $weddingRevenue) {
                Log::error('Required accounts not found for Order revenue recognition');

                return null;
            }

            // Calculate revenue amount (grand_total)
            $revenueAmount = $order->grand_total;

            if ($revenueAmount <= 0) {
                Log::warning("Order {$order->id} has zero or negative grand_total: {$revenueAmount}");

                return null;
            }

            return DB::transaction(function () use ($order, $accountsReceivable, $weddingRevenue, $revenueAmount) {
                // Create journal batch
                $batch = JournalBatch::create([
                    'batch_number' => 'WED-'.$order->id,
                    'transaction_date' => $order->closing_date ?? now(),
                    'description' => "Revenue Recognition - Wedding Project: {$order->name}",
                    'total_debit' => $revenueAmount,
                    'total_credit' => $revenueAmount,
                    'status' => 'posted',
                    'reference_type' => 'order_revenue',
                    'reference_id' => $order->id,
                    'created_by' => Auth::id() ?? 1,
                ]);

                // Create journal entries
                $entries = [
                    // Debit: Accounts Receivable
                    [
                        'journal_batch_id' => $batch->id,
                        'account_id' => $accountsReceivable->id,
                        'transaction_date' => $order->closing_date ?? now(),
                        'description' => "Piutang Wedding Project - {$order->name}",
                        'debit_amount' => $revenueAmount,
                        'credit_amount' => 0,
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'created_by' => Auth::id() ?? 1,
                    ],
                    // Credit: Wedding Revenue
                    [
                        'journal_batch_id' => $batch->id,
                        'account_id' => $weddingRevenue->id,
                        'transaction_date' => $order->closing_date ?? now(),
                        'description' => "Pendapatan Wedding Project - {$order->name}",
                        'debit_amount' => 0,
                        'credit_amount' => $revenueAmount,
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'created_by' => Auth::id() ?? 1,
                    ],
                ];

                foreach ($entries as $entryData) {
                    JournalEntry::create($entryData);
                }

                Log::info("Revenue recognition journal created for Order {$order->id}, Amount: {$revenueAmount}");

                return $batch;
            });

        } catch (Exception $e) {
            Log::error("Failed to generate revenue recognition journal for Order {$order->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Generate journal entries for payment received
     * Called when DataPembayaran is created
     */
    public function generatePaymentJournal(DataPembayaran $payment): ?JournalBatch
    {
        try {
            // Prevent duplicate journal generation (exclude reversed journals)
            $existingBatch = JournalBatch::where('reference_type', 'payment')
                ->where('reference_id', $payment->id)
                ->first();

            if ($existingBatch) {
                // Check if this batch has been reversed
                $hasReversal = JournalBatch::where('reference_type', 'payment_reversal')
                    ->where('reference_id', $payment->id)
                    ->exists();

                if (! $hasReversal) {
                    Log::info("Payment journal already exists for Payment {$payment->id}");

                    return $existingBatch;
                }
                // If reversed, we can create a new journal entry
                Log::info("Previous payment journal was reversed, creating new one for Payment {$payment->id}");
            }

            $order = $payment->order;
            if (! $order) {
                Log::error("Payment {$payment->id} has no associated order");

                return null;
            }

            // Get accounts - automatically select based on payment method
            $cashAccount = $this->getCashAccountByPaymentMethod($payment->payment_method_id);
            Log::info("Auto-selected Chart of Account {$cashAccount->account_code} based on payment method for Payment {$payment->id}");

            $accountsReceivable = ChartOfAccount::where('account_code', '1300')->first(); // Piutang Usaha

            if (! $cashAccount || ! $accountsReceivable) {
                Log::error('Required accounts not found for payment journal');

                return null;
            }

            $paymentAmount = $payment->nominal;

            if ($paymentAmount <= 0) {
                Log::warning("Payment {$payment->id} has zero or negative amount: {$paymentAmount}");

                return null;
            }

            return DB::transaction(function () use ($payment, $order, $cashAccount, $accountsReceivable, $paymentAmount) {
                // Create journal batch with unique number (handle updates)
                $baseNumber = 'PAY-'.$payment->id;
                $batchNumber = $baseNumber;

                // If batch already exists, append short suffix
                if (JournalBatch::where('batch_number', $baseNumber)->exists()) {
                    $suffix = now()->format('His'); // Only time HHMMSS (6 chars)
                    $batchNumber = 'PAY'.$payment->id.'-'.$suffix; // Remove dash to save space
                }

                $batch = JournalBatch::create([
                    'batch_number' => $batchNumber,
                    'transaction_date' => $payment->tgl_bayar ?? now(),
                    'description' => "Payment Received - Wedding Project: {$order->name}",
                    'total_debit' => $paymentAmount,
                    'total_credit' => $paymentAmount,
                    'status' => 'posted',
                    'reference_type' => 'payment',
                    'reference_id' => $payment->id,
                    'created_by' => Auth::id() ?? 1,
                ]);

                // Create journal entries
                $entries = [
                    // Debit: Cash/Bank Account
                    [
                        'journal_batch_id' => $batch->id,
                        'account_id' => $cashAccount->id,
                        'transaction_date' => $payment->tgl_bayar ?? now(),
                        'description' => "Pembayaran Diterima - {$order->name} ({$payment->keterangan})",
                        'debit_amount' => $paymentAmount,
                        'credit_amount' => 0,
                        'reference_type' => 'payment',
                        'reference_id' => $payment->id,
                        'created_by' => Auth::id() ?? 1,
                    ],
                    // Credit: Accounts Receivable
                    [
                        'journal_batch_id' => $batch->id,
                        'account_id' => $accountsReceivable->id,
                        'transaction_date' => $payment->tgl_bayar ?? now(),
                        'description' => "Penerimaan Piutang - {$order->name}",
                        'debit_amount' => 0,
                        'credit_amount' => $paymentAmount,
                        'reference_type' => 'payment',
                        'reference_id' => $payment->id,
                        'created_by' => Auth::id() ?? 1,
                    ],
                ];

                foreach ($entries as $entryData) {
                    JournalEntry::create($entryData);
                }

                Log::info("Payment journal created for Payment {$payment->id}, Amount: {$paymentAmount}");

                return $batch;
            });

        } catch (Exception $e) {
            Log::error("Failed to generate payment journal for Payment {$payment->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Generate journal entries for project expenses
     * Called when Expense is created for an Order
     */
    public function generateExpenseJournal(Expense $expense): ?JournalBatch
    {
        try {
            // Add debug logging
            Log::info("Generating expense journal for Expense {$expense->id}");

            // Prevent duplicate journal generation (check both active and soft deleted)
            $existingBatch = JournalBatch::withTrashed()
                ->where('reference_type', 'expense')
                ->where('reference_id', $expense->id)
                ->first();

            if ($existingBatch) {
                if ($existingBatch->trashed()) {
                    // Restore soft deleted journal
                    $existingBatch->restore();
                    Log::info("Restored soft deleted expense journal for Expense {$expense->id}");
                } else {
                    Log::info("Expense journal already exists for Expense {$expense->id}");
                }

                return $existingBatch;
            }

            $order = $expense->order;
            if (! $order) {
                Log::error("Expense {$expense->id} has no associated order");

                return null;
            }

            // Get required accounts
            $expenseAccount = ChartOfAccount::where('account_code', '5100')->first(); // Biaya Proyek Wedding
            $cashAccount = $this->getCashAccountByPaymentMethod($expense->payment_method_id);

            if (! $expenseAccount) {
                Log::error('Expense account (5100) not found for expense journal');

                return null;
            }

            if (! $cashAccount) {
                Log::error("Cash account not found for payment method {$expense->payment_method_id} for expense journal");

                return null;
            }

            $expenseAmount = $expense->amount;

            if ($expenseAmount <= 0) {
                Log::warning("Expense {$expense->id} has zero or negative amount: {$expenseAmount}");

                return null;
            }

            return DB::transaction(function () use ($expense, $order, $expenseAccount, $cashAccount, $expenseAmount) {
                // Determine logical transaction date
                // Use the earlier of: expense date or order closing date (to prevent future dates)
                $transactionDate = $expense->date_expense ?? now();
                if ($order->closing_date && $transactionDate > $order->closing_date) {
                    $transactionDate = $order->closing_date;
                    Log::info("Adjusted expense transaction date from {$expense->date_expense} to {$transactionDate} for Order {$order->id}");
                }

                // Also ensure it's not in the future
                if ($transactionDate > now()) {
                    $transactionDate = now();
                    Log::info("Adjusted future expense date to today for Expense {$expense->id}");
                }

                // Create journal batch
                $batch = JournalBatch::create([
                    'batch_number' => 'EXP-'.$expense->id,
                    'transaction_date' => $transactionDate,
                    'description' => "Project Expense - Wedding Project: {$order->name}",
                    'total_debit' => $expenseAmount,
                    'total_credit' => $expenseAmount,
                    'status' => 'posted',
                    'reference_type' => 'expense',
                    'reference_id' => $expense->id,
                    'created_by' => Auth::id() ?? 1,
                ]);

                // Create journal entries
                $entries = [
                    // Debit: Wedding Project Costs
                    [
                        'journal_batch_id' => $batch->id,
                        'account_id' => $expenseAccount->id,
                        'transaction_date' => $transactionDate,
                        'description' => "Biaya Proyek Wedding - {$order->name} ({$expense->note})",
                        'debit_amount' => $expenseAmount,
                        'credit_amount' => 0,
                        'reference_type' => 'expense',
                        'reference_id' => $expense->id,
                        'created_by' => Auth::id() ?? 1,
                    ],
                    // Credit: Cash/Bank Account
                    [
                        'journal_batch_id' => $batch->id,
                        'account_id' => $cashAccount->id,
                        'transaction_date' => $transactionDate,
                        'description' => "Pembayaran Biaya Proyek - {$order->name}",
                        'debit_amount' => 0,
                        'credit_amount' => $expenseAmount,
                        'reference_type' => 'expense',
                        'reference_id' => $expense->id,
                        'created_by' => Auth::id() ?? 1,
                    ],
                ];

                foreach ($entries as $entryData) {
                    JournalEntry::create($entryData);
                }

                Log::info("Expense journal created for Expense {$expense->id}, Amount: {$expenseAmount}");

                return $batch;
            });

        } catch (Exception $e) {
            Log::error("Failed to generate expense journal for Expense {$expense->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Generate adjustment journal for Order modifications
     * Called when Order amounts are modified (promo, penambahan, pengurangan)
     */
    public function generateOrderAdjustmentJournal(Order $order, array $changes): ?JournalBatch
    {
        try {
            $adjustmentAmount = 0;
            $adjustmentDescription = "Order Adjustment - {$order->name}: ";

            // Calculate net adjustment amount
            if (isset($changes['promo'])) {
                $adjustmentAmount -= $changes['promo']; // Promo reduces revenue
                $adjustmentDescription .= "Promo {$changes['promo']}, ";
            }

            if (isset($changes['penambahan'])) {
                $adjustmentAmount += $changes['penambahan']; // Addition increases revenue
                $adjustmentDescription .= "Penambahan {$changes['penambahan']}, ";
            }

            if (isset($changes['pengurangan'])) {
                $adjustmentAmount -= $changes['pengurangan']; // Reduction decreases revenue
                $adjustmentDescription .= "Pengurangan {$changes['pengurangan']}, ";
            }

            if ($adjustmentAmount == 0) {
                Log::info("No adjustment needed for Order {$order->id}");

                return null;
            }

            // Get required accounts
            $accountsReceivable = ChartOfAccount::where('account_code', '1300')->first(); // Piutang Usaha
            $weddingRevenue = ChartOfAccount::where('account_code', '4100')->first(); // Pendapatan Jasa Wedding

            if (! $accountsReceivable || ! $weddingRevenue) {
                Log::error('Required accounts not found for order adjustment');

                return null;
            }

            return DB::transaction(function () use ($order, $accountsReceivable, $weddingRevenue, $adjustmentAmount, $adjustmentDescription) {
                // Create journal batch
                $batch = JournalBatch::create([
                    'batch_number' => 'ADJ-'.$order->id,
                    'transaction_date' => now(),
                    'description' => trim($adjustmentDescription, ', '),
                    'total_debit' => abs($adjustmentAmount),
                    'total_credit' => abs($adjustmentAmount),
                    'status' => 'posted',
                    'reference_type' => 'order_adjustment',
                    'reference_id' => $order->id,
                    'created_by' => Auth::id() ?? 1,
                ]);

                // Create journal entries based on adjustment direction
                if ($adjustmentAmount > 0) {
                    // Positive adjustment - increase revenue
                    $entries = [
                        [
                            'journal_batch_id' => $batch->id,
                            'account_id' => $accountsReceivable->id,
                            'transaction_date' => now(),
                            'description' => "Penyesuaian Piutang - {$order->name}",
                            'debit_amount' => $adjustmentAmount,
                            'credit_amount' => 0,
                            'reference_type' => 'order_adjustment',
                            'reference_id' => $order->id,
                            'created_by' => Auth::id() ?? 1,
                        ],
                        [
                            'journal_batch_id' => $batch->id,
                            'account_id' => $weddingRevenue->id,
                            'transaction_date' => now(),
                            'description' => "Penyesuaian Pendapatan - {$order->name}",
                            'debit_amount' => 0,
                            'credit_amount' => $adjustmentAmount,
                            'reference_type' => 'order_adjustment',
                            'reference_id' => $order->id,
                            'created_by' => Auth::id() ?? 1,
                        ],
                    ];
                } else {
                    // Negative adjustment - decrease revenue
                    $absAmount = abs($adjustmentAmount);
                    $entries = [
                        [
                            'journal_batch_id' => $batch->id,
                            'account_id' => $weddingRevenue->id,
                            'transaction_date' => now(),
                            'description' => "Penyesuaian Pendapatan - {$order->name}",
                            'debit_amount' => $absAmount,
                            'credit_amount' => 0,
                            'reference_type' => 'order_adjustment',
                            'reference_id' => $order->id,
                            'created_by' => Auth::id() ?? 1,
                        ],
                        [
                            'journal_batch_id' => $batch->id,
                            'account_id' => $accountsReceivable->id,
                            'transaction_date' => now(),
                            'description' => "Penyesuaian Piutang - {$order->name}",
                            'debit_amount' => 0,
                            'credit_amount' => $absAmount,
                            'reference_type' => 'order_adjustment',
                            'reference_id' => $order->id,
                            'created_by' => Auth::id() ?? 1,
                        ],
                    ];
                }

                foreach ($entries as $entryData) {
                    JournalEntry::create($entryData);
                }

                Log::info("Order adjustment journal created for Order {$order->id}, Amount: {$adjustmentAmount}");

                return $batch;
            });

        } catch (Exception $e) {
            Log::error("Failed to generate order adjustment journal for Order {$order->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Get appropriate cash account based on payment method
     */
    private function getCashAccountByPaymentMethod(?int $paymentMethodId): ?ChartOfAccount
    {
        // Default to cash if no payment method specified
        if (! $paymentMethodId) {
            return ChartOfAccount::where('account_code', '1100')->first(); // Kas
        }

        // Map payment methods to accounts (customize based on your payment methods)
        $paymentMethodMapping = [
            1 => '1100', // Cash -> Kas
            2 => '1200', // Bank Transfer -> Bank
            3 => '1200', // Credit Card -> Bank
            4 => '1200', // Debit Card -> Bank
            // Add more mappings as needed
        ];

        $accountCode = $paymentMethodMapping[$paymentMethodId] ?? '1100';

        return ChartOfAccount::where('account_code', $accountCode)->first();
    }

    /**
     * Reverse journal entries (for deletions or major corrections)
     */
    public function reverseJournal(string $referenceType, int $referenceId, string $reason = 'Correction'): bool
    {
        try {
            $originalBatch = JournalBatch::where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->first();

            if (! $originalBatch) {
                Log::warning("No journal batch found to reverse for {$referenceType} ID {$referenceId}");

                return false;
            }

            return DB::transaction(function () use ($originalBatch, $reason) {
                // Create reversal batch with unique number
                $timestamp = now()->format('His'); // Short time format
                $baseBatchNumber = str_replace('PAY-', '', $originalBatch->batch_number);
                $reversalBatch = JournalBatch::create([
                    'batch_number' => 'REV'.$baseBatchNumber.'-'.$timestamp,
                    'transaction_date' => now(),
                    'description' => "REVERSAL: {$originalBatch->description} - Reason: {$reason}",
                    'total_debit' => $originalBatch->total_debit,
                    'total_credit' => $originalBatch->total_credit,
                    'status' => 'posted',
                    'reference_type' => $originalBatch->reference_type.'_reversal',
                    'reference_id' => $originalBatch->reference_id,
                    'created_by' => Auth::id() ?? 1,
                ]);

                // Create reversed journal entries
                foreach ($originalBatch->journalEntries as $originalEntry) {
                    JournalEntry::create([
                        'journal_batch_id' => $reversalBatch->id,
                        'account_id' => $originalEntry->account_id,
                        'transaction_date' => now(),
                        'description' => "REVERSAL: {$originalEntry->description}",
                        'debit_amount' => $originalEntry->credit_amount, // Swap debit and credit
                        'credit_amount' => $originalEntry->debit_amount,
                        'reference_type' => $originalEntry->reference_type.'_reversal',
                        'reference_id' => $originalEntry->reference_id,
                        'created_by' => Auth::id() ?? 1,
                    ]);
                }

                Log::info("Journal reversed for {$originalBatch->reference_type} ID {$originalBatch->reference_id}");

                return true;
            });

        } catch (Exception $e) {
            Log::error("Failed to reverse journal for {$referenceType} ID {$referenceId}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Generate journal entries for Other Income (Pendapatan Lain)
     * Called when PendapatanLain is created
     */
    public function generateOtherIncomeJournal(PendapatanLain $otherIncome): ?JournalBatch
    {
        try {
            // Prevent duplicate journal generation
            $existingBatch = JournalBatch::where('reference_type', 'other_income')
                ->where('reference_id', $otherIncome->id)
                ->first();

            if ($existingBatch) {
                Log::info("Other income journal already exists for PendapatanLain {$otherIncome->id}");

                return $existingBatch;
            }

            // Get accounts based on payment method
            $cashAccount = $this->getCashAccountByPaymentMethod($otherIncome->payment_method_id);
            $otherIncomeAccount = $otherIncome->incomeAccount ?? ChartOfAccount::where('account_code', '8000')->first(); // Pendapatan Lain

            if (! $cashAccount || ! $otherIncomeAccount) {
                Log::error('Required accounts not found for other income journal');

                return null;
            }

            $incomeAmount = $otherIncome->nominal;

            if ($incomeAmount <= 0) {
                Log::warning("Other income {$otherIncome->id} has zero or negative amount: {$incomeAmount}");

                return null;
            }

            return DB::transaction(function () use ($otherIncome, $cashAccount, $otherIncomeAccount, $incomeAmount) {
                // Create journal batch
                $batch = JournalBatch::create([
                    'batch_number' => 'OTH-'.$otherIncome->id,
                    'transaction_date' => $otherIncome->tgl_bayar ?? now(),
                    'description' => "Pendapatan Lain - {$otherIncome->name}",
                    'total_debit' => $incomeAmount,
                    'total_credit' => $incomeAmount,
                    'status' => 'posted',
                    'reference_type' => 'other_income',
                    'reference_id' => $otherIncome->id,
                    'created_by' => Auth::id() ?? 1,
                ]);

                // Create journal entries
                $entries = [
                    // Debit: Cash/Bank Account
                    [
                        'journal_batch_id' => $batch->id,
                        'account_id' => $cashAccount->id,
                        'transaction_date' => $otherIncome->tgl_bayar ?? now(),
                        'description' => "Penerimaan Pendapatan Lain - {$otherIncome->name}",
                        'debit_amount' => $incomeAmount,
                        'credit_amount' => 0,
                        'reference_type' => 'other_income',
                        'reference_id' => $otherIncome->id,
                        'created_by' => Auth::id() ?? 1,
                    ],
                    // Credit: Other Income Account
                    [
                        'journal_batch_id' => $batch->id,
                        'account_id' => $otherIncomeAccount->id,
                        'transaction_date' => $otherIncome->tgl_bayar ?? now(),
                        'description' => "Pendapatan Lain - {$otherIncome->name}",
                        'debit_amount' => 0,
                        'credit_amount' => $incomeAmount,
                        'reference_type' => 'other_income',
                        'reference_id' => $otherIncome->id,
                        'created_by' => Auth::id() ?? 1,
                    ],
                ];

                foreach ($entries as $entryData) {
                    JournalEntry::create($entryData);
                }

                Log::info("Other income journal created for PendapatanLain {$otherIncome->id}, Amount: {$incomeAmount}");

                return $batch;
            });

        } catch (Exception $e) {
            Log::error("Failed to generate other income journal for PendapatanLain {$otherIncome->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Generate journal entries for Operational Expense (ExpenseOps)
     * Called when ExpenseOps is created
     */
    public function generateOperationalExpenseJournal(ExpenseOps $expenseOps): ?JournalBatch
    {
        try {
            // Prevent duplicate journal generation
            $existingBatch = JournalBatch::where('reference_type', 'expense_ops')
                ->where('reference_id', $expenseOps->id)
                ->first();

            if ($existingBatch) {
                Log::info("Operational expense journal already exists for ExpenseOps {$expenseOps->id}");

                return $existingBatch;
            }

            // Get accounts based on payment method and expense type
            $cashAccount = $this->getCashAccountByPaymentMethod($expenseOps->payment_method_id);
            $expenseAccount = $expenseOps->expenseAccount ?? ChartOfAccount::where('account_code', '6000')->first(); // Beban Operasional

            if (! $cashAccount || ! $expenseAccount) {
                Log::error('Required accounts not found for operational expense journal');

                return null;
            }

            $expenseAmount = $expenseOps->amount;

            if ($expenseAmount <= 0) {
                Log::warning("ExpenseOps {$expenseOps->id} has zero or negative amount: {$expenseAmount}");

                return null;
            }

            return DB::transaction(function () use ($expenseOps, $cashAccount, $expenseAccount, $expenseAmount) {
                // Create journal batch
                $batch = JournalBatch::create([
                    'batch_number' => 'EXP-OPS-'.$expenseOps->id,
                    'transaction_date' => $expenseOps->date_expense ?? now(),
                    'description' => "Beban Operasional - {$expenseOps->name}",
                    'total_debit' => $expenseAmount,
                    'total_credit' => $expenseAmount,
                    'status' => 'posted',
                    'reference_type' => 'expense_ops',
                    'reference_id' => $expenseOps->id,
                    'created_by' => Auth::id() ?? 1,
                ]);

                // Create journal entries
                $entries = [
                    // Debit: Operational Expense Account
                    [
                        'journal_batch_id' => $batch->id,
                        'account_id' => $expenseAccount->id,
                        'transaction_date' => $expenseOps->date_expense ?? now(),
                        'description' => "Beban Operasional - {$expenseOps->name}",
                        'debit_amount' => $expenseAmount,
                        'credit_amount' => 0,
                        'reference_type' => 'expense_ops',
                        'reference_id' => $expenseOps->id,
                        'created_by' => Auth::id() ?? 1,
                    ],
                    // Credit: Cash/Bank Account
                    [
                        'journal_batch_id' => $batch->id,
                        'account_id' => $cashAccount->id,
                        'transaction_date' => $expenseOps->date_expense ?? now(),
                        'description' => "Pembayaran Beban Operasional - {$expenseOps->name}",
                        'debit_amount' => 0,
                        'credit_amount' => $expenseAmount,
                        'reference_type' => 'expense_ops',
                        'reference_id' => $expenseOps->id,
                        'created_by' => Auth::id() ?? 1,
                    ],
                ];

                foreach ($entries as $entryData) {
                    JournalEntry::create($entryData);
                }

                Log::info("Operational expense journal created for ExpenseOps {$expenseOps->id}, Amount: {$expenseAmount}");

                return $batch;
            });

        } catch (Exception $e) {
            Log::error("Failed to generate operational expense journal for ExpenseOps {$expenseOps->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Generate journal entries for Other Expense (PengeluaranLain)
     * Called when PengeluaranLain is created
     */
    public function generateOtherExpenseJournal(PengeluaranLain $otherExpense): ?JournalBatch
    {
        try {
            // Prevent duplicate journal generation
            $existingBatch = JournalBatch::where('reference_type', 'other_expense')
                ->where('reference_id', $otherExpense->id)
                ->first();

            if ($existingBatch) {
                Log::info("Other expense journal already exists for PengeluaranLain {$otherExpense->id}");

                return $existingBatch;
            }

            // Get accounts based on payment method and expense type
            $cashAccount = $this->getCashAccountByPaymentMethod($otherExpense->payment_method_id);
            $expenseAccount = $otherExpense->expenseAccount ?? ChartOfAccount::where('account_code', '9000')->first(); // Beban Lain

            if (! $cashAccount || ! $expenseAccount) {
                Log::error('Required accounts not found for other expense journal');

                return null;
            }

            $expenseAmount = $otherExpense->amount;

            if ($expenseAmount <= 0) {
                Log::warning("PengeluaranLain {$otherExpense->id} has zero or negative amount: {$expenseAmount}");

                return null;
            }

            return DB::transaction(function () use ($otherExpense, $cashAccount, $expenseAccount, $expenseAmount) {
                // Create journal batch
                $batch = JournalBatch::create([
                    'batch_number' => 'EXP-OTH-'.$otherExpense->id,
                    'transaction_date' => $otherExpense->date_expense ?? now(),
                    'description' => "Beban Lain - {$otherExpense->name}",
                    'total_debit' => $expenseAmount,
                    'total_credit' => $expenseAmount,
                    'status' => 'posted',
                    'reference_type' => 'other_expense',
                    'reference_id' => $otherExpense->id,
                    'created_by' => Auth::id() ?? 1,
                ]);

                // Create journal entries
                $entries = [
                    // Debit: Other Expense Account
                    [
                        'journal_batch_id' => $batch->id,
                        'account_id' => $expenseAccount->id,
                        'transaction_date' => $otherExpense->date_expense ?? now(),
                        'description' => "Beban Lain - {$otherExpense->name}",
                        'debit_amount' => $expenseAmount,
                        'credit_amount' => 0,
                        'reference_type' => 'other_expense',
                        'reference_id' => $otherExpense->id,
                        'created_by' => Auth::id() ?? 1,
                    ],
                    // Credit: Cash/Bank Account
                    [
                        'journal_batch_id' => $batch->id,
                        'account_id' => $cashAccount->id,
                        'transaction_date' => $otherExpense->date_expense ?? now(),
                        'description' => "Pembayaran Beban Lain - {$otherExpense->name}",
                        'debit_amount' => 0,
                        'credit_amount' => $expenseAmount,
                        'reference_type' => 'other_expense',
                        'reference_id' => $otherExpense->id,
                        'created_by' => Auth::id() ?? 1,
                    ],
                ];

                foreach ($entries as $entryData) {
                    JournalEntry::create($entryData);
                }

                Log::info("Other expense journal created for PengeluaranLain {$otherExpense->id}, Amount: {$expenseAmount}");

                return $batch;
            });

        } catch (Exception $e) {
            Log::error("Failed to generate other expense journal for PengeluaranLain {$otherExpense->id}: ".$e->getMessage());

            return null;
        }
    }
}
