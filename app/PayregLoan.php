<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
class PayregLoan extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    //
    public function loan_type()
    {
        return $this->belongsTo(LoanType::class);
    }

}
