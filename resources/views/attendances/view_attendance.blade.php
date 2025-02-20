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
                  <form method='get' onsubmit='show();'  enctype="multipart/form-data">
                  <div class=row>
                    <div class='col-md-3'>
                      <div class="form-group row">
                        <label class="col-sm-4 col-form-label text-right">From</label>
                        <div class="col-sm-8">
                          <input type="date" value='{{$from_date}}' class="form-control" name="from" max='{{date('Y-m-d')}}' onchange='get_min(this.value);' required/>
                        </div>
                      </div>
                    </div>
                    <div class='col-md-3'>
                      <div class="form-group row">
                        <label class="col-sm-4 col-form-label text-right">To</label>
                        <div class="col-sm-8">
                          <input type="date" value='{{$to_date}}'  class="form-control"  id='to' name="to" required/>
                        </div>
                      </div>
                    </div>
                    <div class='col-md-3'>
                      <button type="submit" class="btn btn-primary mb-2">Submit</button>
                    </div>
                  </div>
                  </form>
                </p>
                <div class="table-responsive">
                    <table border="1" class="table table-hover table-bordered employee_attendance" id='employee_attendance'>
                        <thead>
                            {{-- <tr>
                                <td colspan='5'>{{$emp->emp_code}} - {{$emp->first_name}} {{$emp->last_name}}</td>
                            </tr> --}}
                            <tr>
                                <th>Sync</th>
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

                        
                        <tbody>
                            @foreach($emp_data as $emp)
                                @php
                                    $work =0;
                                    $lates =0;
                                    $undertimes =0;
                                    $overtimes =0;
                                    $approved_overtimes =0;
                                    $night_diffs =0;
                                    $night_diff_ot =0;

                                    $subtotal_abs = 0;
                                    $subtotal_leave_w_pay=0;
                                    $subtotal_reg_hrs = 0;
                                    $subtotal_late = 0;
                                    $subtotal_undertime = 0;
                                    $subtotal_overtimes = 0;
                                    $subtotal_nd = 0;
                                    $subtotal_ot_nd = 0;
                                    $subtotal_rd_ot = 0;
                                    $subtotal_rd_ot_ge = 0;
                                    $subtotal_rd_nd = 0;
                                    $subtotal_rd_nd_ge = 0;
                                    
                                    $previous_abs = 0;
                                @endphp

                                @foreach($date_range as $date_r)
                                
                                @php
                                    $final_time_in = "";
                                    $time_in = ($emp->attendances)->whereBetween('time_in',[$date_r." 00:00:00",$date_r." 23:59:59"])->sortBy('time_in')->first();
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
                                    $employee_schedule = employeeSchedule($schedules,$date_r,$emp->schedule_id, $emp->employee_code);
                                    $rest = "";
                                    $if_leave = "";
                                    $if_has_ob = employeeHasOBDetails($emp->approved_obs,date('Y-m-d',strtotime($date_r)));
                                @endphp
                                <tr>
                                    <td>
                                        {{-- @dd($date_r) --}}
                                        @if($employee_schedule)
                                        <form method="POST" action="{{url('sync_attendance')}}" onsubmit="show()">
                                            @csrf 
                                            <input type="hidden" name="date" value="{{$date_r}}">
                                            {{-- <input type="hidden" name="to" value="{{$to_date}}"> --}}
                                            <input type="hidden" name="emp_code" value="{{$emp->employee_number}}">

                                            <button type="submit" class="btn btn-sm btn-info">
                                                <i class="ti-loop"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                    <td>{{ $emp->employee_code }}</td>
                                    <td>{{$emp->first_name . ' ' . $emp->last_name}}</td>
                                    <td>{{date('d/m/Y',strtotime($date_r))}}</td>
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
                                    <!-- <td> 
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
                                    </td> -->
                                    @php
                                        $if_has_ob = employeeHasOBDetails($emp->approved_obs,date('Y-m-d',strtotime($date_r)));
                                    @endphp
                                    @php
                                        $cenvertedTime = date('Y-m-d 00:00:00',strtotime($date_r));
                                        if($employee_schedule != null)
                                        {
                                            if($employee_schedule->time_in_from != '00:00')
                                            {
                                                $cenvertedTime = date('Y-m-d H:i:s',strtotime('-6 hours',strtotime($date_r." ".$employee_schedule->time_in_from)));
                                                // dd($cenvertedTime);
                                            }
                                        }
                                       
                                      
                                        $time_in = ($emp->attendances)->whereBetween('time_in',[$cenvertedTime,$date_r." 23:59:59"])->sortBy('time_in')->first();
                                      
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
                                    @endphp

                                    @php
                                        $abs = 1;
                                    @endphp

                                    @if(($time_start) && ($time_end))
                                        @php
                                            $abs = 0;
                                        @endphp
                                    @endif
                                    @if($abs == 1)
                                        @if($employee_schedule)
                                            @php 
                                                $is_absent = '';
                                                $if_leave = '';
                                                $if_attendance_holiday = '';
                                                $if_restday = '';
                                                $check_if_holiday = checkIfHoliday(date('Y-m-d',strtotime($date_r)),$emp->location);
                                                // dd($check_if_holiday);
                                                $if_attendance_holiday_status = '';
                                                
                                              
                                                if($check_if_holiday){
                                                       
                                                  
                                                        $if_attendance_holiday = checkHasAttendanceHoliday(date('Y-m-d',strtotime($date_r)), $emp->employee_number,$emp->location);
                                                    $if_approved_obs = checkHasAttendanceHoliday(date('Y-m-d',strtotime($date_r)), $emp->employee_number,$emp->location);
                                                   
                                                        $check_leave = employeeHasLeave($emp->approved_leaves,date('Y-m-d',strtotime($date_r."-1 day")),$employee_schedule);
                                                        // dd($if_attendance_holiday);
                                                        if($check_leave){
                                                            $if_attendance_holiday_status = 'With-Pay';
                                                            $abs =0;
                                                            $previous_abs = 0;
                                                            if($check_leave){
                                                                // dd($check_leave);
                                                                if(str_contains($check_leave,"Without")){
                                                                    // dd($check_leave);
                                                                    $if_attendance_holiday_status = 'Without-Pay';
                                                                    $abs = 1;
                                                                    $previous_abs = 1;
                                                                    if(str_contains($check_leave,".5"))
                                                                    {
                                                                        $abs = 0;
                                                                        $previous_abs = 0;
                                                                    }
                                                                }else{
                                                                    $if_attendance_holiday_status = 'With-Pay';
                                                                }
                                                            }
                                                        }
                                                        else{
                                                            $check_attendance = checkHasAttendanceHolidayStatus($emp->attendances,$if_attendance_holiday);
                                                            // dd($emp->attendances);
                                                            // dd(date('Y-m-d H:i',strtotime($date_r." 00:00:00")-86400));
                                                            $time_in = ($emp->attendances)->whereBetween('time_in',[date('Y-m-d H:i',strtotime($date_r." 00:00:00")-86400),date('Y-m-d H:i',strtotime($date_r." 23:59:59")-86400)])->sortBy('time_in')->first();
                                                            $time_in_ob = ($emp->approved_obs)->where('applied_date',date('Y-m-d',strtotime($date_r." 00:00:00")-86400))->sortBy('applied_date')->first();
                                                       
                                                            if(empty($check_attendance)){
                                                                $is_absent = 'Absent';
                                                                $abs =1;
                                                            }else{
                                                                $if_attendance_holiday_status = 'With-Pay';
                                                                $abs =0;
                                                            }
                                                            if($time_in != null)
                                                            {
                                                                if(($time_in->time_out) && ($time_in->time_in))
                                                                {
                                                                    if((strtotime($time_in->time_out) - strtotime($time_in->time_in)/3600) >= 4)
                                                                    {
                                                                        $abs =0;
                                                                    }
                                                                }
                                                               
                                                            }
                                                            if($time_in_ob != null)
                                                            {
                                                                $abs =0;
                                                            }
                                                        }
                                                     
                                         
                                                        $employee_schedule_before = employeeSchedule($schedules,date('Y-m-d',strtotime("-1 day",strtotime($date_r))),$emp->schedule_id, $emp->employee_code);
                                                        $check_if_holiday_before = checkIfHoliday(date('Y-m-d',strtotime("-1 day",strtotime($date_r))),$emp->location);
                                                    
                                                      
                                                    
                                                        if($check_if_holiday_before)
                                                        {
                                                            $abs = 0;
                                                        }
                                                        if($employee_schedule_before == null)
                                                        {
                                                            $abs = 0;
                                                        
                                                        }
                                                        else {
                                                            if($employee_schedule_before->time_in_from == '00:00')
                                                            {
                                                                $abs = 0;
                                                            }
                                                            if($employee_schedule_before->time_in_from == null)
                                                            {
                                                                $abs = 0;
                                                            }

                                                            
                                                            }
                                                            if($if_attendance_holiday)
                                                            {
                                                                $abs=0;
                                                            }
                                                      
                                                        if($emp->work_description == "Non-Monthly")
                                                        {
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
                                                            else {
                                                                $abs = 1;
                                                            }
                                                            if($check_if_holiday == "Special Holiday")
                                                            {
                                                                $abs = 1;
                                                            }

                                                        }
                                                    
                                                        if(date('Y-m-d',strtotime($date_r)) < $emp->original_date_hired)
                                                        {
                                                            $abs = 1;
                                                        }
                                                        if($previous_abs == 1)
                                                        {
                                                            $abs = 1;
                                                        }
                                                        $previous_abs = $abs;
                                                        
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
                                                    if($date_r < $emp->original_date_hired)
                                                    {
                                                        $abs = 1;
                                                    }
                                                }
                                                   
                                            @endphp
                                        @else
                                        @endif
                                    @else
                                        @php
                                            $is_absent = '';
                                            $if_restday = '';
                                            
                                            $if_leave = employeeHasLeave($emp->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);
                                        
                                            // $abs=0;
                                        @endphp  
                                    @endif
                                    <td @if($if_has_ob) class='bg-info'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][in]" value="@if($time_start){{date('h:i A',strtotime($time_start))}}@endif">@if($time_start){{date('h:i A',strtotime($time_start))}}@endif</td>
                                    <td @if($if_has_ob) class='bg-info'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][out]" value="@if($time_end){{date('h:i A',strtotime($time_end))}}@endif">@if($time_end){{date('h:i A',strtotime($time_end))}}@endif</td>
                                    @php
                                        $leave_count = 0;
                                        $abs_half = 0;
                                        if($if_leave)
                                        {
                                            $l = explode('-',$if_leave);
                                            $leave_count = (double) $l[1];
                                            if(str_contains($if_leave,"Without"))

                                            {
                                                $leave_count = 0;
                                                $abs_half = $l[1];
                                            }
                                            // dd($leave_count);
                                        }
                                    @endphp
                                
                                    @php
                                        $work =0;
                                        $work_ot =0;
                                        $undertime_hrs = 0;
                                        $undertime = 0;
                                        $original_sched = 0;
                                        $overtime = 0;
                                        $schedule_hours = 0;
                                        if($employee_schedule)
                                        {
                                         
                                            $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to);
                                            $schedule_in = strtotime($date_r." ".$employee_schedule->time_in_to);
                                            if(($schedule_out) < ($schedule_in))
                                            {
                                                
                                                $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to)+86400;
                                                // dd(date('Y-m-d H:i',$schedule_out)." ".date('Y-m-d H:i',$schedule_in));
                                            }
                                            $schedule_hours = ((($schedule_out)-($schedule_in))/3600);
                                            // dd(date('Y-m-d',strtotime($date_r)));
                                            if($schedule_hours > 8)
                                            {
                                                $schedule_hours =  $schedule_hours-1;
                                                
                                                
                                            }
                                            if($emp->employee_code == "A340612") //frosie
                                            {
                                                $schedule_hours =  $schedule_hours-1;
                                                
                                                
                                            }
                                           
                                        
                                        
                                    }
                                    @endphp
                                    @if((($time_start)&&($time_end)) && $employee_schedule)    
                                        @php
                                            $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to);
                                            $schedule_in = strtotime($date_r." ".$employee_schedule->time_in_to);
                                            
                                            if(($schedule_out) < ($schedule_in))
                                            {
                                                
                                                $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to)+86400;
                                                // dd(date('Y-m-d H:i',$schedule_out)." ".date('Y-m-d H:i',$schedule_in));
                                            }
                                            $original_sched = ((($schedule_out)-($schedule_in))/3600);
                                    
                                            $time_start_ts = strtotime($time_start);
                                            $time_end_ts = strtotime($time_end);
                                            // if ($time_end_ts < $time_start_ts) {
                                            //     $time_end_ts += 86400; `
                                            // }
                                            if(strtotime($date_r." ".$employee_schedule->time_in_from) > $time_start_ts)
                                            {
                                                $time_start_ts = strtotime($date_r." ".$employee_schedule->time_in_from);
                                            }
                                            $work_ot =  round((($time_end_ts - $time_start_ts)/3600), 2);
                                        
                                            if($time_end_ts > $schedule_out)
                                            {
                                                $time_end_ts =  $schedule_out;
                                              
                                            }
                                            $work =  round((($time_end_ts - $time_start_ts)/3600), 2);
                                            
                                         
                                            $schedule_hours = 0;
                                            
                                            if($employee_schedule->time_in_from)
                                            {
                                                $schedule_hours = ((($schedule_out)-($schedule_in))/3600);
                                              
                                              
                                                if($schedule_hours > 8)
                                                {
                                                    $schedule_hours =  $schedule_hours-1;
                                                  
                                                    if($work >= ($schedule_hours/1.5))
                                                    {
                                                       
                                                        $work = $work-1;
                                                       
                                                        
                                                    }
                                                   
                                                    
                                                }
                                                if($emp->employee_code == "A340612")//frosie
                                                {
                                                    $schedule_hours =  $schedule_hours-1;
                                                    if($work >= ($schedule_hours/1.5))
                                                    {
                                                       
                                                        $work = $work-1;
                                                       
                                                        
                                                    }
                                                    
                                                }

                                              
                                                if($schedule_hours > $work)
                                                {
                                                    $undertime = (double) number_format($schedule_hours - $work,2);
                                                    // dd($undertime);
                                                }
                                               if($work_ot > $original_sched)
                                               {
                                                $overtime = (double) number_format($work_ot - $original_sched,2);
                                                
                                               }
                                            //    if($date_r == "2024-07-10")
                                            //    {
                                            //     dd($original_sched);
                                            //    }
                                                
                                                
                                              
                                                if($work > $schedule_hours)
                                                {
                                                    $work = $schedule_hours;
                                                }
                                                if($leave_count == .5)
                                                {
                                                    if($work > $schedule_hours)
                                                    {

                                                        $work = $schedule_hours/2;
                                                    }
                                                }
                                                if($abs_half == .5)
                                                {
                                                    if($work > $schedule_hours)
                                                    {
                                                        $work = $schedule_hours/2;
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
                                                $undertime_hrs = $undertime - ($late_diff_hours);

                                                if($late_diff_hours >= ($schedule_hours/2.25))
                                                {
                                                    $undertime_hrs = $undertime - ($late_diff_hours-1);
                                                }
                                          
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
                                            $previous_abs = $abs;
                                        @endphp
                                    @endif
                                    @php
                                        $late = $late_diff_hours*60;
                                      
                                        if($late/60 > (($schedule_hours)/2.25))
                                        {
                                            // dd($late);
                                            $late = $late-60;
                                            
                                        }
                                        if($undertime_hrs/60 > ($schedule_hours/2))

                                        {
                                            $undertime_hrs = $undertime_hrs -60;
                                        }
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

                                        if($abs_half == .5)
                                        {
                                            $abs = .5;
                                            if($work < ($schedule_hours/2))
                                                {
                                                    $late = ($schedule_hours/2)-$work;
                                                    if($work < $schedule_hours/2)
                                                    {
                                                        $late = (double) number_format(($schedule_hours/2 - $work),2) *60;
                                                        $undertime_hrs = 0;
                                                    } 
                                                }
                                                else{
                                                    $work = ($schedule_hours/2);
                                                    $late = 0;
                                                    $undertime_hrs = 0;
                                                }
                                        }
                                        
                                        $overtime = $overtime+$late_diff_hours;
                                    @endphp
                                    @php
                                    if($work <0)
                                    {
                                        $work = 0;
                                    }
                                    if($late <0)
                                    {
                                        $late = 0;
                                    }
                                    if($undertime_hrs <0)
                                    {
                                        $undertime_hrs = 0;
                                    }
                              
                                    @endphp
                                    @php
                                    $approved_overtime_hrs = $emp->approved_ots ? employeeHasOTDetails($emp->approved_ots,date('Y-m-d',strtotime($date_r))) : "";
                                   
                                    $night_diff = 0;
                                    $night_diff_ot = 0;
                                    if(($time_start!=null )&& ($time_end!=null))
                                    {
                                            $nightdiff_start = $time_start;
                                            $nightdiff_end = $time_end;
                                        
                                        if($employee_schedule)
                                        {
                                            
                                            $start_schedule = (date('Y-m-d',strtotime($time_start))." ".$employee_schedule->time_in_to);
                                            $end_schedule = (date('Y-m-d',strtotime($time_start))." ".$employee_schedule->time_out_to);

                                            if(strtotime($start_schedule) > strtotime($end_schedule))
                                            {
                                                $s = date('Y-m-d', strtotime($time_start . ' +1 day'));
                                                $end_schedule = date('Y-m-d H:i', strtotime($s." ".$employee_schedule->time_out_to));
                                            }
                                       
                                            if(strtotime($start_schedule) > strtotime($time_start))
                                            {   
                                                $nightdiff_start = $start_schedule;
                                            }
                                            if(strtotime($end_schedule) < strtotime($time_end))
                                            {   
                                                $nightdiff_end = $end_schedule;
                                            }
                                            $night_diff = night_difference_per_company($nightdiff_start,$nightdiff_end);
                                            if((strtotime($end_schedule)-strtotime($start_schedule))/3600 > 8)
                                            {

                                            
                                            if($night_diff >= 5)
                                            {
                                                $night_diff = $night_diff - 1;
                                            }
                                            }
                                            if($night_diff < 7)
                                            {
                                                $night_diff_ot = night_difference_per_company($time_start,$time_end)-$night_diff;
                                            }
                                            
                                        }
                                         
                                        
                                        
                                    }
                                    if($night_diff_ot < .5)
                                    {
                                        $night_diff_ot = 0;
                                    }
                                    if($overtime <1)
                                    {
                                        $overtime =0;
                                    }
                                    if($overtime < $approved_overtime_hrs)
                                    {
                                        $overtime = ($overtime);
                                    }
                                    else
                                    {
                                        $overtime = ($approved_overtime_hrs);
                                    }
                                    if($overtime > 0)
                                    {
                                        if($overtime < $night_diff_ot)
                                        {
                                            $night_diff_ot = $overtime;
                                        }

                                    }
                                    else
                                    {
                                        $night_diff_ot = 0;
                                    }
                                        // $subtotal_overtimes =  $subtotal_overtimes + $overtime;
                                        $restday_ot = 0;
                                        $restday_ot_ge = 0;
                                        $restday_nd = 0;
                                        $work_rest = 0;
                                        $restnd = 0;
                                        $restnd_ge = 0;
                                        $rest = "";
                                    
                                        if($employee_schedule != null)
                                        {
                                                if($employee_schedule->time_in_from == '00:00')
                                                {
                                                    $rest = "RESTDAY";
                                                }
                                                if($employee_schedule->time_in_from == '')
                                                {
                                                    $rest = "RESTDAY";
                                                    
                                           
                                                }
                                                if($employee_schedule->time_in_from == null)
                                                {
                                                    $rest = "RESTDAY";
                                                   
                                                }
                                           
                                        }
                                        else {
                                            
                                            $rest = "RESTDAY";
                                        }
                                       
                                        if($rest == "RESTDAY")
                                        {
                                            $overtime = 0;
                                            $night_diff = 0;
                                            $night_diff_ot = 0;
                                            if(($time_start) && ($time_end) && ($approved_overtime_hrs >0))
                                            {
                                                
                                                $work_rest =  round(((strtotime($time_end) - strtotime($time_start))/3600), 2);
                                                $restnd =  night_difference_per_company($time_start,$time_end);
                                                if($work_rest >9 )
                                                { 
                                                    $restnd =  round(night_difference_per_company($time_start,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($time_start)))));
                                                    $restnd_ge = night_difference_per_company($time_start,$time_end);
                                                    $restnd_ge = $restnd_ge - $restnd;
                                                    $restnd = $restnd-1;
                                                    if($restnd <0)
                                                    {
                                                        $restnd = 0;
                                                    }
                                                    if($restnd_ge <0)
                                                    {
                                                        $restnd_ge = 0;
                                                    }
                                                }
                                              
                                                
                                            }
                                            $late = 0;
                                            $undertime = 0;
                                            if($work_rest > 0)
                                            {
                                                if($work_rest > $approved_overtime_hrs)
                                                {
                                                    $work_rest = $approved_overtime_hrs;
                                                }
                                                
                                                if($work_rest >2)
                                                {
                                                    $restday_ot = $work_rest;
                                                    if($work_rest >= 8)
                                                    {
                                                        $restday_ot = 8;
                                                        $restday_ot_ge = $work_rest-8;
                                                    }
                                                }
                                            }
                                           
                                        }
                                    if($overtime == null)
                                    {
                                        $overtime = 0;
                                    }
                                    $lh_ot = 0;
                                    $sh_ot = 0;
                                    $lh_ot_ge = 0;
                                    $sh_ot_ge = 0;
                                    $sh_ot_nd = 0;
                                    $sh_ot_nd_ge = 0;
                                    $lh_ot_nd = 0;
                                    $lh_ot_nd_ge = 0;
                                    $check_if_holiday = checkIfHoliday(date('Y-m-d',strtotime($date_r)),$emp->location);
                                    if($check_if_holiday)
                                    {
                                        // dd($check_if_holiday);
                                      $work = $schedule_hours;
                                    //   dd($schedule_hours);
                                      if($rest == "RESTDAY")
                                      {
                                        $work = 0;
                                      }
                                      if($abs == 1)
                                      {
                                        $work = 0;
                                      }

                                      $late = 0;
                                      $undertime_hrs = 0;
                                      $overtime = 0;
                                      $night_diff = 0;
                                      $night_diff_ot = 0;
                                      $restday_ot = 0;
                                      $restday_ot_ge = 0;
                                      $restnd = 0;
                                      
                                        $approved_overtime_hrs = $emp->approved_ots ? employeeHasOTDetails($emp->approved_ots,date('Y-m-d',strtotime($date_r))) : "";
                                        if(strtotime($time_end) - strtotime($time_start) > 2)
                                        {
                                            if($approved_overtime_hrs > 2)
                                        {
                                            if($check_if_holiday == "Special Holiday")
                                            {
                                                $sh_ot = 8;
                                               
                                                $sh_ot_nd =  night_difference_per_company($time_start,$time_end);
                                                if($sh_ot_nd >=4.5 )
                                                {
                                                    $sh_ot_nd = $sh_ot_nd-1;
                                                }
                                                if($sh_ot_nd > $sh_ot)
                                                {
                                                    $sh_ot_nd = $sh_ot;
                                                }
                                                if($approved_overtime_hrs <= 8)
                                                {
                                                    
                                                    $sh_ot = $approved_overtime_hrs;
                                                }
                                                else
                                                {
                                                    $sh_ot_ge = $approved_overtime_hrs-8;
                                                }

                                                if($employee_schedule)
                                                {
                                                    $time_start_string = strtotime($time_start);
                                                    $time_end_string = strtotime($time_end);
                                                    $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to);
                                                    $schedule_in = strtotime($date_r." ".$employee_schedule->time_in_to);
                                                    
                                                    if(($schedule_out) < ($schedule_in))
                                                    {
                                                        
                                                        $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to)+86400;
                                                    }
                                                   
                                                    if($time_end_string>$schedule_out)
                                                    {
                                                        $sh_ot_nd =  night_difference_per_company(date('Y-m-d H:i',$schedule_in),date('Y-m-d H:i',$schedule_out));
                                                        $sh_ot_use = $sh_ot_nd;
                                                        if($sh_ot_nd >=4.5 )
                                                        {   
                                                            $schedule_hours = ((($schedule_out)-($schedule_in))/3600);
                                                            if($schedule_hours > 8)
                                                            {
                                                                $sh_ot_nd = $sh_ot_nd-1;
                                                            }
                                                        }
                                                        $sh_ot_nd_ge =night_difference_per_company(date('Y-m-d H:i',$schedule_in),$time_end)-$sh_ot_use;
                                                       
                                                        if($sh_ot_nd_ge <0)
                                                        {
                                                            $sh_ot_nd_ge=0;
                                                        }
                                                       
                                                    }
                                                    else {
                                                        $sh_ot_nd =  night_difference_per_company(date('Y-m-d H:i',$schedule_in),$time_end);
                                                        if($sh_ot_nd >=4.5 )
                                                        {   
                                                            if($schedule_hours > 8)
                                                            {
                                                            $sh_ot_nd = $sh_ot_nd-1;
                                                            }
                                                        }
                                                    }
                                                 
                                                }
                                               
                                            }
                                            else {
                                                
                                                $lh_ot = 8;
                                                if($approved_overtime_hrs <= 8)
                                                {
                                                    $lh_ot = $approved_overtime_hrs;
                                                   
                                                }
                                                else
                                                {
                                                    $lh_ot_ge = $approved_overtime_hrs-8;
                                                }

                                                if($employee_schedule)
                                                {
                                                    $time_start_string = strtotime($time_start);
                                                    $time_end_string = strtotime($time_end);
                                                    $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to);
                                                    $schedule_in = strtotime($date_r." ".$employee_schedule->time_in_to);
                                                    
                                                    if(($schedule_out) < ($schedule_in))
                                                    {
                                                        
                                                        $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to)+86400;
                                                    }
                                                    
                                                    if($time_end_string>$schedule_out)
                                                    {
                                                        $lh_ot_nd =  night_difference_per_company(date('Y-m-d H:i',$schedule_in),date('Y-m-d H:i',$schedule_out));
                                                        $lh_ot_use = $lh_ot_nd;
                                                        if($lh_ot_nd >=4.5 )
                                                        {   
                                                            $schedule_hours = ((($schedule_out)-($schedule_in))/3600);
                                                            if($schedule_hours > 8)
                                                            {
                                                            $lh_ot_nd = $lh_ot_nd-1;
                                                            }
                                                        }
                                                        $lh_ot_nd_ge =night_difference_per_company(date('Y-m-d H:i',$schedule_in),$time_end)-$lh_ot_use;
                                                        if($lh_ot_nd_ge <0)
                                                        {
                                                            $lh_ot_nd_ge=0;
                                                        }
                                                       
                                                    }
                                                    else {
                                                        $lh_ot_nd =  night_difference_per_company(date('Y-m-d H:i',$schedule_in),$time_end);
                                                        if($lh_ot_nd >=4.5 )
                                                        {   
                                                            $schedule_hours = ((($schedule_out)-($schedule_in))/3600);
                                                            if($schedule_hours > 8)
                                                            {
                                                            $lh_ot_nd = $lh_ot_nd-1;
                                                            }
                                                        }
                                                    }
                                                 
                                                }
                                            }
                                        }
                                        }
                                      
                                    }
                                    if($abs == 1)
                                    {
                                        $work = 0;
                                        $late = 0;
                                        $undertime_hrs = 0;
                                    }
                                    $subtotal_abs += $abs;
                                    $subtotal_leave_w_pay += $leave_count;
                                    $subtotal_reg_hrs += $work;
                                    $subtotal_late += ($late);
                                    $subtotal_undertime += ($undertime_hrs*60);
                                    $subtotal_overtimes +=$overtime;
                                    $subtotal_nd += $night_diff;
                                    $subtotal_ot_nd += $night_diff_ot;
                                    $subtotal_rd_ot += $restday_ot;
                                    $subtotal_rd_ot_ge += $restday_ot_ge;
                                    $subtotal_rd_nd += $restnd;
                                    $subtotal_rd_nd_ge += 0;
                                   
                                    @endphp
                                    <td @if($abs-$leave_count>0) class='bg-danger'@endif ><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][abs]" value="{{$abs}}">{{number_format($abs,2)}}</td>
                                    <td ><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][lv_w_pay]" value="{{$leave_count}}">{{$leave_count}}</td>
                                    <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][reg_hrs]" value="{{$work}}">{{$work}}</td>
                                    <td @if($late>0) class='bg-danger'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][late_min]" value="{{number_format($late)}}">{{number_format($late)}}</td>
                                    <td  @if($undertime_hrs>0) class='bg-danger'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][undertime_min]" value="{{$undertime_hrs*60}}">{{number_format($undertime_hrs*60,2)}}</td>
                                    <td @if($overtime>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][reg_ot]" value="{{$overtime}}">{{number_format($overtime,2)}}</td> {{-- REG OT --}}
                                    <td @if($night_diff>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][reg_nd]" value="{{$night_diff}}">{{number_format($night_diff,2)}}</td> {{-- REG ND --}}
                                    <td @if($night_diff_ot>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][reg_ot_nd]" value="{{$night_diff_ot}}">{{number_format($night_diff_ot,2)}}</td> {{-- REG OT ND --}}
                                    <td @if($restday_ot>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_ot]" value="{{$restday_ot}}">{{number_format($restday_ot,2)}}</td>  {{-- RST OT --}}
                                    <td @if($restday_ot_ge>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_ot_over_eight]" value="{{$restday_ot_ge}}">{{number_format($restday_ot_ge,2)}}</td> {{-- RST OT > 8 --}}
                                    <td @if($restnd>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_nd]" value="{{$restnd}}">{{number_format($restnd,2)}}</td> {{-- RST ND --}}
                                    <td  @if($restnd_ge>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_nd_over_eight]" value="{{$restnd_ge}}">{{number_format($restnd_ge,2)}}</td> {{-- RST ND > 8 --}}
                                    <td @if($lh_ot>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][lh_ot]" value="{{$lh_ot}}">{{number_format($lh_ot,2)}}</td> {{-- LH OT --}}
                                    <td @if($lh_ot_ge>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][lh_ot_over_eight]" value="{{$lh_ot_ge}}">{{number_format($lh_ot_ge,2)}}</td> {{-- LH OT > 8 --}}
                                    <td @if($lh_ot_nd>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][lh_nd]" value="{{$lh_ot_nd}}">{{number_format($lh_ot_nd,2)}}</td> {{-- LH ND --}}
                                    <td @if($lh_ot_nd_ge>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][lh_nd_over_eight]" value="{{$lh_ot_nd_ge}}">{{number_format($lh_ot_nd_ge,2)}}</td> {{-- LH ND > 8 --}}
                                    <td @if($sh_ot>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][sh_ot]" value="{{$sh_ot}}">{{number_format($sh_ot,2)}}</td> {{-- SH OT --}}
                                    <td @if($sh_ot_ge>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][sh_ot_over_eight]" value="{{$sh_ot_ge}}">{{number_format($sh_ot_ge,2)}}</td> {{-- SH OT > 8 --}}
                                    <td @if($sh_ot_nd>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][sh_nd]" value="{{$sh_ot_nd}}">{{number_format($sh_ot_nd,2)}}</td> {{-- SH ND --}}
                                    <td @if($sh_ot_nd_ge>0) class='bg-warning'@endif><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][sh_nd_over_eight]" value="{{$sh_ot_nd_ge}}">{{number_format($sh_ot_nd_ge,2)}}</td> {{-- SH ND > 8 --}}
                                    <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_lh_ot]" value="0.00">0.00</td> {{-- RST LH OT --}}
                                    <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_lh_ot_over_eight]" value="0.00">0.00</td> {{-- RST LH OT > 8 --}}
                                    <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_lh_nd]" value="0.00">0.00</td> {{-- RST LH ND --}}
                                    <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_lh_nd_gt_8]" value="0.00">0.00</td> {{-- RST LH ND > 8 --}}
                                    <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_sh_ot]" value="0.00">0.00</td> {{-- RST SH OT --}}
                                    <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_sh_ot_gt_8]" value="0.00">0.00</td> {{-- RST SH OT > 8 --}}
                                    <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_sh_nd]" value="0.00">0.00</td> {{--RST SH ND--}}
                                    <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_sh_nd_over_eight]" value="0.00">0.00</td> {{-- RST SH ND > 8 --}}
                                    <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][remarks]" value="{{$if_leave}} {{$if_has_ob ? 'OB' : ''}}">
                                        {{$if_leave}} {{$if_has_ob ? 'OB' : ''}}
                                    </td>
                              
                                            
                                </tr>
                                @endforeach
                                <tr>
                                    <td><strong>Subtotal</strong></td>
                                    <td><strong>{{ $emp->employee_code }}</strong></td>
                                    <td><strong>{{$emp->first_name . ' ' . $emp->last_name}}</strong></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><strong>{{ number_format($subtotal_abs,2) }}</strong></td>
                                    <td><strong>{{ number_format($subtotal_leave_w_pay,2) }}</strong></td>
                                    <td><strong>{{ number_format($subtotal_reg_hrs,2) }}</strong></td>
                                    <td><strong>{{ number_format($subtotal_late,2) }}</strong></td>
                                    <td><strong>{{ number_format($subtotal_undertime,2) }}</strong></td>
                                    <td><strong>{{ number_format($subtotal_overtimes,2)}}</strong></td>
                                    <td><strong>{{ number_format($subtotal_nd,2)}}</strong></td>
                                    <td><strong>{{ number_format($subtotal_ot_nd,2)}}</strong></td>
                                    <td><strong>{{ number_format($subtotal_rd_ot,2)}}</strong></td>
                                    <td><strong>{{ number_format($subtotal_rd_ot_ge,2)}}</strong></td>
                                    <td><strong>{{ number_format($subtotal_rd_nd,2)}}</strong></td>
                                    <td><strong>{{ number_format($subtotal_rd_nd_ge,2)}}</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td><strong>0.00</strong></td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
    $end_night   = mktime('06','00','00',date('m',$start_work),date('d',$start_work) + 1,date('Y',$start_work));

    if($start_work >= $start_night && $start_work <= $end_night)
    {
        if($end_work >= $end_night)
        {
            return ($end_night - $start_work) / 3600;
        }
        else
        {
            return ($end_work - $start_work) / 3600;
        }
    }
    elseif($end_work >= $start_night && $end_work <= $end_night)
    {
        if($start_work <= $start_night)
        {
            return ($end_work - $start_night) / 3600;
        }
        else
        {
            return ($end_work - $start_work) / 3600;
        }
    }
    else
    {
        if($start_work < $start_night && $end_work > $end_night)
        {
            return ($end_night - $start_night) / 3600;
        }
        return 0;
    }
}

@endphp
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">

<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script>
  function get_min(value)
  {
    document.getElementById("to").min = value;
  }
  $(document).ready(function() 
    {
        new DataTable('.employee_attendance', 
        {
            // pagelenth:25,
            fixedColumns: {
                leftColumns: 1,  // 'start' and 'end' have been replaced with 'leftColumns' for clarity
            },
            paginate:false,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'excel'
            ],
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }],
            order: [] 
        });
    });
</script>
@endsection
