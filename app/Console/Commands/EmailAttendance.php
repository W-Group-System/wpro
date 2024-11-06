<?php

namespace App\Console\Commands;

use App\Attendance;
use App\Notifications\AttendanceNotif;
use App\User;
use Illuminate\Console\Command;

class EmailAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:email_attendance';

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
        $this->info('START');

        $user_list = User::where('status', 'Active')->get();
        foreach($user_list as $user_data)
        {
            $attendance_data = Attendance::where('employee_code', $user_data->employee->employee_number)->orderBy('id', 'desc')->first();
            
            $table = "<table border='1' cellspacing='0' width='100%' style='text-align:center; margin-bottom:10px;'><tr><th>Log Date</th><th>In</th><th>Out</th></tr>";
            $table.= "<tr>";
            $table.= "<td>".($attendance_data->time_in != null ? date('M d, Y', strtotime($attendance_data->time_in)) : '')."</td>";
            $table.= "<td>".($attendance_data->time_in != null ? date('g:i A', strtotime($attendance_data->time_in)) : '')."</td>";
            $table.= "<td>".($attendance_data->time_out != null ? date('g:i A', strtotime($attendance_data->time_out)) : '')."</td>";
            $table.= "</tr>";
            $table.= "</table>";

            $user_data->notify(new AttendanceNotif($table));
        }
        
        $this->info('END');
    }
}
