<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class EmployeeSalary extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $fillable = [
        'basic_salary', 
        'de_minimis',
        'other_allowance',
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class,'user_id','user_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
