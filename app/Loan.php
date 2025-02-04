<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Loan extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    //
    public function loan_type()
    {
        return $this->belongsTo(LoanType::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function loan_beneficiaries()
    {
        return $this->hasMany(Guarantor::class);
    }
    public function pay()
    {
        return $this->hasMany(PayregLoan::class);
    }
    public function refund()
    {
        return $this->hasMany(PayregInstruction::class);
    }
}
