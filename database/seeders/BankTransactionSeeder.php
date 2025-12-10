<?php

namespace Database\Seeders;

use App\Models\BankStatement;
use App\Models\BankTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BankTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Bank Transactions...');

        $statements = BankStatement::all();
        if ($statements->isEmpty()) {
            $this->command->error('No BankStatement found. Please run BankStatementSeeder first.');
            return;
        }

        foreach ($statements as $statement) {
            $balance = (float) $statement->opening_balance;

            $rows = [
                ['date' => '2025-01-05', 'desc' => 'Wedding Payment - Client A', 'credit' => 5000000, 'debit' => 0],
                ['date' => '2025-01-08', 'desc' => 'Vendor Payment - Dekorasi', 'credit' => 0, 'debit' => 2000000],
                ['date' => '2025-01-12', 'desc' => 'Other Income - Workshop', 'credit' => 2500000, 'debit' => 0],
                ['date' => '2025-01-20', 'desc' => 'Operational Expense - Sewa Kantor', 'credit' => 0, 'debit' => 4500000],
            ];

            $rowNumber = 1;
            foreach ($rows as $row) {
                $balance += $row['credit'] - $row['debit'];
                $date = Carbon::parse($row['date']);

                $exists = BankTransaction::where('bank_statement_id', $statement->id)
                    ->whereDate('transaction_date', $date->toDateString())
                    ->where('description', $row['desc'])
                    ->first();

                if ($exists) {
                    continue;
                }

                BankTransaction::create([
                    'bank_statement_id' => $statement->id,
                    'transaction_date' => $date,
                    'value_date' => $date,
                    'description' => $row['desc'],
                    'reference_number' => 'REF-'.$statement->id.'-'.$rowNumber,
                    'debit_amount' => $row['debit'],
                    'credit_amount' => $row['credit'],
                    'balance' => $balance,
                    'transaction_type' => $row['credit'] > 0 ? 'credit' : 'debit',
                    'category' => $row['credit'] > 0 ? 'deposit' : 'withdrawal',
                    'is_matched' => false,
                    'matched_with_transaction_id' => null,
                    'matching_confidence' => 0,
                    'notes' => null,
                ]);

                $rowNumber++;
            }
        }

        $this->command->info('Bank transactions seeded.');
    }
}
