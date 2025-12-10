<?php

namespace Database\Seeders;

use App\Models\BankReconciliationItem;
use App\Models\BankStatement;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BankReconciliationItemSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Bank Reconciliation Items...');

        $statements = BankStatement::all();
        if ($statements->isEmpty()) {
            $this->command->error('No BankStatement found. Please run BankStatementSeeder first.');
            return;
        }

        foreach ($statements as $statement) {
            $rows = [
                ['date' => '2025-01-05', 'desc' => 'Match Wedding Payment - Client A', 'debit' => 0, 'credit' => 5000000],
                ['date' => '2025-01-08', 'desc' => 'Match Vendor Payment - Dekorasi', 'debit' => 2000000, 'credit' => 0],
            ];

            $rowNumber = 1;
            foreach ($rows as $row) {
                $date = Carbon::parse($row['date']);

                $exists = BankReconciliationItem::where('bank_reconciliation_id', $statement->id)
                    ->whereDate('date', $date->toDateString())
                    ->where('description', $row['desc'])
                    ->first();

                if ($exists) {
                    continue;
                }

                BankReconciliationItem::create([
                    'bank_reconciliation_id' => $statement->id,
                    'date' => $date,
                    'description' => $row['desc'],
                    'debit' => $row['debit'],
                    'credit' => $row['credit'],
                    'row_number' => $rowNumber,
                ]);

                $rowNumber++;
            }
        }

        $this->command->info('Bank reconciliation items seeded.');
    }
}

