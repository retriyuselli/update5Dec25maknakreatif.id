<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'emergency_contact',
        'documents',
        'replacement_employee_id',
        'status',
        'approved_by',
        'approval_notes',
        'substitution_date',
        'substitution_notes',
        'leave_balance_history_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'integer',
        'documents' => 'array',
    ];

    protected static function booted()
    {
        // Event ketika LeaveRequest akan dibuat
        static::creating(function ($leaveRequest) {
            // Auto-fill user_id jika belum diisi
            if (empty($leaveRequest->user_id) && Auth::check()) {
                $leaveRequest->user_id = Auth::id();
            }
        });

        // Event ketika LeaveRequest diupdate
        static::updated(function ($leaveRequest) {
            // Jika status berubah menjadi approved
            if ($leaveRequest->status === 'approved') {
                $leaveRequest->updateLeaveBalance();
            }
        });

        // Event ketika LeaveRequest baru dibuat dengan status approved
        static::created(function ($leaveRequest) {
            if ($leaveRequest->status === 'approved') {
                $leaveRequest->updateLeaveBalance();
            }
        });
    }

    /**
     * Update atau create LeaveBalance untuk user ini
     */
    public function updateLeaveBalance()
    {
        $year = $this->start_date ? $this->start_date->year : now()->year;

        // Cari atau buat LeaveBalance
        $leaveBalance = LeaveBalance::firstOrCreate(
            [
                'user_id' => $this->user_id,
                'leave_type_id' => $this->leave_type_id,
                'year' => $year,
            ],
            [
                'allocated_days' => $this->leaveType->max_days_per_year ?? 12,
                'used_days' => 0,
                'remaining_days' => 0,
            ]
        );

        // Hitung ulang used_days
        $leaveBalance->calculateUsedDays();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function leaveBalanceHistory()
    {
        return $this->belongsTo(LeaveBalanceHistory::class);
    }

    public function replacementEmployee()
    {
        return $this->belongsTo(User::class, 'replacement_employee_id');
    }
}
