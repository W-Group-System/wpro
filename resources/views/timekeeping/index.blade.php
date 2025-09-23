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
                                <div class="col-md-2">
                                    <select data-placeholder="Select department" style="width: 100%;" class="form-control js-example-basic-single" name="department" required>
                                        <option></option>
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" @if($department_data == $department->id)
                                            selected @endif>{{$department->code.' - '.$department->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
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
                                <a class="nav-link" href="#pills-issues" data-toggle="tab" >Issues <span class="badge bg-danger" id="totalIssues">0</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#pills-for-approval" data-toggle="tab" >Pending Approval <span class="badge bg-warning" id="totalPendingApproval">0</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="#pills-for-posting" data-toggle="tab" >For Posting <span class="badge bg-success" id="totalForPosting">0</span></a>
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
                                                        <th>TOTAL HRS</th>
                                                        <th>TOTAL LATE</th>
                                                        <th>ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- @dd($employees[25]) --}}
                                                    @php
                                                        $total_issues = 0;
                                                    @endphp
                                                    @foreach ($employees as $employee)
                                                        @foreach ($date_range as $date_r)
                                                                @php
                                                                    $total_reg_hrs = 0;
                                                                    $total_late = 0;
                                                                    $abs = 0;

                                                                    $rest = "";

                                                                    $employee_schedule = employeeSchedule($employee->ScheduleData,$date_r,$employee->schedule_id,$employee->employee_code);
                                                                    $if_has_pending_approval = ($employee->dtr_correction)->where('employee_id', $employee->id)->where('date', $date_r)->first();
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

                                                                @if(empty($time_in) && empty($time_out) && ($rest == ""))
                                                                @php
                                                                    $abs = 1;
                                                                @endphp
                                                                @endif
                                                                
                                                                @if(!$if_has_pending_approval || ($if_has_pending_approval->status == "Returned"))
                                                                    @if($abs > 0)
                                                                    @php
                                                                        $total_issues = $total_issues+=1;
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $employee->company->company_code }}</td>
                                                                        <td>{{ $employee->department->name }}</td>
                                                                        <td>
                                                                            @if($employee_schedule)
                                                                                <small>{{date('h:i A', strtotime($employee_schedule->time_in_to)).'-'.date('h:i A', strtotime($employee_schedule->time_out_to))}}</small>
                                                                                @if ($employee_schedule->time_in_from != $employee_schedule->time_in_to)
                                                                                    <small>(Flexi)</small>
                                                                                @endif
                                                                            @else
                                                                                @php
                                                                                    $rest = "RESTDAY"
                                                                                @endphp
                                                                                {{ $rest }}
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $employee->employee_code }}</td>
                                                                        <td>{{ $employee->last_name.', '.$employee->first_name }}</td>
                                                                        <td>{{ $date_r }}</td>
                                                                        <td @if(empty($time_in) && $rest == "") class="bg-danger" @endif>
                                                                            @if($time_in)
                                                                                {{ date('h:i A', strtotime($time_in->datetime)) }}
                                                                            @else 
                                                                            @php
                                                                                $abs = 1;
                                                                            @endphp
                                                                            @endif
                                                                        </td>
                                                                        <td  @if(empty($time_out) && $rest == "") class="bg-danger" @endif>
                                                                            @if($time_out)
                                                                                {{ date('h:i A', strtotime($time_out->datetime)) }}
                                                                            @else
                                                                            @php
                                                                                $abs = 1;
                                                                            @endphp
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
                                                                        </td>
                                                                        <td @if($total_late > 0 ) class="bg-danger" @endif>
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
                                                                        </td>
                                                                        <td>
                                                                            {{-- <a href="javascript:void(0)" data-toggle="modal" data-bs-target="#new{{ $employee->id }}{{ $date_r }}"><i class="bi bi-pencil-square h3 text-dark"></i></a> --}}
                                                                        </td>
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
                                                        {{-- <th></th> --}}
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
                                                        {{-- <th>ACTION</th> --}}
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
                                                                        <small>{{date('h:i A', strtotime($employee_schedule->time_in_to)).'-'.date('h:i A', strtotime($employee_schedule->time_out_to))}}</small>
                                                                        @if ($employee_schedule->time_in_from != $employee_schedule->time_in_to)
                                                                            <small>(Flexi)</small>
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
                                                                    {{-- @else 
                                                                    @php
                                                                        $abs = 1;
                                                                    @endphp --}}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($if_has_pending_approval->time_out)
                                                                        {{ date('h:i A', strtotime($if_has_pending_approval->time_out)) }}
                                                                    {{-- @else
                                                                    @php
                                                                        $abs = 1;
                                                                    @endphp --}}
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
                                    <form action="{{ url('timekeeping-per-company/post_dtr') }}" method="post" class="my-3">
                                        @csrf

                                        <button class="btn btn-lg btn-primary mt-3" type="submit">POST DTR</button>

                                        <div class="d-flex align-items-center">
                                            <div class="bg-danger" style="width: 15px; height: 15px; margin-right: 5px;"></div>
                                            <span>Absent</span>
                                        </div>
                            
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered mt-5 timekeepingTable" style="width: 100%;">
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
                                                            $total_for_posting = 0;
                                                        @endphp
                                                        @foreach ($employees as $employee)
                                                            @foreach ($date_range as $date_r)
                                                                @php
                                                                    $total_reg_hrs = 0;
                                                                    $total_late = 0;
                                                                    $abs = 0;

                                                                    $rest = "";

                                                                    $employee_schedule = employeeSchedule($employee->ScheduleData,$date_r,$employee->schedule_id,$employee->employee_code);
                                                                    // $time_in = ($employee->attendance_logs)->where('date', $date_r)->sortBy('datetime')->first();
                                                                    // $time_out = ($employee->attendance_logs)->where('date', $date_r)->sortByDesc('datetime')->first();

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
                                                                            $convertedTimeout = date('Y-m-d H:i:s', strtotime('+1 day', strtotime('+6 hours', strtotime($date_r." ".$employee_schedule->time_out_to))));
                                                                        }
                                                                    }
                                                                    
                                                                    $time_in = ($employee->attendance_logs)->whereBetween('datetime',[$convertedTimein,$date_r." 23:59:59"])->sortBy('datetime')->first();
                                                                    if ($employee_schedule)
                                                                    {
                                                                        if (date('A', strtotime($employee_schedule->time_out_to)) == "AM")
                                                                        {
                                                                            $time_out = ($employee->attendance_logs)->whereBetween('datetime',[$date_r." 23:59:59",$convertedTimeout])->sortByDesc('datetime')->first();
                                                                        }
                                                                        else
                                                                        {
                                                                            $time_out = ($employee->attendance_logs)->where('date', $date_r)->sortByDesc('datetime')->first();
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        $time_out = ($employee->attendance_logs)->where('date', $date_r)->sortByDesc('datetime')->first();
                                                                    }
                                                                @endphp
                                                                @if(empty($employee_schedule))
                                                                @php
                                                                    $rest = "RESTDAY"
                                                                @endphp
                                                                @else
                                                                @php
                                                                    if ($time_in && $time_out)
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
                                                                @if(count(($employee->timekeeping_posted)->where('log_date',$date_r)) == 0)
                                                                    @if($abs == 0)
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
                                                                        <td @if(empty($time_in) && $rest == "") class="bg-danger" @endif>
                                                                            @if($time_in)
                                                                                {{ date('h:i A', strtotime($time_in->datetime)) }}
                                                                                <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][in]" value="{{ date('h:i A', strtotime($time_in->datetime)) }}">
                                                                            @else 
                                                                            @php
                                                                                $abs = 1;
                                                                            @endphp
                                                                            @endif
                                                                        </td>
                                                                        <td  @if(empty($time_out) && $rest == "") class="bg-danger" @endif>
                                                                            @if($time_out)
                                                                                {{ date('h:i A', strtotime($time_out->datetime)) }}
                                                                                <input type="hidden" name="employees[{{ $employee->employee_code }}][{{$date_r}}][out]" value="{{ date('h:i A', strtotime($time_out->datetime)) }}">
                                                                            @else
                                                                            @php
                                                                                $abs = 1;
                                                                            @endphp
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @php
                                                                                if ($time_in && $time_out)
                                                                                {
                                                                                    $start_time = strtotime($time_in->datetime);
                                                                                    $end_time = strtotime($time_out->datetime);
                                                                                    $reg_hrs = ($end_time - $start_time) / 3600;

                                                                                    if ($reg_hrs > 8.00)
                                                                                    {
                                                                                        $total_reg_hrs = $reg_hrs - 1;
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        $total_reg_hrs = $reg_hrs;
                                                                                    }
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
                                                                        </td>
                                                                    </tr>
                                                                    @endif
                                                                @endif

                                                                {{-- @include('edit_timekeeping') --}}
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
    $(document).ready(function() {
        $(".timekeepingTable").DataTable({
            // pagelenth:25,
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