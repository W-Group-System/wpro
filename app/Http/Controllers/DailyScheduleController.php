<?php

namespace App\Http\Controllers;

use App\DailySchedule;
use App\Employee;
use App\Exports\DailyScheduleExport;
use App\Imports\DailyScheduleImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Input;
use Shuchkin\SimpleXLSX;

class DailyScheduleController extends Controller
{
    public function index(Request $request)
    {
        $allowedCompanies = getUserAllowedCompanies(auth()->user()->id);

        $employee = Employee::whereIn('company_id', $allowedCompanies)->where('status', 'Active')->get();

        $dailySchedule = DailySchedule::query();

        if (!empty($request->employee)) {
            $dailySchedule = $dailySchedule->where('employee_number', $request->employee);
        }

        if (!empty($request->date_from) && !empty($request->date_to)) {
            $dailySchedule = $dailySchedule->whereBetween('log_date', [$request->date_from, $request->date_to]);
        }

        $dailySchedule = $dailySchedule->where('created_by', auth()->user()->id)->paginate(10);

        return view(
            'schedules.daily_schedule',
            array(
                'header' => 'schedule',
                'dailySchedule' => $dailySchedule,
                'employee' => $employee,
                'empNum' => $request->employee,
                'dateFrom' => $request->date_from,
                'dateTo' => $request->date_to
            )
        );
    }

    public function upload(Request $request)
    {
        // dd($request->all());
        ini_set('memory_limit', '-1');

        $request->validate([
            'file' => 'max:1024'
        ]);

        // Excel::import(new DailyScheduleImport, $request->file);

        $files = $request->file('file')->getRealPath();

        $xlsx = SimpleXLSX::parse($files)->rows();

        foreach($xlsx as $key=>$row) 
        {
            if ($key > 0)
            {
                $emp_codes = collect($xlsx)->pluck(1)->toArray();

                foreach(array_unique($emp_codes) as $emp_key=>$emp_code)
                {
                    if ($emp_key > 0)
                    {
                        if(!empty($emp_code))
                        {
                            $employees = Employee::where('employee_code', $emp_code)->first();

                            if ($employees == null)
                            {
                                Alert::error('Error: Some employee numbers do not exist in our company records. Please verify the employee numbers and try again.'.$emp_code)->persistent('Dismiss');
                                return back();
                            }
                        }
                    }
                }
                
                $daily_schedules = new DailySchedule;
                $daily_schedules->company = $row[0];
                $daily_schedules->employee_code = $row[1];
                $daily_schedules->employee_name = $row[2];
                $daily_schedules->log_date = date('Y-m-d', strtotime($row[3]));
                $daily_schedules->time_in_from = $row[4] == null ? null : date('H:i', strtotime($row[4]));
                $daily_schedules->time_in_to = $row[5] == null ? null : date('H:i', strtotime($row[5]));
                $daily_schedules->time_out_from = $row[6] == null ? null : date('H:i', strtotime($row[6]));
                $daily_schedules->time_out_to = $row[7] == null ? null : date('H:i', strtotime($row[7]));
                $daily_schedules->working_hours = $row[8] == null ? null : $row[8];
                $daily_schedules->created_by = auth()->user()->id;
                $daily_schedules->save();
            }
        }

        Alert::success('Successfully Saved')->persistent('Dismiss');
        return back();
    }

    // public function update(Request $request, $id)
    // {
    //     $dailySchedule = DailySchedule::findOrFail($id);
    //     $dailySchedule->log_date = $request->log_date;
    //     $dailySchedule->time_in_from = $request->time_in_from;
    //     $dailySchedule->time_in_to = $request->time_in_to;
    //     $dailySchedule->time_out_from = $request->time_out_from;
    //     $dailySchedule->time_out_to = $request->time_out_to;
    //     $dailySchedule->working_hours = $request->working_hours;
    //     $dailySchedule->created_by = auth()->user()->id;
    //     $dailySchedule->save();

    //     Alert::success('Successfully Updated')->persistent('Dismiss');
    //     return back();
    // }

    public function export(Request $request)
    {
        return Excel::download(new DailyScheduleExport, 'Daily Schedule Template.xlsx');
    }
}
