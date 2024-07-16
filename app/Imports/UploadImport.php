<?php

namespace App\Imports;

use App\Approver;
use App\AttendanceLog;
use App\Employee;
use App\EmployeeApprover;
use App\EmployeeLeave;
use App\EmployeeOb;
use App\EmployeeOvertime;
use App\UploadType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use RealRashid\SweetAlert\Facades\Alert;

class UploadImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    // public function collection(Collection $collection)
    // {
    //   foreach($collection)
    // }
    protected $type;
    public function __construct($type)
    {
        $this->type = $type;
    }

    public function collection(Collection $collection)
    {
        $rows = $collection->toArray();

        if (!array_filter($rows)) {
            return null;
        }

        foreach ($rows as $row) {
            
            if(isset($row['employee_no'])) {
                $user_id = Employee::where('employee_code', $row['employee_no'])->pluck('user_id')->toArray();
                $dateFiled = Date::excelToDateTimeObject($row['date_filed'])->format('Y-m-d');
            }
            
            if ($this->type == "OB") {
                $employeeOb = EmployeeOb::whereIn('user_id', $user_id)
                    ->where('applied_date', $dateFiled)
                    ->first();

                foreach ($user_id as $uid) {
                    if (empty($employeeOb)) {
                        $employeeOb = new EmployeeOb;
                        $employeeOb->user_id = $uid;
                        $employeeOb->applied_date = Date::excelToDateTimeObject($row['date_filed'])->format('Y-m-d');
                        $start_time = $this->compute_hours($row['start_time']);
                        $end_time = $this->compute_hours($row['end_time']);

                        $start_date = ($row['start_date'] - 25569) * 86400;
                        $end_date = ($row['end_date'] - 25569) * 86400;
                        $date_approved = ($row['date_approved'] - 25569) * 86400;
                        $employeeOb->date_from =date("Y-m-d H:i:s",strtotime(date('Y-m-d',$start_date)." ".$start_time));
                        $employeeOb->date_to = date("Y-m-d H:i:s",strtotime(date('Y-m-d',$end_date)." ".$end_time));
                        $employeeOb->approved_date = date('Y-m-d',$date_approved);
                        $employeeOb->status = $row['status'];
                        $employeeOb->created_by = auth()->user()->id;
                        $employeeOb->save();
                    } else {
                        $start_time = $this->compute_hours($row['start_time']);
                        $end_time = $this->compute_hours($row['end_time']);

                        $start_date = ($row['start_date'] - 25569) * 86400;
                        $end_date = ($row['end_date'] - 25569) * 86400;
                        $date_approved = ($row['date_approved'] - 25569) * 86400;
                        $employeeOb->date_from =date("Y-m-d H:i:s",strtotime(date('Y-m-d',$start_date)." ".$start_time));
                        $employeeOb->date_to = date("Y-m-d H:i:s",strtotime(date('Y-m-d',$end_date)." ".$end_time));
                        $employeeOb->approved_date = date('Y-m-d',$date_approved);
                        $employeeOb->status = $row['status'];
                        $employeeOb->created_by = auth()->user()->id;
                        $employeeOb->save();
                    }
                }
            } else if ($this->type == "OT") {
                $employeeOt = EmployeeOvertime::whereIn('user_id', $user_id)->where('ot_date', $dateFiled)->first();
                
                foreach ($user_id as $uid) {
                    if (empty($employeeOt)) {
                        $employeeOt = new EmployeeOvertime;
                        $employeeOt->user_id = $uid;
                        $employeeOt->ot_date = $dateFiled;
                        $employeeOt->start_time = date('Y-m-d h:i:s', strtotime($row['start_date_time']));
                        $employeeOt->end_time = date('Y-m-d h:i:s', strtotime($row['end_date_time']));
                        $employeeOt->approved_date = Date::excelToDateTimeObject($row['date_approved'])->format('Y-m-d');
                        $employeeOt->status = $row['status'];
                        $employeeOt->ot_approved_hrs = $row['ot_approved_hrs'];
                        $employeeOt->break_hrs = $row['break_hrs'];
                        $employeeOt->created_by = auth()->user()->id;
                        $employeeOt->save();
                    } else {
                        $employeeOt->start_time = date('Y-m-d h:i:s', strtotime($row['start_date_time']));
                        $employeeOt->end_time =date('Y-m-d h:i:s', strtotime($row['end_date_time']));
                        $employeeOt->approved_date = Date::excelToDateTimeObject($row['date_approved'])->format('Y-m-d');
                        $employeeOt->status = $row['status'];
                        $employeeOt->ot_approved_hrs = $row['ot_approved_hrs'];
                        $employeeOt->break_hrs = $row['break_hrs'];
                        $employeeOt->created_by = $uid;
                        $employeeOt->save();
                    }
                }
            } else if ($this->type == "VL/SL") {
                $types = 0;
                if ($row['leave_name'] == "Vacation Leave" || $row['leave_name'] == "VL") {
                    $types = 1;
                }
                if ($row['leave_name'] == "Sick Leave" || $row['leave_name'] == 'SL') {
                    $types = 2;
                }

                $startDate = Date::excelToDateTimeObject($row['start_date'])->format('Y-m-d');
                $endDate = Date::excelToDateTimeObject($row['end_date'])->format('Y-m-d');

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
                    $leaves->date_from = Date::excelToDateTimeObject($row['start_date'])->format('Y-m-d');
                    $leaves->date_to = Date::excelToDateTimeObject($row['end_date'])->format('Y-m-d');
                    $leaves->approved_date = Date::excelToDateTimeObject($row['date_approved'])->format('Y-m-d');
                    $leaves->status = $row['status'];
                    $leaves->created_by = $uid;
                    $leaves->save();
                }
            }
            else if ($this->type == "DTR") {
                $dateTime = Date::excelToDateTimeObject($row['date_time'])->format('Y-m-d H:i:s');
                
                $attendanceLogs = AttendanceLog::where('emp_code', $row['bio_number'])->where('datetime', $dateTime)->first();
                
                if (empty($attendanceLogs)) {
                    $attendanceLogs = new AttendanceLog;
                    $attendanceLogs->emp_code = $row['bio_number'];
                    $attendanceLogs->date = Date::excelToDateTimeObject($row['date_time'])->format('Y-m-d');
                    $attendanceLogs->datetime = $dateTime;
                    $attendanceLogs->type = strtoupper($row['status']) == 'IN' ? 1 : 0;
                    $attendanceLogs->location = "DTR";
                    $attendanceLogs->ip_address = '192.168.14.80';
                    $attendanceLogs->save();
                }
                else {
                    $attendanceLogs->emp_code = $row['bio_number'];
                    $attendanceLogs->date = Date::excelToDateTimeObject( $row['date_time'])->format('Y-m-d');
                    $attendanceLogs->datetime = $dateTime;
                    $attendanceLogs->type = strtoupper($row['status']) == 'IN' ? 1 : 0;
                    $attendanceLogs->save();
                }
            }
        }

        Alert::success('Successfully Uploaded')->persistent('Dismiss');
    }
    public function compute_hours($variable)
    {
        $total =$variable* 24; //multiply by the 24 hours
        $hours = floor($total); //Gets the natural number part
        $minute_fraction = $total - $hours; //Now has only the decimal part
        $minutes = $minute_fraction * 60; //Get the number of minutes
        $start_time = $hours . ":" . $minutes;
        return $start_time;
    }
    public function compute_dys($variable)
    {
        $start_date = ($variable - 25569) * 86400;
        return $start_date;
    }
}
