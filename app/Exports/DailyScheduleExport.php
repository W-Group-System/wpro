<?php

namespace App\Exports;

use App\DailySchedule;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyScheduleExport implements WithHeadings, WithStyles, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            'Company',
            'Employee No.',
            'Employee Name',
            'Log Date',
            'Time In From',
            'Time In To',
            'Time Out From',
            'Time Out To',
            'Working Hours',
        ];
    }

    // public function view(): View {
    //   return view('schedules.daily-schedule-template');
    // }

    public function styles(Worksheet $sheet) {
        return [
            1 => [
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => '0080c0'],
                ],
            ]
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_NUMBER_00
        ];
    }
}
