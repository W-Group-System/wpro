<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DailySchedule extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;

  protected $fillable = ['company', 'employee_number', 'employee_code', 'employee_name', 'log_date', 'time_in_from', 'time_in_to', 'time_out_from', 'time_out_to', 'working_hours'];

  public function user() {
    return $this->belongsTo(User::class, 'created_by');
  }
}
