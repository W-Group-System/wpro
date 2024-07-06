<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayInstruction extends Model
{
    protected $table = 'pay_instructions';

    public $fillable = ['location', 'site_id', 'name', 'start_date','end_date', 'benefit_name', 'amount', 'frequency', 'deductible', 'remarks' ];
}
