<div class="modal fade" id="viewDetails{{ $emp->id }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View details</h5>
            </div>
            <div class="modal-body">
                @foreach([1, 2] as $balanceLeaveId)
                    @php
                        $monthlyLeaves = $emp->employee_leave_list
                            ->where('leave_id', $balanceLeaveId)
                            ->sortBy(function($leave) {
                                return sprintf('%04d-%02d-%08d', $leave->year, $leave->month, $leave->id);
                            })
                            ->values();
                        $beginningCredit = (float) $emp->employee_leave_credits
                            ->where('leave_type', $balanceLeaveId)
                            ->sum('count');
                        $runningBalance = $beginningCredit;
                        $employeeUsages = isset($leave_usages) ? ($leave_usages[$emp->user_id] ?? collect()) : collect();
                    @endphp
                    @if($monthlyLeaves->count() > 0)
                        <table class="table table-bordered mb-2">
                            <tr>
                                <th>Month</th>
                                <th>Leave</th>
                                <th>Beginning Balance</th>
                                <th>Earned Leave</th>
                                <th>Used Leave</th>
                                <th>End Balance</th>
                            </tr>
                            @if($beginningCredit > 0)
                                <tr>
                                    <td>2024 Beginning</td>
                                    <td>{{ $monthlyLeaves->first()->leave->leave_type }}</td>
                                    <td>{{ number_format(0, 3) }}</td>
                                    <td>{{ number_format($beginningCredit, 3) }}</td>
                                    <td>{{ number_format(0, 3) }}</td>
                                    <td>{{ number_format($beginningCredit, 3) }}</td>
                                </tr>
                            @endif
                            @foreach ($monthlyLeaves as $leave)
                                @php
                                    $monthStart = date('Y-m-01', strtotime($leave->year.'-'.$leave->month.'-01'));
                                    $monthEnd = date('Y-m-t', strtotime($monthStart));
                                    $usageStart = $leave->leave_id == 2 ? $leave->year.'-01-01' : $monthStart;
                                    $usageEnd = $leave->leave_id == 2 ? $leave->year.'-12-31' : $monthEnd;
                                    $beginningBalance = $runningBalance;
                                    $usedLeave = $employeeUsages
                                        ->where('leave_type', $leave->leave_id)
                                        ->filter(function($employeeLeave) use ($usageStart, $usageEnd) {
                                            return $employeeLeave->date_from <= $usageEnd && $employeeLeave->date_to >= $usageStart;
                                        })
                                        ->sum(function($employeeLeave) use ($emp, $usageStart, $usageEnd) {
                                            $from = max($employeeLeave->date_from, $usageStart);
                                            $to = min($employeeLeave->date_to, $usageEnd);
                                            $count = get_count_days_leave($emp->ScheduleData, $from, $to, $emp->location);
                                            return $employeeLeave->halfday == 1 && $count == 1 ? 0.5 : $count;
                                        });
                                    $earnedLeave = (float) $leave->earned_per_month;
                                    $runningBalance = $beginningBalance + $earnedLeave - $usedLeave;
                                @endphp
                                <tr>
                                    <td>{{ date('F Y', strtotime($monthStart)) }}</td>
                                    <td>{{ $leave->leave->leave_type }}</td>
                                    <td>{{ number_format($beginningBalance, 3) }}</td>
                                    <td>{{ number_format($earnedLeave, 3) }}</td>
                                    <td>{{ number_format($usedLeave, 3) }}</td>
                                    <td>{{ number_format($runningBalance, 3) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="6"><b>End Balance:</b> {{ number_format($runningBalance, 3) }}</td>
                            </tr>
                        </table>
                    @endif
                @endforeach

                @if(count($emp->employee_leave_list->where('leave_id',3)) > 0)
                    <table class="table table-bordered mb-2">
                        <tr>
                            <th>Month</th>
                            <th>Leave</th>
                            <th>Earned Leave</th>
                        </tr>
                        @foreach ($emp->employee_leave_list->where('leave_id',3) as $leave)
                        <tr>
                            <td>
                                {{ date('F Y', strtotime($leave->year.'-'.$leave->month)) }}
                            </td>
                            <td>{{ $leave->leave->leave_type }}</td>
                            <td>{{ $leave->earned_per_month }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            @php
                                $total_vl_credits = $emp->employee_leave_list->where('leave_id',3)->sum('earned_per_month');
                            @endphp
                            <td colspan="3"><b>Total:</b> {{ number_format($total_vl_credits,2) }}</td>
                        </tr>
                    </table>
                @endif

                @if(count($emp->employee_leave_list->where('leave_id',4)) > 0)
                    <table class="table table-bordered mb-2">
                        <tr>
                            <th>Month</th>
                            <th>Leave</th>
                            <th>Earned Leave</th>
                        </tr>
                        @foreach ($emp->employee_leave_list->where('leave_id',4) as $leave)
                        <tr>
                            <td>
                                {{ date('F Y', strtotime($leave->year.'-'.$leave->month)) }}
                            </td>
                            <td>{{ $leave->leave->leave_type }}</td>
                            <td>{{ $leave->earned_per_month }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            @php
                                $total_vl_credits = $emp->employee_leave_list->where('leave_id',4)->sum('earned_per_month');
                            @endphp
                            <td colspan="3"><b>Total:</b> {{ number_format($total_vl_credits,2) }}</td>
                        </tr>
                    </table>
                @endif

                @if(count($emp->employee_leave_list->where('leave_id',5)) > 0)
                    <table class="table table-bordered mb-2">
                        <tr>
                            <th>Month</th>
                            <th>Leave</th>
                            <th>Earned Leave</th>
                        </tr>
                        @foreach ($emp->employee_leave_list->where('leave_id',5) as $leave)
                        <tr>
                            <td>
                                {{ date('F Y', strtotime($leave->year.'-'.$leave->month)) }}
                            </td>
                            <td>{{ $leave->leave->leave_type }}</td>
                            <td>{{ $leave->earned_per_month }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            @php
                                $total_vl_credits = $emp->employee_leave_list->where('leave_id',5)->sum('earned_per_month');
                            @endphp
                            <td colspan="3"><b>Total:</b> {{ number_format($total_vl_credits,2) }}</td>
                        </tr>
                    </table>
                @endif

                @if(count($emp->employee_leave_list->where('leave_id',6)) > 0)
                    <table class="table table-bordered mb-2">
                        <tr>
                            <th>Month</th>
                            <th>Leave</th>
                            <th>Earned Leave</th>
                        </tr>
                        @foreach ($emp->employee_leave_list->where('leave_id',6) as $leave)
                        <tr>
                            <td>
                                {{ date('F Y', strtotime($leave->year.'-'.$leave->month)) }}
                            </td>
                            <td>{{ $leave->leave->leave_type }}</td>
                            <td>{{ $leave->earned_per_month }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            @php
                                $total_vl_credits = $emp->employee_leave_list->where('leave_id',6)->sum('earned_per_month');
                            @endphp
                            <td colspan="3"><b>Total:</b> {{ number_format($total_vl_credits,2) }}</td>
                        </tr>
                    </table>
                @endif

                @if(count($emp->employee_leave_list->where('leave_id',7)) > 0)
                    <table class="table table-bordered mb-2">
                        <tr>
                            <th>Month</th>
                            <th>Leave</th>
                            <th>Earned Leave</th>
                        </tr>
                        @foreach ($emp->employee_leave_list->where('leave_id',7) as $leave)
                        <tr>
                            <td>
                                {{ date('F Y', strtotime($leave->year.'-'.$leave->month)) }}
                            </td>
                            <td>{{ $leave->leave->leave_type }}</td>
                            <td>{{ $leave->earned_per_month }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            @php
                                $total_vl_credits = $emp->employee_leave_list->where('leave_id',7)->sum('earned_per_month');
                            @endphp
                            <td colspan="3"><b>Total:</b> {{ number_format($total_vl_credits,2) }}</td>
                        </tr>
                    </table>
                @endif

                @if(count($emp->employee_leave_list->where('leave_id',8)) > 0)
                    <table class="table table-bordered mb-2">
                        <tr>
                            <th>Month</th>
                            <th>Leave</th>
                            <th>Earned Leave</th>
                        </tr>
                        @foreach ($emp->employee_leave_list->where('leave_id',8) as $leave)
                        <tr>
                            <td>
                                {{ date('F Y', strtotime($leave->year.'-'.$leave->month)) }}
                            </td>
                            <td>{{ $leave->leave->leave_type }}</td>
                            <td>{{ $leave->earned_per_month }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            @php
                                $total_vl_credits = $emp->employee_leave_list->where('leave_id',8)->sum('earned_per_month');
                            @endphp
                            <td colspan="3"><b>Total:</b> {{ number_format($total_vl_credits,2) }}</td>
                        </tr>
                    </table>
                @endif

                @if(count($emp->employee_leave_list->where('leave_id',9)) > 0)
                    <table class="table table-bordered mb-2">
                        <tr>
                            <th>Month</th>
                            <th>Leave</th>
                            <th>Earned Leave</th>
                        </tr>
                        @foreach ($emp->employee_leave_list->where('leave_id',9) as $leave)
                        <tr>
                            <td>
                                {{ date('F Y', strtotime($leave->year.'-'.$leave->month)) }}
                            </td>
                            <td>{{ $leave->leave->leave_type }}</td>
                            <td>{{ $leave->earned_per_month }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            @php
                                $total_vl_credits = $emp->employee_leave_list->where('leave_id',9)->sum('earned_per_month');
                            @endphp
                            <td colspan="3"><b>Total:</b> {{ number_format($total_vl_credits,2) }}</td>
                        </tr>
                    </table>
                @endif

                @if(count($emp->employee_leave_list->where('leave_id',10)) > 0)
                    <table class="table table-bordered mb-2">
                        <tr>
                            <th>Month</th>
                            <th>Leave</th>
                            <th>Earned Leave</th>
                        </tr>
                        @foreach ($emp->employee_leave_list->where('leave_id',10) as $leave)
                        <tr>
                            <td>
                                {{ date('F Y', strtotime($leave->year.'-'.$leave->month)) }}
                            </td>
                            <td>{{ $leave->leave->leave_type }}</td>
                            <td>{{ $leave->earned_per_month }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            @php
                                $total_vl_credits = $emp->employee_leave_list->where('leave_id',10)->sum('earned_per_month');
                            @endphp
                            <td colspan="3"><b>Total:</b> {{ number_format($total_vl_credits,2) }}</td>
                        </tr>
                    </table>
                @endif

                @if(count($emp->employee_leave_list->where('leave_id',11)) > 0)
                    <table class="table table-bordered mb-2">
                        <tr>
                            <th>Month</th>
                            <th>Leave</th>
                            <th>Earned Leave</th>
                        </tr>
                        @foreach ($emp->employee_leave_list->where('leave_id',11) as $leave)
                        <tr>
                            <td>
                                {{ date('F Y', strtotime($leave->year.'-'.$leave->month)) }}
                            </td>
                            <td>{{ $leave->leave->leave_type }}</td>
                            <td>{{ $leave->earned_per_month }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            @php
                                $total_vl_credits = $emp->employee_leave_list->where('leave_id',11)->sum('earned_per_month');
                            @endphp
                            <td colspan="3"><b>Total:</b> {{ number_format($total_vl_credits,2) }}</td>
                        </tr>
                    </table>
                @endif

                
                @if(count($emp->employee_leave_list->where('leave_id',12)) > 0)
                    <table class="table table-bordered mb-2">
                        <tr>
                            <th>Month</th>
                            <th>Leave</th>
                            <th>Earned Leave</th>
                        </tr>
                        @foreach ($emp->employee_leave_list->where('leave_id',12) as $leave)
                        <tr>
                            <td>
                                {{ date('F Y', strtotime($leave->year.'-'.$leave->month)) }}
                            </td>
                            <td>{{ $leave->leave->leave_type }}</td>
                            <td>{{ $leave->earned_per_month }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            @php
                                $total_vl_credits = $emp->employee_leave_list->where('leave_id',12)->sum('earned_per_month');
                            @endphp
                            <td colspan="3"><b>Total:</b> {{ number_format($total_vl_credits,2) }}</td>
                        </tr>
                    </table>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
