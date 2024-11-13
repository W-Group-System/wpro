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

        Excel::import(new DailyScheduleImport, $request->file);

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
