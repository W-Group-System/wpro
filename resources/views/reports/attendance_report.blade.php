@extends('layouts.header')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Attendance Reports</h4>
                    <p class="card-description">
                    <form method='GET' onsubmit="show()" action="{{ route('reports') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                        <div class='col-md-2'>
                            From
                            <input type="date" class="form-control form-control-sm" value='{{$from}}' id='from' name="from" required />
                        </div>
                        <div class='col-md-2'>
                            To
                            <input type="date" class="form-control form-control-sm" value='{{$to}}' id='to' name="to" required />
                        </div>
                        <div class='col-md-2'>
                            Greater Than (Count)
                            <input type="number" class="form-control form-control-sm" value='{{$count}}' id='count' min=1 name="count" required />
                        </div>
                            {{-- <div class='col-md-4'>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label text-right" for="monthSelect">Date From</label>
                                    <div class="col-sm-8">
                                        <select id="monthSelect" name="month" class="selectpicker form-control" data-live-search="true" title="Choose a month">
                                            <option value="01">January</option>
                                            <option value="02">February</option>
                                            <option value="03">March</option>
                                            <option value="04">April</option>
                                            <option value="05">May</option>
                                            <option value="06">June</option>
                                            <option value="07">July</option>
                                            <option value="08">August</option>
                                            <option value="09">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class='col-md-4'>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label text-right" for="yearSelect">Year</label>
                                    <div class="col-sm-8">
                                        <select id="yearSelect" name="year" class="selectpicker form-control" data-live-search="true" title="Choose a year">
                                            <!-- Dynamically populate year options with JavaScript -->
                                        </select>
                                    </div>
                                </div>
                            </div> --}}
                            <div class='col-md-4'>
                                <button type="submit" id="submitBtn" class="form-control btn btn-primary mb-2 btn-sm">Generate</button>
                            </div>
                        </div>
                    </form>
                    </p>
                    <div class="row col-md-12 mb-3">
                        <div class="col-md-3" style="margin-top: 5px;">
                            <h3 id="reportTitle">{{date('M d, Y',strtotime($from))}} - {{date('M d, Y',strtotime($to))}}</h3> 
                        </div>
                        <div class="col-md-9">
                            <a href="{{ url('/attendance-report?from=' . $from . '&to=' . $to .'&count=' .$count. '&type=pdf') }}" target="_blank" class='btn btn-success btn-sm'><i class="fa fa-print btn-icon-append"></i>&nbsp;Print</a>
                        </div>
                    </div>
                    <div class="col-12">
                        {{-- <h3 id="reportTitle"></h3> <a href="{{url('/attendance-report?form='.$from.'&year='.$to.'&type=pdf')}}" target="_blank" class='btn btn-danger btn-sm' >Print</a><br> --}}

                        <label><b>I. Tardiness</b></label>
                        <table class="table table-hover table-bordered">
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
                                @foreach($tardinessData as $index => $tardiness)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{$tardiness['employee_no']}}</td>
                                        <td>{{ $tardiness['company_code'] }}</td>
                                        <td>{{ $tardiness['name'] }}</td>
                                        <td>{{ $tardiness['tardiness_days'] }}</td>
                                        <td>Excessive; for NOD issuance</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <label><b>II. Undertime</b></label>
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>No.</th>
                                    <th>Company</th>
                                    <th>Name</th>
                                    <th>No. of Days</th>
                                    <th>Remarks/ Recommendation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($undertimeData as $index => $undertime)
                                    <tr>
                                        <td>{{$undertime['employee_no']}}</td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $undertime['company_code'] }}</td>
                                        <td>{{ $undertime['name'] }}</td>
                                        <td>{{ $undertime['undertime_days'] }}</td>
                                        <td>Excessive; for NOD issuance</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <label style="margin-bottom: 20px;"><b>III. Leaves</b></label><br>
                        <label>A. Leave without Pay</label>
                        <table class="table table-hover table-bordered">
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
                                @foreach($leaveWithoutData as $index => $withoutLeave)
                                    <tr>
                                        <td>{{$withoutLeave['employee_no']}}</td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $withoutLeave['company_code'] }}</td>
                                        <td>{{ $withoutLeave['name'] }}</td>
                                        <td>{{ $withoutLeave['no_lwop_days'] }}</td>
                                        <td></td>
                                        <td>No leave Credits balance</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <label>B. Leave Deviations</label>
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>No.</th>
                                    <th>Company</th>
                                    <th>Name</th>
                                    <th>Leave Date(s)</th>
                                    <th>Leave Type</th>
                                    <th>Remarks/ Recommendation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaveDeviationsData as $index => $leaveDeviations)
                                    <tr>
                                        <td>{{$leaveDeviations['employee_no']}}</td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $leaveDeviations['company_code'] }}</td>
                                        <td>{{ $leaveDeviations['name'] }}</td>
                                        <td>{{ $leaveDeviations['leave_date'] }}</td> 
                                        <td>
                                            @foreach($leaveDeviations['leave_types'] as $leaveType)
                                                {{ $leaveType == 1 ? 'Vacation Leave' : 'Sick Leave' }}
                                                <br>
                                            @endforeach
                                        </td>
                                        <td></td> 
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- <label>C. Leaves more than 5 consecutive days</label>


                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Company</th>
                                    <th>Name</th>
                                    <th>Leave Date(s)</th>
                                    <th>Leave Type</th>
                                    <th>Remarks/ Recommendation</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach($consecLeaveData as $index => $consecLeave)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $consecLeave['company_code'] }}</td>
                                        <td>{{ $consecLeave['name'] }}</td>
                                        <td></td> 
                                        <td>
                                            @foreach($consecLeave['leave_types'] as $leaveTypeGroup)
                                                @foreach($leaveTypeGroup as $leaveType)
                                                    {{ $leaveType == 1 ? 'Vacation Leave' : 'Sick Leave' }}
                                                    <br>
                                                @endforeach
                                            @endforeach
                                        </td>
                                        <td></td> 
                                    </tr>
                                @endforeach --}}
                            </tbody>
                        </table>
                        <label><b>IV. Overtime</b></label>
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    {{-- <th>No.</th> --}}
                                    <th>Company</th>
                                    <th>Regular Working Hours</th>
                                    <th>Overtime Hours Total</th>
                                    <th>% of Overtime</th>
                                    <th>Remarks/ Recommendation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overtimeData as $index => $overtime)
                                    <tr>
                                        <td>{{$overtime['employee_no']}}</td>
                                        {{-- <td>{{ $loop->iteration }}</td> --}}
                                        <td>{{ $overtime['company_code'] }}</td>
                                        <td>{{ number_format($overtime['total_reg_hrs'], 2) }}</td>
                                        <td>{{ number_format($overtime['total_ot'], 2) }}</td>
                                        <td>{{ number_format($overtime['percent_overtime'], 2) }}%</td>
                                        <td>{{ $overtime['remarks'] }}</td>
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
