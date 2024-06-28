@extends('layouts.header')

@section('content')
  <div class="main-panel">
    <div class="content-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <div class="card grid-margin stretch-card">
            <div class="card-body">
              <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#uploadNteModal">
                <i class="ti-plus"></i>
                Add Employee Benefits
              </button>

              <div class="table-responsive">
                <table class="table table-hover table-bordered tablewithSearch">
                  <thead>
                    <tr>
                      <th>Employee Name</th>
                      <th>Benefits</th>
                      <th>Amount</th>
                      <th>Date Posted</th>
                      <th>Posted By</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($employeeBenefits as $eb)
                      <tr>
                        <td>{{$eb->user->name}}</td>
                        <td>
                          @switch($eb->benefits_name)
                              @case('SL')
                                Salary Loan
                                  @break
                              @case('EA')
                                Educational Assistance
                                  @break
                              @case('WG')
                                Wedding Gift
                                  @break
                              @case('BA')
                                Bereavement Assistance
                                  @break
                              @case('HMO')
                                Health Card (HMO)
                                  @break
                              @default
                          @endswitch
                        </td>
                        <td><span>&#8369;</span>{{$eb->amount}}</td>
                        <td>{{date('M. d, Y', strtotime($eb->date))}}</td>
                        <td>{{$eb->postedBy->name}}</td>
                        <td>
                          <button class="btn btn-warning btn-sm mb-1" type="button" data-toggle="modal" data-target="#editModal-{{$eb->id}}">
                            <i class="ti-pencil-alt"></i>
                            Edit
                          </button>

                          <form action="{{url('delete-employee-benefits/'.$eb->id)}}" method="post">
                            {{csrf_field()}}

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

  @include('employee_benefits.add_employee_benefits')

  @foreach ($employeeBenefits as $eb)
    @include('employee_benefits.edit_employee_benefits')
  @endforeach
@endsection
@section('footer')
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/js/inputmask.js') }}"></script>
@endsection
