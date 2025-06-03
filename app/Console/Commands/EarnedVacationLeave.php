<?php

namespace App\Console\Commands;

use App\Employee;
use App\EmployeeEarnedLeave;
use App\EmployeeLeaveList;
use DateTime;
use Illuminate\Console\Command;

class EarnedVacationLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:auto_earned_leaves';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        info('Start Auto Earned');
        // $employees = Employee::where('status','Active')->whereHas('employee_leave_credits')->get();
      
        // $f_d = date('Y-m-01');
        // $f_t = date('Y-m-t');
        // // dd($f_t);

        // $datetime1_d = new DateTime($f_d);
        // $datetime2_d = new DateTime($f_t);
        // $interval_d = $datetime1_d->diff($datetime2_d);
        // $days_d = $interval_d->format('%a')+1;
        // $year = date('Y');
        // $month = date('m');
        // $day = "01";
        // foreach($employees as $employee)
        // {
        //     $leave_credits = ($employee->employee_earned_credits)->where('leave_type',1)->sortByDesc('id')->first();
        //     if($leave_credits != null)
        //     {
              
        //         $check_if_exist_vl = EmployeeEarnedLeave::where('user_id',$employee->user_id)
        //             ->where(function($q) use($month,$year){
        //             $q->whereMonth('earned_date',$month)
        //             ->whereYear('earned_date',$year);
        //         })
        //             ->where('leave_type',1)
        //             ->first();                
               
        //         if(empty($check_if_exist_vl)){
        //             $earned_leave = new EmployeeEarnedLeave;
        //             $earned_leave->leave_type = 1; // Vacation Leave
        //             $earned_leave->user_id = $employee->user_id;
        //             $earned_leave->earned_day = $day;
        //             $earned_leave->earned_month = $month;
        //             $earned_leave->earned_year = $year;
        //             $earned_leave->earned_date = date('Y-m-d');
        //             $earned_leave->earned_leave = $leave_credits->earned_leave;
        //             $earned_leave->save();
        //         }
        //     }
        // }

        $employees = Employee::with('employee_leave_list')
            ->where('status','Active')
            ->whereHas('employee_leave_list')
            // ->where('user_id', 470)
            ->get();
        // dd($employees);
        $f_d = date('Y-m-01');
        $f_t = date('Y-m-t');
        // dd($f_t);

        // $datetime1_d = new DateTime($f_d);
        // $datetime2_d = new DateTime($f_t);
        // $interval_d = $datetime1_d->diff($datetime2_d);
        // $days_d = $interval_d->format('%a')+1;
        $year = date('Y');
        $month = date('m');
        $day = "01";
        foreach($employees as $employee)
        {
            $leave_credits = ($employee->employee_leave_list)->where('leave_id',1)->sortByDesc('id')->first();
            // dd($leave_credits);
            if($leave_credits != null)
            {
                $check_if_exist_vl = EmployeeLeaveList::where('user_id', $employee->user_id)
                    ->where(function($q) use($month,$year){
                        $q->whereMonth('earned_date',$month)
                        ->whereYear('earned_date',$year);
                    })
                    ->whereNotNull('earned_date')
                    ->where('leave_id',1)
                    ->first();                
                
                if(empty($check_if_exist_vl)){
                    $earned_leave = new EmployeeLeaveList;
                    $earned_leave->leave_id = 1; // Vacation Leave
                    $earned_leave->user_id = $employee->user_id;
                    // $earned_leave->earned_day = $day;
                    $earned_leave->month = $month;
                    $earned_leave->year = $year;
                    $earned_leave->earned_date = date('Y-m-d');
                    $earned_leave->earned_per_month = $leave_credits->earned_per_month;
                    $earned_leave->save();
                }
            }
        }

        info('End Auto Earned');
    }
}
