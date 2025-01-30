<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
class PayregInstruction  extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'employee_code', 'instruction_name', 'amount','created_by','payreg_id',
    ];

    public function pay_reg()
    {
        return $this->belongsTo(Payregs::class,'payreg_id','id');
    }
}
