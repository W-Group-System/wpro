<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
class PayregAllowance extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    //

    public function allowance_type()
    {
        return $this->belongsTo(Allowance::class,'allowance_id','id');
    }
    public function pay_reg_emp()
    {
        return $this->belongsTo(Payregs::class);
    }
}
