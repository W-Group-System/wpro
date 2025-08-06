<?php

namespace App\Http\Controllers;

use App\AttendanceDetailedReport;
use App\Company;
use App\Employee;
use App\Payreg;
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
        $employee_array = $request->employee ? $request->employee : [];
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
            ->whereIn('cut_off_date', $cut_off_pay_reg)
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

    public function unpostCompany(Request $request)
    {
        // dd($request->all());
        $header = 'unpost_company';
        $company_data = $request->companies;
        $cut_off_date = $request->cut_off_date;

        $companies = Company::where('id','!=',1)->get();
        
        if ($companies)
        {
            $cutoff_date = Payreg::select('cut_off_date')->where('company_id', $company_data)->groupBy('cut_off_date')->get()->pluck('cut_off_date');
            $atd_cutoff_date = AttendanceDetailedReport::select('cut_off_date','company_id')->where('company_id', $company_data)->whereNotIn('cut_off_date', $cutoff_date)->groupBy('cut_off_date','company_id')->get();

            $attendance_detailed_reports = AttendanceDetailedReport::with('company')->select('cut_off_date','company_id')->where('company_id', $company_data)->where('cut_off_date', $cut_off_date)->groupBy('cut_off_date','company_id')->get();
        }

        return view('unpost_company.unpost_company',
            array(
                'header' => $header,
                'companies' => $companies,
                'attendance_detailed_reports' => $attendance_detailed_reports,
                'atd_cutoff_date' => $atd_cutoff_date,
                'company_data' => $company_data,
                'cut_off_date' => $cut_off_date
            )
        );
    }

    public function unpostPerCompany(Request $request)
    {
        $attendance_detailed_reports =  AttendanceDetailedReport::where('company_id', $request->company_id)->where('cut_off_date', $request->cut_off_date)->get();
        if(count($attendance_detailed_reports) > 0)
        {
            AttendanceDetailedReport::where('company_id', $request->company_id)->where('cut_off_date', $request->cut_off_date)->delete();

            Alert::success('Successfully Unposted')->persistent('Dismiss');
        }
        else
        {
            Alert::error('Not Found')->persistent('Dismiss');
        }

        return back();
    }
}
