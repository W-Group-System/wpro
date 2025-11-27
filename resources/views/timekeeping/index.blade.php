@extends('layouts.header')

@section('css_header')
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css"> --}}

<style>
    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url("{{ asset('login_css/images/loader.gif') }}") 50% 50% no-repeat white;
        opacity: .8;
        background-size: 120px 120px;
    }
</style>
@endsection

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Timekeeping</h4>
                        <form method="get" id="filterForm">
                            <div class="row">
                                <div class="col-md-2">
                                    <select class="form-control js-example-basic-single" name="company" id="companySelect" data-placeholder="Select company" style="width: 100%;" required>
                                        <option></option>
                                        @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{$company->company_code.' - '.$company->company_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select data-placeholder="Select department" style="width: 100%;" class="form-control js-example-basic-single" name="department">
                                        <option></option>
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{$department->code.' - '.$department->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_from" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_to" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">
                                        Filter
                                    </button>
                                    <a href="{{ url('timekeeping-official') }}" class="btn btn-warning">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>

                        <ul class="nav nav-tabs mt-5">
                            <li class="nav-item">
                                <a class="nav-link active" href="#pills-issues" data-toggle="tab" >Issues <span class="badge badge-danger" id="totalIssues">0</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#pills-for-approval" data-toggle="tab" >Pending Approval <span class="badge badge-warning" id="totalPendingApproval">0</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " href="#pills-for-posting" data-toggle="tab" >For Posting <span class="badge badge-success" id="totalForPosting">0</span></a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-issues" role="tabpanel" aria-labelledby="pills-issues-tab">
                                <div class="row mt-5">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger" style="width: 15px; height: 15px; margin-right: 5px;"></div>
                                        <span>Absent</span>
                                    </div>
                        
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered mt-5 issuesTable">
                                                <thead>
                                                    <tr>
                                                        <th>ACTION</th>
                                                        <th>COMPANY</th>
                                                        <th>EMPLOYEE ID</th>
                                                        <th>NAME</th>
                                                        <th>DATE LOGS</th>
                                                        <th>SCHEDULE</th>
                                                        <th>TIME IN</th>
                                                        <th>TIME OUT</th>
                                                        <th>ABSENT</th>
                                                        <th>REG HRS (HRS)</th>
                                                        <th>LATE (MIN)</th>
                                                        <th>UNDERTIME(min)</th>
                                                        <th>LV W/ PAY</th>
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
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade " id="pills-for-approval" role="tabpanel" aria-labelledby="pills-for-posting-tab">
                                <div class="row">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger" style="width: 15px; height: 15px; margin-right: 5px;"></div>
                                        <span>Absent</span>
                                    </div>
                        
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered mt-5 timekeepingTable">
                                                <thead>
                                                    <tr>
                                                        <th>COMPANY</th>
                                                        <th>DEPARTMENT</th>
                                                        <th>SCHEDULE</th>
                                                        <th>EMPLOYEE ID</th>
                                                        <th>NAME</th>
                                                        <th>DATE LOGS</th>
                                                        <th>TIME IN</th>
                                                        <th>TIME OUT</th>
                                                        <th>APPROVERS</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade " id="pills-for-posting" role="tabpanel" aria-labelledby="pills-for-posting-tab">
                                <div class="row">
                                    <form action="{{ url('timekeeping-official/post_dtr') }}" method="post" class="my-3" style="width: 100%;" onsubmit="show()">
                                        @csrf

                                        {{-- @if($department_data)
                                        <button class="btn btn-lg btn-primary mt-3" type="submit">POST DTR</button>
                                        @endif --}}

                                        <div class="d-flex align-items-center ml-2">
                                            <div class="bg-danger" style="width: 15px; height: 15px; margin-right: 5px;"></div>
                                            <span>Absent</span>
                                        </div>
                            
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered mt-5 forPostingTable">
                                                    <thead>
                                                        <tr>
                                                            <th>
                                                                <input type="checkbox" class="form-control" id="checkboxAll">
                                                            </th>
                                                            <th>ACTIONS</th>
                                                            <th>COMPANY</th>
                                                            {{-- <th>DEPARTMENT</th> --}}
                                                            <th>EMPLOYEE ID</th>
                                                            <th>NAME</th>
                                                            <th>DATE LOGS</th>
                                                            <th>SCHEDULE</th>
                                                            <th>TIME IN</th>
                                                            <th>TIME OUT</th>
                                                            <th>ABSENT</th>
                                                            <th>REG HRS (HRS)</th>
                                                            <th>LATE (MIN)</th>
                                                            <th>UNDERTIME(min)</th>
                                                            <th>LV W/ PAY</th>
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
                                                        {{-- @php
                                                            $total_for_posting = 0;
                                                        @endphp
                                                        @foreach ($employees as $employee)
                                                            @foreach ($date_range as $date_r)
                                                                @php
                                                                    $total_reg_hrs = 0;
                                                                    $late = 0;
                                                                    $abs = 0;
                                                                    $undertime = 0;
                                                                    $leave = 0;
                                                                    $overtime = 0;
                                                                    $night_diff = 0;
                                                                    $night_diff_ot = 0;
                                                                    $restday_ot = 0;
                                                                    $restday_ot_ge = 0;
                                                                    $restnd = 0;
                                                                    $restnd_ge = 0;
                                                                    $lh_ot = 0;
                                                                    $lh_ot_ge = 0;
                                                                    $lh_nd = 0;
                                                                    $lh_nd_ge = 0;
                                                                    $sh_ot = 0;
                                                                    $sh_ot_ge = 0;
                                                                    $sh_ot_nd = 0;
                                                                    $sh_ot_nd_ge = 0;
                                                                    $rst_lh_ot= 0;
                                                                    $rst_lh_ot_ge= 0;
                                                                    $rst_lh_ot_nd= 0;
                                                                    $rst_lh_ot_nd_ge= 0;
                                                                    $rst_sh_ot= 0;
                                                                    $rst_sh_ot_ge= 0;
                                                                    $rst_sh_ot_nd= 0;
                                                                    $rst_sh_ot_nd_ge= 0;

                                                                    $rest = "";
                                                                    $ob_in = "";
                                                                    $ob_out = "";
                                                                    $final_time_in = "";
                                                                    $final_time_out = "";
                                                                    $nightdiff_start = "";
                                                                    $nightdiff_end = "";

                                                                    $employee_schedule = employeeSchedule($employee->ScheduleData,$date_r,$employee->schedule_id,$employee->employee_code);
                                                                    $check_if_holiday = checkIfHoliday(date('Y-m-d',strtotime($date_r)),$employee->location);
                                                                
                                                                    $convertedTimein = date('Y-m-d 00:00:00',strtotime($date_r));
                                                                    $convertedTimeout = date('Y-m-d 00:00:00',strtotime($date_r));
                                                                    if($employee_schedule)
                                                                    {
                                                                        if($employee_schedule->time_in_from)
                                                                        {
                                                                            $convertedTimein = date('Y-m-d H:i:s',strtotime('-6 hours',strtotime($date_r." ".$employee_schedule->time_in_from)));
                                                                        }

                                                                        if ($employee_schedule->time_out_to < $employee_schedule->time_in_from)
                                                                        {
                                                                            $convertedTimeout = date('Y-m-d H:i:s', strtotime("+1 day", strtotime($date_r.' '.$employee_schedule->time_out_to.'+6 hours')));
                                                                        }
                                                                        else
                                                                        {
                                                                            $convertedTimeout = date('Y-m-d H:i:s', strtotime($date_r.' '.$employee_schedule->time_out_to.'+8 hours'));
                                                                        }
                                                                    }
                                                                    $time_in = ($employee->attendance_logs)->whereBetween('datetime',[$convertedTimein, $date_r." 23:59:59"])->sortBy('datetime')->first();
                                                                    $time_out = ($employee->attendance_logs)->whereBetween('datetime',[$date_r." 23:59:59", $convertedTimeout])->sortByDesc('datetime')->first();
                                                                    if (empty($time_out))
                                                                    {
                                                                        $time_out = ($employee->attendance_logs)->where('date', $date_r)->sortByDesc('datetime')->first();      
                                                                    }
                                                                    
                                                                    // Schedule
                                                                    if($employee_schedule)
                                                                    {
                                                                        if ($employee_schedule->time_in_from == null)
                                                                        {
                                                                            $rest = "RESTDAY";
                                                                        }
                                                                        else 
                                                                        {
                                                                            $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to);
                                                                            $schedule_out_from = strtotime($date_r." ".$employee_schedule->time_out_from);
                                                                            $schedule_in = strtotime($date_r." ".$employee_schedule->time_in_to);
                                                                            $schedule_in_from = strtotime($date_r." ".$employee_schedule->time_in_frpm);
                                                                            if(($schedule_out) < ($schedule_in))
                                                                            {
                                                                                $schedule_out = strtotime($date_r." ".$employee_schedule->time_out_to)+86400;
                                                                                $schedule_out_from = strtotime($date_r." ".$employee_schedule->time_out_from)+86400;
                                                                            }
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        $rest = "RESTDAY";
                                                                    }

                                                                    // Time in and Time out
                                                                    if ($time_in && $time_out)
                                                                    {
                                                                        $final_time_in = $time_in->datetime;
                                                                        $final_time_out = $time_out->datetime;
                                                                    }

                                                                    // Absent
                                                                    if ($time_in && $time_out)
                                                                    {
                                                                        $abs = 0;
                                                                    }
                                                                    else
                                                                    {
                                                                        $abs = 1;
                                                                    }

                                                                    // Display Restday
                                                                    if (empty($employee_schedule))
                                                                    {
                                                                        $abs = 0;
                                                                    }
                                                                    else 
                                                                    {
                                                                        if ($employee_schedule->time_in_from == null)
                                                                        {
                                                                            $abs = 0;
                                                                        }
                                                                    }

                                                                    // Reg hrs
                                                                    if ($employee_schedule)
                                                                    {
                                                                        if ($time_in && $time_out)
                                                                        {
                                                                            $schedule_in = strtotime($date_r.' '.$employee_schedule->time_in_to);
                                                                            $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to);

                                                                            if ($schedule_in > $schedule_out)
                                                                            {
                                                                                $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to)+86400;
                                                                            }
                                                                            
                                                                            $schedule_hrs = ($schedule_out - $schedule_in) / 3600; // default working hours
                                                                            
                                                                            $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                                                                            if($if_has_ob)
                                                                            {
                                                                                if ($if_has_ob->date_from < $time_in->datetime)
                                                                                {
                                                                                    $final_time_in = $if_has_ob->date_from;
                                                                                }
                                                                                if ($if_has_ob->date_to > $time_out->datetime) 
                                                                                {
                                                                                    $final_time_out = $if_has_ob->date_to;
                                                                                }
                                                                            }

                                                                            $time_start = date('Y-m-d h:i A', strtotime($final_time_in));
                                                                            $time_end = date('Y-m-d h:i A', strtotime($final_time_out));

                                                                            $start_time = strtotime($time_start);
                                                                            $end_time = strtotime($time_end);

                                                                            if (strtotime($date_r." ".$employee_schedule->time_in_from) > $start_time)
                                                                            {
                                                                                $start_time = strtotime($date_r." ".$employee_schedule->time_in_from);
                                                                            }
                                                                            if ($end_time > $schedule_out)
                                                                            {
                                                                                $end_time = $schedule_out;
                                                                            }
                                                                            
                                                                            $working_hrs = round((($end_time - $start_time)/3600), 2);
                                                                            if ($schedule_hrs > 8)
                                                                            {
                                                                                $schedule_hrs = $schedule_hrs-1;
                                                                                if ($working_hrs >= ($schedule_hrs/1.5))
                                                                                {
                                                                                    $working_hrs = $working_hrs-1;
                                                                                }
                                                                            }
                                                                            else
                                                                            {
                                                                                $working_hrs = $working_hrs;
                                                                            }
                                                                            
                                                                            if($working_hrs > $schedule_hrs)
                                                                            {
                                                                                $total_reg_hrs = $schedule_hrs;
                                                                            }
                                                                            else
                                                                            {
                                                                                $total_reg_hrs = $working_hrs;
                                                                            }
                                                                        }
                                                                        else
                                                                        {
                                                                            $schedule_in = strtotime($date_r.' '.$employee_schedule->time_in_to);
                                                                            $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to);
                                                                            if ($schedule_in > $schedule_out)
                                                                            {
                                                                                $schedule_out = strtotime($date_r.' '.$employee_schedule->time_out_to)+86400;
                                                                            }
                                                                            
                                                                            $schedule_hrs = ($schedule_out - $schedule_in) / 3600; // default working hours
                                                                            
                                                                            $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                                                                            if($if_has_ob)
                                                                            {
                                                                                $final_time_in = $if_has_ob->date_from;
                                                                                $final_time_out = $if_has_ob->date_to;

                                                                                $start_time = strtotime($final_time_in);
                                                                                $end_time = strtotime($final_time_out);

                                                                                if (strtotime($date_r." ".$employee_schedule->time_in_from) > $start_time)
                                                                                {
                                                                                    $start_time = strtotime($date_r." ".$employee_schedule->time_in_from);
                                                                                }
                                                                                if ($end_time > $schedule_out)
                                                                                {
                                                                                    $end_time = $schedule_out;
                                                                                }
                                                                                
                                                                                $working_hrs = round((($end_time - $start_time)/3600), 2);
                                                                                if ($schedule_hrs > 8)
                                                                                {
                                                                                    $schedule_hrs = $schedule_hrs-1;
                                                                                    if ($working_hrs >= ($schedule_hrs/1.5))
                                                                                    {
                                                                                        $working_hrs = $working_hrs-1;
                                                                                    }
                                                                                }
                                                                                else
                                                                                {
                                                                                    $working_hrs = $working_hrs;
                                                                                }
                                                                                
                                                                                if($working_hrs > $schedule_hrs)
                                                                                {
                                                                                    $total_reg_hrs = $schedule_hrs;
                                                                                }
                                                                                else
                                                                                {
                                                                                    $total_reg_hrs = $working_hrs;
                                                                                }
                                                                            }
                                                                        }
                                                                    }

                                                                    // Late
                                                                    if ($employee_schedule)
                                                                    {
                                                                        if ($employee_schedule->time_in_from == null)
                                                                        {
                                                                            $late = 0;
                                                                        }
                                                                        else 
                                                                        {
                                                                            if ($time_in)
                                                                            {
                                                                                $in = strtotime(date('H:i',strtotime($final_time_in)));
                                                                                $schedule_in = strtotime(date('H:i',$schedule_in));
                                                                                if ($in > $schedule_in)
                                                                                {
                                                                                    $total_late = ($in - $schedule_in) / 60;
                                                                                    $late = $total_late;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        $late = 0;
                                                                    }

                                                                    // Undertime
                                                                    if ($employee_schedule)
                                                                    {
                                                                        if ($time_in)
                                                                        {
                                                                            $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                                                                            if($if_has_ob)
                                                                            {
                                                                                if ($if_has_ob->date_from < $time_in->datetime)
                                                                                {
                                                                                    $final_time_in = $if_has_ob->date_from;
                                                                                }
                                                                                
                                                                                if ($if_has_ob->date_to > $time_out->datetime) 
                                                                                {
                                                                                    $final_time_out = $if_has_ob->date_to;
                                                                                }
                                                                            }

                                                                            $out = date('Y-m-d H:i:s', strtotime($time_out->datetime));
                                                                            $in = date('Y-m-d H:i:s', strtotime($time_in->datetime));
                                                                            
                                                                            $estimated_out = "";
                                                                            if (date('H:i', strtotime($in)) > $employee_schedule['time_in_to'])
                                                                            {
                                                                                $estimated_out = $employee_schedule['time_out_to'];
                                                                            }
                                                                            elseif(date('H:i', strtotime($in)) < $employee_schedule['time_in_from'])
                                                                            {
                                                                                $estimated_out = $employee_schedule['time_out_from'];
                                                                            }
                                                                            else
                                                                            {
                                                                                $hours = intval($employee_schedule['working_hours']);
                                                                                $minutes = ($employee_schedule['working_hours']-$hours)*60;
                                                                                $estimated_out = date('h:i A', strtotime("+".$hours." hours",strtotime($time_in->datetime)));
                                                                                $estimated_out = date('h:i A', strtotime("+".$minutes." minutes",strtotime($estimated_out)));
                                                                            }
                                                                            // dd($estimated_out);
                                                                            $out_timestamp = strtotime($out);
                                                                            $estimated_out_timestamp = strtotime($date_r.' '.$estimated_out);
                                                                            if ($out_timestamp < $estimated_out_timestamp)
                                                                            {
                                                                                $total_undertime = ($estimated_out_timestamp - $out_timestamp) / 60;
                                                                                $undertime = $total_undertime;
                                                                            }
                                                                        }
                                                                    }

                                                                    // Leave w/ pay
                                                                    $check_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);
                                                                    if ($check_leave)
                                                                    {
                                                                        $leave = explode("-", $check_leave);
                                                                        if (str_contains($check_leave,"With-Pay"))
                                                                        {
                                                                            $leave = $leave[1];
                                                                            if ($leave == 0.5)
                                                                            {
                                                                                $abs = $leave;
                                                                            }
                                                                            else
                                                                            {
                                                                                $abs = 0;
                                                                            }
                                                                            $undertime = 0;
                                                                        }
                                                                        else
                                                                        {
                                                                            // $abs = $leave[1];
                                                                            // $leave = 0;
                                                                            if ($leave[1] == 0.5)
                                                                            {
                                                                                $abs=1;
                                                                                $leave_count=(float)$leave[1];
                                                                                $leave=0;
                                                                            }
                                                                            else 
                                                                            {
                                                                                $abs=1;
                                                                                $leave_count=0;
                                                                                $leave=0;
                                                                            }
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        $leave = 0;
                                                                    }
                                                                    // REG OT
                                                                    $emp_has_ot = employeeHasOTDetails($employee->approved_ots,date('Y-m-d',strtotime($date_r)));
                                                                    if ($rest == "RESTDAY")
                                                                    {
                                                                        $overtime = 0;
                                                                    }
                                                                    else
                                                                    {
                                                                        if (empty($check_if_holiday))
                                                                        {
                                                                            if ($emp_has_ot)
                                                                            {
                                                                                if ($emp_has_ot < 8)
                                                                                {
                                                                                    $original_sched = $employee_schedule['working_hours'];
                                                                                    $work_ot = round(((strtotime($final_time_out) - strtotime($final_time_in)) / 3600), 2)-$original_sched;
                                                                                    if ($work_ot >= 2 && $emp_has_ot >= 2)
                                                                                    {
                                                                                        if ($work_ot <= $emp_has_ot)
                                                                                        {
                                                                                            $overtime = $work_ot;
                                                                                        }
                                                                                        else 
                                                                                        {
                                                                                            $overtime = $emp_has_ot;
                                                                                        }
                                                                                    }
                                                                                    else 
                                                                                    {
                                                                                        if (in_array($employee->company_id, $plant_company))
                                                                                        {
                                                                                            if ($work_ot <= $emp_has_ot)
                                                                                            {
                                                                                                $overtime = $work_ot;
                                                                                            }
                                                                                            else 
                                                                                            {
                                                                                                $overtime = $emp_has_ot;
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                                else
                                                                                {
                                                                                    $overtime = floatval($emp_has_ot) - 1;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                    
                                                                    // OB
                                                                    $if_has_ob = employeeHasOBDetails($employee->approved_obs, $date_r);
                                                                    if($if_has_ob)
                                                                    {
                                                                        if ($time_in && $time_out)
                                                                        {
                                                                            if ($if_has_ob->date_from < $time_in->datetime)
                                                                            {
                                                                                $ob_in = $if_has_ob->date_from;
                                                                                $final_time_in = $ob_in;
                                                                            }
                                                                            if ($if_has_ob->date_to > $time_out->datetime) 
                                                                            {
                                                                                $ob_out = $if_has_ob->date_to;
                                                                                $final_time_out = $ob_out;
                                                                            }
                                                                        }
                                                                        else
                                                                        {
                                                                            $ob_in = $if_has_ob->date_from;
                                                                            $final_time_in = $ob_in;

                                                                            $ob_out = $if_has_ob->date_to;
                                                                            $final_time_out = $ob_out;
                                                                        }

                                                                        $undertime = 0;
                                                                        $abs = 0;
                                                                    }
                                                                    
                                                                    // ND
                                                                    $nightdiff_start = $final_time_in;
                                                                    $nightdiff_end = $final_time_out;
                                                                    if($employee_schedule)
                                                                    {
                                                                        if (empty($check_if_holiday))
                                                                        {
                                                                            $start_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_in_to);
                                                                            $end_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_out_to);
                                                                            
                                                                            if(strtotime($start_schedule) > strtotime($end_schedule))
                                                                            {
                                                                                $s = date('Y-m-d', strtotime($final_time_in . ' +1 day'));
                                                                                $end_schedule = date('Y-m-d H:i', strtotime($s." ".$employee_schedule->time_out_to));
                                                                            }
                                                                            
                                                                            if(strtotime($start_schedule) > strtotime($final_time_in))
                                                                            {   
                                                                                $nightdiff_start = $start_schedule;
                                                                            }
                                                                            if(strtotime($end_schedule) < strtotime($final_time_out))
                                                                            {   
                                                                                $nightdiff_end = $end_schedule;
                                                                            }
                                                                            
                                                                            $night_diff = night_difference_per_company($nightdiff_start,$nightdiff_end);
                                                                            $schedule_hours = (strtotime($end_schedule)-strtotime($start_schedule))/3600;
                                                                            if($schedule_hours > 8)
                                                                            {
                                                                                if($night_diff >= 5)
                                                                                {
                                                                                    $night_diff = $night_diff - 1;
                                                                                }
                                                                            }
    
                                                                            // REG OT ND
                                                                            if(empty($check_if_holiday))
                                                                            {
                                                                                if($night_diff < 7)
                                                                                {
                                                                                    $actual_night_diff = night_difference_per_company($nightdiff_start,$nightdiff_end);
                                                                                    $night_diff_ot = night_difference_per_company($final_time_in,$final_time_out)-$actual_night_diff;
                                                                                }
                                                                            }

                                                                            if ($night_diff_ot < .5)
                                                                            {
                                                                                $night_diff_ot = 0;
                                                                            }
                                                                        }
                                                                    }

                                                                    // RST OT
                                                                    if ($rest == "RESTDAY")
                                                                    {
                                                                        if (empty($check_if_holiday))
                                                                        {
                                                                            if ($emp_has_ot)
                                                                            {
                                                                                $work_ot = round(((strtotime($final_time_out) - strtotime($final_time_in)) / 3600), 2);
                                                                                $break_hrs = ($employee->approved_ots)->first();
                                                                                if ($break_hrs)
                                                                                {
                                                                                    $work_ot = $work_ot-$break_hrs->break_hrs;
                                                                                }
                                                                                if ($work_ot >= 2)
                                                                                {
                                                                                    if ($work_ot > $emp_has_ot)
                                                                                    {
                                                                                        $restday_ot = 8;
                                                                                        if ($emp_has_ot >= 8)
                                                                                        {
                                                                                            $restday_ot = $restday_ot;
                                                                                            $restday_ot_ge = floatval($emp_has_ot)-floatval($restday_ot);
                                                                                        }
                                                                                        else 
                                                                                        {
                                                                                            $restday_ot = $emp_has_ot;
                                                                                        }
                                                                                    }
                                                                                    else 
                                                                                    {
                                                                                        if ($work_ot > 8)
                                                                                        {
                                                                                            $restday_ot = $restday_ot;
                                                                                            $restday_ot_ge = floatval($work_ot)-floatval($restday_ot);
                                                                                        }
                                                                                        else 
                                                                                        {
                                                                                            $restday_ot = $work_ot;
                                                                                        }
                                                                                    }
                                                                                }
                                                                                else 
                                                                                {
                                                                                    if (in_array($employee->company_id, $plant_company))
                                                                                    {
                                                                                        if ($work_ot <= $emp_has_ot)
                                                                                        {
                                                                                            $overtime = $work_ot;
                                                                                        }
                                                                                        else 
                                                                                        {
                                                                                            $overtime = $emp_has_ot;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }

                                                                    // RST ND
                                                                    if ($rest == "RESTDAY")
                                                                    {
                                                                        if (empty($rest))
                                                                        {
                                                                            if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                                                                            {
                                                                                $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                                                                                $restnd =  night_difference_per_company($final_time_in,$final_time_out);
                                                                                if($work_rest > 9 )
                                                                                { 
                                                                                    $restnd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                                                                    $restnd_ge = night_difference_per_company($final_time_in,$final_time_out);
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
                                                                        }
                                                                    }

                                                                    // Holiday OT's
                                                                    // if ($employee_schedule)
                                                                    // {
                                                                    $if_attendance_holiday_status = '';
                                                                    $check_if_holiday = checkIfHoliday(date('Y-m-d',strtotime($date_r)),$employee->location);
                                                                    if ($check_if_holiday)
                                                                    {
                                                                        $abs = 0;
                                                                        $undertime=0;
                                                                        if ($employee_schedule)
                                                                        {
                                                                            $if_attendance_holiday = checkHasAttendanceHoliday(date('Y-m-d',strtotime($date_r)), $employee->employee_number,$employee->location);
                                                                            $check_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r.'-1 day')),$employee_schedule);
                                                                            if ($check_leave)
                                                                            {
                                                                                // $if_attendance_holiday_status = 'With-Pay';
                                                                                if(str_contains($check_leave,"Without")){
                                                                                    $if_attendance_holiday_status = 'Without-Pay';
                                                                                    $abs = 1;
                                                                                    
                                                                                    $time_in = ($employee->attendance_logs)->sortBy('datetime')->first();
                                                                                    $time_out = ($employee->attendance_logs)->sortByDesc('datetime')->first();
                                                                                    $total_reg_hrs = number_format((strtotime($time_out->datetime) - strtotime($time_in->datetime))/3600, 2);
                                                                                    $emp_schedule = $employee_schedule->working_hours-1;
                                                                                    if ($total_reg_hrs >= ($emp_schedule/2))
                                                                                    {
                                                                                        $abs=0;
                                                                                        if ($employee_schedule->working_hours > 8) 
                                                                                        {
                                                                                            $total_reg_hrs = $employee_schedule->working_hours-1;
                                                                                        }
                                                                                        else 
                                                                                        {
                                                                                            $total_reg_hrs = $employee_schedule->working_hours;
                                                                                        }
                                                                                    }
                                                                                }
                                                                                else
                                                                                {
                                                                                    $if_attendance_holiday_status = 'With-Pay';
                                                                                    if(str_contains($check_leave,".5") || str_contains($check_leave,"1"))
                                                                                    {
                                                                                        $abs = 0;

                                                                                        if ($employee_schedule->working_hours > 8) 
                                                                                        {
                                                                                            $total_reg_hrs = $employee_schedule->working_hours-1;
                                                                                        }
                                                                                        else 
                                                                                        {
                                                                                            $total_reg_hrs = $employee_schedule->working_hours;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                            else
                                                                            {
                                                                                $attendance = ($employee->attendance_logs)->map(function($item) {
                                                                                    return [
                                                                                        'time_in' => $item->datetime
                                                                                    ];
                                                                                });
                                                                                
                                                                                $check_attendance = checkHasAttendanceHolidayStatus($attendance,$if_attendance_holiday);
                                                                                if(empty($check_attendance))
                                                                                {
                                                                                    // $is_absent = 'Absent';
                                                                                    $abs = 1;
                                                                                }else{
                                                                                    // $if_attendance_holiday_status = 'With-Pay';
                                                                                    // $abs = 0;

                                                                                    // if ($employee_schedule->working_hours > 8) 
                                                                                    // {
                                                                                    //     $total_reg_hrs = $employee_schedule->working_hours-1;
                                                                                    // }
                                                                                    // else 
                                                                                    // {
                                                                                    //     $total_reg_hrs = $employee_schedule->working_hours;
                                                                                    // }
                                                                                    $emp_schedule = $employee_schedule->working_hours-1;
                                                                                    $time_in = ($employee->attendance_logs)->where('date', (date('Y-m-d', strtotime($check_attendance))))->sortBy('datetime')->first();
                                                                                    $time_out = ($employee->attendance_logs)->where('date', (date('Y-m-d', strtotime($check_attendance))))->sortByDesc('datetime')->first();
                                                                                    $total_reg_hrs = number_format((strtotime($time_out->datetime) - strtotime($time_in->datetime))/3600, 2);
                                                                                    if ($total_reg_hrs >= ($emp_schedule/2))
                                                                                    {
                                                                                        $abs=0;
                                                                                        if ($employee_schedule->working_hours > 8) 
                                                                                        {
                                                                                            $total_reg_hrs = $employee_schedule->working_hours-1;
                                                                                        }
                                                                                        else 
                                                                                        {
                                                                                            $total_reg_hrs = $employee_schedule->working_hours;
                                                                                        }
                                                                                    }
                                                                                    else 
                                                                                    {
                                                                                        $abs=1;
                                                                                        $total_reg_hrs=0;
                                                                                    }
                                                                                }
                                                                            }
                                                                        }

                                                                        // $abs = 0;
                                                                        $approved_ot_hrs = employeeHasOTDetails($employee->approved_ots,date('Y-m-d',strtotime($date_r)));
                                                                        // SH OT
                                                                        if ($check_if_holiday == "Special Holiday")
                                                                        {
                                                                            if ($rest == "RESTDAY")
                                                                            {
                                                                                $rst_sh_ot = 8;
                                                                                if ($approved_ot_hrs > 8)
                                                                                {
                                                                                    $rst_sh_ot = $rst_sh_ot;
                                                                                    $rst_sh_ot_ge = floatval($approved_ot_hrs) - 8;
                                                                                }
                                                                                else
                                                                                {
                                                                                    $rst_sh_ot = $approved_ot_hrs;
                                                                                }
                                                                            }
                                                                            else 
                                                                            {
                                                                                $sh_ot = 8;
                                                                                if ($approved_ot_hrs > 8)
                                                                                {
                                                                                    $sh_ot = $sh_ot;
                                                                                    $sh_ot_ge = floatval($approved_ot_hrs) - 8;
                                                                                }
                                                                                else
                                                                                {
                                                                                    $sh_ot = $approved_ot_hrs;
                                                                                }
                                                                            }

                                                                            if ($employee_schedule)
                                                                            {
                                                                                $start_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_in_to);
                                                                                $end_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_out_to);
                                                                                
                                                                                if(strtotime($start_schedule) > strtotime($end_schedule))
                                                                                {
                                                                                    $s = date('Y-m-d', strtotime($final_time_in . ' +1 day'));
                                                                                    $end_schedule = date('Y-m-d H:i', strtotime($s." ".$employee_schedule->time_out_to));
                                                                                }

                                                                                if(strtotime($start_schedule) > strtotime($final_time_in))
                                                                                {   
                                                                                    $nightdiff_start = $start_schedule;
                                                                                }
                                                                                if(strtotime($end_schedule) < strtotime($final_time_out))
                                                                                {   
                                                                                    $nightdiff_end = $end_schedule;
                                                                                }
                                                                            }
                                                                            
                                                                            
                                                                            if ($rest == "RESTDAY")
                                                                            {
                                                                                $rst_sh_nd = night_difference_per_company($nightdiff_start,$nightdiff_end);
                                                                                // $schedule_hours = (strtotime($end_schedule)-strtotime($start_schedule))/3600;
                                                                                
                                                                                if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                                                                                {
                                                                                    $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                                                                                    $rst_sh_ot_nd_ge =  night_difference_per_company($final_time_in,$final_time_out);
                                                                                    if($work_rest > 9 )
                                                                                    { 
                                                                                        $rst_sh_ot_nd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                                                                        $rst_sh_ot_nd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                                                                        $rst_sh_ot_nd = $rst_sh_ot_nd_ge - $rst_sh_ot_nd;
                                                                                        $rst_sh_ot_nd = $rst_sh_ot_nd-1;
                                                                                        if($rst_sh_ot_nd <0)
                                                                                        {
                                                                                            $rst_sh_ot_nd = 0;
                                                                                        }
                                                                                        if($rst_sh_ot_nd_ge <0)
                                                                                        {
                                                                                            $rst_sh_ot_nd_ge = 0;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                            else 
                                                                            {
                                                                                if ($employee_schedule)
                                                                                {
                                                                                    $sh_nd = night_difference_per_company($nightdiff_start,$nightdiff_end);
                                                                                    $schedule_hours = (strtotime($end_schedule)-strtotime($start_schedule))/3600;
                                                                                    if($schedule_hours > 8)
                                                                                    {
                                                                                        if($sh_nd >= 5)
                                                                                        {
                                                                                            $sh_nd = floatval($sh_nd)-1;
                                                                                        }
    
                                                                                        if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                                                                                        {
                                                                                            $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                                                                                            $sh_ot_nd_ge =  night_difference_per_company($final_time_in,$final_time_out);
                                                                                            if($work_rest > 9 )
                                                                                            { 
                                                                                                $sh_ot_nd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                                                                                $sh_ot_nd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                                                                                $sh_ot_nd = $sh_ot_nd_ge - $sh_ot_nd;
                                                                                                $sh_ot_nd = $sh_ot_nd-1;
                                                                                                if($sh_ot_nd <0)
                                                                                                {
                                                                                                    $sh_ot_nd = 0;
                                                                                                }
                                                                                                if($sh_ot_nd_ge <0)
                                                                                                {
                                                                                                    $sh_ot_nd_ge = 0;
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                        else
                                                                        {
                                                                            if ($rest == "RESTDAY")
                                                                            {
                                                                                $rst_lh_ot = 8;
                                                                                if ($approved_ot_hrs > 8)
                                                                                {
                                                                                    $rst_lh_ot = $rst_lh_ot;
                                                                                    $lh_ot_ge = floatval($approved_ot_hrs) - 8;
                                                                                }
                                                                                else
                                                                                {
                                                                                    $rst_lh_ot = $approved_ot_hrs;
                                                                                }
                                                                            }
                                                                            else 
                                                                            {
                                                                                $lh_ot = 8;
                                                                                if ($approved_ot_hrs > 8)
                                                                                {
                                                                                    $lh_ot = $lh_ot;
                                                                                    $lh_ot_ge = floatval($approved_ot_hrs) - 8;
                                                                                }
                                                                                else
                                                                                {
                                                                                    $lh_ot = $approved_ot_hrs;
                                                                                }
                                                                            }
                                                                            
                                                                            if ($employee_schedule)
                                                                            {
                                                                                $start_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_in_to);
                                                                                $end_schedule = (date('Y-m-d',strtotime($final_time_in))." ".$employee_schedule->time_out_to);
                                                                                
                                                                                if(strtotime($start_schedule) > strtotime($end_schedule))
                                                                                {
                                                                                    $s = date('Y-m-d', strtotime($final_time_in . ' +1 day'));
                                                                                    $end_schedule = date('Y-m-d H:i', strtotime($s." ".$employee_schedule->time_out_to));
                                                                                }
                                                                                
                                                                                if(strtotime($start_schedule) > strtotime($final_time_in))
                                                                                {   
                                                                                    $nightdiff_start = $start_schedule;
                                                                                }
                                                                                if(strtotime($end_schedule) < strtotime($final_time_out))
                                                                                {   
                                                                                    $nightdiff_end = $end_schedule;
                                                                                }
                                                                            }
                                                                            
                                                                            if ($rest == "RESTDAY")
                                                                            {
                                                                                if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                                                                                {
                                                                                    $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                                                                                    $lh_nd_ge =  night_difference_per_company($final_time_in,$final_time_out);
                                                                                    if($work_rest > 9 )
                                                                                    { 
                                                                                        $rst_lh_nd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                                                                        $rst_lh_nd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                                                                        $rst_lh_nd = $rst_lh_nd_ge - $rst_lh_nd;
                                                                                        $rst_lh_nd = $rst_lh_nd-1;
                                                                                        if($rst_lh_nd <0)
                                                                                        {
                                                                                            $rst_lh_nd = 0;
                                                                                        }
                                                                                        if($rst_lh_nd_ge <0)
                                                                                        {
                                                                                            $rst_lh_nd_ge = 0;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                            else 
                                                                            {
                                                                                if(($final_time_in) && ($final_time_out) && ($emp_has_ot >0))
                                                                                {
                                                                                    $work_rest =  round(((strtotime($final_time_out) - strtotime($final_time_in))/3600), 2);
                                                                                    $lh_nd_ge =  night_difference_per_company($final_time_in,$final_time_out);
                                                                                    if($work_rest > 9 )
                                                                                    { 
                                                                                        $lh_nd =  round(night_difference_per_company($final_time_in,date("Y-m-d H:i:s", strtotime('+9 hours',strtotime($final_time_in)))));
                                                                                        $lh_nd_ge = night_difference_per_company($final_time_in,$final_time_out);
                                                                                        $lh_nd = $lh_nd_ge - $lh_nd;
                                                                                        $lh_nd = $lh_nd-1;
                                                                                        if($lh_nd <0)
                                                                                        {
                                                                                            $lh_nd = 0;
                                                                                        }
                                                                                        if($lh_nd_ge <0)
                                                                                        {
                                                                                            $lh_nd_ge = 0;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                    // }

                                                                    if ($total_reg_hrs <= 0)
                                                                    {
                                                                        $total_reg_hrs = 0;
                                                                    }
                                                                @endphp
                                                                
                                                                @php
                                                                    // $approved_dtr = count(($employee->dtr_correction)->where('date',$date_r)->where('status','Approved'));
                                                                    $pending_dtr = count(($employee->dtr_correction)->where('date',$date_r)->where('status','Pending'));
                                                                    $cancelled_dtr = ($employee->dtr_correction)->where('date',$date_r)->where('status','Cancelled')->last();
                                                                    $revert = count(($employee->dtr_status)->where('date',$date_r)->where('status','Revert'));
                                                                    $for_posting = count(($employee->dtr_status)->where('date',$date_r)->where('status','For posting'));
                                                                    $posted_dtr = count(($employee->attendance_detailed_report)->where('log_date', $date_r));
                                                                    // dd($approved_dtr, $revert,$for_posting,$posted_dtr);
                                                                @endphp

                                                                @if(($abs == 0) && ($overtime == 0) && ($revert == 0) && ($posted_dtr == 0) && ($pending_dtr == 0) && ($total_reg_hrs > 3 || $rest=="RESTDAY" || ($leave >= 0)) && (!$if_has_ob) || (($for_posting > 0)))
                                                                    @php
                                                                        $total_for_posting = $total_for_posting+=1;
                                                                    @endphp

                                                                    <tr>
                                                                        <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][cutoff]" value="{{$to_date}}">
                                                                        <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][log_date]" value="{{ $date_r }}">
                                                                        <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][department_id]" value="{{ $employee->department_id }}">
                                                                        <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][shift]" value="{{$employee_schedule && $employee_schedule->time_in_to != null ? date('h:i A', strtotime($employee_schedule->time_in_to)) . '-' . date('h:i A', strtotime($employee_schedule->time_out_to)) : 'RESTDAY'}}">

                                                                        
                                                                        <td>
                                                                            @if($department_data)
                                                                            <input type="checkbox" name="employees[{{ $employee->employee_code }}][{{$date_r}}][selected]" class="selectEmployee form-control">
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-danger" onclick="revertFunction('{{ $employee->id }}', '{{ $date_r }}')">
                                                                                <i class="ti-back-left"></i>
                                                                                Revert
                                                                            </button>
                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][company_id]" value="{{ $employee->company_id }}">
                                                                            {{ $employee->company->company_code }}
                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][department]" value="{{ $employee->department->name }}">
                                                                            {{ $employee->department->name }}
                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][employee_no]" value="{{ $employee->employee_code }}">
                                                                            {{ $employee->employee_code }}
                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][name]" value="{{ $employee->last_name .', '.$employee->first_name }}">
                                                                            {{ $employee->last_name.', '.$employee->first_name }}
                                                                        </td>
                                                                        <td>{{ $date_r }}</td>
                                                                        <td>
                                                                            @if($employee_schedule != null)
                                                                                @if($employee_schedule->time_in_from)
                                                                                    <small>{{date('h:i A', strtotime($employee_schedule->time_in_to)).'-'.date('h:i A', strtotime($employee_schedule->time_out_to))}}</small>
                                                                                    @if ($employee_schedule->time_in_from != $employee_schedule->time_in_to)
                                                                                        <small>(Flexi)</small>
                                                                                    @endif
                                                                                @else
                                                                                    @php
                                                                                        $rest = "RESTDAY";
                                                                                    @endphp
                                                                                    <small>{{ $rest }}</small>
                                                                                @endif
                                                                            @else
                                                                                @php
                                                                                    $rest = "RESTDAY";
                                                                                @endphp
                                                                                <small>{{ $rest }}</small>
                                                                            @endif
                                                                        </td>
                                                                        <td @if(empty($final_time_in) && $rest == "" && $leave == 0 && $abs > 0) class="bg-danger" @endif @if($if_has_ob) class="bg-info" @endif>
                                                                            @if($final_time_in)
                                                                                {{ date('h:i A', strtotime($final_time_in)) }}
                                                                                <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][in]" value="{{ date('h:i A', strtotime($final_time_in)) }}">
                                                                            @else
                                                                                <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][in]" value="0.00">
                                                                            @endif
                                                                        </td>
                                                                        <td  @if(empty($final_time_out) && $rest == "" && $leave == 0 && $abs > 0) class="bg-danger" @endif @if($if_has_ob) class="bg-info" @endif>
                                                                            @if($final_time_out)
                                                                                {{ date('h:i A', strtotime($final_time_out)) }}
                                                                                <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][out]" value="{{ date('h:i A', strtotime($final_time_out)) }}">
                                                                            @else 
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][out]" value="0.00">
                                                                            @endif
                                                                        </td>
                                                                        <td @if($abs-$leave > 0) class="bg-danger" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][abs]" value="{{ $abs }}">
                                                                            {{ number_format($abs, 2) }}
                                                                        </td>
                                                                        <td>
                                                                            {{ $total_reg_hrs }}
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][reg_hrs]" value="{{ $total_reg_hrs }}">
                                                                        </td>
                                                                        <td @if($late > 0) class="bg-danger" @endif>
                                                                            {{ number_format($late,0) }}

                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][late_min]" value="{{ number_format($abs,2) }}">
                                                                        </td>
                                                                        <td @if($undertime > 0) class="bg-danger" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][undertime_min]" value="{{ number_format($undertime,2) }}">
                                                                            {{ number_format($undertime,2) }}
                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][lv_w_pay]" value="{{ number_format($leave,2) }}">
                                                                            {{ number_format($leave,2) }}
                                                                        </td>
                                                                        <td @if($overtime > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][reg_ot]" value="{{ number_format($overtime,2) }}">
                                                                            {{ number_format($overtime,2) }}
                                                                        </td>
                                                                        <td @if($night_diff > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][reg_nd]" value="{{ number_format($night_diff,2) }}">
                                                                            {{ number_format($night_diff,2) }}
                                                                        </td>
                                                                        <td @if($night_diff_ot > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][reg_ot_nd]" value="{{ number_format($night_diff_ot,2) }}">
                                                                            {{ number_format($night_diff_ot,2) }}
                                                                        </td>
                                                                        <td @if($restday_ot > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][rst_ot]" value="{{ number_format($restday_ot,2) }}">
                                                                            {{ number_format($restday_ot,2) }}
                                                                        </td>
                                                                        <td @if($restday_ot_ge > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][rst_ot_over_eight]" value="{{ number_format($restday_ot_ge,2) }}">
                                                                            {{ number_format($restday_ot_ge,2) }}
                                                                        </td>
                                                                        <td @if($restnd > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][rst_nd]" value="{{ number_format($restnd,2) }}">
                                                                            {{ number_format($restnd, 2) }}
                                                                        </td>
                                                                        <td @if($restnd_ge > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][rst_nd_over_eight]" value="{{ number_format($restnd_ge,2) }}">
                                                                            {{ number_format($restnd_ge, 2) }}
                                                                        </td>
                                                                        <td @if($lh_ot > 0) class="bg-warning" @endif> 
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][lh_ot]" value="{{ number_format($lh_ot,2) }}">
                                                                            {{ number_format($lh_ot,2) }}
                                                                        </td>
                                                                        <td  @if($lh_ot_ge > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][lh_ot_over_eight]" value="{{ number_format($lh_ot_ge,2) }}">
                                                                            {{ number_format($lh_ot_ge,2) }}
                                                                        </td>
                                                                        <td @if($lh_nd > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][lh_nd]" value="{{ number_format($lh_nd,2) }}">
                                                                            {{ number_format($lh_nd,2) }}
                                                                        </td>
                                                                        <td @if($lh_nd_ge > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][lh_nd_over_eight]" value="{{ number_format($lh_nd_ge,2) }}">
                                                                            {{ number_format($lh_nd_ge,2) }}
                                                                        </td>
                                                                        <td @if($sh_ot > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][sh_ot]" value="{{ number_format($sh_ot,2) }}">
                                                                            {{ number_format($sh_ot,2) }}
                                                                        </td>
                                                                        <td @if($sh_ot_ge > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][sh_ot_over_eight]" value="{{ number_format($sh_ot_ge,2) }}">
                                                                            {{ number_format($sh_ot_ge,2) }}
                                                                        </td>
                                                                        <td @if($sh_ot_nd > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][sh_nd]" value="{{ number_format($sh_ot_nd,2) }}">
                                                                            {{ number_format($sh_ot_nd,2) }}
                                                                        </td>
                                                                        <td @if($sh_ot_nd_ge > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][sh_nd_over_eight]" value="{{ number_format($sh_ot_nd_ge,2) }}">
                                                                            {{ number_format($sh_ot_nd_ge, 2) }}
                                                                        </td>
                                                                        <td @if($rst_lh_ot > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][rst_lh_ot]" value="{{ number_format($rst_lh_ot,2) }}">
                                                                            {{ number_format($rst_lh_ot,2) }}
                                                                        </td>
                                                                        <td @if($rst_lh_ot_ge > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][rst_lh_ot_over_eight]" value="{{ number_format($rst_lh_ot_ge,2) }}">
                                                                            {{ number_format($rst_lh_ot_ge,2) }}
                                                                        </td>
                                                                        <td @if($rst_lh_ot_nd > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][rst_lh_nd]" value="{{ number_format($rst_lh_ot_nd,2) }}">
                                                                            {{ number_format($rst_lh_ot_nd,2) }}
                                                                        </td>
                                                                        <td @if($rst_lh_ot_nd_ge > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][rst_lh_nd_over_eight]" value="{{ number_format($rst_lh_ot_nd_ge,2) }}">
                                                                            {{ number_format($rst_lh_ot_nd_ge,2) }}
                                                                        </td>
                                                                        <td @if($rst_sh_ot > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][rst_sh_ot]" value="{{ number_format($rst_sh_ot,2) }}">
                                                                            {{ number_format($rst_sh_ot,2) }}
                                                                        </td>
                                                                        <td @if($rst_sh_ot_ge > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][rst_sh_ot_over_eight]" value="{{ number_format($rst_sh_ot_ge,2) }}">
                                                                            {{ number_format($rst_sh_ot_ge, 2) }}
                                                                        </td>
                                                                        <td @if($rst_sh_ot_nd > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][rst_sh_nd]" value="{{ number_format($rst_sh_ot_nd,2) }}">
                                                                            {{ number_format($rst_sh_ot_nd,2) }}
                                                                        </td>
                                                                        <td @if($rst_sh_ot_nd_ge > 0) class="bg-warning" @endif>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][rst_sh_nd_over_eight]" value="{{ number_format($rst_sh_ot_nd_ge,2) }}">
                                                                            {{ number_format($rst_sh_ot_nd_ge,2) }}
                                                                        </td>
                                                                        <td>
                                                                            @php
                                                                                $leave_count = 0;
                                                                                $abs_half = 0;

                                                                                $if_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);
                                                                                if($if_leave)
                                                                                {
                                                                                    $l = explode('-',$if_leave);
                                                                                    $leave_count = (double) $l[1];
                                                                                    if(str_contains($if_leave,"Without"))

                                                                                    {
                                                                                        $leave_count = 0;
                                                                                        $abs_half = $l[1];
                                                                                    }
                                                                                }
                                                                            @endphp
                                                                            {{$if_has_ob ? 'OB' : ''}}
                                                                            {{ $if_leave }}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        @endforeach --}}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('timekeeping.edit_timekeeping')
{{-- @foreach ($employees as $employee)
@foreach ($date_range as $date_r)
@endforeach
@endforeach --}}

{{-- // @php
// var total_issues = "<?php echo($total_issues) ?>"
// var total_for_posting = "<?php echo($total_for_posting) ?>"
// var total_pending_approval = "<?php echo($total_pending_approval) ?>"

// document.getElementById('totalIssues').innerText = total_issues
// document.getElementById('totalForPosting').innerText = total_for_posting
// document.getElementById('totalPendingApproval').innerText = total_pending_approval
// @endphp --}}
@endsection

@section('js')
{{-- <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script> --}}
<script>
    function revertFunction(employeeId, date)
    {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, revert it!"
        }).then((result) => {
            if (result.isConfirmed) {
                // document.getElementById('revertForm'+employeeId).submit()
                $.ajax({
                    type: "POST",
                    url: "{{ url('timekeeping-official/dtrStatus') }}",
                    data: {
                        employee: employeeId,
                        date: date,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function(){
                        show()
                    },
                    success: function() {
                        Swal.fire({
                            title: "Successfully Revert",
                            icon: "success"
                        });

                        setTimeout(() => {
                            location.reload()
                        },200)
                    }
                })
            }
        });
    }

    $(document).ready(function() {
        var issueTable = $('.issuesTable').DataTable({
            pagelength:10,
            dom: 'Bfrtip',
            paginate:true,
            processing: true,
            serverSide: true,
            lengthChange: true,
            ordering: true,
            info: true,
            autoWidth: false,
            stateSave:true,
            ajax: {
                type: "POST",
                url: "{{ url('timekeeping-official/issues_per_company') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function(d) {
                    d.company = $("[name='company']").val()
                    d.date_from = $("[name='date_from']").val()
                    d.date_to = $("[name='date_to']").val()
                }
            },
            columns: [
                { data: 'action', name: 'action' },
                { data: 'company', name: 'company' },
                { data: 'employee_code', name: 'employee_code' },
                { data: 'name', name: 'name' },
                { data: 'date', name: 'date' },
                {
                    render: function(data, type, row) {
                        return `<small>${row.schedule}</small>`
                    }
                },
                {data:'time_in'},
                {data:'time_out'},
                {data:'abs'},
                {data:"reg_hrs"},
                {data:"late"},
                {data:"undertime"},
                {data:"leave"},
                {data:"overtime"},
                {data:"reg_nd"},
                {data:"reg_ot_nd"},
                {data:"restday_ot"},
                {data:"restday_ot_ge"},
                {data:"restnd"},
                {data:"restnd_ge"},
                {data:"lh_ot"},
                {data:"lh_ot_ge"},
                {data:"lh_nd"},
                {data:"lh_nd_ge"},
                {data:"sh_ot"},
                {data:"sh_ot_ge"},
                {data:"sh_ot_nd"},
                {data:"sh_ot_nd_ge"},
                {data:"rst_lh_ot"},
                {data:"rst_lh_ot_ge"},
                {data:"rst_lh_ot_nd"},
                {data:"rst_lh_ot_nd_ge"},
                {data:"rst_sh_ot"},
                {data:"rst_sh_ot_ge"},
                {data:"rst_sh_ot_nd"},
                {data:"rst_sh_ot_nd_ge"},
                {data:"remarks"},
            ],
            language: {
                processing: `
                    <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
                        <span class="visually-hidden"></span>
                    </div>
                    <div style="font-size:18px;margin-top:10px;">Loading data...</div>
                `
            },
            createdRow: function(row, data, dataIndex) {
                if (data.if_has_ob == "Yes") {
                    $(row).find('td:eq(6)').addClass('bg-info');
                }
                else if(data.time_in == "" && data.schedule != "RESTDAY") {
                    $(row).find('td:eq(6)').addClass('bg-danger');
                }

                if (data.if_has_ob == "Yes") {
                    $(row).find('td:eq(7)').addClass('bg-info');
                }
                else if(data.time_out == "" && data.schedule != "RESTDAY") {
                    $(row).find('td:eq(7)').addClass('bg-danger');
                }
                if (parseFloat(data.abs)-parseFloat(data.leave_count) > 0) {
                    $(row).find('td:eq(8)').addClass('bg-danger');
                }
                if (data.late > 0) {
                    $(row).find('td:eq(10)').addClass('bg-danger');
                }
                if (data.undertime > 0) {
                    $(row).find('td:eq(11)').addClass('bg-danger');
                }
                if (data.overtime > 0) {
                    $(row).find('td:eq(13)').addClass('bg-warning');
                }
                if (data.reg_nd > 0) {
                    $(row).find('td:eq(14)').addClass('bg-warning');
                }
                if (data.reg_ot_nd > 0) {
                    $(row).find('td:eq(15)').addClass('bg-warning');
                }
                if (data.restday_ot > 0) {
                    $(row).find('td:eq(16)').addClass('bg-warning');
                }
                if (data.restday_ot_ge > 0) {
                    $(row).find('td:eq(17)').addClass('bg-warning');
                }
                if (data.restnd > 0) {
                    $(row).find('td:eq(18)').addClass('bg-warning');
                }
                if (data.restnd_ge > 0) {
                    $(row).find('td:eq(19)').addClass('bg-warning');
                }
                if (data.lh_ot > 0) {
                    $(row).find('td:eq(20)').addClass('bg-warning');
                }
                if (data.lh_ot_ge > 0) {
                    $(row).find('td:eq(21)').addClass('bg-warning');
                }
                if (data.lh_nd > 0) {
                    $(row).find('td:eq(22)').addClass('bg-warning');
                }
                if (data.lh_nd_ge > 0) {
                    $(row).find('td:eq(23)').addClass('bg-warning');
                }
                if (data.sh_ot > 0) {
                    $(row).find('td:eq(24)').addClass('bg-warning');
                }
                if (data.sh_ot_ge > 0) {
                    $(row).find('td:eq(25)').addClass('bg-warning');
                }
                if (data.sh_nd > 0) {
                    $(row).find('td:eq(26)').addClass('bg-warning');
                }
                if (data.sh_nd_ge > 0) {
                    $(row).find('td:eq(27)').addClass('bg-warning');
                }
                if (data.rst_lh_ot > 0) {
                    $(row).find('td:eq(28)').addClass('bg-warning');
                }
                if (data.rst_lh_ot_ge > 0) {
                    $(row).find('td:eq(29)').addClass('bg-warning');
                }
                if (data.rst_lh_ot_nd > 0) {
                    $(row).find('td:eq(30)').addClass('bg-warning');
                }
                if (data.rst_lh_ot_nd_ge > 0) {
                    $(row).find('td:eq(31)').addClass('bg-warning');
                }
                if (data.rst_sh_ot > 0) {
                    $(row).find('td:eq(32)').addClass('bg-warning');
                }
                if (data.rst_sh_ot_ge > 0) {
                    $(row).find('td:eq(33)').addClass('bg-warning');
                }
                if (data.rst_sh_ot_nd > 0) {
                    $(row).find('td:eq(34)').addClass('bg-warning');
                }
                if (data.rst_sh_ot_nd_ge > 0) {
                    $(row).find('td:eq(35)').addClass('bg-warning');
                }
            },
            rowCallback:function(row, data) {
                $(row).find('#editTimekeepingBtn').on('click', function() {
                    $("#editTimekeepingModal").modal('show')

                    setTimeout(() => {
                        $("#employeeCode").val(data.employee_code)
                        $("#employeeName").val(data.name)
                        $("#dateLogs").val(data.date)
    
                        $("[name='employee_id']").val(data.employee_id)
                        $("[name='date']").val(data.date)
                        

                        if (data.time_in != "" && data.time_out != "")
                        {
                            $("#timeIn").val(data.date+ 'T' +to24HourFormat(data.time_in));
                            $("#timeOut").val(data.date+ 'T' +to24HourFormat(data.time_out));
                        }
                        else 
                        {
                            $("#timeIn").val(data.date+ 'T00:00:00');
                            $("#timeOut").val(data.date+ 'T00:00:00');
                        }
                    }, 500)
                }),
                $(row).find("#moveToForPostingBtn").on('click', function() {
                    var employee = $(this).data()
                    
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, move it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // document.getElementById('moveToForPostingForm'+employeeId).submit()

                            $.ajax({
                                type:"POST",
                                url:"{{ url('timekeeping-official/moveToForPosting') }}",
                                data: {
                                    employee_id: employee.employee,
                                    date: employee.date,
                                    _token:"{{ csrf_token() }}"
                                },
                                beforeSend: function() {
                                    show()
                                },
                                success: function(response) {
                                    if (response.status == "success") {
                                        Swal.fire({
                                            title: response.message,
                                            icon: "success"
                                        });

                                        issueTable.ajax.reload()
                                        forPostingTable.ajax.reload()
                                        hide()
                                    }
                                }
                            })
                        }
                    });
                })
            }
        });

        var forPostingTable = $('.forPostingTable').DataTable({
            pagelength:10,
            dom: 'Bfrtip',
            paginate:true,
            processing: true,
            serverSide: true,
            lengthChange: true,
            ordering: true,
            info: true,
            autoWidth: false,
            stateSave:true,
            language: {
                processing: `
                    <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
                        <span class="visually-hidden"></span>
                    </div>
                    <div style="font-size:18px;margin-top:10px;">Loading data...</div>
                `
            },
            ajax: {
                type: "POST",
                url: "{{ url('timekeeping-official/for_posting_per_company') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function(d) {
                    d.company = $("[name='company']").val()
                    d.date_from = $("[name='date_from']").val()
                    d.date_to = $("[name='date_to']").val()
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox' },
                { data: 'action', name: 'action' },
                { data: 'company', name: 'company' },
                { data: 'employee_code', name: 'employee_code' },
                { data: 'name', name: 'name' },
                { data: 'date', name: 'date' },
                {
                    render: function(data, type, row) {
                        return `<small>${row.schedule}</small>`
                    }
                },
                {data:'time_in'},
                {data:'time_out'},
                {data:'abs'},
                {data:"reg_hrs"},
                {data:"late"},
                {data:"undertime"},
                {data:"leave"},
                {data:"overtime"},
                {data:"reg_nd"},
                {data:"reg_ot_nd"},
                {data:"restday_ot"},
                {data:"restday_ot_ge"},
                {data:"restnd"},
                {data:"restnd_ge"},
                {data:"lh_ot"},
                {data:"lh_ot_ge"},
                {data:"lh_nd"},
                {data:"lh_nd_ge"},
                {data:"sh_ot"},
                {data:"sh_ot_ge"},
                {data:"sh_ot_nd"},
                {data:"sh_ot_nd_ge"},
                {data:"rst_lh_ot"},
                {data:"rst_lh_ot_ge"},
                {data:"rst_lh_ot_nd"},
                {data:"rst_lh_ot_nd_ge"},
                {data:"rst_sh_ot"},
                {data:"rst_sh_ot_ge"},
                {data:"rst_sh_ot_nd"},
                {data:"rst_sh_ot_nd_ge"},
                {data:"remarks"},
            ],
            createdRow: function(row, data, dataIndex) {
                if (data.if_has_ob == "Yes") {
                    $(row).find('td:eq(7)').addClass('bg-info');
                }
                else if(data.time_in == "" && data.schedule != "RESTDAY") {
                    $(row).find('td:eq(7)').addClass('bg-danger');
                }

                if (data.if_has_ob == "Yes") {
                    $(row).find('td:eq(8)').addClass('bg-info');
                }
                else if(data.time_out == "" && data.schedule != "RESTDAY") {
                    $(row).find('td:eq(8)').addClass('bg-danger');
                }
                if (parseFloat(data.abs)-parseFloat(data.leave_count) > 0) {
                    $(row).find('td:eq(9)').addClass('bg-danger');
                }
                if (data.late > 0) {
                    $(row).find('td:eq(11)').addClass('bg-danger');
                }
                if (data.undertime > 0) {
                    $(row).find('td:eq(12)').addClass('bg-danger');
                }
                if (data.overtime > 0) {
                    $(row).find('td:eq(14)').addClass('bg-warning');
                }
                if (data.reg_nd > 0) {
                    $(row).find('td:eq(15)').addClass('bg-warning');
                }
                if (data.reg_ot_nd > 0) {
                    $(row).find('td:eq(16)').addClass('bg-warning');
                }
                if (data.restday_ot > 0) {
                    $(row).find('td:eq(17)').addClass('bg-warning');
                }
                if (data.restday_ot_ge > 0) {
                    $(row).find('td:eq(18)').addClass('bg-warning');
                }
                if (data.restnd > 0) {
                    $(row).find('td:eq(19)').addClass('bg-warning');
                }
                if (data.restnd_ge > 0) {
                    $(row).find('td:eq(20)').addClass('bg-warning');
                }
                if (data.lh_ot > 0) {
                    $(row).find('td:eq(21)').addClass('bg-warning');
                }
                if (data.lh_ot_ge > 0) {
                    $(row).find('td:eq(22)').addClass('bg-warning');
                }
                if (data.lh_nd > 0) {
                    $(row).find('td:eq(23)').addClass('bg-warning');
                }
                if (data.lh_nd_ge > 0) {
                    $(row).find('td:eq(24)').addClass('bg-warning');
                }
                if (data.sh_ot > 0) {
                    $(row).find('td:eq(25)').addClass('bg-warning');
                }
                if (data.sh_ot_ge > 0) {
                    $(row).find('td:eq(26)').addClass('bg-warning');
                }
                if (data.sh_nd > 0) {
                    $(row).find('td:eq(27)').addClass('bg-warning');
                }
                if (data.sh_nd_ge > 0) {
                    $(row).find('td:eq(28)').addClass('bg-warning');
                }
                if (data.rst_lh_ot > 0) {
                    $(row).find('td:eq(29)').addClass('bg-warning');
                }
                if (data.rst_lh_ot_ge > 0) {
                    $(row).find('td:eq(30)').addClass('bg-warning');
                }
                if (data.rst_lh_ot_nd > 0) {
                    $(row).find('td:eq(31)').addClass('bg-warning');
                }
                if (data.rst_lh_ot_nd_ge > 0) {
                    $(row).find('td:eq(32)').addClass('bg-warning');
                }
                if (data.rst_sh_ot > 0) {
                    $(row).find('td:eq(33)').addClass('bg-warning');
                }
                if (data.rst_sh_ot_ge > 0) {
                    $(row).find('td:eq(34)').addClass('bg-warning');
                }
                if (data.rst_sh_ot_nd > 0) {
                    $(row).find('td:eq(35)').addClass('bg-warning');
                }
                if (data.rst_sh_ot_nd_ge > 0) {
                    $(row).find('td:eq(36)').addClass('bg-warning');
                }
            },
            rowCallback:function(row, data) {
                $(row).find("#revertBtn").on('click', function() {
                    var employee = $(this).data()
                    
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, revert it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type:"POST",
                                url:"{{ url('timekeeping-official/dtrStatus') }}",
                                data: {
                                    employee: employee.employee,
                                    date: employee.date,
                                    _token:"{{ csrf_token() }}"
                                },
                                beforeSend: function() {
                                    show()
                                },
                                success: function(response) {
                                    if (response.status == "success") {
                                        Swal.fire({
                                            title: response.message,
                                            icon: "success"
                                        });

                                        issueTable.ajax.reload()
                                        forPostingTable.ajax.reload()
                                        hide()
                                    }
                                }
                            })
                        }
                    });
                })
            }
        })

        function to24HourFormat(time12h) {
            // Example input: "02:30 PM" or "02:30 AM"
            const [time, modifier] = time12h.split(' '); // ["02:30", "PM"]
            let [hours, minutes] = time.split(':');

            hours = parseInt(hours, 10);

            if (modifier.toUpperCase() === 'PM' && hours !== 12) {
                hours += 12;
            }
            if (modifier.toUpperCase() === 'AM' && hours === 12) {
                hours = 0;
            }

            // Pad hours with leading zero
            hours = hours.toString().padStart(2, '0');

            return `${hours}:${minutes}`;
        }

        issueTable.on('xhr.dt', function() {
            hide();
        });

        forPostingTable.on('xhr.dt', function() {
            hide();
        });

        $("#filterForm").on('submit', function(e) {
            e.preventDefault()

            show()
            issueTable.ajax.reload()
            forPostingTable.ajax.reload()
        })

        $("#checkboxAll").on('change', function() {
            if ($(this).is(':checked'))
            {
                $(".selectEmployee").prop('checked', true)
            }
            else 
            {
                $(".selectEmployee").prop('checked', false)
            }
        })

        $(".selectEmployee").on('change', function() {
            var ifSelected = $(this).closest('td').find('.hidden-selected')

            if ($(this).is(':checked'))
            {
                $(this).val("selected")
            }
        })

        $("#editTimekeepingForm").on('submit', function(e) {
            e.preventDefault()

            const form = $(this)[0]
            const formData = new FormData(form)

            $.ajax({
                url: "{{ url('timekeeping-official/for-approval') }}",
                method: 'POST',
                data: formData,
                processData: false, // important for FormData
                contentType: false, // important for FormData
                success: function(response) {
                    if (response.status == "success") {
                        Swal.fire({
                            title: response.message,
                            icon: "success"
                        });

                        $('#editTimekeepingModal').modal('hide')
                        issueTable.ajax.reload()
                        forPostingTable.ajax.reload()
                    }
                }
            });
        })
    })
</script>
@endsection