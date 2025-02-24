<?php
use App\ApplicantSystemNotification;
use App\UserAllowedCompany;
use App\UserAllowedLocation;
use App\UserAllowedProject;
use App\UserPrivilege;
use App\Employee;
use App\EmployeeLeave;
use App\EmployeeEarnedLeave;
use App\EmployeeLeaveCredit;
use App\Holiday;
use App\Attendance;
use App\DailySchedule;
use App\EmployeeOvertime;
use App\EmployeeWfh;
use App\EmployeeOb;
use App\EmployeeDtr;
use App\ScheduleData;
use App\Tax;
use App\ExitClearanceSignatory;
use App\ExitResign;

use Carbon\Carbon;

function employee_name($employee_names,$employee_number){
    foreach($employee_names as $item){
        if($item['employee_number'] == $employee_number){
            return $item->last_name . ' ' . $item->first_name;
        }
    }
}
function getInitial($text) {
    preg_match_all('#([A-Z]+)#', $text, $capitals);
    if (count($capitals[1]) >= 2) {
        return substr(implode('', $capitals[1]), 0, 1);
    }
    return strtoupper(substr($text, 0, 1));
}

function appFormatDate($date) {
    return date("Y-m-d", strtotime($date));
}

function appFormatFullDate($date) {
    return date("F d, Y h:i A", strtotime($date));
}

function roleValidation(){
    if(session('role_ids'))
    {
        if(in_array(1,session('role_ids')) || in_array(3,session('role_ids')) || in_array(9,session('role_ids'))){ //Administrator,DCO and ADCO
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}
function roleValidationAsAdministrator(){
    if(session('role_ids'))
    {
        if(in_array(1,session('role_ids'))){ //Administrator,DCO and ADCO
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}


function get_count_days_leave($data,$date_from,$date_to)
 {
    $data = ($data->pluck('name'))->toArray();
    $count = 0;
    $startTime = strtotime($date_from);
    $endTime = strtotime($date_to);

    for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
      $thisDate = date( 'l', $i ); // 2010-05-01, 2010-05-02, etc
      if(in_array($thisDate,$data)){
          $count= $count+1;
      }
    }
    return($count);
 } 
 
function dateRangeHelper( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
    $dates = [];
    $current = strtotime( $first );
    $last = strtotime( $last );

    while( $current <= $last ) {
        $curr = date('D',$current);
      
            $dates[] = date( $format, $current);
            $current = strtotime( $step, $current );
        
    }

    return $dates;
}

function dateRangeHelperLeaveCount( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
    $dates = [];
    $current = strtotime( $first );
    $last = strtotime( $last );

    while( $current <= $last ) {
        $curr = date('D',$current);
      
        $dates[] = date( $format, $current);
        $current = strtotime( $step, $current );
        
    }

    return $dates;
}
function dateRangeHelperLeave( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
    $dates = [];
    $current = strtotime( $first );
    $last = strtotime( $last );

    while( $current <= $last ) {
        $curr = date('D',$current);
        if ($curr == 'Sun') {
            $current = strtotime( $step, $current);
        }else{
            $dates[] = date( $format, $current);
            $current = strtotime( $step, $current );
        }
    }

    return $dates;
}

function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
    $dates = [];
    $current = strtotime( $first );
    $last = strtotime( $last );

    while( $current <= $last ) {

        $dates[] = date( $format, $current );
        $current = strtotime( $step, $current );
    }

    return $dates;
}


function employeeSchedule($schedules = array(), $check_date, $schedule_id, $empNum=""){
    $dailySchedule = DailySchedule::where('employee_code', $empNum)
        ->where('log_date', $check_date)
        ->orderBy('id', 'DESC')
        ->first();

    if (!empty($dailySchedule)) {
        if($dailySchedule['log_date'] == $check_date && $dailySchedule['employee_code'] == $empNum){
        
        return $dailySchedule;
        }
    }
    
    $schedule_name = date('l',strtotime($check_date));
    if(count($schedules) > 0){
        foreach($schedules as $item){
            if($item['schedule_id'] == $schedule_id && $item['name'] == $schedule_name){
                return $item;
            }
        }
    }
}

function isRestDay( $date ) {
    
    $check_day = date('D',strtotime($date));
    $check = 0;
    if ($check_day == 'Sat' || $check_day == 'Sun') {
        $check = 1;
    }else{
        $check = 0;
    }
    return $check;
}

function employeeHasLeave($employee_leaves = array(), $check_date,$schedule = array()){
    
    $halfday=1;
    if($check_date == "2024-07-21")

    {
    }
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

function employeeHasOB($employee_obs = array(), $check_date){
    if(count($employee_obs) > 0){
        foreach($employee_obs as $item){
            if(date('Y-m-d',strtotime($item['applied_date'])) == date('Y-m-d',strtotime($check_date))){
                return 'OB';
            }
        }
    }
}

function employeeHasOBDetails($employee_obs = array(), $check_date){
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

function employeeHasWFH($employee_wfhs = array(), $check_date){
    if(count($employee_wfhs) > 0){
        foreach($employee_wfhs as $item){
            if(date('Y-m-d',strtotime($item['applied_date'])) == date('Y-m-d',strtotime($check_date))){
                return 'WFH';
            }
        }
    }
}

function employeeHasWFHDetails($employee_wfhs = array(), $check_date){
    if(count($employee_wfhs) > 0){
        foreach($employee_wfhs as $item){
            if(date('Y-m-d',strtotime($item['applied_date'])) == date('Y-m-d',strtotime($check_date))){
                return $item;
            }
        }
    }
}

function employeeHasOTDetails($employee_ots = array(), $check_date){
    if(count($employee_ots) > 0){
        $total_approved_overtime = 0;
        foreach($employee_ots as $item){
            if(date('Y-m-d',strtotime($item['ot_date'])) == date('Y-m-d',strtotime($check_date))){

                $total =(float) $item['ot_approved_hrs'] - (float)$item['break_hrs'];

                $total_approved_overtime += $total;
            }
        }
        return $total_approved_overtime;
    }
}

function employeeHasDTRDetails($employee_dtrs = array(), $check_date){
    if($employee_dtrs){
        foreach($employee_dtrs as $item){
            if(date('Y-m-d',strtotime($item['dtr_date'])) == date('Y-m-d',strtotime($check_date))){
                return $item;
            }
        }
    }
}

function getUserAllowedCompanies($user_id){
    $user_allowed_companies = UserAllowedCompany::where('user_id',$user_id)->first();

    if($user_allowed_companies){
        return json_decode($user_allowed_companies->company_ids);
    }else{
        return [];
    }
}
function getUserAllowedLocations($user_id){
    $user_allowed_locations = UserAllowedLocation::where('user_id',$user_id)->first();

    if($user_allowed_locations){
        return json_decode($user_allowed_locations->location_ids);
    }else{
        return [];
    }
}
function getUserAllowedProjects($user_id){
    $user_allowed_projects = UserAllowedProject::where('user_id',$user_id)->first();

    if($user_allowed_projects){
        return json_decode($user_allowed_projects->project_ids);
    }else{
        return [];
    }
}

function checkUserPrivilege($field,$user_id){
    $user_privilege = UserPrivilege::select('id')->where($field,'on')->where('user_id',$user_id)->first();
    if($user_privilege){
        return 'yes';
    }else{
        return 'no';
    }
}

function checkUserAllowedOvertime($user_id){
    $employee = Employee::select('level')->where('user_id',$user_id)->first();
    if($employee->level == 'RANK&FILE' || $employee->level == '1'){
        return 'yes';
    }else{
        return 'no';
    }
}
function night_difference_per_company($start_work, $end_work)
{
    // Convert timestamps to Unix timestamps if they are not already
    if (!is_numeric($start_work)) {
        $start_work = strtotime($start_work);
    }
    if (!is_numeric($end_work)) {
        $end_work = strtotime($end_work);
    }

    // Define night shift boundaries
    $night_start = mktime(22, 0, 0, date('m', $start_work), date('d', $start_work), date('Y', $start_work));
    $night_end = mktime(6, 0, 0, date('m', $start_work), date('d', $start_work) + 1, date('Y', $start_work));

    // Ensure $end_work is compared with the correct night boundaries
    if ($start_work >= $night_start && $start_work < $night_end) {
        if ($end_work >= $night_end) {
            return ($night_end - $start_work) / 3600;
        } else {
            return ($end_work - $start_work) / 3600;
        }
    } elseif ($end_work >= $night_start && $end_work < $night_end) {
        if ($start_work < $night_start) {
            return ($end_work - $night_start) / 3600;
        } else {
            return ($end_work - $start_work) / 3600;
        }
    } elseif ($start_work < $night_start && $end_work >= $night_end) {
        return ($night_end - $night_start) / 3600;
    }

    return 0; // Default case when no night shift overlap
}

// function get_count_days($dailySchedules, $scheduleDatas, $date_from, $date_to, $halfday)
// {
//     $date_from = Carbon::parse($date_from);
//     $date_to = Carbon::parse($date_to);

//     // Initialize count
//     $count = 0;
    
//     if ($date_from->equalTo($date_to)) {
//         // Single-day period
//         $count = 1;
//         foreach ($dailySchedules as $schedule) {
//             $log_date = $schedule->log_date ? Carbon::parse($schedule->log_date) : null;

//             if ($log_date && $log_date->between($date_from, $date_to)) {
//                 if (is_null($schedule->working_hours)) {
//                     // If working_hours is null, set count to 0 and break out of the loop
//                     return 0;
//                 } else {
//                     $count++;
//                 }
//             }
//         }
//     } else {
//         // Multiple-day period
//         foreach ($dailySchedules as $schedule) {
//             $log_date = Carbon::parse($schedule->log_date); // Parse log_date to Carbon instance

//             if ($log_date->between($date_from, $date_to)) {
//                 if (is_null($schedule->working_hours)) {
//                     // If working_hours is null, set count to 0 and break out of the loop
//                     $count;
//                 } else {
//                     $count++;
//                 }
//             }
//         }

//         // If no entries found with non-empty time_in_from, count based on scheduleDatas
//         if ($count === 0) {
//             $data = $scheduleDatas->pluck('name')->toArray();
//             $startTime = strtotime($date_from);
//             $endTime = strtotime($date_to);
            
//             for ($i = $startTime; $i <= $endTime; $i += 86400) {
//                 $thisDate = Carbon::createFromTimestamp($i)->format('l'); // Get the day name
//                 if (in_array($thisDate, $data)) {
//                     $count++;
//                 }
//             }
//         }
//     }

//     // Adjust count for half-day if applicable
//     if ($count == 1 && $halfday == 1) {
//         return 0.5;
//     } else {
//         return $count;
//     }
// }

function get_count_days($dailySchedules, $scheduleDatas, $date_from, $date_to, $halfday,$withpay = 0)
{
    if($withpay == 0)
{
    return 0;
}
else
{
    $date_from = date('Y-m-d', strtotime($date_from));
    $date_to = date('Y-m-d', strtotime($date_to));
    
    // Initialize count
    $count = 0;
    
    // Generate list of day names from scheduleDatas
    $workingDays = $scheduleDatas->pluck('name')->toArray();
    // Create DateTime objects from string dates
    $dateFromObj = new DateTime($date_from);
    $dateToObj = new DateTime($date_to);
    
    // Loop over each day in the date range
    while ($dateFromObj <= $dateToObj) {
        $dailySchedule = $dailySchedules->firstWhere('log_date', $dateFromObj->format('Y-m-d'));
        
        if ($dailySchedule) {
            // If a daily schedule exists, check if working_hours is set
            if (!is_null($dailySchedule->working_hours)) {
                $count++;
            }
        } else {
            // If no daily schedule, check weekly schedule (scheduleDatas)
            $dayName = $dateFromObj->format('l'); // Get the day name (e.g., Monday, Tuesday)
            if (in_array($dayName, $workingDays)) {
                $count++;
            }
        }
    
        // Increment the date by one day
        $dateFromObj->modify('+1 day');
    }
    
    // Adjust count for half-day if applicable
    if ($count == 1 && $halfday == 1) {
        return 0.5;
    } else {
        return $count;
    }
}
   
}


function checkUsedSLVLSILLeave($user_id, $leave_type, $date_hired,$scheduleDatas = [])
{
    
    $count = 0;
    $all_days = [];
    $workingDays = [];
    if ($date_hired) {
        if($scheduleDatas != [])
        {
            $workingDays = $scheduleDatas->pluck('name')->toArray();
        }
        // dd($workingDays);
        $today = date('Y-m-d');
        $date_hired_md = date('m-d', strtotime($date_hired));
        $last_year = date('Y', strtotime('-1 year', strtotime($today)));
        $this_year = date('Y');

        $date_hired_this_year = $this_year . '-' . $date_hired_md;

        if ($today > $date_hired_this_year) {
            $filter_date_leave = $this_year . '-' . $date_hired_md;
        } else {
            $filter_date_leave = $last_year . '-' . $date_hired_md;
        }

        // Fetch the employee_number from the Employee model
        $employee = Employee::where('user_id', $user_id)->first();
        if (!$employee) {
            return $count; // If no employee found, return the count as 0
        }
        $employee_number = $employee->employee_number;

        $employee_vl = EmployeeLeave::where('user_id', $user_id)
            ->where('leave_type', $leave_type)
            ->where(function ($query) {
                $query->where('status', 'Approved')
                      ->orWhere('status', 'Pending');
            })
            ->where('withpay',1)
            ->where('status','!=','Cancelled')
            // ->where('date_from', '>', $filter_date_leave)
            ->get();
            // dd($employee_vl);
        if ($employee_vl) {
            foreach ($employee_vl as $leave) {
                if ($leave->withpay == 1 && $leave->halfday == 1) {
                    if (date('Y-m-d', strtotime($leave->date_from))) {
                        $count += 0.5;
                    }
                } else {
                    // Fetch daily schedules where log_date is within the leave date range
                    $dailySchedules = DailySchedule::where('employee_number', $employee_number)
                        ->whereBetween('log_date', [$leave->date_from, $leave->date_to])
                        ->get();
                    
                    // Iterate through each date in the date range
                    $date_range = dateRangeHelperLeaveCount($leave->date_from, $leave->date_to);
                    
                    if ($date_range) {
                        
                        foreach ($date_range as $date_r) {
                            $leave_Date = date('Y-m-d', strtotime($date_r));
                            // Check if withpay is 1 and leave_Date is valid
                            if ($leave->withpay == 1) {
                                // Check if log_date exists in dailySchedules
                                $d = $dailySchedules->where('log_date',$leave_Date)->first();
                            
                                if($d != null)
                                {
                                    foreach ($dailySchedules as $schedule) {
                                        $log_date = $schedule->log_date ? Carbon::parse($schedule->log_date)->format('Y-m-d') : null;
                                        
                                        if ($log_date === $leave_Date) {
                                            if (is_null($schedule->working_hours)) {
    
                                            } else {
                                                $count++; 
                                                $all_days[]=$leave_Date;
                                            }
                                        }
                                    }
                                }
                                else
                                {
                              
                                    $dayName = date('l',strtotime($leave_Date)); // Get the day name (e.g., Monday, Tuesday)
                              
                                    if (in_array($dayName, $workingDays)) {
                                        // dd($dayName);
                                        
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
    }
    return $count;
}


// function checkUsedSLVLSILLeave($user_id,$leave_type,$date_hired){

//     $count = 0;
//     if($date_hired){
//         $today  = date('Y-m-d');
//         $date_hired_md = date('m-d',strtotime($date_hired));
//         $date_hired_m = date('m',strtotime($date_hired));
//         $last_year = date('Y', strtotime('-1 year', strtotime($today)) );
//         $this_year = date('Y');

//         $date_hired_this_year = $this_year . '-'. $date_hired_md;

//         if($today > $date_hired_this_year  ){
//             $filter_date_leave = $this_year . '-'. $date_hired_md;
//         }else{
//             $filter_date_leave = $last_year . '-'. $date_hired_md;
//         }
        
//         $employee_vl = EmployeeLeave::where('user_id',$user_id)
//                                         ->where('leave_type',$leave_type)
//                                         ->where('status','Approved')
//                                         // ->where('date_from','>',$filter_date_leave)
//                                         ->get();
        
//         $date_today = date('Y-m-d');
//         if($employee_vl){
//             foreach($employee_vl as $leave){
//                 if($leave->withpay == 1 && $leave->halfday == 1){
//                     if(date('Y-m-d',strtotime($leave->date_from))){
//                         $count += 0.5;
//                     }
//                 }else{
//                     $date_range = dateRange($leave->date_from,$leave->date_to);
//                     if($date_range){
//                         foreach($date_range as $date_r){
//                             $leave_Date = date('Y-m-d', strtotime($date_r));
//                             if($leave->withpay == 1 && $leave_Date){
//                                 $count += 1;
//                             }
//                         }
//                     }
//                 }
                
//             }
//         }
//     }
//     return $count;
// }

function checkEarnedLeave($user_id,$leave_type,$date_hired){

    //Get From Last Year Earned
    // if($date_hired){
    //     $today  = date('Y-m-d');
    //     $date_hired_md = date('m-d',strtotime($date_hired));
    //     $date_hired_m = date('m',strtotime($date_hired));
    //     $last_year = date('Y', strtotime('-1 year', strtotime($today)) );
    //     $this_year = date('Y');

    //     $date_hired_this_year = $this_year . '-'. $date_hired_md;
    //     $date_hired_last_year = $last_year . '-'. $date_hired_md;

    //     if($today >= $date_hired_this_year){ //if Date hired meets todays date get earned leaves from last year to this year date_hired
    //         $date_hired_this_minus_1_month = date('Y-m-d', strtotime('-1 month', strtotime($date_hired_this_year)) );
            return $vl_earned = EmployeeEarnedLeave::where('user_id',$user_id)
                                                        ->where('leave_type',$leave_type)
                                                        ->whereNull('converted_to_cash')
                                                        // ->whereBetween('earned_date', [$date_hired_last_year, $date_hired_this_minus_1_month])
                                                        ->sum('earned_leave');
        // }else{
        //     return 0;
        // }
    // }

    
    
}

function checkUsedSickLeave($user_id){
    $employee_sl = EmployeeLeave::where('user_id',$user_id)
                                    ->where('leave_type','2')
                                    ->where('status','Approved')
                                    ->get();

    $count = 0;
    if($employee_sl){
        foreach($employee_sl as $leave){
            if($leave->withpay == 1 && $leave->halfday == 1){
                $count += 0.5;
            }else{
                $date_range = dateRangeHelper($leave->date_from,$leave->date_to);
                if($date_range){
                    foreach($date_range as $date_r){
                        if($leave->withpay == 1){
                            $count += 1;
                        }
                    }
                }
            }
        }
    }

    return $count;
}

function checkUsedServiceIncentiveLeave($user_id){
    $employee_sil = EmployeeLeave::where('user_id',$user_id)
                                    ->where('leave_type','10')
                                    ->where('status','Approved')
                                    ->get();

    $count = 0;
    if($employee_sil){
        foreach($employee_sil as $leave){
            if($leave->withpay == 1 && $leave->halfday == 1){
                $count += 0.5;
            }else{
                $date_range = dateRangeHelper($leave->date_from,$leave->date_to);
                if($date_range){
                    foreach($date_range as $date_r){
                        if($leave->withpay == 1){
                            $count += 1;
                        }
                    }
                }
            }
        }
    }

    return $count;
}

function checkUsedLeave($user_id,$leave_type){
    $employee_leave = EmployeeLeave::where('user_id',$user_id)
                                    ->where('leave_type',$leave_type)
                                    ->where('status','Approved')
                                    ->get();

    $count = 0;
    if($employee_leave){
        foreach($employee_leave as $leave){
            if($leave->withpay == 1 && $leave->halfday == 1){
                $count += 0.5;
            }else{
                $date_range = dateRangeHelper($leave->date_from,$leave->date_to);
                if($date_range){
                    foreach($date_range as $date_r){
                        if($leave->withpay == 1){
                            $count += 1;
                        }
                    }
                }
            }
        }
    }

    return $count;
}

function checkIfHoliday($date,$location){
    $check_holiday = Holiday::where('holiday_date',$date)->first();
    if($check_holiday){
        if($check_holiday->location){
            if($check_holiday->location == $location){
                return $check_holiday->holiday_type;
            }else{
                return "";
            }
        }else{
            return $check_holiday->holiday_type;
        }
    }else{
        return "";
    }
}

function checkHasAttendanceHoliday($date,$employee_code,$location){

    $date_attendance = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date) ) ));
    $check_if_holiday = checkIfHoliday($date_attendance,$location);
    $check_if_restday = isRestDay($date_attendance);

    if($check_if_holiday){ //Holiday
        $date_attendance_1 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance) ) ));
        $check_if_holiday_1 = checkIfHoliday($date_attendance_1,$location);
        $check_if_restday_1 = isRestDay($date_attendance_1);

        if($check_if_holiday_1){ //Holiday
            
            $date_attendance_2 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_1) ) ));
            $check_if_holiday_2 = checkIfHoliday($date_attendance_2,$location);
            $check_if_restday_2 = isRestDay($date_attendance_2);

            if($check_if_holiday_2){ //Holiday

                $date_attendance_3 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_2) ) ));
                $check_if_holiday_3 = checkIfHoliday($date_attendance_3,$location);
                $check_if_restday_3 = isRestDay($date_attendance_3);

                if($check_if_holiday_3){ //Holiday

                }else{ //Regular Work
                    if($check_if_restday_3 == 0){ //Rest day no
                        return $date_attendance_3;
                    }
                }
            }else{ //Regular Work
                if($check_if_restday_2 == 0){ //Rest day no
                    return $date_attendance_2;
                }
            }

        }else{ //Regular Work
            if($check_if_restday_1 == 0){ //Rest day no
                return $date_attendance_1;
            }else{
                $date_attendance_2 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_1) ) ));
                $check_if_holiday_2 = checkIfHoliday($date_attendance_2,$location);
                $check_if_restday_2 = isRestDay($date_attendance_2);

                if($check_if_holiday_2){ //Holiday
                    
                    $date_attendance_3 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_2) ) ));
                    $check_if_holiday_3 = checkIfHoliday($date_attendance_3,$location);
                    $check_if_restday_3 = isRestDay($date_attendance_3);

                    if($check_if_holiday_3){ //Holiday

                        $date_attendance_4 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_3) ) ));
                        $check_if_holiday_4 = checkIfHoliday($date_attendance_4,$location);
                        $check_if_restday_4 = isRestDay($date_attendance_4);
                        
                        if($check_if_holiday_4){ //Holiday

                        }else{ //Regular Work
                            if($check_if_restday_4 == 0){ //Rest day no
                                return $date_attendance_4;
                            }
                        }
                    }else{ //Regular Work
                        if($check_if_restday_3 == 0){ //Rest day no
                            return $date_attendance_3;
                        }
                    }
                }else{ //Regular Work
                    if($check_if_restday_2 == 0){ //Rest day no
                        return $date_attendance_2;
                    }
                }
            }
        }
    }else{ //Regular Work
        if($check_if_restday == 0){
            return $date_attendance;
        }else{ // Regular days
            $date_attendance_1 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance) ) ));
            $check_if_holiday_1 = checkIfHoliday($date_attendance_1,$location);
            $check_if_restday_1 = isRestDay($date_attendance_1);

            if($check_if_holiday_1){ //Holiday

                $date_attendance_2 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_1) ) ));
                $check_if_holiday_2 = checkIfHoliday($date_attendance_2,$location);
                $check_if_restday_2 = isRestDay($date_attendance_2);

                if($check_if_holiday_2){ //Holiday
                    $date_attendance_3 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_2) ) ));
                    $check_if_holiday_3 = checkIfHoliday($date_attendance_3,$location);
                    $check_if_restday_3 = isRestDay($date_attendance_3);

                    if($check_if_holiday_3){ //Holiday

                    }else{ //Regular Work
                        if($check_if_restday_3 == 0){ //Rest day no
                            return $date_attendance_3;
                        }
                    }
                }else{ //Regular Work
                    if($check_if_restday_2 == 0){ //Rest day no
                        return $date_attendance_2;
                    }
                }
            }else{ //Regular Work
                if($check_if_restday_1 == 0){ //Rest day no
                    return $date_attendance_1;
                }else{

                    $date_attendance_2 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_1) ) ));
                    $check_if_holiday_2 = checkIfHoliday($date_attendance_2,$location);
                    $check_if_restday_2 = isRestDay($date_attendance_2);

                    if($check_if_holiday_2){ //Holiday

                        $date_attendance_3 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_2) ) ));
                        $check_if_holiday_3 = checkIfHoliday($date_attendance_3,$location);
                        $check_if_restday_3 = isRestDay($date_attendance_3);

                        if($check_if_holiday_3){ //Holiday
                            $date_attendance_4 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_3) ) ));
                            $check_if_holiday_4 = checkIfHoliday($date_attendance_4,$location);
                            $check_if_restday_4 = isRestDay($date_attendance_4);

                            if($check_if_holiday_4){ //Holiday

                            }else{ //Regular Work
                                if($check_if_restday_4 == 0){ //Rest day no
                                    return $date_attendance_4;
                                }
                            }
                        }else{ //Regular Work
                            if($check_if_restday_3 == 0){ //Rest day no
                                return $date_attendance_3;
                            }
                        }
                    }else{
                        if($check_if_restday_2 == 0){ //Rest day no
                            return $date_attendance_2;
                        }
                    }
                }
            }
        }
    }
}

function checkHasAttendanceHolidayStatus($attendances=array(),$check_date){
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

function checkEmployeeLeaveCredits($user_id, $leave_type){
    $employee_leave = EmployeeLeaveCredit::where('user_id',$user_id)
                                    ->where('leave_type',$leave_type)
                                    ->first();
    if($employee_leave){
        return $employee_leave->count;
    }else{
        return 0;
    }
}
function compute_tax($employee_salary,$level) {
    if($level == 4)
    {
        $taxes = Tax::where('level',4)->get();
    }
    else

    {
        $taxes = Tax::where('level',null)->get();
    }
   

    foreach ($taxes as $tax) 
    {
        // if ($employee_salary >= $tax->from_gross && ($tax->to_gross == 0 || $employee_salary <= $tax->to_gross)) {
        //     $excess_income = $employee_salary - $tax->excess_over;
        //     $computed_tax = $tax->tax_plus + ($excess_income * ($tax->percentage / 100));
        //     return $computed_tax;
        // }
        if ($employee_salary >= $tax->from_gross && ($tax->to_gross == 0 || $employee_salary <= $tax->to_gross)) {
            $excess_income = $employee_salary - $tax->excess_over;
            $computed_tax = $tax->tax_plus + ($excess_income * ($tax->percentage / 100));
            return $computed_tax;
        }
    }

    return 0; 
}

function getEmployeeHierarchy($userId)
{
    // Get the employee
    $employee = Employee::where('user_id', $userId)->firstOrFail();
    $to_top = Employee::with(['allSupervisors' => function($query) {
        $query->with('immediateSupervisor');
    }])->where('user_id', $userId)->first();

    $to_bottom = Employee::where('status', 'Active')
    ->where('immediate_sup', $userId)
    ->with(['subordinates' => function($query) {
        $query->where('status', 'Active')->with('subordinates');
    }])
    ->get();
    // Get the immediate supervisor
    
    $datas = [];
    if ($to_bottom) {
        processSubordinates($to_bottom, $datas);
    }
    if ($to_top) {
        addEmployeeToDatas($to_top, $to_top->immediate_sup, $datas);
    
        if ($to_top->immediateSupervisor) {
            processApprovers($to_top->immediateSupervisor, $to_top->immediateSupervisor->immediate_sup, $datas);
        } elseif ($to_top->immediateSupervisor && $to_top->status === "Active") {
            $datas[] = (object)[
                'id' => $to_top->immediate_sup,
                'pid' => null,
                'name' => $to_top->first_name . ' ' . $to_top->last_name,
                'position' => $to_top->position,
                'img' => $to_top->avatar ? asset($to_top->avatar) : null,
            ];
        }
    } else {
        // Fallback case for self
        $datas[] = (object)[
            'id' => $employee->id,
            'pid' => null,
            'name' => $employee->first_name . ' ' . $employee->last_name,
            'position' => $employee->position,
            'img' => $employee->avatar ? asset($employee->avatar) : null,
        ];
    }
    return $datas;
}

function processSubordinates($subordinates, &$datas)
{
    foreach ($subordinates as $under) {
        if ($under->status === "Active") {
            $datas[] = (object)[
                "id" => $under->user_id,
                'pid' => $under->immediate_sup,
                'name' => $under->first_name . " " . $under->last_name,
                'position' => $under->position,
                'img' => $under->avatar ? asset($under->avatar) : null,
            ];
        }

        // Recursively process subordinates
        if ($under->subordinates) {
            processSubordinates($under->subordinates, $datas);
        }
       
    }
}

function addEmployeeToDatas($employee, $pid, &$datas)
{
    if ($employee->status === "Active") {
        $datas[] = (object)[
            'id' => $employee->user_id,
            'pid' => $pid,
            'name' => $employee->first_name . ' ' . $employee->last_name,
            'position' => $employee->position,
            'img' => $employee->avatar ? asset($employee->avatar) : null,
        ];
    }
}
$approvers_ids = [];
function processApprovers($approvers, $pid, &$datas)
{
    if ($approvers) {
        addEmployeeToDatas($approvers, $pid, $datas);

        if ($approvers->immediate_sup) {  
            processApprovers($approvers->immediateSupervisor, $approvers->immediateSupervisor->immediate_sup, $datas);
        } elseif ($approvers->immediateSupervisor && $approvers->status === "Active") {
            $datas[] = (object)[
                'id' => $approvers->user_id,
                'pid' => null,
                'name' => $approvers->first_name . ' ' . $approvers->last_name,
                'position' => $approvers->position,
                'img' => $approvers->avatar ? asset($approvers->avatar) : null,
            ];
        }
    }
}

function documentTypes() {
  $documentTypes = array(
    '1' => 'ID',
    '2' => 'Diploma',
    '3' => 'Transcript of Records',
    '4' => 'Original Clearance (NBI / Police / Barangay)',
    '5' => 'SSS',
    '6' => 'PAGIBIG',
    '7' => 'PHILHEALTH',
    '8' => 'Birth Certificate',
    '9' => 'Training Certificate',
    '10' => 'PRC License',
    '11' => 'Passport',
    '12' => 'Marriage Certificate',
    '13' => "Child's Birth Certificate",
    '14' => 'Certificate of Employment',
    '15' => 'BIR 2316',
    '16' => 'Medical Examination'
  );

  return $documentTypes;
}

function benefits() {
  $benefits = array(
    'SL' => 'Salary Loan',
    'EA' => 'Educational Assistance',
    'WG' => 'Wedding Gifts',
    'BA' => 'Bereavement Assistance',
    'HMO' => 'Health Card (HMO)'
  );

  return $benefits;
}

function pending_leave_count($approver_id){

    $today = date('Y-m-d');
    $from_date = date('Y-m-d',(strtotime ( '-1 month' , strtotime ( $today) ) ));
    $to_date = date('Y-m-d');

    return EmployeeLeave::select('user_id')->with('approver.approver_info')
                                ->whereHas('approver',function($q) use($approver_id) {
                                    $q->where('approver_id',$approver_id);
                                })
                                ->where('status','Pending')
                                // ->whereDate('created_at','>=',$from_date)
                                // ->whereDate('created_at','<=',$to_date)
                                ->count();
}

function pending_overtime_count($approver_id){
    
    $today = date('Y-m-d');
    $from_date = date('Y-m-d',(strtotime ( '-1 month' , strtotime ( $today) ) ));
    $to_date = date('Y-m-d');

    return EmployeeOvertime::select('user_id')->whereHas('approver',function($q) use($approver_id) {
                                    $q->where('approver_id',$approver_id);
                                })
                                ->where('status','Pending')
                                // ->whereDate('created_at','>=',$from_date)
                                // ->whereDate('created_at','<=',$to_date)
                                ->count();
}

function pending_ob_count($approver_id){
    
    $today = date('Y-m-d');
    $from_date = date('Y-m-d',(strtotime ( '-1 month' , strtotime ( $today) ) ));
    $to_date = date('Y-m-d');

    return EmployeeOb::select('user_id')->whereHas('approver',function($q) use($approver_id) {
                                    $q->where('approver_id',$approver_id);
                                })
                                ->where('status','Pending')
                                // ->whereDate('created_at','>=',$from_date)
                                // ->whereDate('created_at','<=',$to_date)
                                ->count();
}




//clearance-functions
function for_clearance()
{
    $for_clearances = ExitClearanceSignatory::with('clearance')
    ->where('employee_id',auth()->user()->employee->id)
    ->where('status','Pending')
->count();

    return $for_clearances;
}
function for_setup()
{
    $exit = ExitResign::whereDoesntHave('exit_clearance')->count();

    return $exit;
}
function ongoing_clearance()
{
    $exit = ExitResign::where('status','Ongoing Clearance')->count();

    return $exit;
}
function cleared()
{
    $exit = ExitResign::where('status','Cleared')->count();

    return $exit;
}
function ongoing_computation()
{
    $exit = ExitResign::where('status','Ongoing Computation')->count();

    return $exit;
}
function for_release()
{
    $exit = ExitResign::where('status','For Release')->count();

    return $exit;
}

function get_avatar($id)
{
    $avatar = Employee::findOrfail($id);
    $image = "https://hris.wsystem.online/".$avatar->avatar;

    return $image;
}

function usedSlVlThisYear($user_id, $leave_type, $date_hired,$scheduleDatas = [])
{
    $count = 0;
    $all_days = [];
    $workingDays = [];
    if ($date_hired) {
        if($scheduleDatas != [])
        {
            $workingDays = $scheduleDatas->pluck('name')->toArray();
        }

        // Fetch the employee_number from the Employee model
        $employee = Employee::where('user_id', $user_id)->first();
        if (!$employee) {
            return $count; // If no employee found, return the count as 0
        }
        $employee_number = $employee->employee_number;

        $employee_sl = EmployeeLeave::where('user_id', $user_id)
            ->where('leave_type', $leave_type)
            ->where(function ($query) {
                $query->where('status', 'Approved')
                      ->orWhere('status', 'Pending');
            })
            ->where('withpay',1)
            ->whereYear('created_at', date('Y'))
            ->where('status','!=','Cancelled')
            // ->where('date_from', '>', $filter_date_leave)
            ->get();
            
        if ($employee_sl) {
            foreach ($employee_sl as $leave) {
                if ($leave->withpay == 1 && $leave->halfday == 1) {
                    if (date('Y-m-d', strtotime($leave->date_from))) {
                        $count += 0.5;
                    }
                } else {
                    // Fetch daily schedules where log_date is within the leave date range
                    $dailySchedules = DailySchedule::where('employee_number', $employee_number)
                        ->whereBetween('log_date', [$leave->date_from, $leave->date_to])
                        ->get();
                    
                    // // // Iterate through each date in the date range
                    $date_range = dateRangeHelperLeaveCount($leave->date_from, $leave->date_to);
                    
                    if ($date_range) {
                        
                        foreach ($date_range as $date_r) {
                            $leave_Date = date('Y-m-d', strtotime($date_r));
                            // Check if withpay is 1 and leave_Date is valid
                            if ($leave->withpay == 1) {
                                // Check if log_date exists in dailySchedules
                                $d = $dailySchedules->where('log_date',$leave_Date)->first();
                            
                                if($d != null)
                                {
                                    foreach ($dailySchedules as $schedule) {
                                        $log_date = $schedule->log_date ? Carbon::parse($schedule->log_date)->format('Y-m-d') : null;
                                        
                                        if ($log_date === $leave_Date) {
                                            if (is_null($schedule->working_hours)) {
    
                                            } else {
                                                $count++; 
                                                $all_days[]=$leave_Date;
                                            }
                                        }
                                    }
                                }
                                else
                                {
                              
                                    $dayName = date('l',strtotime($leave_Date)); // Get the day name (e.g., Monday, Tuesday)
                                    
                                    if (in_array($dayName, $workingDays)) {
                                        // dd($dayName);
                                        
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
    }
    
    return $count;
}