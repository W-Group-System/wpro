@extends('layouts.header')

@section('content')
  <div class="main-panel">
    <div class="content-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <div class="card grid-margin stretch-card">
            <div class="card-body">
              <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#addModal">
                Add
              </button>

              <div class="table-responsive">
                <table class="table table-hover table-bordered tablewithSearch">
                  <thead>
                    <tr>
                      <th>Employee Code</th>
                      <th>Employee Name</th>
                      <th>Company</th>
                      <th>Department</th>
                      <th>SSS</th>
                      <th>PAGIBIG</th>
                      <th>PHILHEALTH</th>
                      <th>TIN</th>
                      <th>Tax Percent</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($taxMapping as $tm)
                      <tr>
                        <td>{{$tm->employee->employee_code}}</td>
                        <td>{{$tm->employee->first_name.' '.$tm->employee->last_name}}</td>
                        <td>{{$tm->employee->company->company_name}}</td>
                        <td>{{$tm->employee->department->name}}</td>
                        <td>{{$tm->sss==1?'YES':'NO'}}</td>
                        <td>{{$tm->pagibig==1?'YES':'NO'}}</td>
                        <td>{{$tm->philhealth==1?'YES':'NO'}}</td>
                        <td>{{$tm->tin==1?'YES':'NO'}}</td>
                        <td>
                          @php
                            $tax_percent = $tm->tax_percent * 100;
                          @endphp
                          {{number_format($tax_percent,0)}}%
                        </td>
                        <td>
                          <button class="btn btn-sm btn-warning mb-1" title="Edit" data-toggle="modal" data-target="#editModal-{{$tm->id}}">
                            <i class="ti-eye"></i>
                            Edit
                          </button>

                          <form action="{{url('delete-tax-mapping/'.$tm->id)}}" method="post" onsubmit="show()">
                            {{csrf_field()}}
                            <button class="btn btn-sm btn-danger" title="Delete">
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

  @include('payroll_settings.new-tax-mapping')

  @foreach ($taxMapping as $tm)
    @include('payroll_settings.edit-tax-mapping')
  @endforeach
@endsection

@section('footer')
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/js/inputmask.js') }}"></script>
@endsection
