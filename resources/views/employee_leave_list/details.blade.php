<div class="modal fade" id="viewDetails{{ $emp->id }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View details</h5>
            </div>
            <div class="modal-body">
                @if(count($emp->employee_leave_list->where('leave_id',1)) > 0)
                    <table class="table table-bordered mb-2">
                        <tr>
                            <th>Month</th>
                            <th>Leave</th>
                            <th>Earned Leave</th>
                        </tr>
                        @foreach ($emp->employee_leave_list->where('leave_id',1) as $leave)
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
                                $total_vl_credits = $emp->employee_leave_list->where('leave_id',1)->sum('earned_per_month');
                            @endphp
                            <td colspan="3"><b>Total:</b> {{ number_format($total_vl_credits,2) }}</td>
                        </tr>
                    </table>
                @endif

                @if(count($emp->employee_leave_list->where('leave_id',2)) > 0)
                    <table class="table table-bordered mb-2">
                        <tr>
                            <th>Month</th>
                            <th>Leave</th>
                            <th>Earned Leave</th>
                        </tr>
                        @foreach ($emp->employee_leave_list->where('leave_id',2) as $leave)
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
                                $total_vl_credits = $emp->employee_leave_list->where('leave_id',2)->sum('earned_per_month');
                            @endphp
                            <td colspan="3"><b>Total:</b> {{ number_format($total_vl_credits,2) }}</td>
                        </tr>
                    </table>
                @endif

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