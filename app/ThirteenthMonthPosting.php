<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThirteenthMonthPosting extends Model
{
    protected $fillable = [
        'company_id',
        'employee_no',
        'employee_name',
        'department',
        'account_number',
        'half',
        'year',
        'posting_cut_off',
        'monthly_salary',
        'subliq_amount',
        'annual_thirteenth_month',
        'first_half_released',
        'release_amount',
        'posted_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_no', 'employee_code');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
