<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'bank_name',
        'no_rekening',
        'is_cash',
        'cabang',
        'opening_balance',
        'opening_balance_date',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'opening_balance_date' => 'date',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DataPembayaran::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'payment_method_id');
    }

    public function expenseOps(): HasMany
    {
        return $this->hasMany(ExpenseOps::class, 'payment_method_id');
    }

    public function pendapatanLains(): HasMany
    {
        return $this->hasMany(PendapatanLain::class, 'payment_method_id');
    }

    public function pengeluaranLains(): HasMany
    {
        return $this->hasMany(PengeluaranLain::class, 'payment_method_id');
    }

    /**
     * Hitung saldo akhir rekening berdasarkan formula:
     * Saldo Akhir = Saldo Awal + Total Uang Masuk - Total Uang Keluar
     *
     * Catatan: Hanya transaksi pada/setelah opening_balance_date yang dihitung
     */
    public function getSaldoAttribute(): float
    {
        $startDate = $this->opening_balance_date;

        // Validasi tanggal pembukuan
        if (! $startDate) {
            return (float) $this->opening_balance;
        }

        $totalMasuk = $this->getTotalUangMasuk($startDate);
        $totalKeluar = $this->getTotalUangKeluar($startDate);

        // Formula: Saldo Awal + Uang Masuk - Uang Keluar
        return (float) $this->opening_balance + $totalMasuk - $totalKeluar;
    }

    /**
     * Hitung total uang masuk dari semua sumber
     */
    public function getTotalUangMasuk($startDate = null): float
    {
        $startDate = $startDate ?? $this->opening_balance_date;

        // 1. Uang masuk dari pembayaran wedding (DataPembayaran)
        $totalMasukWedding = $this->payments()
            ->when($startDate, fn ($query) => $query->where('tgl_bayar', '>=', $startDate))
            ->whereNull('deleted_at')
            ->sum('nominal') ?? 0;

        // 2. Uang masuk dari sumber lain (PendapatanLain)
        $totalMasukLain = $this->pendapatanLains()
            ->when($startDate, fn ($query) => $query->where('tgl_bayar', '>=', $startDate))
            ->whereNull('deleted_at')
            ->sum('nominal') ?? 0;

        return (float) ($totalMasukWedding + $totalMasukLain);
    }

    /**
     * Hitung total uang keluar dari semua sumber
     */
    public function getTotalUangKeluar($startDate = null): float
    {
        $startDate = $startDate ?? $this->opening_balance_date;

        // 1. Uang keluar untuk biaya wedding (Expense)
        $totalKeluarWedding = $this->expenses()
            ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
            ->whereNull('deleted_at')
            ->sum('amount') ?? 0;

        // 2. Uang keluar untuk operasional (ExpenseOps)
        $totalKeluarOps = $this->expenseOps()
            ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
            ->whereNull('deleted_at')
            ->sum('amount') ?? 0;

        // 3. Uang keluar untuk keperluan lain (PengeluaranLain)
        $totalKeluarLain = $this->pengeluaranLains()
            ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
            ->whereNull('deleted_at')
            ->sum('amount') ?? 0;

        return (float) ($totalKeluarWedding + $totalKeluarOps + $totalKeluarLain);
    }

    /**
     * Hitung perubahan saldo (naik/turun) dari saldo awal
     */
    public function getPerubahanSaldoAttribute(): float
    {
        return $this->saldo - $this->opening_balance;
    }

    /**
     * Status perubahan saldo (positif/negatif)
     */
    public function getStatusPerubahanAttribute(): string
    {
        $perubahan = $this->perubahan_saldo;

        if ($perubahan > 0) {
            return 'naik';
        } elseif ($perubahan < 0) {
            return 'turun';
        } else {
            return 'tetap';
        }
    }

    /**
     * Breakdown detail saldo untuk debugging
     */
    public function getSaldoBreakdown(): array
    {
        $startDate = $this->opening_balance_date;

        return [
            'saldo_awal' => $this->opening_balance,
            'tanggal_pembukuan' => $startDate?->format('Y-m-d'),
            'uang_masuk' => [
                'wedding' => $this->payments()
                    ->when($startDate, fn ($query) => $query->where('tgl_bayar', '>=', $startDate))
                    ->whereNull('deleted_at')
                    ->sum('nominal') ?? 0,
                'lainnya' => $this->pendapatanLains()
                    ->when($startDate, fn ($query) => $query->where('tgl_bayar', '>=', $startDate))
                    ->whereNull('deleted_at')
                    ->sum('nominal') ?? 0,
                'total' => $this->getTotalUangMasuk($startDate),
            ],
            'uang_keluar' => [
                'wedding' => $this->expenses()
                    ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0,
                'operasional' => $this->expenseOps()
                    ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0,
                'lainnya' => $this->pengeluaranLains()
                    ->when($startDate, fn ($query) => $query->where('date_expense', '>=', $startDate))
                    ->whereNull('deleted_at')
                    ->sum('amount') ?? 0,
                'total' => $this->getTotalUangKeluar($startDate),
            ],
            'saldo_akhir' => $this->saldo,
            'perubahan' => $this->perubahan_saldo,
            'status' => $this->status_perubahan,
        ];
    }
}
