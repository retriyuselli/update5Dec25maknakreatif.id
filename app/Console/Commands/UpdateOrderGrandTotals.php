<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class UpdateOrderGrandTotals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:update-grand-totals {--force : Force update all records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update grand_total column for all existing orders based on formula: total_price + penambahan - promo - pengurangan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Updating grand_total for all orders...');

        $query = Order::query();

        // If not forced, only update records where grand_total is null or 0
        if (! $this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('grand_total')->orWhere('grand_total', 0);
            });
        }

        $orders = $query->get();
        $total = $orders->count();

        if ($total === 0) {
            $this->info('✅ No orders need updating.');

            return;
        }

        $this->info("Found {$total} orders to update.");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updated = 0;

        foreach ($orders as $order) {
            $oldGrandTotal = $order->grand_total;

            // Calculate grand_total using the same formula as in the model
            $newGrandTotal = $order->total_price + $order->penambahan - $order->promo - $order->pengurangan;

            // Update without triggering model events to avoid recursion
            $order->timestamps = false;
            $order->update(['grand_total' => $newGrandTotal]);
            $order->timestamps = true;

            $updated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("✅ Successfully updated grand_total for {$updated} orders.");

        // Show some statistics
        $this->newLine();
        $this->info('📊 Grand Total Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Orders Updated', number_format($updated)],
                ['Average Grand Total', 'IDR '.number_format(Order::avg('grand_total'), 0)],
                ['Total Grand Total Sum', 'IDR '.number_format(Order::sum('grand_total'), 0)],
                ['Highest Grand Total', 'IDR '.number_format(Order::max('grand_total'), 0)],
                ['Lowest Grand Total', 'IDR '.number_format(Order::min('grand_total'), 0)],
            ]
        );

        return Command::SUCCESS;
    }
}
