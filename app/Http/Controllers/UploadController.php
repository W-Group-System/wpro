<?php

namespace App\Http\Controllers;

use App\AttendanceLog;
use App\Company;
use App\Employee;
use App\Exports\OvertimeTemplate;
use App\Exports\OvertimeTemplateExport;
use App\Imports\UploadImport;
use App\UploadType;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Shuchkin\SimpleXLSX;
use App\EmployeeLeave;
use App\EmployeeOb;
use App\EmployeeOvertime;
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
        $path = $request->file('file')->getRealPath();

        $xlsx = SimpleXLSX::parse($path)->rows();
        // dd($xlsx);
        // Excel::import(new UploadImport($request->type), $request->file);
        // dd($xlsx);
        
        foreach ($xlsx as $key => $row) {
            if($key > 0)
            {
                if($row[0])
                {
                    $user_id = Employee::where('employee_code', $row[0])->pluck('user_id')->toArray();
                    $dateFiled =date('Y-m-d',strtotime($row[2]));
                    
                    
                    if ($request->type == "OB") {
                        $employeeOb = EmployeeOb::whereIn('user_id', $user_id)
                            ->where('applied_date',date('Y-m-d',strtotime($row[3])))
                            ->first();
                        // dd($row[5]);
                        foreach ($user_id as $uid) {
                            if (empty($employeeOb)) {
                                $employeeOb = new EmployeeOb;
                                $employeeOb->user_id = $uid;
                                $employeeOb->applied_date = date('Y-m-d',strtotime($row[3]));
                                $date = 
                                $employeeOb->date_from =date("Y-m-d H:i:s",strtotime(date('Y-m-d',strtotime($row[3]))." ".date('H:i:s',strtotime($row[5]))));
                                $employeeOb->date_to = date("Y-m-d H:i:s",strtotime(date('Y-m-d',strtotime($row[4]))." ".date('H:i:s',strtotime($row[6]))));
                                $employeeOb->approved_date = date('Y-m-d',strtotime($row[4]));
                                $employeeOb->status = $row[10];
                                $employeeOb->created_by = auth()->user()->id;
                                $employeeOb->remarks = $row[9];
                                $employeeOb->save();
                            } else {
                                $employeeOb->date_from =date("Y-m-d H:i:s",strtotime(date('Y-m-d',strtotime($row[3]))." ".date('H:i:s',strtotime($row[5]))));
                                $employeeOb->date_to = date("Y-m-d H:i:s",strtotime(date('Y-m-d',strtotime($row[4]))." ".date('H:i:s',strtotime($row[6]))));
                                $employeeOb->approved_date = date('Y-m-d',strtotime($row[4]));
                                $employeeOb->status =  $row[10];
                                $employeeOb->created_by = auth()->user()->id;
                                $employeeOb->remarks = $row[9];
                                $employeeOb->save();
                            }
                        }
                    } else if ($request->type == "OT") {
                        $employeeOt = EmployeeOvertime::whereIn('user_id', $user_id)->where('ot_date',  date('Y-m-d', strtotime($row[3])))->first();
                        foreach ($user_id as $uid) {
                            if (empty($employeeOt)) {
                                $employeeOt = new EmployeeOvertime;
                                $employeeOt->user_id = $uid;
                                $employeeOt->ot_date =  date('Y-m-d', strtotime($row[3]));
                                $employeeOt->start_time = date('Y-m-d h:i:s', strtotime($row[3]));
                                $employeeOt->end_time = date('Y-m-d h:i:s', strtotime($row[4]));
                                $employeeOt->approved_date = date('Y-m-d h:i:s', strtotime($row[7]));
                                $employeeOt->status = $row[10];
                                $employeeOt->ot_approved_hrs = $row[5];
                                $employeeOt->break_hrs = $row[6];
                                $employeeOt->created_by = auth()->user()->id;
                                $employeeOt->remarks = $row[9];
                                $employeeOt->save();
                            } else {
                                $employeeOt->start_time = date('Y-m-d h:i:s', strtotime($row[3]));
                                $employeeOt->end_time = date('Y-m-d h:i:s', strtotime($row[4]));
                                $employeeOt->approved_date = date('Y-m-d h:i:s', strtotime($row[7]));
                                $employeeOt->status = $row[10];
                                $employeeOt->ot_approved_hrs = $row[5];
                                $employeeOt->break_hrs = $row[6];
                                $employeeOt->remarks = $row[9];
                                $employeeOt->created_by = auth()->user()->id;
                                $employeeOt->save();
                            }
                        }
                    } else if ($request->type == "VL/SL") {
                        $types = 0;
                        $pay =1;
                        $halfday =0;
                        if ($row[6] == "Vacation Leave" || $row[6] == "VL") {
                            $types = 1;
                        }
                        if ($row[6] == "Sick Leave" || $row[6] == 'SL') {
                            $types = 2;
                        }
                        if ($row[6] == "Emergency Leave" || $row[6] == 'EL') {
                            $types = 6;
                        }
                        if ($row[6] == "Bereavement Leave" || $row[6] == 'BL') {
                            $types = 11;
                        }
                        if (str_contains($row[6],"without") || $row[6] == "LWOP"){
                            $types = 13;
                            $pay =0;

                        }
                        if($row[5] == .5)
                        {
                            $halfday = 1;
                        }
                        
                        $startDate = date('Y-m-d',strtotime($row[3]));
                        $endDate = date('Y-m-d',strtotime($row[4]));;
                        $approved_date = date('Y-m-d',strtotime($row[7]));;
        
                        $leaves = EmployeeLeave::whereIn('user_id', $user_id)
                            ->where('date_from', $startDate)
                            ->where('date_to', $endDate)
                            ->first();
        
                        foreach ($user_id as $uid) {
                            if (!empty($leaves)) {
                                $leaves = $leaves->delete();
                            }
        
                            $leaves = new EmployeeLeave;
                            $leaves->user_id = $uid;
                            $leaves->leave_type = $types;
                            $leaves->date_from =$startDate;
                            $leaves->date_to = $endDate;
                            $leaves->withpay = $pay;
                            $leaves->halfday = $halfday;
                            $leaves->approved_date = $approved_date;
                            $leaves->status = $row[10];
                            $leaves->created_by = auth()->user()->id;
                            $leaves->save();
                        }
                    }
                    else if ($request->type == "DTR") {
                        // $dateTime = Date::excelToDateTimeObject($row['date_time'])->format('Y-m-d H:i:s');
                        $dateTime = date('Y-m-d H:i:s',strtotime($row[1]));
                        
                        $attendanceLogs = AttendanceLog::where('emp_code', $row[0])->where('datetime', $dateTime)->first();
                        
                        if (empty($attendanceLogs)) {
                            $attendanceLogs = new AttendanceLog;
                            $attendanceLogs->emp_code = $row[0];
                            $attendanceLogs->date = date('Y-m-d', strtotime($row[1]));
                            $attendanceLogs->datetime = $dateTime;
                            $attendanceLogs->type = strtoupper($row[0]) == 'IN' ? 1 : 0;
                            $attendanceLogs->location = "DTR";
                            $attendanceLogs->ip_address = 'DTR Upload';
                            $attendanceLogs->save();
                        }
                        else {
                            $attendanceLogs->emp_code = $row[0];
                            $attendanceLogs->date = date('Y-m-d', strtotime($row[1]));
                            $attendanceLogs->datetime = $dateTime;
                            $attendanceLogs->type = strtoupper($row[0]) == 'IN' ? 1 : 0;
                            $attendanceLogs->save();
                        }
                    }
                }
           
            }
            
        }


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

        Alert::success('Successfully Uploaded')->persistent('Dismiss');
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

    public function obFiles(Request $request)
    {
        $header = 'ob_files';
        $companies = Company::where('id', 10)->get();
        $company_filter = $request->company;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        
        $files = UploadType::where('type','OB')
            ->whereHas('user.employee', function($q)use($company_filter) {
                $q->where('company_id', $company_filter);
            })
            ->whereBetween('uploaded_at', [$date_from, $date_to])
            ->orderBy('uploaded_at','desc')
            ->paginate(25);

        return view('uploaded_leave_files.ob_file', compact('header','files','companies','company_filter','date_from','date_to'));
    }
}
