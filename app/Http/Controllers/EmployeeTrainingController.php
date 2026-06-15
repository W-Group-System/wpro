<?php

namespace App\Http\Controllers;

use App\Employee;
use App\EmployeeTraining;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class EmployeeTrainingController extends Controller
{
    public function index()
    {

        $employee = Employee::where('status', 'Active')->get();

        $employeeTraining = EmployeeTraining::get();

        return view(
            'hr-portal.training',
            array(
                'header' => 'hrPortal',
                'employee' => $employee,
                'employeeTraining' => $employeeTraining
            )
        );
    }

    public function store(Request $request)
    {
        $employeeTraining = new EmployeeTraining;
        $employeeTraining->employee_id = $request->user_id;
        $employeeTraining->start_date = $request->start_date;
        $employeeTraining->end_date = $request->end_date;
        $employeeTraining->bond_start_date = $request->bond_start_date;
        $employeeTraining->bond_end_date = $request->bond_end_date;
        $employeeTraining->amount = $request->amount;
        $employeeTraining->training = $request->training;
        $employeeTraining->encoded_by = auth()->user()->id;

        if ($request->file('file')) {
            $attachment = $request->file('file');
            $original_name = $attachment->getClientOriginalName();
            $name = time() . '_' . $attachment->getClientOriginalName();
            $attachment->move(public_path() . '/training/', $name);
            $file_name = '/training/' . $name;
            
        $employeeTraining->attachment = $file_name;
        }
        if ($request->file('training_attachment')) {
            $attachment = $request->file('training_attachment');
            $original_name = $attachment->getClientOriginalName();
            $name = time() . '_' . $attachment->getClientOriginalName();
            $attachment->move(public_path() . '/training/', $name);
            $file_name = '/training/' . $name;
            
            $employeeTraining->training_attachment = $file_name;
        }
   
        $employeeTraining->save();

        Alert::success('Successfully Added')->persistent('Dismiss');
        return back();
    }

    public function update(Request $request, $id)
    {
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

    public function delete($id)
    {
        $employeeTraining = EmployeeTraining::findOrFail($id);
        $employeeTraining->delete();

        Alert::success('Successfully Deleted')->persistent('Dismiss');
        return back();
    }

    public function employeeTrainingReports()
    {
        $employeeTraining = EmployeeTraining::where('employee_id', auth()->user()->employee->user_id)->get();

        return view(
            'hr_report.training',
            array(
                'header' => 'hrReport',
                'employeeTraining' => $employeeTraining
            )
        );
    }
}
