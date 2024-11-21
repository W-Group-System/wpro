<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
class ExitClearance extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    //
    public function signatories()
    {
        return $this->hasMany(ExitClearanceSignatory::class);
    }
    public function comments()
    {
        return $this->hasMany(ExitClearanceComment::class);
    }
    public function checklists()
    {
        return $this->hasMany(ExitClearanceChecklist::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function resign()
    {
        return $this->belongsTo(ExitResign::class);
    }
}
