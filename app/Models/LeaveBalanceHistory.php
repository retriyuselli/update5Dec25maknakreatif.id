<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalanceHistory extends Model
{
    protected $fillable = [
        'leave_balance_id',
        'amount',
        'transaction_date',
        'reason',
        'created_by',
        'status',
    ];

    public function leaveBalance()
    {
        return $this->belongsTo(LeaveBalance::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
