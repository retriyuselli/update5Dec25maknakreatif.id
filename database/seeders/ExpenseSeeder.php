<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\NotaDinas;
use App\Models\NotaDinasDetail;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get required data
        $orders = Order::all();
        $vendors = Vendor::all();
        $paymentMethods = PaymentMethod::all();

        if ($orders->isEmpty()) {
            $this->command->error('No orders found. Please run OrderSeeder first.');

            return;
        }

        if ($vendors->isEmpty()) {
            $this->command->error('No vendors found. Please run VendorSeeder first.');

            return;
        }

        if ($paymentMethods->isEmpty()) {
            $this->command->error('No payment methods found. Please run PaymentMethodSeeder first.');

            return;
        }

        // Check NotaDinasDetail data using raw query
        $notaDinasDetails = DB::table('nota_dinas_details')->get();

        if ($notaDinasDetails->isEmpty()) {
            $this->command->error('No NotaDinasDetail found. Please run NotaDinasDetailSeeder first.');

            return;
        }

        $this->command->info('Creating Wedding Expense records from NotaDinasDetail...');
        $this->command->info("Found {$notaDinasDetails->count()} NotaDinasDetail records");

        $created = 0;
        $skipped = 0;

        foreach ($notaDinasDetails as $detail) {
            // Check if expense already exists for this NotaDinasDetail
            $existingExpense = Expense::where('nota_dinas_detail_id', $detail->id)->first();

            if ($existingExpense) {
                $skipped++;

                continue;
            }

            // Get NotaDinas data
            $notaDinas = DB::table('nota_dinas')->where('id', $detail->nota_dinas_id)->first();

            if (! $notaDinas) {
                $this->command->warn("NotaDinas not found for detail ID: {$detail->id}");

                continue;
            }

            // Create expense based on NotaDinasDetail
            $expenseData = [
                'order_id' => $orders->random()->id,
                'vendor_id' => $detail->vendor_id ?? $vendors->random()->id,
                'payment_method_id' => $paymentMethods->random()->id,
                'nota_dinas_id' => $detail->nota_dinas_id,
                'nota_dinas_detail_id' => $detail->id,
                'note' => $detail->keperluan.' - '.$detail->event,
                'date_expense' => $detail->created_at ? Carbon::parse($detail->created_at) : Carbon::now()->subDays(rand(1, 90)),
                'amount' => $detail->jumlah_transfer,
                'no_nd' => $notaDinas->no_nd,
                'kategori_transaksi' => 'uang_keluar',
                'payment_stage' => $detail->payment_stage,
                'account_holder' => $detail->account_holder,
                'bank_name' => $detail->bank_name,
                'bank_account' => $detail->bank_account,
                'image' => $detail->invoice_file,
            ];

            try {
                $expense = Expense::create($expenseData);
                $created++;

                $this->command->info("✅ Created Expense #{$created}: {$detail->keperluan} - Rp ".number_format($detail->jumlah_transfer, 0, ',', '.'));

            } catch (\Exception $e) {
                $this->command->warn("⚠️ Failed to create expense for detail ID {$detail->id}: {$e->getMessage()}");
                $skipped++;
            }
        }

        $this->command->info('🎉 Expense seeder completed successfully!');
        $this->command->info("📊 Created {$created} new wedding expense records");

        if ($skipped > 0) {
            $this->command->info("⏭️ Skipped {$skipped} records (already exist or failed)");
        }

        // Show summary statistics
        $totalExpenses = Expense::count();
        $totalAmount = Expense::sum('amount');
        $avgAmount = Expense::avg('amount');

        $this->command->table(
            ['Metric', 'Value'],
            [
                ['Total Expenses', number_format($totalExpenses)],
                ['New Expenses Created', number_format($created)],
                ['Skipped Records', number_format($skipped)],
                ['Total Amount (All)', 'Rp '.number_format($totalAmount, 0, ',', '.')],
                ['Average Amount', 'Rp '.number_format($avgAmount, 0, ',', '.')],
            ]
        );

        // Show breakdown by jenis_pengeluaran
        $jenisBreakdown = DB::table('expenses')
            ->join('nota_dinas_details', 'expenses.nota_dinas_detail_id', '=', 'nota_dinas_details.id')
            ->select('nota_dinas_details.jenis_pengeluaran',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(expenses.amount) as total'))
            ->whereNotNull('expenses.nota_dinas_detail_id')
            ->groupBy('nota_dinas_details.jenis_pengeluaran')
            ->get();

        if ($jenisBreakdown->count() > 0) {
            $this->command->info("\n📈 Breakdown by Jenis Pengeluaran:");
            $jenisData = [];
            foreach ($jenisBreakdown as $jenis) {
                $jenisData[] = [
                    ucfirst($jenis->jenis_pengeluaran),
                    $jenis->count,
                    'Rp '.number_format($jenis->total, 0, ',', '.'),
                ];
            }

            $this->command->table(
                ['Jenis Pengeluaran', 'Count', 'Total Amount'],
                $jenisData
            );
        }

        // Show breakdown by payment stage
        $stageBreakdown = Expense::selectRaw('payment_stage, COUNT(*) as count, SUM(amount) as total')
            ->whereNotNull('payment_stage')
            ->groupBy('payment_stage')
            ->get();

        if ($stageBreakdown->count() > 0) {
            $this->command->info("\n💰 Breakdown by Payment Stage:");
            $stageData = [];
            foreach ($stageBreakdown as $stage) {
                $stageLabel = Expense::getPaymentStageLabel($stage->payment_stage);
                $stageData[] = [
                    $stageLabel,
                    $stage->count,
                    'Rp '.number_format($stage->total, 0, ',', '.'),
                ];
            }

            $this->command->table(
                ['Payment Stage', 'Count', 'Total Amount'],
                $stageData
            );
        }

        // Show recent NotaDinasDetail-based expenses
        $recentExpenses = Expense::with(['vendor'])
            ->whereNotNull('nota_dinas_detail_id')
            ->latest()
            ->take(5)
            ->get();

        if ($recentExpenses->count() > 0) {
            $this->command->info("\n🔗 Recent NotaDinasDetail-based Expenses:");
            foreach ($recentExpenses as $expense) {
                $vendor = $expense->vendor ? $expense->vendor->name : 'No Vendor';
                $this->command->line("- {$expense->note} (Rp ".number_format($expense->amount, 0, ',', '.').") - {$vendor}");
            }
        }
    }
}
