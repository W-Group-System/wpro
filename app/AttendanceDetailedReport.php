<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendanceDetailedReport extends Model
{
   public function company()
   {
    return $this->belongsTo(Company::class);
   }

   public function employee()
   {
      return $this->belongsTo(Employee::class, 'employee_code', 'employee_number');
   }

}
