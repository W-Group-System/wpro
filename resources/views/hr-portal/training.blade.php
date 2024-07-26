@extends('layouts.header')

@section('content')
  <div class="main-panel">
    <div class="content-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <div class="card grid-margin stretch-card">
            <div class="card-body">
              <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#addTrainingModal">Add Training</button>

              <div class="table-responsive">
                <table class="table table-hover table-bordered tablewithSearch">
                  <thead>
                    <tr>
                      <th>Employee Number</th>
                      <th>Employee Name</th>
                      <th>Company</th>
                      <th>Department</th>
                      <th>Position</th>
                      <th>Training Period</th>
                      <th>Bond Period</th>
                      <th>Amount</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($employeeTraining as $et)
                      <tr>
                        <td>{{$et->employee->employee_number}}</td>
                        <td>{{$et->employee->first_name.' '.$et->employee->middle_name.' '.$et->employee->last_name}}</td>
                        <td>{{$et->employee->company->company_name}}</td>
                        <td>{{$et->employee->department->name}}</td>
                        <td>{{$et->employee->position}}</td>
                        <td>
                          {{date('M. d, Y',strtotime($et->start_date))}} - {{date('M. d, Y',strtotime($et->end_date))}}
                          {{-- @php
                            $start_date = new DateTime($et->start_date);
                            $end_date = new DateTime($et->end_date);

                            $date_diff = $start_date->diff($end_date);
                            $s_y = $date_diff->format('%y') > 1 ? 's' : '';
                            $s_m = $date_diff->format('%m') > 1 ? 's' : '';
                          @endphp

                          {{$date_diff->format('%y Year'.$s_y.', '.'%m Month'.$s_m)}} --}}
                        </td>
                        <td>
                          {{date('M. d, Y',strtotime($et->bond_start_date))}} - {{date('M. d, Y',strtotime($et->bond_end_date))}}
                        </td>
                        <td><span>&#8369;</span>{{number_format($et->amount, 2)}}</td>
                        <td>
                          <button class="btn btn-warning btn-sm mb-1" type="button" data-toggle="modal" data-target="#editModal-{{$et->id}}">
                            <i class="ti-pencil-alt"></i>
                            Edit
                          </button>

                          <form action="{{url('delete-employee-training/'.$et->id)}}" method="post" onsubmit="show()">
                            {{csrf_field()}}
                            <input type="hidden" name="id" value="{{$et->id}}">
                            <button class="btn btn-danger btn-sm" type="submit">
                              <i class="ti-trash"></i>
                              Delete
                            </button>
                          </form>
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

  @include('hr-portal.new-training')

  @foreach ($employeeTraining as $et)
    @include('hr-portal.edit-training')
  @endforeach
@endsection
@section('footer')
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/js/inputmask.js') }}"></script>
@endsection
