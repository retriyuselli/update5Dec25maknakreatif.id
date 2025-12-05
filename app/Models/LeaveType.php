<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'keterangan',
        'max_days_per_year',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
