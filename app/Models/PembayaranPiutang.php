<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranPiutang extends Model
{
    use HasFactory;

    protected $fillable = [
        'piutang_id',
        'nomor_pembayaran',
        'jumlah_pembayaran',
        'jumlah_bunga',
        'denda',
        'total_pembayaran',
        'payment_method_id',
        'tanggal_pembayaran',
        'tanggal_dicatat',
        'nomor_referensi',
        'dibayar_oleh', // customer yang membayar
        'dikonfirmasi_oleh',
        'bukti_pembayaran',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'tanggal_dicatat' => 'date',
        'bukti_pembayaran' => 'array',
        'jumlah_pembayaran' => 'decimal:2',
        'jumlah_bunga' => 'decimal:2',
        'denda' => 'decimal:2',
        'total_pembayaran' => 'decimal:2',
    ];

    // Relationships
    public function piutang(): BelongsTo
    {
        return $this->belongsTo(Piutang::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function dibayarOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibayar_oleh');
    }

    public function dikonfirmasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }

    // Static Methods
    public static function generateNomorPembayaran(): string
    {
        $tahun = date('Y');
        $prefix = 'PP'; // PP = Pembayaran Piutang

        $lastNumber = self::where('nomor_pembayaran', 'like', "{$prefix}/%/{$tahun}")
            ->orderBy('nomor_pembayaran', 'desc')
            ->first();

        if ($lastNumber) {
            $parts = explode('/', $lastNumber->nomor_pembayaran);
            $lastNum = (int) $parts[1];
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }

        return sprintf('%s/%03d/%s', $prefix, $newNum, $tahun);
    }

    // Events
    protected static function booted()
    {
        static::created(function ($pembayaran) {
            $pembayaran->piutang->sudah_dibayar += $pembayaran->total_pembayaran;
            $pembayaran->piutang->sisa_piutang = $pembayaran->piutang->total_piutang - $pembayaran->piutang->sudah_dibayar;
            $pembayaran->piutang->updateStatus();
        });

        static::deleted(function ($pembayaran) {
            $pembayaran->piutang->sudah_dibayar -= $pembayaran->total_pembayaran;
            $pembayaran->piutang->sisa_piutang = $pembayaran->piutang->total_piutang - $pembayaran->piutang->sudah_dibayar;
            $pembayaran->piutang->updateStatus();
        });
    }
}
