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
                          <select data-placeholder="Filter By Employee" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='employee'>
                              <option value="">-- Filter By Employee --</option>
                              <option value=" " @if($employee_data === null) selected @endif>-- Remove Employee --</option>
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
                                  <option value=" " @if($company  === null) selected @endif>-- Remove Company --</option>
                                  @foreach($companies as $comp)
                                  <option value="{{$comp->id}}" @if ($comp->id == $company) selected @endif>{{$comp->company_code}}</option>
                                  @endforeach
                              </select>
                          </div>
                    </div>
                    
                    <div class='col-md-2'>
                        <div class="form-group">
                          <input value='{{ $from_date }}'  class="form-control date-own" name="from" type="year" type='year' required/>
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

                    @if(request()->get('company') && request()->get('company') !== null)
                   <button id="btnExport" onclick="fnExcelReport();"> EXPORT </button>
                    {{-- @foreach ($emp_data as $emp) --}}
                        @foreach ( $empD as $empDetail)
                        
                            <table border="1" width='100%' class="table table-hover table-bordered " id='YTD'>
                                <thead>
                                    {{-- <tr>
                                        <td colspan='5'>{{$emp->emp_code}} - {{$emp->first_name}} {{$emp->last_name}}</td>
                                    </tr> --}}
                                    <tr>
                                        <td colspan='14' class='text-center'><b>Annual Payroll Summary</b></td>
                                    </tr>
                                    <tr>
                                    
                                        <td colspan='14' class='text-center'>{{$empDetail->company->company_name}} - <b>{{$from_date}}</b></td>
                                    </tr>
                                    <tr>
                                    
                                        <td colspan='7' class='text-left'>Employee No: <b>{{$empDetail->employee_code}} </b></td>
                                        <td colspan='7' class='text-left'>Department:  <b>{{$empDetail->department->name}}</b> </td>
                                    </tr>
                                    <tr>
                                    
                                        <td colspan='7' class='text-left'>Name:  <b>{{$empDetail->last_name}}, {{$empDetail->first_name}}</b></td>
                                        <td colspan='7' class='text-left'>Tax Status:  <b>{{$empDetail->tax_status}}</b> </td>
                                    </tr>
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
                                    @php
                                        $taxable = [];
                                        for($i = 1; $i <= 12; $i++)
                                        {
                                            $taxable["taxable".$i] = 0;
                                        }
                                    @endphp
                                    <tr>
                                        <td>BASIC PAY</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        @php
                                            $taxable["taxable".$i] = $taxable["taxable".$i]+$emp_data->whereBetween('cut_off_date', [
                                                date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date . "-01-01")),
                                                date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date . "-01-01"))
                                            ])->sum('basic_pay');
                                        @endphp
                                        <td>
                                            {{number_format($emp_data->whereBetween('cut_off_date', [
                                                date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date . "-01-01")),
                                                date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date . "-01-01"))
                                            ])->sum('basic_pay'),2)}}
                                        </td>
                                    @endfor
                                    <td>{{number_format($emp_data->sum('basic_pay'),2)}}</td>
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
                                    @php
                                    $va='';
                                    @endphp
                                    @foreach ($amounts as $label => $field)
                                    @php
                                        if($label != "ND")
                                        {

                                            $va = $va ." ,'+', "."'".$field."'";
                                        }
                                        else {
                                            $va = "'".$field."'";
                                        }
                                
                                    @endphp
                                    
                                    <tr>
                                        <td>{{ $label }}</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        @php
                                            $taxable["taxable".$i] = $taxable["taxable".$i]+$emp_data->whereBetween('cut_off_date', [
                                                    date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),
                                                    date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))
                                                ])->sum($field);
                                        @endphp
                                            <td>{{ number_format($emp_data->whereBetween('cut_off_date', [
                                                date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),
                                                date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))
                                            ])->sum($field) ,2)}}</td>
                                        @endfor
                                        <td>{{ number_format($emp_data->sum($field),2) }}

                                            {{-- <input class="qty" type="hidden" value="{{$emp_data->sum($field)}}"> --}}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr >
                                    <td >SALARY ADJUSTMENT</td>
                                    
                                    @for ($i = 1; $i <= 12; $i++)
                                    @php
                                        $taxable["taxable".$i] = $taxable["taxable".$i]+$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('salary_adjustment');
                                    @endphp
                                        <td>{{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('salary_adjustment'),2)}}</td>
                                        @endfor
                                        <td>{{number_format($emp_data->sum('salary_adjustment'),2)}}</td>
                                </tr>
                                    <tr style="background-color: #e0e0e0;">
                                        <td colspan="1"><strong>Taxable Income</strong></td>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <td>{{ number_format($taxable["taxable".$i],2)}}</td>
                                        @endfor
                                
                                        <td>{{ number_format(array_sum($taxable),2)}}</td>
                                    </tr>
                                    @php
                                        $value = [];
                                        for($i = 1; $i <= 12; $i++)
                                        {
                                            $value["nontaxable_".$i] = 0;
                                        }
                                    @endphp
                                    <tr>
                                        <td>De Minimis</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        <td>
                                            @php
                                                $value["nontaxable_".$i] = $value["nontaxable_".$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('deminimis');
                                            @endphp
                                            {{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('deminimis')}}</td>
                                        @endfor
                                        <td>{{number_format($emp_data->sum('deminimis'),2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Other Allowances</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        @php
                                            $value["nontaxable_".$i] = $value["nontaxable_".$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('other_allowances_basic_pay');
                                        @endphp
                                        <td>{{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('other_allowances_basic_pay'),2)}}</td>
                                        @endfor
                                        <td>{{number_format($emp_data->sum('other_allowances_basic_pay'),2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Subliq</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        @php
                                            $value["nontaxable_".$i] = $value["nontaxable_".$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('subliq');
                                        @endphp
                                        <td>{{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('subliq'),2)}}</td>
                                        @endfor
                                        <td>{{number_format($emp_data->sum('subliq'),2)}}</td>
                                    </tr>
                                    @php
                                        $allowances_pluck = $allowances->pluck('allowance_id')->toArray();
                                    @endphp
                                    @foreach($allowances as $allowance)
                                    <tr>
                                        <td>{{$allowance->allowance_type->name}}</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        <td>{{ $emp_data->whereBetween('cut_off_date', [
                                            date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),
                                            date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))
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
                                        @for ($i = 1; $i <= 12; $i++)
                                        <td>{{number_format($value["nontaxable_".$i],2)}}</td>
                                        @endfor
                                        <td>{{number_format(array_sum($value),2)}}</td>
                                    </tr>
                                    <tr style="background-color: #e0e0e0;">
                                        <td colspan="1"><strong>Total Earnings</strong></td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        <td>{{number_format($taxable['taxable'.$i] + $value["nontaxable_".$i],2)}}</td>
                                        @endfor
                                        <td>{{number_format(array_sum($taxable) + array_sum($value),2)}}</td>
                                    </tr>
                                    @php
                                        $taxable_deduction = [];
                                        for($i = 1; $i <= 12; $i++)
                                        {
                                            $taxable_deduction["taxable_deduction".$i] = 0;
                                        }
                                    @endphp
                                    <tr>
                                        <td>Absent</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        
                                        @php
                                            $taxable_deduction["taxable_deduction".$i] = $taxable_deduction["taxable_deduction".$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('absent_amount');
                                        @endphp

                                        <td>{{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('absent_amount') * -1,2)}}</td>
                                        @endfor
                                        <td>{{number_format($emp_data->sum('absent_amount') * -1,2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Tardiness</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        @php
                                            $taxable_deduction["taxable_deduction".$i] = $taxable_deduction["taxable_deduction".$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('tardiness_amount');
                                        @endphp
                                        <td>{{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('tardiness_amount') * -1,2)}}</td>
                                        @endfor
                                        <td>{{number_format($emp_data->sum('tardiness_amount') * -1,2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Undertime</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        @php
                                            $taxable_deduction["taxable_deduction".$i] = $taxable_deduction["taxable_deduction".$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('undertime_amount');
                                        @endphp
                                        <td>{{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('undertime_amount') * -1,2)}}</td>
                                        @endfor
                                        <td>{{number_format($emp_data->sum('undertime_amount') * -1,2)}}</td>
                                    </tr>
                                    <tr style="background-color: #e0e0e0;">
                                        <td colspan="1"><strong>Taxable Deduction</strong></td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        <td>{{number_format($taxable_deduction["taxable_deduction".$i]*-1,2)}}</td>
                                        @endfor
                                        <td>{{number_format(array_sum($taxable_deduction)*-1,2)}}</td>
                                    </tr>
                                    @php
                                    $statutory = [];
                                    for($i = 1; $i <= 12; $i++)
                                    {
                                        $statutory["statutory".$i] = 0;
                                    }
                                @endphp
                                    <tr>
                                        <td>SSS EMPLOYEE SHARE</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        @php
                                        $statutory['statutory'.$i] =  $statutory['statutory'.$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('sss_employee_share');
                                        @endphp
                                        <td>{{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('sss_employee_share') 
                                    *-1,2)}}</td>
                                        @endfor
                                        <td>{{number_format($emp_data->sum('sss_employee_share')*-1 ,2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>HDMF EMPLOYEE SHARE</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        @php
                                        $statutory['statutory'.$i] =  $statutory['statutory'.$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('hdmf_employee_share');
                                    @endphp
                                        <td>
                                        {{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('hdmf_employee_share')*-1,2)}}
                                        </td>
                                        @endfor
                                        <td>{{number_format($emp_data->sum('hdmf_employee_share')*-1,2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>PHIC EMPLOYEE SHARE</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        @php
                                        $statutory['statutory'.$i] =  $statutory['statutory'.$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('phic_employee_share');
                                    @endphp
                                        <td>
                                        {{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('phic_employee_share')*-1,2)}}
                                        </td>
                                        @endfor
                                        <td>{{number_format($emp_data->sum('phic_employee_share')*-1,2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>MPF EMPLOYEE SHARE</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        @php
                                        $statutory['statutory'.$i] =  $statutory['statutory'.$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('mpf_employee_share');
                                    @endphp
                                        <td>
                                        {{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('mpf_employee_share')*-1,2)}}
                                        </td>
                                        @endfor
                                        <td>{{number_format($emp_data->sum('mpf_employee_share')*-1,2)}}</td>
                                    </tr>
                                    <tr style="background-color: #e0e0e0;">
                                        <td colspan="1"><strong>Statutory</strong></td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        <td>
                                        {{number_format($statutory['statutory'.$i]*-1,2)}}
                                        </td>
                                        @endfor
                                        <td>{{number_format(array_sum($statutory)*-1,2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>WITHHOLDING TAX</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        <td>
                                        {{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('withholding_tax')*-1,2)}}
                                        </td>
                                        @endfor
                                        <td>{{number_format($emp_data->sum('withholding_tax')*-1,2)}}</td>
                                    </tr>
                                    <tr style="background-color: #e0e0e0;">
                                        <td><b>WITHHOLDING TAX</b></td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        <td>
                                        {{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('withholding_tax')*-1,2)}}
                                        </td>
                                        @endfor
                                        <td>{{number_format($emp_data->sum('withholding_tax')*-1,2)}}</td>
                                    </tr>
                                    @php
                                        $non_taxable_deduction = [];
                                        for($i = 1; $i <= 12; $i++)
                                        {
                                            $non_taxable_deduction["non_taxable_deduction".$i] = 0;
                                        }
                                    @endphp
                                    @foreach($loans as $loan)
                                    <tr>
                                        <td>{{$loan->loan_type->loan_name}}</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        @php
                                            $non_taxable_deduction["non_taxable_deduction".$i] = $non_taxable_deduction["non_taxable_deduction".$i]  + $emp_data->whereBetween('cut_off_date', [
                                            date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),
                                            date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))
                                        ])->flatMap(function($emp) use ($loan) {
                                            return $emp->pay_loan->where('loan_type_id',$loan->loan_type_id); // Filter by allowance_id
                                        })->sum('amount');
                                        @endphp
                                        <td>{{ number_format($emp_data->whereBetween('cut_off_date', [
                                            date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),
                                            date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))
                                        ])->flatMap(function($emp) use ($loan) {
                                            return $emp->pay_loan->where('loan_type_id',$loan->loan_type_id); // Filter by allowance_id
                                        })->sum('amount')*-1,2) }}
                                        </td>
                                        @endfor
                                        <td>{{number_format($loans_data->where('loan_type_id',$loan->loan_type_id)->sum('amount')*-1,2)}}</td>
                                    </tr>
                                    @endforeach
                                    @foreach($instructions as $instruction)
                                    <tr>
                                        <td>{{strtoupper($instruction->instruction_name)}}</td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        @php
                                            $non_taxable_deduction["non_taxable_deduction".$i] = $non_taxable_deduction["non_taxable_deduction".$i]  + $emp_data->whereBetween('cut_off_date', [
                                            date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),
                                            date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))
                                        ])->flatMap(function($emp) use ($instruction) {
                                            return $emp->pay_instructions->where('instruction_name',$instruction->instruction_name); // Filter by allowance_id
                                        })->sum('amount');
                                        @endphp
                                        <td>{{ number_format($emp_data->whereBetween('cut_off_date', [
                                            date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),
                                            date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))
                                        ])->flatMap(function($emp) use ($instruction) {
                                            return $emp->pay_instructions->where('instruction_name',$instruction->instruction_name); // Filter by allowance_id
                                        })->sum('amount'),2) }}
                                        </td>
                                        @endfor
                                        <td>{{number_format($instructions_data->where('instruction_name',$instruction->instruction_name)->sum('amount'),2)}}</td>
                                    </tr>
                                    @endforeach
                                    @php
                                        $total_deduction = [];
                                        for($i = 1; $i <= 12; $i++)
                                        {
                                            $total_deduction["total_deduction".$i] = 0;
                                        }
                                    @endphp
                                    <tr style="background-color: #e0e0e0;">
                                        <td colspan="1"><strong>Non-Taxable Deduction</strong></td>
                                        @for($i = 1; $i <= 12; $i++)
                                        <td>{{number_format($non_taxable_deduction["non_taxable_deduction".$i]*-1,2)}}</td>
                                        @endfor
                                        
                                        <td>{{number_format(array_sum($non_taxable_deduction)*-1,2)}}</td>
                                    </tr>
                                    <tr style="background-color: #e0e0e0;">
                                        <td colspan="1"><strong>Total Deductions</strong></td>
                                        @for($i = 1; $i <= 12; $i++)
                                        <td>{{number_format($non_taxable_deduction["non_taxable_deduction".$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('withholding_tax')*-1 + $statutory['statutory'.$i]*-1,2)}}</td>
                                        @endfor
                                        <td>{{number_format(array_sum($non_taxable_deduction) + $emp_data->sum('withholding_tax')*-1+ array_sum($statutory)*-1,2)}}</td>
                                    </tr>
                                    <tr style="background-color: #e0e0e0;">
                                        <td colspan="1"><strong>Net Pay</strong></td>
                                        @for ($i = 1; $i <= 12; $i++)
                                        <td>
                                        {{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('netpay'),2)}}
                                        </td>
                                        @endfor
                                        <td>{{number_format($emp_data->sum('netpay'),2)}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        {{-- @endforeach --}}
                    @endforeach
                    {{ $emp_data->appends([
                        'employee' => request('employee'),
                        'company' => request('company'),
                        'from' => request('from'),
                        'page' => request('page')
                    ])->links() }}
                    <br>
                    @elseif (request()->get('employee') !== null )
                    <button id="btnExport" onclick="fnExcelReport();"> EXPORT </button>
                         
                             <table border="1" width='100%' class="table table-hover table-bordered " id='YTD'>
                                 <thead>
                                     {{-- <tr>
                                         <td colspan='5'>{{$emp->emp_code}} - {{$emp->first_name}} {{$emp->last_name}}</td>
                                     </tr> --}}
                                     <tr>
                                         <td colspan='14' class='text-center'><b>Annual Payroll Summary</b></td>
                                     </tr>
                                     <tr>
                                     
                                         {{-- <td colspan='14' class='text-center'>{{$empD->company->company_name}} - <b>{{$from_date}}</b></td> --}}
                                     </tr>
                                     <tr>
                                     
                                         <td colspan='7' class='text-left'>Employee No: <b>{{$empD->employee_code}} </b></td>
                                         <td colspan='7' class='text-left'>Department:  <b>{{$empD->department->name}}</b> </td>
                                     </tr>
                                     <tr>
                                     
                                         <td colspan='7' class='text-left'>Name:  <b>{{$empD->last_name}}, {{$empD->first_name}}</b></td>
                                         <td colspan='7' class='text-left'>Tax Status:  <b>{{$empD->tax_status}}</b> </td>
                                     </tr>
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
                                     @php
                                         $taxable = [];
                                         for($i = 1; $i <= 12; $i++)
                                         {
                                             $taxable["taxable".$i] = 0;
                                         }
                                     @endphp
                                     <tr>
                                         <td>BASIC PAY</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         @php
                                             $taxable["taxable".$i] = $taxable["taxable".$i]+$emp_data->whereBetween('cut_off_date', [
                                                 date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date . "-01-01")),
                                                 date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date . "-01-01"))
                                             ])->sum('basic_pay');
                                         @endphp
                                         <td class="text-right">
                                             {{number_format($emp_data->whereBetween('cut_off_date', [
                                                 date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date . "-01-01")),
                                                 date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date . "-01-01"))
                                             ])->sum('basic_pay'),2)}}
                                         </td>
                                     @endfor
                                     <td class="text-right">{{number_format($emp_data->sum('basic_pay'),2)}}</td>
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
                                     @php
                                     $va='';
                                     @endphp
                                     @foreach ($amounts as $label => $field)
                                     @php
                                         if($label != "ND")
                                         {
 
                                             $va = $va ." ,'+', "."'".$field."'";
                                         }
                                         else {
                                             $va = "'".$field."'";
                                         }
                                 
                                     @endphp
                                     
                                     <tr>
                                         <td>{{ $label }}</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         @php
                                             $taxable["taxable".$i] = $taxable["taxable".$i]+$emp_data->whereBetween('cut_off_date', [
                                                     date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),
                                                     date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))
                                                 ])->sum($field);
                                         @endphp
                                             <td class="text-right">{{ number_format($emp_data->whereBetween('cut_off_date', [
                                                 date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),
                                                 date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))
                                             ])->sum($field) ,2)}}</td>
                                         @endfor
                                         <td class="text-right">{{ number_format($emp_data->sum($field),2) }}
 
                                             {{-- <input class="qty" type="hidden" value="{{$emp_data->sum($field)}}"> --}}
                                         </td>
                                     </tr>
                                 @endforeach
                                 <tr >
                                     <td>SALARY ADJUSTMENT</td>
                                     
                                     @for ($i = 1; $i <= 12; $i++)
                                     @php
                                         $taxable["taxable".$i] = $taxable["taxable".$i]+$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('salary_adjustment');
                                     @endphp
                                         <td class="text-right">{{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('salary_adjustment'),2)}}</td>
                                         @endfor
                                         <td class="text-right">{{number_format($emp_data->sum('salary_adjustment'),2)}}</td>
                                 </tr>
                                     <tr style="background-color: #e0e0e0;">
                                         <td colspan="1"><strong>Taxable Income</strong></td>
                                         @for ($i = 1; $i <= 12; $i++)
                                             <td class="text-right">{{ number_format($taxable["taxable".$i],2)}}</td>
                                         @endfor
                                 
                                         <td class="text-right">{{ number_format(array_sum($taxable),2)}}</td>
                                     </tr>
                                     @php
                                         $value = [];
                                         for($i = 1; $i <= 12; $i++)
                                         {
                                             $value["nontaxable_".$i] = 0;
                                         }
                                     @endphp
                                     <tr>
                                         <td>De Minimis</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         <td class="text-right">
                                             @php
                                                 $value["nontaxable_".$i] = $value["nontaxable_".$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('deminimis');
                                             @endphp
                                             {{$emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('deminimis')}}</td>
                                         @endfor
                                         <td class="text-right">{{number_format($emp_data->sum('deminimis'),2)}}</td>
                                     </tr>
                                     <tr>
                                         <td>Other Allowances</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         @php
                                             $value["nontaxable_".$i] = $value["nontaxable_".$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('other_allowances_basic_pay');
                                         @endphp
                                         <td class="text-right">{{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('other_allowances_basic_pay'),2)}}</td>
                                         @endfor
                                         <td class="text-right">{{number_format($emp_data->sum('other_allowances_basic_pay'),2)}}</td>
                                     </tr>
                                     <tr>
                                         <td>Subliq</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         @php
                                             $value["nontaxable_".$i] = $value["nontaxable_".$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('subliq');
                                         @endphp
                                         <td class="text-right">{{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('subliq'),2)}}</td>
                                         @endfor
                                         <td class="text-right">{{number_format($emp_data->sum('subliq'),2)}}</td>
                                     </tr>
                                     @php
                                         $allowances_pluck = $allowances->pluck('allowance_id')->toArray();
                                     @endphp
                                     @foreach($allowances as $allowance)
                                     <tr>
                                         <td>{{$allowance->allowance_type->name}}</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         <td class="text-right">{{ $emp_data->whereBetween('cut_off_date', [
                                             date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),
                                             date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))
                                         ])->flatMap(function($emp) use ($allowance) {
                                             return $emp->pay_allowances->where('allowance_id',$allowance->allowance_id); // Filter by allowance_id
                                         })->sum('amount') }}
                                         </td>
                                         @endfor
                                         <td class="text-right">{{$allowances_data->where('allowance_id',$allowance->allowance_id)->sum('amount')}}</td>
                                     </tr>
                                     @endforeach
                                     <tr style="background-color: #e0e0e0;">
                                         <td colspan="1"><strong>Non-Taxable Income</strong></td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         <td class="text-right">{{number_format($value["nontaxable_".$i],2)}}</td>
                                         @endfor
                                         <td class="text-right">{{number_format(array_sum($value),2)}}</td>
                                     </tr>
                                     <tr style="background-color: #e0e0e0;">
                                         <td colspan="1"><strong>Total Earnings</strong></td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         <td class="text-right">{{number_format($taxable['taxable'.$i] + $value["nontaxable_".$i],2)}}</td>
                                         @endfor
                                         <td class="text-right">{{number_format(array_sum($taxable) + array_sum($value),2)}}</td>
                                     </tr>
                                     @php
                                         $taxable_deduction = [];
                                         for($i = 1; $i <= 12; $i++)
                                         {
                                             $taxable_deduction["taxable_deduction".$i] = 0;
                                         }
                                     @endphp
                                     <tr>
                                         <td>Absent</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         
                                         @php
                                             $taxable_deduction["taxable_deduction".$i] = $taxable_deduction["taxable_deduction".$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('absent_amount');
                                         @endphp
 
                                         <td class="text-right">{{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('absent_amount') * -1,2)}}</td>
                                         @endfor
                                         <td class="text-right">{{number_format($emp_data->sum('absent_amount') * -1,2)}}</td>
                                     </tr>
                                     <tr>
                                         <td>Tardiness</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         @php
                                             $taxable_deduction["taxable_deduction".$i] = $taxable_deduction["taxable_deduction".$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('tardiness_amount');
                                         @endphp
                                         <td class="text-right">{{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('tardiness_amount') * -1,2)}}</td>
                                         @endfor
                                         <td class="text-right">{{number_format($emp_data->sum('tardiness_amount') * -1,2)}}</td>
                                     </tr>
                                     <tr>
                                         <td>Undertime</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         @php
                                             $taxable_deduction["taxable_deduction".$i] = $taxable_deduction["taxable_deduction".$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('undertime_amount');
                                         @endphp
                                         <td class="text-right">{{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('undertime_amount') * -1,2)}}</td>
                                         @endfor
                                         <td class="text-right">{{number_format($emp_data->sum('undertime_amount') * -1,2)}}</td>
                                     </tr>
                                     <tr style="background-color: #e0e0e0;">
                                         <td colspan="1"><strong>Taxable Deduction</strong></td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         <td class="text-right">{{number_format($taxable_deduction["taxable_deduction".$i]*-1,2)}}</td>
                                         @endfor
                                         <td class="text-right">{{number_format(array_sum($taxable_deduction)*-1,2)}}</td>
                                     </tr>
                                     @php
                                     $statutory = [];
                                     for($i = 1; $i <= 12; $i++)
                                     {
                                         $statutory["statutory".$i] = 0;
                                     }
                                 @endphp
                                     <tr>
                                         <td>SSS EMPLOYEE SHARE</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         @php
                                         $statutory['statutory'.$i] =  $statutory['statutory'.$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('sss_employee_share');
                                         @endphp
                                         <td class="text-right">{{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('sss_employee_share') 
                                     *-1,2)}}</td>
                                         @endfor
                                         <td class="text-right">{{number_format($emp_data->sum('sss_employee_share')*-1 ,2)}}</td>
                                     </tr>
                                     <tr>
                                         <td>HDMF EMPLOYEE SHARE</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         @php
                                         $statutory['statutory'.$i] =  $statutory['statutory'.$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('hdmf_employee_share');
                                     @endphp
                                         <td class="text-right">
                                         {{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('hdmf_employee_share')*-1,2)}}
                                         </td>
                                         @endfor
                                         <td class="text-right">{{number_format($emp_data->sum('hdmf_employee_share')*-1,2)}}</td>
                                     </tr>
                                     <tr>
                                         <td>PHIC EMPLOYEE SHARE</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         @php
                                         $statutory['statutory'.$i] =  $statutory['statutory'.$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('phic_employee_share');
                                     @endphp
                                         <td class="text-right">
                                         {{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('phic_employee_share')*-1,2)}}
                                         </td>
                                         @endfor
                                         <td class="text-right">{{number_format($emp_data->sum('phic_employee_share')*-1,2)}}</td>
                                     </tr>
                                     <tr>
                                         <td>MPF EMPLOYEE SHARE</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         @php
                                         $statutory['statutory'.$i] =  $statutory['statutory'.$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('mpf_employee_share');
                                     @endphp
                                         <td class="text-right">
                                         {{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('mpf_employee_share')*-1,2)}}
                                         </td>
                                         @endfor
                                         <td class="text-right">{{number_format($emp_data->sum('mpf_employee_share')*-1,2)}}</td>
                                     </tr>
                                     <tr style="background-color: #e0e0e0;">
                                         <td colspan="1"><strong>Statutory</strong></td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         <td class="text-right">
                                         {{number_format($statutory['statutory'.$i]*-1,2)}}
                                         </td>
                                         @endfor
                                         <td class="text-right">{{number_format(array_sum($statutory)*-1,2)}}</td>
                                     </tr>
                                     <tr>
                                         <td>WITHHOLDING TAX</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         <td class="text-right">
                                         {{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('withholding_tax')*-1,2)}}
                                         </td>
                                         @endfor
                                         <td class="text-right">{{number_format($emp_data->sum('withholding_tax')*-1,2)}}</td>
                                     </tr>
                                     <tr style="background-color: #e0e0e0;">
                                         <td><b>WITHHOLDING TAX</b></td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         <td class="text-right">
                                         {{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('withholding_tax')*-1,2)}}
                                         </td>
                                         @endfor
                                         <td class="text-right">{{number_format($emp_data->sum('withholding_tax')*-1,2)}}</td>
                                     </tr>
                                     @php
                                         $non_taxable_deduction = [];
                                         for($i = 1; $i <= 12; $i++)
                                         {
                                             $non_taxable_deduction["non_taxable_deduction".$i] = 0;
                                         }
                                     @endphp
                                     @foreach($loans as $loan)
                                     <tr>
                                         <td>{{$loan->loan_type->loan_name}}</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         @php
                                             $non_taxable_deduction["non_taxable_deduction".$i] = $non_taxable_deduction["non_taxable_deduction".$i]  + $emp_data->whereBetween('cut_off_date', [
                                             date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),
                                             date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))
                                         ])->flatMap(function($emp) use ($loan) {
                                             return $emp->pay_loan->where('loan_type_id',$loan->loan_type_id); // Filter by allowance_id
                                         })->sum('amount');
                                         @endphp
                                         <td class="text-right">{{ number_format($emp_data->whereBetween('cut_off_date', [
                                             date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),
                                             date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))
                                         ])->flatMap(function($emp) use ($loan) {
                                             return $emp->pay_loan->where('loan_type_id',$loan->loan_type_id); // Filter by allowance_id
                                         })->sum('amount')*-1,2) }}
                                         </td>
                                         @endfor
                                         <td class="text-right">{{number_format($loans_data->where('loan_type_id',$loan->loan_type_id)->sum('amount')*-1,2)}}</td>
                                     </tr>
                                     @endforeach
                                     @foreach($instructions as $instruction)
                                     <tr>
                                         <td>{{strtoupper($instruction->instruction_name)}}</td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         @php
                                             $non_taxable_deduction["non_taxable_deduction".$i] = $non_taxable_deduction["non_taxable_deduction".$i]  + $emp_data->whereBetween('cut_off_date', [
                                             date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),
                                             date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))
                                         ])->flatMap(function($emp) use ($instruction) {
                                             return $emp->pay_instructions->where('instruction_name',$instruction->instruction_name); // Filter by allowance_id
                                         })->sum('amount');
                                         @endphp
                                         <td class="text-right">{{ number_format($emp_data->whereBetween('cut_off_date', [
                                             date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),
                                             date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))
                                         ])->flatMap(function($emp) use ($instruction) {
                                             return $emp->pay_instructions->where('instruction_name',$instruction->instruction_name); // Filter by allowance_id
                                         })->sum('amount'),2) }}
                                         </td>
                                         @endfor
                                         <td class="text-right">{{number_format($instructions_data->where('instruction_name',$instruction->instruction_name)->sum('amount'),2)}}</td>
                                     </tr>
                                     @endforeach
                                     @php
                                         $total_deduction = [];
                                         for($i = 1; $i <= 12; $i++)
                                         {
                                             $total_deduction["total_deduction".$i] = 0;
                                         }
                                     @endphp
                                     <tr style="background-color: #e0e0e0;">
                                         <td colspan="1"><strong>Non-Taxable Deduction</strong></td>
                                         @for($i = 1; $i <= 12; $i++)
                                         <td class="text-right">{{number_format($non_taxable_deduction["non_taxable_deduction".$i]*-1,2)}}</td>
                                         @endfor
                                         
                                         <td class="text-right">{{number_format(array_sum($non_taxable_deduction)*-1,2)}}</td>
                                     </tr>
                                     <tr style="background-color: #e0e0e0;">
                                         <td colspan="1"><strong>Total Deductions</strong></td>
                                         @for($i = 1; $i <= 12; $i++)
                                         <td class="text-right">{{number_format($non_taxable_deduction["non_taxable_deduction".$i] + $emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('withholding_tax')*-1 + $statutory['statutory'.$i]*-1,2)}}</td>
                                         @endfor
                                         <td class="text-right">{{number_format(array_sum($non_taxable_deduction) + $emp_data->sum('withholding_tax')*-1+ array_sum($statutory)*-1,2)}}</td>
                                     </tr>
                                     <tr style="background-color: #e0e0e0;">
                                         <td colspan="1"><strong>Net Pay</strong></td>
                                         @for ($i = 1; $i <= 12; $i++)
                                         <td class="text-right">
                                         {{number_format($emp_data->whereBetween('cut_off_date',[date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01', strtotime($from_date."-01-01")),date('Y-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-t', strtotime($from_date."-01-01"))])->sum('netpay'),2)}}
                                         </td>
                                         @endfor
                                         <td class="text-right">{{number_format($emp_data->sum('netpay'),2)}}</td>
                                     </tr>
                                 </tbody>
                             </table>
 
                     <br>
                     @else
                   @endif

                  </div>
                </div>

            </div>
          </div>
        
        </div>
    </div>
</div>
<iframe id="txtArea1" style="display:none"></iframe>
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
    function fnExcelReport() {
        var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
        var tables = document.querySelectorAll("table"); 

        tables.forEach((tab, index) => {
            tab_text += `<tr><td colspan="100%" style="background-color: #ddd; font-weight: bold;">Table ${index + 1}</td></tr>`;
            for (var j = 0; j < tab.rows.length; j++) {
                tab_text += "<tr>" + tab.rows[j].innerHTML + "</tr>";
            }
        });

        tab_text += "</table>";
        tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); 
        tab_text = tab_text.replace(/<img[^>]*>/gi, ""); 
        tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); 

        var blob = new Blob([tab_text], { type: "application/vnd.ms-excel" });
        var link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "Exported_Data.xls";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    $('.qty[value="0"]').closest("tr").hide();
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
                // 'copy', 
                // 'excel'
            ],
            columnDefs: 
            [
                {
                    "defaultContent": "-",
                    "targets": "_all"
                }
                ,
                {
                    "targets": [1, 2,3,4,5,6,7,8,9,10,11,12,13], // Adjust columns where numbers should align to the right
                    "className": "dt-right"
                }
            ],
            order: [] ,
            searching: false, // Disable the search box
            ordering: false,  // D
        });
    });
</script>
@endsection
