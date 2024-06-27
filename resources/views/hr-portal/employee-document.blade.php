@extends('layouts.header')
@section('css_header')
  <style>
    .pagination {
      float: right;
    }
  </style>
@endsection

@section('content')
  <div class="main-panel">
    <div class="content-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <div class="card grid-margin stretch-card">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-bordered mb-4">
                  <thead>
                    <tr>
                      <th>Employee Number</th>
                      <th>Employee Name</th>
                      <th>Company</th>
                      <th>Department</th>
                      <th>Position</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($employee as $emp)
                      <tr>
                        <td>{{$emp->employee_number}}</td>
                        <td>{{$emp->first_name.' '.$emp->middle_name.' '.$emp->last_name}}</td>
                        <td>{{$emp->company->company_name}}</td>
                        <td>{{isset($emp->department->name)?$emp->department->name:''}}</td>
                        <td>{{$emp->position}}</td>
                        <td>
                          <button class="btn btn-warning btn-sm" type="button" data-toggle="modal" data-target="#editModal-{{$emp->id}}">
                            <i class="ti-pencil-alt"></i>
                            Edit
                          </button>

                          <button class="btn btn-info btn-sm" type="button" data-toggle="modal" data-target="#viewModal-{{$emp->id}}">
                            <i class="ti-eye"></i>
                            View
                          </button>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>

                {!! $employee->links() !!}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @foreach ($employee as $emp)
    @include('hr-portal.edit-employee-document')
    @include('hr-portal.view-employee-document')
  @endforeach
@endsection
@section('footer')
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/js/inputmask.js') }}"></script>
@endsection
