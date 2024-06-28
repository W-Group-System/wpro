<?php

namespace App\Imports;

use App\DailySchedule;
use App\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use RealRashid\SweetAlert\Facades\Alert;

class DailyScheduleImport implements WithHeadingRow, ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
      $allowed_companies = getUserAllowedCompanies(auth()->user()->id);

      $rows = $rows->toArray();
      foreach($rows as $row) {
        if (!array_filter($row)) {
          return null;
        }
        
        $employee = Employee::where('status', 'Active')
          ->whereIn('company_id', $allowed_companies)
          ->where('employee_number', $row['employee_number'])
          ->first();
        
        if (empty($employee)) {
          
          Alert::error('Error! You dont have access in this company')->persistent('Dismiss');
          return back();
        }
        else {
          
          $dailySchedule = new DailySchedule;
          $dailySchedule->company = $row['company'];
          $dailySchedule->employee_number = $row['employee_number'];
          $dailySchedule->employee_code = $row['employee_code'];
          $dailySchedule->employee_name = $row['employee_name'];
          $dailySchedule->log_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['log_date']);
          $dailySchedule->time_in_from = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['time_in_from'])->format('H:i');
          $dailySchedule->time_in_to = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['time_in_to'])->format('H:i');
          $dailySchedule->time_out_from = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['time_out_from'])->format('H:i');
          $dailySchedule->time_out_to = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['time_out_to'])->format('H:i');
          $dailySchedule->working_hours = $row['working_hours'];
          $dailySchedule->save();
        }
      }

      Alert::success('Successfully Uploaded')->persistent('Dismiss');
      return back();
    }
}
