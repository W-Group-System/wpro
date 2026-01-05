<?php

namespace App\Http\Controllers;

use App\Employee;
use App\EmployeeLeaveCredit;
use App\EmployeeLeaveList;
use App\Helpers\HelperClass;
use App\Leave;
use App\Level;
use DateTime;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use stdClass;

class EmployeeLeaveListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $header = 'employee_leaves_list';
        // $employee_leave_lists = EmployeeLeaveList::select('leave_id','user_id')
        //     ->whereHas('user.employee', function($q) {
        //         $q->where('status','Active');
        //     })
        //     ->with('leave','user')
        //     ->groupBy('leave_id','user_id')
        //     ->get();
        $employee_leave_lists = Employee::with('employee_leave_list.leave','user_info')
            ->whereHas('employee_leave_list')
            ->where('status','Active')
            ->get();
        
        // Dropdown
        $employees = Employee::where('status', 'Active')->get();
        $leaves = Leave::get();
        $levels = Level::get();

        return view('employee_leave_list.index', 
            array(
                'employee_leave_lists' => $employee_leave_lists,
                'employees' => $employees,
                'leaves' => $leaves,
                'levels' => $levels,
                'header' => $header,
            )
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
        // dd($request->all());
        if ($request->type == 1)
        {
            $employee = Employee::where('user_id', $request->employee)->first();
            $leave_entitlement = get_leave_entitlement($employee->level, $request->date_hired, $employee->company_id);
            $leave_credits = compute_leave_credits($request->leave, $leave_entitlement, $request->date_hired, $request->date_regularization);
            $earned_per_month = earn_per_month($request->leave, $request->date_regularization, $leave_entitlement);
            // dd($earned_per_month);
            $employee_leave_list = new EmployeeLeaveList;
            $employee_leave_list->user_id = $request->employee;
            $employee_leave_list->leave_id = $request->leave;
            $employee_leave_list->year = date('Y');
            $employee_leave_list->month = date('m');
            $employee_leave_list->total_leaves = $leave_credits;
            $employee_leave_list->leave_entitlement = $leave_entitlement;
            if ($request->leave == 1)
            {
                $employee_leave_list->earned_per_month = $earned_per_month;
            }
            else
            {
                $employee_leave_list->earned_per_month = $request->leave_credit;
            }
            $employee_leave_list->save();
        }
        elseif($request->type == 2)
        {
            $employee_leave_list = new EmployeeLeaveList;
            $employee_leave_list->user_id = $request->employee;
            $employee_leave_list->leave_id = $request->leave;
            $employee_leave_list->earned_per_month = $request->earned_per_month;
            $employee_leave_list->year = date('Y');
            $employee_leave_list->month = date('m');
            $employee_leave_list->save();
        }

        Alert::success('Successfully Saved')->persistent('Dismiss');
        return back();
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

    public function refreshEmployee(Request $request)
    {
        $employee = Employee::where('user_id', $request->employee_id)->first();

        return $employee;
    }

    public function refreshLeave()
    {
        $employees = Employee::with('employee_leave_list')
            ->where('status','Active')
            ->whereHas('employee_leave_list')
            // ->where('user_id', 470)
            ->get();

        $year = date('Y');
        $month = date('m');
        foreach($employees as $employee)
        {
            $leave_entitlement = get_leave_entitlement($employee->level, $employee->original_date_hired, $employee->company_id);
            $total_earned_month = intval($leave_entitlement) / 12;

            $leave_credits = ($employee->employee_leave_list)->where('leave_id',1)->sortByDesc('id')->first();
            
            if($leave_credits != null)
            {
                $check_if_exist_vl = EmployeeLeaveList::where('user_id', $employee->user_id)
                    ->where(function($q) use($month,$year){
                        $q->whereMonth('earned_date',$month)
                        ->whereYear('earned_date',$year);
                    })
                    ->whereNotNull('earned_date')
                    ->where('leave_id',1)
                    ->first();                
                
                if(empty($check_if_exist_vl)){
                    $earned_leave = new EmployeeLeaveList;
                    $earned_leave->leave_id = 1; // Vacation Leave
                    $earned_leave->user_id = $employee->user_id;
                    // $earned_leave->earned_day = $day;
                    $earned_leave->month = $month;
                    $earned_leave->year = $year;
                    $earned_leave->earned_date = date('Y-m-d');
                    // $earned_leave->earned_per_month = $leave_credits->earned_per_month;
                    $earned_leave->earned_per_month = number_format($total_earned_month, 3);
                    $earned_leave->save();
                }
            }
        }

        return "success";
    }

    public function leaveReport(Request $request)
    {
        // dd($request->all());
        $header = 'leave_report';
        $employees = Employee::with('employee_leave_list')
            ->where('status','Active')
            ->whereHas('employee_leave_list')
            // ->where('employee_code','A346512')
            ->get();

        $sl_leave_array = [];
        $vl_leave_array = [];
        foreach($employees as $employee)
        {
            // $sl = ($employee_leave_list)->where('leave_id', 2)->first();
            $used_sl_this_yr = HelperClass::usedSlVlThisYear($employee->user_id,2,$employee->original_date_hired,$employee->ScheduleData);
            $used_vl_this_yr = HelperClass::usedSlVlThisYear($employee->user_id,1,$employee->original_date_hired,$employee->ScheduleData);

            $total_earned_vl = ($employee->employee_leave_list)->where('leave_id', 1)->pluck('earned_per_month')->sum();
            $total_earned_sl = ($employee->employee_leave_list)->where('leave_id', 2)->pluck('earned_per_month')->sum();
            
            $obj = new stdClass;
            $obj->employee_id = $employee->employee_code;
            $obj->lastname = $employee->last_name;
            $obj->name = $employee->last_name .', '.$employee->first_name;
            $obj->leave_type = 'Sick Leave';
            $obj->leave_entitlement =  get_leave_entitlement($employee->level, $employee->original_date_hired, $employee->company_id);
            $obj->used_leave = $used_sl_this_yr;
            $obj->total_earned_sl = $total_earned_sl;
            $sl_leave_array[] = $obj;

            $obj_vl = new stdClass;
            $obj_vl->employee_id = $employee->employee_code;
            $obj_vl->lastname = $employee->last_name;
            $obj_vl->name = $employee->last_name .', '.$employee->first_name;
            $obj_vl->leave_type = 'Vacation Leave';
            $obj_vl->leave_entitlement =  get_leave_entitlement($employee->level, $employee->original_date_hired, $employee->company_id);
            $obj_vl->used_leave = $used_vl_this_yr;
            $obj_vl->total_earned_vl = $total_earned_vl;
            $vl_leave_array[] = $obj_vl;
        }

        $merge_arr = collect($vl_leave_array)->merge($sl_leave_array);

        return view('employee_leave_list.leave_report', compact('header', 'merge_arr'));
    }

    public function refreshLeaveCredit(Request $request)
    {
        // dd($request->all());
        $employee = Employee::where('user_id', $request->employee)->first();
        $leave_entitlement = get_leave_entitlement($employee->level, $request->date_hired, $employee->company_id);
        $leave_credits = compute_leave_credits($request->leave, $leave_entitlement, $request->date_hired, $request->date_regularization);
        
        return $leave_credits;
    }

    public function refreshSickLeave(Request $request)
    {

    }
}
