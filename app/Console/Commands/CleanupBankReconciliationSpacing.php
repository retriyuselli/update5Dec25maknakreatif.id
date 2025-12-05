<?php

namespace App\Console\Commands;

use App\Models\BankReconciliationItem;
use Illuminate\Console\Command;

class CleanupBankReconciliationSpacing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:cleanup-spacing {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up excessive whitespace in bank reconciliation item descriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('Starting cleanup of excessive whitespace in bank reconciliation items...');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        $items = BankReconciliationItem::all();
        $updated = 0;
        $processed = 0;

        $progressBar = $this->output->createProgressBar($items->count());
        $progressBar->start();

        foreach ($items as $item) {
            $originalDescription = $item->description;

            // Clean excessive whitespace
            $cleanDescription = preg_replace('/\s+/', ' ', trim($originalDescription));

            if ($originalDescription !== $cleanDescription) {
                if (! $isDryRun) {
                    $item->description = $cleanDescription;
                    $item->save();
                }

                $updated++;

                if ($this->output->isVerbose()) {
                    $this->newLine();
                    $this->line("Item ID {$item->id}:");
                    $this->line('  Before: '.json_encode($originalDescription));
                    $this->line('  After:  '.json_encode($cleanDescription));
                }
            }

            $processed++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        if ($isDryRun) {
            $this->info('DRY RUN COMPLETED!');
            $this->info("Would update {$updated} out of {$processed} items.");
            $this->info('Run without --dry-run to apply changes.');
        } else {
            $this->info('CLEANUP COMPLETED!');
            $this->info("Updated {$updated} out of {$processed} items.");
        }

        return 0;
    }
}
