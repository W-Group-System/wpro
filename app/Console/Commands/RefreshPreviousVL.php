<?php

namespace App\Console\Commands;

use App\Employee;
use App\EmployeeLeaveList;
use Illuminate\Console\Command;

class RefreshPreviousVL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:refresh_previous_vl';

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
        $employees = Employee::where('status','Active')->get();
        foreach($employees as $employee)
        {
            $employee_leave_list = EmployeeLeaveList::where('year', date('Y', strtotime('-1 year')))->where('user_id', $employee->user_id)->first();
            if($employee_leave_list)
            {
                $employee_leave_list->earned_per_month = 0;
                $employee_leave_list->save();
            }
        }
        
        return "Success";
    }
}
