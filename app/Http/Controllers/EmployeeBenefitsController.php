<?php

namespace App\Http\Controllers;

use App\Employee;
use App\EmployeeBenefits;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class EmployeeBenefitsController extends Controller
{
  public function index() {
    $allowedCompanies = getUserAllowedCompanies(auth()->user()->id);
    
    $employee = Employee::whereIn('company_id', $allowedCompanies)->where('status', 'Active')->get();

    $benefits = benefits();

    $employeeBenefits = EmployeeBenefits::with('user')->get();
    
    return view('employee_benefits.employee_benefits',
      array(
        'header' => 'masterfiles',
        'employee' => $employee,
        'benefits' => $benefits,
        'employeeBenefits' => $employeeBenefits
      )
    );
  }

  public function store(Request $request) {
    $employeeBenefits = new EmployeeBenefits;
    $employeeBenefits->user_id = $request->user_id;
    $employeeBenefits->benefits_name = $request->benefits;
    $employeeBenefits->date = date('Y-m-d');
    $employeeBenefits->amount = $request->amount;
    $employeeBenefits->posted_by = auth()->user()->id;
    $employeeBenefits->save();

    Alert::success('Successfully Added.')->persistent('Dismiss');
    return back();
  }

  public function update(Request $request, $id) {

    $employeeBenefits = EmployeeBenefits::findOrFail($id);
    $employeeBenefits->user_id = $request->user_id;
    $employeeBenefits->benefits_name = $request->benefits;
    $employeeBenefits->date = date('Y-m-d');
    $employeeBenefits->amount = $request->amount;
    $employeeBenefits->posted_by = auth()->user()->id;
    $employeeBenefits->save();

    Alert::success('Successfully Updated.')->persistent('Dismiss');
    return back();
  }

  public function delete($id) {
    $employeeBenefits = EmployeeBenefits::findOrFail($id);
    $employeeBenefits->delete();

    Alert::success('Successfully Deleted.')->persistent('Dismiss');
    return back();
  }
}
