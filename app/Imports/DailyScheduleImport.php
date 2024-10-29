<?php

namespace App\Imports;

use App\Company;
use App\DailySchedule;
use App\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use RealRashid\SweetAlert\Facades\Alert;

class DailyScheduleImport implements WithHeadingRow, ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function collection(Collection $collection)
    {
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $rows = $collection->toArray();
        
        $rows = array_filter($rows, function ($row) {
            return array_filter($row);
        });

        $employeeCodes = array_column($rows, 'employee_no');

        $employees = Employee::where('status', 'Active')
            ->whereIn('company_id', $allowed_companies)
            // ->whereIn('employee_code', $employeeCodes)
            ->get()
            ->keyBy('employee_code');

        $dailySchedules = [];
        foreach ($rows as $row) {
            if ($employees->isNotEmpty())
            {
                $employees_array = $employees->pluck('employee_code')->toArray();
                foreach(array_unique($employeeCodes) as $code)
                {
                    $employee_id = $employees->where('employee_code',$code)->first();
                    if($employee_id == null)
                    {
                        Alert::error('Error: Some employee numbers do not exist in our company records. Please verify the employee numbers and try again.'.$code)->persistent('Dismiss');
                        return back();
                    }
                }
                $employees = $employees->whereIn('employee_code', $employeeCodes);
                
                
                if (!isset($employees[$row['employee_no']])) {

                    Alert::error('Error: Some employee numbers do not exist in our company records. Please verify the employee numbers and try again.')->persistent('Dismiss');
                    return back();
                }
            }
            else
            {
                Alert::error('Error! You don\'t have access in this company')->persistent('Dismiss');
                return back();
            }
            
            $dailySchedules[] = [
                'company' => $row['company'],
                'employee_code' => $row['employee_no'],
                'employee_name' => $row['employee_name'],
                'log_date' => Date::excelToDateTimeObject($row['log_date'])->format('Y-m-d'),
                'time_in_from' => Date::excelToDateTimeObject($row['time_in_from'])->format('H:i'),
                'time_in_to' => Date::excelToDateTimeObject($row['time_in_to'])->format('H:i'),
                'time_out_from' => Date::excelToDateTimeObject($row['time_out_from'])->format('H:i'),
                'time_out_to' => Date::excelToDateTimeObject($row['time_out_to'])->format('H:i'),
                'working_hours' => $row['working_hours'],
                'created_by' => auth()->user()->id,
            ];
        }
        
        DB::transaction(function () use ($dailySchedules) {
            DailySchedule::insert($dailySchedules);
        });

        Alert::success('Successfully Uploaded')->persistent('Dismiss');
        return back();
    }
}
