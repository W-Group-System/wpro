<?php

namespace App\Http\Controllers;

use App\Employee;
use App\EmployeeLeave;
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
    public function index(Request $request)
    {
        $header = 'employee_leaves_list';
        $search = trim($request->search);
        // $employee_leave_lists = EmployeeLeaveList::select('leave_id','user_id')
        //     ->whereHas('user.employee', function($q) {
        //         $q->where('status','Active');
        //     })
        //     ->with('leave','user')
        //     ->groupBy('leave_id','user_id')
        //     ->get();
        $employee_leave_lists = collect();
        if ($search) {
            $employee_leave_lists = Employee::with('employee_leave_list.leave','employee_leave_credits','user_info','ScheduleData')
                ->whereHas('employee_leave_list')
                ->where('status','Active')
                ->where(function($q) use ($search) {
                    $q->where('employee_code', 'like', '%'.$search.'%')
                    ->orWhere('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%'.$search.'%'])
                    ->orWhereRaw("CONCAT(last_name, ', ', first_name) LIKE ?", ['%'.$search.'%']);
                })
                ->get();
        }

        $leave_usages = collect();
        if ($employee_leave_lists->isNotEmpty()) {
            $leave_usages = EmployeeLeave::whereIn('user_id', $employee_leave_lists->pluck('user_id')->toArray())
                ->whereIn('status', ['Approved', 'Pending'])
                ->where('status', '!=', 'Cancelled')
                ->where('withpay', 1)
                ->get()
                ->groupBy('user_id');
        }
        
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
                'leave_usages' => $leave_usages,
                'search' => $search,
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
        $header = 'leave_report';
        $merge_arr = collect();

        return view('employee_leave_list.leave_report', compact('header', 'merge_arr'));
    }

    public function leaveReportEmployees(Request $request)
    {
        $search = trim($request->get('term', $request->get('q', '')));

        if (strlen($search) < 2) {
            return response()->json(['results' => []]);
        }

        $employees = Employee::select('id', 'employee_code', 'first_name', 'last_name')
            ->where('status','Active')
            ->whereHas('employee_leave_list')
            ->where(function ($query) use ($search) {
                $query->where('employee_code', 'like', '%'.$search.'%')
                    ->orWhere('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%'.$search.'%'])
                    ->orWhereRaw("CONCAT(last_name, ', ', first_name) LIKE ?", ['%'.$search.'%']);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(25)
            ->get();

        return response()->json([
            'results' => $employees->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'text' => trim($employee->employee_code.' - '.$employee->last_name.', '.$employee->first_name),
                ];
            })->values(),
        ]);
    }

    public function leaveReportSearch(Request $request)
    {
        $employeeId = $request->get('employee_id');

        if (empty($employeeId)) {
            return response()->json([]);
        }

        $employees = Employee::with('employee_leave_list', 'ScheduleData')
            ->where('status','Active')
            ->whereHas('employee_leave_list')
            ->where('id', $employeeId)
            ->get();

        return response()->json($this->buildLeaveReportRows($employees)->values());
    }

    private function buildLeaveReportRows($employees)
    {
        $sl_leave_array = [];
        $vl_leave_array = [];
        foreach($employees as $employee)
        {
            $leaveTallies = getEmployeeLeaveCreditTallies($employee)->keyBy('leave_id');
            $slTally = $leaveTallies->get(2);
            $vlTally = $leaveTallies->get(1);
            
            $obj = new stdClass;
            $obj->employee_id = $employee->employee_code;
            $obj->lastname = $employee->last_name;
            $obj->name = $employee->last_name .', '.$employee->first_name;
            $obj->leave_type = 'Sick Leave';
            $obj->leave_entitlement =  get_leave_entitlement($employee->level, $employee->original_date_hired, $employee->company_id);
            $obj->used_leave = optional($slTally)->used ?: 0;
            $obj->total_earned_sl = optional($slTally)->total ?: 0;
            $obj->balance = optional($slTally)->balance ?: 0;
            $sl_leave_array[] = $obj;

            $obj_vl = new stdClass;
            $obj_vl->employee_id = $employee->employee_code;
            $obj_vl->lastname = $employee->last_name;
            $obj_vl->name = $employee->last_name .', '.$employee->first_name;
            $obj_vl->leave_type = 'Vacation Leave';
            $obj_vl->leave_entitlement =  get_leave_entitlement($employee->level, $employee->original_date_hired, $employee->company_id);
            $obj_vl->used_leave = optional($vlTally)->used ?: 0;
            $obj_vl->total_earned_vl = optional($vlTally)->total ?: 0;
            $obj_vl->balance = optional($vlTally)->balance ?: 0;
            $vl_leave_array[] = $obj_vl;
        }

        return collect($vl_leave_array)->merge($sl_leave_array)->sortBy('lastname');
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
        $employees = Employee::with('employee_leave_list')
                        ->where('status','Active')
                        ->whereHas('employee_leave_list')
                        ->get();
        
        $year = date('Y');
        $month = date('m');
        foreach($employees as $employee)
        {
            $leave_entitlement = get_leave_entitlement($employee->level, $employee->original_date_hired, $employee->company_id);
            $leave_credits = ($employee->employee_leave_list)->where('leave_id',2)->sortByDesc('id')->first();
            
            if($leave_credits != null)
            {
                $check_if_exist_vl = EmployeeLeaveList::where('user_id', $employee->user_id)
                                        ->where('year', $year)
                                        ->whereNotNull('earned_date')
                                        ->where('leave_id',2)
                                        ->first();                
                
                if(empty($check_if_exist_vl)){
                    $earned_leave = new EmployeeLeaveList;
                    $earned_leave->leave_id = 2;
                    $earned_leave->user_id = $employee->user_id;
                    $earned_leave->month = $month;
                    $earned_leave->year = $year;
                    $earned_leave->earned_date = date('Y-m-d');
                    $earned_leave->earned_per_month = $leave_entitlement;
                    $earned_leave->save();
                }
            }
        }

        return "success";
    }
}
