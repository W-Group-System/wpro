<?php

namespace App\Http\Controllers;

use App\AttendanceDetailedReport;
use App\Company;
use App\Department;
use App\DtrCorrection;
use Illuminate\Http\Request;
use App\Employee;
use App\EmployeeLeave;
use App\EmployeeOb;
use App\EmployeeWfh;
use App\EmployeeOvertime;
use App\EmployeeDtr;
use App\Leave;
use App\Timekeeping;
use App\TimekeepingPosted;
use RealRashid\SweetAlert\Facades\Alert;

class TimekeepingDashboardController extends Controller
{
    public function index(Request $request){

        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $allowed_locations = getUserAllowedLocations(auth()->user()->id);
        $allowed_projects = getUserAllowedProjects(auth()->user()->id);

        $companies = Company::whereHas('employee_has_company')
                                ->whereIn('id',$allowed_companies)
                                ->get();
                                $company = isset($request->company) ? $request->company : "";                        
        $from = isset($request->from) ? $request->from : "";
        $to =  isset($request->to) ? $request->to : "";
        $status =  isset($request->status) ? $request->status : "";
        $leave_types = Leave::all();
        $leaves = EmployeeLeave::with('approver.approver_info','user')
                                // ->whereHas('employee',function($q) use($allowed_companies){
                                //     $q->whereIn('company_id',$allowed_companies);
                                // })
                                ->whereHas('employee', function ($q) use ($company) {
                                    if ($company) {
                                        $q->where('company_id', $company);
                                    }
                                })
                                ->when($allowed_locations,function($w) use($allowed_locations){
                                    $w->whereHas('employee',function($q) use($allowed_locations){
                                        $q->whereIn('location',$allowed_locations);
                                    });
                                })
                                ->when($allowed_projects,function($w) use($allowed_projects){
                                    $w->whereHas('employee',function($q) use($allowed_projects){
                                        $q->whereIn('project',$allowed_projects);
                                    });
                                })
                                ->where(function ($query) use ($status) {
                                    if ($status == 'All') {
                                        $query->whereIn('status', ['Approved', 'Pending', 'Declined', 'Cancelled']);
                                    } else {
                                        $query->where('status', $status);
                                    }
                                })
                                // ->where('status',$status)
                                ->whereDate('date_from','>=',$from)
                                ->whereDate('date_from','<=',$to)
                                ->orderBy('created_at','DESC')
                                ->get();
        $obs = EmployeeOb::with('approver.approver_info','user')
                                // ->whereHas('employee',function($q) use($allowed_companies){
                                //     $q->whereIn('company_id',$allowed_companies);
                                // })
                                ->whereHas('employee', function ($q) use ($company) {
                                    if ($company) {
                                        $q->where('company_id', $company);
                                    }
                                })
                                ->when($allowed_locations,function($w) use($allowed_locations){
                                    $w->whereHas('employee',function($q) use($allowed_locations){
                                        $q->whereIn('location',$allowed_locations);
                                    });
                                })
                                ->when($allowed_projects,function($w) use($allowed_projects){
                                    $w->whereHas('employee',function($q) use($allowed_projects){
                                        $q->whereIn('project',$allowed_projects);
                                    });
                                })
                                ->where(function ($query) use ($status) {
                                    if ($status == 'All') {
                                        $query->whereIn('status', ['Approved', 'Pending', 'Declined', 'Cancelled']);
                                    } else {
                                        $query->where('status', $status);
                                    }
                                })
                                // ->where('status','Pending')
                                // ->where('status',$status)
                                ->whereDate('applied_date','>=',$from)
                                ->whereDate('applied_date','<=',$to)
                                ->orderBy('created_at','DESC')
                                ->get();
        
        $wfhs = EmployeeWfh::with('approver.approver_info','user')
                                ->whereHas('employee',function($q) use($allowed_companies){
                                    $q->whereIn('company_id',$allowed_companies);
                                })
                                ->when($allowed_locations,function($w) use($allowed_locations){
                                    $w->whereHas('employee',function($q) use($allowed_locations){
                                        $q->whereIn('location',$allowed_locations);
                                    });
                                })
                                ->when($allowed_projects,function($w) use($allowed_projects){
                                    $w->whereHas('employee',function($q) use($allowed_projects){
                                        $q->whereIn('project',$allowed_projects);
                                    });
                                })
                                ->where(function ($query) use ($status) {
                                    if ($status == 'All') {
                                        $query->whereIn('status', ['Approved', 'Pending', 'Declined', 'Cancelled']);
                                    } else {
                                        $query->where('status', $status);
                                    }
                                })
                                // ->where('status','Pending')
                                // ->where('status',$status)
                                ->whereDate('applied_date','>=',$from)
                                ->whereDate('applied_date','<=',$to)
                                ->orderBy('created_at','DESC')
                                ->get();
        
        $overtimes = EmployeeOvertime::with('approver.approver_info','user')
                                // ->whereHas('employee',function($q) use($allowed_companies){
                                //     $q->whereIn('company_id',$allowed_companies);
                                // })
                                ->whereHas('employee', function ($q) use ($company) {
                                    if ($company) {
                                        $q->where('company_id', $company);
                                    }
                                })
                                ->when($allowed_locations,function($w) use($allowed_locations){
                                    $w->whereHas('employee',function($q) use($allowed_locations){
                                        $q->whereIn('location',$allowed_locations);
                                    });
                                })
                                ->when($allowed_projects,function($w) use($allowed_projects){
                                    $w->whereHas('employee',function($q) use($allowed_projects){
                                        $q->whereIn('project',$allowed_projects);
                                    });
                                })
                                ->where(function ($query) use ($status) {
                                    if ($status == 'All') {
                                        $query->whereIn('status', ['Approved', 'Pending', 'Declined', 'Cancelled']);
                                    } else {
                                        $query->where('status', $status);
                                    }
                                })
                                // ->where('status','Pending')
                                // ->where('status',$status)
                                ->whereDate('ot_date','>=',$from)
                                ->whereDate('ot_date','<=',$to)
                                ->orderBy('created_at','DESC')
                                ->get();
        
        $dtrs = EmployeeDtr::with('approver.approver_info','user')
                                ->whereHas('employee',function($q) use($allowed_companies){
                                    $q->whereIn('company_id',$allowed_companies);
                                })
                                ->when($allowed_locations,function($w) use($allowed_locations){
                                    $w->whereHas('employee',function($q) use($allowed_locations){
                                        $q->whereIn('location',$allowed_locations);
                                    });
                                })
                                ->when($allowed_projects,function($w) use($allowed_projects){
                                    $w->whereHas('employee',function($q) use($allowed_projects){
                                        $q->whereIn('project',$allowed_projects);
                                    });
                                })
                                ->where(function ($query) use ($status) {
                                    if ($status == 'All') {
                                        $query->whereIn('status', ['Approved', 'Pending']);
                                    } else {
                                        $query->where('status', $status);
                                    }
                                })
                                // ->where('status','Pending')
                                // ->where('status',$status)
                                ->whereDate('dtr_date','>=',$from)
                                ->whereDate('dtr_date','<=',$to)
                                ->orderBy('created_at','DESC')
                                ->get();
        
        $emp_data = Employee::select('id','user_id','employee_number','first_name','last_name')->with(['attendances' => function ($query) use ($from, $to) {
                                    $query->whereBetween('time_in', [$from." 00:00:01", $to." 23:59:59"])
                                    ->orWhereBetween('time_out', [$from." 00:00:01", $to." 23:59:59"])
                                    ->orderBy('time_in','asc')
                                    ->orderby('time_out','desc')
                                    ->orderBy('id','asc');
                            }])
                            ->whereIn('company_id',$allowed_companies)
                            ->when($allowed_locations,function($q) use($allowed_locations){
                                $q->whereIn('location',$allowed_locations);
                            })
                            ->when($allowed_projects,function($q) use($allowed_projects){
                                $q->whereIn('project',$allowed_projects);
                            })
                            ->get();
        
        $getLastCutOffDate = AttendanceDetailedReport::where('company_id', $request->company)->orderBy('id', 'desc')->first();

        return view('dashboards.timekeeping_dashboard', 
                    array(
                        'header' => 'Timekeeping',
                        'from' => $from,
                        'to' => $to,
                        'status' => $status,
                        'leaves' => $leaves,
                        'leave_types' => $leave_types,
                        'obs' => $obs,
                        'wfhs' => $wfhs,
                        'overtimes' => $overtimes,
                        'companies' => $companies,
                        'company' => $company,
                        'dtrs' => $dtrs,
                        'emp_data' => $emp_data,
                        'getLastCutOffDate' => $getLastCutOffDate
                    )
        );
    }

    public function reset_leave($id){
        $request = EmployeeLeave::where('id',$id)->first();
        $request->level = 0;
        $request->mail_1 = null;
        $request->mail_2 = null;
        $request->save();
        Alert::success('Successfully reset')->persistent('Dismiss');
        return back();
    }
    public function reset_ob($id){
        $request = EmployeeOb::where('id',$id)->first();
        $request->level = 0;
        $request->mail_1 = null;
        $request->mail_2 = null;
        $request->save();
        Alert::success('Successfully reset')->persistent('Dismiss');
        return back();
    }
    public function reset_wfh($id){
        $request = EmployeeWfh::where('id',$id)->first();
        $request->level = 0;
        $request->mail_1 = null;
        $request->mail_2 = null;
        $request->save();
        Alert::success('Successfully reset')->persistent('Dismiss');
        return back();
    }
    public function reset_ot($id){
        $request = EmployeeOvertime::where('id',$id)->first();
        $request->level = 0;
        $request->mail_1 = null;
        $request->mail_2 = null;
        $request->save();
        Alert::success('Successfully reset')->persistent('Dismiss');
        return back();
    }
    public function reset_dtr($id){
        $request = EmployeeDtr::where('id',$id)->first();
        $request->level = 0;
        $request->mail_1 = null;
        $request->mail_2 = null;
        $request->save();
        Alert::success('Successfully reset')->persistent('Dismiss');
        return back();
    }

    public function timekeeping(Request $request)
    {
        $from_date = date('Y-m-d', strtotime($request->date_from));
        $to_date = date('Y-m-d', strtotime($request->date_to));
        $company_data = $request->company;
        $department_data = $request->department;
        $employee_data = $request->employee;

        $companies = Company::where('id','!=',1)->get();
        $departments = Department::get();
        $employees = Employee::select('id','user_id','employee_code','first_name','last_name','schedule_id','employee_number','company_id','department_id')
            ->with(['schedule_info',
                'timekeeping_logs' => function($query)use($from_date,$to_date) {
                    $query->whereBetween('datetime', [$from_date." 00:00:00", $to_date." 23:59:59"])
                        ->orderBy('datetime','asc');
                }
            ])
            ->where('company_id', $request->company)
            ->where('department_id', $request->department)
            ->where('status','Active')
            ->orderBy('last_name','asc')
            ->get();
        // $employees = Employee::select('id','user_id','employee_code','first_name','last_name','schedule_id','employee_number','company_id','department_id')
        //     ->with(['schedule_info',
        //         'attendance_logs' => function($query)use($from_date,$to_date) {
        //             $query->whereBetween('datetime', [$from_date." 00:00:00", $to_date." 23:59:59"])
        //                 ->orderBy('datetime','asc');
        //         }
        //     ])
        //     ->where('company_id', $request->company)
        //     ->where('department_id', $request->department)
        //     ->where('status','Active')
        //     ->orderBy('last_name','asc')
        //     ->get();
        
        $attendance_controller = new AttendanceController;
        $date_range =  $attendance_controller->dateRange($from_date, $to_date);

        return view('timekeeping',
            array(
                'companies' => $companies,
                'departments'=> $departments,
                'employees' => $employees,
                'date_range' => $date_range,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'company_data' => $company_data,
                'department_data' => $department_data,
                'employee_data' => $employee_data
            )
        );
    }

    public function updateTimekeeping(Request $request,$id)
    {
        // dd($request->all(),$id);
        $dtr_correction = DtrCorrection::with('employee')->findOrFail($id);
        $dtr_correction->status = $request->status;
        $dtr_correction->save();
        // dd($dtr_correction);
        if ($request->status == "Approved")
        {
            $employees = Employee::where('id', $request->emp_id)->first();
            $timekeeping_in = Timekeeping::where('emp_code',$employees->employee_number)->where('date', $request->date)->orderBy('id','asc')->first();
            $timekeeping_out = Timekeeping::where('emp_code',$employees->employee_number)->where('date', $request->date)->orderBy('id','desc')->first();
            // dd($timekeeping_in, $timekeeping_out);
            if ($timekeeping_in && $timekeeping_out)
            {
                $timekeeping_in->datetime = $dtr_correction->time_in;
                $timekeeping_in->save();

                $timekeeping_out->datetime = $dtr_correction->time_out;
                $timekeeping_out->save();
            }
            else
            {
                $timekeeping = new Timekeeping;
                $timekeeping->emp_code = $dtr_correction->employee->employee_number;
                $timekeeping->date = $dtr_correction->date;
                $timekeeping->datetime = $dtr_correction->time_in;
                $timekeeping->save();
    
                $timekeeping = new Timekeeping;
                $timekeeping->emp_code = $dtr_correction->employee->employee_number;
                $timekeeping->date = $dtr_correction->date;
                $timekeeping->datetime = $dtr_correction->time_out;
                $timekeeping->save();
            }

        }
        
        // if ($request->has('attendance_logs_in') && $request->has('attendance_logs_out'))
        // {
        //     $timekeeping_timein = Timekeeping::findOrFail($request->attendance_logs_in);
        //     $timekeeping_timeout = Timekeeping::findOrFail($request->attendance_logs_out);

        //     $timekeeping_timein->datetime = $datetime_in;
        //     $timekeeping_timein->save();

        //     $timekeeping_timeout->datetime = $datetime_out;
        //     $timekeeping_timeout->save();
        // }
        // else
        // {
        //     $timekeeping = new Timekeeping;
        //     $timekeeping->emp_code = $employee->employee_number;
        //     $timekeeping->date = $request->date;
        //     $timekeeping->datetime = date('Y-m-d H:i:s', strtotime($datetime_in));
        //     $timekeeping->save();

        //     $timekeeping = new Timekeeping;
        //     $timekeeping->emp_code = $employee->employee_number;
        //     $timekeeping->date = $request->date;
        //     $timekeeping->datetime = date('Y-m-d H:i:s', strtotime($datetime_out));
        //     $timekeeping->save();
        // }

        Alert::success('Successfully Saved')->persistent('Dismiss');
        return back();
    }

    public function forApproval(Request $request)
    {
        // dd($request->all());
        $dtr_correction = new DtrCorrection;
        $dtr_correction->employee_id = $request->employee_id;
        $dtr_correction->date = $request->date;
        $dtr_correction->time_in = $request->date.' '.$request->employee_time_in;
        $dtr_correction->time_out = $request->date.' '.$request->employee_time_out;
        $dtr_correction->remarks = $request->remarks;
        $dtr_correction->status = 'Pending';
        // $dtr_correction->employee_id = $request->employee_id;
        if ($request->has('incident_report'))
        {
            $file = $request->file('incident_report');
            $name = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('incident_report'),$name);
            $file_name = '/incident_report/'.$name;
            $dtr_correction->file = $file_name;
        }
        $dtr_correction->save();

        Alert::success('Successfully Saved')->persistent('Dismiss');
        return back();
    }

    public function forApprovalView()
    {
        $dtr_corrections = DtrCorrection::with('employee')->get();

        return view('for_approval_dtr',
            array(
                'dtr_corrections' => $dtr_corrections
            )
        );
    }

    public function postDtr(Request $request)
    {
        foreach($request->employees as $employee_code => $employee)
        {
            foreach($employee as $date => $attendance)
            {
                $timekeeping = new TimekeepingPosted;
                $timekeeping->company_id = $attendance['company_id'] ?? null;
                $timekeeping->department_id = $attendance['department_id'] ?? null;
                $timekeeping->employee_no = $attendance['employee_no'] ?? null;
                $timekeeping->name = $attendance['name'] ?? null;
                $timekeeping->cut_off_date = $attendance['cutoff'] ?? null;
                $timekeeping->log_date = $attendance['log_date'] ?? null;
                $timekeeping->shift = $attendance['shift'] ?? null;
                $timekeeping->in = $attendance['in'] ?? null;
                $timekeeping->out = $attendance['out'] ?? null;
                $timekeeping->abs = $attendance['abs'] ?? null;
                $timekeeping->save();
            }
        }

        Alert::success('Successfully Saved')->persistent('Dismiss');
        return back();
    }
}
