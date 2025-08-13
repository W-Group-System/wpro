<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Timekeeping Beta</title>
    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    {{-- Datatable CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.min.css">
    {{-- Date Picker CSS --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
    {{-- Bootstrap Icon --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Select2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Or for RTL support -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
</head>

<body>
    <div class="container-fluid p-4">
        <h2>TIMEKEEPING MONITORING</h2>
        <form method="get">
            <div class="row mt-4">
                <div class="col-md-2">
                    <input class="datepicker form-control" name="date_from" data-date-format="mm/dd/yyyy"
                        placeholder="Date From" autocomplete="off" value="{{ $from_date != "1970-01-01" ? $from_date : null }}" required>
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-th"></span>
                    </div>
                </div>
                <div class="col-md-2">
                    <input class="datepicker form-control" name="date_to" data-date-format="mm/dd/yyyy"
                        placeholder="Date To" autocomplete="off" value="{{ $to_date != "1970-01-01" ? $to_date : null }}" required>
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-th"></span>
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select select2" name="company" data-placeholder="Select company" required>
                        <option></option>
                        @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @if($company_data == $company->id) selected @endif>{{$company->company_code.' - '.$company->company_name }}
                        </option>
                        @endforeach
                    </select>
                </div>  
                <div class="col-md-2">
                    <select class="form-select select2" name="department" data-placeholder="Select department">
                        <option></option>
                        @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @if($department_data == $department->id) selected @endif>{{$department->code.' - '.$department->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select select2" name="employee" data-placeholder="Select employee name">
                        <option></option>
                        @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" @if($employee_data == $employee->id) selected @endif>{{$employee->last_name.' '.$employee->first_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        <div class="row mt-5">
            <div class="d-flex align-items-center">
                <div class="bg-danger" style="width: 15px; height: 15px; margin-right: 5px;"></div>
                <span>Absent</span>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered mt-5" id="myTable">
                        <thead>
                            <tr>
                                <th></th>
                                <th>COMPANY</th>
                                <th>DEPARTMENT</th>
                                <th>SCHEDULE</th>
                                <th>EMPLOYEE ID</th>
                                <th>NAME</th>
                                <th>DATE LOGS</th>
                                <th>TIME IN</th>
                                <th>TIME OUT</th>
                                <th>TOTAL HRS</th>
                                <th>TOTAL LATE</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @dd($employees[25]) --}}
                            @foreach ($employees as $employee)
                                @foreach ($date_range as $date_r)
                                    @php
                                        $employee_schedule = employeeSchedule($employee->ScheduleData,$date_r,$employee->schedule_id,$employee->employee_code);
                                        $time_in = ($employee->timekeeping_logs)->where('date', $date_r)->sortBy('id')->first();
                                        $time_out = ($employee->timekeeping_logs)->where('date', $date_r)->sortByDesc('id')->first();
                                        $total_reg_hrs = 0;
                                        $total_late = 0;
                                    @endphp

                                    <tr>
                                        <td>
                                            <input type="checkbox" name="" id="">
                                        </td>
                                        <td>{{ $employee->company->company_code }}</td>
                                        <td>{{ $employee->department->name }}</td>
                                        <td>
                                            @php
                                            @endphp
                                            @if($employee_schedule)
                                                {{ $employee_schedule->schedule_info->schedule_name }}
                                            @else
                                                RESTDAY
                                            @endif
                                        </td>
                                        <td>{{ $employee->employee_code }}</td>
                                        <td>{{ $employee->last_name.', '.$employee->first_name }}</td>
                                        <td>{{ $date_r }}</td>
                                        <td @if(empty($time_in)) class="bg-danger" @endif>
                                            @if($time_in)
                                                {{ date('h:i A', strtotime($time_in->datetime)) }}
                                            @endif
                                        </td>
                                        <td  @if(empty($time_out)) class="bg-danger" @endif>
                                            @if($time_out)
                                                {{ date('h:i A', strtotime($time_out->datetime)) }}
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                if ($employee_schedule)
                                                {
                                                    $start_time = strtotime($employee_schedule->time_in_from);
                                                    $end_time = strtotime($employee_schedule->time_out_from);
                                                    $reg_hrs = ($end_time - $start_time) / 3600;
                                                    $total_reg_hrs = $reg_hrs - 1;
                                                }
                                            @endphp
                                            {{ number_format($total_reg_hrs,2) }}
                                        </td>
                                        <td>
                                            @php
                                                
                                                if ($employee_schedule)
                                                {
                                                    if ($time_in)
                                                    {
                                                        if (date('H:i', strtotime($time_in->datetime)) > $employee_schedule->time_in_to)
                                                        {
                                                            $late_time_in = strtotime(date('H:i', strtotime($time_in->datetime)));
                                                            $late_time_in_to = strtotime(date('H:i', strtotime($employee_schedule->time_in_to)));
                                                            $total_late = abs($late_time_in_to - $late_time_in) / 60;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            {{ number_format($total_late,0) }}
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#edit{{ $employee->id }}{{ $date_r }}"><i class="bi bi-pencil-square h3 text-dark"></i></a>
                                        </td>
                                    </tr>

                                    @include('edit_timekeeping')
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('sweetalert::alert')
    {{-- Jquery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    {{-- Datatable JS --}}
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.min.js"></script>
    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous">
    </script>
    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    {{-- Date Picker JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js">
    </script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy/mm/dd',
        })
        $( '.select2' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        } );

        let table = new DataTable('#myTable');
    </script>
</body>

</html>