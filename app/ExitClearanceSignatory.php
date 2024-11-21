<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable; 
class ExitClearanceSignatory extends Model implements Auditable
{
    //
    use \OwenIt\Auditing\Auditable;
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function clearance()
    {
        return $this->belongsTo(ExitClearance::class,'exit_clearance_id','id');
    }
}
