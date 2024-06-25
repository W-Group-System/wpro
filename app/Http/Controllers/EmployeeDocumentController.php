<?php

namespace App\Http\Controllers;

use App\Employee;
use App\EmployeeDocument;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class EmployeeDocumentController extends Controller
{
  public function index() {

    $documentTypes = documentTypes();
    $employeeSubmitted = collect($documentTypes)->keys();

    // $employeeSubmitted = EmployeeDocument::whereIn('document_type', $employeeSubmitted)->get();
    // dd($employeeSubmitted);

    $employee = Employee::with([
        'employeeDocuments' => function($query)use($employeeSubmitted) {
          $query->whereIn('document_type', $employeeSubmitted);
        }
      ])
    ->where('status', 'Active')
    // ->where('id', 117)
    ->get();

    return view('hr-portal.employee-document',
      array(
        'header' => 'hrPortal',
        'employee' => $employee
      )
    );
  }

  public function upload(Request $request, $id) {
    $request->validate([
      'file' => 'mimes:pdf|max:2048'
    ]);

    $employeeDocuments = EmployeeDocument::where('employee_id', $id)
      ->where('document_type', $request->document_type)
      ->first();

    if (!empty($employeeDocuments)) {
      $employeeDocs = EmployeeDocument::findOrFail($employeeDocuments->id);
      $employeeDocs->employee_id = $id;
      $employeeDocs->document_type = $request->document_type;
      
      if ($request->hasFile('file')) {
        $files = $request->file('file');
        $fileName = time().'-'.$files->getClientOriginalName();
        $files->move(public_path().'/employee_documents/', $fileName);
        $employeeDocs->file_path = '/employee_documents/'.$fileName;
        $employeeDocs->file_name = $fileName;
      }

      $employeeDocs->save();

    }
    else {
      $files = $request->file('file');
      $fileName = time().'-'.$files->getClientOriginalName();
      $files->move(public_path().'/employee_documents/', $fileName);

      $employeeDocs = new EmployeeDocument;
      $employeeDocs->employee_id = $id;
      $employeeDocs->document_type = $request->document_type;
      $employeeDocs->file_path = '/employee_documents/'.$fileName;
      $employeeDocs->file_name = $fileName;
      $employeeDocs->save();
      
    }

    Alert::success('Successfully Upload.')->persistent('Dismiss');
    return back();
  }
}
