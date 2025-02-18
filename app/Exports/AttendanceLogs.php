<?php

namespace App\Exports;

use App\AttendanceLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceLogs implements WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return AttendanceLog::all();
    // }

    public function headings(): array
    {
        return [
            'emp_code',
            'date',
            'date_time',
            'type',
            'location',
            'ip_address'
        ];
    }
}
