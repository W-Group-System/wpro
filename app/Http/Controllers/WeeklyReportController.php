<?php

namespace App\Http\Controllers;

use App\Company;
use App\DailySchedule;
use App\Employee;
use App\ScheduleData;
use PDF;
use DateTime;
use App\Exports\MonthlyAttendanceReportExport;
use Maatwebsite\Excel\Facades\Excel;
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

    // public function monthly(Request $request)
    // {
    //     $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
    //     $to = $request->input('to', now()->endOfMonth()->format('Y-m-d'));

    //     $allowed_companies = getUserAllowedCompanies(auth()->user()->id);

    //     // Get company selection from request (array)
    //     $company = (array) $request->input('companies', []);

    //     // Get all companies the user is allowed to see
    //     $companies = Company::whereHas('employee_has_company')
    //         ->whereIn('id', $allowed_companies)
    //         ->get();

    //     // Get selected companies (if any)
    //     $company_data = collect();
    //     if (!empty($company)) {
    //         $company_data = Company::whereIn('id', $company)->get();
    //     }

    //     // Fetch attendance logs within date range
    //     $data = AttendanceLog::with('employee')
    //         ->whereBetween('date', [$from, $to])
    //         ->get();

    //     // Fetch employees with necessary relations and filters
    //     $employees = Employee::select(
    //             'employee_number',
    //             'user_id',
    //             'first_name',
    //             'last_name',
    //             'middle_name',
    //             'location',
    //             'schedule_id',
    //             'employee_code',
    //             'company_id',
    //             'work_description',
    //             'original_date_hired'
    //         )
    //         ->with([
    //             'company',
    //             'user_info' => fn($q) => $q->where('status', 'Active'),
    //             'leaves' => fn($q) => $q->whereBetween('date_from', [$from, $to]),
    //             'approved_ots' => fn($q) => $q->whereBetween('ot_date', [$from, $to])->where('status', 'Approved'),
    //             'attendances' => function ($query) use ($from, $to) {
    //                 $query->where(function ($q) use ($from, $to) {
    //                     $q->whereBetween('time_in', [$from . ' 00:00:00', $to . ' 23:59:59'])
    //                     ->orWhereBetween('time_out', [$from . ' 00:00:00', $to . ' 23:59:59']);
    //                 })
    //                 ->orderBy('time_in', 'asc')
    //                 ->orderBy('time_out', 'desc')
    //                 ->orderBy('id', 'asc');
    //             }
    //         ])
    //         ->where('status', 'Active')
    //         ->when($company_data->isNotEmpty(), fn($q) => $q->whereIn('company_id', $company_data->pluck('id')))
    //         ->get();
        
    //     // Daily schedule (filter using date range, not collection)
    //     $daily_schedules = DailySchedule::whereBetween('log_date', [$from, $to])->get();

    //     // All schedule data
    //     $schedules = ScheduleData::all();

    //     // Return to view
    //     return view('weekly_attendance_report.monthly', [
    //         'header' => 'weekly_attendance_report',
    //         'from' => $from,
    //         'to' => $to,
    //         'companies' => $companies,
    //         'company_data' => $company_data,
    //         'company' => $company,
    //         'data' => $data,
    //         'employees' => $employees,
    //         'daily_schedules' => $daily_schedules,
    //         'schedules' => $schedules,
    //     ]);

    // }

    public function monthly(Request $request) 
    {
        $header = 'monthly_attendance_report';
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->endOfMonth()->format('Y-m-d'));
        $company = (array) $request->input('companies', []);

        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $companies = Company::whereHas('employee_has_company')
            ->whereIn('id', $allowed_companies)
            ->get();

        // Empty by default
        $employees = collect();
        $company_data = collect();
        $daily_schedules = collect();
        $date_range = [];

        if ($from && $to && !empty($company)) {
            $period = \Carbon\CarbonPeriod::create($from, $to);
            foreach ($period as $date) {
                $date_range[] = $date->format('Y-m-d');
            }
            // $start = strtotime($from);
            // $end = strtotime($to);
            // for ($i = $start; $i <= $end; $i += 86400) {
            //     $date_range[] = date('Y-m-d', $i);
            // }
           
            $company_data = Company::whereIn('id', $company)->get();

            $employees = Employee::select(
                'employee_number',
                'user_id',
                'first_name',
                'last_name',
                'middle_name',
                'location',
                'schedule_id',
                'employee_code',
                'company_id',
                'work_description',
                'original_date_hired'
            )
            ->with([
                'company',
                'ScheduleData',
                'user_info' => fn($q) => $q->where('status', 'Active'),
                'leaves' => fn($q) => $q->whereBetween('date_from', [$from, $to]),
                'approved_ots' => fn($q) => $q->whereBetween('ot_date', [$from, $to])->where('status', 'Approved'),
                'attendances' => function ($query) use ($from, $to) {
                    $query->where(function ($q) use ($from, $to) {
                        $q->whereBetween('time_in', [$from . ' 00:00:01', $to . ' 23:59:59'])
                        ->orWhereBetween('time_out', [$from . ' 00:00:01', $to . ' 23:59:59']);
                    })
                    ->orderBy('time_in', 'asc')
                    ->orderBy('time_out', 'desc')
                    ->orderBy('id', 'asc');
                }
            ])
            ->where('status', 'Active')
            ->whereIn('company_id', $company)
            ->get()
            ->take(500);

            $daily_schedules = DailySchedule::whereBetween('log_date', [$from, $to])->get();
        }
        // dd($daily_schedules);
        $schedules = ScheduleData::all();

        return view('weekly_attendance_report.monthly', compact(
            'header', 'from', 'to', 'companies', 'company_data', 'company', 'employees', 'daily_schedules','schedules', 'date_range'
        ));
    }

    public function exportPdf(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->endOfMonth()->format('Y-m-d'));
        $company = (array) $request->input('companies', []);

        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $companies = Company::whereHas('employee_has_company')
            ->whereIn('id', $allowed_companies)
            ->get();

        $employees = collect();
        $company_data = collect();
        $daily_schedules = collect();
       
        $date_range = [];

        if ($from && $to && !empty($company)) {
            $period = \Carbon\CarbonPeriod::create($from, $to);
            foreach ($period as $date) {
                $date_range[] = $date->format('Y-m-d');
            }

            $company_data = Company::whereIn('id', $company)->get();

            $employees = Employee::with([
                'company',
                'ScheduleData',
                'user_info' => fn($q) => $q->where('status', 'Active'),
                'leaves' => fn($q) => $q->whereBetween('date_from', [$from, $to]),
                'approved_ots' => fn($q) => $q->whereBetween('ot_date', [$from, $to])->where('status', 'Approved'),
                'attendances' => function ($query) use ($from, $to) {
                    $query->where(function ($q) use ($from, $to) {
                        $q->whereBetween('time_in', [$from . ' 00:00:01', $to . ' 23:59:59'])
                        ->orWhereBetween('time_out', [$from . ' 00:00:01', $to . ' 23:59:59']);
                    })
                    ->orderBy('time_in', 'asc')
                    ->orderBy('time_out', 'desc')
                    ->orderBy('id', 'asc');
                }
            ])
            ->where('status', 'Active')
            ->whereIn('company_id', $company)
            ->get();

            $daily_schedules = DailySchedule::whereBetween('log_date', [$from, $to])->get();
        }

        $schedules = ScheduleData::all();
        $header = 'monthly_attendance_report';

        $pdf = PDF::loadView('weekly_attendance_report.monthly_pdf', compact(
        'header', 'from', 'to', 'companies', 'company_data', 'company', 'employees', 'daily_schedules', 'schedules', 'date_range'
            ))->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'dpi' => 96,
                'defaultFont' => 'Arial',
            ]);

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="monthly_attendance_report.pdf"');
    }

    public function exportExcel(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->endOfMonth()->format('Y-m-d'));
        $company = (array) $request->input('companies', []);

        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $companies = Company::whereHas('employee_has_company')
            ->whereIn('id', $allowed_companies)
            ->get();

        $employees = collect();
        $company_data = collect();
        $daily_schedules = collect();
        $date_range = [];

        if ($from && $to && !empty($company)) {
            $period = \Carbon\CarbonPeriod::create($from, $to);
            foreach ($period as $date) {
                $date_range[] = $date->format('Y-m-d');
            }

            $company_data = Company::whereIn('id', $company)->get();

            $employees = Employee::with([
                'company',
                'ScheduleData',
                'user_info' => fn($q) => $q->where('status', 'Active'),
                'leaves' => fn($q) => $q->whereBetween('date_from', [$from, $to]),
                'approved_ots' => fn($q) => $q->whereBetween('ot_date', [$from, $to])->where('status', 'Approved'),
                'attendances' => function ($query) use ($from, $to) {
                    $query->where(function ($q) use ($from, $to) {
                        $q->whereBetween('time_in', [$from . ' 00:00:01', $to . ' 23:59:59'])
                        ->orWhereBetween('time_out', [$from . ' 00:00:01', $to . ' 23:59:59']);
                    })
                    ->orderBy('time_in', 'asc')
                    ->orderBy('time_out', 'desc')
                    ->orderBy('id', 'asc');
                }
            ])
            ->where('status', 'Active')
            ->whereIn('company_id', $company)
            ->get();

            $daily_schedules = DailySchedule::whereBetween('log_date', [$from, $to])->get();
        }

        $schedules = ScheduleData::all();
        $header = 'monthly_attendance_report';

        return Excel::download(
            new MonthlyAttendanceReportExport($header, $from, $to, $companies, $company_data, $company, $employees, $daily_schedules, $schedules, $date_range),
            'monthly_attendance_report.xlsx'
        );
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
