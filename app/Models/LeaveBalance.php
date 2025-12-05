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
        'used_days',
        'remaining_days',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($leaveBalance) {
            // Auto-calculate remaining days
            $leaveBalance->remaining_days = $leaveBalance->allocated_days - $leaveBalance->used_days;
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

    // Calculate used days from approved leave requests
    public function calculateUsedDays()
    {
        $usedDays = $this->user->leaveRequests()
            ->where('leave_type_id', $this->leave_type_id)
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->sum('total_days');

        $this->update(['used_days' => $usedDays]);

        return $usedDays;
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
                $leaveBalance = self::firstOrCreate([
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id,
                ], [
                    'allocated_days' => $leaveType->max_days_per_year, // Always use LeaveType value
                    'used_days' => 0,
                    'remaining_days' => 0, // Will be calculated in boot method
                ]);

                if ($leaveBalance->wasRecentlyCreated) {
                    $created++;
                } else {
                    // Update allocated days to match LeaveType
                    $allocatedDays = $leaveType->max_days_per_year;
                    if ($leaveBalance->allocated_days != $allocatedDays) {
                        $leaveBalance->update(['allocated_days' => $allocatedDays]);
                        $updated++;
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
            ], [
                'allocated_days' => $leaveType->max_days_per_year, // Always use LeaveType value
                'used_days' => 0,
                'remaining_days' => 0,
            ]);

            if ($leaveBalance->wasRecentlyCreated) {
                $created++;
            } else {
                $allocatedDays = $leaveType->max_days_per_year;
                if ($leaveBalance->allocated_days != $allocatedDays) {
                    $leaveBalance->update(['allocated_days' => $allocatedDays]);
                    $updated++;
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
            $this->allocated_days = $this->leaveType->max_days_per_year;
            $this->remaining_days = $this->allocated_days - $this->used_days;
            $this->save();
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
