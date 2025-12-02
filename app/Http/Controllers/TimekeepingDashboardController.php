<?php

namespace App\Http\Controllers;

use App\AttendanceDetailedReport;
use App\AttendanceLog;
use App\Company;
use App\Department;
use App\DtrApprover;
use App\DtrCorrection;
use App\DtrCorrectionApprover;
use App\DtrRevert;
use App\DtrStatus;
use Illuminate\Http\Request;
use App\Employee;
use App\EmployeeLeave;
use App\EmployeeOb;
use App\EmployeeWfh;
use App\EmployeeOvertime;
use App\EmployeeDtr;
use App\Leave;
use App\ScheduleData;
use App\Timekeeping;
use App\TimekeepingPosted;
use App\Helpers\HelperClass;
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

    public function updateTimekeeping(Request $request,$id)
    {
        // dd($request->all(),$id);
        $dtr_correction_approvers = DtrCorrectionApprover::where('dtr_correction_id', $id)->orderBy('id','asc')->get();
        // dd($dtr_correction_approvers);
        if ($request->status == "Approved")
        {
            if (count($dtr_correction_approvers->where('status','Waiting')->sortBy('id')) > 0)
            {
                foreach($dtr_correction_approvers as $key=>$approver)
                {
                    if ($key == 0)
                    {
                        $approver->status = "Approved";
                    }
                    else
                    {
                        $approver->status = "Pending";
                    }

                    $approver->save();
                }
            }
            else
            {
                $dtr_status = DtrStatus::where('date', $request->date)->where('employee_id', $request->emp_id)->first();
                if ($dtr_status)
                {
                    $dtr_status->status = 'For posting';
                    $dtr_status->save();
                }
                $dtr_correction_approvers = $dtr_correction_approvers->where('status','Pending')->last();
                $dtr_correction_approvers->status = $request->status;
                $dtr_correction_approvers->save();
                
                $dtr_correction = DtrCorrection::findOrFail($id);
                $dtr_correction->status = $request->status;
                $dtr_correction->save();

                // $employees = Employee::findOrFail($request->emp_id);
                // $timekeeping_in = AttendanceLog::where('emp_code',$employees->employee_number)->where('date', $request->date)->orderBy('id','asc')->first();
                // $timekeeping_out = AttendanceLog::where('emp_code',$employees->employee_number)->where('date', $request->date)->orderBy('id','desc')->first();
                
                // if ($timekeeping_in && $timekeeping_out)
                // {
                //     $timekeeping_in->datetime = $dtr_correction->time_in;
                //     $timekeeping_in->save();
    
                //     $timekeeping_out->datetime = $dtr_correction->time_out;
                //     $timekeeping_out->save();
                // }
                // else
                // {
                // }
                $timekeeping = new AttendanceLog;
                $timekeeping->emp_code = $dtr_correction->employee->employee_number;
                $timekeeping->date = $dtr_correction->date;
                $timekeeping->datetime = $dtr_correction->time_in;
                $timekeeping->save();
    
                $timekeeping = new AttendanceLog;
                $timekeeping->emp_code = $dtr_correction->employee->employee_number;
                $timekeeping->date = $dtr_correction->date;
                $timekeeping->datetime = $dtr_correction->time_out;
                $timekeeping->save();
            }

        }
        else 
        {
            foreach($dtr_correction_approvers as $key => $dtr_correction_approver)
            {
                if ($key == 0)
                {
                    $dtr_correction_approver->status = "Cancelled";
                }

                $dtr_correction_approver->save();
            }

            $dtr_correction = DtrCorrection::with('employee')->findOrFail($id);
            $dtr_correction->status = $request->status;
            $dtr_correction->save();
        }

        Alert::success('Successfully Saved')->persistent('Dismiss');
        return back();
    }

    public function forApproval(Request $request)
    {
        // dd($request->all());
        $dtr_correction = DtrCorrection::where('employee_id', $request->employee_id)->where('date', $request->date)->first();
        if ($dtr_correction)
        {
            $dtr_correction->date = $request->date;
            $dtr_correction->time_in = date('Y-m-d H:i:s', strtotime($request->employee_time_in));
            $dtr_correction->time_out = date('Y-m-d H:i:s', strtotime($request->employee_time_out));
            $dtr_correction->remarks = $request->remarks;
            $dtr_correction->status = 'Pending';
            if ($request->has('incident_report'))
            {
                $file = $request->file('incident_report');
                $name = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('incident_report'),$name);
                $file_name = '/incident_report/'.$name;
                $dtr_correction->file = $file_name;
            }
            $dtr_correction->save();
        }
        else
        {
            $dtr_correction = new DtrCorrection;
            $dtr_correction->employee_id = $request->employee_id;
            $dtr_correction->date = $request->date;
            $dtr_correction->time_in = date('Y-m-d H:i:s', strtotime($request->employee_time_in));
            $dtr_correction->time_out = date('Y-m-d H:i:s', strtotime($request->employee_time_out));
            $dtr_correction->remarks = $request->remarks;
            $dtr_correction->status = 'Pending';
            if ($request->has('incident_report'))
            {
                $file = $request->file('incident_report');
                $name = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('incident_report'),$name);
                $file_name = '/incident_report/'.$name;
                $dtr_correction->file = $file_name;
            }
            $dtr_correction->save();

        }
        
        $approvers = DtrApprover::orderBy('level','asc')->get();
        foreach($approvers as $key=>$approver)
        {
            $dtr_correction_approver = new DtrCorrectionApprover;
            $dtr_correction_approver->dtr_correction_id = $dtr_correction->id;
            $dtr_correction_approver->user_id = $approver->user_id;
            if($key == 0)
            {
                $dtr_correction_approver->status = "Pending";
            }
            else 
            {
                $dtr_correction_approver->status = "Waiting";
            }
            $dtr_correction_approver->save();
        }

        // $dtr_status = DtrStatus::where('employee_id', $request->employee_id)->where('date', $request->date)->first();
        // if ($dtr_status)
        // {
        //     $dtr_status->status = 'Pending';
        //     $dtr_status->save();
        // }

        // Alert::success('Successfully Saved')->persistent('Dismiss');
        // return back();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully Saved'
        ]);
    }

    public function forApprovalView()
    {
        $dtr_corrections = DtrCorrection::with('employee.user_info', 'dtr_correction_approver.user')->get();
        $headers = 'for_approval';

        return view('for_approval_dtr',
            array(
                'dtr_corrections' => $dtr_corrections,
                'header' => $headers
            )
        );
    }

    public function postDtr(Request $request)
    {
        // dd($request->all());
        foreach($request->employees as $employee_code => $employee)
        {
            foreach($employee as $date => $attendance)
            {
                if (isset($attendance['selected']))
                {
                    $timekeeping = new AttendanceDetailedReport;
                    $timekeeping->cut_off_date = $attendance['cutoff'] ?? null;
                    $timekeeping->log_date = $attendance['log_date'] ?? null;
                    $timekeeping->department_id = $attendance['department_id'] ?? null;
                    $timekeeping->shift = $attendance['shift'] ?? null;
                    $timekeeping->name = $attendance['name'] ?? null;
                    $timekeeping->company_id = $attendance['company_id'] ?? null;
                    $timekeeping->employee_no = $attendance['employee_no'] ?? null;
                    $timekeeping->in = $attendance['in'] ?? null;
                    $timekeeping->log_date = $attendance['log_date'] ?? null;
                    $timekeeping->shift = $attendance['shift'] ?? null;
                    $timekeeping->in = $attendance['in'] ?? null;
                    $timekeeping->out = $attendance['out'] ?? null;
                    $timekeeping->abs = $attendance['abs'] ?? null;
                    $timekeeping->reg_hrs = $attendance['reg_hrs'] ?? null;
                    $timekeeping->late_min = $attendance['late_min'] ?? null;
                    $timekeeping->undertime_min = $attendance['undertime_min'] ?? null;
                    $timekeeping->lv_w_pay = $attendance['lv_w_pay'] ?? null;
                    $timekeeping->reg_ot = $attendance['reg_ot'] ?? null;
                    $timekeeping->reg_nd = $attendance['reg_nd'] ?? null;
                    $timekeeping->reg_ot_nd = $attendance['reg_ot_nd'] ?? null;
                    $timekeeping->rst_ot = $attendance['rst_ot'] ?? null;
                    $timekeeping->rst_ot_over_eight = $attendance['rst_ot_over_eight'] ?? null;
                    $timekeeping->rst_nd = $attendance['rst_nd'] ?? null;
                    $timekeeping->rst_nd_over_eight = $attendance['rst_nd_over_eight'] ?? null;
                    $timekeeping->lh_ot = $attendance['lh_ot'] ?? null;
                    $timekeeping->lh_ot_over_eight = $attendance['lh_ot_over_eight'] ?? null;
                    $timekeeping->lh_nd = $attendance['lh_nd'] ?? null;
                    $timekeeping->lh_nd_over_eight = $attendance['lh_nd_over_eight'] ?? null;
                    $timekeeping->sh_ot = $attendance['sh_ot'] ?? null;
                    $timekeeping->sh_ot_over_eight = $attendance['sh_ot_over_eight'] ?? null;
                    $timekeeping->sh_nd = $attendance['sh_nd'] ?? null;
                    $timekeeping->sh_nd_over_eight = $attendance['sh_nd_over_eight'] ?? null;
                    $timekeeping->rst_lh_ot = $attendance['rst_lh_ot'] ?? null;
                    $timekeeping->rst_lh_ot_over_eight = $attendance['rst_lh_ot_over_eight'] ?? null;
                    $timekeeping->rst_lh_nd = $attendance['rst_lh_nd'] ?? null;
                    $timekeeping->rst_lh_nd_over_eight = $attendance['rst_lh_nd_over_eight'] ?? null;
                    $timekeeping->rst_sh_ot = $attendance['rst_sh_ot'] ?? null;
                    $timekeeping->rst_sh_ot_over_eight = $attendance['rst_sh_ot_over_eight'] ?? null;
                    $timekeeping->rst_sh_nd = $attendance['rst_sh_nd'] ?? null;
                    $timekeeping->rst_sh_nd_over_eight = $attendance['rst_sh_nd_over_eight'] ?? null;
                    $timekeeping->save();
                }
            }
        }

        Alert::success('Successfully Saved')->persistent('Dismiss');
        return back();
    }

    public function timekeepingOfficial(Request $request)
    {
        $header = 'timekeeping-official';
        $companies = Company::where('id','!=',1)->get();
        $departments = Department::get();

        return view('timekeeping.index',
            array(
                'header' => $header,
                'companies' => $companies,
                'departments'=> $departments,
            )
        );
    }

    public function issuesPerCompany(Request $request)
    {
        // dd($request->all());
        $from_date = $request->date_from;
        $to_date = $request->date_to;
        $date_from = date('Y-m-d', strtotime($from_date."-1 day"));
        $company_data = $request->company;
        $department_data = $request->department;

        $query = Employee::select('id','user_id','employee_code','first_name','last_name','schedule_id','employee_number','company_id','department_id');

            if ($search = $request->input('search.value'))
            {
                $query->where(function($query)use($search) {
                    $query->where('employee_code', "LIKE", "%".$search."%")
                        ->orWhere('last_name',"LIKE","%".$search."%")
                        ->orWhere('first_name',"LIKE","%".$search."%")
                        ;
                });
            }

            $query->with(['schedule_info'])
            ->with([
                'daily_schedules' => function($q) use ($date_from, $to_date) {
                    $q->whereBetween('log_date', [$date_from, $to_date]);
                }
            ])
            ->with(['dtr_correction.dtr_correction_approver.user'])
            ->with([
                'attendance_logs' => function($q) use ($date_from, $to_date) {
                    $q->select('id','emp_code','date','datetime')
                        ->whereBetween('datetime', [$date_from.' 00:00:01', date('Y-m-d 23:59:59', strtotime($to_date. '+1 day'))])
                        ->orderBy('datetime','asc');
                }
            ])
            ->with([
                'approved_ots' => function($q) use ($date_from, $to_date) {
                    $q->whereBetween('ot_date', [$date_from, $to_date])
                        ->where('status','Approved')
                        ->orderBy('ot_date','asc');
                }
            ])
            ->with(['approved_leaves' => function ($query) use ($date_from, $to_date) {
                $query->where(function ($q) use ($date_from, $to_date) {
                    $q->whereBetween('date_from', [$date_from, $to_date])
                        ->orWhereBetween('date_to', [$date_from, $to_date]);
                })
                ->where('status','Approved')
                ->orderBy('id','asc');
            },'approved_leaves.leave'])
            ->with(['approved_obs' => function ($query) use ($date_from, $to_date) {
                $query->whereBetween('applied_date', [$date_from, $to_date])
                ->where('status','Approved')
                ->orderBy('id','asc');
            }])
            ->where('company_id', $request->company)
            ->when($department_data, function($q)use($department_data) {
                $q->where('department_id', $department_data);
            })
            ->where('status','Active')
            // ->where('employee_code','A3174924')
            // ->where('employee_code','A3179024')
            // ->where('employee_code','A3191125')
            // ->where('employee_code','A192724')
            // ->whereIn('employee_code',['A3189225'])
            ->orderBy('last_name','asc');

        $recordsFiltered = $query->count();

        $employees = $query->offset($request->start)
            ->limit($request->length)
            ->get();
            
        // dd($employees);
        $attendance_controller = new AttendanceController;
        $date_range =  $attendance_controller->dateRange($from_date, $to_date);

        // $schedules = ScheduleData::get();
        $data=[];
        foreach($employees as $employee)
        {
            foreach($date_range as $date_r)
            {
                $total_reg_hrs = 0;
                $late = 0;
                $abs = 0;
                $undertime = 0;
                $leave = 0;
                $overtime = 0;
                $night_diff = 0;
                $night_diff_ot = 0;
                $restday_ot = 0;
                $restday_ot_ge = 0;
                $restnd = 0;
                $restnd_ge = 0;
                $lh_ot = 0;
                $lh_ot_ge = 0;
                $lh_nd = 0;
                $lh_nd_ge = 0;
                $sh_ot = 0;
                $sh_ot_ge = 0;
                $sh_ot_nd = 0;
                $sh_ot_nd_ge = 0;
                $rst_lh_ot= 0;
                $rst_lh_ot_ge= 0;
                $rst_lh_ot_nd= 0;
                $rst_lh_ot_nd_ge= 0;
                $rst_sh_ot= 0;
                $rst_sh_ot_ge= 0;
                $rst_sh_ot_nd= 0;
                $rst_sh_ot_nd_ge= 0;
                $plant_company = [5, 10, 11, 12];
                $leave_count=0;

                $rest = "";
                $ob_in = "";
                $ob_out = "";
                $final_time_in = "";
                $final_time_out = "";
                $nightdiff_start = "";
                $nightdiff_end = "";
                $schedule_display = "";
                $remarks="";

                $employee_schedule = HelperClass::employeeSchedule($employee->ScheduleData,$employee->daily_schedules,$date_r,$employee->schedule_id,$employee->employee_code);
                $check_if_holiday = checkIfHoliday(date('Y-m-d',strtotime($date_r)),$employee->location);
                $check_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);
                // Employee Schedule
                if($employee_schedule)
                {
                    if ($employee_schedule->time_in_from)
                    {
                        $schedule_display=date('h:i A', strtotime($employee_schedule->time_in_to)).'-'.date('h:i A', strtotime($employee_schedule->time_out_to));
                        if ($employee_schedule->time_in_from != $employee_schedule->time_in_to)
                        {
                            $schedule_display.="(Flexi)";
                        }
                    }
                }
                else 
                {
                    $rest="RESTDAY";
                    $schedule_display=$rest;
                }
            
                $convertedTimein = date('Y-m-d 00:00:00',strtotime($date_r));
                $convertedTimeout = date('Y-m-d 00:00:00',strtotime($date_r));
                if($employee_schedule)
                {
                    if($employee_schedule->time_in_from)
                    {
                        $convertedTimein = date('Y-m-d H:i:s',strtotime('-6 hours',strtotime($date_r." ".$employee_schedule->time_in_from)));
                    }

                    if ($employee_schedule->time_out_to < $employee_schedule->time_in_from)
                    {
                        $convertedTimeout = date('Y-m-d H:i:s', strtotime("+1 day", strtotime($date_r.' '.$employee_schedule->time_out_to.'+6 hours')));
                    }
                    else
                    {
                        $convertedTimeout = date('Y-m-d H:i:s', strtotime($date_r.' '.$employee_schedule->time_out_to.'+8 hours'));
                    }
                }
                $time_in = ($employee->attendance_logs)->whereBetween('datetime',[$convertedTimein, $date_r." 23:59:59"])->sortBy('datetime')->first();
                $time_out = ($employee->attendance_logs)->whereBetween('datetime',[$date_r." 23:59:59", $convertedTimeout])->sortByDesc('datetime')->first();
                if (empty($time_out))
                {
                    $time_out = ($employee->attendance_logs)->where('date', $date_r)->sortByDesc('datetime')->first();      
                }

                // Schedule
                if($employee_schedule)
                {
                    if ($employee_schedule->time_in_from == null)
                    {
                        $rest = "RESTDAY";
                    }
                    else 
                    {
                        $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to);
                        $schedule_out_from = strtotime($date_r." ".$employee_schedule->time_out_from);
                        $schedule_in = strtotime($date_r." ".$employee_schedule->time_in_to);
                        $schedule_in_from = strtotime($date_r." ".$employee_schedule->time_in_frpm);
                        if(($schedule_out) < ($schedule_in))
                        {
                            $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to)+86400;
                            $schedule_out_from = strtotime($date_r." ".$employee_schedule->time_out_from)+86400;
                        }
                    }
                }
                else
                {
                    $rest = "RESTDAY";
                }

                // Time in and Time out
                if ($time_in && $time_out)
                {
                    $final_time_in = $time_in->datetime;
                    $final_time_out = $time_out->datetime;
                }

                // Absent
                if ($time_in && $time_out)
                {
                    $abs = 0;
                }
                else
                {
                    $abs = 1;
                }

                // Display Restday
                if (empty($employee_schedule))
                {
                    $abs = 0;
                }
                else 
                {
                    if ($employee_schedule->time_in_from == null)
                    {
                        $abs = 0;
                    }
                }

                // Reg hrs
                $schedule_hrs = 0;
                if ($employee_schedule)
                {
                    if ($time_in && $time_out)
                    {
                        $schedule_in = strtotime($date_r.' '.$employee_schedule->time_in_to);
                        $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to);

                        if ($schedule_in > $schedule_out)
                        {
                            $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to)+86400;
                        }
                        
                        $schedule_hrs = ($schedule_out - $schedule_in) / 3600; // default working hours

                        $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                        if($if_has_ob)
                        {
                            if ($if_has_ob->date_from < $time_in->datetime)
                            {
                                $final_time_in = $if_has_ob->date_from;
                            }
                            if ($if_has_ob->date_to > $time_out->datetime) 
                            {
                                $final_time_out = $if_has_ob->date_to;
                            }
                        }
                        
                        $time_start = date('Y-m-d h:i A', strtotime($final_time_in));
                        $time_end = date('Y-m-d h:i A', strtotime($final_time_out));

                        $start_time = strtotime($time_start);
                        $end_time = strtotime($time_end);

                        if (strtotime($date_r." ".$employee_schedule->time_in_from) > $start_time)
                        {
                            $start_time = strtotime($date_r." ".$employee_schedule->time_in_from);
                        }
                        if ($end_time > $schedule_out)
                        {
                            $end_time = $schedule_out;
                        }
                        
                        $working_hrs = round((($end_time - $start_time)/3600), 2);
                        
                        if ($schedule_hrs > 8)
                        {
                            $schedule_hrs = $schedule_hrs-1;
                            if ($working_hrs >= ($schedule_hrs/1.5))
                            {
                                $working_hrs = $working_hrs-1;
                            }
                        }
                        else
                        {
                            $working_hrs = $working_hrs;
                        }
                        
                        if($working_hrs > $schedule_hrs)
                        {
                            $total_reg_hrs = $schedule_hrs;
                        }
                        else
                        {
                            if ($check_leave)
                            {
                                if ($working_hrs >= ($schedule_hrs/2))
                                {
                                    $total_reg_hrs = $schedule_hrs/2;
                                }
                            }
                            else 
                            {
                                $total_reg_hrs = $working_hrs;
                            }
                        }
                    }
                    else
                    {
                        $schedule_in = strtotime($date_r.' '.$employee_schedule->time_in_to);
                        $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to);
                        if ($schedule_in > $schedule_out)
                        {
                            $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to)+86400;
                        }
                        
                        $schedule_hrs = ($schedule_out - $schedule_in) / 3600; // default working hours

                        $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                        if($if_has_ob)
                        {
                            $final_time_in = $if_has_ob->date_from;
                            $final_time_out = $if_has_ob->date_to;

                            $start_time = strtotime($final_time_in);
                            $end_time = strtotime($final_time_out);

                            if (strtotime($date_r." ".$employee_schedule->time_in_from) > $start_time)
                            {
                                $start_time = strtotime($date_r." ".$employee_schedule->time_in_from);
                            }
                            if ($end_time > $schedule_out)
                            {
                                $end_time = $schedule_out;
                            }
                            
                            $working_hrs = round((($end_time - $start_time)/3600), 2);
                            if ($schedule_hrs > 8)
                            {
                                $schedule_hrs = $schedule_hrs-1;
                                if ($working_hrs >= ($schedule_hrs/1.5))
                                {
                                    $working_hrs = $working_hrs-1;
                                }
                            }
                            else
                            {
                                $working_hrs = $working_hrs;
                            }

                            if ($check_leave)
                            {
                                if($working_hrs >= $schedule_hrs)
                                {
                                    $total_reg_hrs = $schedule_hrs;
                                }
                            }
                            else
                            {
                                $total_reg_hrs = $working_hrs;
                            }
                        }
                    }
                }

                // Late
                if ($employee_schedule)
                {
                    if ($employee_schedule->time_in_from == null)
                    {
                        $late = 0;
                    }
                    else 
                    {
                        if ($time_in)
                        {
                            $in = strtotime(date('H:i',strtotime($final_time_in)));
                            $schedule_in = strtotime(date('H:i',$schedule_in));
                            if ($in > $schedule_in)
                            {
                                $total_late = ($in - $schedule_in) / 60;
                                $late = $total_late;
                            }
                        }
                    }
                }
                else
                {
                    $late = 0;
                }

                // Undertime
                if ($employee_schedule)
                {
                    if ($time_out && $time_in)
                    {
                        $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                        if($if_has_ob)
                        {
                            if ($if_has_ob->date_from < $time_in->datetime)
                            {
                                $final_time_in = $if_has_ob->date_from;
                            }
                            
                            if ($if_has_ob->date_to > $time_out->datetime) 
                            {
                                $final_time_out = $if_has_ob->date_to;
                            }
                        }

                        $out = date('Y-m-d H:i:s', strtotime($time_out->datetime));
                        $in = date('Y-m-d H:i:s', strtotime($time_in->datetime));
                        
                        $estimated_out = "";
                        if (date('H:i', strtotime($in)) > $employee_schedule['time_in_to'])
                        {
                            $estimated_out = $employee_schedule['time_out_to'];
                        }
                        elseif(date('H:i', strtotime($in)) < $employee_schedule['time_in_from'])
                        {
                            $estimated_out = $employee_schedule['time_out_from'];
                        }
                        else
                        {
                            $hours = intval($employee_schedule['working_hours']);
                            $minutes = ($employee_schedule['working_hours']-$hours)*60;
                            $estimated_out = date('h:i A', strtotime("+".$hours." hours",strtotime($time_in->datetime)));
                            $estimated_out = date('h:i A', strtotime("+".$minutes." minutes",strtotime($estimated_out)));
                        }
                        
                        // $out_timestamp = strtotime($out);
                        // $estimated_out_timestamp = strtotime($date_r.' '.$estimated_out);
                        // if ($out_timestamp < $estimated_out_timestamp)
                        // {
                        //     $total_undertime = ($estimated_out_timestamp - $out_timestamp) / 60;
                        //     $undertime = $total_undertime;
                        // }
                        $schedule_in = strtotime($date_r.' '.$employee_schedule->time_in_to);
                        $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to);
                        if ($schedule_in > $schedule_out)
                        {
                            $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to)+86400;
                        }
                        $schedule_hrs = ($schedule_out - $schedule_in) / 3600; // default working hours

                        if(($schedule_hrs-1) > $total_reg_hrs)
                        {
                            $undertime_hrs = (double) number_format(($schedule_hrs-1) - $total_reg_hrs,2);
                            $undertime = ($undertime_hrs)*60;
                        }
                    }
                }

                // Leave w/ pay
                $check_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);
                $leave_count = 0;
                if ($check_leave)
                {
                    $leave = explode("-", $check_leave);
                    
                    if ($leave[0] == "LWOP")
                    {
                        if ($leave[1] == 0.5)
                        {
                            $abs=$leave[1];
                            $leave_count=(float)$leave[1];
                            $leave=0;
                        }
                        else 
                        {
                            $abs=1;
                            $leave_count=0;
                            $leave=0;
                        }

                        $schedule_hrs = $employee_schedule->working_hours;
                        if(($schedule_hrs/2) >= 4.75)
                        {
                            $undertime=0;
                        }
                    }
                    else
                    {
                        if ($leave[1] == 0.5)
                        {
                            $abs=$leave[1];
                            $leave_count=(float)$leave[1];
                            $leave=$leave_count;
                            $undertime=0;
                        }
                        else 
                        {
                            $abs=$leave[1];
                            $leave_count=(float)$leave[1];
                            $leave=$leave_count;
                        }
                    }
                }
                else
                {
                    $leave = 0;
                }

                // REG OT
                $emp_has_ot = employeeHasOTDetails($employee->approved_ots,date('Y-m-d',strtotime($date_r)));
                if ($rest == "RESTDAY")
                {
                    $overtime = 0;
                }
                else
                {
                    if (empty($check_if_holiday))
                    {
                        if ($emp_has_ot)
                        {
                            if ($emp_has_ot < 8)
                            {
                                $original_sched = $employee_schedule['working_hours'];
                                $work_ot = round(((strtotime($final_time_out) - strtotime($final_time_in)) / 3600), 2)-$original_sched;
                                // dd($work_ot);
                                if ($work_ot >= 2 && $emp_has_ot >= 2)
                                {
                                    if ($work_ot <= $emp_has_ot)
                                    {
                                        $overtime = $work_ot;
                                    }
                                    else 
                                    {
                                        $overtime = $emp_has_ot;
                                    }
                                }
                                else 
                                {
                                    if (in_array($employee->company_id, $plant_company))
                                    {
                                        if ($work_ot <= $emp_has_ot)
                                        {
                                            $overtime = $work_ot;
                                        }
                                        else 
                                        {
                                            $overtime = $emp_has_ot;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $overtime = floatval($emp_has_ot) - 1;
                            }
                        }
                    }
                }
                
                // OB
                $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                if($if_has_ob)
                {
                    if ($time_in && $time_out)
                    {
                        if ($if_has_ob->date_from < $time_in->datetime)
                        {
                            $ob_in = $if_has_ob->date_from;
                            $final_time_in = $ob_in;
                        }
                        if ($if_has_ob->date_to > $time_out->datetime) 
                        {
                            $ob_out = $if_has_ob->date_to;
                            $final_time_out = $ob_out;
                        }
                    }
                    else
                    {
                        $ob_in = $if_has_ob->date_from;
                        $final_time_in = $ob_in;

                        $ob_out = $if_has_ob->date_to;
                        $final_time_out = $ob_out;
                    }

                    $undertime = 0;
                    $abs = 0;
                }
                
                // ND
                $nightdiff_start = $final_time_in;
                $nightdiff_end = $final_time_out;
                if($employee_schedule)
                {
                    if (empty($check_if_holiday))
                    {
                        $start_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_in_to);
                        $end_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_out_to);
                        
                        if(strtotime($start_schedule) > strtotime($end_schedule))
                        {
                            $s = date('Y-m-d', strtotime($final_time_in . ' +1 day'));
                            $end_schedule = date('Y-m-d H:i', strtotime($s." ".$employee_schedule->time_out_to));
                        }
                        
                        if(strtotime($start_schedule) > strtotime($final_time_in))
                        {   
                            $nightdiff_start = $start_schedule;
                        }
                        if(strtotime($end_schedule) < strtotime($final_time_out))
                        {   
                            $nightdiff_end = $end_schedule;
                        }
                        
                        $night_diff = night_difference_per_company($nightdiff_start,$nightdiff_end);
                        $schedule_hours = (strtotime($end_schedule)-strtotime($start_schedule))/3600;
                        if($schedule_hours > 8)
                        {
                            if($night_diff >= 5)
                            {
                                $night_diff = $night_diff - 1;
                            }
                        }

                        // REG OT ND
                        if(empty($check_if_holiday))
                        {
                            if($night_diff < 7)
                            {
                                $actual_night_diff = night_difference_per_company($nightdiff_start,$nightdiff_end);
                                $night_diff_ot = night_difference_per_company($final_time_in,$final_time_out)-$actual_night_diff;
                            }
                        }

                        if ($night_diff_ot < .5)
                        {
                            $night_diff_ot = 0;
                        }
                    }
                }

                // RST OT
                if ($rest == "RESTDAY")
                {
                    if (empty($check_if_holiday))
                    {
                        if ($emp_has_ot)
                        {
                            $work_ot = round(((strtotime($final_time_out) - strtotime($final_time_in)) / 3600), 2);
                            $break_hrs = ($employee->approved_ots)->first();
                            if ($break_hrs)
                            {
                                $work_ot = $work_ot-$break_hrs->break_hrs;
                            }
                            if ($work_ot >= 2)
                            {
                                if ($work_ot > $emp_has_ot)
                                {
                                    $restday_ot = 8;
                                    if ($emp_has_ot >= 8)
                                    {
                                        $restday_ot = $restday_ot;
                                        $restday_ot_ge = floatval($emp_has_ot)-floatval($restday_ot);
                                    }
                                    else 
                                    {
                                        $restday_ot = $emp_has_ot;
                                    }
                                }
                                else 
                                {
                                    if ($work_ot > 8)
                                    {
                                        $restday_ot = $restday_ot;
                                        $restday_ot_ge = floatval($work_ot)-floatval($restday_ot);
                                    }
                                    else 
                                    {
                                        $restday_ot = $work_ot;
                                    }
                                }
                            }
                            else 
                            {
                                if (in_array($employee->company_id, $plant_company))
                                {
                                    if ($work_ot <= $emp_has_ot)
                                    {
                                        $overtime = $work_ot;
                                    }
                                    else 
                                    {
                                        $overtime = $emp_has_ot;
                                    }
                                }
                            }
                        }
                    }
                }

                // RST ND
                if ($rest == "RESTDAY")
                {
                    if (empty($rest))
                    {
                        if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                        {
                            $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                            $restnd =  night_difference_per_company($final_time_in,$final_time_out);
                            if($work_rest > 9 )
                            { 
                                $restnd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                $restnd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                $restnd_ge = $restnd_ge - $restnd;
                                $restnd = $restnd-1;
                                if($restnd <0)
                                {
                                    $restnd = 0;
                                }
                                if($restnd_ge <0)
                                {
                                    $restnd_ge = 0;
                                }
                            }
                        }
                    }
                }

                // Holiday OT's
                $check_if_holiday = checkIfHoliday(date('Y-m-d',strtotime($date_r)),$employee->location);
                if ($check_if_holiday)
                {
                    $abs=0;
                    $undertime=0;
                    $overtime=0;
                    if ($employee_schedule)
                    {
                        // $if_attendance_holiday = checkHasAttendanceHoliday(date('Y-m-d',strtotime($date_r)), $employee->employee_number,$employee->location);
                        // $check_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r.'-1 day')),$employee_schedule);
                        // if ($check_leave)
                        // {
                        //     // $if_attendance_holiday_status = 'With-Pay';
                        //     if(str_contains($check_leave,"Without")){
                        //         $if_attendance_holiday_status = 'Without-Pay';
                        //         $abs = 1;

                        //         $emp_schedule = $employee_schedule->working_hours-1;
                        //         $time_in = ($employee->attendance_logs)->sortBy('datetime')->first();
                        //         $time_out = ($employee->attendance_logs)->sortByDesc('datetime')->first();
                        //         $total_reg_hrs = number_format((strtotime($time_out->datetime) - strtotime($time_in->datetime))/3600, 2);
                        //         if ($total_reg_hrs >= ($emp_schedule/2))
                        //         {
                        //             $abs=0;
                        //             if ($employee_schedule->working_hours > 8) 
                        //             {
                        //                 $total_reg_hrs = $employee_schedule->working_hours-1;
                        //             }
                        //             else 
                        //             {
                        //                 $total_reg_hrs = $employee_schedule->working_hours;
                        //             }
                        //         }
                        //     }
                        //     else
                        //     {
                        //         $if_attendance_holiday_status = 'With-Pay';
                        //         if(str_contains($check_leave,".5") || str_contains($check_leave,"1"))
                        //         {
                        //             $abs = 0;
                        //         }
                        //     }
                        // }
                        // else
                        // {
                        //     $attendance = ($employee->attendance_logs)->map(function($item) {
                        //         return [
                        //             'time_in' => $item->datetime
                        //         ];
                        //     });

                        //     $check_attendance = checkHasAttendanceHolidayStatus($attendance,$if_attendance_holiday);
                        //     if(empty($check_attendance))
                        //     {
                        //         // $is_absent = 'Absent';
                        //         $abs = 1;
                        //     }else{
                        //         $if_attendance_holiday_status = 'With-Pay';
                        //         $abs = 0;
                        //     }
                        // }

                        $approved_ot_hrs = employeeHasOTDetails($employee->approved_ots,date('Y-m-d',strtotime($date_r)));
                        // SH OT
                        if ($check_if_holiday == "Special Holiday")
                        {
                            if ($rest == "RESTDAY")
                            {
                                $rst_sh_ot = 8;
                                if ($approved_ot_hrs > 8)
                                {
                                    $rst_sh_ot = $rst_sh_ot;
                                    $rst_sh_ot_ge = floatval($approved_ot_hrs) - 8;
                                }
                                else
                                {
                                    $rst_sh_ot = $approved_ot_hrs;
                                }
                            }
                            else 
                            {
                                $sh_ot = 8;
                                if ($approved_ot_hrs > 8)
                                {
                                    $sh_ot = $sh_ot;
                                    $sh_ot_ge = floatval($approved_ot_hrs) - 8;
                                }
                                else
                                {
                                    $sh_ot = $approved_ot_hrs;
                                }
                            }
    
                            $start_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_in_to);
                            $end_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_out_to);
                            if(strtotime($start_schedule) > strtotime($end_schedule))
                            {
                                $s = date('Y-m-d', strtotime($final_time_in . ' +1 day'));
                                $end_schedule = date('Y-m-d H:i', strtotime($s." ".$employee_schedule->time_out_to));
                            }
                            if(strtotime($start_schedule) > strtotime($final_time_in))
                            {   
                                $nightdiff_start = $start_schedule;
                            }
                            if(strtotime($end_schedule) < strtotime($final_time_out))
                            {   
                                $nightdiff_end = $end_schedule;
                            }
                            
                            if ($rest == "RESTDAY")
                            {
                                $rst_sh_ot_nd = night_difference_per_company($nightdiff_start,$nightdiff_end);
                                if($rst_sh_ot_nd >= 4.5)
                                {
                                    $rst_sh_ot_nd = $rst_sh_ot_nd-1;
                                }
                                if ($rst_sh_ot_nd > $sh_ot)
                                {
                                    $rst_sh_ot_nd = $sh_ot;
                                }

                                $time_start_string = strtotime($time_start);
                                $time_end_string = strtotime($time_end);
                                $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to);
                                $schedule_in = strtotime($date_r." ".$employee_schedule->time_in_to);
                                
                                if(($schedule_out) < ($schedule_in))
                                {
                                    
                                    $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to)+86400;
                                }
                                if($time_end_string>$schedule_out)
                                {
                                    $rst_sh_ot_nd =  night_difference_per_company(date('Y-m-d H:i',$schedule_in),date('Y-m-d H:i',$schedule_out));
                                    $sh_ot_use = $rst_sh_ot_nd;
                                    if($rst_sh_ot_nd >=4.5 )
                                    {   
                                        $schedule_hours = ((($schedule_out)-($schedule_in))/3600);
                                        if($schedule_hours > 8)
                                        {
                                            $rst_sh_ot_nd = $rst_sh_ot_nd-1;
                                        }
                                    }

                                    $rst_sh_ot_nd_ge =night_difference_per_company(date('Y-m-d H:i',$schedule_in),$time_end)-$sh_ot_use;
                                    $rst_sh_ot_nd_ge = $rst_sh_ot_nd_ge;
                                    if($rst_sh_ot_nd_ge <0)
                                    {
                                        $rst_sh_ot_nd_ge=0;
                                    }
                                    
                                }
                                else {
                                    $rst_sh_ot_nd =  night_difference_per_company(date('Y-m-d H:i',$schedule_in),$time_end);
                                    if($rst_sh_ot_nd >=4.5 )
                                    {   
                                        if($schedule_hours > 8)
                                        {
                                        $rst_sh_ot_nd = $rst_sh_ot_nd-1;
                                        }
                                    }
                                }
                            }
                            else 
                            {
                                $sh_ot_nd = night_difference_per_company($nightdiff_start,$nightdiff_end);
                                if($sh_ot_nd >= 4.5)
                                {
                                    $sh_ot_nd = $sh_ot_nd-1;
                                }
                                if ($sh_ot_nd > $sh_ot)
                                {
                                    $sh_ot_nd = $sh_ot;
                                }

                                $time_start_string = strtotime($time_start);
                                $time_end_string = strtotime($time_end);
                                $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to);
                                $schedule_in = strtotime($date_r." ".$employee_schedule->time_in_to);
                                
                                if(($schedule_out) < ($schedule_in))
                                {
                                    
                                    $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to)+86400;
                                }
                                if($time_end_string>$schedule_out)
                                {
                                    $sh_ot_nd =  night_difference_per_company(date('Y-m-d H:i',$schedule_in),date('Y-m-d H:i',$schedule_out));
                                    $sh_ot_use = $sh_ot_nd;
                                    if($sh_ot_nd >=4.5 )
                                    {   
                                        $schedule_hours = ((($schedule_out)-($schedule_in))/3600);
                                        if($schedule_hours > 8)
                                        {
                                            $sh_ot_nd = $sh_ot_nd-1;
                                        }
                                    }

                                    $sh_ot_nd_ge =night_difference_per_company(date('Y-m-d H:i',$schedule_in),$time_end)-$sh_ot_use;
                                    $sh_ot_nd_ge = $sh_ot_nd_ge;
                                    if($sh_ot_nd_ge <0)
                                    {
                                        $sh_ot_nd_ge=0;
                                    }
                                    
                                }
                                else {
                                    $sh_ot_nd =  night_difference_per_company(date('Y-m-d H:i',$schedule_in),$time_end);
                                    if($sh_ot_nd >=4.5 )
                                    {   
                                        if($schedule_hours > 8)
                                        {
                                        $sh_ot_nd = $sh_ot_nd-1;
                                        }
                                    }
                                }
                            }
                        }
                        else
                        {
                            if ($rest == "RESTDAY")
                            {
                                $rst_lh_ot = 8;
                                if ($approved_ot_hrs > 8)
                                {
                                    $rst_lh_ot = $rst_lh_ot;
                                    $lh_ot_ge = floatval($approved_ot_hrs) - 8;
                                }
                                else
                                {
                                    $rst_lh_ot = $approved_ot_hrs;
                                }
                            }
                            else 
                            {
                                $lh_ot = 8;
                                if ($approved_ot_hrs > 8)
                                {
                                    $lh_ot = $lh_ot;
                                    $lh_ot_ge = floatval($approved_ot_hrs) - 8;
                                }
                                else
                                {
                                    $lh_ot = $approved_ot_hrs;
                                }
                            }
                            
                            if ($employee_schedule)
                            {
                                $start_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_in_to);
                                $end_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_out_to);
                                
                                if(strtotime($start_schedule) > strtotime($end_schedule))
                                {
                                    $s = date('Y-m-d', strtotime($final_time_in . ' +1 day'));
                                    $end_schedule = date('Y-m-d H:i', strtotime($s." ".$employee_schedule->time_out_to));
                                }
                                
                                if(strtotime($start_schedule) > strtotime($final_time_in))
                                {   
                                    $nightdiff_start = $start_schedule;
                                }
                                if(strtotime($end_schedule) < strtotime($final_time_out))
                                {   
                                    $nightdiff_end = $end_schedule;
                                }
                            }
                            
                            if ($rest == "RESTDAY")
                            {
                                if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                                {
                                    $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                                    $lh_nd_ge =  night_difference_per_company($final_time_in,$final_time_out);
                                    if($work_rest > 9 )
                                    { 
                                        $rst_lh_nd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                        $rst_lh_nd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                        $rst_lh_nd = $rst_lh_nd_ge - $rst_lh_nd;
                                        $rst_lh_nd = $rst_lh_nd-1;
                                        if($rst_lh_nd <0)
                                        {
                                            $rst_lh_nd = 0;
                                        }
                                        if($rst_lh_nd_ge <0)
                                        {
                                            $rst_lh_nd_ge = 0;
                                        }
                                    }
                                }
                            }
                            else 
                            {
                                if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                                {
                                    $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                                    $lh_nd_ge =  night_difference_per_company($final_time_in,$final_time_out);
                                    if($work_rest > 9 )
                                    { 
                                        $lh_nd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                        $lh_nd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                        $lh_nd = $lh_nd_ge - $lh_nd;
                                        $lh_nd = $lh_nd-1;
                                        if($lh_nd <0)
                                        {
                                            $lh_nd = 0;
                                        }
                                        if($lh_nd_ge <0)
                                        {
                                            $lh_nd_ge = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }

                }

                if ($total_reg_hrs <= 0)
                {
                    $total_reg_hrs = 0;
                }

                // Remarks
                $leave_count = 0;
                $abs_half = 0;
                if($if_has_ob)
                {
                    $remarks = 'OB';
                }
                else 
                {
                    $if_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);
                    if($if_leave)
                    {
                        $l = explode('-',$if_leave);
                        $leave_count = (double) $l[1];
                        if(str_contains($if_leave,"Without"))
    
                        {
                            $leave_count = 0;
                            $abs_half = $l[1];
                        }
                    }
                    $remarks = $if_leave;
                }

                $pending_dtr = count(($employee->dtr_correction)->where('date', $date_r)->where('status','Pending'));
                $revert = count(($employee->dtr_status)->where('date',$date_r)->where('status','Revert'));

                $approved_dtr = count(($employee->dtr_correction)->where('date',$date_r)->where('status','Approved'));
                $cancelled_dtr = count(($employee->dtr_correction)->where('date', $date_r)->where('status','Cancelled'));
                $for_posting = count(($employee->dtr_status)->where('date',$date_r)->where('status','For posting'));
                $posted_dtr = count(($employee->attendance_detailed_report)->where('log_date', $date_r));

                // if(($pending_dtr == 0) && ($for_posting == 0) && ($posted_dtr == 0) && (($abs > 0) || ($if_has_ob) || ($overtime > 0) || ($revert > 0) || ($cancelled_dtr > 0) || ($total_reg_hrs <= 3 && $rest != "RESTDAY" && $leave == 0 && $abs > 0)))
                // {
                // }

                $action = '';
                if ($revert > 0)
                {
                    $action = '
                        <button type="button" class="btn btn-sm btn-warning" id="editTimekeepingBtn" data-employee="'.$employee->id.'" data-date="'.$date_r.'">
                            <i class="ti-pencil"></i>
                            Edit
                        </button>
                    ';
                }
                else 
                {
                    $action = '
                        <button type="button" class="btn btn-sm btn-warning" id="editTimekeepingBtn" data-employee="'.$employee->id.'" data-date="'.$date_r.'">
                            <i class="ti-pencil"></i>
                            Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-success" id="moveToForPostingBtn" data-employee="'.$employee->id.'" data-date="'.$date_r.'">
                            <i class="ti-arrow-right"></i>
                            Move to for posting
                        </button>
                    ';
                }

                if (($pending_dtr == 0) && ($for_posting == 0) && ($abs > 0) || ($overtime > 0) || ($revert > 0) || ($cancelled_dtr > 0) || ($if_has_ob))
                {
                    $data[]=[
                        'action' => $action,
                        'company' => $employee->company->company_code,
                        'employee_code' => $employee->employee_code,
                        'name' => $employee->last_name.', '.$employee->first_name,
                        'date' => $date_r,
                        'schedule' => $schedule_display,
                        'time_in' => $final_time_in ? date('h:i A', strtotime($final_time_in)) : '',
                        'time_out' => $final_time_out ? date('h:i A', strtotime($final_time_out)) : '',
                        'abs' => number_format($abs, 2),
                        'reg_hrs' => round($total_reg_hrs,2),
                        'late' => $late,
                        'undertime' => number_format($undertime,2),
                        'leave' => number_format($leave,2),
                        'leave_count' => $leave_count,
                        'overtime'=> number_format($overtime,2),
                        'reg_nd'=> number_format($night_diff,2),
                        'reg_ot_nd'=> number_format($night_diff_ot,2),
                        'restday_ot'=> number_format($restday_ot,2),
                        'restday_ot_ge'=> number_format($restday_ot_ge,2),
                        'restnd'=> number_format($restnd,2),
                        'restnd_ge'=> number_format($restnd_ge,2),
                        'lh_ot'=> number_format($lh_ot,2),
                        'lh_ot_ge'=> number_format($lh_ot_ge,2),
                        'lh_nd'=> number_format($lh_nd,2),
                        'lh_nd_ge'=> number_format($lh_nd_ge,2),
                        'sh_ot'=> number_format($sh_ot,2),
                        'sh_ot_ge'=> number_format($sh_ot_ge,2),
                        'sh_ot_nd'=> number_format($sh_ot_nd,2),
                        'sh_ot_nd_ge'=> number_format($sh_ot_nd_ge,2),
                        'rst_lh_ot'=> number_format($rst_lh_ot,2),
                        'rst_lh_ot_ge'=> number_format($lh_ot_ge,2),
                        'rst_lh_ot_nd'=> number_format($rst_lh_ot_nd,2),
                        'rst_lh_ot_nd_ge'=> number_format($rst_lh_ot_nd_ge,2),
                        'rst_sh_ot'=> number_format($rst_sh_ot,2),
                        'rst_sh_ot_ge'=> number_format($rst_sh_ot_ge,2),
                        'rst_sh_ot_nd'=> number_format($rst_sh_ot_nd,2),
                        'rst_sh_ot_nd_ge'=> number_format($rst_sh_ot_nd_ge,2),
                        'remarks' => $remarks,
                        'if_has_ob' => $if_has_ob ? 'Yes' : 'No',
                        'employee_id' => $employee->id
                    ];
                }

            }
        }
        
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => count($employees),
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    public function forPostingCompany(Request $request)
    {
        $from_date = $request->date_from;
        $to_date = $request->date_to;
        $date_from = date('Y-m-d', strtotime($from_date."-1 day"));
        $company_data = $request->company;
        $department_data = $request->department;

        $query = Employee::select('id','user_id','employee_code','first_name','last_name','schedule_id','employee_number','company_id','department_id');
            if ($search = $request->input('search.value'))
            {
                $query->where(function($query)use($search) {
                    $query->where('employee_code', "LIKE", "%".$search."%")
                        ->orWhere('last_name',"LIKE","%".$search."%")
                        ->orWhere('first_name',"LIKE","%".$search."%")
                        ;
                });
            }
            $query->with(['schedule_info'])
            ->with([
                'daily_schedules' => function($q) use ($date_from, $to_date) {
                    $q->whereBetween('log_date', [$date_from, $to_date]);
                }
            ])
            ->with(['dtr_correction.dtr_correction_approver.user'])
            ->with([
                'attendance_logs' => function($q) use ($date_from, $to_date) {
                    $q->select('id','emp_code','date','datetime')
                        ->whereBetween('datetime', [$date_from.' 00:00:01', date('Y-m-d 23:59:59', strtotime($to_date. '+1 day'))])
                        ->orderBy('datetime','asc');
                }
            ])
            ->with([
                'approved_ots' => function($q) use ($date_from, $to_date) {
                    $q->whereBetween('ot_date', [$date_from, $to_date])
                        ->where('status','Approved')
                        ->orderBy('ot_date','asc');
                }
            ])
            ->with(['approved_leaves' => function ($query) use ($date_from, $to_date) {
                $query->where(function ($q) use ($date_from, $to_date) {
                    $q->whereBetween('date_from', [$date_from, $to_date])
                        ->orWhereBetween('date_to', [$date_from, $to_date]);
                })
                ->where('status','Approved')
                ->orderBy('id','asc');
            },'approved_leaves.leave'])
            ->with(['approved_obs' => function ($query) use ($date_from, $to_date) {
                $query->whereBetween('applied_date', [$date_from, $to_date])
                ->where('status','Approved')
                ->orderBy('id','asc');
            }])
            ->where('company_id', $request->company)
            ->when($department_data, function($q)use($department_data) {
                $q->where('department_id', $department_data);
            })
            ->where('status','Active')
            // ->where('employee_code','A3189525')
            // ->where('employee_code','A3179024')
            // ->where('employee_code','A3191125')
            // ->where('employee_code','A192724')
            // ->whereIn('employee_code',['A3189225'])
            ->orderBy('last_name','asc');

        $recordsFiltered = $query->count();

        $employees = $query->offset($request->start)
            ->limit($request->length)
            ->get();
            
        // dd($employees);
        $attendance_controller = new AttendanceController;
        $date_range =  $attendance_controller->dateRange($from_date, $to_date);

        // $schedules = ScheduleData::get();
        $data=[];
        foreach($employees as $employee)
        {
            foreach($date_range as $date_r)
            {
                $total_reg_hrs = 0;
                $late = 0;
                $abs = 0;
                $undertime = 0;
                $leave = 0;
                $overtime = 0;
                $night_diff = 0;
                $night_diff_ot = 0;
                $restday_ot = 0;
                $restday_ot_ge = 0;
                $restnd = 0;
                $restnd_ge = 0;
                $lh_ot = 0;
                $lh_ot_ge = 0;
                $lh_nd = 0;
                $lh_nd_ge = 0;
                $sh_ot = 0;
                $sh_ot_ge = 0;
                $sh_ot_nd = 0;
                $sh_ot_nd_ge = 0;
                $rst_lh_ot= 0;
                $rst_lh_ot_ge= 0;
                $rst_lh_ot_nd= 0;
                $rst_lh_ot_nd_ge= 0;
                $rst_sh_ot= 0;
                $rst_sh_ot_ge= 0;
                $rst_sh_ot_nd= 0;
                $rst_sh_ot_nd_ge= 0;
                $plant_company = [5, 10, 11, 12];

                $rest = "";
                $ob_in = "";
                $ob_out = "";
                $final_time_in = "";
                $final_time_out = "";
                $nightdiff_start = "";
                $nightdiff_end = "";
                $schedule_display="";
                $remarks="";

                $employee_schedule = HelperClass::employeeSchedule($employee->ScheduleData,$employee->daily_schedules,$date_r,$employee->schedule_id,$employee->employee_code);
                $check_if_holiday = checkIfHoliday(date('Y-m-d',strtotime($date_r)),$employee->location);
                $check_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);

                if($employee_schedule)
                {
                    if ($employee_schedule->time_in_from)
                    {
                        $schedule_display=date('h:i A', strtotime($employee_schedule->time_in_to)).'-'.date('h:i A', strtotime($employee_schedule->time_out_to));
                        if ($employee_schedule->time_in_from != $employee_schedule->time_in_to)
                        {
                            $schedule_display.="(Flexi)";
                        }
                    }
                }
                else 
                {
                    $rest="RESTDAY";
                    $schedule_display=$rest;
                }
            
                $convertedTimein = date('Y-m-d 00:00:00',strtotime($date_r));
                $convertedTimeout = date('Y-m-d 00:00:00',strtotime($date_r));
                if($employee_schedule)
                {
                    if($employee_schedule->time_in_from)
                    {
                        $convertedTimein = date('Y-m-d H:i:s',strtotime('-6 hours',strtotime($date_r." ".$employee_schedule->time_in_from)));
                    }

                    if ($employee_schedule->time_out_to < $employee_schedule->time_in_from)
                    {
                        $convertedTimeout = date('Y-m-d H:i:s', strtotime("+1 day", strtotime($date_r.' '.$employee_schedule->time_out_to.'+6 hours')));
                    }
                    else
                    {
                        $convertedTimeout = date('Y-m-d H:i:s', strtotime($date_r.' '.$employee_schedule->time_out_to.'+8 hours'));
                    }
                }
                $time_in = ($employee->attendance_logs)->whereBetween('datetime',[$convertedTimein, $date_r." 23:59:59"])->sortBy('datetime')->first();
                $time_out = ($employee->attendance_logs)->whereBetween('datetime',[$date_r." 23:59:59", $convertedTimeout])->sortByDesc('datetime')->first();
                if (empty($time_out))
                {
                    $time_out = ($employee->attendance_logs)->where('date', $date_r)->sortByDesc('datetime')->first();      
                }
                
                // Schedule
                if($employee_schedule)
                {
                    if ($employee_schedule->time_in_from == null)
                    {
                        $rest = "RESTDAY";
                    }
                    else 
                    {
                        $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to);
                        $schedule_out_from = strtotime($date_r." ".$employee_schedule->time_out_from);
                        $schedule_in = strtotime($date_r." ".$employee_schedule->time_in_to);
                        $schedule_in_from = strtotime($date_r." ".$employee_schedule->time_in_frpm);
                        if(($schedule_out) < ($schedule_in))
                        {
                            $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to)+86400;
                            $schedule_out_from = strtotime($date_r." ".$employee_schedule->time_out_from)+86400;
                        }
                    }
                }
                else
                {
                    $rest = "RESTDAY";
                }

                // Time in and Time out
                if ($time_in && $time_out)
                {
                    $final_time_in = $time_in->datetime;
                    $final_time_out = $time_out->datetime;
                }

                // Absent
                if ($time_in && $time_out)
                {
                    $abs = 0;
                }
                else
                {
                    $abs = 1;
                }

                // Display Restday
                if (empty($employee_schedule))
                {
                    $abs = 0;
                }
                else 
                {
                    if ($employee_schedule->time_in_from == null)
                    {
                        $abs = 0;
                    }
                }

                // Reg hrs
                if ($employee_schedule)
                {
                    if ($time_in && $time_out)
                    {
                        $schedule_in = strtotime($date_r.' '.$employee_schedule->time_in_to);
                        $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to);

                        if ($schedule_in > $schedule_out)
                        {
                            $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to)+86400;
                        }
                        
                        $schedule_hrs = ($schedule_out - $schedule_in) / 3600; // default working hours
                        
                        $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                        if($if_has_ob)
                        {
                            if ($if_has_ob->date_from < $time_in->datetime)
                            {
                                $final_time_in = $if_has_ob->date_from;
                            }
                            if ($if_has_ob->date_to > $time_out->datetime) 
                            {
                                $final_time_out = $if_has_ob->date_to;
                            }
                        }

                        $time_start = date('Y-m-d h:i A', strtotime($final_time_in));
                        $time_end = date('Y-m-d h:i A', strtotime($final_time_out));

                        $start_time = strtotime($time_start);
                        $end_time = strtotime($time_end);

                        if (strtotime($date_r." ".$employee_schedule->time_in_from) > $start_time)
                        {
                            $start_time = strtotime($date_r." ".$employee_schedule->time_in_from);
                        }
                        if ($end_time > $schedule_out)
                        {
                            $end_time = $schedule_out;
                        }
                        
                        $working_hrs = round((($end_time - $start_time)/3600), 2);
                        if ($schedule_hrs > 8)
                        {
                            $schedule_hrs = $schedule_hrs-1;
                            if ($working_hrs >= ($schedule_hrs/1.5))
                            {
                                $working_hrs = $working_hrs-1;
                            }
                        }
                        else
                        {
                            $working_hrs = $working_hrs;
                        }
                        
                        if($working_hrs > $schedule_hrs)
                        {
                            $total_reg_hrs = $schedule_hrs;
                        }
                        else
                        {
                            if ($check_leave)
                            {
                                if ($working_hrs >= ($schedule_hrs/2))
                                {
                                    $total_reg_hrs = $schedule_hrs/2;
                                }
                            }
                            else 
                            {
                                $total_reg_hrs = $working_hrs;
                            }
                        }
                    }
                    else
                    {
                        $schedule_in = strtotime($date_r.' '.$employee_schedule->time_in_to);
                        $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to);
                        if ($schedule_in > $schedule_out)
                        {
                            $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to)+86400;
                        }
                        
                        $schedule_hrs = ($schedule_out - $schedule_in) / 3600; // default working hours
                        
                        $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                        if($if_has_ob)
                        {
                            $final_time_in = $if_has_ob->date_from;
                            $final_time_out = $if_has_ob->date_to;

                            $start_time = strtotime($final_time_in);
                            $end_time = strtotime($final_time_out);

                            if (strtotime($date_r." ".$employee_schedule->time_in_from) > $start_time)
                            {
                                $start_time = strtotime($date_r." ".$employee_schedule->time_in_from);
                            }
                            if ($end_time > $schedule_out)
                            {
                                $end_time = $schedule_out;
                            }
                            
                            $working_hrs = round((($end_time - $start_time)/3600), 2);
                            if ($schedule_hrs > 8)
                            {
                                $schedule_hrs = $schedule_hrs-1;
                                if ($working_hrs >= ($schedule_hrs/1.5))
                                {
                                    $working_hrs = $working_hrs-1;
                                }
                            }
                            else
                            {
                                $working_hrs = $working_hrs;
                            }
                            
                            if($working_hrs > $schedule_hrs)
                            {
                                $total_reg_hrs = $schedule_hrs;
                            }
                            else
                            {
                                if ($check_leave)
                                {
                                    if($working_hrs >= $schedule_hrs)
                                    {
                                        $total_reg_hrs = $schedule_hrs;
                                    }
                                }
                                else
                                {
                                    $total_reg_hrs = $working_hrs;
                                }
                            }
                        }
                    }
                }

                // Late
                if ($employee_schedule)
                {
                    if ($employee_schedule->time_in_from == null)
                    {
                        $late = 0;
                    }
                    else 
                    {
                        if ($time_in)
                        {
                            $in = strtotime(date('H:i',strtotime($final_time_in)));
                            $schedule_in = strtotime(date('H:i',$schedule_in));
                            if ($in > $schedule_in)
                            {
                                $total_late = ($in - $schedule_in) / 60;
                                $late = $total_late;
                            }
                        }
                    }
                }
                else
                {
                    $late = 0;
                }

                // Undertime
                if ($employee_schedule)
                {
                    if ($time_in)
                    {
                        $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                        if($if_has_ob)
                        {
                            if ($if_has_ob->date_from < $time_in->datetime)
                            {
                                $final_time_in = $if_has_ob->date_from;
                            }
                            
                            if ($if_has_ob->date_to > $time_out->datetime) 
                            {
                                $final_time_out = $if_has_ob->date_to;
                            }
                        }

                        $out = date('Y-m-d H:i:s', strtotime($time_out->datetime));
                        $in = date('Y-m-d H:i:s', strtotime($time_in->datetime));
                        
                        $estimated_out = "";
                        if (date('H:i', strtotime($in)) > $employee_schedule['time_in_to'])
                        {
                            $estimated_out = $employee_schedule['time_out_to'];
                        }
                        elseif(date('H:i', strtotime($in)) < $employee_schedule['time_in_from'])
                        {
                            $estimated_out = $employee_schedule['time_out_from'];
                        }
                        else
                        {
                            $hours = intval($employee_schedule['working_hours']);
                            $minutes = ($employee_schedule['working_hours']-$hours)*60;
                            $estimated_out = date('h:i A', strtotime("+".$hours." hours",strtotime($time_in->datetime)));
                            $estimated_out = date('h:i A', strtotime("+".$minutes." minutes",strtotime($estimated_out)));
                        }
                        
                        // $out_timestamp = strtotime($out);
                        // $estimated_out_timestamp = strtotime($date_r.' '.$estimated_out);
                        // if ($out_timestamp < $estimated_out_timestamp)
                        // {
                        //     $total_undertime = ($estimated_out_timestamp - $out_timestamp) / 60;
                        //     $undertime = $total_undertime;
                        // }
                        // dd($employee_schedule->working_hours, $total_reg_hrs);
                        $schedule_in = strtotime($date_r.' '.$employee_schedule->time_in_to);
                        $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to);
                        if ($schedule_in > $schedule_out)
                        {
                            $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to)+86400;
                        }
                        $schedule_hrs = ($schedule_out - $schedule_in) / 3600; // default working hours

                        if(($schedule_hrs-1) > $total_reg_hrs)
                        {
                            $undertime_hrs = (double) number_format(($schedule_hrs-1) - $total_reg_hrs,2);
                            $undertime = ($undertime_hrs)*60;
                        }
                    }
                }

                // Leave w/ pay
                $check_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);
                if ($check_leave)
                {
                    $leave = explode("-", $check_leave);
                    if (str_contains($check_leave,"With-Pay"))
                    {
                        $leave = $leave[1];
                        if ($leave == 0.5)
                        {
                            $abs = $leave;
                        }
                        else
                        {
                            $abs = $leave;
                        }
                        $undertime = 0;
                    }
                    else
                    {
                        if ($leave[1] == 0.5)
                        {
                            $abs=1;
                            $leave_count=(float)$leave[1];
                            $leave=0;
                        }
                        else 
                        {
                            $abs=1;
                            $leave_count=0;
                            $leave=0;
                        }
                    }
                }
                else
                {
                    $leave = 0;
                }
                // REG OT
                $emp_has_ot = employeeHasOTDetails($employee->approved_ots,date('Y-m-d',strtotime($date_r)));
                if ($rest == "RESTDAY")
                {
                    $overtime = 0;
                }
                else
                {
                    if (empty($check_if_holiday))
                    {
                        if ($emp_has_ot)
                        {
                            if ($emp_has_ot < 8)
                            {
                                $original_sched = $employee_schedule['working_hours'];
                                $work_ot = round(((strtotime($final_time_out) - strtotime($final_time_in)) / 3600), 2)-$original_sched;
                                if ($work_ot >= 2 && $emp_has_ot >= 2)
                                {
                                    if ($work_ot <= $emp_has_ot)
                                    {
                                        $overtime = $work_ot;
                                    }
                                    else 
                                    {
                                        $overtime = $emp_has_ot;
                                    }
                                }
                                else 
                                {
                                    if (in_array($employee->company_id, $plant_company))
                                    {
                                        if ($work_ot <= $emp_has_ot)
                                        {
                                            $overtime = $work_ot;
                                        }
                                        else 
                                        {
                                            $overtime = $emp_has_ot;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $overtime = floatval($emp_has_ot) - 1;
                            }
                        }
                    }
                }
                
                // OB
                $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                if($if_has_ob)
                {
                    if ($time_in && $time_out)
                    {
                        if ($if_has_ob->date_from < $time_in->datetime)
                        {
                            $ob_in = $if_has_ob->date_from;
                            $final_time_in = $ob_in;
                        }
                        if ($if_has_ob->date_to > $time_out->datetime) 
                        {
                            $ob_out = $if_has_ob->date_to;
                            $final_time_out = $ob_out;
                        }
                    }
                    else
                    {
                        $ob_in = $if_has_ob->date_from;
                        $final_time_in = $ob_in;

                        $ob_out = $if_has_ob->date_to;
                        $final_time_out = $ob_out;
                    }

                    $undertime = 0;
                    $abs = 0;
                }
                
                // ND
                $nightdiff_start = $final_time_in;
                $nightdiff_end = $final_time_out;
                if($employee_schedule)
                {
                    if (empty($check_if_holiday))
                    {
                        $start_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_in_to);
                        $end_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_out_to);
                        
                        if(strtotime($start_schedule) > strtotime($end_schedule))
                        {
                            $s = date('Y-m-d', strtotime($final_time_in . ' +1 day'));
                            $end_schedule = date('Y-m-d H:i', strtotime($s." ".$employee_schedule->time_out_to));
                        }
                        
                        if(strtotime($start_schedule) > strtotime($final_time_in))
                        {   
                            $nightdiff_start = $start_schedule;
                        }
                        if(strtotime($end_schedule) < strtotime($final_time_out))
                        {   
                            $nightdiff_end = $end_schedule;
                        }
                        
                        $night_diff = night_difference_per_company($nightdiff_start,$nightdiff_end);
                        $schedule_hours = (strtotime($end_schedule)-strtotime($start_schedule))/3600;
                        if($schedule_hours > 8)
                        {
                            if($night_diff >= 5)
                            {
                                $night_diff = $night_diff - 1;
                            }
                        }

                        // REG OT ND
                        if(empty($check_if_holiday))
                        {
                            if($night_diff < 7)
                            {
                                $actual_night_diff = night_difference_per_company($nightdiff_start,$nightdiff_end);
                                $night_diff_ot = night_difference_per_company($final_time_in,$final_time_out)-$actual_night_diff;
                            }
                        }

                        if ($night_diff_ot < .5)
                        {
                            $night_diff_ot = 0;
                        }
                    }
                }

                // RST OT
                if ($rest == "RESTDAY")
                {
                    if (empty($check_if_holiday))
                    {
                        if ($emp_has_ot)
                        {
                            $work_ot = round(((strtotime($final_time_out) - strtotime($final_time_in)) / 3600), 2);
                            $break_hrs = ($employee->approved_ots)->first();
                            if ($break_hrs)
                            {
                                $work_ot = $work_ot-$break_hrs->break_hrs;
                            }
                            if ($work_ot >= 2)
                            {
                                if ($work_ot > $emp_has_ot)
                                {
                                    $restday_ot = 8;
                                    if ($emp_has_ot >= 8)
                                    {
                                        $restday_ot = $restday_ot;
                                        $restday_ot_ge = floatval($emp_has_ot)-floatval($restday_ot);
                                    }
                                    else 
                                    {
                                        $restday_ot = $emp_has_ot;
                                    }
                                }
                                else 
                                {
                                    if ($work_ot > 8)
                                    {
                                        $restday_ot = $restday_ot;
                                        $restday_ot_ge = floatval($work_ot)-floatval($restday_ot);
                                    }
                                    else 
                                    {
                                        $restday_ot = $work_ot;
                                    }
                                }
                            }
                            else 
                            {
                                if (in_array($employee->company_id, $plant_company))
                                {
                                    if ($work_ot <= $emp_has_ot)
                                    {
                                        $overtime = $work_ot;
                                    }
                                    else 
                                    {
                                        $overtime = $emp_has_ot;
                                    }
                                }
                            }
                        }
                    }
                }

                // RST ND
                if ($rest == "RESTDAY")
                {
                    if (empty($rest))
                    {
                        if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                        {
                            $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                            $restnd =  night_difference_per_company($final_time_in,$final_time_out);
                            if($work_rest > 9 )
                            { 
                                $restnd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                $restnd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                $restnd_ge = $restnd_ge - $restnd;
                                $restnd = $restnd-1;
                                if($restnd <0)
                                {
                                    $restnd = 0;
                                }
                                if($restnd_ge <0)
                                {
                                    $restnd_ge = 0;
                                }
                            }
                        }
                    }
                }

                // Holiday OT's
                // if ($employee_schedule)
                // {
                $if_attendance_holiday_status = '';
                $check_if_holiday = checkIfHoliday(date('Y-m-d',strtotime($date_r)),$employee->location);
                if ($check_if_holiday)
                {
                    $abs = 0;
                    $undertime=0;
                    $overtime=0;
                    if ($employee_schedule)
                    {
                        // $if_attendance_holiday = checkHasAttendanceHoliday(date('Y-m-d',strtotime($date_r)), $employee->employee_number,$employee->location);
                        // $check_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r.'-1 day')),$employee_schedule);
                        // if ($check_leave)
                        // {
                        //     // $if_attendance_holiday_status = 'With-Pay';
                        //     if(str_contains($check_leave,"Without")){
                        //         $if_attendance_holiday_status = 'Without-Pay';
                        //         $abs = 1;
                                
                        //         $time_in = ($employee->attendance_logs)->sortBy('datetime')->first();
                        //         $time_out = ($employee->attendance_logs)->sortByDesc('datetime')->first();
                        //         $total_reg_hrs = number_format((strtotime($time_out->datetime) - strtotime($time_in->datetime))/3600, 2);
                        //         $emp_schedule = $employee_schedule->working_hours-1;
                        //         if ($total_reg_hrs >= ($emp_schedule/2))
                        //         {
                        //             $abs=0;
                        //             if ($employee_schedule->working_hours > 8) 
                        //             {
                        //                 $total_reg_hrs = $employee_schedule->working_hours-1;
                        //             }
                        //             else 
                        //             {
                        //                 $total_reg_hrs = $employee_schedule->working_hours;
                        //             }
                        //         }
                        //     }
                        //     else
                        //     {
                        //         $if_attendance_holiday_status = 'With-Pay';
                        //         if(str_contains($check_leave,".5") || str_contains($check_leave,"1"))
                        //         {
                        //             $abs = 0;

                        //             if ($employee_schedule->working_hours > 8) 
                        //             {
                        //                 $total_reg_hrs = $employee_schedule->working_hours-1;
                        //             }
                        //             else 
                        //             {
                        //                 $total_reg_hrs = $employee_schedule->working_hours;
                        //             }
                        //         }
                        //     }
                        // }
                        // else
                        // {
                        //     $attendance = ($employee->attendance_logs)->map(function($item) {
                        //         return [
                        //             'time_in' => $item->datetime
                        //         ];
                        //     });
                            
                        //     $check_attendance = checkHasAttendanceHolidayStatus($attendance,$if_attendance_holiday);
                        //     if(empty($check_attendance))
                        //     {
                        //         // $is_absent = 'Absent';
                        //         $abs = 1;
                        //     }else{
                        //         $if_attendance_holiday_status = 'With-Pay';
                        //         $abs = 0;
                        //     }
                        // }

                        $approved_ot_hrs = employeeHasOTDetails($employee->approved_ots,date('Y-m-d',strtotime($date_r)));
                        // SH OT
                        if ($check_if_holiday == "Special Holiday")
                        {
                            if ($rest == "RESTDAY")
                            {
                                $rst_sh_ot = 8;
                                if ($approved_ot_hrs > 8)
                                {
                                    $rst_sh_ot = $rst_sh_ot;
                                    $rst_sh_ot_ge = floatval($approved_ot_hrs) - 8;
                                }
                                else
                                {
                                    $rst_sh_ot = $approved_ot_hrs;
                                }
                            }
                            else 
                            {
                                $sh_ot = 8;
                                if ($approved_ot_hrs > 8)
                                {
                                    $sh_ot = $sh_ot;
                                    $sh_ot_ge = floatval($approved_ot_hrs) - 8;
                                }
                                else
                                {
                                    $sh_ot = $approved_ot_hrs;
                                }
                            }
    
                            $start_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_in_to);
                            $end_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_out_to);
                            if(strtotime($start_schedule) > strtotime($end_schedule))
                            {
                                $s = date('Y-m-d', strtotime($final_time_in . ' +1 day'));
                                $end_schedule = date('Y-m-d H:i', strtotime($s." ".$employee_schedule->time_out_to));
                            }
                            if(strtotime($start_schedule) > strtotime($final_time_in))
                            {   
                                $nightdiff_start = $start_schedule;
                            }
                            if(strtotime($end_schedule) < strtotime($final_time_out))
                            {   
                                $nightdiff_end = $end_schedule;
                            }
                            
                            if ($rest == "RESTDAY")
                            {
                                $rst_sh_ot_nd = night_difference_per_company($nightdiff_start,$nightdiff_end);
                                if($rst_sh_ot_nd >= 4.5)
                                {
                                    $rst_sh_ot_nd = $rst_sh_ot_nd-1;
                                }
                                if ($rst_sh_ot_nd > $sh_ot)
                                {
                                    $rst_sh_ot_nd = $sh_ot;
                                }

                                $time_start_string = strtotime($time_start);
                                $time_end_string = strtotime($time_end);
                                $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to);
                                $schedule_in = strtotime($date_r." ".$employee_schedule->time_in_to);
                                
                                if(($schedule_out) < ($schedule_in))
                                {
                                    
                                    $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to)+86400;
                                }
                                if($time_end_string>$schedule_out)
                                {
                                    $rst_sh_ot_nd =  night_difference_per_company(date('Y-m-d H:i',$schedule_in),date('Y-m-d H:i',$schedule_out));
                                    $sh_ot_use = $rst_sh_ot_nd;
                                    if($rst_sh_ot_nd >=4.5 )
                                    {   
                                        $schedule_hours = ((($schedule_out)-($schedule_in))/3600);
                                        if($schedule_hours > 8)
                                        {
                                            $rst_sh_ot_nd = $rst_sh_ot_nd-1;
                                        }
                                    }

                                    $rst_sh_ot_nd_ge =night_difference_per_company(date('Y-m-d H:i',$schedule_in),$time_end)-$sh_ot_use;
                                    $rst_sh_ot_nd_ge = $rst_sh_ot_nd_ge;
                                    if($rst_sh_ot_nd_ge <0)
                                    {
                                        $rst_sh_ot_nd_ge=0;
                                    }
                                    
                                }
                                else {
                                    $rst_sh_ot_nd =  night_difference_per_company(date('Y-m-d H:i',$schedule_in),$time_end);
                                    if($rst_sh_ot_nd >=4.5 )
                                    {   
                                        if($schedule_hours > 8)
                                        {
                                        $rst_sh_ot_nd = $rst_sh_ot_nd-1;
                                        }
                                    }
                                }
                            }
                            else 
                            {
                                $sh_ot_nd = night_difference_per_company($nightdiff_start,$nightdiff_end);
                                if($sh_ot_nd >= 4.5)
                                {
                                    $sh_ot_nd = $sh_ot_nd-1;
                                }
                                if ($sh_ot_nd > $sh_ot)
                                {
                                    $sh_ot_nd = $sh_ot;
                                }

                                $time_start_string = strtotime($time_start);
                                $time_end_string = strtotime($time_end);
                                $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to);
                                $schedule_in = strtotime($date_r." ".$employee_schedule->time_in_to);
                                
                                if(($schedule_out) < ($schedule_in))
                                {
                                    
                                    $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to)+86400;
                                }
                                if($time_end_string>$schedule_out)
                                {
                                    $sh_ot_nd =  night_difference_per_company(date('Y-m-d H:i',$schedule_in),date('Y-m-d H:i',$schedule_out));
                                    $sh_ot_use = $sh_ot_nd;
                                    if($sh_ot_nd >=4.5 )
                                    {   
                                        $schedule_hours = ((($schedule_out)-($schedule_in))/3600);
                                        if($schedule_hours > 8)
                                        {
                                            $sh_ot_nd = $sh_ot_nd-1;
                                        }
                                    }

                                    $sh_ot_nd_ge =night_difference_per_company(date('Y-m-d H:i',$schedule_in),$time_end)-$sh_ot_use;
                                    $sh_ot_nd_ge = $sh_ot_nd_ge;
                                    if($sh_ot_nd_ge <0)
                                    {
                                        $sh_ot_nd_ge=0;
                                    }
                                    
                                }
                                else {
                                    $sh_ot_nd =  night_difference_per_company(date('Y-m-d H:i',$schedule_in),$time_end);
                                    if($sh_ot_nd >=4.5 )
                                    {   
                                        if($schedule_hours > 8)
                                        {
                                        $sh_ot_nd = $sh_ot_nd-1;
                                        }
                                    }
                                }
                            }
                        }
                        else
                        {
                            if ($rest == "RESTDAY")
                            {
                                $rst_lh_ot = 8;
                                if ($approved_ot_hrs > 8)
                                {
                                    $rst_lh_ot = $rst_lh_ot;
                                    $lh_ot_ge = floatval($approved_ot_hrs) - 8;
                                }
                                else
                                {
                                    $rst_lh_ot = $approved_ot_hrs;
                                }
                            }
                            else 
                            {
                                $lh_ot = 8;
                                if ($approved_ot_hrs > 8)
                                {
                                    $lh_ot = $lh_ot;
                                    $lh_ot_ge = floatval($approved_ot_hrs) - 8;
                                }
                                else
                                {
                                    $lh_ot = $approved_ot_hrs;
                                }
                            }
                            
                            if ($employee_schedule)
                            {
                                $start_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_in_to);
                                $end_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_out_to);
                                
                                if(strtotime($start_schedule) > strtotime($end_schedule))
                                {
                                    $s = date('Y-m-d', strtotime($final_time_in . ' +1 day'));
                                    $end_schedule = date('Y-m-d H:i', strtotime($s." ".$employee_schedule->time_out_to));
                                }
                                
                                if(strtotime($start_schedule) > strtotime($final_time_in))
                                {   
                                    $nightdiff_start = $start_schedule;
                                }
                                if(strtotime($end_schedule) < strtotime($final_time_out))
                                {   
                                    $nightdiff_end = $end_schedule;
                                }
                            }
                            
                            if ($rest == "RESTDAY")
                            {
                                if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                                {
                                    $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                                    $lh_nd_ge =  night_difference_per_company($final_time_in,$final_time_out);
                                    if($work_rest > 9 )
                                    { 
                                        $rst_lh_nd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                        $rst_lh_nd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                        $rst_lh_nd = $rst_lh_nd_ge - $rst_lh_nd;
                                        $rst_lh_nd = $rst_lh_nd-1;
                                        if($rst_lh_nd <0)
                                        {
                                            $rst_lh_nd = 0;
                                        }
                                        if($rst_lh_nd_ge <0)
                                        {
                                            $rst_lh_nd_ge = 0;
                                        }
                                    }
                                }
                            }
                            else 
                            {
                                if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                                {
                                    $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                                    $lh_nd_ge =  night_difference_per_company($final_time_in,$final_time_out);
                                    if($work_rest > 9 )
                                    { 
                                        $lh_nd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                        $lh_nd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                        $lh_nd = $lh_nd_ge - $lh_nd;
                                        $lh_nd = $lh_nd-1;
                                        if($lh_nd <0)
                                        {
                                            $lh_nd = 0;
                                        }
                                        if($lh_nd_ge <0)
                                        {
                                            $lh_nd_ge = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }

                }
                // }

                // Remarks
                $leave_count = 0;
                $abs_half = 0;
                if($if_has_ob)
                {
                    $remarks = 'OB';
                }
                else 
                {
                    $if_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);
                    if($if_leave)
                    {
                        $l = explode('-',$if_leave);
                        $leave_count = (double) $l[1];
                        if(str_contains($if_leave,"Without"))
    
                        {
                            $leave_count = 0;
                            $abs_half = $l[1];
                        }
                    }
                    $remarks = $if_leave;
                }

                if ($total_reg_hrs <= 0)
                {
                    $total_reg_hrs = 0;
                }

                $pending_dtr = count(($employee->dtr_correction)->where('date',$date_r)->where('status','Pending'));
                $cancelled_dtr = ($employee->dtr_correction)->where('date',$date_r)->where('status','Cancelled')->last();
                $revert = count(($employee->dtr_status)->where('date',$date_r)->where('status','Revert'));
                $for_posting = count(($employee->dtr_status)->where('date',$date_r)->where('status','For posting'));
                $posted_dtr = count(($employee->attendance_detailed_report)->where('log_date', $date_r));


                if(($abs == 0) && ($overtime == 0) && ($revert == 0) && ($pending_dtr == 0) && (!$if_has_ob) || (($for_posting > 0)))
                {
                    $data[]=[
                        'checkbox' => '
                            <input type="checkbox" class="form-control" >
                        ', 
                        'action' => '
                            <button type="button" class="btn btn-sm btn-danger" id="revertBtn" data-employee="'.$employee->id.'" data-date="'.$date_r.'">
                                <i class="ti-pencil"></i>
                                Revert
                            </button>
                        ',
                        'company' => $employee->company->company_code,
                        'employee_code' => $employee->employee_code,
                        'name' => $employee->last_name.', '.$employee->first_name,
                        'date' => $date_r,
                        'schedule' => $schedule_display,
                        'time_in' => $final_time_in ? date('h:i A', strtotime($final_time_in)) : '',
                        'time_out' => $final_time_out ? date('h:i A', strtotime($final_time_out)) : '',
                        'abs' => number_format($abs, 2),
                        'reg_hrs' => round($total_reg_hrs,2),
                        'late' => $late,
                        'undertime' => number_format($undertime,2),
                        'leave' => number_format($leave,2),
                        'leave_count' => $leave_count,
                        'overtime'=> number_format($overtime,2),
                        'reg_nd'=> number_format($night_diff,2),
                        'reg_ot_nd'=> number_format($night_diff_ot,2),
                        'restday_ot'=> number_format($restday_ot,2),
                        'restday_ot_ge'=> number_format($restday_ot_ge,2),
                        'restnd'=> number_format($restnd,2),
                        'restnd_ge'=> number_format($restnd_ge,2),
                        'lh_ot'=> number_format($lh_ot,2),
                        'lh_ot_ge'=> number_format($lh_ot_ge,2),
                        'lh_nd'=> number_format($lh_nd,2),
                        'lh_nd_ge'=> number_format($lh_nd_ge,2),
                        'sh_ot'=> number_format($sh_ot,2),
                        'sh_ot_ge'=> number_format($sh_ot_ge,2),
                        'sh_ot_nd'=> number_format($sh_ot_nd,2),
                        'sh_ot_nd_ge'=> number_format($sh_ot_nd_ge,2),
                        'rst_lh_ot'=> number_format($rst_lh_ot,2),
                        'rst_lh_ot_ge'=> number_format($lh_ot_ge,2),
                        'rst_lh_ot_nd'=> number_format($rst_lh_ot_nd,2),
                        'rst_lh_ot_nd_ge'=> number_format($rst_lh_ot_nd_ge,2),
                        'rst_sh_ot'=> number_format($rst_sh_ot,2),
                        'rst_sh_ot_ge'=> number_format($rst_sh_ot_ge,2),
                        'rst_sh_ot_nd'=> number_format($rst_sh_ot_nd,2),
                        'rst_sh_ot_nd_ge'=> number_format($rst_sh_ot_nd_ge,2),
                        'remarks' => $remarks,
                        'if_has_ob' => $if_has_ob ? 'Yes' : 'No',
                        'employee_id' => $employee->id
                    ];
                }
            }
        }

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => count($employees),
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    public function dtrStatus(Request $request)
    {
        // dd($request->all());
        $dtr_status = DtrStatus::where('employee_id', $request->employee)->where('date',$request->date)->first();
        if ($dtr_status)
        {
            $dtr_status->status = 'Revert';
            $dtr_status->action_by = auth()->user()->id;
            $dtr_status->save();
        }
        else 
        {
            $dtr_status = new DtrStatus;
            $dtr_status->employee_id = $request->employee;
            $dtr_status->date = $request->date;
            $dtr_status->status = 'Revert';
            $dtr_status->action_by = auth()->user()->id;
            $dtr_status->save();
        }

        // Alert::success('Successfully Revert')->persistent('Dismiss');
        // return back();
        return response()->json([
            'status' =>'success',
            'message' => 'Successfully Revert'
        ]);
    }

    public function moveToForPosting(Request $request)
    {
        // dd($request->all());
        $dtr_status = DtrStatus::where('employee_id', $request->employee_id)->where('date',$request->date)->first();
        if($dtr_status)
        {
            $dtr_status->status = 'For posting';
            $dtr_status->action_by = auth()->user()->id;
            $dtr_status->save();
        }
        else
        {
            $dtr_status = new DtrStatus;
            $dtr_status->employee_id = $request->employee_id;
            $dtr_status->date = $request->date;
            $dtr_status->status = 'For posting';
            $dtr_status->action_by = auth()->user()->id;
            $dtr_status->save();
        }

        // Alert::success('Successfully Moved')->persistent('Dismiss');
        // return back();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully Move to for posting'
        ]);
    }

    public function refreshDate(Request $request)
    {
        // dd($request->all());
        $attendance_detailed_reports = AttendanceDetailedReport::select('cut_off_date')
            ->where('company_id', $request->company)
            ->orderBy('id','desc')
            ->first();

        return $attendance_detailed_reports->cut_off_date;
    }

    public function perEmployee(Request $request)
    {
        $header = 'timekeeping-official';        
        $employees = Employee::select('id','last_name','first_name','employee_code')->get();

        return view('per_employee.index',
            array(
                'header' => $header,
                'employees' => $employees,
            )
        );
    }

    public function getPerEmployee(Request $request)
    {
        $from_date = $request->date_from;
        $to_date = $request->date_to;
        $date_from = date('Y-m-d', strtotime($from_date."-1 day"));
        $employee_data = $request->employee ? $request->employee : [];
        
        $employees = Employee::select('id','user_id','employee_code','first_name','last_name','schedule_id','employee_number','company_id','department_id')
            ->with(['schedule_info'])
            ->with([
                'daily_schedules' => function($q) use ($date_from, $to_date) {
                    $q->whereBetween('log_date', [$date_from, $to_date]);
                }
            ])
            ->with([
                'attendance_logs' => function($q) use ($date_from, $to_date) {
                    $q->select('id','emp_code','date','datetime')
                        ->whereBetween('datetime', [$date_from.' 00:00:01', date('Y-m-d 23:59:59', strtotime($to_date. '+1 day'))])
                        ->orderBy('datetime','asc');
                }
            ])
            ->with([
                'approved_ots' => function($q) use ($date_from, $to_date) {
                    $q->whereBetween('ot_date', [$date_from, $to_date])
                        ->where('status','Approved')
                        ->orderBy('ot_date','asc');
                }
            ])
            ->with(['approved_leaves' => function ($query) use ($date_from, $to_date) {
                $query->where(function ($q) use ($date_from, $to_date) {
                    $q->whereBetween('date_from', [$date_from, $to_date])
                        ->orWhereBetween('date_to', [$date_from, $to_date]);
                })
                ->where('status','Approved')
                ->orderBy('id','asc');
            },'approved_leaves.leave'])
            ->with(['approved_obs' => function ($query) use ($date_from, $to_date) {
                $query->whereBetween('applied_date', [$date_from, $to_date])
                ->where('status','Approved')
                ->orderBy('id','asc');
            }])
            ->whereIn('id', $employee_data)
            ->offset($request->start)
            ->limit($request->length)
            ->orderBy('last_name','asc')
            ->get();
        
        $attendance_controller = new AttendanceController;
        $date_range =  $attendance_controller->dateRange($from_date, $to_date);
        
        $data=[];
        foreach($employees as $employee)
        {
            foreach($date_range as $date_r)
            {
                $reg_hrs = 0;
                $late = 0;
                $abs = 0;
                $undertime = 0;
                $leave = 0;
                $overtime = 0;
                $night_diff = 0;
                $night_diff_ot = 0;
                $restday_ot = 0;
                $restday_ot_ge = 0;
                $restnd = 0;
                $restnd_ge = 0;
                $lh_ot = 0;
                $lh_ot_ge = 0;
                $lh_nd = 0;
                $lh_nd_ge = 0;
                $sh_ot = 0;
                $sh_ot_ge = 0;
                $sh_ot_nd = 0;
                $sh_ot_nd_ge = 0;
                $rst_lh_ot= 0;
                $rst_lh_ot_ge= 0;
                $rst_lh_ot_nd= 0;
                $rst_lh_ot_nd_ge= 0;
                $rst_sh_ot= 0;
                $rst_sh_ot_ge= 0;
                $rst_sh_ot_nd= 0;
                $rst_sh_ot_nd_ge= 0;
                $plant_company = [5, 10, 11, 12];

                $rest = "";
                $ob_in = "";
                $ob_out = "";
                $final_time_in = "";
                $final_time_out = "";
                $nightdiff_start = "";
                $nightdiff_end = "";
                $schedule_display="";

                // Helper Functions
                $employee_schedule = HelperClass::employeeSchedule($employee->ScheduleData,$employee->daily_schedules,$date_r,$employee->schedule_id,$employee->employee_code);
                $if_has_ob = HelperClass::employeeHasOBDetails($employee->approved_obs, $date_r);
                $check_leave = HelperClass::employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);

                // Employee Schedule
                if($employee_schedule)
                {
                    if ($employee_schedule->time_in_from)
                    {
                        $schedule_display=date('h:i A', strtotime($employee_schedule->time_in_to)).'-'.date('h:i A', strtotime($employee_schedule->time_out_to));
                        if ($employee_schedule->time_in_from != $employee_schedule->time_in_to)
                        {
                            $schedule_display.="(Flexi)";
                        }
                    }
                }
                else 
                {
                    $rest="RESTDAY";
                    $schedule_display=$rest;
                }

                // Time in & Time out
                $convertedTimein = date('Y-m-d 00:00:00',strtotime($date_r));
                $convertedTimeout = date('Y-m-d 00:00:00',strtotime($date_r));
                if($employee_schedule)
                {
                    if($employee_schedule->time_in_from)
                    {
                        $convertedTimein = date('Y-m-d H:i:s',strtotime('-6 hours',strtotime($date_r." ".$employee_schedule->time_in_from)));
                    }

                    if ($employee_schedule->time_out_to < $employee_schedule->time_in_from)
                    {
                        $convertedTimeout = date('Y-m-d H:i:s', strtotime("+1 day", strtotime($date_r.' '.$employee_schedule->time_out_to.'+6 hours')));
                    }
                    else
                    {
                        $convertedTimeout = date('Y-m-d H:i:s', strtotime($date_r.' '.$employee_schedule->time_out_to.'+8 hours'));
                    }
                }
                $time_in = ($employee->attendance_logs)->whereBetween('datetime',[$convertedTimein, $date_r." 23:59:59"])->sortBy('datetime')->first();
                $time_out = ($employee->attendance_logs)->whereBetween('datetime',[$date_r." 23:59:59", $convertedTimeout])->sortByDesc('datetime')->first();
                if (empty($time_out))
                {
                    $time_out = ($employee->attendance_logs)->where('date', $date_r)->sortByDesc('datetime')->first();      
                }

                // Schedule
                if($employee_schedule)
                {
                    if ($employee_schedule->time_in_from == null)
                    {
                        $rest = "RESTDAY";
                    }
                    else 
                    {
                        $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to);
                        $schedule_out_from = strtotime($date_r." ".$employee_schedule->time_out_from);
                        $schedule_in = strtotime($date_r." ".$employee_schedule->time_in_to);
                        $schedule_in_from = strtotime($date_r." ".$employee_schedule->time_in_frpm);
                        if(($schedule_out) < ($schedule_in))
                        {
                            $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to)+86400;
                            $schedule_out_from = strtotime($date_r." ".$employee_schedule->time_out_from)+86400;
                        }
                    }
                }
                else
                {
                    $rest = "RESTDAY";
                }

                // Time in and Time out
                if ($time_in && $time_out)
                {
                    $final_time_in = $time_in->datetime;
                    $final_time_out = $time_out->datetime;
                }

                 // Absent
                if ($time_in && $time_out)
                {
                    $abs = 0;
                }
                else
                {
                    if ($rest == "RESTDAY")
                    {
                        $abs=0;
                    }
                    else 
                    {
                        $abs=1;
                    }
                }

                // Reg hrs
                if ($employee_schedule)
                {
                    if ($time_in && $time_out)
                    {
                        $schedule_in = strtotime($date_r.' '.$employee_schedule->time_in_to);
                        $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to);

                        if ($schedule_in > $schedule_out)
                        {
                            $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to)+86400;
                        }
                        
                        $schedule_hrs = ($schedule_out - $schedule_in) / 3600; // default working hours
                        
                        // $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                        if($if_has_ob)
                        {
                            if ($if_has_ob->date_from < $time_in->datetime)
                            {
                                $final_time_in = $if_has_ob->date_from;
                            }
                            if ($if_has_ob->date_to > $time_out->datetime) 
                            {
                                $final_time_out = $if_has_ob->date_to;
                            }
                        }
                        
                        $time_start = date('Y-m-d h:i A', strtotime($final_time_in));
                        $time_end = date('Y-m-d h:i A', strtotime($final_time_out));
                        
                        $start_time = strtotime($time_start);
                        $end_time = strtotime($time_end);

                        if (strtotime($date_r." ".$employee_schedule->time_in_from) > $start_time)
                        {
                            $start_time = strtotime($date_r." ".$employee_schedule->time_in_from);
                        }
                        if ($end_time > $schedule_out)
                        {
                            $end_time = $schedule_out;
                        }
                        
                        $working_hrs = round((($end_time - $start_time)/3600), 2);
                        if ($schedule_hrs > 8)
                        {
                            $schedule_hrs = $schedule_hrs-1;
                            if ($working_hrs >= ($schedule_hrs/1.5))
                            {
                                $working_hrs = $working_hrs-1;
                            }
                        }
                        else
                        {
                            $working_hrs = $working_hrs;
                        }
                        
                        if($working_hrs > $schedule_hrs)
                        {
                            $reg_hrs = $schedule_hrs;
                        }
                        else
                        {
                            $reg_hrs = $working_hrs;
                        }
                    }
                    else
                    {
                        $schedule_in = strtotime($date_r.' '.$employee_schedule->time_in_to);
                        $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to);
                        if ($schedule_in > $schedule_out)
                        {
                            $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to)+86400;
                        }
                        
                        $schedule_hrs = ($schedule_out - $schedule_in) / 3600; // default working hours
                        
                        // $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                        if($if_has_ob)
                        {
                            $start_time = strtotime($if_has_ob->date_from);
                            $end_time = strtotime($if_has_ob->date_to);
                            
                            // $start_time = strtotime($date_r.' '.$final_time_in);
                            // $end_time = strtotime($date_r.' '.$final_time_out);
                            // dd($start_time,$end_time);
                            if (strtotime($date_r." ".$employee_schedule->time_in_from) > $start_time)
                            {
                                $start_time = strtotime($date_r." ".$employee_schedule->time_in_from);
                            }
                            if ($end_time > $schedule_out)
                            {
                                $end_time = $schedule_out;
                            }
                            
                            $working_hrs = round((($end_time - $start_time)/3600), 2);
                            if ($schedule_hrs > 8)
                            {
                                $schedule_hrs = $schedule_hrs-1;
                                if ($working_hrs >= ($schedule_hrs/1.5))
                                {
                                    $working_hrs = $working_hrs-1;
                                }
                            }
                            else
                            {
                                $working_hrs = $working_hrs;
                            }
                            
                            if($working_hrs > $schedule_hrs)
                            {
                                $reg_hrs = $schedule_hrs;
                            }
                            else
                            {
                                $reg_hrs = $working_hrs;
                            }
                        }
                    }

                    // Late
                    if ($employee_schedule)
                    {
                        if ($employee_schedule->time_in_from == null)
                        {
                            $late = 0;
                        }
                        else 
                        {
                            if ($time_in)
                            {
                                $in = strtotime(date('H:i',strtotime($final_time_in)));
                                $schedule_in = strtotime(date('H:i',$schedule_in));
                                if ($in > $schedule_in)
                                {
                                    $total_late = ($in - $schedule_in) / 60;
                                    $late = $total_late;
                                }
                            }
                        }
                    }
                    else
                    {
                        $late = 0;
                    }

                     // Undertime
                    if ($employee_schedule)
                    {
                        if ($time_in)
                        {
                            $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                            if($if_has_ob)
                            {
                                if ($if_has_ob->date_from < $time_in->datetime)
                                {
                                    $final_time_in = $if_has_ob->date_from;
                                }
                                
                                if ($if_has_ob->date_to > $time_out->datetime) 
                                {
                                    $final_time_out = $if_has_ob->date_to;
                                }
                            }

                            $out = date('Y-m-d H:i:s', strtotime($time_out->datetime));
                            $in = date('Y-m-d H:i:s', strtotime($time_in->datetime));
                            
                            $estimated_out = "";
                            if (date('H:i', strtotime($in)) > $employee_schedule['time_in_to'])
                            {
                                $estimated_out = $employee_schedule['time_out_to'];
                            }
                            elseif(date('H:i', strtotime($in)) < $employee_schedule['time_in_from'])
                            {
                                $estimated_out = $employee_schedule['time_out_from'];
                            }
                            else
                            {
                                $hours = intval($employee_schedule['working_hours']);
                                $minutes = ($employee_schedule['working_hours']-$hours)*60;
                                $estimated_out = date('h:i A', strtotime("+".$hours." hours",strtotime($time_in->datetime)));
                                $estimated_out = date('h:i A', strtotime("+".$minutes." minutes",strtotime($estimated_out)));
                            }
                            // dd($estimated_out);
                            $out_timestamp = strtotime($out);
                            $estimated_out_timestamp = strtotime($date_r.' '.$estimated_out);
                            if ($out_timestamp < $estimated_out_timestamp)
                            {
                                $total_undertime = ($estimated_out_timestamp - $out_timestamp) / 60;
                                $undertime = $total_undertime;
                            }
                        }
                    }
                }

                // Leave w/ pay
                $leave_count=0;
                if ($check_leave)
                {
                    $leave = explode("-", $check_leave);
                    if (str_contains($check_leave,"With-Pay"))
                    {
                        $leave = $leave[1];
                        if ($leave == 0.5)
                        {
                            $leave_count = $leave;
                            $leave=$leave;
                            $abs=$leave_count;
                            $undertime=0;
                        }
                        else
                        {
                            $leave=$leave;
                            $abs = 0;
                            $undertime = 0;
                        }
                    }
                    else
                    {
                        if ($leave[1] == 0.5)
                        {
                            $halfday_hrs = (($employee_schedule->working_hours-1)/2);
                            if ($reg_hrs >= $halfday_hrs)
                            {
                                $leave_count=$leave[1];
                                $leave=0;
                                $abs=$leave_count;
                                $undertime=0;
                            }
                            else
                            {
                                $leave_count = $leave[1];
                                $leave=0;
                                $abs=$leave_count;
                            }
                        }
                        else 
                        {
                            $abs=1;
                            $leave=0;
                        }
                    }
                }
                else
                {
                    $leave = 0;
                }

                // REG OT
                $emp_has_ot = employeeHasOTDetails($employee->approved_ots,date('Y-m-d',strtotime($date_r)));
                if ($rest == "RESTDAY")
                {
                    $overtime = 0;
                }
                else
                {
                    if (empty($check_if_holiday))
                    {
                        if ($emp_has_ot)
                        {
                            if ($emp_has_ot < 8)
                            {
                                $original_sched = $employee_schedule['working_hours'];
                                $work_ot = round(((strtotime($final_time_out) - strtotime($final_time_in)) / 3600), 2)-$original_sched;
                                if ($work_ot >= 2 && $emp_has_ot >= 2)
                                {
                                    if ($work_ot <= $emp_has_ot)
                                    {
                                        $overtime = $work_ot;
                                    }
                                    else 
                                    {
                                        $overtime = $emp_has_ot;
                                    }
                                }
                                else 
                                {
                                    if (in_array($employee->company_id, $plant_company))
                                    {
                                        if ($work_ot <= $emp_has_ot)
                                        {
                                            $overtime = $work_ot;
                                        }
                                        else 
                                        {
                                            $overtime = $emp_has_ot;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $overtime = floatval($emp_has_ot) - 1;
                            }
                        }
                    }
                }

                // OB
                if($if_has_ob)
                {
                    if ($time_in && $time_out)
                    {
                        if ($if_has_ob->date_from < $time_in->datetime)
                        {
                            $ob_in = $if_has_ob->date_from;
                            $final_time_in = date('h:i A', strtotime($ob_in));
                        }
                        if ($if_has_ob->date_to > $time_out->datetime) 
                        {
                            $ob_out = $if_has_ob->date_to;
                            $final_time_out = date('h:i A', strtotime($ob_out));
                        }
                    }
                    else
                    {
                        $ob_in = $if_has_ob->date_from;
                        $final_time_in = date('h:i A', strtotime($ob_in));

                        $ob_out = $if_has_ob->date_to;
                        $final_time_out = date('h:i A', strtotime($ob_out));
                    }

                    $undertime = 0;
                    $abs = 0;
                }
                
                // ND
                $nightdiff_start = $final_time_in;
                $nightdiff_end = $final_time_out;
                if($employee_schedule)
                {
                    if (empty($check_if_holiday))
                    {
                        $start_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_in_to);
                        $end_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_out_to);
                        
                        if(strtotime($start_schedule) > strtotime($end_schedule))
                        {
                            $s = date('Y-m-d', strtotime($final_time_in . ' +1 day'));
                            $end_schedule = date('Y-m-d H:i', strtotime($s." ".$employee_schedule->time_out_to));
                        }
                        
                        if(strtotime($start_schedule) > strtotime($final_time_in))
                        {   
                            $nightdiff_start = $start_schedule;
                        }
                        if(strtotime($end_schedule) < strtotime($final_time_out))
                        {   
                            $nightdiff_end = $end_schedule;
                        }
                        
                        $night_diff = night_difference_per_company($nightdiff_start,$nightdiff_end);
                        $schedule_hours = (strtotime($end_schedule)-strtotime($start_schedule))/3600;
                        if($schedule_hours > 8)
                        {
                            if($night_diff >= 5)
                            {
                                $night_diff = $night_diff - 1;
                            }
                        }

                        // REG OT ND
                        if(empty($check_if_holiday))
                        {
                            if($night_diff < 7)
                            {
                                $actual_night_diff = night_difference_per_company($nightdiff_start,$nightdiff_end);
                                $night_diff_ot = night_difference_per_company($final_time_in,$final_time_out)-$actual_night_diff;
                            }
                        }

                        if ($night_diff_ot < .5)
                        {
                            $night_diff_ot = 0;
                        }
                    }
                }
                
                // RST OT
                if ($rest == "RESTDAY")
                {
                    if (empty($check_if_holiday))
                    {
                        if ($emp_has_ot)
                        {
                            $work_ot = round(((strtotime($final_time_out) - strtotime($final_time_in)) / 3600), 2);
                            $break_hrs = ($employee->approved_ots)->first();
                            if ($break_hrs)
                            {
                                $work_ot = $work_ot-$break_hrs->break_hrs;
                            }
                            if ($work_ot >= 2)
                            {
                                if ($work_ot > $emp_has_ot)
                                {
                                    $restday_ot = 8;
                                    if ($emp_has_ot >= 8)
                                    {
                                        $restday_ot = $restday_ot;
                                        $restday_ot_ge = floatval($emp_has_ot)-floatval($restday_ot);
                                    }
                                    else 
                                    {
                                        $restday_ot = $emp_has_ot;
                                    }
                                }
                                else 
                                {
                                    if ($work_ot > 8)
                                    {
                                        $restday_ot = $restday_ot;
                                        $restday_ot_ge = floatval($work_ot)-floatval($restday_ot);
                                    }
                                    else 
                                    {
                                        $restday_ot = $work_ot;
                                    }
                                }
                            }
                            else 
                            {
                                if (in_array($employee->company_id, $plant_company))
                                {
                                    if ($work_ot <= $emp_has_ot)
                                    {
                                        $restday_ot = $work_ot;
                                    }
                                    else 
                                    {
                                        $restday_ot = $emp_has_ot;
                                    }
                                }
                            }
                        }
                    }
                }

                // RST ND
                if ($rest == "RESTDAY")
                {
                    if (empty($rest))
                    {
                        if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                        {
                            $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                            $restnd =  night_difference_per_company($final_time_in,$final_time_out);
                            if($work_rest > 9 )
                            { 
                                $restnd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                $restnd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                $restnd_ge = $restnd_ge - $restnd;
                                $restnd = $restnd-1;
                                if($restnd <0)
                                {
                                    $restnd = 0;
                                }
                                if($restnd_ge <0)
                                {
                                    $restnd_ge = 0;
                                }
                            }
                        }
                    }
                }

                // Holiday OT
                $if_attendance_holiday_status = '';
                $check_if_holiday = checkIfHoliday(date('Y-m-d',strtotime($date_r)),$employee->location);
                if ($check_if_holiday)
                {
                    $abs = 0;
                    $undertime=0;
                    if ($employee_schedule)
                    {
                        $if_attendance_holiday = checkHasAttendanceHoliday(date('Y-m-d',strtotime($date_r)), $employee->employee_number,$employee->location);
                        $check_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r.'-1 day')),$employee_schedule);
                        if ($check_leave)
                        {
                            // $if_attendance_holiday_status = 'With-Pay';
                            if(str_contains($check_leave,"Without")){
                                $if_attendance_holiday_status = 'Without-Pay';
                                $abs = 1;
                                
                                $time_in = ($employee->attendance_logs)->sortBy('datetime')->first();
                                $time_out = ($employee->attendance_logs)->sortByDesc('datetime')->first();
                                $total_reg_hrs = number_format((strtotime($time_out->datetime) - strtotime($time_in->datetime))/3600, 2);
                                $emp_schedule = $employee_schedule->working_hours-1;
                                if ($total_reg_hrs >= ($emp_schedule/2))
                                {
                                    $abs=0;
                                    if ($employee_schedule->working_hours > 8) 
                                    {
                                        $total_reg_hrs = $employee_schedule->working_hours-1;
                                    }
                                    else 
                                    {
                                        $total_reg_hrs = $employee_schedule->working_hours;
                                    }
                                }
                            }
                            else
                            {
                                $if_attendance_holiday_status = 'With-Pay';
                                if(str_contains($check_leave,".5") || str_contains($check_leave,"1"))
                                {
                                    $abs = 0;

                                    if ($employee_schedule->working_hours > 8) 
                                    {
                                        $total_reg_hrs = $employee_schedule->working_hours-1;
                                    }
                                    else 
                                    {
                                        $total_reg_hrs = $employee_schedule->working_hours;
                                    }
                                }
                            }
                        }
                        else
                        {
                            $attendance = ($employee->attendance_logs)->map(function($item) {
                                return [
                                    'time_in' => $item->datetime
                                ];
                            });
                            
                            $check_attendance = checkHasAttendanceHolidayStatus($attendance,$if_attendance_holiday);
                            if(empty($check_attendance))
                            {
                                // $is_absent = 'Absent';
                                $abs = 1;
                            }else{
                                // $if_attendance_holiday_status = 'With-Pay';
                                // $abs = 0;

                                // if ($employee_schedule->working_hours > 8) 
                                // {
                                //     $total_reg_hrs = $employee_schedule->working_hours-1;
                                // }
                                // else 
                                // {
                                //     $total_reg_hrs = $employee_schedule->working_hours;
                                // }
                                $emp_schedule = $employee_schedule->working_hours-1;
                                $time_in = ($employee->attendance_logs)->where('date', (date('Y-m-d', strtotime($check_attendance))))->sortBy('datetime')->first();
                                $time_out = ($employee->attendance_logs)->where('date', (date('Y-m-d', strtotime($check_attendance))))->sortByDesc('datetime')->first();
                                $total_reg_hrs = number_format((strtotime($time_out->datetime) - strtotime($time_in->datetime))/3600, 2);
                                if ($total_reg_hrs >= ($emp_schedule/2))
                                {
                                    $abs=0;
                                    if ($employee_schedule->working_hours > 8) 
                                    {
                                        $total_reg_hrs = $employee_schedule->working_hours-1;
                                    }
                                    else 
                                    {
                                        $total_reg_hrs = $employee_schedule->working_hours;
                                    }
                                }
                                else 
                                {
                                    $abs=1;
                                    $total_reg_hrs=0;
                                }
                            }
                        }
                    }

                    // $abs = 0;
                    $approved_ot_hrs = employeeHasOTDetails($employee->approved_ots,date('Y-m-d',strtotime($date_r)));
                    // SH OT
                    if ($check_if_holiday == "Special Holiday")
                    {
                        if ($rest == "RESTDAY")
                        {
                            $rst_sh_ot = 8;
                            if ($approved_ot_hrs > 8)
                            {
                                $rst_sh_ot = $rst_sh_ot;
                                $rst_sh_ot_ge = floatval($approved_ot_hrs) - 8;
                            }
                            else
                            {
                                $rst_sh_ot = $approved_ot_hrs;
                            }
                        }
                        else 
                        {
                            $sh_ot = 8;
                            if ($approved_ot_hrs > 8)
                            {
                                $sh_ot = $sh_ot;
                                $sh_ot_ge = floatval($approved_ot_hrs) - 8;
                            }
                            else
                            {
                                $sh_ot = $approved_ot_hrs;
                            }
                        }

                        if ($employee_schedule)
                        {
                            $start_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_in_to);
                            $end_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_out_to);
                            
                            if(strtotime($start_schedule) > strtotime($end_schedule))
                            {
                                $s = date('Y-m-d', strtotime($final_time_in . ' +1 day'));
                                $end_schedule = date('Y-m-d H:i', strtotime($s." ".$employee_schedule->time_out_to));
                            }

                            if(strtotime($start_schedule) > strtotime($final_time_in))
                            {   
                                $nightdiff_start = $start_schedule;
                            }
                            if(strtotime($end_schedule) < strtotime($final_time_out))
                            {   
                                $nightdiff_end = $end_schedule;
                            }
                        }
                        
                        
                        if ($rest == "RESTDAY")
                        {
                            $rst_sh_nd = night_difference_per_company($nightdiff_start,$nightdiff_end);
                            // $schedule_hours = (strtotime($end_schedule)-strtotime($start_schedule))/3600;
                            
                            if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                            {
                                $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                                $rst_sh_ot_nd_ge =  night_difference_per_company($final_time_in,$final_time_out);
                                if($work_rest > 9 )
                                { 
                                    $rst_sh_ot_nd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                    $rst_sh_ot_nd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                    $rst_sh_ot_nd = $rst_sh_ot_nd_ge - $rst_sh_ot_nd;
                                    $rst_sh_ot_nd = $rst_sh_ot_nd-1;
                                    if($rst_sh_ot_nd <0)
                                    {
                                        $rst_sh_ot_nd = 0;
                                    }
                                    if($rst_sh_ot_nd_ge <0)
                                    {
                                        $rst_sh_ot_nd_ge = 0;
                                    }
                                }
                            }
                        }
                        else 
                        {
                            if ($employee_schedule)
                            {
                                $sh_nd = night_difference_per_company($nightdiff_start,$nightdiff_end);
                                $schedule_hours = (strtotime($end_schedule)-strtotime($start_schedule))/3600;
                                if($schedule_hours > 8)
                                {
                                    if($sh_nd >= 5)
                                    {
                                        $sh_nd = floatval($sh_nd)-1;
                                    }

                                    if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                                    {
                                        $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                                        $sh_ot_nd_ge =  night_difference_per_company($final_time_in,$final_time_out);
                                        if($work_rest > 9 )
                                        { 
                                            $sh_ot_nd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                            $sh_ot_nd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                            $sh_ot_nd = $sh_ot_nd_ge - $sh_ot_nd;
                                            $sh_ot_nd = $sh_ot_nd-1;
                                            if($sh_ot_nd <0)
                                            {
                                                $sh_ot_nd = 0;
                                            }
                                            if($sh_ot_nd_ge <0)
                                            {
                                                $sh_ot_nd_ge = 0;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        if ($rest == "RESTDAY")
                        {
                            $rst_lh_ot = 8;
                            if ($approved_ot_hrs > 8)
                            {
                                $rst_lh_ot = $rst_lh_ot;
                                $lh_ot_ge = floatval($approved_ot_hrs) - 8;
                            }
                            else
                            {
                                $rst_lh_ot = $approved_ot_hrs;
                            }
                        }
                        else 
                        {
                            $lh_ot = 8;
                            if ($approved_ot_hrs > 8)
                            {
                                $lh_ot = $lh_ot;
                                $lh_ot_ge = floatval($approved_ot_hrs) - 8;
                            }
                            else
                            {
                                $lh_ot = $approved_ot_hrs;
                            }
                        }
                        
                        if ($employee_schedule)
                        {
                            $start_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_in_to);
                            $end_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_out_to);
                            
                            if(strtotime($start_schedule) > strtotime($end_schedule))
                            {
                                $s = date('Y-m-d', strtotime($final_time_in . ' +1 day'));
                                $end_schedule = date('Y-m-d H:i', strtotime($s." ".$employee_schedule->time_out_to));
                            }
                            
                            if(strtotime($start_schedule) > strtotime($final_time_in))
                            {   
                                $nightdiff_start = $start_schedule;
                            }
                            if(strtotime($end_schedule) < strtotime($final_time_out))
                            {   
                                $nightdiff_end = $end_schedule;
                            }
                        }
                        
                        if ($rest == "RESTDAY")
                        {
                            if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                            {
                                $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                                $lh_nd_ge =  night_difference_per_company($final_time_in,$final_time_out);
                                if($work_rest > 9 )
                                { 
                                    $rst_lh_nd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                    $rst_lh_nd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                    $rst_lh_nd = $rst_lh_nd_ge - $rst_lh_nd;
                                    $rst_lh_nd = $rst_lh_nd-1;
                                    if($rst_lh_nd <0)
                                    {
                                        $rst_lh_nd = 0;
                                    }
                                    if($rst_lh_nd_ge <0)
                                    {
                                        $rst_lh_nd_ge = 0;
                                    }
                                }
                            }
                        }
                        else 
                        {
                            if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                            {
                                $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                                $lh_nd_ge =  night_difference_per_company($final_time_in,$final_time_out);
                                if($work_rest > 9 )
                                { 
                                    $lh_nd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                    $lh_nd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                    $lh_nd = $lh_nd_ge - $lh_nd;
                                    $lh_nd = $lh_nd-1;
                                    if($lh_nd <0)
                                    {
                                        $lh_nd = 0;
                                    }
                                    if($lh_nd_ge <0)
                                    {
                                        $lh_nd_ge = 0;
                                    }
                                }
                            }
                        }
                    }
                }

                // Remarks
                $leave_count = 0;
                $abs_half = 0;
                if($if_has_ob)
                {
                    $remarks = 'OB';
                }
                else 
                {
                    $if_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);
                    if($if_leave)
                    {
                        $l = explode('-',$if_leave);
                        $leave_count = (double) $l[1];
                        if(str_contains($if_leave,"Without"))
    
                        {
                            $leave_count = 0;
                            $abs_half = $l[1];
                        }
                    }
                    $remarks = $if_leave;
                }


                $data[] = [
                    'company' => $employee->company->company_code,
                    'employee_code' => $employee->employee_code,
                    'name' => $employee->last_name.' '.$employee->first_name,
                    'date_logs' => $date_r,
                    'schedule' => $schedule_display,
                    'time_in' => $final_time_in ? date('h:i A', strtotime($final_time_in)) : '',
                    'time_out' => $final_time_out ? date('h:i A', strtotime($final_time_out)) : '',
                    'absent' => number_format($abs,2),
                    'reg_hrs' => round($reg_hrs,2),
                    'late' => $late,
                    'undertime' => number_format($undertime,2),
                    'leave' => number_format($leave,2),
                    'leave_count' => $leave_count,
                    'overtime'=> number_format($overtime,2),
                    'reg_nd'=> number_format($night_diff,2),
                    'reg_ot_nd'=> number_format($night_diff_ot,2),
                    'restday_ot'=> number_format($restday_ot,2),
                    'restday_ot_ge'=> number_format($restday_ot_ge,2),
                    'restnd'=> number_format($restnd,2),
                    'restnd_ge'=> number_format($restnd_ge,2),
                    'lh_ot'=> number_format($lh_ot,2),
                    'lh_ot_ge'=> number_format($lh_ot_ge,2),
                    'lh_nd'=> number_format($lh_nd,2),
                    'lh_nd_ge'=> number_format($lh_nd_ge,2),
                    'sh_ot'=> number_format($sh_ot,2),
                    'sh_ot_ge'=> number_format($sh_ot_ge,2),
                    'sh_ot_nd'=> number_format($sh_ot_nd,2),
                    'sh_ot_nd_ge'=> number_format($sh_ot_nd_ge,2),
                    'rst_lh_ot'=> number_format($rst_lh_ot,2),
                    'rst_lh_ot_ge'=> number_format($lh_ot_ge,2),
                    'rst_lh_ot_nd'=> number_format($rst_lh_ot_nd,2),
                    'rst_lh_ot_nd_ge'=> number_format($rst_lh_ot_nd_ge,2),
                    'rst_sh_ot'=> number_format($rst_sh_ot,2),
                    'rst_sh_ot_ge'=> number_format($rst_sh_ot_ge,2),
                    'rst_sh_ot_nd'=> number_format($rst_sh_ot_nd,2),
                    'rst_sh_ot_nd_ge'=> number_format($rst_sh_ot_nd_ge,2),
                    'remarks' => $remarks,
                    'if_has_ob' => $if_has_ob ? 'Yes' : 'No',
                ];
            }
        }
        
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => count($employees),
            'recordsFiltered' => count($employees),
            'data' => $data
        ]);
    }
}
