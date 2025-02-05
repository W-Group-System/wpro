@extends('layouts.header')

@section('content')

<div class="main-panel">
  <div class="content-wrapper">
    
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Attendance Detailed Report </h4>
          <form method='get' onsubmit='show();' enctype="multipart/form-data">
            <div class=row>
              <div class='col-md-3'>
                <div class="form-group">
                  <select data-placeholder="Select Company" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='company' required>
                    <option value="">-- Select Employee --</option>
                    @foreach($companies as $comp)
                    <option value="{{$comp->id}}" @if ($comp->id == $company) selected @endif>{{$comp->company_code}}</option>
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
            <table class="table table-hover table-bordered table-detailed">
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
                      <th>RST OT > 8</th>
                      <th>RST ND</th>
                      <th>RST ND > 8</th>
                      <th>LH OT</th>
                      <th>LH OT > 8</th>
                      <th>LH ND</th>
                      <th>LH ND > 8</th>
                      <th>SH OT</th>
                      <th>SH OT > 8</th>
                      <th>SH ND</th>
                      <th>SH ND > 8</th>
                      <th>RST LH OT</th>
                      <th>RST LH OT > 8</th>
                      <th>RST LH ND</th>
                      <th>RST LH ND > 8</th>
                      <th>RST SH OT</th>
                      <th>RST SH OT > 8</th>
                      <th>RST SH ND</th>
                      <th>RST SH ND > 8</th>
                      <th>Remarks</th>
                  </tr>
              </thead>
              <tbody>
                  @php
                  $subtotals = [
                      'abs' => 0,
                      'lv_w_pay' => 0,
                      'reg_hrs' => 20,
                      'late_min' => 0,
                      'undertime_min' => 0,
                      'reg_ot' => 0,
                      'reg_nd' => 0,
                      'reg_ot_nd' => 0,
                      'rst_ot' => 0,
                      'rst_ot_over_eight' => 0,
                      'rst_nd' => 0,
                      'rst_nd_over_eight' => 0,
                      'lh_ot' => 0,
                      'lh_ot_over_eight' => 0,
                      'lh_nd' => 0,
                      'lh_nd_over_eight' => 0,
                      'sh_ot' => 0,
                      'sh_ot_over_eight' => 0,
                      'sh_nd' => 0,
                      'sh_nd_over_eight' => 0,
                      'rst_lh_ot' => 0,
                      'rst_lh_ot_over_eight' => 0,
                      'rst_lh_nd' => 0,
                      'rst_lh_nd_over_eight' => 0,
                      'rst_sh_ot' => 0,
                      'rst_sh_ot_over_eight' => 0,
                      'rst_sh_nd' => 0,
                      'rst_sh_nd_over_eight' => 0
                  ];
                  $totals = [
                    'abs' => 0,
                    'lv_w_pay' => 0,
                    'reg_hrs' => 0,
                    'late_min' => 0,
                    'undertime_min' => 0,
                    'reg_ot' => 0,
                    'reg_nd' => 0,
                    'reg_ot_nd' => 0,
                    'rst_ot' => 0,
                    'rst_ot_over_eight' => 0,
                    'rst_nd' => 0,
                    'rst_nd_over_eight' => 0,
                    'lh_ot' => 0,
                    'lh_ot_over_eight' => 0,
                    'lh_nd' => 0,
                    'lh_nd_over_eight' => 0,
                    'sh_ot' => 0,
                    'sh_ot_over_eight' => 0,
                    'sh_nd' => 0,
                    'sh_nd_over_eight' => 0,
                    'rst_lh_ot' => 0,
                    'rst_lh_ot_over_eight' => 0,
                    'rst_lh_nd' => 0,
                    'rst_lh_nd_over_eight' => 0,
                    'rst_sh_ot' => 0,
                    'rst_sh_ot_over_eight' => 0,
                    'rst_sh_nd' => 0,
                    'rst_sh_nd_over_eight' => 0
                ];
                  $currentEmployeeNo = null;
                  $currentEmployeeName = null;
                  // $currentEmployeeLogdate =null;
                  // $currentEmployeeShift = null;
                  @endphp
          
                  @foreach($generated_timekeepings as $timekeeping)
                      @if($currentEmployeeNo !== $timekeeping->employee_no)
                          @if(!is_null($currentEmployeeNo))
                          <tr class="subtotal-row">
                              <td><strong>Subtotal</strong></td>
                              <td><strong>{{ $currentEmployeeNo }}</strong></td>
                              <td><strong>{{ $currentEmployeeName }}</strong></td>
                              <td></td>
                              <td></td>
                              {{-- <td><strong>{{ $currentEmployeeLogdate }}</strong></td>
                              <td><strong>{{ $currentEmployeeShift }}</strong></td> --}}
                              <td></td>
                              <td></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['abs'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['lv_w_pay'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['reg_hrs'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['late_min'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['undertime_min'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['reg_ot'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['reg_nd'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['reg_ot_nd'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['rst_ot'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['rst_ot_over_eight'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['rst_nd'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['rst_nd_over_eight'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['lh_ot'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['lh_ot_over_eight'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['lh_nd'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['lh_nd_over_eight'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['sh_ot'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['sh_ot_over_eight'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['sh_nd'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['sh_nd_over_eight'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['rst_lh_ot'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['rst_lh_ot_over_eight'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['rst_lh_nd'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['rst_lh_nd_over_eight'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['rst_sh_ot'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['rst_sh_ot_over_eight'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['rst_sh_nd'] }}</strong></td>
                              <td class="dt-type-numeric"><strong>{{ $subtotals['rst_sh_nd_over_eight'] }}</strong></td>
                              <td></td> 
                          </tr>
                          @endif
                          @php
                          $subtotals = [
                              'abs' => 0,
                              'lv_w_pay' => 0,
                              'reg_hrs' => 0,
                              'late_min' => 0,
                              'undertime_min' => 0,
                              'reg_ot' => 0,
                              'reg_nd' => 0,
                              'reg_ot_nd' => 0,
                              'rst_ot' => 0,
                              'rst_ot_over_eight' => 0,
                              'rst_nd' => 0,
                              'rst_nd_over_eight' => 0,
                              'lh_ot' => 0,
                              'lh_ot_over_eight' => 0,
                              'lh_nd' => 0,
                              'lh_nd_over_eight' => 0,
                              'sh_ot' => 0,
                              'sh_ot_over_eight' => 0,
                              'sh_nd' => 0,
                              'sh_nd_over_eight' => 0,
                              'rst_lh_ot' => 0,
                              'rst_lh_ot_over_eight' => 0,
                              'rst_lh_nd' => 0,
                              'rst_lh_nd_over_eight' => 0,
                              'rst_sh_ot' => 0,
                              'rst_sh_ot_over_eight' => 0,
                              'rst_sh_nd' => 0,
                              'rst_sh_nd_over_eight' => 0
                          ];
                          $currentEmployeeNo = $timekeeping->employee_no;
                          $currentEmployeeName = $timekeeping->name;
                          // $currentEmployeeLogdate =$timekeeping->log_date;
                          // $currentEmployeeShift = $timekeeping->shift;
                          @endphp
                      @endif
                      <tr>
                          <td>{{ $timekeeping->company->company_code }}</td>
                          <td>{{ $timekeeping->employee_no }}</td>
                          <td>{{ $timekeeping->name }}</td>
                          <td>{{ $timekeeping->log_date }}</td>
                          <td>{{ $timekeeping->shift }}</td>
                          <td>{{ $timekeeping->in }}</td>
                          <td>{{ $timekeeping->out }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->abs }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->lv_w_pay }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->reg_hrs }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->late_min }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->undertime_min }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->reg_ot }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->reg_nd }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->reg_ot_nd }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->rst_ot }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->rst_ot_over_eight }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->rst_nd }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->rst_nd_over_eight }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->lh_ot }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->lh_ot_over_eight }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->lh_nd }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->lh_nd_over_eight }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->sh_ot }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->sh_ot_over_eight }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->sh_nd }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->sh_nd_over_eight }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->rst_lh_ot }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->rst_lh_ot_over_eight }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->rst_lh_nd }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->rst_lh_nd_over_eight }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->rst_sh_ot }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->rst_sh_ot_over_eight }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->rst_sh_nd }}</td>
                          <td class="dt-type-numeric">{{ $timekeeping->rst_sh_nd_over_eight }}</td>
                          <td>
                            @if (!empty($timekeeping->OB))
                                {{ $timekeeping->OB }}
                            @elseif (!empty($timekeeping->LWP))
                                {{ $timekeeping->LWP }}
                            @endif
                        </td> 
                      </tr>
                      @php
                      // Update subtotals
                      $subtotals['abs'] += $timekeeping->abs;
                      $subtotals['lv_w_pay'] += $timekeeping->lv_w_pay;
                      $subtotals['reg_hrs'] += $timekeeping->reg_hrs;
                      $subtotals['late_min'] += $timekeeping->late_min;
                      $subtotals['undertime_min'] += $timekeeping->undertime_min;
                      $subtotals['reg_ot'] += $timekeeping->reg_ot;
                      $subtotals['reg_nd'] += $timekeeping->reg_nd;
                      $subtotals['reg_ot_nd'] += $timekeeping->reg_ot_nd;
                      $subtotals['rst_ot'] += $timekeeping->rst_ot;
                      $subtotals['rst_ot_over_eight'] += $timekeeping->rst_ot_over_eight;
                      $subtotals['rst_nd'] += $timekeeping->rst_nd;
                      $subtotals['rst_nd_over_eight'] += $timekeeping->rst_nd_over_eight;
                      $subtotals['lh_ot'] += $timekeeping->lh_ot;
                      $subtotals['lh_ot_over_eight'] += $timekeeping->lh_ot_over_eight;
                      $subtotals['lh_nd'] += $timekeeping->lh_nd;
                      $subtotals['lh_nd_over_eight'] += $timekeeping->lh_nd_over_eight;
                      $subtotals['sh_ot'] += $timekeeping->sh_ot;
                      $subtotals['sh_ot_over_eight'] += $timekeeping->sh_ot_over_eight;
                      $subtotals['sh_nd'] += $timekeeping->sh_nd;
                      $subtotals['sh_nd_over_eight'] += $timekeeping->sh_nd_over_eight;
                      $subtotals['rst_lh_ot'] += $timekeeping->rst_lh_ot;
                      $subtotals['rst_lh_ot_over_eight'] += $timekeeping->rst_lh_ot_over_eight;
                      $subtotals['rst_lh_nd'] += $timekeeping->rst_lh_nd;
                      $subtotals['rst_lh_nd_over_eight'] += $timekeeping->rst_lh_nd_over_eight;
                      $subtotals['rst_sh_ot'] += $timekeeping->rst_sh_ot;
                      $subtotals['rst_sh_ot_over_eight'] += $timekeeping->rst_sh_ot_over_eight;
                      $subtotals['rst_sh_nd'] += $timekeeping->rst_sh_nd;
                      $subtotals['rst_sh_nd_over_eight'] += $timekeeping->rst_sh_nd_over_eight;

                      $totals['abs'] += $timekeeping->abs;
                      $totals['lv_w_pay'] += $timekeeping->lv_w_pay;
                      $totals['reg_hrs'] += $timekeeping->reg_hrs;
                      $totals['late_min'] += $timekeeping->late_min;
                      $totals['undertime_min'] += $timekeeping->undertime_min;
                      $totals['reg_ot'] += $timekeeping->reg_ot;
                      $totals['reg_nd'] += $timekeeping->reg_nd;
                      $totals['reg_ot_nd'] += $timekeeping->reg_ot_nd;
                      $totals['rst_ot'] += $timekeeping->rst_ot;
                      $totals['rst_ot_over_eight'] += $timekeeping->rst_ot_over_eight;
                      $totals['rst_nd'] += $timekeeping->rst_nd;
                      $totals['rst_nd_over_eight'] += $timekeeping->rst_nd_over_eight;
                      $totals['lh_ot'] += $timekeeping->lh_ot;
                      $totals['lh_ot_over_eight'] += $timekeeping->lh_ot_over_eight;
                      $totals['lh_nd'] += $timekeeping->lh_nd;
                      $totals['lh_nd_over_eight'] += $timekeeping->lh_nd_over_eight;
                      $totals['sh_ot'] += $timekeeping->sh_ot;
                      $totals['sh_ot_over_eight'] += $timekeeping->sh_ot_over_eight;
                      $totals['sh_nd'] += $timekeeping->sh_nd;
                      $totals['sh_nd_over_eight'] += $timekeeping->sh_nd_over_eight;
                      $totals['rst_lh_ot'] += $timekeeping->rst_lh_ot;
                      $totals['rst_lh_ot_over_eight'] += $timekeeping->rst_lh_ot_over_eight;
                      $totals['rst_lh_nd'] += $timekeeping->rst_lh_nd;
                      $totals['rst_lh_nd_over_eight'] += $timekeeping->rst_lh_nd_over_eight;
                      $totals['rst_sh_ot'] += $timekeeping->rst_sh_ot;
                      $totals['rst_sh_ot_over_eight'] += $timekeeping->rst_sh_ot_over_eight;
                      $totals['rst_sh_nd'] += $timekeeping->rst_sh_nd;
                      $totals['rst_sh_nd_over_eight'] += $timekeeping->rst_sh_nd_over_eight;
                      @endphp
                  @endforeach
          
                  @if(!is_null($currentEmployeeNo))
                  <tr class="subtotal-row">
                      <td><strong>Subtotal</strong></td>
                      <td><strong>{{ $currentEmployeeNo }}</strong></td>
                      <td><strong>{{ $currentEmployeeName }}</strong></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['abs'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['lv_w_pay'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['reg_hrs'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['late_min'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['undertime_min'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['reg_ot'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['reg_nd'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['reg_ot_nd'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['rst_ot'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['rst_ot_over_eight'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['rst_nd'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['rst_nd_over_eight'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['lh_ot'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['lh_ot_over_eight'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['lh_nd'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['lh_nd_over_eight'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['sh_ot'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['sh_ot_over_eight'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['sh_nd'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['sh_nd_over_eight'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['rst_lh_ot'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['rst_lh_ot_over_eight'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['rst_lh_nd'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['rst_lh_nd_over_eight'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['rst_sh_ot'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['rst_sh_ot_over_eight'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['rst_sh_nd'] }}</strong></td>
                      <td class="dt-type-numeric"><strong>{{ $subtotals['rst_sh_nd_over_eight'] }}</strong></td>
                      <td></td> <!-- Placeholder for Remarks column -->
                  </tr>
                  @endif
                  <tr class="total">
                    <td>Grand Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><strong>{{ $totals['abs'] }}</strong></td>
                    <td><strong>{{ $totals['lv_w_pay'] }}</strong></td>
                    <td><strong>{{ $totals['reg_hrs'] }}</strong></td>
                    <td><strong>{{ $totals['late_min'] }}</strong></td>
                    <td><strong>{{ $totals['undertime_min'] }}</strong></td>
                    <td><strong>{{ $totals['reg_ot'] }}</strong></td>
                    <td><strong>{{ $totals['reg_nd'] }}</strong></td>
                    <td><strong>{{ $totals['reg_ot_nd'] }}</strong></td>
                    <td><strong>{{ $totals['rst_ot'] }}</strong></td>
                    <td><strong>{{ $totals['rst_ot_over_eight'] }}</strong></td>
                    <td><strong>{{ $totals['rst_nd'] }}</strong></td>
                    <td><strong>{{ $totals['rst_nd_over_eight'] }}</strong></td>
                    <td><strong>{{ $totals['lh_ot'] }}</strong></td>
                    <td><strong>{{ $totals['lh_ot_over_eight'] }}</strong></td>
                    <td><strong>{{ $totals['lh_nd'] }}</strong></td>
                    <td><strong>{{ $totals['lh_nd_over_eight'] }}</strong></td>
                    <td><strong>{{ $totals['sh_ot'] }}</strong></td>
                    <td><strong>{{ $totals['sh_ot_over_eight'] }}</strong></td>
                    <td><strong>{{ $totals['sh_nd'] }}</strong></td>
                    <td><strong>{{ $totals['sh_nd_over_eight'] }}</strong></td>
                    <td><strong>{{ $totals['rst_lh_ot'] }}</strong></td>
                    <td><strong>{{ $totals['rst_lh_ot_over_eight'] }}</strong></td>
                    <td><strong>{{ $totals['rst_lh_nd'] }}</strong></td>
                    <td><strong>{{ $totals['rst_lh_nd_over_eight'] }}</strong></td>
                    <td><strong>{{ $totals['rst_sh_ot'] }}</strong></td>
                    <td><strong>{{ $totals['rst_sh_ot_over_eight'] }}</strong></td>
                    <td><strong>{{ $totals['rst_sh_nd'] }}</strong></td>
                    <td><strong>{{ $totals['rst_sh_nd_over_eight'] }}</strong></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- DataTables CSS and JS includes -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">

<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>

<script>
  $(document).ready(function() {
    new DataTable('.table-detailed', {
      // pagelenth:25,
      paginate:false,
      dom: 'Bfrtip',
      buttons: [
          'copy', 'excel'
      ],
      columnDefs: [{
        "defaultContent": "-",
        "targets": "_all"
      }],
      order: [] 
    });
  });
</script>

<style>
div.dt-search {
    float: right;
}

div.dt-info {
    float: left;
    margin-top: 0.8em;
}

div.dt-paging {
    float: right;
    margin-top: 0.5em;
}
</style>

@endsection

