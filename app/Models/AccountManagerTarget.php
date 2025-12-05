<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountManagerTarget extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'target_amount',
        'achieved_amount',
        'status', // Contoh: 'pending', 'on_track', 'achieved', 'behind'
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'achieved_amount' => 'decimal:2',
        'year' => 'integer',
        'month' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMonthNameAttribute(): string
    {
        return Carbon::create()->month($this->month)->format('F');
    }

    public function getAchievementPercentageAttribute(): float
    {
        // Prioritaskan calculated_achieved_amount jika ada (dari selectSub),
        // jika tidak, gunakan achieved_amount yang tersimpan, default ke 0 jika keduanya null.
        $achieved = $this->calculated_achieved_amount ?? $this->achieved_amount ?? 0;
        $target = $this->target_amount ?? 0;

        if ((float) $target > 0) {
            return round(((float) $achieved / (float) $target) * 100, 2);
        }

        return 0;
    }

    public function getRemainingTargetAttribute(): float
    {
        $achieved = $this->calculated_achieved_amount ?? $this->achieved_amount ?? 0;
        $target = $this->target_amount ?? 0;

        return max(0, (float) $target - (float) $achieved);
    }

    /**
     * Get the calculated status based on achievement percentage.
     */
    public function getCalculatedStatusAttribute(): string
    {
        $percentage = $this->achievement_percentage; // Accessor ini sudah menggunakan calculated_achieved_amount

        if ($percentage > 100) {
            return 'Overachieved';
        } elseif ($percentage == 100) {
            return 'Achieved';
        } elseif ($percentage >= 50) { // Mencakup 50% hingga 99.99...%
            return 'Partially Achieved';
        } else { // Mencakup < 50%
            return 'Failed';
        }
    }

    /**
     * Get the color for the status badge based on calculated status.
     */
    public function getStatusColor(): string
    {
        // Menggunakan accessor untuk status yang dihitung
        $calculatedStatus = $this->calculated_status;

        return match (strtolower($calculatedStatus)) {
            'overachieved' => 'info', // Atau warna lain yang menonjol
            'achieved' => 'success',
            'partially achieved' => 'warning',
            'failed' => 'danger',
            default => 'secondary', // Fallback
        };
    }
}
