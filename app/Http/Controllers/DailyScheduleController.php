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
    public function index() {

      $dailySchedule = DailySchedule::get();

      return view('schedules.daily_schedule',
        array(
          'header' => 'schedule',
          'dailySchedule' => $dailySchedule
        )
      );
    }

    public function upload(Request $request) {
      $request->validate([
        'file' => 'max:10240'
      ]);

      Excel::import(new DailyScheduleImport, $request->file);

      return back();
    }

    public function update(Request $request, $id) {
      $dailySchedule = DailySchedule::findOrFail($id);
      $dailySchedule->log_date = $request->log_date;
      $dailySchedule->time_in_from = $request->time_in_from;
      $dailySchedule->time_in_to = $request->time_in_to;
      $dailySchedule->time_out_from = $request->time_out_from;
      $dailySchedule->time_out_to = $request->time_out_to;
      $dailySchedule->working_hours = $request->working_hours;
      $dailySchedule->save();

      Alert::success('Successfully Updated')->persistent('Dismiss');
      return back();
    }

    public function export(Request $request) {
      
      return Excel::download(new DailyScheduleExport, 'Daily Schedule Template.xlsx');
    }
}
