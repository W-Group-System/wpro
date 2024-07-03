<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeBenefits extends Model
{
  public function user() {
    
    return $this->belongsTo(User::class, 'user_id');
  }

  public function postedBy() {
    
    return $this->belongsTo(User::class, 'posted_by');
  }
}
