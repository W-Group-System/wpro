<?php

namespace App\Exports;

use App\SlBank;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SlBankExport implements WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public function headings(): array
    {
        return [
            'employee_no',
            'sl_bank_balance'
        ];
    }
}
