@extends('layouts.header')

@section('css_header')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">
@endsection

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Monthly Attendance Report</h4>
                    <form method='GET' onsubmit="show()" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class='col-md-2'>
                                <label for="from">From</label>
                                <input type="date" class="form-control form-control-sm" value="{{ old('from', $from) }}" id="from" name="from" required/>
                            </div>

                            <div class='col-md-2'>
                                <label for="to">To</label>
                                <input type="date" class="form-control form-control-sm" value="{{ old('to', $to) }}" id="to" name="to" required
                                />
                            </div>
                            <div class='col-md-3'>
                                Company
                                <select data-placeholder="Select Company" onchange='clear();' 
                                    class="form-control form-control-sm required js-example-basic-single" 
                                    style="width:100%;" name="companies[]" id="companySelect" multiple required>
                                    <option value="">-- Select Companies --</option>
                                    @foreach($companies as $comp)
                                        <option value="{{ $comp->id }}" 
                                            @if(in_array($comp->id, $company_data->pluck('id')->toArray())) selected @endif>
                                            {{ $comp->company_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class='col-md-2'>
                                Greater Than (Count)
                                <input type="number" class="form-control form-control-sm" value='{{$count}}' id='count' min=1 name="count" required />
                            </div> --}}
                            <div class='col-md-3' style="margin-top: 18px;">
                                <button type="submit" id="submitBtn" class="form-control btn btn-primary mb-2 btn-sm">Generate</button>
                            </div>
                        </div>
                        <div class="row mt-3 mb-3">
                            <div class="col-md-4" style="margin-top: 5px;">
                                <h3 id="reportTitle">{{date('M d, Y',strtotime($from))}} - {{date('M d, Y',strtotime($to))}}</h3> 
                            </div>
                            <div class="col-md-8">
                                <!-- <a href="{{ url('/attendance-report?from=' . $from . '&to=' . $to . '&type=pdf') }}" target="_blank" class='btn btn-success btn-sm'><i class="fa fa-print btn-icon-append"></i>&nbsp;Print</a> -->
                                 <a href="{{ route('monthly_attendance_report.pdf', ['from' => $from, 'to' => $to, 'companies' => request()->input('companies')]) }}"
                                    target="_blank"
                                    class="btn btn-danger btn-sm">
                                        <i class="fa fa-file-pdf"></i> Export as PDF
                                    </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label><b>I. Tardiness</b></label> 
                                <table class="table table-hover table-bordered" id="tardiness">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Company</th>
                                            <th>Employee ID</th>
                                            <th>Name</th>
                                            <th>No. of Days</th>
                                            <th>Remarks/ Recommendation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $tardy_employees = collect();
                                            $row_number = 1;
                                        @endphp

                                        @foreach($employees as $employee)
                                            @php
                                                $tardy_count = 0;
                                            @endphp
                                            @foreach($date_range as $date_a)
                                                @php
                                                    $schedule = employeeSchedule($employee->ScheduleData, $date_a, $employee->schedule_id, $employee->employee_code);
                                                    $attendance = $employee->attendances
                                                        ->filter(function ($att) use ($date_a) {
                                                            return date('Y-m-d', strtotime($att->time_in)) === $date_a;
                                                        })
                                                        ->sortBy('time_in')
                                                        ->first();

                                                    $emp_leaves = $employee->approved_leaves_halfday
                                                        ->filter(fn($leave) => $date_a >= date('Y-m-d', strtotime($leave->date_from)) && $date_a <= date('Y-m-d', strtotime($leave->date_to)))
                                                        ->sortBy('date_from')
                                                        ->first();   
                                                        
                                                    if ($schedule && $attendance && !$emp_leaves) {
                                                        if (date('H:i', strtotime($attendance->time_in)) > $schedule->time_in_to) {
                                                            $tardy_count++;
                                                        }
                                                    }
                                                @endphp
                                            @endforeach

                                            @if($tardy_count > 0)
                                                @php
                                                    $tardy_employees->push([
                                                        'row_number' => $row_number++,
                                                        'company_name' => $employee->company->company_name ?? '',
                                                        'employee_code' => $employee->employee_code,
                                                        'name' => optional($employee->user_info)->name,
                                                        'tardy_count' => $tardy_count,
                                                        'remarks' => 'Excessive; for NOD issuance'
                                                    ]);
                                                @endphp
                                            @endif
                                        @endforeach

                                        @foreach($tardy_employees as $tardy)
                                            <tr>
                                                <td>{{ $tardy['row_number'] }}</td>
                                                <td>{{ $tardy['company_name'] }}</td>
                                                <td>{{ $tardy['employee_code'] }}</td>
                                                <td>{{ $tardy['name'] }}</td>
                                                <td>{{ $tardy['tardy_count'] }}</td>
                                                <td>{{ $tardy['remarks'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <hr>                                
                                <label><b>II. Undertime</b></label>
                                <table class="table table-hover table-bordered" id="undertime">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Company</th>
                                            <th>Employee ID</th>
                                            <th>Name</th>
                                            <th>No. of Days</th>
                                            <th>Remarks/ Recommendation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $undertime_employees = collect();
                                            $row_number = 1;
                                            $test_array = [];
                                        @endphp
                                      
                                        @foreach($employees as $employee)
                                            @php
                                                $undertime_count = 0;
                                            @endphp
                                            @foreach($date_range as $date_a)
                                                @php
                                                    $schedule = employeeSchedule($employee->ScheduleData, $date_a, $employee->schedule_id, $employee->employee_code);
                                                    
                                                    $attendance = $employee->attendances
                                                        ->filter(function ($att) use ($date_a) {
                                                            $date_in = date('Y-m-d', strtotime($att->time_in));
                                                            $date_out = date('Y-m-d', strtotime($att->time_out));
                                                            return $date_in == $date_a && $date_out == $date_a;
                                                        })
                                                        // ->where('time_in', '>=', date('Y-m-d', strtotime($date_a)))
                                                        // ->where('time_out',"<=",date('Y-m-d', strtotime($date_a)))
                                                        ->sortBy('time_out')
                                                        ->first();
                                                    
                                                    if ($schedule && !empty($attendance)) {
                                                        if (date('H:i', strtotime($attendance->time_in)) < date('H:i', strtotime($schedule->time_in_from)))
                                                        {
                                                            $estimated_out = date('H:i', strtotime($schedule->time_out_from));
                                                        }
                                                        else
                                                        {
                                                            $hours = intval($schedule->working_hours);
                                                            $minutes = ($schedule->working_hours-$hours)*60;
                                                            $estimated_out = date('H:i', strtotime("+".$hours." hours",strtotime($attendance->time_in)));
                                                            $estimated_out = date('H:i', strtotime("+".$minutes." minutes",strtotime($estimated_out)));
                                                        }
                                                        if (date('H:i', strtotime($attendance->time_in)) > date('H:i', strtotime($schedule->time_in_to)))
                                                        {
                                                            $estimated_out = date('H:i', strtotime($schedule->time_out_to));
                                                        }

                                                        $if_has_leave = employeeHasLeave(
                                                            $employee->approved_leaves,
                                                            date('Y-m-d', strtotime($date_a)),
                                                            $schedule
                                                        );

                                                        $if_has_ob = employeeHasOB(
                                                            $employee->approved_obs,
                                                            date('Y-m-d', strtotime($date_a)),
                                                            $schedule
                                                        );

                                                        if (empty($if_has_leave) && empty($if_has_ob)) {
                                                            if (date('H:i', strtotime($attendance->time_out)) < $estimated_out) {
                                                                $undertime_count++;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                            @endforeach
                                            
                                            @if($undertime_count > 0)
                                                @php
                                                    $undertime_employees->push([
                                                        'row_number' => $row_number++,
                                                        'company_name' => $employee->company->company_name ?? '',
                                                        'employee_code' => $employee->employee_code,
                                                        'name' => optional($employee->user_info)->name,
                                                        'undertime_count' => $undertime_count,
                                                        'remarks' => 'Excessive; for NOD issuance'
                                                    ]);
                                                @endphp
                                            @endif
                                        @endforeach
                                       
                                        @foreach($undertime_employees as $undertime)
                                            <tr>
                                                <td>{{ $undertime['row_number'] }}</td>
                                                <td>{{ $undertime['company_name'] }}</td>
                                                <td>{{ $undertime['employee_code'] }}</td>
                                                <td>{{ $undertime['name'] }}</td>
                                                <td>{{ $undertime['undertime_count'] }}</td>
                                                <td>{{ $undertime['remarks'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <hr>
                                <label style="margin-bottom: 20px;"><b>III. Leaves</b></label><br>
                                <label>A. Leave without Pay</label>
                                <table class="table table-hover table-bordered mb-2" id="leaves">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Employee ID - Name</th>
                                            <th>Company</th>
                                            <th>No. of LWOP days</th>
                                            <th>Remarks/ Recommendation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $count = 0;
                                        @endphp
                                        @foreach($employees as $key=>$employee)
                                            @if(count($employee->leaves->where('withpay', 0)) > 0)
                                                <tr>
                                                    <td>
                                                        @php
                                                            $count++;
                                                        @endphp

                                                        {{ $count }}
                                                    </td>
                                                    <td>{{ $employee->employee_code.' - '.$employee->user_info->name }}</td>
                                                    <td>{{ $employee->company->company_code }}</td>
                                                    <td>
                                                        @php
                                                            $total_array = [];
                                                            foreach ($employee->leaves as $leaves)
                                                            {
                                                                $total_array[] = get_count_days_leave($leaves->employee->scheduleData, $leaves->date_from, $leaves->date_to);
                                                            }
                                                        @endphp
                                                        {{ collect($total_array)->sum() }}
                                                    </td>
                                                    <td>
                                                        No leave Credits balance
                                                    </td> 
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                                
                                <label>B. Leave Deviations</label>
                                <table class="table table-hover table-bordered" id="leaveDeviations">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Employee ID - Name</th>
                                            <th>Company</th>
                                            <th>Leave Date(s)</th>
                                            <th>Leave Type</th>
                                            <th>Remarks/ Recommendation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $count = 0;
                                        @endphp
                                        @foreach($employees as $key=>$employee)
                                            @if(count($employee->leaves->where('withpay', 1)) > 0)
                                                <tr>
                                                    <td>
                                                        @php
                                                            $count++;
                                                        @endphp

                                                        {{ $count }}
                                                    </td>
                                                    <td>{{ $employee->employee_code.' - '.$employee->user_info->name }}</td>
                                                    <td>{{ $employee->company->company_code }}</td>
                                                    <td>
                                                        @foreach ($employee->leaves->where('withpay', 1)->sortBy('date_from') as $leaves)
                                                            {{ date('M d, Y', strtotime($leaves->date_from)) .' - '. date('M d, Y', strtotime($leaves->date_to)) }}  <br>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @foreach ($employee->leaves->where('withpay', 1)->sortBy('date_from') as $leaves)
                                                            {{ $leaves->leave->leave_type }}  <br>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @foreach ($employee->leaves->where('withpay', 1)->sortBy('date_from') as $leaves)
                                                            {{ $leaves->approval_remarks }}  <br>
                                                        @endforeach
                                                    </td> 
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                                <hr> 

                                <label><b>IV. Overtime</b></label>
                                <table class="table table-hover table-bordered" id="overtime">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Employee ID - Name</th>
                                            <th>Company</th>
                                            <th>Regular Working Hours</th>
                                            <th>Overtime Hours Total</th>
                                            <th>% of Overtime</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $count = 0;
                                        @endphp
                                        @foreach($employees as $key => $employee)
                                            @if(count($employee->approved_ots) > 0)
                                                @php
                                                    $count++;
                                                    $total_work_hours = 0;
                                                @endphp
                                                <tr>
                                                    <td>{{ $count }}</td>
                                                    <td>{{ $employee->employee_code.' - '.$employee->user_info->name }}</td>
                                                    <td>{{ $employee->company->company_code }}</td>
                                                    @foreach($date_range as $date_a)
                                                        @php    
                                                            $sched = employeeSchedule(
                                                                $employee->ScheduleData,
                                                                $date_a,
                                                                $employee->schedule_id,
                                                                $employee->employee_code
                                                            );
                                                            if ($sched) {
                                                                $total_work_hours += floatval($sched->working_hours) - 1;
                                                            }
                                                        @endphp
                                                    @endforeach
                                                    <td>{{ number_format($total_work_hours, 2) }} hrs</td>
                                                    <td>
                                                        {{-- @foreach ($employee->approved_ots as $overtime)
                                                            {{ date('M d, Y', strtotime($overtime->ot_date)).' - '. $overtime->ot_approved_hrs }} <br>
                                                        @endforeach --}}
                                                       {{ $employee->approved_ots->sum('ot_approved_hrs') }} hrs
                                                    </td>
                                                    <td>
                                                        @php
                                                            $total_ot_hours = $employee->approved_ots->sum('ot_approved_hrs');
                                                            $total_schedule_hours = $employee->schedule_info->ScheduleData->sum('working_hours');

                                                            $percent_overtime = $total_schedule_hours > 0 
                                                                ? ($total_ot_hours / $total_schedule_hours) * 100 
                                                                : 0;
                                                        @endphp

                                                        {{ number_format($percent_overtime, 2) }}%
                                                    </td>
                                                </tr>
                                            @endif
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
@endsection