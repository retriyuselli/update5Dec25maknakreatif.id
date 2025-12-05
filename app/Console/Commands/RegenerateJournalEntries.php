<?php

namespace App\Console\Commands;

use App\Models\DataPembayaran;
use App\Models\Expense;
use App\Models\JournalBatch;
use App\Models\JournalEntry;
use App\Models\Order;
use App\Services\OrderJournalService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegenerateJournalEntries extends Command
{
    protected $signature = 'journal:regenerate 
                            {--type=all : Type of journals to regenerate (expense|payment|revenue|all)}
                            {--order-id= : Specific order ID to regenerate}
                            {--dry-run : Show what would be changed without making changes}
                            {--force : Force regeneration even if journals exist}';

    protected $description = 'Regenerate journal entries with correct transaction dates';

    protected OrderJournalService $journalService;

    public function __construct(OrderJournalService $journalService)
    {
        parent::__construct();
        $this->journalService = $journalService;
    }

    public function handle()
    {
        $type = $this->option('type');
        $orderId = $this->option('order-id');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        } else {
            $this->info('🔄 REGENERATING journal entries...');

            // Skip confirmation if running in non-interactive mode or with force flag
            if (! $force && ! $this->option('no-interaction') && ! $this->confirm('This will regenerate journal entries. Continue?')) {
                $this->info('Operation cancelled.');

                return;
            }
        }

        $this->newLine();

        try {
            DB::beginTransaction();

            switch ($type) {
                case 'expense':
                    $this->regenerateExpenseJournals($orderId, $dryRun);
                    break;
                case 'payment':
                    $this->regeneratePaymentJournals($orderId, $dryRun);
                    break;
                case 'revenue':
                    $this->regenerateRevenueJournals($orderId, $dryRun);
                    break;
                case 'all':
                default:
                    $this->regenerateExpenseJournals($orderId, $dryRun);
                    $this->regeneratePaymentJournals($orderId, $dryRun);
                    $this->regenerateRevenueJournals($orderId, $dryRun);
                    break;
            }

            if (! $dryRun) {
                DB::commit();
                $this->info('✅ Journal entries regenerated successfully!');
            } else {
                DB::rollBack();
                $this->info('✅ Dry run completed! Use without --dry-run to apply changes');
            }

        } catch (Exception $e) {
            DB::rollBack();
            $this->error('❌ Error regenerating journals: '.$e->getMessage());
            Log::error('Journal regeneration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function regenerateExpenseJournals($orderId = null, $dryRun = false)
    {
        $this->info('💰 Regenerating expense journals...');

        $query = Expense::with(['order', 'journalBatches']);

        if ($orderId) {
            $query->where('order_id', $orderId);
        }

        $expenses = $query->get();
        $this->line("   Found {$expenses->count()} expenses to process");

        $processed = 0;
        $regenerated = 0;

        foreach ($expenses as $expense) {
            // Check if expense has existing journal (check both 'expense' and 'expense_reversal' types)
            $existingJournal = JournalBatch::where('reference_id', $expense->id)
                ->whereIn('reference_type', ['expense', 'expense_reversal'])
                ->first();

            if ($existingJournal) {
                $originalDate = $existingJournal->transaction_date;
                $newDate = $this->calculateCorrectExpenseDate($expense);

                if ($originalDate != $newDate || $this->option('force')) {
                    if ($dryRun) {
                        $this->line("   [DRY RUN] Would regenerate Expense {$expense->id} journal: {$originalDate} → {$newDate}");
                    } else {
                        // Delete existing journal entries
                        JournalEntry::where('journal_batch_id', $existingJournal->id)->delete();
                        $existingJournal->delete();

                        // Regenerate with correct date
                        $journal = $this->journalService->generateExpenseJournal($expense);
                        if ($journal) {
                            $this->line("   ✅ Regenerated Expense {$expense->id} journal: {$originalDate} → {$newDate}");
                        } else {
                            $this->error("   ❌ Failed to regenerate journal for Expense {$expense->id}");
                        }

                        Log::info('Regenerated expense journal', [
                            'expense_id' => $expense->id,
                            'original_date' => $originalDate,
                            'new_date' => $newDate,
                            'success' => $journal !== null,
                        ]);
                    }
                    $regenerated++;
                }
            } else {
                // Generate missing journal
                if ($dryRun) {
                    $this->line("   [DRY RUN] Would create missing journal for Expense {$expense->id}");
                } else {
                    $journal = $this->journalService->generateExpenseJournal($expense);
                    if ($journal) {
                        $this->line("   ✅ Created missing journal for Expense {$expense->id}");
                    } else {
                        $this->error("   ❌ Failed to create journal for Expense {$expense->id}");
                    }
                }
                $regenerated++;
            }

            $processed++;
            if ($processed % 100 === 0) {
                $this->line("   Processed {$processed}/{$expenses->count()} expenses...");
            }
        }

        $this->line("   Processed: {$processed}, Regenerated: {$regenerated}");
    }

    private function regeneratePaymentJournals($orderId = null, $dryRun = false)
    {
        $this->info('💳 Regenerating payment journals...');

        $query = DataPembayaran::with(['order', 'journalBatches']);

        if ($orderId) {
            $query->where('order_id', $orderId);
        }

        $payments = $query->get();
        $this->line("   Found {$payments->count()} payments to process");

        $processed = 0;
        $regenerated = 0;

        foreach ($payments as $payment) {
            $existingJournal = $payment->journalBatches()->first();

            if ($existingJournal) {
                if ($this->option('force')) {
                    if ($dryRun) {
                        $this->line("   [DRY RUN] Would regenerate Payment {$payment->id} journal");
                    } else {
                        // Delete existing journal entries
                        JournalEntry::where('journal_batch_id', $existingJournal->id)->delete();
                        $existingJournal->delete();

                        // Regenerate
                        $this->journalService->generatePaymentJournal($payment);
                        $this->line("   ✅ Regenerated Payment {$payment->id} journal");
                    }
                    $regenerated++;
                }
            } else {
                // Generate missing journal
                if ($dryRun) {
                    $this->line("   [DRY RUN] Would create missing journal for Payment {$payment->id}");
                } else {
                    $this->journalService->generatePaymentJournal($payment);
                    $this->line("   ✅ Created missing journal for Payment {$payment->id}");
                }
                $regenerated++;
            }

            $processed++;
        }

        $this->line("   Processed: {$processed}, Regenerated: {$regenerated}");
    }

    private function regenerateRevenueJournals($orderId = null, $dryRun = false)
    {
        $this->info('📈 Regenerating revenue journals...');

        $query = Order::with(['journalBatches']);

        if ($orderId) {
            $query->where('id', $orderId);
        } else {
            // 🚨 CRITICAL FIX: Only regenerate revenue journals for DONE orders
            $query->where('status', 'done');
        }

        $orders = $query->get();
        $this->line("   Found {$orders->count()} orders to process (status filter: ".($orderId ? 'specific order' : 'done only').')');

        $processed = 0;
        $regenerated = 0;
        $skipped = 0;

        foreach ($orders as $order) {
            // Double check: Only process DONE orders with grand_total > 0
            if (! $orderId && ($order->status?->value !== 'done' || $order->grand_total <= 0)) {
                $this->line("   ⏭️  Skipping Order {$order->id}: {$order->name} (Status: ".($order->status?->getLabel() ?? 'NULL').', Amount: '.number_format($order->grand_total, 0).')');
                $skipped++;

                continue;
            }

            $existingJournal = $order->journalBatches()
                ->where('reference_type', 'revenue')
                ->first();

            if ($existingJournal) {
                if ($this->option('force')) {
                    if ($dryRun) {
                        $this->line("   [DRY RUN] Would regenerate Order {$order->id} revenue journal");
                    } else {
                        // Delete existing journal entries
                        JournalEntry::where('journal_batch_id', $existingJournal->id)->delete();
                        $existingJournal->delete();

                        // Regenerate
                        $this->journalService->generateRevenueRecognitionJournal($order);
                        $this->line("   ✅ Regenerated Order {$order->id} revenue journal");
                    }
                    $regenerated++;
                }
            } else {
                // Generate missing journal
                if ($dryRun) {
                    $this->line("   [DRY RUN] Would create missing revenue journal for Order {$order->id}");
                } else {
                    $this->journalService->generateRevenueRecognitionJournal($order);
                    $this->line("   ✅ Created missing revenue journal for Order {$order->id}");
                }
                $regenerated++;
            }

            $processed++;
        }

        $this->line("   Processed: {$processed}, Regenerated: {$regenerated}, Skipped: {$skipped}");
    }

    private function calculateCorrectExpenseDate($expense)
    {
        $expenseDate = $expense->date_expense;
        $orderClosingDate = $expense->order?->closing_date;
        $today = now();

        // Use the earliest date among: expense date, order closing date, today
        $dates = array_filter([$expenseDate, $orderClosingDate, $today]);

        return min($dates)->format('Y-m-d');
    }
}
