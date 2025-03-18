@extends('layouts.header')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card grid-margin stretch-card">
                    <div class="card-body">
                        <h4 class="card-title">Notice of personnel and action</h4>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Employee Name</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($employee_movement)
                                        <tr>
                                            @php
                                                $old_values = json_decode($employee_movement->old_values);
                                                $old_department = $department->where('id', $old_values->department_id)->first();
                                                $old_immediate_sup = $employee->where('id', $old_values->immediate_sup)->first();

                                                $new_values = json_decode($employee_movement->new_values);
                                                $new_department = $department->where('id', $new_values->department_id)->first();
                                                $new_immediate_sup = $employee->where('id', $new_values->immediate_sup)->first();
                                            @endphp

                                            <td>{{ $employee_movement->employee->user_info->name }}</td>
                                            <td>
                                                <b>Department:</b> {{ $old_department->name }} <br>
                                                <b>Immediate Head:</b> {{ $old_immediate_sup->user_info->name }} <br>
                                                <b>Date From:</b> {{ date('M d, Y', strtotime($old_values->date_from)) }}
                                            </td>
                                            <td>
                                                <b>Department:</b> {{ $new_department->name }} <br>
                                                <b>Immediate Head:</b> {{ $new_immediate_sup->user_info->name }} <br>
                                                <b>Date From:</b> {{ date('M d, Y', strtotime($new_values->date_to)) }}
                                            </td>
                                            <td>
                                                <a href="{{url($employee_movement->nopa_attachment)}}" target="_blank">
                                                    <i class="ti-file"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="4">No data available.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection