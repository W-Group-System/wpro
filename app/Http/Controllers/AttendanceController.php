<?php

namespace App\Http\Controllers;
use App\IclockTransation;
use App\Attendance;
use App\Employee;
use App\PersonnelEmployee;
use App\Company;
use App\ScheduleData;
use App\EmployeeLeave;
use App\SeabasedAttendance;
use App\HikAttLog;
use App\HikVisionAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\AttendanceLog;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use App\Exports\AttedancePerCompanyExport;;

use App\Exports\AttendanceSeabasedExport;
use App\Imports\EmployeeSeabasedAttendanceImport;
use App\Imports\HikAttLogAttendanceImport;
use App\AttendanceDetailedReport;

use Excel;

use RealRashid\SweetAlert\Facades\Alert;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        ini_set('memory_limit', '-1');
        
        //  
        $from_date = $request->from;
        $to_date = $request->to;
        $date_range =  [];
        $attendances = [];
        if($from_date != null)
        {
        $date_range =  $this->dateRange( $from_date, $to_date);
        $attendances =  $this->get_attendances($from_date,$to_date,auth()->user()->employee->employee_number);
        }
        $schedules = ScheduleData::all();
        // dd($attendances);
        $emp_data = Employee::select('id','user_id','employee_code','first_name','last_name','schedule_id','location','employee_number')
                                ->with(['schedule_info','attendances' => function ($query) use ($from_date, $to_date) {
                                        $query->whereBetween('time_in', [$from_date." 00:00:01", $to_date." 23:59:59"])
                                        ->orWhereBetween('time_out', [$from_date." 00:00:01", $to_date." 23:59:59"])
                                        ->orderBy('time_in','asc')
                                        ->orderby('time_out','desc')
                                        ->orderBy('id','asc');
                                }])
                                ->where('employee_number', auth()->user()->employee->employee_number)
                                ->whereIn('status',['Active','HBU'])
                                ->get();
        return view('attendances.view_attendance',
        array(
            'header' => 'attendances',
            'from_date' => $from_date,
            'to_date' => $to_date,
            'date_range' => $date_range,
            'attendances' => $attendances,
            'schedules' => $schedules,
            'emp_data' => $emp_data,
        ));
    }
    public function subordinates(Request $request)
    {
        $attendance_controller = new AttendanceController; 
        $from_date = $request->from;
        $to_date = $request->to;
        $date_range =  [];
        $attendances = [];
        $schedules = [];
        $emp_code = $request->employee;
        $schedule_id = null;
        $emp_data = [];
        if ($from_date != null) {
            $emp_data = Employee::select('id','user_id','employee_number','first_name','last_name','schedule_id','employee_code')
                                    ->with(['schedule_info','attendances' => function ($query) use ($from_date, $to_date) {
                                            $query->whereBetween('time_in', [$from_date." 00:00:01", $to_date." 23:59:59"])
                                                    ->orWhereBetween('time_out', [$from_date." 00:00:01", $to_date." 23:59:59"])
                                                    ->orderBy('time_in','asc')
                                                    ->orderby('time_out','desc')
                                                    ->orderBy('id','asc');
                                    }])
                                    ->whereIn('employee_number', $request->employee)
                                    ->where('status','Active')
                                    ->get();

            $date_range =  $attendance_controller->dateRange($from_date, $to_date);
           
        }
        $schedules = ScheduleData::all();
        
        return view(
            'attendances.subordinates_attendances',
            array(
                'header' => 'subordinates',
                // 'employees' => $employees,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'date_range' => $date_range,
                'attendances' => $attendances,
                'schedules' => $schedules,
                'emp_code' => $emp_code,
                'emp_data' => $emp_data,
            )
        );

        // //  
        // $from_date = $request->from;
        // $to_date = $request->to;
        // $date_range =  [];
        // $attendances = [];
        // $schedules = [];
        // if($from_date != null)
        // {
        //     $date_range =  $this->dateRange( $from_date, $to_date);
        //     $attendances =  $this->get_attendances($from_date,$to_date,$request->employee);
        //     $schedule_id = Employee::where('employee_number',$request->employee)->first();
        //     // dd($schedule_id);
        //     $schedules = ScheduleData::where('schedule_id',$schedule_id->schedule_id)->get();
        // }
    
        // // dd($attendances);
        // return view('attendances.subordinates_attendances',
        // array(
        //     'header' => 'subordinates',
        //     'from_date' => $from_date,
        //     'to_date' => $to_date,
        //     'date_range' => $date_range,
        //     'attendances' => $attendances,
        //     'schedules' => $schedules,
        // ));
    }
    
    public function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
        $dates = [];
        $current = strtotime( $first );
        $last = strtotime( $last );
    
        while( $current <= $last ) {
    
            $dates[] = date( $format, $current );
            $current = strtotime( $step, $current );
        }
    
        return $dates;
    }

    public function get_attendances($from_date,$to_date,$id)
    {
        $attendances = Attendance::where('employee_code',$id)
        ->orderBy('time_in','asc')
        // ->orderBy('id','asc')
        ->where(function($q) use ($from_date, $to_date) {
            $q->whereBetween('time_in', [$from_date." 00:00:01", $to_date." 23:59:59"])
            ->orWhereBetween('time_out', [$from_date." 00:00:01", $to_date." 23:59:59"]);
        })
        ->get();
        // dd($attendances);
        return $attendances;
    }
    public function get_attendance_now($id)
    {
        $attendances = Attendance::where('employee_code',$id)
        ->orderBy('time_in','asc')
        ->orderBy('id','asc')
        ->where(function($q){
            $q->whereDate('time_in', date('Y-m-d'));
        })
        ->first();

        return $attendances;
    }
    public function get_all_attendances($employees,$from_date,$to_date)
    {
          $employees = PersonnelEmployee::whereIn('employee_code',$employees)->get();

          return $employees;
    }
    public function get_attendances_employees($from_date,$to_date,$employees)
    {
        $attendances = Attendance::whereIn('employee_code',$employees)
        ->orderBy('time_in','asc')
        ->where(function($q) use ($from_date, $to_date) {
            $q->whereBetween('time_in', [$from_date." 00:00:01", $to_date." 23:59:59"]);
            // ->orWhereBetween('time_out', [$from_date." 00:00:01", $to_date." 23:59:59"]);
        })
        ->get();

        return $attendances;
    }
    

    public function attendancePerCompanyExport(Request $request){

        $company = isset($request->company) ? $request->company : "";
        $from = isset($request->from) ? $request->from : "";
        $to =  isset($request->to) ? $request->to : "";
        $company_detail = Company::where('id',$company)->first();
        return Excel::download(new AttedancePerCompanyExport($company,$from,$to),  'Attendance Data ' . $company_detail->company_code . ' ' . $from . ' to ' . $to . '.xlsx');

    }

    public function seabasedAttendances(Request $request){
        
        ini_set('memory_limit', '-1');

        $from_date = $request->from;
        $to_date = $request->to;

        $attendances = SeabasedAttendance::with('employee')->orderBy('time_in','asc')
                                            ->orderBy('id','asc')
                                            ->where(function($q) use ($from_date, $to_date) {
                                                $q->whereBetween('time_in', [$from_date." 00:00:01", $to_date." 23:59:59"])
                                                ->orWhereBetween('time_out', [$from_date." 00:00:01", $to_date." 23:59:59"]);
                                            })
                                            ->get();

        return view('attendances.employee_seabased_attendances',
        array(
            'header' => 'attendances',
            'from_date' => $from_date,
            'to_date' => $to_date,
            'attendances' => $attendances
        )); 
    }

    public function uploadSeabasedAttendance(Request $request){
        
        $path = $request->file('file')->getRealPath();
        $data = Excel::toArray(new EmployeeSeabasedAttendanceImport, $request->file('file'));

        if(count($data[0]) > 0)
        {
            $save_count = 0;
            $not_save = [];
            foreach($data[0] as $key => $value)
            {
                // return $value;
                if(isset($value['attendance_date'])){

                    $check_attendace = SeabasedAttendance::where('employee_code',$value['employee_code'])
                                                            ->where('attendance_date',date('Y-m-d',strtotime($value['attendance_date'])))
                                                            ->where('shift',$value['shift'])
                                                            ->first();
                    if($check_attendace){
                    
                        $time_in = $this->convertTime($value['time_in']);
                        $time_out = $this->convertTime($value['time_out']);

                        $check_attendace->attendance_date =  date('Y-m-d',strtotime($value['attendance_date']));
                        $check_attendace->time_in =  date('Y-m-d',strtotime($value['attendance_date'])) . ' ' . $time_in;
                        $check_attendace->time_out =  date('Y-m-d',strtotime($value['attendance_date'])) . ' ' .$time_out;
                        $check_attendace->updated_by =  auth()->user()->id;
                        $check_attendace->save();
                        $save_count++;

                    }else{
                        $new_attendance = new SeabasedAttendance;
                        $new_attendance->employee_code =  $value['employee_code'];
                        $time_in = $this->convertTime($value['time_in']);
                        $time_out = $this->convertTime($value['time_out']);

                        $new_attendance->attendance_date =  date('Y-m-d',strtotime($value['attendance_date']));
                        $new_attendance->time_in =  date('Y-m-d',strtotime($value['attendance_date'])) . ' ' .$time_in;
                        $new_attendance->time_out = date('Y-m-d',strtotime($value['attendance_date'])) . ' ' .$time_out;
                        $new_attendance->shift =  $value['shift'];
                        $new_attendance->created_by =  auth()->user()->id;
                        $new_attendance->save();
                        $save_count++;
                    }

                    
                    
                }
            }
            Alert::success('Successfully Import Attendances (' . $save_count. ')')->persistent('Dismiss');
            return redirect('/seabased-attendances');
        }
    }

    public function convertTime($time){
        $decimalValue = $time;

        // Calculate total minutes in a day
        $totalMinutesInADay = 24 * 60;

        // Calculate the total minutes for the given decimal fraction of a day
        $totalMinutes = $decimalValue * $totalMinutesInADay;

        // Convert minutes to hours and minutes
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        // Format the result as H:i (hours and minutes)
        return $timeFormatted = sprintf("%02d:%02d", $hours, $minutes);

    }

    public function attendanceSeabasedAttendnaceExport(Request $request){

        $from = isset($request->from) ? $request->from : "";
        $to =  isset($request->to) ? $request->to : "";

        return Excel::download(new AttendanceSeabasedExport($from,$to),  'Attendance Data ' . $from . ' to ' . $to . '.xlsx');

    }

    public function hikAttendances(Request $request){
        
        ini_set('memory_limit', '-1');

        $from_date = $request->from ." 00:00:01";
        $to_date = $request->to ." 23:59:59";
        $terminal = $request->terminal_hik;

        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $allowed_locations = getUserAllowedLocations(auth()->user()->id);
        $allowed_projects = getUserAllowedProjects(auth()->user()->id);

        $attendances = HikVisionAttendance::whereBetween('attendance_date',[$from_date,$to_date])
                                ->orderBy('created_at','asc')
                                ->get();

        return view('attendances.employee_hik_attendances',
        array(
            'header' => 'attendances',
            'from_date' => $request->from,
            'to_date' => $request->to,
            'attendances' => $attendances
        )); 
    }

    public function uploadHikAttendance(Request $request){
        
        $path = $request->file('file')->getRealPath();
        $data = Excel::toArray(new HikAttLogAttendanceImport, $request->file('file'));

        if(count($data[0]) > 0)
        {
            $save_count = 0;
            $not_save = [];

            $start_date = '';
            $end_date = '';
            $is_first = true;

            foreach($data[0] as $key => $value)
            {
                
                if($value['time']){

                    
                    $person_id = str_replace("'","",$value['person_id']);               
                    $attendance_date = isset($value['time']) ? date('Y-m-d H:i',strtotime($value['time'])) : null;

                    if($is_first){
                        $is_first = false;
                        $start_date = date('Y-m-d',strtotime($attendance_date));
                    }

                    $direction = '';
                    if($value['attendance_status'] == 'Check-in'){
                        $direction = 'In';
                    }
                    elseif($value['attendance_status'] == 'Check-out'){
                        $direction = 'Out';
                    }
                    
                    $check_attendace = HikVisionAttendance::select('id')
                                                            ->where('employee_code',$person_id)
                                                            ->where('attendance_date',$attendance_date)
                                                            ->where('direction',$direction)
                                                            ->first();
                    if(empty($check_attendace)){
                        $new_attendance = new HikVisionAttendance;
                        $new_attendance->employee_code = $person_id;   
                        $new_attendance->attendance_date = $attendance_date;
                        $new_attendance->direction = $direction;
                        $new_attendance->device = $value['attendance_check_point'];
                        $new_attendance->save();
                        $save_count++;

                        $end_date = date('Y-m-d',strtotime($attendance_date));
                    }
                    

                }
                
            }

            Alert::success('Successfully Import Attendances (' . $save_count. ')')->persistent('Dismiss');
            return redirect('/hik-attendances?from='.$start_date.'&to='.$end_date);
           
        }
    }
    public function store_logs(Request $request)
    {
        ini_set('memory_limit', '-1');
        $attendance = [];
       foreach($request->data as $req)
       {
            
            $attendance = new AttendanceLog;
            $attendance->emp_code = $req['id'];
            $attendance->date = date('Y-m-d',strtotime($req['timestamp']));
            $attendance->datetime = $req['timestamp'];
            $attendance->type = $req['type'];
            $attendance->location = $request->location;
            $attendance->ip_address = $request->ip_address;
            $attendance->save();
       }
       
       if ($attendance && isset($attendance->id)) {
            return [
                'code' => 200,
                'attendance' => $attendance,
                'message' => 'success',
            ];
        } else {
            return [
                'code' => 500,
                'attendance' => null, // Ensure null is explicitly returned
                'message' => 'error',
            ];
        }
    
    //    if($attendance->id != null)
    //    {
    //    return array( 'code' => 200,
    //     'attendance' => $attendance,
    //     'message' => 'success',
    //     );
    //    }
    //    else
    //    {
    //     return array( 'code' => 500,
    //     'attendance' => $attendance,
    //     'message' => 'error',
    //     );
    //    }
    }
    public function store_logs_hk(Request $request)
    {
        ini_set('memory_limit', '-1');
        if(!empty($request->data))
        {
            foreach($request->data as $req)
            {
                 if($req['time_input'] != '00:00:00')
                 {
     
                     $attendance = new AttendanceLog;
                     $attendance->last_id = $req['id'];
                     $attendance->emp_code = $req['id_bio'];
                     $attendance->date = date('Y-m-d',strtotime($req['date_time']));
                     $attendance->datetime = $req['date_time'];
                     if($req['device_name'] == "HO IN")
                     {
                         $attendance->type = 0;
                     }
                     else
                     {
                         $attendance->type = 1;
                     }
                     $attendance->location = $request->location;
                     $attendance->ip_address = $request->ip_address;
                     $attendance->save();
                 }
                 
            }
            
            if($attendance->id != null)
            {
            return array( 'code' => 200,
             'attendance' => $attendance,
             'message' => 'success',
             );
            }
            else
            {
             return array( 'code' => 500,
             'attendance' => $attendance,
             'message' => 'error',
             );
            }
        }
      
    }
    public function getlastId($company)
    {
        $attendance = AttendanceLog::Where('ip_address',$company)->orderBy('datetime','desc')->first();
        // $id = $attendance->last_id;
        if($attendance != null)
        {
            return array('id' => $attendance->datetime);
        }
        return array('id' => 0);
    
    }
    public function getlastIdHK($company)
    {
        $attendance = AttendanceLog::Where('ip_address',$company)->orderBy('last_id','desc')->first();
        // $id = $attendance->last_id;
        if($attendance != null)
        {
            return array('id' => $attendance->last_id);
        }
        return array('id' => 0);
    
    }
    public function devices()
    {
        ini_set('memory_limit', '-1');
        $locations = ['CCC', 'Head Office', 'PBI', 'SPAI', 'WCC', 'WFA', 'WGC', 'WHI-Carmona', 'WOI', 'WTCC','PRI','FMTCC/MRDC'];
        $devices = AttendanceLog::select('*')
    ->whereIn('id', function ($query) {
        $query->select(DB::raw('MAX(id)'))
              ->from('attendance_logs')
              ->orderBy('datetime', 'desc') 
              ->groupBy('location');
    })
    ->whereIn('location',$locations)
    ->get();
    return $devices;
        return view('attendances.devices',
        array(
            'devices' => $devices,
            'locations' => $locations,
        )
        );
    }

    // public function checkLogDate(Request $request, $company_id)
    // {
    //     try {
    //         // Retrieve log dates for the given company within the specified date range
    //         $fromDate = $request->input('from');
    //         // Add logic to fetch log dates ordered by log_date for the given company_id
    //         $logDates = AttendanceDetailedReport::where('company_id', $company_id)
    //                                             ->whereDate('log_date', '>=', $fromDate)
    //                                             ->orderBy('log_date')
    //                                             ->pluck('log_date')
    //                                             ->toArray();

    //         // Format dates to 'Y-m-d' if needed
    //         $logDates = array_map(function($date) {
    //             return Carbon::parse($date)->format('Y-m-d');
    //         }, $logDates);

    //         return response()->json(['logDates' => $logDates]);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    public function fetchDisabledDates($companyId)
    {
        try {
            // Retrieve log dates for the given company from the attendanceDetailedReport
            $logDates = AttendanceDetailedReport::where('company_id', $companyId)
                ->orderBy('log_date')
                ->pluck('log_date')
                ->toArray();

            // Format dates to 'Y-m-d' format
            $formattedLogDates = array_map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            }, $logDates);

            return response()->json(['log_dates' => $formattedLogDates]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeAttendance(Request $request)
    {        
        $employees = $request->input('employees'); // Get all employee data
        // dd($request->all());
        foreach ($employees as $employee_code => $dates) {
            foreach ($dates as $date => $employee) {
                AttendanceDetailedReport::create([
                    'company_id' => $employee['company_id'],
                    'employee_no' => $employee['employee_no'],
                    'name' => $employee['name'],
                    'log_date' => $employee['log_date'],
                    'shift' => $employee['shift'],
                    'in' => $employee['in'] ?? null,
                    'out' => $employee['out'] ?? null,
                    'abs' => $employee['abs'] ?? null,
                    'lv_w_pay' => $employee['lv_w_pay'] ?? null,
                    'reg_hrs' => $employee['reg_hrs'] ?? null,
                    'late_min' => $employee['late_min'] ?? null,
                    'undertime_min' => $employee['undertime_min'] ?? null,
                    'reg_ot' => $employee['reg_ot'] ?? null,
                    'reg_nd' => $employee['reg_nd'] ?? null,
                    'reg_ot_nd' => $employee['reg_ot_nd'] ?? null,
                    'rst_ot' => $employee['rst_ot'] ?? null,
                    'rst_ot_over_eight' => $employee['rst_ot_over_eight'] ?? null,
                    'rst_nd' => $employee['rst_nd'] ?? null,
                    'rst_nd_over_eight' => $employee['rst_nd_over_eight'] ?? null,
                    'lh_ot' => $employee['lh_ot'] ?? null,
                    'lh_ot_over_eight' => $employee['lh_ot_over_eight'] ?? null,
                    'lh_nd' => $employee['lh_nd'] ?? null,
                    'lh_nd_over_eight' => $employee['lh_nd_over_eight'] ?? null,
                    'sh_ot' => $employee['sh_ot'] ?? null,
                    'sh_ot_over_eight' => $employee['sh_ot_over_eight'] ?? null,
                    'sh_nd' => $employee['sh_nd'] ?? null,
                    'sh_nd_over_eight' => $employee['sh_nd_over_eight'] ?? null,
                    'rst_lh_ot' => $employee['rst_lh_ot'] ?? 0.00,
                    'rst_lh_ot_over_eight' => $employee['rst_lh_ot_over_eight'] ?? 0.00,
                    'rst_lh_nd' => $employee['rst_lh_nd'] ?? 0.00,
                    'rst_lh_nd_over_eight' => $employee['rst_lh_nd_gt_8'] ?? 0.00,
                    'rst_sh_ot' => $employee['rst_sh_ot'] ?? 0.00,
                    'rst_sh_ot_over_eight' => $employee['rst_sh_ot_gt_8'] ?? 0.00,
                    'rst_sh_nd' => $employee['rst_sh_nd'] ?? 0.00,
                    'rst_sh_nd_over_eight' => $employee['rst_sh_nd_over_eight'] ?? 0.00,
                    'cut_off_date' => $employee['to'] ?? 0.00,
                ]);
            }
        }
        
        // Redirect back with a success message
        Alert::success('Successfully Posted')->persistent('Dismiss');
        return redirect('/biometrics-per-company');
    }

    public function reports(Request $request)
    {
        // Get the selected month and year from the request
        $from = $request->input('from');
        $to = $request->input('to');
        $count = $request->input('count');

        // Validate that both month and year are provided
        if ($from && $to) {
            // Filter the data based on the selected month and year
            $data = AttendanceDetailedReport::with('employee')
                                            ->whereBetween('log_date', [$from,$to])
                                            ->get();
        } else {
            // If no month or year is selected, set data to empty collection
            $data = collect();
        }
        // dd($data[0]);

        // Tardiness
        $tardinessData = $data->filter(function ($item) use ($count) {
            return $item->late_min > 0;
        })->groupBy('name')->map(function ($group) {
            return [
                'company_code' => $group->first()->company->company_code,
                'name' => $group->first()->name,
                'tardiness_days' => $group->filter(function ($item) {
                    return $item->late_min > 0;
                })->count(),
                'undertime_days' => $group->filter(function ($item) {
                    return $item->undertime_min > 0;
                })->count(),
                'remarks' => $group->first()->remarks,
                'employee_no' => $group->first()->employee_no
            ];
        })->filter(function ($item) use ($count) {
            return $item['tardiness_days'] >= $count;
        });
        $undertimeData = $data->filter(function ($item) use ($count){
            return $item->undertime_min > 0;
        })->groupBy('name')->map(function ($group) {
            return [
                'company_code' => $group->first()->company->company_code,
                'name' => $group->first()->name,
                'tardiness_days' => $group->filter(function ($item) {
                    return $item->late_min > 0;
                })->count(),
                'undertime_days' => $group->filter(function ($item) {
                    return $item->undertime_min > 0;
                })->count(),
                'remarks' => $group->first()->remarks,
                'employee_no' => $group->first()->employee_no
            ];
        })->filter(function ($item) use ($count) {
            return $item['undertime_days'] >= $count;
        });

        // Leave without pay
        $leaveWithoutData = $data->filter(function ($item) {
            // Ensure the log_date is after or equal to the hired_date
            return $item->abs == 1 && $item->lv_w_pay == 0 && $item->log_date >= $item->employee->original_date_hired;
        })->groupBy('name')->map(function ($group) {
            return [
                'company_code' => $group->first()->company->company_code,
                'name' => $group->first()->name,
                'no_lwop_days' => $group->count(), // Count the number of days matching the criteria
                'remarks' => $group->first()->remarks,
                'employee_no' => $group->first()->employee_no
            ];
        })->filter(function ($item) {
            return $item['no_lwop_days'] >= 3;
        });

        // Leave Deviations
        $leaveDeviationsData = $data->filter(function ($item) {
            return $item->abs > 1 && $item->lv_w_pay > 1 ;
        })->map(function ($item) {
            // Retrieve the employee related to this attendance record
            $employee = Employee::where('employee_code', $item->employee_no)->first();
        
            // Initialize an empty array for leave_types
            $leaveTypes = [];
        
            if ($employee) {
                // Retrieve leave_type from related EmployeeLeave records for this employee
                $leaveTypes = EmployeeLeave::where('user_id', $employee->user_id)
                    ->where('date_from', $item->log_date)  // Ensure date_from matches log_date
                    ->pluck('leave_type')
                    ->unique()
                    ->toArray();
            }
            
            return [
                'company_code' => optional($item->company)->company_code,
                'name' => $item->name,
                'leave_date' => $item->log_date,
                'leave_types' => $leaveTypes,
                'employee_no' => $employee->employee_code
            ];
        });        

        // Leaves 5 more consecutive
        $consecLeaveData = $data->filter(function ($item) {
            return $item->abs > 1 && $item->lv_w_pay > 0;
        })->groupBy('name')->map(function ($group) {
            $firstAttendance = $group->first(); // Get the first attendance record
            
            // Retrieve employees based on employee_no from $firstAttendance
            $employees = Employee::where('employee_code', $firstAttendance->employee_no)->get();
            
            // Initialize an empty array for leave_types
            $leaveTypes = [];
        
            // Iterate through each employee to fetch leave_types
            foreach ($employees as $employee) {
                $leaveTypes[] = EmployeeLeave::where('user_id', $employee->user_id)
                                             ->pluck('leave_type')
                                             ->unique()
                                             ->toArray();
            }
            
            return [
                'company_code' => optional($firstAttendance->company)->company_code,
                'name' => $firstAttendance->name,
                'leave_types' => $leaveTypes,
            ];
        });

        // Overtime
        $overtimeData = $data->filter(function ($item) {
            return $item->reg_hrs > 0;
        })->groupBy('company_id')->map(function ($group) {
            $totalRegHrs = $group->sum('reg_hrs');
            $totalOt = collect(['reg_ot', 'rst_ot', 'lh_ot', 'sh_ot', 'rst_lh_ot', 'rst_sh_ot'])
                ->sum(function ($ot) use ($group) {
                    return $group->sum($ot);
                });
        
            return [
                'company_code' => $group->first()->company->company_code,
                'total_reg_hrs' => $totalRegHrs,
                'total_ot' => $totalOt,
                'percent_overtime' => $totalRegHrs > 0 ? ($totalOt / $totalRegHrs) * 100 : 0,
                'remarks' => $group->first()->remarks,
                'employee_no' => $group->first()->employee_no
            ];
        });
        
        if ($request->type == 'pdf') {
            $pdf = PDF::loadView('reports.print_attendance', [
                'header' => 'attendance-report',
                'tardinessData' => $tardinessData,
                'undertimeData' => $undertimeData,
                'leaveWithoutData' => $leaveWithoutData,
                'leaveDeviationsData' => $leaveDeviationsData,
                'overtimeData' => $overtimeData,
                'from' => $from,
                'to' => $to
            ])->setPaper('a4', 'portrait');
    
            return $pdf->stream('attendance_report' . $from . '-' . $to . '.pdf');
        }

        // Pass the filtered data to the view
        return view('reports.attendance_report', [
            'header' => 'attendance-report',
            'tardinessData' => $tardinessData,
            'undertimeData' => $undertimeData,
            'count' => $count,
            'leaveWithoutData' => $leaveWithoutData,
            'leaveDeviationsData' => $leaveDeviationsData,
            // 'consecLeaveData' => $consecLeaveData,
            'overtimeData' => $overtimeData,
            'from' => $from,
            'to' => $to
        ]);
    }

    public function syncAttendance(Request $request)
    {
        // dd($request->all());
        $from = $request->from;
        $to = $request->to;
        $date = $request->date;
        
        $attendanceLogs = AttendanceLog::where('date', $date)
            ->where('emp_code', $request->emp_code)
            ->orderBy('datetime','asc')
            ->get();

            if ($attendanceLogs != null) 
            {
                foreach($attendanceLogs as $att)
                {
                    if ($att->type == 0)
                    {
                        $attend = Attendance::where('employee_code', $att->emp_code)->where('time_in', date('Y-m-d H:i:s', strtotime($att->datetime)))->first();
                        
                        if($attend == null)
                        {
                            $attendance = new Attendance;
                            $attendance->employee_code  = $att->emp_code;   
                            $attendance->time_in = date('Y-m-d H:i:s',strtotime($att->datetime));
                            $attendance->device_in = $att->location ." - ".$att->ip_address;
                            $attendance->last_id = $att->id;
                            $attendance->save();
                        }
                    }
                    else 
                    {
                        $time_in_after = date('Y-m-d H:i:s',strtotime($att->datetime));
                        $time_in_before = date('Y-m-d H:i:s', strtotime ( '-23 hour' , strtotime ( $time_in_after ) )) ;
                        
                        $update = [
                            'time_out' =>  date('Y-m-d H:i:s', strtotime($att->datetime)),
                            'device_out' => $att->location ." - ".$att->ip_address,
                            'last_id' =>$att->id,
                        ];
                    
                        $attendance_in = Attendance::where('employee_code',$att->emp_code)
                            ->whereBetween('time_in',[$time_in_before,$time_in_after])
                            ->first();
                        
                        Attendance::where('employee_code',(string)$att->emp_code)
                        ->whereBetween('time_in',[$time_in_before,$time_in_after])
                        ->update($update);
                        
                        if($attendance_in == null)
                        {
                            $attendance = new Attendance;
                            $attendance->employee_code  = $att->emp_code;   
                            $attendance->time_out = date('Y-m-d H:i:s', strtotime($att->datetime));
                            $attendance->device_out = $att->location ." - ".$att->ip_address;
                            $attendance->last_id = $att->id;
                            $attendance->save(); 
                        }
                    }
                }

                Alert::success("Successfully Sync")->persistent('Dismiss');
            }
            else 
            {
                Alert::error("Cannot Sync. Because the employee is not existing in attendance logs")->persistent('Dismiss');
            }
            
            return back();
    }
}