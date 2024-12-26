<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class PayInstruction extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    
    protected $table = 'pay_instructions';

    public $fillable = ['location', 'site_id', 'name', 'start_date','end_date', 'benefit_name', 'amount', 'frequency', 'deductible', 'remarks', 'created_by' ];
}
