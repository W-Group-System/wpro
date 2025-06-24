<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
class SalaryAdjustment extends Model  implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function encoded_by()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
}
