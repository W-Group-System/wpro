<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleChangeRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'date_from', 'date_to',
        'time_in_from', 'time_in_to', 'time_out_from', 'time_out_to',
        'working_hours', 'reason', 'status', 'level',
        'approved_by', 'approved_date', 'approval_remarks', 'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->hasMany(EmployeeApprover::class, 'user_id', 'user_id');
    }

    public function approveBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'user_id', 'user_id');
    }
}
