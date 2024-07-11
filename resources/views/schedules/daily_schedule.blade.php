@extends('layouts.header')
@section('css_header')
<style>
    .pagination {
        margin-top: 15px;
        float: right;
    }
</style>
@endsection
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        @if($errors->any())
        <div class="form-group alert alert-danger alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <strong>{{$errors->first()}}</strong>
        </div>
        @endif
        <div class='row'>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Daily Schedules</h4>
                        <p class="card-description">
                            <button type="button" class="btn btn-outline-warning btn-icon-text" data-toggle="modal"
                                data-target="#uploadSchedule">
                                <i class="ti-plus btn-icon-prepend"></i>
                                Upload Schedule
                            </button>
                            <a href="{{url('export-schedule')}}" class="btn btn-outline-success btn-icon-text">
                                <i class="ti-plus btn-icon-prepend"></i>
                                Export Template
                            </a>
                        </p>
                        <form action="" method="get" onsubmit="show();">
                            <div class="row">
                                <div class="col-md-3">
                                    Employee
                                    <select name="employee" class="form-control js-example-basic-single" required>
                                        <option value="">-Employee-</option>
                                        @foreach ($employee as $emp)
                                        <option value="{{$emp->employee_number}}" {{$empNum==$emp->
                                            employee_number?'selected':''}}>{{$emp->employee_code}}-{{$emp->first_name.'
                                            '.$emp->last_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Date From
                                    <input type="date" name="date_from" class="form-control" value="{{$dateFrom}}"
                                        required>
                                </div>
                                <div class="col-md-3">
                                    Date To
                                    <input type="date" name="date_to" class="form-control" value="{{$dateTo}}" required>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Employee Number</th>
                                        <th>Employee Code</th>
                                        <th>Name</th>
                                        <th>Log Date</th>
                                        <th>Time In From</th>
                                        <th>Time In To</th>
                                        <th>Time Out From</th>
                                        <th>Time Out To</th>
                                        <th>Working Hours</th>
                                        {{-- <th>Actions</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dailySchedule as $ds)
                                    <tr>
                                        <td>{{$ds->company}}</td>
                                        <td>{{$ds->employee_number}}</td>
                                        <td>{{$ds->employee_code}}</td>
                                        <td>{{$ds->employee_name}}</td>
                                        <td>{{date('M d, Y - l', strtotime($ds->log_date))}}</td>
                                        <td>{{$ds->time_in_from != "00:00" ? date('h:i A', strtotime($ds->time_in_from))
                                            : 'Rest Day'}}</td>
                                        <td>{{$ds->time_in_to != "00:00" ? date('h:i A', strtotime($ds->time_in_to)) :
                                            'Rest Day'}}</td>
                                        <td>{{$ds->time_out_from != "00:00" ? date('h:i A',
                                            strtotime($ds->time_out_from)) : 'Rest Day'}}</td>
                                        <td>{{$ds->time_out_to != "00:00" ? date('h:i A', strtotime($ds->time_out_to)) :
                                            'Rest Day'}}</td>
                                        <td>{{$ds->working_hours!=null?$ds->working_hours:'0.00'}}</td>
                                        {{-- <td>
                                            <button type="button" class="btn btn-outline-info btn-icon-text btn-sm"
                                                data-toggle="modal" data-target="#editDailySchedule-{{$ds->id}}">
                                                Edit
                                                <i class="ti-file btn-icon-append"></i>
                                            </button>
                                        </td> --}}
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {!! $dailySchedule->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('schedules.upload_daily_schedule')
@foreach ($dailySchedule as $ds)
@include('schedules.edit_daily_schedule')
@endforeach
@endsection