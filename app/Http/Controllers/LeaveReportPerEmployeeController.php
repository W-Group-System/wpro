<?php

namespace App\Http\Controllers;

use App\Employee;
use App\EmployeeLeave;
use Illuminate\Http\Request;

class LeaveReportPerEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $header = 'reports';
        $from = $request->from;
        $to = $request->to;
        $status = $request->status;
        $employee = !empty($request->employee) ? $request->employee : [];
        
        $employees = Employee::with('user_info')->where('status','Active')->get();
        
        $employee_leaves = [];
        if(isset($request->from) && isset($request->to)){
            $employee_leaves = EmployeeLeave::with('user','leave','approver.approver_info')
            ->whereDate('date_from', '>=', $from)
            ->whereDate('date_to', '<=', $to)
            ->whereIn('user_id', $employee)
            ->get();

            if($status != "ALL" && $status != 'Approved')
            {
                $employee_leaves = $employee_leaves->where('status',$status);
            }
            elseif($status == 'Approved')
            {
                $employee_leaves = $employee_leaves->whereIn('status', ['Approved', 'approved']);
            }
        }
        // dd($employee_leaves);
        return view('leave_reports.leave_reports_per_employee', compact('header','employees','from','to','status','employee','employee_leaves'));
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
