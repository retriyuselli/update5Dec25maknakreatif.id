<?php

namespace Database\Seeders;

use App\Models\BankStatement;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BankStatementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get payment methods that are not cash (bank accounts only)
        $bankAccounts = PaymentMethod::where('is_cash', false)->get();

        if ($bankAccounts->isEmpty()) {
            $this->command->warn('No bank accounts found. Please run PaymentMethodSeeder first.');

            return;
        }

        $bankStatements = [];

        foreach ($bankAccounts as $account) {
            // Create bank statements for different periods
            $statements = [
                [
                    'payment_method_id' => $account->id,
                    'period_start' => Carbon::parse('2024-01-01'),
                    'period_end' => Carbon::parse('2024-01-31'),
                    'branch' => $account->cabang,
                    'opening_balance' => $account->opening_balance,
                    'closing_balance' => $account->opening_balance + 15000000, // Simulate growth
                    'no_of_debit' => 8,
                    'tot_debit' => 5000000,
                    'no_of_credit' => 12,
                    'tot_credit' => 20000000,
                    'source_type' => 'pdf',
                    'status' => 'parsed',
                    'file_path' => 'bank-statements/sample-'.strtolower(str_replace(' ', '-', $account->name)).'-jan-2024.pdf',
                    'uploaded_at' => Carbon::parse('2024-02-01'),
                ],
                [
                    'payment_method_id' => $account->id,
                    'period_start' => Carbon::parse('2024-02-01'),
                    'period_end' => Carbon::parse('2024-02-29'),
                    'branch' => $account->cabang,
                    'opening_balance' => $account->opening_balance + 15000000,
                    'closing_balance' => $account->opening_balance + 22000000,
                    'no_of_debit' => 6,
                    'tot_debit' => 8000000,
                    'no_of_credit' => 15,
                    'tot_credit' => 15000000,
                    'source_type' => 'excel',
                    'status' => 'parsed',
                    'file_path' => 'bank-statements/sample-'.strtolower(str_replace(' ', '-', $account->name)).'-feb-2024.xlsx',
                    'uploaded_at' => Carbon::parse('2024-03-01'),
                ],
            ];

            $bankStatements = array_merge($bankStatements, $statements);
        }

        foreach ($bankStatements as $statementData) {
            BankStatement::create($statementData);
        }

        $this->command->info('✅ BankStatementSeeder completed! Created '.count($bankStatements).' bank statements.');
    }
}
