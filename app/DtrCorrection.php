<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DtrCorrection extends Model
{
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function dtr_correction_approver()
    {
        return $this->hasMany(DtrCorrectionApprover::class);
    }
}
