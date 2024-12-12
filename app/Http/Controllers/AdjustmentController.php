<?php

namespace App\Http\Controllers;

use App\SalaryAdjustment;
use App\Allowance;
use App\Employee;
use Illuminate\Http\Request;
use App\SalaryAdjustmentName;
use RealRashid\SweetAlert\Facades\Alert;
class AdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $employees = Employee::select('id','user_id','first_name','last_name','middle_name')
                                    ->whereIn('company_id',$allowed_companies)
                                    ->where('status','Active')
                                    ->get();
        $adjustments = SalaryAdjustment::where('pay_reg_id',null)->get();
        $names = SalaryAdjustmentName::get();
        if($request->status)
        {
            $adjustments = SalaryAdjustment::where('pay_reg_id','!=',null)->get();
        }
        return view('salary_managements.index', array(
            'header' => 'salaryAdjustments',
            'employees' => $employees,
            'adjustments' => $adjustments,
            'names' => $names,
        ));
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
        // dd($request->all());
  
        $new_adjustment = new SalaryAdjustment;
        $new_adjustment->name = $request->name;
        $new_adjustment->employee_id = $request->employee;
        $new_adjustment->amount = $request->amount;
        $new_adjustment->remarks = $request->remarks;
        $new_adjustment->created_by = auth()->user()->id;
        $new_adjustment->save();
        Alert::success('Successfully Stored')->persistent('Dismiss');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Adjustment  $adjustment
     * @return \Illuminate\Http\Response
     */
    public function show(Adjustment $adjustment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Adjustment  $adjustment
     * @return \Illuminate\Http\Response
     */
    public function edit(Adjustment $adjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Adjustment  $adjustment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Adjustment $adjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Adjustment  $adjustment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Adjustment $adjustment)
    {
        //
    }
}
