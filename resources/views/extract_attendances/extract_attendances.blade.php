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
                    <h4 class="card-title">Attendance Report</h4>
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
                                Type
                                <select name="type" class="form-control" required>
                                    <option value="">Select type</option>
                                    <option value="All Access" @if($type == 'All Access') selected @endif>All Access</option>
                                    <option value="First In Last Out" @if($type == 'First In Last Out') selected @endif>First In Last Out</option>
                                    <option value="Time In" @if($type == 'Time In') selected @endif>Time In</option>
                                </select>
                            </div>
                            <div class='col-md-2'>
                                From
                                <input type="date" name="date_from" class="form-control" value="{{ $from }}" min="{{ date('Y-m-d', strtotime('-1 weekday')) }}" max="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class='col-md-2'>
                                To
                                <input type="date" name="date_to" class="form-control" value="{{ $to }}" min="{{ date('Y-m-d', strtotime('-1 weekday')) }}" max="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class='col-md-4'>
                                <button type="submit" id="submitBtn" class="form-control btn btn-primary mb-2 btn-sm">Generate</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="attendance">
                            <thead>
                                <tr>
                                    <th>SNO</th>
                                    <th>Person ID</th>
                                    <th>Name</th>
                                    <th>Org</th>
                                    <th>Job Title</th>
                                    <th>Sex</th>
                                    <th>Date</th>
                                    <th>Day Of Week</th>
                                    <th>Time Period</th>
                                    <th>Records</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($emps as $key=>$emp)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $emp->employee_number }}</td>
                                        <td>{{ optional($emp->user_info)->name }}</td>
                                        <td></td>
                                        <td>{{ $emp->position }}</td>
                                        <td>{{ $emp->gender }}</td>
                                        <td>{{ $from }}</td>
                                        <td>{{ date('l', strtotime($from)) }}</td>
                                        <td>{{ $emp->schedule_info->schedule_name }}</td>
                                        <td>
                                            @if($type == 'All Access')
                                                @foreach (($emp->vms_attendance)->where('date_input', $from) as $attendance)
                                                    {{ $attendance->date_time }} <br>
                                                @endforeach
                                            @elseif($type == 'First In Last Out')
                                                @php
                                                    $first_in = ($emp->vms_attendance)->where('date_input', $from)->sortBy('date_time')->where('device_name', 'HO IN')->first();
                                                    $last_out = ($emp->vms_attendance)->where('date_input', $from)->sortByDesc('date_time')->where('device_name', 'HO OUT')->first();
                                                @endphp

                                                @if($first_in)
                                                {{ $first_in->date_time }} <br>
                                                @endif

                                                @if($last_out)
                                                {{ $last_out->date_time }}
                                                @endif
                                            @elseif($type == 'Time In')
                                                @foreach (($emp->vms_attendance)->where('date_input', $from)->where('device_name', 'HO IN') as $attendance)
                                                    {{ $attendance->date_time }} <br>
                                                @endforeach
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
@endsection


@section('js')
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script> 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function() {
        var type = "<?php echo $type ?>"
        var date = "<?php echo date('m-d-Y', strtotime($from)) ?>"
        
        $("#attendance").DataTable({
            paginate: false,
            sDom: 'Bfrtip',
            buttons: [
                // {
                //     extend: 'copy',
                //     title: 'Weekly Attendance Report'
                // },
                {
                    extend: 'excel',
                    title: type, // Sets the Excel title
                    filename: type + ' - ' + date// Formats filename
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