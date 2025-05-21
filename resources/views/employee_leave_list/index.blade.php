@extends('layouts.header')

@section('content')
	<div class="main-panel">
		<div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Employee Leave List</h4>
                            <p class="card-description">
                                <button type="button" class="btn btn-outline-success btn-icon-text" data-toggle="modal"
                                    data-target="#new">
                                    <i class="ti-plus btn-icon-prepend"></i>
                                    New Leave Credit
                                </button>
                            </p>
    
                            @if ($errors->any())
                                @foreach ($errors->all() as $error)
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ $error }}
                                    </div>
                                @endforeach
                            @endif
    
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered tablewithSearch">
                                    <thead>
                                        <tr>
                                            {{-- <th>Action</th> --}}
                                            <th>Employee</th>
                                            <th>Leave Entitlement</th>
                                            <th>Type of Leave</th>
                                            <th>Leave Credits</th>
                                            <th>Total Leaves</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employee_leave_lists as $employee_leave_list)
                                            <tr>
                                                {{-- <td>
                                                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit{{ $employee_leave_list->id }}" >
                                                        <i class="ti-pencil-alt"></i>
                                                    </button>
                                                </td> --}}
                                                <td>{{ $employee_leave_list->user->employee->employee_code . ' - ' .$employee_leave_list->user->name }}</td>
                                                <td>{{ get_leave_entitlement($employee_leave_list->user->employee->level, $employee_leave_list->user->employee->original_date_hired) }}</td>
                                                <td>{{ $employee_leave_list->leave->leave_type }}</td>
                                                <td>{{ $employee_leave_list->total_leaves }}</td>
                                                <td>{{ $employee_leave_list->earned_per_month }}</td>
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

@include('employee_leave_list.new_employee_leave_list')
{{-- @foreach ($employee_leave_lists as $employee_leave_list)
@include('employee_leave_list.edit_employee_leave_list')    
@endforeach --}}
<script>
    $(document).ready(function() {
        $("[name='employee']").on('change', function() {
            var value = $(this).val()
    
            $.ajax({
                type: "POST",
                url: "{{ url('refresh_employee') }}",
                data: {
                    employee_id: value,
                    _token: "{{ csrf_token() }}"
                },
                success:function(res) {
                    $("[name='level']").val(res.level).trigger('change')
                    $("[name='date_hired']").val(res.original_date_hired)
                }
            })
        })

        $("[name='date_regularization']").on('change', function() {
            var employee = $("[name='employee']").val()
            var leave = $("[name='leave']").val()
            var level = $("[name='level']").val()
            var dateHired = $("[name='date_hired']").val()
            var dateRegularization = $(this).val() 

            $.ajax({
                type: "POST",
                url: "{{ url('refresh_leave_credit') }}",
                data: {
                    employee: employee,
                    leave: leave,
                    level: level,
                    date_hired: dateHired,
                    date_regularization: dateRegularization,
                    _token: "{{ csrf_token() }}",
                },
                success: function(data)
                {
                    $("[name='leave_credit']").val(data)
                }
            })
        })
    })
</script>
@endsection
