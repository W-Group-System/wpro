<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLeaveList extends Model
{
    use SoftDeletes;
    
    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
