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

                $employees = Employee::findOrFail($request->emp_id);
                $timekeeping_in = AttendanceLog::where('emp_code',$employees->employee_number)->where('date', $request->date)->orderBy('id','asc')->first();
                $timekeeping_out = AttendanceLog::where('emp_code',$employees->employee_number)->where('date', $request->date)->orderBy('id','desc')->first();
                
                if ($timekeeping_in && $timekeeping_out)
                {
                    $timekeeping_in->datetime = $dtr_correction->time_in;
                    $timekeeping_in->save();
    
                    $timekeeping_out->datetime = $dtr_correction->time_out;
                    $timekeeping_out->save();
                }
                else
                {
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

        $dtr_status = DtrStatus::where('employee_id', $request->employee_id)->where('date', $request->date)->first();
        if ($dtr_status)
        {
            $dtr_status->status = 'Pending';
            $dtr_status->save();
        }

        Alert::success('Successfully Saved')->persistent('Dismiss');
        return back();
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
        $from_date = $request->date_from;   
        $to_date = $request->date_to;
        $company_data = $request->company;
        $department_data = $request->department;

        $companies = Company::where('id','!=',1)->get();
        $departments = Department::get();
        $employees = Employee::select('id','user_id','employee_code','first_name','last_name','schedule_id','employee_number','company_id','department_id')
            ->with(['schedule_info','approved_ots',
                'attendance_logs' => function($q)use($from_date,$to_date) {
                    $q->whereBetween('datetime', [$from_date.' 00:00:01', date('Y-m-d 23:59:59', strtotime($to_date. '+1 day'))])->orderBy('datetime','asc');
                    // $q->whereBetween('datetime', [$from_date.' 00:00:01', $to_date.' 23:59:59'])->orderBy('datetime','asc');
                },
                'dtr_correction.dtr_correction_approver.user'
            ])
            ->where('company_id', $request->company)
            ->when($department_data, function($q)use($department_data) {
                $q->where('department_id', $department_data);
            })
            ->where('status','Active')
            // ->where('employee_code','A2109625')
            // ->where('employee_code','A2109925')
            // ->where('employee_code','A2110025')
            // ->where('employee_code','A192524')
            // ->whereIn('employee_code',['A2112625','A3176324','A189423',])
            ->orderBy('last_name','asc')
            ->get();

        $attendance_controller = new AttendanceController;
        $date_range =  $attendance_controller->dateRange($from_date, $to_date);

        return view('timekeeping.index',
            array(
                'header' => $header,
                'companies' => $companies,
                'departments'=> $departments,
                'employees' => $employees,
                'date_range' => $date_range,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'company_data' => $company_data,
                'department_data' => $department_data,
            )
        );
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
    }

    public function moveToForPosting(Request $request)
    {
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

        Alert::success('Successfully Moved')->persistent('Dismiss');
        return back();
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
}
