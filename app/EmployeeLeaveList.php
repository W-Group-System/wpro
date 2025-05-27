<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class EmployeeLeaveList extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    
    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
