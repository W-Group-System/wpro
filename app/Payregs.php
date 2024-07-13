<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
use OwenIt\Auditing\Contracts\Auditable;
class Payregs extends Model  implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'payregs';
    public function pay_allowances()
    {
        return $this->hasMany(PayregAllowance::class,'id','payreg_id');
    }
    public function pay_loan()
    {
        return $this->hasMany(PayregLoan::class,'id','payreg_id');
    }
    public function pay_instructions()
    {
        return $this->hasMany(PayregInstruction::class,'id','payreg_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_no','employee_code');
    }
}