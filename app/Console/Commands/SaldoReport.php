<?php

namespace App\Console\Commands;

use App\Models\PaymentMethod;
use Illuminate\Console\Command;

class SaldoReport extends Command
{
    protected $signature = 'saldo:report {--detail : Show detailed breakdown}';

    protected $description = 'Generate saldo report for all payment methods';

    public function handle()
    {
        $paymentMethods = PaymentMethod::all();

        $this->info('=== LAPORAN SALDO SEMUA REKENING ===');
        $this->info('Tanggal: '.now()->format('d F Y H:i:s'));
        $this->line('');

        $totalSaldo = 0;

        foreach ($paymentMethods as $pm) {
            $this->showRekeningSummary($pm);

            if ($this->option('detail')) {
                $this->showDetailedBreakdown($pm);
            }

            $totalSaldo += $pm->saldo;
        }

        $this->info('TOTAL SALDO SEMUA REKENING: Rp '.number_format($totalSaldo, 0, ',', '.'));
    }

    private function showRekeningSummary($paymentMethod)
    {
        $this->info("📊 Rekening: {$paymentMethod->name}");
        $this->line("🏦 Bank: {$paymentMethod->bank_name}");
        $this->line("💳 Nomor: {$paymentMethod->no_rekening}");
        $this->line('💰 Saldo Awal: Rp '.number_format($paymentMethod->opening_balance, 0, ',', '.'));
        $this->line('💵 Saldo Akhir: Rp '.number_format($paymentMethod->saldo, 0, ',', '.'));
        $this->line('📅 Tanggal Pembukuan: '.$paymentMethod->opening_balance_date->format('d F Y'));

        // Hitung selisih
        $selisih = $paymentMethod->saldo - $paymentMethod->opening_balance;
        $status = $selisih >= 0 ? '📈 Naik' : '📉 Turun';
        $this->line("📊 Perubahan: {$status} Rp ".number_format(abs($selisih), 0, ',', '.'));

        $this->line('----------------------------------------');
    }

    private function showDetailedBreakdown($paymentMethod)
    {
        $startDate = $paymentMethod->opening_balance_date;

        // Uang Masuk
        $totalMasukWedding = $paymentMethod->payments()
            ->when($startDate, fn ($query) => $query->where('tgl_bayar', '>=', $startDate))
            ->sum('nominal');

        $totalMasukLain = $paymentMethod->pendapatanLains()
            ->when($startDate, fn ($query) => $query->where('tgl_bayar', '>=', $startDate))
            ->sum('nominal');

        // Uang Keluar
        $totalKeluarWedding = $paymentMethod->expenses()
            ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
            ->sum('amount');

        $totalKeluarOps = $paymentMethod->expenseOps()
            ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
            ->sum('amount');

        $totalKeluarLain = $paymentMethod->pengeluaranLains()
            ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
            ->sum('amount');

        $this->line('  📥 UANG MASUK:');
        $this->line('    💒 Wedding: Rp '.number_format($totalMasukWedding, 0, ',', '.'));
        $this->line('    ➕ Lainnya: Rp '.number_format($totalMasukLain, 0, ',', '.'));
        $this->line('    📊 Total Masuk: Rp '.number_format($totalMasukWedding + $totalMasukLain, 0, ',', '.'));

        $this->line('  📤 UANG KELUAR:');
        $this->line('    💒 Wedding: Rp '.number_format($totalKeluarWedding, 0, ',', '.'));
        $this->line('    🏢 Operasional: Rp '.number_format($totalKeluarOps, 0, ',', '.'));
        $this->line('    ➖ Lainnya: Rp '.number_format($totalKeluarLain, 0, ',', '.'));
        $this->line('    📊 Total Keluar: Rp '.number_format($totalKeluarWedding + $totalKeluarOps + $totalKeluarLain, 0, ',', '.'));

        $this->line('');
    }
}
