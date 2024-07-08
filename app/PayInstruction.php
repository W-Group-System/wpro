<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class PayInstruction extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'pay_instructions';

    public $fillable = ['location', 'site_id', 'name', 'start_date','end_date', 'benefit_name', 'amount', 'frequency', 'deductible', 'remarks', 'created_by' ];
}
