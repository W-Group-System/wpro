<?php

namespace App\Exports;

use App\EmployeeOvertime;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OvertimeTemplateExport implements WithHeadings, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            'EMPLOYEE NO',
            'EMPLOYEE NAME',
            'DATE FILED',
            'START DATE TIME',
            'END DATE TIME',
            'OT APPROVED HRS',
            'BREAK HRS',
            'DATE APPROVED',
            'APPROVER EMPLOYEE NO',
            'REASON',
            'STATUS'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }
}
