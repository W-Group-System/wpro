@extends('layouts.header')

@section('css_header')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">
@endsection

@section('content')
	<div class="main-panel">
		<div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Leave Report (as of {{ date('M Y') }})</h4>
                            {{-- <p class="card-description">
                                <button type="button" class="btn btn-outline-success btn-icon-text" data-toggle="modal"
                                    data-target="#new">
                                    <i class="ti-plus btn-icon-prepend"></i>
                                    New Leave Credit
                                </button>
                            </p> --}}
    
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered " id="leaveReportTable">
                                    <thead>
                                        <tr>
                                            <th>Employee ID</th>
                                            <th>Employee Name</th>
                                            <th>Leave Type</th>
                                            <th>Leave Entitlement</th>
                                            <th>Used Leave</th>
                                            <th>Leave Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($merge_arr->sortBy('lastname') as $employee)
                                            <tr>
                                                <td>{{ $employee->employee_id }}</td>
                                                <td>{{ $employee->name }}</td>
                                                <td>{{ $employee->leave_type }}</td>
                                                <td>{{$employee->leave_entitlement }}</td>
                                                <td>{{ $employee->used_leave }}</td>
                                                <td>
                                                    {{-- 15 > 15 --}}
                                                    @if($employee->leave_type == 'Sick Leave')
                                                        @if($employee->total_earned_sl > $employee->used_leave)
                                                            {{ round($employee->total_earned_sl - $employee->used_leave, 2) }}
                                                        @else
                                                            0
                                                        @endif
                                                    @else
                                                        {{-- @dd($employee->total_earned_vl) --}}
                                                        @if($employee->total_earned_vl > $employee->used_leave)
                                                            {{ round($employee->total_earned_vl - $employee->used_leave, 2) }}
                                                        @else
                                                            0
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script>
    $(document).ready(function() {
        $("#leaveReportTable").DataTable({
            paginate: false,
            sDom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    title: 'Leave Report'
                },
                {
                    extend: 'excel',
                    title: 'Leave Report', // Sets the Excel title
                    filename: 'Leave Report'// Formats filename
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
