<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Exports\OvertimeTemplate;
use App\Exports\OvertimeTemplateExport;
use App\Imports\UploadImport;
use App\UploadType;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class UploadController extends Controller
{
    public function index()
    {
        $uploadTypes = UploadType::with('user')->where('uploaded_by', auth()->user()->id)->orderBy('uploaded_at', 'DESC')->paginate(10);

        return view(
            'upload.upload',
            array(
                'header' => 'upload',
                'uploadTypes' => $uploadTypes
            )
        );
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'mimes:xlsx,csv,xls'
        ]);
        
        Excel::import(new UploadImport($request->type), $request->file);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path() . '/uploads/', $fileName);

        $uploadData = new UploadType;
        $uploadData->file_name = $fileName;
        $uploadData->file_path = '/uploads/' . $fileName;
        $uploadData->uploaded_at = date('Y-m-d');
        $uploadData->uploaded_by = auth()->user()->id;
        $uploadData->type = $request->type;
        $uploadData->save();

        return back();
    }

    public function export(Request $request)
    {

        if ($request->type == "OB") {
            $template = public_path('/template/ob_template.xlsx');

            if (file_exists($template)) {
                return response()->download($template, 'Official Business.xlsx');
            }
        } else if ($request->type == "OT") {
            // $template = public_path('/template/ot_template.xlsx');

            // if (file_exists($template)) {
            //     return response()->download($template, 'Overtime.xlsx');
            // }
            return Excel::download(new OvertimeTemplateExport, 'Overtime Template.xlsx');
            
        } else if ($request->type == "VL/SL") {
            $template = public_path('/template/leave_template.xlsx');

            if (file_exists($template)) {
                return response()->download($template, 'Leave.xlsx');
            }
        }
        else if ($request->type == "DTR") {
            $template = public_path('/template/dtr_template.xlsx');

            if (file_exists($template)) {
                return response()->download($template, 'DTR Template.xlsx');
            }
        } else {
            Alert::error("No Template")->persistent('Dismiss');
        }

        return back();
    }
}
