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
                        <form method="get" onsubmit="show();" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select data-placeholder="Select Company" class="form-control form-control-sm required js-example-basic-single" style="width:100%;" name="company" id="companySelect" required>
                                            <option value="">-- Select Company --</option>
                                            @foreach($companies as $comp)
                                            <option value="{{$comp->id}}" @if ($comp->id == $company) selected @endif>{{$comp->company_name}} - {{$comp->company_code}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="fromDate" name="from" value="{{$from_date}}" placeholder="From Date" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="toDate" name="to" value="{{$to_date}}" placeholder="To Date" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ url('/biometrics-per-company') }}" class="btn btn-warning">Reset Filter</a>
                                </div>
                            </div>
                        </form>

                            <!-- <form method='get' onsubmit='show();' enctype="multipart/form-data">
                                <div class="row">
                                    <div class='col-md-4'>
                                        <div class="form-group">
                                            <select data-placeholder="Select Company" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='company' id='companySelect' onchange="checkLogDate();" required>
                                                <option value="">-- Select Company --</option>
                                                @foreach($companies as $comp)
                                                <option value="{{$comp->id}}" @if ($comp->id == $company) selected @endif>{{$comp->company_name}} - {{$comp->company_code}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class='col-md-2'>
                                        <div class="form-group">
                                            <input type="date" value='{{$from_date}}' class="form-control" name="from" id='fromDate' max='{{date('Y-m-d')}}' onchange="checkLogDate();" required />
                                        </div>
                                    </div>
                                    <div class='col-md-2'>
                                        <div class="form-group">
                                            <input type="date" value='{{$to_date}}' class="form-control" name="to" id='toDate' max='{{date('Y-m-d')}}' onchange="checkLogDate();" required />
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <button type="submit" class="btn btn-primary" id="btnFilter">Filter</button>
                                        <a href="{{ url('/biometrics-per-company') }}" class="btn btn-warning">Reset Filter</a>
                                    </div>
                                </div>
                            </form> -->
                        </p>
                        <form method="POST" action="{{ route('attendance.store') }}" id="attendanceForm" onsubmit="return showConfirmation()" enctype="multipart/form-data">
                            @csrf
                            @if($date_range)
                                {{-- <a href="attendance-per-company-export?company={{$company}}&from={{$from_date}}&to={{$to_date}}" class='btn btn-info mb-1'>Export {{count($emp_data)}} Employees</a> --}}
                                <button type="submit" class="btn btn-success mb-1">Post</button>
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
                                            @endphp

                                            @foreach($date_range as $date_r)
                                            @php
                                                $employee_schedule = employeeSchedule($schedules,$date_r,$emp->schedule_id, $emp->employee_code);
                                                $rest = "";
                                                $if_leave = "";
                                            @endphp
                                            <tr>
                                                <input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][to]" value="{{$to_date}}">
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][company_id]" value="{{$emp->company->id}}">{{$emp->company->company_code}}</td>
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][employee_no]" value="{{$emp->employee_code}}">{{$emp->employee_code}}</td>
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][name]" value="{{$emp->last_name .', '. $emp->first_name . ' ' . $emp->middle_name}}">{{$emp->first_name . ' ' . $emp->last_name}}</td>
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][log_date]" value="{{date('Y-m-d',strtotime($date_r))}}">{{date('d/m/Y',strtotime($date_r))}}</td>
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][shift]" value="{{$employee_schedule ? date('h:i A', strtotime($employee_schedule->time_in_to)).'-'.date('h:i A', strtotime($employee_schedule->time_out_to)) : 'RESTDAY'}}">
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
                                                    $cenvertedTime = date('Y-m-d 00:00:00');
                                                    if($employee_schedule != null)
                                                    {
                                                        if($employee_schedule->time_in_from != '00:00')
                                                        {
                                                            $cenvertedTime = date('Y-m-d H:i:s',strtotime('-3 hours',strtotime($date_r." ".$employee_schedule->time_in_from)));
                                                            // dd($cenvertedTime);
                                                        }
                                                    }
                                                    // dd($cenvertedTime);
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
                                                    if($time_in->time_out != null)
                                                    {
                                                        if(strtotime($if_has_ob->date_to) > strtotime($final_time_out))
                                                        {
                                                        
                                                        $time_end = date('Y-m-d h:i A',strtotime($if_has_ob->date_to));
                                                        }
                                                        else {
                                                            
                                                            $time_end = date('Y-m-d h:i A',strtotime($final_time_out));
                                                        }
                                                        
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

                                                @if(($time_start) || ($time_end))
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
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][in]" value="@if($time_start){{date('h:i A',strtotime($time_start))}}@endif">@if($time_start){{date('h:i A',strtotime($time_start))}}@endif</td>
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][out]" value="@if($time_end){{date('h:i A',strtotime($time_end))}}@endif">@if($time_end){{date('h:i A',strtotime($time_end))}}@endif</td>
                                                @php
                                                    $leave_count = 0;
                                                    if($if_leave)
                                                    {
                                                        $l = explode('-',$if_leave);
                                                        $leave_count = (double) $l[1];
                                                        if(str_contains($if_leave,"Without"))

                                                        {
                                                            $leave_count = 0;
                                                        }
                                                        // dd($leave_count);
                                                    }
                                                @endphp
                                            
                                                @php
                                                    $work =0;
                                                    $undertime_hrs = 0;
                                                    $undertime = 0;
                                                    $original_sched = 0;
                                                    $overtime = 0;
                                                @endphp
                                                @if($employee_schedule)
                                                @php
                                                     $original_sched = ((strtotime($date_r." ".$employee_schedule->time_out_to)-strtotime($date_r." ".$employee_schedule->time_in_to))/3600);
                                                @endphp
                                               
                                                @endif
                                                @if((($time_start)&&($time_end)) && $employee_schedule)    
                                                    @php
                                                        $time_start_ts = strtotime($time_start);
                                                        $time_end_ts = strtotime($time_end);
                                                        if ($time_end_ts < $time_start_ts) {
                                                            $time_end_ts += 86400; 
                                                        }
                                                        if(strtotime($date_r." ".$employee_schedule->time_in_from) > $time_start_ts)
                                                        {
                                                            $time_start_ts = strtotime($date_r." ".$employee_schedule->time_in_from);
                                                        }
                                                        $work =  round((($time_end_ts - $time_start_ts)/3600), 2);
                                                        $schedule_hours = 0;
                                                    
                                                        if($employee_schedule->time_in_from)
                                                        {
                                                            $schedule_hours = ((strtotime($date_r." ".$employee_schedule->time_out_to)-strtotime($date_r." ".$employee_schedule->time_in_to))/3600);
                                                            // dd($schedule_hours);
                                                            if($schedule_hours > 8)
                                                            {
                                                                $schedule_hours =  $schedule_hours-1;
                                                            }

                                                          
                                                            if($original_sched > $work)
                                                            {
                                                                $undertime = (double) number_format($original_sched - $work,2);
                                                            }
                                                            else {
                                                                $overtime = (double) number_format($work - $original_sched,2);
                                                            }
                                                          
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
                                                $subtotal_abs += $abs;
                                                $subtotal_leave_w_pay += $leave_count;
                                                $subtotal_reg_hrs += $work;
                                                $subtotal_late += $late;
                                                $subtotal_undertime += ($undertime_hrs*60);
                                                @endphp
                                                @php
                                                $approved_overtime_hrs = $emp->approved_ots ? employeeHasOTDetails($emp->approved_ots,date('Y-m-d',strtotime($date_r))) : "";
                                                
                                               
                                                $night_diff = 0;
                                                if(($time_start!=null )&& ($time_end!=null))
                                                {
                                                        $nightdiff_start = $time_start;
                                                        $nightdiff_end = $time_end;
                                                    
                                                    if($employee_schedule)
                                                    {
                                                        $start_schedule = (date('Y-m-d',strtotime($time_start))." ".$employee_schedule->time_in_to);
                                                        $end_schedule = (date('Y-m-d',strtotime($time_end))." ".$employee_schedule->time_out_to);
                                                        if(strtotime($start_schedule) > strtotime($time_start))
                                                        {   
                                                            $nightdiff_start = $start_schedule;
                                                        }
                                                        if(strtotime($end_schedule) > strtotime($time_end))
                                                        {   
                                                            $nightdiff_end = $end_schedule;
                                                        }
                                                    }
                                                     $night_diff = night_difference_per_company($nightdiff_start,$nightdiff_end);
                                                    
                                                  

                                                }
                                                if($overtime < $approved_overtime_hrs)
                                                {
                                                    $overtime = roundDownToNearestHalf($overtime);
                                                }
                                                else
                                                {
                                                    $overtime = roundDownToNearestHalf($approved_overtime_hrs);
                                                }
                                                    $subtotal_overtimes =  $subtotal_overtimes + $overtime;
                                                
                                                @endphp
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][abs]" value="{{$abs}}">{{number_format($abs,2)}}</td>
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][lv_w_pay]" value="{{$leave_count}}">{{$leave_count}}</td>
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][reg_hrs]" value="{{$work}}">{{$work}}</td>
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][late_min]" value="{{number_format($late)}}">{{number_format($late)}}</td>
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][undertime_min]" value="{{$undertime_hrs*60}}">{{$undertime_hrs*60}}</td>
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][reg_ot]" value="0.00">{{$overtime}}</td> {{-- REG OT --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][reg_nd]" value="0.00">{{number_format($night_diff,2)}}</td> {{-- REG ND --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][reg_ot_nd]" value="0.00">0.00</td> {{-- REG OT ND --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_ot]" value="0.00">0.00</td>  {{-- RST OT --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_ot_over_eight]" value="0.00">0.00</td> {{-- RST OT > 8 --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_nd]" value="0.00">0.00</td> {{-- RST ND --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_nd_over_eight]" value="0.00">0.00</td> {{-- RST ND > 8 --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][lh_ot]" value="0.00">0.00</td> {{-- LH OT --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][lh_ot_over_eight]" value="0.00">0.00</td> {{-- LH OT > 8 --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][lh_nd]" value="0.00">0.00</td> {{-- LH ND --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][lh_nd_over_eight]" value="0.00">0.00</td> {{-- LH ND > 8 --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][sh_ot]" value="0.00">0.00</td> {{-- SH OT --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][sh_ot_over_eight]" value="0.00">0.00</td> {{-- SH OT > 8 --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][sh_nd]" value="0.00">0.00</td> {{-- SH ND --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][sh_nd_over_eight]" value="0.00">0.00</td> {{-- SH ND > 8 --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_lh_ot]" value="0.00">0.00</td> {{-- RST LH OT --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_sh_ot_over_eight]" value="0.00">0.00</td> {{-- RST LH OT > 8 --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_lh_nd]" value="0.00">0.00</td> {{-- RST LH ND --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_lh_nd_gt_8]" value="0.00">0.00</td> {{-- RST LH ND > 8 --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_sh_ot]" value="0.00">0.00</td> {{-- RST SH OT --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_sh_ot_gt_8]" value="0.00">0.00</td> {{-- RST SH OT > 8 --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_sh_nd]" value="0.00">0.00</td> {{--RST SH ND--}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][rst_sh_nd_over_eight]" value="0.00">0.00</td> {{-- RST SH ND > 8 --}}
                                                <td><input type="hidden" name="employees[{{ $emp->employee_code }}][{{$date_r}}][remarks]" value="{{$if_leave}} {{$if_has_ob ? 'OB' : ''}}">
                                                    {{$if_leave}} {{$if_has_ob ? 'OB' : ''}}
                                                </td>
                                                <!-- <td>0.00</td> {{--reg ot--}}
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
                                                <td>0.00</td> {{--RST SH ND > 8	--}} -->
                                                <!-- {{-- <td>{{$if_leave}} --}}
                                                <td>
                                                    {{$if_leave}}
                                                @if($if_has_ob)
                                                OB
                                                @endif
                                            </td> {{--Remarks--}} -->
                                                        
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td><strong>Subtotal</strong></td>
                                                <td><strong>{{ $emp->employee_code }}</strong></td>
                                                <td><strong>{{$emp->first_name . ' ' . $emp->last_name}}</strong></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><strong>{{ $subtotal_abs }}</strong></td>
                                                <td><strong>{{ $subtotal_leave_w_pay }}</strong></td>
                                                <td><strong>{{ $subtotal_reg_hrs }}</strong></td>
                                                <td><strong>{{ $subtotal_late }}</strong></td>
                                                <td><strong>{{ $subtotal_undertime }}</strong></td>
                                                <td><strong>{{$approved_overtimes}}</strong></td>
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
                                                <td><strong>0.00</strong></td>
                                                <td><strong>0.00</strong></td>
                                                <td><strong>0.00</strong></td>
                                                <td><strong>0.00</strong></td>
                                                <td><strong>0.00</strong></td>
                                               
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    function night_difference($start_work,$end_work) {
        $start_night = mktime('22','00','00',date('m',$start_work),date('d',$start_work),date('Y',$start_work));
        $end_night = mktime('06','00','00',date('m',$start_work),date('d',$start_work) + 1,date('Y',$start_work));

        if($start_work >= $start_night && $start_work <= $end_night) { if($end_work>= $end_night)
            {
                return ($end_night - $start_work) / 3600;
            } else {
                return ($end_work - $start_work) / 3600;
            }
        } elseif($end_work >= $start_night && $end_work <= $end_night) { 
            if($start_work <=$start_night) { 
                return ($end_work - $start_night) / 3600; 
            } else { 
                return ($end_work - $start_work) / 3600; 
            } 
        } else { 
            if($start_work < $start_night && $end_work> $end_night)
        {
            return ($end_night - $start_night) / 3600;
        }
            return 0;
        }
    }
    function roundDownToNearestHalf($number) {
            return floor($number * 2) / 2;
        }
@endphp

<!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.12.5/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.12.5/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>

    $(document).ready(function() {
        var fromDateInput = document.getElementById('fromDate');
        var toDateInput = document.getElementById('toDate');

        // Initialize Flatpickr on date inputs
        var fromFlatpickr = flatpickr(fromDateInput, {
            dateFormat: "Y-m-d",
            disable: [], // Initialize with an empty array of disabled dates
        });

        var toFlatpickr = flatpickr(toDateInput, {
            dateFormat: "Y-m-d",
            disable: [], // Initialize with an empty array of disabled dates
        });

        // Event listener for company select change
        $("#companySelect").on('change', function() {
            var selectedCompanyId = $(this).val();
            if (selectedCompanyId) {
                // Enable date inputs
                fromDateInput.disabled = false;
                toDateInput.disabled = false;

                fromDateInput.style.backgroundColor = 'transparent';
                toDateInput.style.backgroundColor = 'transparent';

                // Fetch disabled dates via AJAX
                $.ajax({
                    url: "{{ url('/fetch-disabled-dates') }}/" + selectedCompanyId,
                    type: 'GET',
                    success: function(response) {
                        var logDates = response.log_dates;

                        // Update Flatpickr options to disable fetched dates
                        fromFlatpickr.set('disable', logDates);
                        toFlatpickr.set('disable', logDates);
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to fetch disabled dates: ' + status + ' - ' + error);
                    }
                });
            } else {
                // Disable date inputs if no company is selected
                fromDateInput.disabled = true;
                toDateInput.disabled = true;

                
                // Reset Flatpickr options if no company is selected
                fromFlatpickr.set('disable', []);
                toFlatpickr.set('disable', []);
            }
        });
        // Initialize with the selected company (if any)
        var selectedCompanyId = $("#companySelect").val();
        if (selectedCompanyId) {
            // Trigger change event to fetch and disable dates for initially selected company
            $("#companySelect").trigger('change');
        }
    });



    function get_min(value) {
        document.getElementById("to").min = value;
    }

    function showConfirmation() {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to submit this form?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, submit it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("loader").style.display = "flex";
                document.getElementById('attendanceForm').submit();
            }
        });

        return false; // Prevent default form submission
    }

    // function checkLogDate() {
    //     var companyId = document.getElementById('companySelect').value;
    //     var fromDateInput = document.getElementById('fromDate');
    //     var toDateInput = document.getElementById('toDate');
    //     var btnFilter = document.getElementById('btnFilter');
    //     var fromDate = fromDateInput.value;
    //     var toDate = toDateInput.value;

    //     if (companyId && fromDate && toDate) {
    //         $.ajax({
    //             url: "{{ url('/check-log-date') }}/" + companyId,
    //             type: 'GET',
    //             data: {
    //                 from: fromDate,
    //                 to: toDate
    //             },
    //             success: function(response) {
    //                 var logDates = response.logDates;
    //                 var fromDateValue = new Date(fromDate);
    //                 var toDateValue = new Date(toDate);

    //                 var disableFilterButton = false;
    //                 if (logDates.length > 0) {
    //                     logDates.forEach(function(logDate) {
    //                         var logDateValue = new Date(logDate);

    //                         if (logDateValue >= fromDateValue && logDateValue <= toDateValue) {
    //                             // fromDateInput.value = ''; // Clear input value
    //                             // toDateInput.value = ''; // Clear input value
    //                             fromDateInput.disabled = false;
    //                             toDateInput.disabled = false;
    //                             disableFilterButton = true;
    //                         }
    //                     });
    //                 }
    //                     btnFilter.disabled = disableFilterButton;
    //             },
    //             error: function(xhr, status, error) {
    //                 console.error('AJAX Error: ' + status + ' - ' + error);
    //             }
    //         });
    //     } else {
    //         fromDateInput.disabled = false;
    //         toDateInput.disabled = false;
    //     }
    // }


        </script>
        @endsection

@section('js')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">

<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script>
    $(document).ready(function() 
    {
        new DataTable('.employee_attendance', 
        {
            // pagelenth:25,
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
