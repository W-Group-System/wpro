<?php

namespace App\Http\Controllers;

use App\Employee;
use App\EmployeeTraining;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class EmployeeTrainingController extends Controller
{
  public function index() {

    $employee = Employee::where('status', 'Active')->get();

    $employeeTraining = EmployeeTraining::get();

    return view('hr-portal.training', array(
        'header' => 'hrPortal',
        'employee' => $employee,
        'employeeTraining' => $employeeTraining
      )
    );
  }

  public function store(Request $request) {
    $employeeTraining = new EmployeeTraining;
    $employeeTraining->employee_id = $request->user_id;
    $employeeTraining->start_date = $request->start_date;
    $employeeTraining->end_date = $request->end_date;
    $employeeTraining->amount = $request->amount;
    $employeeTraining->training = $request->training;
    $employeeTraining->save();

    Alert::success('Successfully Added')->persistent('Dismiss');
    return back();
  }

  public function update(Request $request, $id) {
    $employeeTraining = EmployeeTraining::findOrFail($id);
    $employeeTraining->employee_id = $request->employee;
    $employeeTraining->start_date = $request->start_date;
    $employeeTraining->end_date = $request->end_date;
    $employeeTraining->amount = $request->amount;
    $employeeTraining->training = $request->training;
    $employeeTraining->save();
    
    Alert::success('Successfully Updated')->persistent('Dismiss');
    return back();
  }

  public function delete($id) {
    $employeeTraining = EmployeeTraining::findOrFail($id);
    $employeeTraining->delete();

    Alert::success('Successfully Deleted')->persistent('Dismiss');
    return back();
  }
}
