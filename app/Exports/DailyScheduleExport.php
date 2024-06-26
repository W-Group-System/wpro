<?php

namespace App\Exports;

use App\DailySchedule;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class DailyScheduleExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //   return DailySchedule::all();
    // }

    public function view(): View {
      return view('schedules.daily-schedule-template');
    }
}
