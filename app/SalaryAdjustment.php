<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
class SalaryAdjustment extends Model  implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function encoded_by()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
}
