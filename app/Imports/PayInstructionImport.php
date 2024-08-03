<?php

namespace App\Imports;

use App\PayInstruction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class PayInstructionImport implements ToModel, WithHeadingRow
{
    
    public function model(array $row)
    {
        $start_date = $row['start_date'];
        $end_date = $row['end_date'];

        if (is_numeric($start_date)) {
            $start_date = Date::excelToDateTimeObject($start_date)->format('Y-m-d');
        }
        if (is_numeric($end_date)) {
            $end_date = Date::excelToDateTimeObject($end_date)->format('Y-m-d');
        }
        $amount = $row['amount'];
        if ($row['deductible'] === 'YES') {
            $amount = -$amount; 
        }

        return new PayInstruction([
            'location'     => $row['location'] ,      
            'site_id'      => $row['employee_code'],       
            'name'         => $row['name'],          
            'start_date'   => $start_date,    
            'end_date'     => $end_date,      
            'benefit_name' => $row['benefit_name'],  
            'amount'       => $amount,        
            'frequency'    => $row['frequency'],     
            'deductible'   => $row['deductible'],    
            'remarks'      => $row['remarks'],       
        ]);
    }
}
