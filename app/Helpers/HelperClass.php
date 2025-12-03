<?php
namespace App\Helpers;

use App\DailySchedule;
use App\Employee;
use App\EmployeeLeave;

class HelperClass {
    public static function employeeSchedule($schedules = array(), $dailySchedule=array(), $check_date, $schedule_id, $empNum=""){
        if (count($dailySchedule) > 0){
            foreach($dailySchedule as $item){
                if ($item['log_date'] == $check_date && $item['employee_code'] == $empNum) {
                    return $item;
                }
            }
        }
        
        $schedule_name = date('l',strtotime($check_date));
        if(count($schedules) > 0){
            foreach($schedules as $item){
                if (isset($item['schedule_id'], $item['name'])) {
                    if($item['schedule_id'] == $schedule_id && $item['name'] == $schedule_name){
                        return $item;
                    }
                }
            }
        }
    }

    public static function employeeHasOBDetails($employee_obs = array(), $check_date){
        $final_data = "";
        if(count($employee_obs) > 0){
            $collect = collect($employee_obs);
            foreach($collect->sortBy('date_from') as $item){
                // dd($item);
                if(date('Y-m-d',strtotime($item->applied_date)) == date('Y-m-d',strtotime($check_date))){
                    $final_data = $item;
                    break;
                }
            }
            foreach($collect->sortByDesc('date_to') as $item){
                if(date('Y-m-d',strtotime($item->applied_date)) == date('Y-m-d',strtotime($check_date))){
                    $final_data->date_to = $item->date_to;
                    break;
                }
            }
        }
        return $final_data;
    }

    public static function employeeHasLeave($employee_leaves = array(), $check_date,$schedule = array()){
        $halfday=1;
        if(count($employee_leaves) > 0 && $schedule){
            foreach($employee_leaves as $item){
                if($check_date <= $item['date_to']){
                    if($check_date >= $item['date_from'])
                    {
                        // if(date('Y-m-d',strtotime($check_date)) == date('Y-m-d',strtotime($item['date_from']))){
                            $status = 'Without-Pay';
                            if($item['withpay'] == 1){
                                $status = 'With-Pay';
                            }
                            if($item['halfday'] == '1'){
                                $halfday=.5;
                                return $item['leave']['code'] . '-' . $halfday . '-' . $status;
                            }else{
                                return $item['leave']['code'] . '-' . $halfday . '-' . $status;
                            }
                        // }
                    }
                }else{
                    $date_range = dateRangeHelperLeave($item['date_from'],$item['date_to']);
                    if(count($date_range) > 0){
                        foreach($date_range as $date_r){
                            if(date('Y-m-d',strtotime($date_r)) == date('Y-m-d',strtotime($check_date))){
                                $status = 'Without-Pay';
                                if($item['withpay'] == 1){
                                    $status = 'With-Pay';
                                }
                                if($item['halfday'] == '1'){
                                    $halfday=.5;
                                    
                                    return $item['leave']['code'] . '-' . $halfday . ' ' . $status;
                                }else{
                                    return $item['leave']['code'] . '-' . $halfday . ' ' .$status;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public static function usedSlVlThisYear($user_id, $leave_type, $date_hired,$scheduleDatas,$dailySchedule=array())
    {
        $count = 0;
        $all_days = [];
        $workingDays = [];
        if ($date_hired) {
            if(count($scheduleDatas) > 0)
            {
                $workingDays = $scheduleDatas->pluck('name')->toArray();
            }
            
            // Fetch the employee_number from the Employee model
            $employee = Employee::where('user_id', $user_id)->first();
            if (empty($employee)) {
                return $count; // If no employee found, return the count as 0
            }
            
            $employee_number = $employee->employee_number;
            
            $employee_leave = EmployeeLeave::where('user_id', $user_id)
                ->where('leave_type', $leave_type)
                ->where(function ($query) {
                    $query->where('status', 'Approved')
                        ->orWhere('status', 'Pending');
                })
                ->where('withpay',1)
                // ->whereYear('date_from', date('Y'))
                ->where(function($q) {
                    $q->whereYear('date_from', date('Y'))->orWhereYear('date_from', date('Y', strtotime('+1 year')));
                })
                ->where('status','!=','Cancelled')
                ->whereYear('created_at', date('Y'))
                ->whereNull('is_previous_year')
                ->get();
            // // dd($employee_leave);
            if (count($employee_leave) > 0) 
            {
                foreach ($employee_leave as $leave) 
                {
                    if ($leave->withpay == 1 && $leave->halfday == 1) 
                    {
                        if (date('Y-m-d', strtotime($leave->date_from))) 
                        {
                            $count += 0.5;
                        }
                    } else {
                        // Fetch daily schedules where log_date is within the leave date range
                        // $dailySchedules = DailySchedule::select('log_date')->where('employee_number', $employee_number)
                        //     ->whereBetween('log_date', [$leave->date_from, $leave->date_to])
                        //     ->get()
                        //     ->pluck('log_date')
                        //     ->toArray();
                        // dd($dailySchedules);
                        // // // Iterate through each date in the date range
                        $date_range = dateRangeHelperLeaveCount($leave->date_from, $leave->date_to);
                        // // dd($date_range);
                        if (count($date_range) > 0) {
                            foreach ($date_range as $date_r) {
                                $leave_Date = date('Y-m-d', strtotime($date_r));
                                // // Check if withpay is 1 and leave_Date is valid
                                if ($leave->withpay == 1) {
                                    // Check if log_date exists in dailySchedules
                                    // $d = $dailySchedules->where('log_date',$leave_Date)->first();
                                    // if($d)
                                    // {
                                    //     foreach ($dailySchedules as $schedule) {
                                    //         $log_date = $schedule->log_date ? Carbon::parse($schedule->log_date)->format('Y-m-d') : null;
                                            
                                    //         if ($log_date === $leave_Date) {
                                    //             if (is_null($schedule->working_hours)) {
        
                                    //             } else {
                                    //                 $count++; 
                                    //                 $all_days[]=$leave_Date;
                                    //             }
                                    //         }
                                    //     }
                                    // }
                                    // else
                                    // {
                                    // }
                                    $dayName = date('l',strtotime($leave_Date)); // Get the day name (e.g., Monday, Tuesday)
                                    // dd($dayName);
                                    if (in_array($dayName, $workingDays)) {
                                        $count++;
                                        $all_days[]=$leave_Date;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $count;
    }

    public static function checkHasAttendanceHolidayStatus($attendances=array(),$check_date){
        $status =  '';
        if(count($attendances) > 0 && $check_date){
            foreach($attendances as $item){            
                if(date('Y-m-d',strtotime($item['time_in'])) == date('Y-m-d',strtotime($check_date))){
                return $item['time_in'];
                }
            }
        }
        return $status;
    }
}