<?php

namespace App\Imports;

use App\Models\BankStatement;
use App\Models\BankTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;

class BankStatementImport implements ToCollection, WithValidation
{
    use Importable;

    protected $bankStatement;

    protected $importedCount = 0;

    protected $skippedCount = 0;

    protected $errors = [];

    protected $headerRow = 11; // Bank Mandiri header is on row 11

    protected $dataStartRow = 12; // Data starts from row 12

    public function __construct(BankStatement $bankStatement)
    {
        $this->bankStatement = $bankStatement;
    }

    /**
     * Detect bank format from file content
     */
    private function detectBankFormat(Collection $rows): string
    {
        $firstFewRows = $rows->take(15)->map(function ($row) {
            return $row->implode(' ');
        })->implode(' ');

        if (stripos($firstFewRows, 'Balance History') !== false) {
            return 'mandiri_balance_history';
        } elseif (stripos($firstFewRows, 'Transaction History') !== false) {
            return 'mandiri_transaction_history';
        } elseif (stripos($firstFewRows, 'BCA') !== false) {
            return 'bca';
        } elseif (stripos($firstFewRows, 'BNI') !== false) {
            return 'bni';
        }

        return 'generic';
    }

    /**
     * Process the imported collection
     */
    public function collection(Collection $rows)
    {
        // Detect bank format
        $bankFormat = $this->detectBankFormat($rows);

        if ($bankFormat === 'mandiri_balance_history') {
            $this->processMandiriBalanceHistory($rows);
        } elseif ($bankFormat === 'mandiri_transaction_history') {
            $this->processMandiriTransactionHistory($rows);
        } else {
            $this->processGenericFormat($rows);
        }
    }

    /**
     * Process Bank Mandiri Balance History format
     */
    private function processMandiriBalanceHistory(Collection $rows)
    {
        $headers = null;
        $rowIndex = 0;

        foreach ($rows as $row) {
            $rowIndex++;

            // Skip rows before header
            if ($rowIndex < $this->headerRow) {
                continue;
            }

            // Extract headers from row 11
            if ($rowIndex == $this->headerRow) {
                $headers = $row->toArray();

                continue;
            }

            // Skip empty rows or rows before data starts
            if ($rowIndex < $this->dataStartRow || $this->isEmptyRow($row)) {
                continue;
            }

            try {
                $this->processBankMandiriRow($row, $headers, $rowIndex);
                $this->importedCount++;
            } catch (Exception $e) {
                $this->skippedCount++;
                $this->errors[] = "Row {$rowIndex}: ".$e->getMessage();
                // Log the error for debugging
                Log::warning('Bank Import Row Error', [
                    'row' => $rowIndex,
                    'error' => $e->getMessage(),
                    'data' => $row->toArray(),
                ]);
            }
        }
    }

    /**
     * Process Bank Mandiri Transaction History format
     * This format contains actual transaction details with debit/credit amounts
     */
    private function processMandiriTransactionHistory(Collection $rows)
    {
        $headers = null;
        $rowIndex = 0;

        foreach ($rows as $row) {
            $rowIndex++;

            // Find the header row (usually contains "Transaction Date", "Description", etc.)
            if (! $headers && $this->containsTransactionHeaders($row)) {
                $headers = $row->toArray();

                continue;
            }

            // Skip if headers not found yet or empty rows
            if (! $headers || $this->isEmptyRow($row)) {
                continue;
            }

            try {
                $this->processMandiriTransactionRow($row, $headers, $rowIndex);
                $this->importedCount++;
            } catch (Exception $e) {
                $this->skippedCount++;
                $this->errors[] = "Row {$rowIndex}: ".$e->getMessage();
                Log::warning('Bank Transaction Import Row Error', [
                    'row' => $rowIndex,
                    'error' => $e->getMessage(),
                    'data' => $row->toArray(),
                ]);
            }
        }
    }

    /**
     * Check if row contains transaction history headers
     */
    private function containsTransactionHeaders(Collection $row): bool
    {
        $rowText = $row->implode(' ');
        $requiredHeaders = ['date', 'description', 'amount', 'debit', 'credit'];

        $foundHeaders = 0;
        foreach ($requiredHeaders as $header) {
            if (stripos($rowText, $header) !== false) {
                $foundHeaders++;
            }
        }

        return $foundHeaders >= 3; // At least 3 headers found
    }

    /**
     * Process Bank Mandiri Transaction History row
     */
    private function processMandiriTransactionRow(Collection $row, array $headers, int $rowIndex)
    {
        // Map row data to associative array using headers
        $data = [];
        foreach ($headers as $index => $header) {
            $data[strtolower(trim($header))] = $row->get($index, '');
        }

        // Parse transaction date
        $transactionDate = $this->parseTransactionDate($data);
        if (! $transactionDate) {
            throw new Exception('Invalid transaction date');
        }

        // Parse description
        $description = $this->parseDescription($data);
        if (empty($description)) {
            throw new Exception('Missing transaction description');
        }

        // Parse amounts (debit/credit)
        $amounts = $this->parseTransactionAmounts($data);

        // Skip zero amount transactions unless it's a balance record
        if ($amounts['debit'] == 0 && $amounts['credit'] == 0 && ! $this->isBalanceRecord($description)) {
            throw new Exception('Zero amount non-balance transaction');
        }

        // Create bank transaction
        BankTransaction::create([
            'bank_statement_id' => $this->bankStatement->id,
            'transaction_date' => $transactionDate,
            'value_date' => $transactionDate, // Same as transaction date for now
            'description' => $description,
            'reference_number' => $this->parseReferenceNumber($data),
            'debit_amount' => $amounts['debit'],
            'credit_amount' => $amounts['credit'],
            'balance' => $amounts['balance'],
            'transaction_type' => $amounts['debit'] > 0 ? 'debit' : 'credit',
            'category' => $this->categorizeTransaction($description, $amounts['debit'], $amounts['credit']),
            'is_matched' => false,
            'notes' => "Transaction History Record - Row {$rowIndex}",
        ]);
    }

    /**
     * Parse transaction date from various possible column names
     */
    private function parseTransactionDate(array $data): ?Carbon
    {
        $dateFields = ['transaction date', 'date', 'tanggal', 'tgl transaksi'];

        foreach ($dateFields as $field) {
            if (isset($data[$field]) && ! empty($data[$field])) {
                try {
                    return Carbon::parse($data[$field]);
                } catch (Exception $e) {
                    continue;
                }
            }
        }

        return null;
    }

    /**
     * Parse transaction description
     */
    private function parseDescription(array $data): string
    {
        $descFields = ['description', 'keterangan', 'desc', 'remarks'];

        foreach ($descFields as $field) {
            if (isset($data[$field]) && ! empty(trim($data[$field]))) {
                return trim($data[$field]);
            }
        }

        return '';
    }

    /**
     * Parse reference number
     */
    private function parseReferenceNumber(array $data): string
    {
        $refFields = ['reference', 'ref number', 'no referensi', 'ref'];

        foreach ($refFields as $field) {
            if (isset($data[$field]) && ! empty(trim($data[$field]))) {
                return trim($data[$field]);
            }
        }

        return '';
    }

    /**
     * Parse transaction amounts (debit/credit/balance)
     */
    private function parseTransactionAmounts(array $data): array
    {
        $debit = 0;
        $credit = 0;
        $balance = 0;

        // Try to find debit amount
        $debitFields = ['debit', 'debit amount', 'keluar', 'pengeluaran'];
        foreach ($debitFields as $field) {
            if (isset($data[$field]) && ! empty($data[$field])) {
                $debit = $this->parseAmount($data[$field]);
                break;
            }
        }

        // Try to find credit amount
        $creditFields = ['credit', 'credit amount', 'masuk', 'penerimaan'];
        foreach ($creditFields as $field) {
            if (isset($data[$field]) && ! empty($data[$field])) {
                $credit = $this->parseAmount($data[$field]);
                break;
            }
        }

        // Try to find balance
        $balanceFields = ['balance', 'saldo', 'current balance'];
        foreach ($balanceFields as $field) {
            if (isset($data[$field]) && ! empty($data[$field])) {
                $balance = $this->parseAmount($data[$field]);
                break;
            }
        }

        return [
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $balance,
        ];
    }

    /**
     * Check if this is a balance record (not an actual transaction)
     */
    private function isBalanceRecord(string $description): bool
    {
        $balanceKeywords = ['saldo akhir', 'balance', 'opening balance', 'closing balance'];

        foreach ($balanceKeywords as $keyword) {
            if (stripos($description, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Process generic bank format (original implementation)
     */
    private function processGenericFormat(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                $this->processRow($row);
                $this->importedCount++;
            } catch (Exception $e) {
                $this->skippedCount++;
                $this->errors[] = "Row {$row->keys()->first()}: ".$e->getMessage();
            }
        }
    }

    /**
     * Check if row is empty
     */
    private function isEmptyRow(Collection $row): bool
    {
        return $row->filter(function ($value) {
            return ! empty(trim($value));
        })->isEmpty();
    }

    /**
     * Process Bank Mandiri specific row format
     */
    private function processBankMandiriRow(Collection $row, array $headers, int $rowIndex)
    {
        // Map row data to associative array using headers
        $data = [];
        foreach ($headers as $index => $header) {
            $data[strtolower(trim($header))] = $row->get($index, '');
        }

        // Extract data for Bank Mandiri format
        // Based on the image: Date, Account Type, Currency, Account, Available Balance, Hold Amount, Current Balance, etc.

        $transactionDate = $this->parseBankMandiriDate($data['date'] ?? '');
        if (! $transactionDate) {
            throw new Exception("Invalid date format in row {$rowIndex}");
        }

        // For Bank Mandiri Balance History, we need to calculate transaction amounts
        // by comparing consecutive balances
        $currentBalance = $this->parseAmount($data['current balance'] ?? $data['available balance'] ?? 0);

        // Since this is balance history (not transaction detail), we create synthetic transactions
        // representing daily balance changes
        $this->createBankMandiriTransaction($transactionDate, $currentBalance, $data, $rowIndex);
    }

    /**
     * Create transaction record for Bank Mandiri format
     */
    private function createBankMandiriTransaction($transactionDate, $currentBalance, array $data, int $rowIndex)
    {
        // For balance history, we'll create a balance record
        // The actual transaction amount will be calculated later by comparing with previous day

        $description = 'Saldo Akhir Hari - '.
                      ($data['account type'] ?? 'Current Account').' '.
                      ($data['account'] ?? '');

        // Since we don't have individual transaction details, create a summary record
        BankTransaction::create([
            'bank_statement_id' => $this->bankStatement->id,
            'transaction_date' => $transactionDate,
            'value_date' => $transactionDate,
            'description' => $description,
            'reference_number' => 'BAL-'.str_replace('-', '', $transactionDate),
            'debit_amount' => 0, // Will be calculated later
            'credit_amount' => 0, // Will be calculated later
            'balance' => $currentBalance,
            'transaction_type' => 'credit', // Default
            'category' => 'other',
            'is_matched' => false,
            'notes' => 'Balance History Record - Row '.$rowIndex,
        ]);
    }

    /**
     * Parse Bank Mandiri date format
     */
    private function parseBankMandiriDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        // Bank Mandiri format is typically "DD MMM YYYY" like "30 Sep 2025"
        try {
            return Carbon::createFromFormat('d M Y', $dateString)->format('Y-m-d');
        } catch (Exception $e) {
            // Try other formats
            return $this->parseDate($dateString);
        }
    }

    /**
     * Process individual row (legacy method for other bank formats)
     */
    private function processRow(Collection $row)
    {
        // Parse transaction date with multiple possible formats
        $transactionDate = $this->parseDate($row['tanggal'] ?? $row['date'] ?? $row['transaction_date']);

        // Parse amounts - handle different column names and formats
        $debitAmount = $this->parseAmount($row['debit'] ?? $row['debet'] ?? $row['keluar'] ?? 0);
        $creditAmount = $this->parseAmount($row['credit'] ?? $row['kredit'] ?? $row['masuk'] ?? 0);

        // Determine transaction type
        $transactionType = $debitAmount > 0 ? 'debit' : 'credit';

        // Clean and prepare description
        $description = $this->cleanDescription($row['keterangan'] ?? $row['description'] ?? $row['desc'] ?? '');

        // Parse balance if available
        $balance = $this->parseAmount($row['saldo'] ?? $row['balance'] ?? null);

        // Extract reference number
        $referenceNumber = $this->extractReference($row['no_ref'] ?? $row['reference'] ?? $description);

        // Auto-categorize transaction
        $category = $this->categorizeTransaction($description, $transactionType);

        // Create bank transaction
        BankTransaction::create([
            'bank_statement_id' => $this->bankStatement->id,
            'transaction_date' => $transactionDate,
            'value_date' => $this->parseDate($row['tanggal_valuta'] ?? $row['value_date'] ?? $transactionDate),
            'description' => $description,
            'reference_number' => $referenceNumber,
            'debit_amount' => $debitAmount,
            'credit_amount' => $creditAmount,
            'balance' => $balance,
            'transaction_type' => $transactionType,
            'category' => $category,
            'is_matched' => false,
        ]);
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return now()->format('Y-m-d');
        }

        // Handle Excel date number format
        if (is_numeric($dateString)) {
            return Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($dateString - 2)->format('Y-m-d');
        }

        // Try various date formats common in Indonesian banks
        $formats = [
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
            'd.m.Y',
            'm/d/Y',
            'Y/m/d',
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $dateString)->format('Y-m-d');
            } catch (Exception $e) {
                continue;
            }
        }

        throw new Exception("Unable to parse date: {$dateString}");
    }

    /**
     * Parse amount from various formats
     */
    private function parseAmount($amountString)
    {
        if (empty($amountString) || $amountString === '-') {
            return 0;
        }

        // Remove currency symbols and separators
        $cleaned = preg_replace('/[^\d,.-]/', '', $amountString);

        // Handle different decimal separators
        if (substr_count($cleaned, ',') === 1 && substr_count($cleaned, '.') === 0) {
            // European format: 1.234,56
            $cleaned = str_replace(['.', ','], ['', '.'], $cleaned);
        } else {
            // US format: 1,234.56 or just remove commas
            $cleaned = str_replace(',', '', $cleaned);
        }

        return (float) $cleaned;
    }

    /**
     * Clean and normalize description
     */
    private function cleanDescription($description)
    {
        // Remove extra spaces and normalize
        $description = trim(preg_replace('/\s+/', ' ', $description));

        // Remove common bank prefixes
        $prefixes = ['TRF ', 'TRSF ', 'TRANSFER ', 'ATM ', 'DEBET ', 'KREDIT '];
        foreach ($prefixes as $prefix) {
            if (Str::startsWith(strtoupper($description), $prefix)) {
                $description = trim(substr($description, strlen($prefix)));
                break;
            }
        }

        return $description;
    }

    /**
     * Extract reference number from description or dedicated field
     */
    private function extractReference($input)
    {
        if (empty($input)) {
            return null;
        }

        // Look for patterns like: REF:123456, NO:123456, #123456
        if (preg_match('/(REF|NO|#):?\s*([A-Z0-9]+)/i', $input, $matches)) {
            return $matches[2];
        }

        // If it looks like a reference (alphanumeric, 6+ chars)
        if (preg_match('/^[A-Z0-9]{6,}$/i', trim($input))) {
            return trim($input);
        }

        return null;
    }

    /**
     * Auto-categorize transaction based on description and type
     */
    private function categorizeTransaction($description, $type)
    {
        $description = strtoupper($description);

        // Transfer patterns
        if (preg_match('/(TRANSFER|TRF|TRSF|KIRIM|TERIMA)/i', $description)) {
            return 'transfer';
        }

        // Fee patterns
        if (preg_match('/(BIAYA|FEE|ADMIN|CHARGE|DENDA)/i', $description)) {
            return 'fee';
        }

        // Interest patterns
        if (preg_match('/(BUNGA|INTEREST|JASA GIRO)/i', $description)) {
            return 'interest';
        }

        // ATM patterns
        if (preg_match('/(ATM|TARIK TUNAI|CASH|WITHDRAWAL)/i', $description)) {
            return 'withdrawal';
        }

        // Deposit patterns
        if (preg_match('/(SETOR|DEPOSIT|SETORAN)/i', $description)) {
            return 'deposit';
        }

        // Correction patterns
        if (preg_match('/(KOREKSI|CORRECTION|REVERSAL|BATAL)/i', $description)) {
            return 'correction';
        }

        return 'other';
    }

    /**
     * Validation rules - flexible for different bank formats
     */
    public function rules(): array
    {
        return [
            // No strict validation as we handle different bank formats
            // Validation is done in processing methods
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'required_without_all' => 'Kolom :attribute diperlukan jika kolom lainnya tidak ada.',
        ];
    }

    /**
     * Get import statistics
     */
    public function getImportStats(): array
    {
        return [
            'imported' => $this->importedCount,
            'skipped' => $this->skippedCount,
            'errors' => $this->errors,
            'total_processed' => $this->importedCount + $this->skippedCount,
        ];
    }

    /**
     * Calculate actual transactions from balance history (Bank Mandiri format)
     */
    public function calculateTransactionAmounts(): void
    {
        $transactions = BankTransaction::where('bank_statement_id', $this->bankStatement->id)
            ->orderBy('transaction_date')
            ->get();

        if ($transactions->count() < 2) {
            return;
        }

        $previousBalance = null;

        foreach ($transactions as $index => $transaction) {
            if ($previousBalance !== null) {
                $balanceChange = $transaction->balance - $previousBalance;

                if ($balanceChange > 0) {
                    // Credit transaction (money in)
                    $transaction->update([
                        'credit_amount' => $balanceChange,
                        'debit_amount' => 0,
                        'transaction_type' => 'credit',
                        'description' => 'Pemasukan - '.number_format($balanceChange, 0, ',', '.'),
                    ]);
                } elseif ($balanceChange < 0) {
                    // Debit transaction (money out)
                    $transaction->update([
                        'debit_amount' => abs($balanceChange),
                        'credit_amount' => 0,
                        'transaction_type' => 'debit',
                        'description' => 'Pengeluaran - '.number_format(abs($balanceChange), 0, ',', '.'),
                    ]);
                } else {
                    // No change
                    $transaction->update([
                        'description' => 'Tidak ada perubahan saldo',
                        'category' => 'other',
                    ]);
                }
            }

            $previousBalance = $transaction->balance;
        }
    }

    /**
     * Update bank statement with calculated statistics
     */
    public function updateBankStatementStatistics(): void
    {
        // Get all transactions for this bank statement
        $transactions = BankTransaction::where('bank_statement_id', $this->bankStatement->id)->get();

        if ($transactions->isEmpty()) {
            return;
        }

        // Calculate statistics
        $debits = $transactions->where('debit_amount', '>', 0);
        $credits = $transactions->where('credit_amount', '>', 0);

        $no_of_debit = $debits->count();
        $tot_debit = $debits->sum('debit_amount');
        $no_of_credit = $credits->count();
        $tot_credit = $credits->sum('credit_amount');

        // Get opening and closing balance from first and last transaction
        $sortedTransactions = $transactions->sortBy('transaction_date');
        $firstTransaction = $sortedTransactions->first();
        $lastTransaction = $sortedTransactions->last();

        // Calculate opening and closing balance
        $opening_balance = 0;
        $closing_balance = 0;

        if ($firstTransaction->balance !== null) {
            // Calculate opening balance (first transaction balance - net amount of first transaction)
            $opening_balance = $firstTransaction->balance - ($firstTransaction->credit_amount - $firstTransaction->debit_amount);
            $closing_balance = $lastTransaction->balance ?? 0;
        } else {
            // If no balance data, calculate from running total
            $running_balance = 0;
            foreach ($sortedTransactions as $transaction) {
                $running_balance += ($transaction->credit_amount - $transaction->debit_amount);
            }
            $closing_balance = $running_balance;
        }

        // Update bank statement
        $this->bankStatement->update([
            'opening_balance' => $opening_balance,
            'closing_balance' => $closing_balance,
            'no_of_debit' => $no_of_debit,
            'tot_debit' => $tot_debit,
            'no_of_credit' => $no_of_credit,
            'tot_credit' => $tot_credit,
            'processed_at' => now(),
        ]);
    }
}
