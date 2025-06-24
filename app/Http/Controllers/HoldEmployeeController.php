<?php

namespace App\Http\Controllers;

use App\AttendanceDetailedReport;
use App\Company;
use App\Employee;
use App\Payregs;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class HoldEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $header = 'Hold Employee';
        $company_data = $request->company;
        $employee_array = $request->employee;
        $cut_off_date = $request->cut_off_date;

        $employees = Employee::where('status','Active')->get();
        $companies = Company::whereNotIn('id',[1])->get();
        $cutoff = Payregs::select('cut_off_date')->groupBy('cut_off_date')->where('company_id', $company_data)->orderBy('cut_off_date', 'desc')->get()->pluck('cut_off_date')->toArray();

        $cut_off_pay_reg = AttendanceDetailedReport::select('cut_off_date')
            ->where('company_id', $request->company)
            ->whereNotIn('cut_off_date', $cutoff)
            ->groupBy('cut_off_date')
            ->get()
            ->pluck('cut_off_date')
            ->toArray();

        $attendance_detailed_reports = AttendanceDetailedReport::with('company','employee')->where('company_id', $company_data)
            ->select('cut_off_date','company_id','employee_no')
            ->where('cut_off_date', $cut_off_pay_reg)
            ->whereIn('employee_no', $employee_array)
            ->groupBy('cut_off_date','company_id','employee_no')
            ->get();
        
        return view('hold_employee.index', compact('header', 'employees','companies','cut_off_pay_reg','company_data','employee_array','attendance_detailed_reports','cut_off_date'));
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
    public function destroy(Request $request)
    {
        if ($request->has('employee_no'))
        {
            AttendanceDetailedReport::with('company','employee')->where('company_id', $request->company)
                ->where('cut_off_date', $request->cut_off_date)
                ->whereIn('employee_no', $request->employee_no)
                ->delete();

            Alert::success('Successfully Removed')->persistent('Dismiss');
        }
        else
        {
            Alert::warning('You are not selecting employee')->persistent('Dismiss');
        }
        
        return back();
    }
}
