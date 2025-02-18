<?php

namespace App\Http\Controllers;

use App\Company;
use App\Department;
use App\Employee;
use Illuminate\Http\Request;

class SlBankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request->all());
        $header = 'sl_banks';
        $company_id = $request->company;
        $department_id = $request->department;

        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $companies = Company::whereIn('id', $allowed_companies)->get();
        $departments = Department::where('status',1)->get();

        $employees = Employee::where('company_id', $request->company)
            // ->where('department_id', $department_id)
            ->where('status', 'Active')
            ->get();

        return view('sl_banks.sl_banks', compact(
            'header', 
            'companies', 
            'employees', 
            'company_id', 
            'departments', 
            'department_id'
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
