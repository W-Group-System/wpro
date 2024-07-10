<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Guarantor extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['employee_id', 'loan_id'];
}
