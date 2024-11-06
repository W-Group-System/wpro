<?php

namespace App\Console\Commands;

use App\Attendance;
use App\Employee;
use App\ScheduleData;
use App\AttendanceDetailedReport;
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
        $email_attendance = new EmailAttendance; 
        $this->info('START');

        $user_list = User::where('status', 'Active')  // Filter Users where their status is Active
        ->whereHas('employee', function ($query) {
            $query->where('status', 'Active');  // Ensure the related Employee's status is Active
        })
        ->whereNotNull('email')  // Ensure email is not null
        ->where('email', '!=', '')  // Ensure email is not an empty string
        ->get();
        $schedules = ScheduleData::all();
        foreach($user_list as $user_data)
        {
            $attendances_cut_off = AttendanceDetailedReport::where('employee_no',$user_data->employee->employee_code)->orderBy('log_date','desc')->first();
            $date_ranges = dateRange(date('Y-m-d', strtotime($attendances_cut_off->log_date . ' +1 day')),date('Y-m-d'));

            $from_date = date('Y-m-d', strtotime($attendances_cut_off->log_date . ' +1 day'));
            $to_date = date('Y-m-d');
            // dd($to_date);
          
            $emp_data = Employee::select('id','user_id','employee_number','first_name','last_name','schedule_id','employee_code')
            ->with(['schedule_info','approved_obs','attendances' => function ($query) use ($from_date, $to_date) {
                    $query->whereBetween('time_in', [$from_date." 00:00:01", $to_date." 23:59:59"])
                            ->orWhereBetween('time_out', [$from_date." 00:00:01", $to_date." 23:59:59"])
                            ->orderBy('time_in','asc')
                            ->orderby('time_out','desc')
                            ->orderBy('id','asc');
            }])
            ->where('employee_number', $user_data->employee->employee_number)
            ->where('status','Active')
            ->first();
            if($emp_data)
            {
                
            $table = "<table border='1' cellspacing='0' width='100%' style='text-align:center; margin-bottom:10px;'>
            <tr>
                <th>Log Date</th>
                <th>Schedule</th>
                <th>Time In</th>
                <th>Time Out</th>
            </tr>";
            foreach($date_ranges as $date)
            {
                $if_has_ob = employeeHasOBDetails($emp_data->approved_obs,date('Y-m-d',strtotime($date)));
                
                $employee_schedule = employeeSchedule($schedules,$date,$emp_data->schedule_id, $emp_data->employee_code);
                $cenvertedTime = date('Y-m-d 00:00:00',strtotime($date));
          if($employee_schedule != null)
          {
              if($employee_schedule->time_in_from != '00:00')
              {
                  $cenvertedTime = date('Y-m-d H:i:s',strtotime('-5 hours',strtotime($date." ".$employee_schedule->time_in_from)));
                  // dd($cenvertedTime);
              }
          }
         
        
          $time_in = ($emp_data->attendances)->whereBetween('time_in',[$cenvertedTime,$date." 23:59:59"])->sortBy('time_in')->first();
        
          $time_out = null;
          $final_time_in = "";
          $final_time_out = "";
          if($time_in == null)
          {
          
              $time_out = ($emp_data->attendances)->whereBetween('time_out',[$date." 00:00:00", $date." 23:59:59"])->where('time_in',null)->first();
              if($time_out)
              {
                  $final_time_out = $time_out->time_out;
              }
          }
          else {
              $final_time_in =   $time_in->time_in;
              $final_time_out =   $time_in->time_out;
          }
          $time_start = "";
      $time_end = "";

      if($final_time_in)
      {
          $time_start = date('Y-m-d h:i A',strtotime($final_time_in));
      }

      if($final_time_out)
      {
          $time_end = date('Y-m-d  h:i A',strtotime($final_time_out));
      }
      if($if_has_ob)
      {
      
      if($final_time_in != null)
      {
          if($if_has_ob->date_from < $final_time_in)
          {
              $time_start = date('Y-m-d h:i A',strtotime($if_has_ob->date_from));
          }
          else {
              $time_start = date('Y-m-d h:i A',strtotime($final_time_in));
          }
      }
      else {
          
          $time_start = date('Y-m-d h:i A',strtotime($if_has_ob->date_from));
      }
      
          if($final_time_out != null){
              // dd($time_in);
              if(strtotime($if_has_ob->date_to) > strtotime($final_time_out))
              {
              
              $time_end = date('Y-m-d h:i A',strtotime($if_has_ob->date_to));
              }
              else {
                  
                  $time_end = date('Y-m-d h:i A',strtotime($final_time_out));
              }
          }
          else {
              
              $time_end = date('Y-m-d h:i A',strtotime($if_has_ob->date_to));
          }
      }
      if($time_start)
      {
         $time_start = date('h:i A',strtotime($time_start));
      }
      if($time_end)
      {
         $time_end = date('h:i A',strtotime($time_end));
      }
                $sched = $employee_schedule && $employee_schedule->time_in_to != '00:00' ? date('h:i A', strtotime($employee_schedule->time_in_to)) . '-' . date('h:i A', strtotime($employee_schedule->time_out_to)) : 'RESTDAY';
                $table.= "<tr>";
                $table.= "<td>".date('M d, Y',strtotime($date))."</td>";
                $table.= "<td>".$sched."</td>";
                $table.= "<td>".$time_start."</td>";
                $table.= "<td>".$time_end."</td>";
                $table.= "</tr>";
            }
            $table.= "</table>";

            $user_data->notify(new AttendanceNotif($table));
        }
        
    }
        
        $this->info('END');
    }

    public function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
        $dates = [];
        $current = strtotime( $first );
        $last = strtotime( $last );
    
        while( $current <= $last ) {
    
            $dates[] = date( $format, $current );
            $current = strtotime( $step, $current );
        }
    
        return $dates;
    }
    public function get_attendances($from_date,$to_date,$id)
    {
        $attendances = Attendance::where('employee_code',$id)
        ->orderBy('time_in','asc')
        // ->orderBy('id','asc')
        ->where(function($q) use ($from_date, $to_date) {
            $q->whereBetween('time_in', [$from_date." 00:00:01", $to_date." 23:59:59"])
            ->orWhereBetween('time_out', [$from_date." 00:00:01", $to_date." 23:59:59"]);
        })
        ->get();
        // dd($attendances);
        return $attendances;
    }
}
