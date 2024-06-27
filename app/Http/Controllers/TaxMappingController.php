<?php

namespace App\Http\Controllers;

use App\Employee;
use App\TaxMapping;
use App\UserAllowedCompany;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TaxMappingController extends Controller
{
  public function index() {
    $allowedCompanies = getUserAllowedCompanies(auth()->user()->id);
    
    $employee = Employee::where('status', 'Active')->whereIn('company_id', $allowedCompanies)->get();
    
    $taxMapping = TaxMapping::with('employee')->get();
    
    return view('payroll_settings.tax-mapping', array(
        'header' => 'payrollSetting',
        'employee' => $employee,
        'taxMapping' => $taxMapping
      )
    );
  }

  public function addTaxMapping(Request $request) {
    
    $taxMapping = new TaxMapping;
    $taxMapping->employee_id = $request->employee;
    $taxMapping->sss = isset($request->sss)?$request->sss:0;
    $taxMapping->philhealth = isset($request->philhealth)?$request->philhealth:0;
    $taxMapping->pagibig = isset($request->pagibig)?$request->pagibig:0;
    $taxMapping->tin = isset($request->tin)?$request->tin:0;
    $taxMapping->tax_percent = $request->tax_percent/100;
    $taxMapping->save();

    Alert::success('Successfully Saved')->persistent('Dismiss');
    return back();
  }

  public function updateTaxMapping(Request $request, $id) {

    $taxMapping = TaxMapping::findOrFail($id);
    $taxMapping->employee_id = $request->employee;
    $taxMapping->sss = isset($request->sss)?$request->sss:0;
    $taxMapping->philhealth = isset($request->philhealth)?$request->philhealth:0;
    $taxMapping->pagibig = isset($request->pagibig)?$request->pagibig:0;
    $taxMapping->tin = isset($request->tin)?$request->tin:0;
    $taxMapping->tax_percent = $request->tax_percent/100;
    $taxMapping->save();

    Alert::success('Successfully Updated')->persistent('Dismiss');
    return back();
  }

  public function deleteTaxMapping($id) {
    $taxMapping = TaxMapping::findOrFail($id);
    $taxMapping->delete();

    Alert::success('Successfully Deleted')->persistent('Dismiss');
    return back();
  }
}
