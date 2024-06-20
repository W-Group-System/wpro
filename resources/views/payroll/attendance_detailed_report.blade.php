@extends('layouts.header')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
            @if (count($errors))
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger alert-dismissible fade show " role="alert">
                        <span class="alert-inner--icon"><i class="ni ni-fat-remove"></i></span>
                        <span class="alert-inner--text"><strong>Error!</strong> {{ $error }}</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endforeach
            @endif
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Attendance Detailed Report </h4>
                <form method='get' onsubmit='show();'  enctype="multipart/form-data">
                    <div class=row>
                        <div class='col-md-3'>
                            <div class="form-group">
                                <select data-placeholder="Select Company" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='company' required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach($companies as $comp)
                                    <option value="{{$comp->id}}" @if ($comp->id == $company) selected @endif>{{$comp->company_name}} - {{$comp->company_code}}</option>
                                    @endforeach
                                </select>
                            </div>
                      </div>
                      <div class='col-md-3'>
                        <div class="form-group row">
                          <label class="col-sm-4 col-form-label text-right">From</label>
                          <div class="col-sm-8">
                            <input type="date" value='{{$from_date}}' class="form-control" name="from" max='{{date('Y-m-d')}}' onchange='get_min(this.value);' required/>
                          </div>
                        </div>
                      </div>
                      <div class='col-md-3'>
                        <div class="form-group row">
                          <label class="col-sm-4 col-form-label text-right">To</label>
                          <div class="col-sm-8">
                            <input type="date" value='{{$to_date}}'  class="form-control"  id='to' name="to" required/>
                          </div>
                        </div>
                      </div>
                      <div class='col-md-3'>
                        <button type="submit" class="btn btn-primary mb-2">Submit</button>
                      </div>
                    </div>
                    </form>
                <div class="table-responsive">
                  <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Employee #</th>
                            <th>Name</th>
                            <th>Log Date</th>
                            <th>Shift</th>
                            <th>IN</th>
                            <th>OUT</th>
                            <th>ABS</th>
                            <th>LV W/ PAY</th>
                            <th>REG HRS</th>
                            <th>LATE (min)</th>
                            <th>Undertime (min)</th>
                            <th>REG OT</th>
                            <th>REG ND</th>
                            <th>REG OT ND</th>
                            <th>RST OT</th>
                            <th>RST OT> 8</th>
                            <th>RST ND</th>
                            <th>RST ND>8</th>
                            <th>LH OT</th>
                            <th>LH OT>8</th>
                            <th>LH ND</th>
                            <th>LH ND>8</th>
                            <th>SH OT</th>
                            <th>SH OT>8</th>
                            <th>SH ND</th>
                            <th>SH ND>8</th>
                            <th>RST LH OT</th>
                            <th>RST LH OT>8</th>
                            <th>RST LH ND</th>
                            <th>RST LH ND>8</th>
                            <th>RST SH OT</th>
                            <th>RST SH OT>8</th>
                            <th>RST SH ND</th>
                            <th>RST SH ND>8</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    @foreach($generated_timekeepings as $timekeeping)
                    <tbody>
                        <tr>
                            <td>{{$timekeeping->company->company_code}}</td>
                            <td>{{$timekeeping->employee_no}}</td>
                            <td>{{$timekeeping->name}}</td>
                            <td>{{$timekeeping->log_date}}</td> 
                            <td>{{ $timekeeping->shift }}</td>
                            <td>{{ $timekeeping->in }}</td>
                            <td>{{ $timekeeping->out }}</td>
                            <td>{{ $timekeeping->abs }}</td>
                            <td>{{ $timekeeping->lv_w_pay }}</td>
                            <td>{{ $timekeeping->reg_hrs }}</td>
                            <td>{{ $timekeeping->late_min }}</td>
                            <td>{{ $timekeeping->undertime_min }}</td>
                            <td>{{ $timekeeping->reg_ot }}</td>
                            <td>{{ $timekeeping->reg_nd }}</td>
                            <td>{{ $timekeeping->reg_ot_nd }}</td>
                            <td>{{ $timekeeping->rst_ot }}</td>
                            <td>{{ $timekeeping->rst_ot_over_eight }}</td>
                            <td>{{ $timekeeping->rst_nd }}</td>
                            <td>{{ $timekeeping->rst_nd_over_eight }}</td>
                            <td>{{ $timekeeping->lh_ot }}</td>
                            <td>{{ $timekeeping->lh_ot_over_eight }}</td>
                            <td>{{ $timekeeping->lh_nd }}</td>
                            <td>{{ $timekeeping->lh_nd_over_eight }}</td>
                            <td>{{ $timekeeping->sh_ot }}</td>
                            <td>{{ $timekeeping->sh_ot_over_eight }}</td>
                            <td>{{ $timekeeping->sh_nd }}</td>
                            <td>{{ $timekeeping->sh_nd_over_eight }}</td>
                            <td>{{ $timekeeping->rst_lh_ot }}</td>
                            <td>{{ $timekeeping->rst_lh_ot_over_eight }}</td>
                            <td>{{ $timekeeping->rst_lh_nd }}</td>
                            <td>{{ $timekeeping->rst_lh_nd_over_eight }}</td>
                            <td>{{ $timekeeping->rst_sh_ot }}</td>
                            <td>{{ $timekeeping->rst_sh_ot_over_eight }}</td>
                            <td>{{ $timekeeping->rst_sh_nd }}</td>
                            <td>{{ $timekeeping->rst_sh_nd_over_eight }}</td>
                        </tr>
                    </tbody>
                    @endforeach
                  </table>
                </div>
              </div>
            </div>
           </div>
    </div>
</div>
    {{-- @foreach($attendances as $att)
        @include('payroll.view_attendances')   
    @endforeach
    @include('payroll.upload_attendance')  --}}
@endsection

