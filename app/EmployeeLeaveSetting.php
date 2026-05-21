<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EmployeeLeaveSetting extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'employee_id',
        'user_id',
        'vl_annual_credit',
        'vl_is_accumulative',
        'vl_credit_month',
        'vl_credit_day',
        'vl_accumulate_day',
        'sl_annual_credit',
        'sl_is_accumulative',
        'sl_credit_month',
        'sl_credit_day',
        'sl_accumulate_day',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
