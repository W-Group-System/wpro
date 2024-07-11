<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
use OwenIt\Auditing\Contracts\Auditable;
class Payreg extends Model  implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function pay_allowances()
    {
        return $this->hasMany(PayregAllowance::class);
    }
    public function pay_loan()
    {
        return $this->hasMany(PayregLoan::class);
    }
    public function pay_instructions()
    {
        return $this->hasMany(PayregInstruction::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_no','employee_code');
    }
}