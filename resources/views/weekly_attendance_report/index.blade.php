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
                    <h4 class="card-title">Weekly Attendance Report</h4>
                    <form method='GET' onsubmit="show()" action="">
                        @csrf
                        <div class="row">
                            {{-- <div class='col-md-2'>
                                From
                                <input type="date" class="form-control form-control-sm" name="from" required />
                            </div> --}}
                            {{-- <div class='col-md-2'>
                                To
                                <input type="date" class="form-control form-control-sm" name="to" required />
                            </div> --}}
                            <div class='col-md-2'>
                                Company
                                <select data-placeholder="Select company" name="company" class="form-control js-example-basic-single" required>
                                    <option value=""></option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" @if($company_data == $company->id) selected @endif>{{ $company->company_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class='col-md-2'>
                                Week
                                <input type="week" class="form-control form-control-sm" max="{{ date('Y-\WW', strtotime('This week')) }}" value="{{ $week }}" name="week" required />
                            </div>
                            <div class='col-md-4'>
                                <button type="submit" id="submitBtn" class="form-control btn btn-primary mb-2 btn-sm">Generate</button>
                            </div>
                        </div>
                    </form>
                    <div class="row col-md-12 mb-3">
                        {{-- <div class="col-md-3" style="margin-top: 5px;">
                            <h3 id="reportTitle">{{date('M d, Y',strtotime($from))}} - {{date('M d, Y',strtotime($to))}}</h3> 
                        </div>
                        <div class="col-md-9">
                            <a href="{{ url('/attendance-report?from=' . $from . '&to=' . $to .'&count=' .$count. '&type=pdf') }}" target="_blank" class='btn btn-success btn-sm'><i class="fa fa-print btn-icon-append"></i>&nbsp;Print</a>
                        </div> --}}
                    </div>
                    <div class="col-12">
                        {{-- <h3 id="reportTitle"></h3> <a href="{{url('/attendance-report?form='.$from.'&year='.$to.'&type=pdf')}}" target="_blank" class='btn btn-danger btn-sm' >Print</a><br> --}}

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
                                @foreach($employees as $key=>$employee)
                                    @php
                                        $count = 0;
                                    @endphp
                                    @foreach ($date_array as $date_a)
                                        @php
                                            $employee_schedule = employeeSchedule($employee->ScheduleData,$date_a,$employee->schedule_id,$employee->employee_code);
                                            
                                            $final_time_in = ($employee->attendances)
                                                ->filter(function ($attendance) use ($date_a) {
                                                    return date('Y-m-d', strtotime($attendance->time_in)) === $date_a;
                                                })
                                                ->sortBy('time_in')
                                                ->first();
                                            
                                            if ($employee_schedule && $final_time_in)
                                            {
                                                if (date('H:i', strtotime($final_time_in->time_in)) > $employee_schedule->time_in_to)
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
                                    @foreach ($date_array as $date_a)
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
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script> 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function() {
        $("#tardiness").DataTable({
            paginate: false,
            sDom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    title: 'Weekly Attendance Report'
                },
                {
                    extend: 'excel',
                    title: 'Weekly Report - Tardiness', // Sets the Excel title
                    filename: 'Weekly Attendance Report - Tardiness'// Formats filename
                }
            ],
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }],
            order: []
        });
        $("#undertime").DataTable({
            paginate: false,
            sDom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    title: 'Weekly Attendance Report'
                },
                {
                    extend: 'excel',
                    title: 'Weekly Report - Undertime', // Sets the Excel title
                    filename: 'Weekly Attendance Report - Undertime'// Formats filename
                }
            ],
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }],
            order: []
        });
        $("#leaves").DataTable({
            paginate: false,
            sDom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    title: 'Weekly Attendance Report'
                },
                {
                    extend: 'excel',
                    title: 'Weekly Report - Leaves', // Sets the Excel title
                    filename: 'Weekly Attendance Report - Leaves'// Formats filename
                }
            ],
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }],
            order: []
        });
        $("#leaveDeviations").DataTable({
            paginate: false,
            sDom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    title: 'Weekly Attendance Report'
                },
                {
                    extend: 'excel',
                    title: 'Weekly   Report - Leave Deviations', // Sets the Excel title
                    filename: 'Weekly Attendance Report - Leave Deviations'// Formats filename
                }
            ],
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }],
            order: []
        });
        $("#overtime").DataTable({
            paginate: false,
            sDom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    title: 'Weekly Attendance Report'
                },
                {
                    extend: 'excel',
                    title: 'Weekly   Report - Overtime', // Sets the Excel title
                    filename: 'Weekly Attendance Report - Overtime'// Formats filename
                }
            ],
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }],
            order: []
        });
    })

</script>
@endsection