<?php

namespace App\Console\Commands;

use App\Models\AccountManagerTarget;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateAccountManagerTargets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'targets:generate 
                            {--update : Update existing targets with latest achieved amounts}
                            {--year= : Generate targets for specific year (default: current year)}
                            {--auto-12-months : Generate targets for all 12 months of the year}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Account Manager targets automatically from existing Orders, or for full 12 months';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎯 Generating Account Manager Targets...');

        // Check if auto-12-months option is used
        if ($this->option('auto-12-months')) {
            return $this->generateTwelveMonthsTargets();
        }

        // Get all Account Managers who have orders
        $accountManagers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Account Manager');
        })->whereHas('orders')->get();

        $this->info("Found {$accountManagers->count()} Account Managers with orders");

        // Get unique year-month combinations from orders
        $orderPeriods = Order::select(
            DB::raw('YEAR(closing_date) as year'),
            DB::raw('MONTH(closing_date) as month'),
            'user_id'
        )
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'Account Manager');
            })
            ->groupBy('year', 'month', 'user_id')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $this->info("Found {$orderPeriods->count()} unique periods with orders");

        $created = 0;
        $updated = 0;

        // Create progress bar
        $bar = $this->output->createProgressBar($orderPeriods->count());
        $bar->start();

        foreach ($orderPeriods as $period) {
            // Calculate achieved amount using grand_total column
            $achievedAmount = Order::where('user_id', $period->user_id)
                ->whereYear('closing_date', $period->year)
                ->whereMonth('closing_date', $period->month)
                ->sum('grand_total') ?? 0;

            // Create or update target
            $target = AccountManagerTarget::firstOrCreate([
                'user_id' => $period->user_id,
                'year' => $period->year,
                'month' => $period->month,
            ], [
                'target_amount' => 1000000000, // Default 1 billion
                'achieved_amount' => $achievedAmount,
                'status' => 'pending',
            ]);

            if ($target->wasRecentlyCreated) {
                $created++;
            } elseif ($this->option('update')) {
                $target->update(['achieved_amount' => $achievedAmount]);
                $updated++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Update all targets if --update option is used
        if ($this->option('update')) {
            $this->info('📊 Updating all targets with latest achieved amounts...');

            $allTargets = AccountManagerTarget::all();
            $updateBar = $this->output->createProgressBar($allTargets->count());
            $updateBar->start();

            foreach ($allTargets as $target) {
                $achievedAmount = Order::where('user_id', $target->user_id)
                    ->whereYear('closing_date', $target->year)
                    ->whereMonth('closing_date', $target->month)
                    ->sum('grand_total') ?? 0;

                $target->update(['achieved_amount' => $achievedAmount]);
                $updateBar->advance();
            }

            $updateBar->finish();
            $this->newLine();
        }

        // Summary
        $this->info('✅ Operation completed:');
        $this->line("   • Created: {$created} new targets");
        $this->line("   • Updated: {$updated} existing targets");
        $this->line('   • Total targets: '.AccountManagerTarget::count());

        // Show top performers
        $this->newLine();
        $this->info('🏆 Top 5 Account Managers (Latest Month):');

        $topPerformers = AccountManagerTarget::with('user')
            ->select('*')
            ->selectRaw('(achieved_amount / target_amount * 100) as percentage')
            ->orderBy(DB::raw('YEAR(created_at)'), 'desc')
            ->orderBy(DB::raw('MONTH(created_at)'), 'desc')
            ->orderBy('percentage', 'desc')
            ->limit(5)
            ->get();

        $headers = ['Account Manager', 'Year/Month', 'Target', 'Achieved', 'Percentage'];
        $rows = [];

        foreach ($topPerformers as $performer) {
            $rows[] = [
                $performer->user->name ?? 'N/A',
                $performer->year.'/'.$performer->month,
                'IDR '.number_format($performer->target_amount, 0, ',', '.'),
                'IDR '.number_format($performer->achieved_amount, 0, ',', '.'),
                number_format($performer->percentage, 2).'%',
            ];
        }

        $this->table($headers, $rows);

        $this->newLine();
        $this->info('💡 Tip: Use --update flag to refresh achieved amounts from latest orders');
        $this->info('Example: php artisan targets:generate --update');
        $this->info('💡 Use --auto-12-months to generate targets for all 12 months of current year');
        $this->info('Example: php artisan targets:generate --auto-12-months');

        return Command::SUCCESS;
    }

    /**
     * Generate targets for all 12 months of the year
     */
    private function generateTwelveMonthsTargets()
    {
        $year = $this->option('year') ?? date('Y');
        $this->info("📅 Generating targets for all 12 months of year {$year}...");

        // Get all Account Managers
        $accountManagers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Account Manager');
        })->get();

        if ($accountManagers->isEmpty()) {
            $this->warn('⚠️  No Account Managers found!');

            return Command::FAILURE;
        }

        $this->info("Found {$accountManagers->count()} Account Managers");

        $created = 0;
        $updated = 0;
        $totalTargets = $accountManagers->count() * 12; // 12 months per account manager

        // Create progress bar
        $bar = $this->output->createProgressBar($totalTargets);
        $bar->start();

        foreach ($accountManagers as $accountManager) {
            for ($month = 1; $month <= 12; $month++) {
                // Calculate achieved amount for this month
                $achievedAmount = Order::where('user_id', $accountManager->id)
                    ->whereYear('closing_date', $year)
                    ->whereMonth('closing_date', $month)
                    ->sum('grand_total') ?? 0;

                // Set different target amounts based on month (you can customize this)
                $targetAmount = $this->getMonthlyTargetAmount($month);

                // Create or update target with updateOrCreate to force update
                $target = AccountManagerTarget::updateOrCreate([
                    'user_id' => $accountManager->id,
                    'year' => $year,
                    'month' => $month,
                ], [
                    'target_amount' => $targetAmount,
                    'achieved_amount' => $achievedAmount,
                    'status' => $achievedAmount >= $targetAmount ? 'achieved' : 'pending',
                ]);

                if ($target->wasRecentlyCreated) {
                    $created++;
                } else {
                    // Update existing target
                    $target->update([
                        'achieved_amount' => $achievedAmount,
                        'status' => $achievedAmount >= $targetAmount ? 'achieved' : 'pending',
                    ]);
                    $updated++;
                }

                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info("✅ 12-Month Generation completed for year {$year}:");
        $this->line("   • Created: {$created} new targets");
        $this->line("   • Updated: {$updated} existing targets");
        $this->line("   • Total Account Managers: {$accountManagers->count()}");
        $this->line('   • Total targets: '.AccountManagerTarget::whereYear('created_at', $year)->count());

        // Show monthly summary for current year
        $this->newLine();
        $this->info("📊 Monthly Summary for {$year}:");

        $monthlySummary = AccountManagerTarget::where('year', $year)
            ->selectRaw('month, COUNT(*) as total_targets, SUM(achieved_amount) as total_achieved, SUM(target_amount) as total_target')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $headers = ['Month', 'Targets', 'Total Achieved', 'Total Target', 'Achievement %'];
        $rows = [];

        $monthNames = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];

        foreach ($monthlySummary as $summary) {
            $percentage = $summary->total_target > 0 ? ($summary->total_achieved / $summary->total_target * 100) : 0;
            $rows[] = [
                $monthNames[$summary->month],
                $summary->total_targets,
                'IDR '.number_format($summary->total_achieved, 0, ',', '.'),
                'IDR '.number_format($summary->total_target, 0, ',', '.'),
                number_format($percentage, 2).'%',
            ];
        }

        $this->table($headers, $rows);

        $this->newLine();
        $this->info('🎉 All Account Manager targets for 12 months have been generated successfully!');

        return Command::SUCCESS;
    }

    /**
     * Get target amount for specific month
     * Fixed target of 1 billion for all months
     */
    private function getMonthlyTargetAmount($month)
    {
        // Fixed target: 1 billion for all months
        return 1000000000; // 1 billion IDR
    }
}
