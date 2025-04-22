<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class EmployeeEarnedLeave extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use SoftDeletes;
   
    public function employee()
    {
        return $this->belongsTo(Employee::class,'user_id','user_id');
    }
    public function leave_type_info()
    {
        return $this->belongsTo(Leave::class,'leave_type','id');
    }
}
