<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeMovement extends Model
{
    protected $fillable = [
        'user_id', 
        'old_values',
        'new_values',
        'nopa_attachment',
        'changed_by',
        'changed_at',
    ];

    public function classification_info()
    {
        return $this->belongsTo(Classification::class,'classification','id');
    }

    public function level_info()
    {
        return $this->belongsTo(Level::class,'level','id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function immediate_sup_data()
    {
        return $this->belongsTo(User::class,'immediate_sup','id');
    }
    public function user_info()
    {
        return $this->belongsTo(User::class,'changed_by','id');
    }
    
}
