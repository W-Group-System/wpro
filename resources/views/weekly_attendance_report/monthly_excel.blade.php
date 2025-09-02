<!DOCTYPE html>
<html>
<head>
    <title>Monthly Attendance Report</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 2px; text-align: left; }
        @page {
            size: a4 portrait; 
        }
        body {
            background-color: #FFF;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: black;
        }
    </style>
</head>
<body>
    <div class="row">
        <div class="col-12" align="center" style="margin-top: -30px">
            <img src='{{ asset('images/icon.png')}}' width='100px'>
            <h4 style="margin-top: 0px" class="mb-4"><b>Monthly Attendance Report of <br> {{ date('F d, Y', strtotime($from)) }} - {{ date('F d, Y', strtotime($to)) }}</b></h4>
        </div>
        <div class="col-12">
            <label><b>I. Tardiness</b></label> 
            <table class="table table-bordered" id="tardiness">
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
                                // $schedule = employeeSchedule($employee->ScheduleData, $date_a, $employee->schedule_id, $employee->employee_code);
                                // $attendance = $employee->attendances
                                //     ->filter(function ($att) use ($date_a) {
                                //         return date('Y-m-d', strtotime($att->time_in)) === $date_a;
                                //     })
                                //     ->sortBy('time_in')
                                //     ->first();

                                // if ($schedule && $attendance) {
                                //     if (date('H:i', strtotime($attendance->time_in)) > $schedule->time_in_to) {
                                //         $tardy_count++;
                                //     }
                                // }
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
            <table class="table table-bordered" id="undertime">
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
                                // $schedule = employeeSchedule($employee->ScheduleData, $date_a, $employee->schedule_id, $employee->employee_code);
                                
                                // $attendance = $employee->attendances
                                //     ->filter(function ($att) use ($date_a) {
                                //         $date_in = date('Y-m-d', strtotime($att->time_in));
                                //         $date_out = date('Y-m-d', strtotime($att->time_out));
                                //         return $date_in == $date_a && $date_out == $date_a;
                                //     })
                                //     // ->where('time_in', '>=', date('Y-m-d', strtotime($date_a)))
                                //     // ->where('time_out',"<=",date('Y-m-d', strtotime($date_a)))
                                //     ->sortBy('time_out')
                                //     ->first();
                                
                                // if ($schedule && !empty($attendance)) {
                                //     if (date('H:i', strtotime($attendance->time_in)) < date('H:i', strtotime($schedule->time_in_from)))
                                //     {
                                //         $estimated_out = date('H:i', strtotime($schedule->time_out_from));
                                //     }
                                //     else
                                //     {
                                //         $hours = intval($schedule->working_hours);
                                //         $minutes = ($schedule->working_hours-$hours)*60;
                                //         $estimated_out = date('H:i', strtotime("+".$hours." hours",strtotime($attendance->time_in)));
                                //         $estimated_out = date('H:i', strtotime("+".$minutes." minutes",strtotime($estimated_out)));
                                //     }
                                //     if (date('H:i', strtotime($attendance->time_in)) > date('H:i', strtotime($schedule->time_in_to)))
                                //     {
                                //         $estimated_out = date('H:i', strtotime($schedule->time_out_to));
                                //     }

                                //     $if_has_leave = employeeHasLeave($employee->approved_leaves,date('Y-m-d',strtotime($date_a)),$schedule);
                                //     if (empty($if_has_leave))
                                //     {
                                //         if (date('H:i', strtotime($attendance->time_out)) < $estimated_out) {
                                //             $undertime_count++;
                                //         }
                                //         // $test_array[] = $date_a;
                                //     }
                                // }
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
            <table class="table table-bordered mb-2" id="leaves">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Employee ID - Name</th>
                        <th>Company</th>
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
            <table class="table table-bordered" id="leaveDeviations">
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
            <table class="table table-bordered" id="overtime">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Employee ID - Name</th>
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
                                @php
                                    $total_work_hours = 0;
                                @endphp

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
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div> 
</body>
</html>
