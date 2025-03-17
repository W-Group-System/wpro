<?php

namespace App\Http\Controllers;

use App\Company;
use App\DailySchedule;
use App\Employee;
use App\ScheduleData;
use DateTime;
use Illuminate\Http\Request;

class WeeklyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request->all());
        $header = 'weekly_attendance_report';
        $week = $request->week;
        $company_data = $request->company;

        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);

        $date_array = [];
        if ($week)
        {
            $start_date = new DateTime($request->week);
            $start_date->modify('Monday this week');
            
            for($i=0; $i < 7; $i++)
            {
                $date_array[] = $start_date->format('Y-m-d');
                $start_date->modify('+1 day');
            }
        }

        $companies = Company::whereIn('id', $allowed_companies)->get();
        
        $employees = Employee::select('employee_number','user_id','first_name','last_name','middle_name','location','schedule_id','employee_code','company_id','work_description','original_date_hired')
            ->with([
                'company',
                // 'attendances',
                'user_info' => function($q) 
                {
                    $q->where('status', 'Active');
                },
                'leaves' => function($q)use($date_array) 
                {
                    $q->whereIn('date_from', $date_array);
                },
                'approved_ots' => function($q)use($date_array) 
                {
                    $q->whereIn('ot_date', $date_array)->where('status','Approved');
                },
                'attendances' => function ($query) use ($date_array) {
                    $query->whereBetween('time_in', [$date_array[0]." 00:00:01", $date_array[6]." 23:59:59"])
                    ->orWhereBetween('time_out', [$date_array[0]." 00:00:01", $date_array[6]." 23:59:59"])
                    ->orderBy('time_in','asc')
                    ->orderby('time_out','desc')
                    ->orderBy('id','asc');
                }
            ])
            ->where('status','Active')
            ->where('company_id', $company_data)
            ->get();
        
        $daily_schedules = DailySchedule::whereIn('log_date', $date_array)->get();

        $schedules = ScheduleData::all();
        
        return view('weekly_attendance_report.index', compact('header', 'employees', 'week', 'daily_schedules', 'companies', 'company_data', 'date_array', 'schedules'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
