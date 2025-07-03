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
                                            @if(is_array($company_data) && in_array($comp->id, $company_data)) selected @endif>
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
                            <div class="col-md-3" style="margin-top: 5px;">
                                <h3 id="reportTitle">{{date('M d, Y',strtotime($from))}} - {{date('M d, Y',strtotime($to))}}</h3> 
                            </div>
                            <div class="col-md-9">
                                <a href="{{ url('/attendance-report?from=' . $from . '&to=' . $to . '&type=pdf') }}" target="_blank" class='btn btn-success btn-sm'><i class="fa fa-print btn-icon-append"></i>&nbsp;Print</a>
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
                                            $dates = $data->pluck('date')->unique(); // Get unique dates from AttendanceLog
                                        @endphp

                                        @foreach($employees as $key => $employee)
                                            @php
                                                $count = 0;
                                            @endphp

                                            @foreach ($dates as $date_a)
                                                @php
                                                    $employee_schedule = employeeSchedule($employee->ScheduleData, $date_a, $employee->schedule_id, $employee->employee_code);

                                                    $final_time_in = $employee->attendances
                                                        ->filter(function ($attendance) use ($date_a) {
                                                            return date('Y-m-d', strtotime($attendance->time_in)) === $date_a;
                                                        })
                                                        ->sortBy('time_in')
                                                        ->first();

                                                    if ($employee_schedule && $final_time_in) {
                                                        if (date('H:i', strtotime($final_time_in->time_in)) > $employee_schedule->time_in_to) {
                                                            $count++;
                                                        }
                                                    }
                                                @endphp
                                            @endforeach

                                            @if($count > 0)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $employee->company->company_name ?? '' }}</td>
                                                    <td>{{ $employee->employee_code }}</td>
                                                    <td>{{ optional($employee->user_info)->name }}</td>
                                                    <td>{{ $count }}</td>
                                                    <td></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                                <hr>
                                <label><b>II. Undertime</b></label>
                                <table class="table table-hover table-bordered" id="undertime">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Employee ID</th>
                                            <th>Company</th>
                                            <th>Name</th>
                                            <th>No. of Days</th>
                                            <th>Remarks/ Recommendation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($employees as $key=>$employee)
                                            @php
                                                $count = 0;
                                            @endphp
                                            @foreach ($data as $date_a)
                                                @php
                                                    $employee_schedule = employeeSchedule($employee->ScheduleData,$date_a,$employee->schedule_id,$employee->employee_code);
                                                    
                                                    $final_time_out = ($employee->attendances)
                                                        ->filter(function ($attendance) use ($date_a) {
                                                            return date('Y-m-d', strtotime($attendance->time_out)) === $date_a;
                                                        })
                                                        ->sortByDesc('time_out')
                                                        ->first();
                                                    
                                                    if ($employee_schedule && $final_time_out)
                                                    {
                                                        if (date('H:i', strtotime($final_time_out->time_out)) < $employee_schedule->time_out_to)
                                                        {
                                                            $count++;
                                                        }
                                                    }
                                                    
                                                @endphp
                                            @endforeach

                                            @if($count > 0)
                                                <tr>
                                                    <td>{{ $key+1 }}</td>
                                                    <td>{{ $employee->company->company_name }}</td>
                                                    <td>{{ $employee->employee_code }}</td>
                                                    <td>{{ $employee->user_info->name }}</td>
                                                    <td>{{ $count }}</td>
                                                    <td></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                                <hr>

                                <label style="margin-bottom: 20px;"><b>III. Leaves</b></label><br>
                                <label>A. Leave without Pay</label>
                                <table class="table table-hover table-bordered mb-2" id="leaves">
                                    <thead>
                                        <tr>
                                            <th>Employee ID</th>
                                            <th>No.</th>
                                            <th>Company</th>
                                            <th>Name</th>
                                            <th>No. of LWOP days</th>
                                            <th>Reason</th>
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
                                                    <td>{{ $employee->employee_code }}</td>
                                                    <td>{{ $employee->company->company_code }}</td>
                                                    <td>{{ $employee->user_info->name }}</td>
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
                                            <th>Employee ID</th>
                                            <th>Company</th>
                                            <th>Name</th>
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
                                                    <td>{{ $employee->employee_code }}</td>
                                                    <td>{{ $employee->company->company_code }}</td>
                                                    <td>{{ $employee->user_info->name }}</td>
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
                                            <th>Employee ID</th>
                                            <th>Company</th>
                                            <th>Regular Working Hours</th>
                                            <th>Overtime Hours Total</th>
                                            <th>% of Overtime</th>
                                            <th>Remarks/ Recommendation</th>
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
                                                @endphp
                                                <tr>
                                                    <td>{{ $count }}</td>
                                                    <td>{{ $employee->employee_code.' - '.$employee->user_info->name }}</td>
                                                    <td>{{ $employee->company->company_code }}</td>
                                                    <td>
                                                        @php
                                                            $day_name = [];
                                                            
                                                            foreach ($employee->approved_ots as $overtime)
                                                            {
                                                                $day_name[] = date('l', strtotime($overtime->ot_date));
                                                            }
                                                            
                                                            $employee_schedule = $employee->schedule_info->ScheduleData->whereIn('name', $day_name)->pluck('working_hours')->toArray();
                                                        @endphp
                                                        {{ implode("\n", $employee_schedule) }}
                                                    </td>
                                                    <td>
                                                        @foreach ($employee->approved_ots as $overtime)
                                                            {{ date('M d, Y', strtotime($overtime->ot_date)).' - '. $overtime->ot_approved_hrs }} <br>
                                                        @endforeach
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            @endif
                                            @if(count($daily_schedules->where('employee_code', $employee->employee_code)) > 0)
                                            <tr>
                                                <td>{{ $count++ }}</td>
                                                <td>{{ $employee->employee_code.' - '.$employee->user_info->name }}</td>
                                                <td>{{ $employee->company->company_code }}</td>
                                                <td>
                                                    @php
                                                        $reg_hrs = [];
                                                        foreach($daily_schedules->where('employee_code', $employee->employee_code) as $daily_sched)
                                                        {
                                                            $reg_hrs[] = $daily_sched->working_hours;
                                                        }
                                                    @endphp

                                                    {!! implode("<br>", array_unique($reg_hrs)) !!}
                                                </td>
                                                <td>
                                                    @php
                                                        $date_arr = [];
                                                        foreach($daily_schedules->where('employee_code', $employee->employee_code) as $daily_sched)
                                                        {
                                                            $date_arr[] = date('M d, Y', strtotime($daily_sched->log_date));
                                                        }
                                                    @endphp

                                                    {!! implode("<br>", array_unique($date_arr)) !!}
                                                </td>
                                                <td></td>
                                                <td></td>
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