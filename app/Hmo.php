<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hmo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = "hmos";
    protected $fillable = [
        'employee_name', 'email', 'company', 'department', 'date_availment', 'user_id', 'status'
    ];

    public function attachments()
    {
        return $this->hasMany(HmoAttachment::class, 'hmo_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class,'user_id','user_id');
    }  

}
