<?php

namespace App\Imports;

use App\Approver;
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

    public function collection(Collection $collection) {
      $rows = $collection->toArray();
      
      if (!array_filter($rows)) {
        return null;
      }
      
      foreach($rows as $row) {
        $user_id = Employee::where('employee_number', $row['employee_no'])->pluck('user_id')->toArray();
        $dateFiled = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_filed'])->format('Y-m-d');

        if ($this->type == "OB") {
          $employeeOb = EmployeeOb::whereIn('user_id', $user_id)
            ->where('applied_date', $dateFiled)
            ->first();
          
          foreach($user_id as $uid) {
            if(empty($employeeOb)) {
              $employeeOb = new EmployeeOb;
              $employeeOb->user_id = $uid;
              $employeeOb->applied_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_filed'])->format('Y-m-d');
              $employeeOb->date_from = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['start_date'])->format('Y-m-d').':'.date('H:i:s', strtotime($row['start_time']));
              $employeeOb->date_to = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['end_date'])->format('Y-m-d').':'.date('H:i:s', strtotime($row['end_time']));
              $employeeOb->approved_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_approved'])->format('Y-m-d');
              $employeeOb->status = $row['status'];
              $employeeOb->created_by = $uid;
              $employeeOb->save();
            }
            else {
              $employeeOb->date_from = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['start_date'])->format('Y-m-d').':'.date('H:i:s', strtotime($row['start_time']));
              $employeeOb->date_to = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['end_date'])->format('Y-m-d').':'.date('H:i:s', strtotime($row['end_time']));
              $employeeOb->approved_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_approved'])->format('Y-m-d');
              $employeeOb->status = $row['status'];
              $employeeOb->created_by = $uid;
              $employeeOb->save();
            }
          }
        }
        else if ($this->type == "OT") {
          $employeeOt = EmployeeOvertime::whereIn('user_id', $user_id)->where('ot_date', $dateFiled)->first();

          foreach($user_id as $uid) {
            if (empty($employeeOt)) {
              $employeeOt = new EmployeeOvertime;
              $employeeOt->user_id = $uid;
              $employeeOt->ot_date = $dateFiled;
              $employeeOt->start_time = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['start_date_time'])->format('Y-m-d H:i:s');
              $employeeOt->end_time = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['end_date_time'])->format('Y-m-d H:i:s');
              $employeeOt->approved_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_approved'])->format('Y-m-d');
              $employeeOt->status = $row['status'];
              $employeeOt->created_by = $uid;
              $employeeOt->save();
            }
            else {
              $employeeOt->start_time = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['start_date_time'])->format('Y-m-d H:i:s');
              $employeeOt->end_time = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['end_date_time'])->format('Y-m-d H:i:s');
              $employeeOt->approved_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_approved'])->format('Y-m-d');
              $employeeOt->status = $row['status'];
              $employeeOt->created_by = $uid;
              $employeeOt->save();
            }
          }
        }
        else if ($this->type == "VL/SL") {
          $types = 0;
          if ($row['leave_name'] == "Vacation Leave" || $row['leave_name'] == "VL") {
            $types = 1;
          }
          if ($row['leave_name'] == "Sick Leave" || $row['leave_name'] == 'SL') {
            $types = 2;
          }
          
          $startDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['start_date'])->format('Y-m-d');
          $endDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['end_date'])->format('Y-m-d');

          $leaves = EmployeeLeave::whereIn('user_id', $user_id)
            ->where('date_from', $startDate)
            ->where('date_to', $endDate)
            ->first();

          foreach($user_id as $uid) {
            if (!empty($leaves)) {
              $leaves = $leaves->delete();
            }
            
            $leaves = new EmployeeLeave;
            $leaves->user_id = $uid;
            $leaves->leave_type = $types;
            $leaves->date_from = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['start_date'])->format('Y-m-d');
            $leaves->date_to = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['end_date'])->format('Y-m-d');
            $leaves->approved_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_approved'])->format('Y-m-d');
            $leaves->status = $row['status'];
            $leaves->created_by = $uid;
            $leaves->save();
          }
        }
      }

      Alert::success('Successfully Uploaded')->persistent('Dismiss');
    }
}
