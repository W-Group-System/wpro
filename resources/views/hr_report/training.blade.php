@extends('layouts.header')

@section('content')
  <div class="main-panel">
    <div class="content-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <div class="card grid-margin stretch-card">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-bordered tablewithSearch">
                  <thead>
                    <tr>
                      <th>Employee Number</th>
                      <th>Employee Code</th>
                      <th>Employee Name</th>
                      <th>Department</th>
                      <th>Training</th>
                      <th>Start Date</th>
                      <th>End Date</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($employeeTraining as $et)
                      <tr>
                        <td>{{$et->employee->employee_number}}</td>
                        <td>{{$et->employee->employee_code}}</td>
                        <td>{{$et->employee->first_name.' '.$et->employee->last_name}}</td>
                        <td>{{$et->employee->department->name}}</td>
                        <td>{{$et->training}}</td>
                        <td>{{date('M. d, Y', strtotime($et->start_date))}}</td>
                        <td>{{date('M. d, Y', strtotime($et->end_date))}}</td>
                        <td><span>&#8369;</span>{{$et->amount}}</td>
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
@endsection
