<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type_id',
        'year',
        'allocated_days',
        'carried_over_days',
        'used_days',
        'remaining_days',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($leaveBalance) {
            // Recalculate remaining days before saving
            $leaveBalance->calculateRemainingDays();
        });

        static::creating(function ($leaveBalance) {
            // When creating new record, always set allocated_days from LeaveType
            if ($leaveBalance->leaveType && ! $leaveBalance->allocated_days) {
                $leaveBalance->allocated_days = $leaveBalance->leaveType->max_days_per_year;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function histories()
    {
        return $this->hasMany(LeaveBalanceHistory::class);
    }

    /**
     * Calculate remaining days based on allocation, carry over, and usage.
     * Does NOT save the model.
     */
    public function calculateRemainingDays()
    {
        $year = $this->year ?? now()->year;
        
        // Logic for Carry Over Expiration (March 31st)
        $carriedOver = $this->carried_over_days ?? 0;
        $allocated = $this->allocated_days;
        
        // We need to know how many days were used in Jan-Mar of this balance year
        $usedJanMar = $this->user->leaveRequests()
            ->where('leave_type_id', $this->leave_type_id)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', '<=', 3) // Jan, Feb, Mar
            ->sum('total_days');
            
        // If today > March 31 of this balance year
        $cutoffDate = \Carbon\Carbon::create($year, 3, 31)->endOfDay();
        
        if (now()->gt($cutoffDate)) {
            // Expired. 
            // The amount effectively used from carry over is min(carriedOver, usedJanMar).
            $effectiveCarryOver = min($carriedOver, $usedJanMar);
        } else {
            // Not yet expired. Full carry over available.
            $effectiveCarryOver = $carriedOver;
        }
        
        // Use $this->used_days which should be set before calling this
        $this->remaining_days = $allocated + $effectiveCarryOver - ($this->used_days ?? 0);
    }

    // Calculate used days from approved leave requests
    public function calculateUsedDays()
    {
        $year = $this->year ?? now()->year;
        
        $totalUsed = $this->user->leaveRequests()
            ->where('leave_type_id', $this->leave_type_id)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->sum('total_days');

        $this->used_days = $totalUsed;

        $this->calculateRemainingDays();
        
        $this->saveQuietly(); // Use saveQuietly to avoid triggering events if any
        
        return $totalUsed;
    }

    // Get percentage of used days
    public function getUsagePercentageAttribute()
    {
        if ($this->allocated_days == 0) {
            return 0;
        }

        return round(($this->used_days / $this->allocated_days) * 100, 1);
    }

    // Check if balance is critical (> 80% used)
    public function getIsCriticalAttribute()
    {
        return $this->usage_percentage > 80;
    }

    // Check if balance is exhausted
    public function getIsExhaustedAttribute()
    {
        return $this->remaining_days <= 0;
    }

    /**
     * Auto-generate leave balances for all users and leave types
     * This will create missing leave balance records and update existing ones
     */
    public static function generateForAllUsers($year = null)
    {
        $year = $year ?? now()->year;
        $users = User::all();
        $leaveTypes = LeaveType::all();

        $created = 0;
        $updated = 0;

        foreach ($users as $user) {
            foreach ($leaveTypes as $leaveType) {
                // Determine carry over from previous year (Only for Cuti Tahunan)
                $carryOver = 0;
                if ($leaveType->name === 'Cuti Tahunan' || stripos($leaveType->name, 'tahunan') !== false || stripos($leaveType->name, 'annual') !== false) {
                    $previousYear = $year - 1;
                    $previousBalance = self::where('user_id', $user->id)
                        ->where('leave_type_id', $leaveType->id)
                        ->where('year', $previousYear)
                        ->first();
                    $carryOver = ($previousBalance && $previousBalance->remaining_days > 0) ? $previousBalance->remaining_days : 0;
                }

                $leaveBalance = self::firstOrCreate([
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id,
                    'year' => $year,
                ], [
                    'allocated_days' => $leaveType->max_days_per_year, // Always use LeaveType value
                    'carried_over_days' => $carryOver,
                    'used_days' => 0,
                    'remaining_days' => 0, // Will be calculated
                ]);

                if ($leaveBalance->wasRecentlyCreated) {
                    $created++;
                } else {
                    // Update allocated days to match LeaveType ONLY if not Cuti Pengganti
                    if (stripos($leaveType->name, 'pengganti') === false && stripos($leaveType->name, 'replacement') === false) {
                        $allocatedDays = $leaveType->max_days_per_year;
                        $needsUpdate = false;
                        
                        if ($leaveBalance->allocated_days != $allocatedDays) {
                            $leaveBalance->allocated_days = $allocatedDays;
                            $needsUpdate = true;
                        }
                        
                        // Update carry over if it was missing
                        if ($carryOver > 0 && $leaveBalance->carried_over_days == 0) {
                            $leaveBalance->carried_over_days = $carryOver;
                            $needsUpdate = true;
                        }
                        
                        if ($needsUpdate) {
                            $leaveBalance->save();
                            $updated++;
                        }
                    }
                }

                // Recalculate used days from approved leave requests
                $leaveBalance->calculateUsedDays();
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'message' => "Generated {$created} new records and updated {$updated} existing records.",
        ];
    }

    /**
     * Auto-generate leave balance for specific user
     */
    public static function generateForUser(User $user, $year = null)
    {
        $year = $year ?? now()->year;
        $leaveTypes = LeaveType::all();

        $created = 0;
        $updated = 0;

        foreach ($leaveTypes as $leaveType) {
            $leaveBalance = self::firstOrCreate([
                'user_id' => $user->id,
                'leave_type_id' => $leaveType->id,
                'year' => $year,
            ], [
                'allocated_days' => $leaveType->max_days_per_year, // Always use LeaveType value
                'used_days' => 0,
                'remaining_days' => 0,
            ]);

            if ($leaveBalance->wasRecentlyCreated) {
                $created++;
            } else {
                if (stripos($leaveType->name, 'pengganti') === false && stripos($leaveType->name, 'replacement') === false) {
                    $allocatedDays = $leaveType->max_days_per_year;
                    if ($leaveBalance->allocated_days != $allocatedDays) {
                        $leaveBalance->update(['allocated_days' => $allocatedDays]);
                        $updated++;
                    }
                }
            }

            $leaveBalance->calculateUsedDays();
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'message' => "Generated {$created} new records and updated {$updated} existing records for {$user->name}.",
        ];
    }

    /**
     * Sync allocated days with leave type max_days_per_year
     */
    public function syncWithLeaveType()
    {
        if ($this->leaveType) {
            // Only sync if max_days_per_year > 0
            if ($this->leaveType->max_days_per_year > 0) {
                $this->allocated_days = $this->leaveType->max_days_per_year;
                $this->remaining_days = $this->allocated_days - $this->used_days;
                $this->save();
            }
        }

        return $this;
    }

    /**
     * Auto-set allocated days from LeaveType when leave_type_id changes
     */
    public function setLeaveTypeIdAttribute($value)
    {
        $this->attributes['leave_type_id'] = $value;

        // Auto-set allocated_days when leave_type_id is set
        if ($value && ! $this->allocated_days) {
            $leaveType = LeaveType::find($value);
            if ($leaveType) {
                $this->attributes['allocated_days'] = $leaveType->max_days_per_year;
            }
        }
    }
}
