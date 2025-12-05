<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class UnifiedTransaction extends Model
{
    /**
     * Virtual model untuk menggabungkan semua transaksi PaymentMethod
     * Tidak memerlukan tabel fisik di database
     */
    protected $fillable = [
        'payment_method_id',
        'transaction_date',
        'description',
        'debit_amount',
        'credit_amount',
        'source_type',
        'source_id',
        'source_table',
        'reconciliation_status',
        'matched_bank_item_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    // Disable database table usage
    public $timestamps = false;

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get all unified transactions for a specific PaymentMethod
     */
    public static function getForPaymentMethod($paymentMethodId, $startDate = null, $endDate = null): Collection
    {
        $transactions = collect();

        // 1. DataPembayaran (Wedding Payments - Credit/Masuk)
        $weddingPayments = DataPembayaran::with(['order.prospect'])
            ->where('payment_method_id', $paymentMethodId)
            ->when($startDate, fn ($q) => $q->where('tgl_bayar', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('tgl_bayar', '<=', $endDate))
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($payment) {
                // Build description with customer name
                $description = $payment->keterangan ?? 'Wedding Payment';
                if ($payment->order && $payment->order->prospect) {
                    $customerName = $payment->order->prospect->name_event ?:
                                  ($payment->order->prospect->name_cpp ?: $payment->order->prospect->name_cpw);
                    if ($customerName) {
                        $description = "Payment - {$customerName}".($payment->keterangan ? " ({$payment->keterangan})" : '');
                    }
                }

                return new self([
                    'payment_method_id' => $payment->payment_method_id,
                    'transaction_date' => $payment->tgl_bayar,
                    'description' => $description,
                    'debit_amount' => 0,
                    'credit_amount' => $payment->nominal,
                    'source_type' => 'wedding_payment',
                    'source_id' => $payment->id,
                    'source_table' => 'data_pembayarans',
                    'reconciliation_status' => 'unmatched',
                ]);
            });

        // 2. PendapatanLain (Other Income - Credit/Masuk)
        $otherIncome = PendapatanLain::where('payment_method_id', $paymentMethodId)
            ->when($startDate, fn ($q) => $q->where('tgl_bayar', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('tgl_bayar', '<=', $endDate))
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($income) {
                return new self([
                    'payment_method_id' => $income->payment_method_id,
                    'transaction_date' => $income->tgl_bayar,
                    'description' => $income->keterangan ?? 'Other Income',
                    'debit_amount' => 0,
                    'credit_amount' => $income->nominal,
                    'source_type' => 'other_income',
                    'source_id' => $income->id,
                    'source_table' => 'pendapatan_lains',
                    'reconciliation_status' => 'unmatched',
                ]);
            });

        // 3. Expenses (Wedding Expenses - Debit/Keluar)
        $weddingExpenses = Expense::with(['order.prospect'])
            ->where('payment_method_id', $paymentMethodId)
            ->when($startDate, fn ($q) => $q->where('date_expense', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('date_expense', '<=', $endDate))
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($expense) {
                $description = $expense->note ?? 'Wedding Expense';
                if ($expense->order && $expense->order->prospect) {
                    $customerName = $expense->order->prospect->name_event ?:
                                   ($expense->order->prospect->name_cpp ?: $expense->order->prospect->name_cpw);
                    if ($customerName) {
                        $description = "Expense - {$customerName}".($expense->note ? " ({$expense->note})" : '');
                    }
                }

                return new self([
                    'payment_method_id' => $expense->payment_method_id,
                    'transaction_date' => $expense->date_expense,
                    'description' => $description,
                    'debit_amount' => $expense->amount,
                    'credit_amount' => 0,
                    'source_type' => 'wedding_expense',
                    'source_id' => $expense->id,
                    'source_table' => 'expenses',
                    'reconciliation_status' => 'unmatched',
                ]);
            });

        // 4. ExpenseOps (Operational Expenses - Debit/Keluar)
        $operationalExpenses = ExpenseOps::where('payment_method_id', $paymentMethodId)
            ->when($startDate, fn ($q) => $q->where('date_expense', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('date_expense', '<=', $endDate))
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($expense) {
                return new self([
                    'payment_method_id' => $expense->payment_method_id,
                    'transaction_date' => $expense->date_expense,
                    'description' => $expense->name ?? $expense->note ?? 'Operational Expense',
                    'debit_amount' => $expense->amount,
                    'credit_amount' => 0,
                    'source_type' => 'operational_expense',
                    'source_id' => $expense->id,
                    'source_table' => 'expense_ops',
                    'reconciliation_status' => 'unmatched',
                ]);
            });

        // 5. PengeluaranLain (Other Expenses - Debit/Keluar)
        $otherExpenses = PengeluaranLain::where('payment_method_id', $paymentMethodId)
            ->when($startDate, fn ($q) => $q->where('date_expense', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('date_expense', '<=', $endDate))
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($expense) {
                return new self([
                    'payment_method_id' => $expense->payment_method_id,
                    'transaction_date' => $expense->date_expense,
                    'description' => $expense->name ?? $expense->note ?? 'Other Expense',
                    'debit_amount' => $expense->amount,
                    'credit_amount' => 0,
                    'source_type' => 'other_expense',
                    'source_id' => $expense->id,
                    'source_table' => 'pengeluaran_lains',
                    'reconciliation_status' => 'unmatched',
                ]);
            });

        // Combine all transactions
        $transactions = $transactions->concat($weddingPayments)
            ->concat($otherIncome)
            ->concat($weddingExpenses)
            ->concat($operationalExpenses)
            ->concat($otherExpenses);

        // Sort by date
        return $transactions->sortBy('transaction_date');
    }

    /**
     * Get net amount (credit - debit)
     */
    public function getNetAmountAttribute(): float
    {
        return (float) ($this->credit_amount - $this->debit_amount);
    }

    /**
     * Check if transaction is income (credit > 0)
     */
    public function getIsIncomeAttribute(): bool
    {
        return $this->credit_amount > 0;
    }

    /**
     * Check if transaction is expense (debit > 0)
     */
    public function getIsExpenseAttribute(): bool
    {
        return $this->debit_amount > 0;
    }
}
