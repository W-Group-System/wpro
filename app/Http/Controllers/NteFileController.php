<?php

namespace App\Http\Controllers;

use App\Employee;
use App\NteFile;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class NteFileController extends Controller
{
    public function index()
    {
        $employee = Employee::where('status', 'Active')->get();

        $nteFile = NteFile::with('employee')->get();

        return view(
            'hr-portal.ntefiles',
            array(
                'header' => 'hrPortal',
                'employee' => $employee,
                'nteFile' => $nteFile
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'mimes:pdf|max:2048'
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path() . '/nte_files/', $fileName);

        $nteFile = new NteFile;
        $nteFile->employee_id = $request->user_id;
        $nteFile->violation = $request->violation;
        $nteFile->file_path = '/nte_files/' . $fileName;
        $nteFile->file_name = $fileName;
        $nteFile->save();

        Alert::success('Successfully saved')->persistent('Dismiss');
        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'file' => 'mimes:pdf|max:2048'
        ]);

        $nteFile = NteFile::findOrFail($id);
        $nteFile->employee_id = $request->user_id;
        $nteFile->violation = $request->violation;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path() . '/nte_files/', $fileName);

            $nteFile->file_path = '/nte_files/' . $fileName;
            $nteFile->file_name = $fileName;
        }

        $nteFile->save();

        Alert::success('Successfully updated.')->persistent('Dismiss');
        return back();
    }

    public function nteReports()
    {
        $employeeNte = NteFile::with('employee')->where('employee_id', auth()->user()->employee->id)->get();

        return view(
            'hr_report.nte_report',
            array(
                'header' => 'hrReport',
                'employeeNte' => $employeeNte
            )
        );
    }
}
