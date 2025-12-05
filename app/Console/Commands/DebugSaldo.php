<?php

namespace App\Console\Commands;

use App\Models\PaymentMethod;
use Illuminate\Console\Command;

class DebugSaldo extends Command
{
    protected $signature = 'saldo:debug {payment_method_id : ID of payment method to debug}';

    protected $description = 'Debug saldo calculation for specific payment method';

    public function handle()
    {
        $paymentMethodId = $this->argument('payment_method_id');
        $paymentMethod = PaymentMethod::find($paymentMethodId);

        if (! $paymentMethod) {
            $this->error("Payment Method dengan ID {$paymentMethodId} tidak ditemukan!");

            return;
        }

        $this->info('=== DEBUG SALDO REKENING ===');
        $this->info("Rekening: {$paymentMethod->name}");
        $this->info("Bank: {$paymentMethod->bank_name}");
        $this->info("Nomor: {$paymentMethod->no_rekening}");
        $this->line('');

        $startDate = $paymentMethod->opening_balance_date;
        $this->info('📅 Tanggal Pembukuan: '.$startDate->format('d F Y'));
        $this->info('💰 Saldo Awal: Rp '.number_format($paymentMethod->opening_balance, 0, ',', '.'));
        $this->line('');

        // Hitung detail uang masuk
        $this->info('=== UANG MASUK ===');

        $totalMasukWedding = $paymentMethod->payments()
            ->when($startDate, fn ($query) => $query->where('tgl_bayar', '>=', $startDate))
            ->sum('nominal');
        $countMasukWedding = $paymentMethod->payments()
            ->when($startDate, fn ($query) => $query->where('tgl_bayar', '>=', $startDate))
            ->count();

        $totalMasukLain = $paymentMethod->pendapatanLains()
            ->when($startDate, fn ($query) => $query->where('tgl_bayar', '>=', $startDate))
            ->sum('nominal');
        $countMasukLain = $paymentMethod->pendapatanLains()
            ->when($startDate, fn ($query) => $query->where('tgl_bayar', '>=', $startDate))
            ->count();

        $this->line("💒 Wedding ({$countMasukWedding} transaksi): Rp ".number_format($totalMasukWedding, 0, ',', '.'));
        $this->line("➕ Lainnya ({$countMasukLain} transaksi): Rp ".number_format($totalMasukLain, 0, ',', '.'));
        $totalMasuk = $totalMasukWedding + $totalMasukLain;
        $this->info('📊 TOTAL UANG MASUK: Rp '.number_format($totalMasuk, 0, ',', '.'));
        $this->line('');

        // Hitung detail uang keluar
        $this->info('=== UANG KELUAR ===');

        $totalKeluarWedding = $paymentMethod->expenses()
            ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
            ->sum('amount');
        $countKeluarWedding = $paymentMethod->expenses()
            ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
            ->count();

        $totalKeluarOps = $paymentMethod->expenseOps()
            ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
            ->sum('amount');
        $countKeluarOps = $paymentMethod->expenseOps()
            ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
            ->count();

        $totalKeluarLain = $paymentMethod->pengeluaranLains()
            ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
            ->sum('amount');
        $countKeluarLain = $paymentMethod->pengeluaranLains()
            ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
            ->count();

        $this->line("💒 Wedding ({$countKeluarWedding} transaksi): Rp ".number_format($totalKeluarWedding, 0, ',', '.'));
        $this->line("🏢 Operasional ({$countKeluarOps} transaksi): Rp ".number_format($totalKeluarOps, 0, ',', '.'));
        $this->line("➖ Lainnya ({$countKeluarLain} transaksi): Rp ".number_format($totalKeluarLain, 0, ',', '.'));
        $totalKeluar = $totalKeluarWedding + $totalKeluarOps + $totalKeluarLain;
        $this->info('📊 TOTAL UANG KELUAR: Rp '.number_format($totalKeluar, 0, ',', '.'));
        $this->line('');

        // Perhitungan manual vs model
        $this->info('=== PERHITUNGAN SALDO ===');
        $saldoManual = $paymentMethod->opening_balance + $totalMasuk - $totalKeluar;
        $saldoModel = $paymentMethod->saldo;

        $this->line('💰 Saldo Awal: Rp '.number_format($paymentMethod->opening_balance, 0, ',', '.'));
        $this->line('➕ Total Masuk: Rp '.number_format($totalMasuk, 0, ',', '.'));
        $this->line('➖ Total Keluar: Rp '.number_format($totalKeluar, 0, ',', '.'));
        $this->line('======================================');
        $this->info('💵 Saldo Manual: Rp '.number_format($saldoManual, 0, ',', '.'));
        $this->info('💵 Saldo Model: Rp '.number_format($saldoModel, 0, ',', '.'));

        if ($saldoManual == $saldoModel) {
            $this->info('✅ PERHITUNGAN BENAR!');
        } else {
            $this->error('❌ ADA PERBEDAAN!');
            $this->error('Selisih: Rp '.number_format(abs($saldoManual - $saldoModel), 0, ',', '.'));
        }

        // Show recent transactions
        $this->line('');
        $this->info('=== 5 TRANSAKSI TERAKHIR ===');
        $this->showRecentTransactions($paymentMethod);
    }

    private function showRecentTransactions($paymentMethod)
    {
        // Recent DataPembayaran
        $recentPayments = $paymentMethod->payments()
            ->orderBy('tgl_bayar', 'desc')
            ->limit(3)
            ->get();

        if ($recentPayments->count() > 0) {
            $this->line('💒 Pembayaran Wedding:');
            foreach ($recentPayments as $payment) {
                $this->line('  '.$payment->tgl_bayar->format('d/m/Y').' - Rp '.number_format($payment->nominal, 0, ',', '.'));
            }
        }

        // Recent Expenses
        $recentExpenses = $paymentMethod->expenses()
            ->orderBy('date_expense', 'desc')
            ->limit(3)
            ->get();

        if ($recentExpenses->count() > 0) {
            $this->line('💸 Pengeluaran Wedding:');
            foreach ($recentExpenses as $expense) {
                $this->line('  '.$expense->date_expense->format('d/m/Y').' - Rp '.number_format($expense->amount, 0, ',', '.'));
            }
        }
    }
}
