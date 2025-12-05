<?php

namespace App\Models;

use App\Enums\JenisPiutang;
use App\Enums\StatusPiutang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Piutang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_piutang',
        'jenis_piutang',
        'dibuat_oleh',
        'nama_debitur', // kreditor -> debitur (yang berhutang ke kita)
        'kontak_debitur',
        'keterangan',
        'jumlah_pokok',
        'persentase_bunga',
        'total_piutang',
        'sudah_dibayar',
        'sisa_piutang',
        'tanggal_piutang',
        'tanggal_jatuh_tempo',
        'tanggal_lunas',
        'status',
        'prioritas',
        'lampiran',
        'catatan',
    ];

    protected $casts = [
        'tanggal_piutang' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_lunas' => 'date',
        'lampiran' => 'array',
        'jumlah_pokok' => 'decimal:2',
        'persentase_bunga' => 'decimal:2',
        'total_piutang' => 'decimal:2',
        'sudah_dibayar' => 'decimal:2',
        'sisa_piutang' => 'decimal:2',
        'jenis_piutang' => JenisPiutang::class,
        'status' => StatusPiutang::class,
    ];

    // Relationships
    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function pembayaranPiutangs(): HasMany
    {
        return $this->hasMany(PembayaranPiutang::class);
    }

    // Static Methods
    public static function generateNomorPiutang(): string
    {
        $tahun = date('Y');
        $prefix = 'PT'; // PT = Piutang

        // Cari nomor terakhir tahun ini
        $lastNumber = self::where('nomor_piutang', 'like', "{$prefix}/%/{$tahun}")
            ->orderBy('nomor_piutang', 'desc')
            ->first();

        if ($lastNumber) {
            // Extract nomor dari format PT/XXX/YYYY
            $parts = explode('/', $lastNumber->nomor_piutang);
            $lastNum = (int) $parts[1];
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }

        return sprintf('%s/%03d/%s', $prefix, $newNum, $tahun);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', StatusPiutang::AKTIF);
    }

    public function scopeJatuhTempo($query)
    {
        return $query->where('tanggal_jatuh_tempo', '<=', now())
            ->whereIn('status', [StatusPiutang::AKTIF, StatusPiutang::DIBAYAR_SEBAGIAN]);
    }

    public function scopeAkanJatuhTempo($query, $hari = 7)
    {
        return $query->whereBetween('tanggal_jatuh_tempo', [
            now(),
            now()->addDays($hari),
        ])->whereIn('status', [StatusPiutang::AKTIF, StatusPiutang::DIBAYAR_SEBAGIAN]);
    }

    // Accessor & Mutator
    public function getIsJatuhTempoAttribute(): bool
    {
        return $this->tanggal_jatuh_tempo <= now() &&
               in_array($this->status, [StatusPiutang::AKTIF, StatusPiutang::DIBAYAR_SEBAGIAN]);
    }

    public function getHariTerlambatAttribute(): int
    {
        if (! $this->is_jatuh_tempo) {
            return 0;
        }

        return $this->tanggal_jatuh_tempo->diffInDays(now());
    }

    // Methods
    public function updateStatus(): void
    {
        if ($this->sisa_piutang <= 0) {
            $this->status = StatusPiutang::LUNAS;
            $this->tanggal_lunas = now();
        } elseif ($this->sudah_dibayar > 0) {
            $this->status = StatusPiutang::DIBAYAR_SEBAGIAN;
        } elseif ($this->is_jatuh_tempo) {
            $this->status = StatusPiutang::JATUH_TEMPO;
        }

        $this->save();
    }

    public function hitungTotalPiutang(): void
    {
        $bunga = ($this->jumlah_pokok * $this->persentase_bunga) / 100;
        $this->total_piutang = $this->jumlah_pokok + $bunga;
        $this->sisa_piutang = $this->total_piutang - $this->sudah_dibayar;
        $this->save();
    }
}
