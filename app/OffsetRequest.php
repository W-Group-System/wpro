<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OffsetRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'ot_date', 'ot_hours', 'date_to_use',
        'reason', 'attachment', 'status', 'level',
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
