<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PayInstructionExport implements FromArray, WithHeadings, WithEvents
{
    public function array(): array
    {
        return [
            [],
        ];
    }

    public function headings(): array
    {
        return [
            'Location',
            'Employee Code',
            'Name',
            'Start Date',
            'End Date',
            'Benefit Name',
            'Amount',
            'Frequency',
            'Deductible',
            'Remarks',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $frequencyOptions = '"Every 1st cut off,Every 2nd cut off,Every cut off,This cut off"';
                $deductibleOptions = '"YES, NO"';
                for ($i = 2; $i <= 100; $i++) { 
                    $validation = $sheet->getCell('H' . $i)->getDataValidation();
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1($frequencyOptions);

                    $validation = $sheet->getCell('I' . $i)->getDataValidation();
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1($deductibleOptions);
                }
            },
        ];
    }
}
