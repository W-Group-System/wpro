<?php
namespace App\Helpers;

use App\DailySchedule;

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

}