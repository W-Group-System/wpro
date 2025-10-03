@extends('layouts.header')

@section('css_header')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">

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
                        <form method="get" onsubmit="show()">
                            <div class="row">
                                <div class="col-md-2">
                                    <select class="form-control js-example-basic-single" name="company" data-placeholder="Select company" style="width: 100%;" required>
                                        <option></option>
                                        @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" @if($company->id == $company_data) selected
                                            @endif>{{$company->company_code.' - '.$company->company_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <div class="col-md-2">
                                    <select data-placeholder="Select department" style="width: 100%;" class="form-control js-example-basic-single" name="department" required>
                                        <option></option>
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" @if($department_data == $department->id)
                                            selected @endif>{{$department->code.' - '.$department->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div> --}}
                                <div class="col-md-2">
                                    <input type="date" name="date_from" class="form-control" value="{{ $from_date }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date_to" class="form-control" value="{{ $to_date }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">
                                        Filter
                                    </button>
                                    <a href="{{ url('timekeeping-per-company') }}" class="btn btn-warning">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>

                        {{-- @if ($errors->any())
                        @foreach ($errors->all() as $error)
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ $error }}
                        </div>
                        @endforeach
                        @endif --}}
                        <ul class="nav nav-tabs mt-5">
                            <li class="nav-item">
                                <a class="nav-link" href="#pills-issues" data-toggle="tab" >Issues <span class="badge badge-danger" id="totalIssues">0</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#pills-for-approval" data-toggle="tab" >Pending Approval <span class="badge badge-warning" id="totalPendingApproval">0</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="#pills-for-posting" data-toggle="tab" >For Posting <span class="badge badge-success" id="totalForPosting">0</span></a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade" id="pills-issues" role="tabpanel" aria-labelledby="pills-issues-tab">
                                <div class="row mt-5">
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
                                                    @php
                                                        $total_issues = 0;
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

                                                                $rest = "";

                                                                $employee_schedule = employeeSchedule($employee->ScheduleData,$date_r,$employee->schedule_id,$employee->employee_code);
                                                            
                                                                $convertedTimein = date('Y-m-d 00:00:00',strtotime($date_r));
                                                                $convertedTimeout = date('Y-m-d 00:00:00',strtotime($date_r));
                                                                if($employee_schedule)
                                                                {
                                                                    if($employee_schedule->time_in_from)
                                                                    {
                                                                        $convertedTimein = date('Y-m-d H:i:s',strtotime('-6 hours',strtotime($date_r." ".$employee_schedule->time_in_from)));
                                                                    }

                                                                    if ($employee_schedule->time_out_to)
                                                                    {
                                                                        $convertedTimeout = date('Y-m-d H:i:s', strtotime('+1 day', strtotime('+4 hours', strtotime($date_r." ".$employee_schedule->time_out_to))));
                                                                    }
                                                                }
                                                                // dd($convertedTimeout);
                                                                $time_in = ($employee->attendance_logs)->whereBetween('datetime',[$convertedTimein,$date_r." 23:59:59"])->sortBy('datetime')->first();
                                                                $time_out = ($employee->attendance_logs)->where('date', $date_r)->sortByDesc('datetime')->first();
                                                                if ($employee_schedule)
                                                                {
                                                                    if ($employee_schedule->time_in_from == null)
                                                                    {
                                                                        $time_out = ($employee->attendance_logs)->where('date', $date_r)->sortByDesc('datetime')->first();
                                                                    }
                                                                    else
                                                                    {
                                                                        if (date('A', strtotime($employee_schedule->time_out_to)) == "AM")
                                                                        {
                                                                            $time_out = ($employee->attendance_logs)->whereBetween('datetime',[$date_r." 23:59:59",$convertedTimeout])->sortByDesc('datetime')->first();
                                                                        }
                                                                    }
                                                                }

                                                                // Time in & Time out
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
                                                                if ($time_in && $time_out)
                                                                {
                                                                    $start_time = strtotime($time_in->datetime);
                                                                    $end_time = strtotime($time_out->datetime);
                                                                    $reg_hrs = ($end_time - $start_time) / 3600;

                                                                    if ($reg_hrs > 9.5)
                                                                    {
                                                                        $total_reg_hrs = 9.5;
                                                                    }
                                                                    else
                                                                    {
                                                                        $total_reg_hrs = $reg_hrs;
                                                                    }
                                                                }

                                                                // Late
                                                                if ($employee_schedule)
                                                                {
                                                                    if ($time_in)
                                                                    {
                                                                        $late_time_in = strtotime(date('H:i', strtotime($time_in->datetime)));
                                                                        $late_time_in_to = strtotime(date('H:i', strtotime($employee_schedule->time_in_to)));

                                                                        if (date('H:i', strtotime($time_in->datetime)) > $employee_schedule->time_in_to)
                                                                        {
                                                                            $total_late = ($late_time_in - $late_time_in_to) / 60;
                                                                            $late = $total_late;
                                                                        }
                                                                    }
                                                                }

                                                                // Undertime
                                                                if ($employee_schedule)
                                                                {
                                                                    if ($time_out)
                                                                    {
                                                                        $out = strtotime(date('H:i', strtotime($time_out->datetime)));
                                                                        $schedule_out = strtotime(date('H:i', strtotime($employee_schedule->time_out_to)));
                                                                        if ((date('H:i', strtotime($time_out->datetime)) < $employee_schedule->time_out_to) && (date('H:i', strtotime($time_out->datetime)) < $employee_schedule->time_out_from))
                                                                        {
                                                                            $total_undertime = ($schedule_out - $out) / 60;

                                                                            $undertime = $total_undertime;
                                                                        }
                                                                    }
                                                                }

                                                                // Leave w/ pay
                                                                $check_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);
                                                                if ($check_leave)
                                                                {
                                                                    $leave = 1;
                                                                    $abs = 0;
                                                                }
                                                                else
                                                                {
                                                                    $leave = 0;
                                                                }

                                                                // REG OT
                                                                $emp_has_ot = employeeHasOTDetails($employee->approved_ots,date('Y-m-d',strtotime($date_r)));
                                                                if ($emp_has_ot)
                                                                {
                                                                    if ($emp_has_ot < 8)
                                                                    {
                                                                        $overtime = $emp_has_ot;
                                                                    }
                                                                    else
                                                                    {
                                                                        $overtime = intval($emp_has_ot) - 1;
                                                                    }
                                                                }
                                                            @endphp
                                                            
                                                            @if(count(($employee->timekeeping_posted)->where('log_date',$date_r)) == 0)
                                                                @if($abs > 0 || $overtime > 0)
                                                                @php
                                                                    $total_issues = $total_issues+=1;
                                                                @endphp

                                                                <tr>
                                                                    <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][cutoff]" value="{{$to_date}}">
                                                                    <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][log_date]" value="{{ $date_r }}">
                                                                    <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][department_id]" value="{{ $employee->department_id }}">
                                                                    <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][shift]" value="{{$employee_schedule && $employee_schedule->time_in_to != null ? date('h:i A', strtotime($employee_schedule->time_in_to)) . '-' . date('h:i A', strtotime($employee_schedule->time_out_to)) : 'RESTDAY'}}">

                                                                    <td>
                                                                        <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][company_id]" value="{{ $employee->company_id }}">
                                                                        {{ $employee->company->company_code }}
                                                                    </td>
                                                                    <td>{{ $employee->department->name }}</td>
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
                                                                    <td>
                                                                        <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][employee_no]" value="{{ $employee->employee_code }}">
                                                                        {{ $employee->employee_code }}
                                                                    </td>
                                                                    <td>
                                                                        <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][name]" value="{{ $employee->last_name .', '.$employee->first_name }}">
                                                                        {{ $employee->last_name.', '.$employee->first_name }}
                                                                    </td>
                                                                    <td>{{ $date_r }}</td>
                                                                    <td @if(empty($time_in) && $rest == "" && $leave == 0) class="bg-danger" @endif>
                                                                        @if($time_in)
                                                                            {{ date('h:i A', strtotime($time_in->datetime)) }}
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][in]" value="{{ date('h:i A', strtotime($time_in->datetime)) }}">
                                                                        @endif
                                                                    </td>
                                                                    <td  @if(empty($time_out) && $rest == "" && $leave == 0) class="bg-danger" @endif>
                                                                        @if($time_out)
                                                                            {{ date('h:i A', strtotime($time_out->datetime)) }}
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][out]" value="{{ date('h:i A', strtotime($time_out->datetime)) }}">
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        {{ number_format($total_reg_hrs,2) }}
                                                                        <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][reg_hrs]" value="{{ number_format($total_reg_hrs,2) }}">
                                                                    </td>
                                                                    <td @if($late > 0) class="bg-danger" @endif>
                                                                        {{ number_format($late,0) }}

                                                                        <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][abs]" value="{{ number_format($abs,2) }}">
                                                                    </td>
                                                                    <td @if($undertime > 0) class="bg-danger" @endif>
                                                                        {{ number_format($undertime,2) }}
                                                                    </td>
                                                                    <td>
                                                                        {{ number_format($leave,2) }}
                                                                    </td>
                                                                    <td @if($overtime > 0) class="bg-warning" @endif>
                                                                        {{ number_format($overtime,2) }}
                                                                    </td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                    <td>0.00</td>
                                                                </tr>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-for-approval" role="tabpanel" aria-labelledby="pills-for-posting-tab">
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
                                                        <th>TOTAL HRS</th>
                                                        <th>TOTAL LATE</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $total_pending_approval = 0;
                                                    @endphp
                                                    @foreach ($employees as $employee)
                                                        @foreach ($date_range as $date_r)
                                                            @php
                                                                $total_reg_hrs = 0;
                                                                $total_late = 0;
                                                                $abs = 0;

                                                                $rest = "";

                                                                $employee_schedule = employeeSchedule($employee->ScheduleData,$date_r,$employee->schedule_id,$employee->employee_code);
                                                                $if_has_pending_approval = ($employee->dtr_correction)->where('employee_id', $employee->id)->where('date', $date_r)->where('status','Pending')->first();
                                                                $time_in = ($employee->attendance_logs)->where('date', $date_r)->sortBy('datetime')->first();
                                                                $time_out = ($employee->attendance_logs)->where('date', $date_r)->sortByDesc('datetime')->first();
                                                            @endphp
                                                            
                                                            @if(empty($employee_schedule))
                                                            @php
                                                                $rest = "RESTDAY"
                                                            @endphp
                                                            @else
                                                            @php
                                                                if ($time_in)
                                                                {
                                                                    if (date('H:i', strtotime($time_in->datetime)) > $employee_schedule->time_in_to)
                                                                    {
                                                                        $late_time_in = strtotime(date('H:i', strtotime($time_in->datetime)));
                                                                        $late_time_in_to = strtotime(date('H:i', strtotime($employee_schedule->time_in_to)));
                                                                        $total_late = abs($late_time_in_to - $late_time_in) / 60;
                                                                    }
                                                                }
                                                            @endphp
                                                            @endif

                                                            @if($employee_schedule)
                                                                @if($time_in && $time_out)
                                                                @php
                                                                    $abs = 0;
                                                                @endphp
                                                                @else
                                                                @php
                                                                    $abs = 1;
                                                                @endphp 
                                                                @endif
                                                            @else
                                                            @php
                                                                $abs = 0;
                                                            @endphp
                                                            @endif
                        
                                                            @if($if_has_pending_approval)
                                                            @php
                                                                $total_pending_approval = $total_pending_approval+=1;
                                                            @endphp

                                                            <tr>
                                                                <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][cutoff]" value="{{$to_date}}">
                                                                <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][log_date]" value="{{ $date_r }}">
                                                                <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][department_id]" value="{{ $employee->department_id }}">
                                                                <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][shift]" value="{{$employee_schedule && $employee_schedule->time_in_to != null ? date('h:i A', strtotime($employee_schedule->time_in_to)) . '-' . date('h:i A', strtotime($employee_schedule->time_out_to)) : 'RESTDAY'}}">

                                                                <td>
                                                                    <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][company_id]" value="{{ $employee->company_id }}">
                                                                    {{ $employee->company->company_code }}
                                                                </td>
                                                                <td>{{ $employee->department->name }}</td>
                                                                <td>
                                                                    @if($employee_schedule)
                                                                        @if($employee_schedule->time_in_from != null)
                                                                            <small>{{date('h:i A', strtotime($employee_schedule->time_in_to)).'-'.date('h:i A', strtotime($employee_schedule->time_out_to))}}</small>
                                                                            @if ($employee_schedule->time_in_from != $employee_schedule->time_in_to)
                                                                                <small>(Flexi)</small>
                                                                            @endif
                                                                        @else
                                                                        @php
                                                                            $rest = "RESTDAY"
                                                                        @endphp
                                                                        <small>{{ $rest }}</small>
                                                                        @endif
                                                                    @else
                                                                        @php
                                                                            $rest = "RESTDAY"
                                                                        @endphp
                                                                        {{ $rest }}
                                                                    @endif
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
                                                                    @if($if_has_pending_approval->time_in)
                                                                        {{ date('h:i A', strtotime($if_has_pending_approval->time_in)) }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($if_has_pending_approval->time_out)
                                                                        {{ date('h:i A', strtotime($if_has_pending_approval->time_out)) }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        if ($time_in && $time_out)
                                                                        {
                                                                            $start_time = strtotime($time_in->datetime);
                                                                            $end_time = strtotime($time_out->datetime);
                                                                            $reg_hrs = ($end_time - $start_time) / 3600;
                                                                            $total_reg_hrs = $reg_hrs - 1;
                                                                        }
                                                                    @endphp
                                                                    {{ number_format($total_reg_hrs,2) }}

                                                                    <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][reg_hrs]" value="{{ number_format($total_reg_hrs,2) }}">
                                                                </td>
                                                                <td @if($total_late > 0) class="bg-danger" @endif>
                                                                    @php
                                                                        if ($employee_schedule)
                                                                        {
                                                                            if ($time_in)
                                                                            {
                                                                                if (date('H:i', strtotime($time_in->datetime)) > $employee_schedule->time_in_to)
                                                                                {
                                                                                    $late_time_in = strtotime(date('H:i', strtotime($time_in->datetime)));
                                                                                    $late_time_in_to = strtotime(date('H:i', strtotime($employee_schedule->time_in_to)));
                                                                                    $total_late = abs($late_time_in_to - $late_time_in) / 60;
                                                                                }
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    {{ number_format($total_late,0) }}

                                                                    <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][abs]" value="{{ number_format($abs,2) }}">
                                                            </tr>
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade show active" id="pills-for-posting" role="tabpanel" aria-labelledby="pills-for-posting-tab">
                                <div class="row">
                                    <form action="{{ url('timekeeping-per-company/post_dtr') }}" method="post" class="my-3" style="width: 100%;">
                                        @csrf

                                        {{-- <button class="btn btn-lg btn-primary mt-3" type="submit">POST DTR</button> --}}

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
                                                        @php
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

                                                                    $rest = "";

                                                                    $employee_schedule = employeeSchedule($employee->ScheduleData,$date_r,$employee->schedule_id,$employee->employee_code);
                                                                
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
                                                                            $convertedTimeout = date('Y-m-d H:i:s', strtotime('+1 day', strtotime('+8 hours', strtotime($date_r." ".$employee_schedule->time_out_to))));
                                                                        }
                                                                        else
                                                                        {
                                                                            $convertedTimeout = date('Y-m-d H:i:s', strtotime('+8 hours', strtotime($date_r." ".$employee_schedule->time_out_to)));
                                                                        }
                                                                    }
                                                                    // dd($convertedTimeout);
                                                                    $time_in = ($employee->attendance_logs)->whereBetween('datetime',[$convertedTimein,$date_r." 23:59:59"])->sortBy('datetime')->first();
                                                                    $time_out = ($employee->attendance_logs)->whereBetween('datetime',[$date_r." 23:59:59",$convertedTimeout])->sortByDesc('datetime')->first();
                                                                    if (empty($time_out))
                                                                    {
                                                                        $time_out = ($employee->attendance_logs)->where('date', $date_r)->sortByDesc('datetime')->first();
                                                                    }

                                                                    // Schedule
                                                                    if($employee_schedule)
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

                                                                    // Time in & Time out
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
                                                                    if ($time_in && $time_out)
                                                                    {
                                                                        $start_time = strtotime($time_in->datetime);
                                                                        $end_time = strtotime($time_out->datetime);
                                                                        $reg_hrs = ($end_time - $start_time) / 3600;

                                                                        if ($reg_hrs > 9.5)
                                                                        {
                                                                            $total_reg_hrs = 9.5;
                                                                        }
                                                                        else
                                                                        {
                                                                            $total_reg_hrs = $reg_hrs;
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
                                                                                $late_time_in = strtotime($time_in->datetime);
                                                                                $late_time_in_to = $schedule_in;
                                                                                if ($late_time_in > $late_time_in_to)
                                                                                {
                                                                                    $total_late = ($late_time_in - $late_time_in_to) / 60;
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
                                                                        if ($time_out)
                                                                        {
                                                                            $out = strtotime($time_out->datetime);
                                                                            $schedule_out_to = $schedule_out;
                                                                            $schedule_out_from = $schedule_out_from;
                                                                            if(($out < $schedule_out_to) && ($out < $schedule_out_from))
                                                                            {
                                                                                $total_undertime = ($schedule_out_to - $out) / 60;
                                                                                $undertime = $total_undertime;
                                                                            }
                                                                        }
                                                                    }

                                                                    // Leave w/ pay
                                                                    $check_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_r)),$employee_schedule);
                                                                    if ($check_leave)
                                                                    {
                                                                        $leave = 1;
                                                                        $abs = 0;
                                                                    }
                                                                    else
                                                                    {
                                                                        $leave = 0;
                                                                    }

                                                                    // REG OT
                                                                    $emp_has_ot = employeeHasOTDetails($employee->approved_ots,date('Y-m-d',strtotime($date_r)));
                                                                    if ($emp_has_ot)
                                                                    {
                                                                        if ($emp_has_ot < 8)
                                                                        {
                                                                            $overtime = $emp_has_ot;
                                                                        }
                                                                        else
                                                                        {
                                                                            $overtime = intval($emp_has_ot) - 1;
                                                                        }
                                                                    }
                                                                @endphp
                                                                
                                                                @if(count(($employee->timekeeping_posted)->where('log_date',$date_r)) == 0)
                                                                    @if($abs == 0 && $overtime == 0)
                                                                    @php
                                                                        $total_for_posting = $total_for_posting+=1;
                                                                    @endphp

                                                                    <tr>
                                                                        <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][cutoff]" value="{{$to_date}}">
                                                                        <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][log_date]" value="{{ $date_r }}">
                                                                        <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][department_id]" value="{{ $employee->department_id }}">
                                                                        <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][shift]" value="{{$employee_schedule && $employee_schedule->time_in_to != null ? date('h:i A', strtotime($employee_schedule->time_in_to)) . '-' . date('h:i A', strtotime($employee_schedule->time_out_to)) : 'RESTDAY'}}">

                                                                        <td>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][company_id]" value="{{ $employee->company_id }}">
                                                                            {{ $employee->company->company_code }}
                                                                        </td>
                                                                        <td>{{ $employee->department->name }}</td>
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
                                                                        <td>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][employee_no]" value="{{ $employee->employee_code }}">
                                                                            {{ $employee->employee_code }}
                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][name]" value="{{ $employee->last_name .', '.$employee->first_name }}">
                                                                            {{ $employee->last_name.', '.$employee->first_name }}
                                                                        </td>
                                                                        <td>{{ $date_r }}</td>
                                                                        <td @if(empty($time_in) && $rest == "" && $leave == 0) class="bg-danger" @endif>
                                                                            @if($time_in)
                                                                                {{ date('h:i A', strtotime($time_in->datetime)) }}
                                                                                <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][in]" value="{{ date('h:i A', strtotime($time_in->datetime)) }}">
                                                                            @endif
                                                                        </td>
                                                                        <td  @if(empty($time_out) && $rest == "" && $leave == 0) class="bg-danger" @endif>
                                                                            @if($time_out)
                                                                                {{ date('h:i A', strtotime($time_out->datetime)) }}
                                                                                <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][out]" value="{{ date('h:i A', strtotime($time_out->datetime)) }}">
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            {{ number_format($total_reg_hrs,2) }}
                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][reg_hrs]" value="{{ number_format($total_reg_hrs,2) }}">
                                                                        </td>
                                                                        <td @if($late > 0) class="bg-danger" @endif>
                                                                            {{ number_format($late,0) }}

                                                                            <input type="hidden" name="employees[{{ $employee->employee_code }}][{{ $date_r }}][abs]" value="{{ number_format($abs,2) }}">
                                                                        </td>
                                                                        <td @if($undertime > 0) class="bg-danger" @endif>
                                                                            {{ number_format($undertime,2) }}
                                                                        </td>
                                                                        <td>
                                                                            {{ number_format($leave,2) }}
                                                                        </td>
                                                                        <td @if($overtime > 0) class="bg-warning" @endif>
                                                                            {{ number_format($overtime,2) }}
                                                                        </td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                        <td>0.00</td>
                                                                    </tr>
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        @endforeach
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
@endsection

@section('js')
{{-- <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script> --}}
<script>
    var total_issues = "<?php echo($total_issues) ?>"
    var total_for_posting = "<?php echo($total_for_posting) ?>"
    var total_pending_approval = "<?php echo($total_pending_approval) ?>"

    document.getElementById('totalIssues').innerText = total_issues
    document.getElementById('totalForPosting').innerText = total_for_posting
    document.getElementById('totalPendingApproval').innerText = total_pending_approval

    $(document).ready(function() {
        $(".timekeepingTable").DataTable({
            // pagelength:15,
            fixedColumns: {
                leftColumns: 1,  // 'start' and 'end' have been replaced with 'leftColumns' for clarity
            },
            paginate:false,
            dom: 'Bfrtip',
            // buttons: [
            //     'copy', 'excel'
            // ],
            // columnDefs: [{
            //     "defaultContent": "-",
            //     "targets": "_all"
            // }],
            order: [] 
        })
    })
</script>
@endsection