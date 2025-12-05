<?php

namespace App\Imports;

use App\Models\BankReconciliationItem;
use App\Models\BankStatement;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BankReconciliationImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;

    protected $bankReconciliation;

    protected $importedCount = 0;

    protected $errors = [];

    public function __construct($bankReconciliation)
    {
        $this->bankReconciliation = $bankReconciliation;
    }

    public function collection(Collection $collection)
    {
        $totalDebit = 0;
        $totalCredit = 0;
        $rowNumber = 1;

        foreach ($collection as $row) {
            try {
                // Skip empty rows
                if (empty(array_filter($row->toArray()))) {
                    continue;
                }

                // Get date field - support multiple column names
                $dateValue = $row['tanggal'] ?? $row['date'] ?? null;
                if (empty($dateValue)) {
                    $this->errors[] = "Row {$rowNumber}: Date field is required (tanggal/date column not found or empty)";
                    $rowNumber++;

                    continue;
                }

                // Get description field - support multiple column names
                $description = $row['keterangan'] ?? $row['description'] ?? '';
                if (empty($description)) {
                    $this->errors[] = "Row {$rowNumber}: Description field is required (keterangan/description column not found or empty)";
                    $rowNumber++;

                    continue;
                }

                // Parse date
                $date = $this->parseDate($dateValue);

                // Parse amounts
                $debit = $this->parseAmount($row['debit'] ?? 0);
                $credit = $this->parseAmount($row['credit'] ?? $row['kredit'] ?? 0);

                // Create item
                BankReconciliationItem::create([
                    'bank_reconciliation_id' => $this->bankReconciliation->id,
                    'date' => $date,
                    'description' => $description,
                    'debit' => $debit,
                    'credit' => $credit,
                    'row_number' => $rowNumber,
                ]);

                $totalDebit += $debit;
                $totalCredit += $credit;
                $this->importedCount++;

            } catch (Exception $e) {
                $this->errors[] = "Row {$rowNumber}: ".$e->getMessage();
            }

            $rowNumber++;
        }

        // Update bank reconciliation totals - works for both BankReconciliation and BankStatement
        $updateData = [
            'total_records' => $this->importedCount,
            'processed_at' => now(),
        ];

        // Check if this is a BankStatement or BankReconciliation model
        if ($this->bankReconciliation instanceof BankStatement) {
            $updateData['total_debit_reconciliation'] = $totalDebit;
            $updateData['total_credit_reconciliation'] = $totalCredit;
            $updateData['reconciliation_status'] = empty($this->errors) ? 'completed' : 'failed';
        } else {
            $updateData['total_debit'] = $totalDebit;
            $updateData['total_credit'] = $totalCredit;
            $updateData['status'] = empty($this->errors) ? 'completed' : 'failed';
        }

        $this->bankReconciliation->update($updateData);
    }

    protected function parseDate($date)
    {
        if (empty($date)) {
            throw new Exception('Date is required');
        }

        try {
            // Handle Excel date serial number
            if (is_numeric($date)) {
                return Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($date - 2);
            }

            // Handle various date formats
            $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'];

            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $date);
                } catch (Exception $e) {
                    continue;
                }
            }

            return Carbon::parse($date);
        } catch (Exception $e) {
            throw new Exception("Invalid date format: {$date}");
        }
    }

    protected function parseAmount($amount)
    {
        if (empty($amount)) {
            return 0;
        }

        // Remove currency symbols and formatting
        $cleaned = preg_replace('/[^\d,.-]/', '', $amount);
        $cleaned = str_replace(',', '', $cleaned);

        return (float) $cleaned;
    }

    public function rules(): array
    {
        return [
            // Make validation more flexible - we'll handle validation in the collection method
        ];
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
