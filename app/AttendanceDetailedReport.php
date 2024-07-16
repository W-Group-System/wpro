<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendanceDetailedReport extends Model
{
   protected $fillable = [
      'company_id', 
      'employee_no',
      'name',
      'log_date',
      'shift',
      'in',
      'out',
      'abs',
      'lv_w_pay',
      'reg_hrs',
      'late_min',
      'undertime_min',
      'reg_ot',
      'reg_nd',
      'reg_ot_nd',
      'rst_ot',
      'rst_ot_over_eight',
      'rst_nd',
      'rst_nd_over_eight',
      'lh_ot',
      'lh_ot_over_eight',
      'lh_nd',
      'lh_nd_over_eight',
      'sh_ot',
      'sh_ot_over_eight',
      'sh_nd',
      'sh_nd_over_eight',
      'rst_lh_ot',
      'rst_sh_ot_over_eight',
      'rst_sh_nd',
      'rst_sh_nd_over_eight',
      'cut_off_date',
      'rst_lh_ot_over_eight',
      'rst_lh_nd',
      'rst_lh_nd_over_eight',
      'rst_sh_ot',
  ];
   
   public function company()
   {
    return $this->belongsTo(Company::class);
   }

   public function employee()
   {
      return $this->belongsTo(Employee::class, 'employee_no', 'employee_code');
   }

}
