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
                      <th>File</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($employeeNte as $en)
                      <tr>
                        <td>{{$en->employee->employee_number}}</td>
                        <td>{{$en->employee->employee_code}}</td>
                        <td>{{$en->employee->first_name.' '.$en->employee->last_name}}</td>
                        <td>{{$en->employee->department->name}}</td>
                        <td>
                          <a href="{{url($en->file_path)}}" target="_blank">
                            {{$en->file_name}}
                          </a>
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
@endsection
