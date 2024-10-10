@extends('layouts.header')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class='row'>
         
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">YTD Report</h4>
                <p class="card-description">
                  <form method='get' onsubmit='show();'  enctype="multipart/form-data">
                  <div class=row>
                    <div class='col-md-4'>
                        <div class="form-group">
                          <select data-placeholder="Filter By Employee" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='employee' required>
                              <option value="">-- Filter By Employee --</option>
                              @foreach($employees as $emp)
                                  <option value="{{$emp->employee_code}}" @if($employee_data == $emp->employee_code) selected @endif >{{$emp->employee_number}} - {{$emp->first_name}} {{$emp->last_name}}</option>
                              @endforeach
                          </select>
                        </div> 
                    </div>
                    <div class='col-md-3'>
                          <div class="form-group">
                              <select data-placeholder="Filter By Company" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='company'>
                                  <option value="">-- Filter By Company --</option>
                                  @foreach($companies as $comp)
                                  <option value="{{$comp->id}}" @if ($comp->id == $company) selected @endif>{{$comp->company_code}}</option>
                                  @endforeach
                              </select>
                          </div>
                    </div>
                    
                    <div class='col-md-2'>
                        <div class="form-group">
                          <input value='{{ date('Y', strtotime($from_date)) }}' min='{{$from_date}}' class="form-control date-own" name="from" type="year" type='year' required/>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <button type="submit" class="btn btn-primary mb-2">Submit</button>
                        {{-- @if($date_range)
                            <button class='btn btn-info mb-2' onclick="exportTableToExcel('employee_attendance','{{$from_date}} - {{$to_date}}')">Export</button>
                        @endif --}}
                    
                    </div>
                      
                  </div>
                  
                  </form>
                </p>
                
                
                <div class="table-responsive">

                   @if($emp_data)
                    <table border="1" class="table table-hover table-bordered employee_attendance" id='employee_attendance'>
                        <thead>
                            {{-- <tr>
                                <td colspan='5'>{{$emp->emp_code}} - {{$emp->first_name}} {{$emp->last_name}}</td>
                            </tr> --}}
                            <tr style="background-color: #81b0e6;">
                                <th>Description</th>
                                <th>January</th>
                                <th>February</th>
                                <th>March</th>
                                <th>April</th>
                                <th>May</th>
                                <th>June</th>
                                <th>July</th>
                                <th>August</th>
                                <th>September</th>
                                <th>October</th>
                                <th>November</th>
                                <th>December</th>
                                <th>Total</th>
                                
                            </tr>
                        </thead>

                        
                        <tbody>
                         
                            <tr>
                                <td>BASIC PAY</td>
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-01-01',strtotime($from_date)),date('Y-01-t',strtotime($from_date))])->sum('basic_pay')}}</td>
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-02-01',strtotime($from_date)),date('Y-02-t',strtotime($from_date))])->sum('basic_pay')}}</td>
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-03-01',strtotime($from_date)),date('Y-03-t',strtotime($from_date))])->sum('basic_pay')}}</td>
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-04-01',strtotime($from_date)),date('Y-04-t',strtotime($from_date))])->sum('basic_pay')}}</td>
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-05-01',strtotime($from_date)),date('Y-05-t',strtotime($from_date))])->sum('basic_pay')}}</td>
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-06-01',strtotime($from_date)),date('Y-06-t',strtotime($from_date))])->sum('basic_pay')}}</td>
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-07-01',strtotime($from_date)),date('Y-07-t',strtotime($from_date))])->sum('basic_pay')}}</td>
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-08-01',strtotime($from_date)),date('Y-08-t',strtotime($from_date))])->sum('basic_pay')}}</td>
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-09-01',strtotime($from_date)),date('Y-09-t',strtotime($from_date))])->sum('basic_pay')}}</td>
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-10-01',strtotime($from_date)),date('Y-10-t',strtotime($from_date))])->sum('basic_pay')}}</td>
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-11-01',strtotime($from_date)),date('Y-11-t',strtotime($from_date))])->sum('basic_pay')}}</td>
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-12-01',strtotime($from_date)),date('Y-12-t',strtotime($from_date))])->sum('basic_pay')}}</td>
                                <td>{{$emp_data->sum('basic_pay')}}</td>
                            </tr>
                            @php
                                $amounts = [
                                    'ND' => 'reg_nd_amount',
                                    'OT' => 'reg_ot_amount',
                                    'OT ND' => 'reg_ot_nd_amount',
                                    'RD ND' => 'rst_nd_amount',
                                    'RD OT' => 'rst_ot_amount',
                                    'RD OT GE' => 'rst_ot_ge_amount',
                                    'RD ND GE' => 'rst_nd_ge_amount',
                                    'LH ND' => 'lh_nd_amount',
                                    'LH ND GE' => 'lh_nd_ge_amount',
                                    'LH ND OT' => 'lh_ot_amount',
                                    'LH ND OT GE' => 'lh_ot_ge_amount',
                                    'SH' => 'sh_amount',
                                    'SH ND' => 'sh_nd_amount',
                                    'SH GE' => 'sh_ot_ge_amount',
                                    'SH ND GE' => 'sh_nd_ge_amount',
                                ];
                            @endphp    
                            @foreach ($amounts as $label => $field)
                            <tr>
                                <td>{{ $label }}</td>
                                @for ($i = 1; $i <= 12; $i++)
                                    <td>{{ $emp_data->whereBetween('cut_off_date', [
                                        date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),
                                        date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))
                                    ])->sum($field) }}</td>
                                @endfor
                                <td>{{ $emp_data->sum($field) }}</td>
                            </tr>
                        @endforeach
                        <tr >
                            <td >SALARY ADJUSTMENT</td>
                            
                            @for ($i = 1; $i <= 12; $i++)
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))])->sum('salary_adjustment')}}</td>
                                @endfor
                                <td>{{$emp_data->sum('salary_adjustment')}}</td>
                        </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Taxable Income</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>De Minimis</td>
                                @for ($i = 1; $i <= 12; $i++)
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))])->sum('deminimis')}}</td>
                                @endfor
                                <td>{{$emp_data->sum('deminimis')}}</td>
                            </tr>
                            <tr>
                                <td>Other Allowances</td>
                                @for ($i = 1; $i <= 12; $i++)
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))])->sum('other_allowances_basic_pay')}}</td>
                                @endfor
                                <td>{{$emp_data->sum('other_allowances_basic_pay')}}</td>
                            </tr>
                            <tr>
                                <td>Subliq</td>
                                @for ($i = 1; $i <= 12; $i++)
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))])->sum('subliq')}}</td>
                                @endfor
                                <td>{{$emp_data->sum('subliq')}}</td>
                            </tr>
                            @foreach($allowances as $allowance)
                            <tr>
                                <td>{{$allowance->allowance_type->name}}</td>
                                @for ($i = 1; $i <= 12; $i++)
                                <td>{{ $emp_data->whereBetween('cut_off_date', [
                                    date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),
                                    date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))
                                ])->flatMap(function($emp) use ($allowance) {
                                    return $emp->pay_allowances->where('allowance_id',$allowance->allowance_id); // Filter by allowance_id
                                })->sum('amount') }}
                                </td>
                                @endfor
                                <td>{{$allowances_data->where('allowance_id',$allowance->allowance_id)->sum('amount')}}</td>
                            </tr>
                            @endforeach
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Non-Taxable Income</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Total Earnings</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Absent</td>
                                @for ($i = 1; $i <= 12; $i++)
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))])->sum('absent_amount') * -1}}</td>
                                @endfor
                                <td>{{$emp_data->sum('absent_amount') * -1}}</td>
                            </tr>
                            <tr>
                                <td>Tardiness</td>
                                @for ($i = 1; $i <= 12; $i++)
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))])->sum('tardiness_amount') * -1}}</td>
                                @endfor
                                <td>{{$emp_data->sum('tardiness_amount') * -1}}</td>
                            </tr>
                            <tr>
                                <td>Undertime</td>
                                @for ($i = 1; $i <= 12; $i++)
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))])->sum('undertime_amount') * -1}}</td>
                                @endfor
                                <td>{{$emp_data->sum('undertime_amount') * -1}}</td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Taxable Deduction</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>SSS EMPLOYEE SHARE</td>
                                @for ($i = 1; $i <= 12; $i++)
                                <td>{{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))])->sum('sss_employee_share') 
                              }}</td>
                                @endfor
                                <td>{{$emp_data->sum('sss_employee_share') }}</td>
                            </tr>
                            <tr>
                                <td>HDMF EMPLOYEE SHARE</td>
                                @for ($i = 1; $i <= 12; $i++)
                                <td>
                                {{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))])->sum('hdmf_employee_share')}}
                                </td>
                                @endfor
                                <td>{{$emp_data->sum('hdmf_employee_share')}}</td>
                            </tr>
                            <tr>
                                <td>PHIC EMPLOYEE SHARE</td>
                                @for ($i = 1; $i <= 12; $i++)
                                <td>
                                {{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))])->sum('phic_employee_share')}}
                                </td>
                                @endfor
                                <td>{{$emp_data->sum('phic_employee_share')}}</td>
                            </tr>
                            <tr>
                                <td>MPF EMPLOYEE SHARE</td>
                                @for ($i = 1; $i <= 12; $i++)
                                <td>
                                {{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))])->sum('mpf_employee_share')}}
                                </td>
                                @endfor
                                <td>{{$emp_data->sum('mpf_employee_share')}}</td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Statutory</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>WITHHOLDING TAX</td>
                                @for ($i = 1; $i <= 12; $i++)
                                <td>
                                {{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))])->sum('withholding_tax')}}
                                </td>
                                @endfor
                                <td>{{$emp_data->sum('withholding_tax')}}</td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Withholding Tax</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            @foreach($loans as $loan)
                            <tr>
                                <td>{{$loan->loan_type->loan_name}}</td>
                                @for ($i = 1; $i <= 12; $i++)
                                <td>{{ $emp_data->whereBetween('cut_off_date', [
                                    date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),
                                    date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))
                                ])->flatMap(function($emp) use ($loan) {
                                    return $emp->pay_loan->where('loan_type_id',$loan->loan_type_id); // Filter by allowance_id
                                })->sum('amount') }}
                                </td>
                                @endfor
                                <td>{{$loans_data->where('loan_type_id',$loan->loan_type_id)->sum('amount')}}</td>
                            </tr>
                            @endforeach
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Non-Taxable Deduction</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Total Deductions</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Net Pay</strong></td>
                                @for ($i = 1; $i <= 12; $i++)
                                <td>
                                {{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date)),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date))])->sum('netpay')}}
                                </td>
                                @endfor
                                <td>{{$emp_data->sum('netpay')}}</td>
                            </tr>
                        </tbody>
                    </table>

                    <br>
                   @endif

                  </div>
                </div>

            </div>
          </div>
        
        </div>
    </div>
</div>
@php
function night_difference($start_work,$end_work)
{
    $start_night = mktime('22','00','00',date('m',$start_work),date('d',$start_work),date('Y',$start_work));
    $end_night   = mktime('06','00','00',date('m',$start_work),date('d',$start_work) + 1,date('Y',$start_work));

    if($start_work >= $start_night && $start_work <= $end_night)
    {
        if($end_work >= $end_night)
        {
            return ($end_night - $start_work) / 3600;
        }
        else
        {
            return ($end_work - $start_work) / 3600;
        }
    }
    elseif($end_work >= $start_night && $end_work <= $end_night)
    {
        if($start_work <= $start_night)
        {
            return ($end_work - $start_night) / 3600;
        }
        else
        {
            return ($end_work - $start_work) / 3600;
        }
    }
    else
    {
        if($start_work < $start_night && $end_work > $end_night)
        {
            return ($end_night - $start_night) / 3600;
        }
        return 0;
    }
}

@endphp
{{-- Datatable --}}
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">

<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
   $('.date-own').datepicker({
         minViewMode: 2,
         format: 'yyyy',
       });
    function get_min(value)
    {
        document.getElementById("to").min = value;
    }

    $(document).ready(function() 
    {
        new DataTable('.table', 
        {
            paginate:false,
            dom: 'Bfrtip',
            buttons: 
            [
                'copy', 
                'excel'
            ],
            columnDefs: 
            [
                {
                    "defaultContent": "-",
                    "targets": "_all"
                }
            ],
            order: [] 
        });
    });
</script>
@endsection
