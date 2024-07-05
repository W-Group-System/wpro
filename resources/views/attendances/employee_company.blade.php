@extends('layouts.header')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class='row'>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Attendances</h4>
                        <p class="card-description">
                            <form method='get' onsubmit='show();' enctype="multipart/form-data">
                                <div class=row>
                                    <div class='col-md-4'>
                                        <div class="form-group">
                                            <select data-placeholder="Select Company" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='company' required>
                                                <option value="">-- Select Employee --</option>
                                                @foreach($companies as $comp)
                                                <option value="{{$comp->id}}" @if ($comp->id == $company) selected @endif>{{$comp->company_name}} - {{$comp->company_code}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    {{-- <div class='col-md-2'>
                                        <div class="form-group">
                                            <select data-placeholder="Select Department" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='department'>
                                                <option value="">-- Select Department --</option>
                                                @foreach($departments as $dep)
                                                <option value="{{$dep->id}}" @if ($dep->id == $department) selected @endif>{{$dep->name}} - {{$dep->code}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class='col-md-2'>
                                        <div class="form-group">
                                            <select data-placeholder="Select Location" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='location'>
                                                <option value="">-- Select Location --</option>
                                                @foreach($locations as $loc)
                                                <option value="{{$loc->location}}" @if ($loc->location == $location) selected @endif>{{$loc->location}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class='col-md-2'>
                                        <div class="form-group">
                                            <input type="date" value='{{$from_date}}' class="form-control" name="from" max='{{date('d/m/Y')}}' onchange='get_min(this.value);' required />
                                        </div>
                                    </div>
                                    <div class='col-md-2'>
                                        <div class="form-group">
                                            <input type="date" value='{{$to_date}}' class="form-control" name="to" id='to' max='{{date('d/m/Y')}}' required />
                                        </div>
                                    </div>
                                    <div class='col-md-2'>
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="/biometrics-per-company" class="btn btn-warning">Reset Filter</a>
                                    </div>
                                </div>
                            </form>
                        </p>
                        @if($date_range)
                        <a href="attendance-per-company-export?company={{$company}}&from={{$from_date}}&to={{$to_date}}" class='btn btn-info mb-1'>Export {{count($emp_data)}} Employees</a>
                        @endif
                        
                        <div class="table-responsive">

                            <table border="1" class="table table-hover table-bordered employee_attendance" id='employee_attendance'>

                                <thead>
                                    {{-- <tr>
                                        <td colspan='5'>{{$emp->emp_code}} - {{$emp->first_name}} {{$emp->last_name}}</td>
                                    </tr> --}}
                                    <tr>
                                        <th>Company</th>
                                        <th>Employee #</th>
                                        <th>Name</th>
                                        <th>Log Date</th>
                                        <th>Shift</th>
                                        <th>IN</th>
                                        <th>OUT</th>
                                        <th>ABS</th>
                                        <th>LV W/ PAY</th>
                                        <th>REG HRS</th>
                                        <th>LATE (min)</th>
                                        <th>Undertime (min)</th>
                                        <th>REG OT</th>
                                        <th>REG ND</th>
                                        <th>REG OT ND</th>
                                        <th>RST OT</th>
                                        <th>RST OT > 8</th>
                                        <th>RST ND</th>
                                        <th>RST ND > 8</th>
                                        <th>LH OT</th>
                                        <th>LH OT > 8</th>
                                        <th>LH ND</th>
                                        <th>LH ND > 8</th>
                                        <th>SH OT</th>
                                        <th>SH OT > 8</th>
                                        <th>SH ND</th>
                                        <th>SH ND > 8</th>
                                        <th>RST LH OT</th>
                                        <th>RST LH OT > 8</th>
                                        <th>RST LH ND</th>
                                        <th>RST LH ND > 8</th>
                                        <th>RST SH OT</th>
                                        <th>RST SH OT > 8</th>
                                        <th>RST SH ND</th>
                                        <th>RST SH ND > 8</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>

                                @foreach($emp_data as $emp)
                                @php
                                $work =0;
                                $lates =0;
                                $undertimes =0;
                                $overtimes =0;
                                $approved_overtimes =0;
                                $night_diffs =0;
                                $night_diff_ot =0;
                                @endphp
                                
                                <tbody>

                                    @foreach($date_range as $date_r)
                                    @php
                                        $employee_schedule = employeeSchedule($schedules,$date_r,$emp->schedule_id, $emp->employee_number);
                                        $rest = "";
                                        $if_leave = "";
                                    @endphp
                                    <tr>
                                        <td>{{$emp->company->company_code}}</td>
                                        <td>{{$emp->employee_code}}</td>
                                        <td>{{$emp->first_name . ' ' . $emp->last_name}}</td>
                                        <td >{{date('d/m/Y',strtotime($date_r))}}</td>
                                        <td> 
                                          @if($employee_schedule != null)
                                            @if($employee_schedule->time_in_from != '00:00')
                                              <small>{{date('h:i A', strtotime($employee_schedule->time_in_to)).'-'.date('h:i A', strtotime($employee_schedule->time_out_to))}}</small>
                                              @if ($employee_schedule->time_in_from != $employee_schedule->time_in_to)
                                                <small>(Flexi)</small>
                                              @endif
                                            @else 
                                            <small>RESTDAY</small>
                                            @php
                                                $rest = "RESTDAY";
                                            @endphp
                                            @endif
                                          @else
                                          <small>RESTDAY</small>
                                            @php
                                                $rest = "RESTDAY";
                                            @endphp

                                          @endif
                                          {{-- @if($employee_schedule)
                                            <small>{{$emp->schedule_info->schedule_name}}</small>
                                          @endif --}}
                                        </td>
                                        @php
                                            $if_has_ob = employeeHasOBDetails($emp->approved_obs,date('Y-m-d',strtotime($date_r)));
                                        @endphp
                                        @php
                                            $time_in = ($emp->attendances)->whereBetween('time_in',[$date_r." 00:00:00", $date_r." 23:59:59"])->first();
                                            $time_out = null;
                                            $final_time_in = "";
                                            $final_time_out = "";
                                            if($time_in == null)
                                            {
                                              
                                                $time_out = ($emp->attendances)->whereBetween('time_out',[$date_r." 00:00:00", $date_r." 23:59:59"])->where('time_in',null)->first();
                                                if($time_out)
                                                {
                                                    $final_time_out = $time_out->time_out;
                                                }
                                            }
                                            else {
                                                $final_time_in =   $time_in->time_in;
                                                $final_time_out =   $time_in->time_out;
                                            }
                                        @endphp
                                        @php
                                        $time_start = "";
                                        $time_end = "";

                                        if($final_time_in)
                                        {
                                            $time_start = date('h:i A',strtotime($final_time_in));
                                        }

                                        if($final_time_out)
                                        {
                                            $time_end = date('h:i A',strtotime($final_time_out));
                                        }
                                        if($if_has_ob)
                                        {
                                        if($final_time_in != null)
                                        {
                                            if($if_has_ob->date_from < $final_time_in)
                                            {
                                                $time_start = date('h:i A',strtotime($if_has_ob->date_from));
                                            }
                                            else {
                                                $time_start = date('h:i A',strtotime($final_time_in));
                                            }
                                        }
                                        
                                        if($final_time_in != null){
                                                // dd($time_in);
                                            if($time_in->time_out != null)
                                            {
                                                if($if_has_ob->date_to > $final_time_out)
                                                {
                                                   $time_end = date('h:i A',strtotime($if_has_ob->date_to));
                                                }
                                                else {
                                                    
                                                    $time_end = date('h:i A',strtotime($final_time_out));
                                                }
                                                
                                            }
                                        }
                                        }
                                        @endphp

                                        @php
                                            $abs = 1;
                                        @endphp

                                        @if(($time_start) || ($time_end))
                                            @php
                                                $abs = 0;
                                            @endphp
                                        @endif
                                        @if($abs = 1)
                                            @if($employee_schedule)
                                                @php 
                                                    $is_absent = '';
                                                    $if_leave = '';
                                                    $if_attendance_holiday = '';
                                                    $if_restday = '';
                                                    $check_if_holiday = checkIfHoliday(date('Y-m-d',strtotime($date_r)),$emp->location);
                                                    $if_attendance_holiday_status = '';
                                                    if($check_if_holiday){
                                                        $if_attendance_holiday = checkHasAttendanceHoliday(date('Y-m-d',strtotime($date_r)), $emp->employee_number,$emp->location);
                                                        if($if_attendance_holiday){

                                                            $check_leave = employeeHasLeave($emp->approved_leaves,date('Y-m-d',strtotime($if_attendance_holiday)),$employee_schedule);
                                                        
                                                            if($check_leave){
                                                                $if_attendance_holiday_status = 'With-Pay';
                                                                $abs =0;
                                                                if($check_leave){
                                                                    if($check_leave == 'SL Without-Pay' || $check_leave == 'VL Without-Pay'){
                                                                        $if_attendance_holiday_status = 'Without-Pay';
                                                                    }else{
                                                                        $if_attendance_holiday_status = 'With-Pay';
                                                                    }
                                                                }
                                                            }
                                                            else{
                                                                $check_attendance = checkHasAttendanceHolidayStatus($emp->attendances,$if_attendance_holiday);
                                                                if(empty($check_attendance)){
                                                                    $is_absent = 'Absent';
                                                                    $abs =1;
                                                                }else{
                                                                    $if_attendance_holiday_status = 'With-Pay';
                                                                    $abs =0;
                                                                }
                                                            }
                                                        }
                                                    }else{
                                                        $if_leave = employeeHasLeave($emp->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);
                                                        
                                                        if(empty($if_leave)){
                                                            if($employee_schedule->time_in_from != '00:00') {
                                                            if(empty($if_has_dtr)){
                                                                    if($time_out == null){
                                                                        $is_absent = 'Absent';
                                                                    }
                                                            }
                                                            }
                                                            else {
                                                                $abs = 0;
                                                                $if_restday = 'Restday';
                                                            }
                                                        } 
                                                    }
                                                        
                                                @endphp
                                            @endif
                                        @else
                                            @php
                                                $is_absent = '';
                                                $if_restday = '';
                                                
                                                $if_leave = employeeHasLeave($emp->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);
                                               
                                                // $abs=0;
                                            @endphp  
                                        @endif
                                        <td>{{$time_start}}</td>
                                        <td>{{$time_end}}</td>
                                
                                        @php
                                            $leave_count = 0;
                                            if($if_leave)
                                            {
                                                $l = explode('-',$if_leave);
                                                $leave_count = $l[1];
                                            }
                                        @endphp
                                     
                                        @php
                                            $work =0;
                                            $undertime_hrs = 0;
                                            $undertime = 0;
                                        @endphp
                                        @if((($time_start)&&($time_end)) && $employee_schedule)    
                                            @php
                                                $work =  round((((strtotime($time_end) - strtotime($time_start)))/3600),2);
                                                $schedule_hours = 0;
                                            
                                                $sched = $employee_schedule->working_hours;
                                                if($employee_schedule->time_in_from)
                                                {
                                                    if($employee_schedule->working_hours > 8)
                                                    {
                                                        $schedule_hours =  $employee_schedule->working_hours-1;
                                                        $sched = $employee_schedule->working_hours+1;
                                                    }
                                                    else {
                                                        $schedule_hours = $employee_schedule->working_hours;
                                                    }

                                                    if($work > $schedule_hours)
                                                    {
                                                        $work = $schedule_hours;
                                                    }
                                                    if($schedule_hours > $work)
                                                    {
                                                        $undertime = (double) number_format($schedule_hours - $work,2);
                                                    }
                                                    if($leave_count == .5)
                                                    {
                                                        if($work > $schedule_hours)
                                                        {

                                                            $work = $schedule_hours/2;
                                                        }
                                                        else {
                                                            $work = $work;

                                                        }
                                                    }
                                                }

                                            @endphp                                            
                                        @endif
                                        @php
                                        $late_diff_hours=0;
                                        if($time_start && $time_end && $employee_schedule)
                                        {
                                            $time_in_data_full =  date('Y-m-d H:i:s',strtotime($time_start));
                                            $time_in_data_date =  date('Y-m-d',strtotime($time_start));
                                            $schedule_time_in =  $time_in_data_date . ' ' . $employee_schedule['time_in_to'];
                                            $schedule_time_out =  $time_in_data_date . ' ' . $employee_schedule['time_out_to'];
                                            $schedule_time_in =  date('Y-m-d H:i:s',strtotime($schedule_time_in));
                                            $schedule_time_in_final =  new DateTime($schedule_time_in);
                                            if(date('Y-m-d H:i',strtotime($time_in_data_full)) > date('Y-m-d H:i',strtotime($schedule_time_in))){
                                                $late_diff = $schedule_time_in_final->diff(new DateTime($time_in_data_full));
                                                $late_diff_hours = round($late_diff->s / 3600 + $late_diff->i / 60 + $late_diff->h + $late_diff->days * 24, 2);
                                            }   
                                            
                                            if($undertime > 0){
                                                        if($late_diff_hours > 0){
                                                            $undertime_hrs = $undertime - $late_diff_hours;
                                                        }else{
                                                            $undertime_hrs = $undertime;
                                                        }
                                                    }  
                                        }
                                        @endphp
                                        @if($work > 0)
                                            @php
                                                $abs = 0;
                                            @endphp
                                        @endif
                                        @if(($leave_count != 0) && ($abs == 0))
                                            @php
                                                $abs = $leave_count;
                                            @endphp
                                        @endif
                                        @if($rest)
                                            @php
                                                $abs =0;
                                                $leave_count =0;
                                            @endphp
                                        @endif
                                        @php
                                            $late = $late_diff_hours*60;
                                            if($leave_count == .5)
                                            {
                                                if($work < ($schedule_hours/2))
                                                {
                                                    $late = ($schedule_hours/2)-$work;
                                                    if($work < $schedule_hours/2)
                                                    {
                                                        $late = 0;
                                                        $undertime_hrs = (double) number_format(($schedule_hours/2 - $work),2);
                                                    }
                                                

                                                   
                                                }
                                                else{
                                                    $work = ($schedule_hours/2);
                                                    $late = 0;
                                                    $undertime_hrs = 0;
                                                }
                                            }
                                        @endphp
                                        <td>{{$abs}}</td>
                                        <td>{{$leave_count}}</td>
                                        <td>{{$work}}</td>
                                        <td>@if($late<0)0.00 @else {{$late}}@endif</td>
                                        <td>@if($undertime_hrs<0)0.00 @else {{$undertime_hrs*60}}@endif</td>
                                        <td>0.00</td> {{--reg ot--}}
                                        <td>0.00</td> {{--reg nd--}}
                                        <td>0.00</td> {{--rst ot--}}
                                        <td>0.00</td> {{--RST OT > 8--}}
                                        <td>0.00</td> {{--RST ND--}}
                                        <td>0.00</td> {{--RST ND > 8--}}
                                        <td>0.00</td> {{--RST ND > 8--}}
                                        <td>0.00</td> {{--LH OT--}}
                                        <td>0.00</td> {{--LH OT > 8--}}
                                        <td>0.00</td> {{--LH ND	--}}
                                        <td>0.00</td> {{--LH ND > 8--}}
                                        <td>0.00</td> {{--SH OT	--}}
                                        <td>0.00</td> {{--	SH OT > 8	--}}
                                        <td>0.00</td> {{--SH ND	--}}
                                        <td>0.00</td> {{--SH ND > 8	--}}
                                        <td>0.00</td> {{--RST LH OT	--}}
                                        <td>0.00</td> {{--RST LH OT > 8--}}
                                        <td>0.00</td> {{--RST LH ND--}}
                                        <td>0.00</td> {{--RST LH ND > 8--}}
                                        <td>0.00</td> {{--RST SH OT--}}
                                        <td>0.00</td> {{--RST SH OT > 8--}}
                                        <td>0.00</td> {{--RST SH ND--}}
                                        <td>0.00</td> {{--RST SH ND > 8	--}}
                                        {{-- <td>{{$if_leave}} --}}
                                        <td>
                                            {{$if_leave}}
                                        @if($if_has_ob)
                                        OB
                                        @endif
                                    </td> {{--Remarks--}}
                                                   
                                    </tr>
                                    @endforeach
                                </tbody>

                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@php
function night_difference($start_work,$end_work)
{
$start_night = mktime('22','00','00',date('m',$start_work),date('d',$start_work),date('Y',$start_work));
$end_night = mktime('06','00','00',date('m',$start_work),date('d',$start_work) + 1,date('Y',$start_work));

if($start_work >= $start_night && $start_work <= $end_night) { if($end_work>= $end_night)
    {
    return ($end_night - $start_work) / 3600;
    }
    else
    {
    return ($end_work - $start_work) / 3600;
    }
    }
    elseif($end_work >= $start_night && $end_work <= $end_night) { if($start_work <=$start_night) { return ($end_work - $start_night) / 3600; } else { return ($end_work - $start_work) / 3600; } } else { if($start_work < $start_night && $end_work> $end_night)
        {
        return ($end_night - $start_night) / 3600;
        }
        return 0;
        }
        }

        @endphp
        <script>
            function get_min(value) {
                document.getElementById("to").min = value;
            }

        </script>
        @endsection
