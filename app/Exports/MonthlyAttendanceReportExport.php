<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class MonthlyAttendanceReportExport implements FromView
{
    public $header, $from, $to, $companies, $company_data, $company, $employees, $daily_schedules, $schedules, $date_range;

    public function __construct($header, $from, $to, $companies, $company_data, $company, $employees, $daily_schedules, $schedules, $date_range)
    {
        $this->header = $header;
        $this->from = $from;
        $this->to = $to;
        $this->companies = $companies;
        $this->company_data = $company_data;
        $this->company = $company;
        $this->employees = $employees;
        $this->daily_schedules = $daily_schedules;
        $this->schedules = $schedules;
        $this->date_range = $date_range;
    }

    public function view(): View
    {
        return view('weekly_attendance_report.monthly_excel', [
            'header' => $this->header,
            'from' => $this->from,
            'to' => $this->to,
            'companies' => $this->companies,
            'company_data' => $this->company_data,
            'company' => $this->company,
            'employees' => $this->employees,
            'daily_schedules' => $this->daily_schedules,
            'schedules' => $this->schedules,
            'date_range' => $this->date_range,
        ]);
    }
}
