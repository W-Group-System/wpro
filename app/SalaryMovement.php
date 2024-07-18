<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalaryMovement extends Model
{
    protected $fillable = [
        'user_id', 
        'old_values',
        'new_values',
        'salary_nopa_attachment',
        'changed_by',
        'changed_at',
    ];
    public function user_info()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function change_by()
    {
        return $this->belongsTo(User::class,'changed_by','id');
    }
    
}
