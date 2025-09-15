<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Timekeeping Beta</title>
    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    {{-- Datatable CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.min.css">
    {{-- Date Picker CSS --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
    {{-- Bootstrap Icon --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Select2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Or for RTL support -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
</head>

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

<body>
    <div id="loader" style="display:none;" class="loader"></div>
    <div class="container-fluid p-4">
        <h2>TIMEKEEPING MONITORING</h2>
        <form method="get" onsubmit="show()">
            <div class="row mt-4">
                <div class="col-md-2">
                    <select class="form-select select2" name="company" data-placeholder="Select company" required>
                        <option></option>
                        @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @if($company_data == $company->id) selected @endif>{{$company->company_code.' - '.$company->company_name }}
                        </option>
                        @endforeach
                    </select>
                </div>  
                <div class="col-md-2">
                    <select class="form-select select2" name="department" data-placeholder="Select department">
                        <option></option>
                        @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @if($department_data == $department->id) selected @endif>{{$department->code.' - '.$department->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ $from_date != "1970-01-01" ? $from_date : null }}">
                </div>
                <div class="col-md-2">
                    <input type="date"  name="date_to" class="form-control" value="{{ $to_date != "1970-01-01" ? $to_date : null }}">
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

        <ul class="nav nav-tabs mt-5">
            <li class="nav-item">
                <a class="nav-link active" href="#pills-issues" data-bs-toggle="tab" >Issues <span class="badge text-bg-danger" id="totalIssues">0</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#pills-for-approval" data-bs-toggle="tab" >Pending Approval <span class="badge text-bg-warning" id="totalPendingApproval">0</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#pills-for-posting" data-bs-toggle="tab" >For Posting <span class="badge text-bg-success" id="totalForPosting">0</span></a>
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
                            <table class="table table-bordered mt-5 myTable">
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
                                                    $employee_schedule = employeeSchedule($employee->ScheduleData,$date_r,$employee->schedule_id,$employee->employee_code);
                                                    $if_has_pending_approval = ($employee->dtr_correction)->where('employee_id', $employee->id)->where('date', $date_r)->first();
                                                    $time_in = ($employee->timekeeping_logs)->where('date', $date_r)->sortBy('id')->first();
                                                    $time_out = ($employee->timekeeping_logs)->where('date', $date_r)->sortByDesc('id')->first();
                                                    $total_reg_hrs = 0;
                                                    $total_late = 0;
                                                    $rest = "";
            
                                                    $abs = 0;
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
                                                    @if($abs > 0 || $total_late > 0)
                                                    @php
                                                        $total_issues = $total_issues+=1;
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" name="" id="">
                                                        </td>
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
                                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#new{{ $employee->id }}{{ $date_r }}"><i class="bi bi-pencil-square h3 text-dark"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                @endif
                                                    @include('new_timekeeping')
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
                            <table class="table table-bordered mt-5 myTable">
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
                                                $employee_schedule = employeeSchedule($employee->ScheduleData,$date_r,$employee->schedule_id,$employee->employee_code);
                                                $if_has_pending_approval = ($employee->dtr_correction)->where('employee_id', $employee->id)->where('date', $date_r)->where('status','Pending')->first();
                                                $time_in = ($employee->timekeeping_logs)->where('date', $date_r)->sortBy('id')->first();
                                                $time_out = ($employee->timekeeping_logs)->where('date', $date_r)->sortByDesc('id')->first();
                                                $total_reg_hrs = 0;
                                                $total_late = 0;
                                                $rest = "";
        
                                                $abs = 0;
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
            <div class="tab-pane fade" id="pills-for-posting" role="tabpanel" aria-labelledby="pills-for-posting-tab">
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
                                <table class="table table-bordered mt-5 myTable">
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
                                        {{-- @dd($employees[25]) --}}
                                        @php
                                            $total_for_posting = 0;
                                        @endphp
                                        @foreach ($employees as $employee)
                                            @foreach ($date_range as $date_r)
                                                @php
                                                    $employee_schedule = employeeSchedule($employee->ScheduleData,$date_r,$employee->schedule_id,$employee->employee_code);
                                                    $time_in = ($employee->timekeeping_logs)->where('date', $date_r)->sortBy('id')->first();
                                                    $time_out = ($employee->timekeeping_logs)->where('date', $date_r)->sortByDesc('id')->first();
                                                    $total_reg_hrs = 0;
                                                    $total_late = 0;
                                                    $rest = "";
            
                                                    $abs = 0;
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
                                                @if(count(($employee->timekeeping_posted)->where('log_date',$date_r)) == 0)
                                                    @if($abs == 0 && $total_late == 0)
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

    @include('sweetalert::alert')
    {{-- Jquery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    {{-- Datatable JS --}}
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.min.js"></script>
    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous">
    </script>
    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    {{-- Date Picker JS --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js">
    </script> --}}
    <script>
        function show() {
            document.getElementById("loader").style.display = "block";
        }
        var total_issues = "<?php echo($total_issues) ?>"
        var total_for_posting = "<?php echo($total_for_posting) ?>"
        var total_pending_approval = "<?php echo($total_pending_approval) ?>"

        document.getElementById('totalIssues').innerText = total_issues
        document.getElementById('totalForPosting').innerText = total_for_posting
        document.getElementById('totalPendingApproval').innerText = total_pending_approval
        // $('.datepicker').datepicker({
        //     format: 'yyyy/mm/dd',
        // })
        $( '.select2' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        } );

        let table = new DataTable('.myTable', {
            paginate: false
        });
    </script>
</body>

</html>