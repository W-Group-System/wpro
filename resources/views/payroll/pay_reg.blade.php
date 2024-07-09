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
        
        <div id="payroll" class="tab-pane  active">
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Generated Payroll 
                    {{-- <button type="button" class="btn btn-outline-danger btn-icon-text btn-sm"  data-toggle="modal" data-target="#payrollD">
                        <i class="ti-upload btn-icon-prepend"></i>                                                    
                        Upload
                    </button> --}}
                    
                    {{-- <a href='{{url('payroll.xlsx')}}' target='_blank'><button type="button" class="btn btn-primary btn-icon-text btn-sm">
                        <i class="ti-file btn-icon-prepend "></i>
                        Download Format
                    </button></a> --}}
                </h4>
                <form method='get' onsubmit='show();' enctype="multipart/form-data">
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
                          <label class="col-sm-4 col-form-label text-right">Date </label>
                          <div class="col-sm-8">
                            <select data-placeholder="Select Cutoff" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='cut_off' >
                                <option value="">-- Select Cutoff --</option>
                                @foreach($cut_off as $cut)
                                <option value="{{$cut->cut_off_date}}" @if($cut->cut_off_date == $cutoff) selected @endif >{{$cut->cut_off_date}}</option>
                                  @endforeach
                              </select>
                          </div>
                        </div>
                      </div>
                      <div class='col-md-3'>
                        <button type="submit" class="btn btn-primary mb-2">Submit</button>
                      </div>
                    </div>
                  </form>
                <div class="table-responsive">
                  <table class="table table-db table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Row No</th>
                            <th>Employee No</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Department</th>
                            <th>Cost Center</th>
                            <th>Account No</th>
                            <th>Pay Rate</th>
                            <th>Tax Status</th>
                            <th>DAYS RENDERED</th>
                            <th>BASIC PAY</th>
                            <th>LH ND</th>
                            <th>LH ND AMOUNT</th>
                            <th>LH ND GE</th>
                            <th>LH ND GE AMOUNT</th>
                            <th>LH OT</th>
                            <th>LH OT Amount</th>
                            <th>LH OT OVER 8</th>
                            <th>LH OT OVER 8 AMOUNT</th>
                            <th>REG ND</th>
                            <th>REG ND AMOUNT</th>
                            <th>REG OT</th>
                            <th>REG OT AMOUNT</th>
                            <th>REG OT ND</th>
                            <th>REG OT ND AMOUNT</th>
                            <th>RST ND</th>
                            <th>RST ND AMOUNT</th>
                            <th>RST ND GE</th>
                            <th>RST ND GE AMOUNT</th>
                            <th>RST OT</th>
                            <th>RST OT AMOUNT</th>
                            <th>RST OT OVER 8</th>
                            <th>RST OT OVER 8 AMOUNT</th>
                            <th>OVERTIME TOTAL</th>
                            <th>PL</th>
                            <th>PL AMOUNT</th>
                            <th>SL</th>
                            <th>SL AMOUNT</th>
                            <th>VL</th>
                            <th>VL AMOUNT</th>
                            <th>LEAVE AMOUNT TOTAL</th>
                            <th>SALARY ADJUSTMENT</th>
                            <th>TAXABLE BENEFITS TOTAL</th>
                            <th>GROSS TAXABLE INCOME</th>
                            <th>DAYS ABSENT</th>
                            <th>ABSENT AMOUNT</th>
                            <th>TARDINESS TOTAL</th>
                            <th>TARDINESS AMOUNT</th>
                            <th>UNDERTIME TOTAL</th>
                            <th>UNDERTIME AMOUNT</th>
                            <th>SSS EC</th>
                            <th>SSS EMPLOYEE SHARE</th>
                            <th>SSS EMPLOYER SHARE</th>
                            <th>HDMF EMPLOYEE SHARE</th>
                            <th>HDMF EMPLOYER SHARE</th>
                            <th>PHIC EMPLOYEE SHARE</th>
                            <th>PHIC EMPLOYER SHARE</th>
                            <th>MPF EMPLOYEE SHARE</th>
                            <th>MPF EMPLOYER SHARE</th>
                            <th>STATUTORY TOTAL</th>
                            <th>TAXABLE DEDUCTIBLE TOTAL</th>
                            <th>NET TAXABLE INCOME</th>
                            <th>WITHHOLDING TAX</th>
                            <th>DEMINIMIS</th>
                            {{-- <th>DEMINIMIS ADJUSTMENT</th>
                            <th>LOAD ALLOWANCE</th>
                            <th>OTHER ALLOWANCES</th>
                            <th>OTHER NTA</th>
                            <th>SSS LOAN REFUND</th>
                            <th>SUBLIQ</th> --}}
                            @foreach($allowances_total as $total_allow)
                            <th>{{$total_allow->allowance->name}}</th>
                            @endforeach
                            {{-- <th>OTHER ALLOWANCES</th> --}}
                            <th>NONTAXABLE BENEFITS TOTAL</th>
                            
                            {{-- <th>CANTEEN</th>
                            <th>EMERGENCY</th>
                            <th>HDMF CALAMITY LOAN</th>
                            <th>HDMF CONTRIBUTION UPGRADE</th>
                            <th>HDMF LOAN</th>
                            <th>MOTORCYCLE LOAN</th>
                            <th>RICE LOAN</th>
                            <th>SSS CALAMITY LOAN</th>
                            <th>SSS LOAN</th>
                            <th>STAFF HOUSE</th>
                            <th>WESLA LOAN</th> --}}
                            @foreach($instructions as $ins)
                            <th>{{$ins->benefit_name}}</th>
                            @endforeach
                            {{-- <th>Others</th> --}}
                            @foreach($loans_all as $loans_al)
                            <th>{{$loans_al->loan_type->loan_name}}</th>
                            @endforeach
                            <th>NONTAXABLE DEDUCTIBLE BENEFITS TOTAL</th>
                            <th>GROSS PAY</th>
                            <th>DEDUCTIONS TOTAL</th>
                            <th>NETPAY</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($names as $key => $name)
                        @php
                            $payroll_b = $dates->where('log_date',25)->first();
                            $payroll_a = $dates->where('log_date',10)->first();
                            $pay_rate = 0.00;
                            $basic_pay = 0.00;
                            $hourly_rate = 0.00;
                            $daily_rate = 0.00;
                            $de_minimis = 0.00;
                            $pl = 0.00;
                            $pl_amount = 0.00;
                            $sl = 0.00;
                            $sl_amount = 0.00;
                            $vl = 0.00;
                            $vl_amount = 0.00;
                            $salary_adjustment = 0.00;
                            $sss_ecc= 0.00;
                            $sss_er= 0.00;
                            $sss_ee= 0.00;
                            $hdmf = 0.00;
                            $philhealth = 0.00;
                            $wisp_ee = 0.00;
                            $wisp_er = 0.00;
                            $de_minimis_adj = 0.00;
                            $load_allowance = 0.00;
                            $other_allowances = 0.00;
                            $other_nta = 0.00;
                            $sss_loan_refund = 0.00;
                            $subliq = 0.00;

                            $canteen = 0.00;
                            $emergency = 0.00;
                            $hdmf_calamity_loan = 0.00;
                            $hdmf_contribution_upgrade = 0.00;
                            $hdmf_loan = 0.00;
                            $motorcycle_loan = 0.00;
                            $rice_loan = 0.00;
                            $sss_calamity_loan = 0.00;
                            $sss_loan = 0.00;
                            $staff_loan = 0.00;
                            $wesla_loan = 0.00;
                            
                            $loans = 0.00;
                            $allowances = 0.00;
                            
                            
                            $leave_total_amount = $pl_amount+$sl_amount+$vl_amount;
                            if(!empty($name->employee->salary))
                            {
                              $pay_rate = $name->employee->salary->basic_salary;
                              $basic_pay = $name->employee->salary->basic_salary/2;
                              $daily_rate = ($name->employee->salary->basic_salary)*12/313;
                              $hourly_rate = $daily_rate/8;
                              $de_minimis = $name->employee->salary->de_minimis/2;
                            }
                            $hours = 0;
                            foreach($absents_data->where('employee_no',$name->employee_no) as $absent_dat)
                            {
                              // dd($absent_dat->shift);
                              $shift = $absent_dat->shift;
                              $shift = str_replace("(Semi-Flexi)", '', $shift);
                              $shift = str_replace("(Semi - flexi)", '', $shift);
                              $shift = str_replace("(Flexi)", '', $shift);
                              // $shift = str_replace(" ", '', $shift);
                              $shift = explode("-",$shift);
                              $start = strtotime($shift[0]);
                              $end = strtotime($shift[1]);
                              $hours_count = ($end - $start)/3600;
                              if($start > $end)
                              {
                                $hours_count = (($start - $end)-8)/3600;
                              }
                              if($hours_count>8)
                              {
                                $hours_count = $hours_count-1;
                              }

                              $hours_data = $hours_count*($absent_dat->abs-$absent_dat->lv_w_pay);
                              $hours = $hours+$hours_data;
                            }
                          
                            $total_loans = 0;
                            if(!empty($name->employee->loan))
                            {
                              $loa = ($name->employee->loan);
                              $every_cut_off_loan = $loa->whereIn('schedule', ['Every cut off', 'This cut off'])->sum('monthly_ammort_amt');
                              $loans = $loa->where('schedule', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off')->sum('monthly_ammort_amt');
                              $total_loans = $loans+$every_cut_off_loan;
                            }
                            $total_allowances=0;
                            if (!empty($name->employee->allowances)) {
                                $allow = $name->employee->allowances;
                                $every_cut_off = $allow->whereIn('schedule', ['Every cut off', 'This cut off'])->sum('allowance_amount');
                                $allowances = $allow->where('schedule', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off')->sum('allowance_amount');
                                $total_allowances = $allowances + $every_cut_off;
                            }
                            $total_payroll_instructions = 0;
                            if(!empty($name->employee->pay_instructions))
                            {
                              $payroll_instructions = ($name->employee->pay_instructions);
                              $every_cut_off_payroll_instructions = $payroll_instructions->whereIn('frequency', ['Every cut off', 'This cut off'])->sum('amount');
                              $other = $payroll_instructions->where('frequency', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off')->sum('amount');
                              $total_payroll_instructions = $other+$every_cut_off_payroll_instructions;
                            }

                            $total_lh_nd_amount = $name->total_lh_nd*$hourly_rate*.2;
                            $total_lh_nd_over_eight = $name->total_lh_nd_over_eight*$hourly_rate*.26;
                            $total_lh_ot = $name->total_lh_ot*$hourly_rate;
                            $total_lh_ot_over_eight = $name->total_lh_ot_over_eight*$hourly_rate*2.6;
                            $total_reg_nd = $name->total_reg_nd*$hourly_rate*.1;
                            $total_reg_ot = $name->total_reg_ot*$hourly_rate*1.25;
                            $total_reg_ot_nd = $name->total_reg_ot_nd*$hourly_rate*.125;
                            $total_rst_nd = $name->total_rst_nd*$hourly_rate*.13;
                            $total_rst_nd_over_eight = $name->total_rst_nd_over_eight*$hourly_rate*.169;
                            $total_rst_ot = $name->total_rst_ot*$hourly_rate*1.3;
                            $total_rst_ot_over_eight = $name->total_rst_ot_over_eight*$hourly_rate*1.69;
                            $total_ot_pay = $total_lh_ot+$total_lh_ot_over_eight+$total_reg_ot+$total_reg_ot_nd+$total_rst_ot+$total_rst_ot_over_eight;
                            $total_taxable_benefits = $salary_adjustment;
                            $gross_taxable_income = $basic_pay+$total_ot_pay+$leave_total_amount+$total_taxable_benefits;
                            
                            $total_late_min = $name->total_late_min/60*$hourly_rate;
                            $total_undertime_min = $name->total_undertime_min/60*$hourly_rate;
                            
                            $total_abs_count = $name->total_abs-$name->total_lv_w_pay;
                            $total_abs = $hours*$hourly_rate;
                            
                            $government_amount = $gross_taxable_income-$total_abs-$total_late_min- $total_undertime_min;
                            if($payroll_b)
                            {
                              $sss_amount = $sss->where('salary_from','>=',$government_amount)->first();
                              $sss_ecc = $sss_amount->ecc;
                              $sss_ee = $sss_amount->regular_ee;
                              $sss_er = $sss_amount->regular_er;
                              $wisp_ee = $sss_amount->wisp_ee;
                              $wisp_er = $sss_amount->wisp_er;
                              $hdmf = 200.00;
                              $philhealth = ($pay_rate*.05)/2;
                            }
                            
                            $statutory = $sss_ee+$wisp_ee+$hdmf+$philhealth;
                            $taxable_deductable_total = $statutory+$total_abs+$total_late_min+$total_undertime_min;
                            $net_taxable_income = $gross_taxable_income-$taxable_deductable_total;
                            $tax = compute_tax($net_taxable_income);
                            $load_allowance = 0.00;
                            $other_allowances = 0.00;
                            $other_nta = 0.00;
                            $sss_loan_refund = 0.00;
                            $subliq = 0.00;
                            $non_taxable_benefits = $load_allowance+$other_allowances+$other_nta+$sss_loan_refund+$subliq;
                        @endphp
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$name->employee_no}}</td>
                            <td>{{$name->employee->last_name}}</td>
                            <td>{{$name->employee->first_name}}</td>
                            <td>{{$name->employee->middle_name}}</td>
                            <td>{{$name->employee->department->name}}</td>
                            <td></td>
                            <td>{{$name->employee->bank_account_number}}</td>
                            <td>{{number_format($pay_rate,2)}}</td>
                            <td></td>
                            <td></td>
                            <td>{{number_format($basic_pay,2)}}</td>
                            <td>{{number_format($name->total_lh_nd,2)}}</td>
                            <td>{{number_format($total_lh_nd_amount,2)}}</td>
                            <td>{{number_format($name->total_lh_nd_over_eight,2)}}</td>
                            <td>{{number_format($total_lh_nd_over_eight,2)}}</td>
                            <td>{{number_format($name->total_lh_ot,2)}}</td>
                            <td>{{number_format($total_lh_ot,2)}}</td>
                            <td>{{number_format($name->total_lh_ot_over_eight,2)}}</td>
                            <td>{{number_format($total_lh_ot_over_eight,2)}}</td>
                            <td>{{number_format($name->total_reg_nd,2)}}</td>
                            <td>{{number_format($total_reg_nd,2)}}</td>
                            <td>{{number_format($name->total_reg_ot,2)}}</td>
                            <td>{{number_format($total_reg_ot,2)}}</td>
                            <td>{{number_format($name->total_reg_ot_nd,2)}}</td>
                            <td>{{number_format($total_reg_ot_nd,2)}}</td>
                            <td>{{number_format($name->total_rst_nd,2)}}</td>
                            <td>{{number_format($total_rst_nd,2)}}</td>
                            <td>{{number_format($name->total_rst_nd_over_eight,2)}}</td>
                            <td>{{number_format($total_rst_nd_over_eight,2)}}</td>
                            <td>{{number_format($name->total_rst_ot,2)}}</td>
                            <td>{{number_format($total_rst_ot,2)}}</td>
                            <td>{{number_format($name->total_rst_ot_over_eight,2)}}</td>
                            <td>{{number_format($total_rst_ot_over_eight,2)}}</td>
                            <td>{{number_format($total_ot_pay,2)}}</td>
                            <td>{{number_format($pl,2)}}</td>
                            <td>{{number_format($pl_amount,2)}}</td>
                            <td>{{number_format($sl,2)}}</td>
                            <td>{{number_format($sl_amount,2)}}</td>
                            <td>{{number_format($vl,2)}}</td>
                            <td>{{number_format($vl_amount,2)}}</td>
                            <td>{{number_format($leave_total_amount,2)}}</td>
                            <td>{{number_format($salary_adjustment,2)}}</td>
                            <td>{{number_format($total_taxable_benefits,2)}}</td>
                            <td>{{number_format($gross_taxable_income,2)}}</td>
                            <td>{{number_format($total_abs_count,2)}}</td>
                            <td>{{number_format($total_abs,2)}}</td>
                            <td>{{number_format($name->total_late_min,2)}}</td>
                            <td>{{number_format($total_late_min,2)}}</td>
                            <td>{{number_format($name->total_undertime_min,2)}}</td>
                            <td>{{number_format($total_undertime_min,2)}}</td>
                            <td>{{number_format($sss_ecc,2)}}</td>
                            <td>{{number_format($sss_ee,2)}}</td>
                            <td>{{number_format($sss_er,2)}}</td>
                            <td>{{number_format($hdmf,2)}}</td>
                            <td>{{number_format($hdmf,2)}}</td>
                            <td>{{number_format($philhealth,2)}}</td>
                            <td>{{number_format($philhealth,2)}}</td>
                            <td>{{number_format($wisp_ee,2)}}</td>
                            <td>{{number_format($wisp_er,2)}}</td>
                            <td>{{number_format($statutory,2)}}</td>
                            <td>{{number_format($taxable_deductable_total,2)}}</td>
                            <td>{{number_format($net_taxable_income,2)}}</td>
                            <td>{{number_format($tax,2)}}</td>
                            <td>{{number_format($de_minimis,2)}}</td>
                            {{-- <td>{{number_format($de_minimis_adj,2)}}</td> --}}
                            {{-- <td>{{number_format($load_allowance,2)}}</td>
                            <td>{{number_format($other_allowances,2)}}</td>
                            <td>{{number_format($other_nta,2)}}</td>
                            <td>{{number_format($sss_loan_refund,2)}}</td>
                            <td>{{number_format($subliq,2)}}</td> --}}
                            @foreach($allowances_total as $total_allow)
                            @php
                                $allllowance = 0;
                                 if (!empty($name->employee->allowances)) {
                                $allow = $name->employee->allowances;
                                $every_cut_off = $allow->where('allowance_id',$total_allow->allowance_id)->whereIn('schedule', ['Every cut off', 'This cut off'])->sum('allowance_amount');
                                $bbb = $allow->where('allowance_id',$total_allow->allowance_id)->where('schedule', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off')->sum('allowance_amount');
                                $allllowance = $bbb+$every_cut_off;
                                }
                            @endphp
                            <td>{{number_format($allllowance,2)}}</td>
                            @endforeach
                            {{-- <td><a href='#' data-toggle="modal" data-target="#allowances{{$name->employee_no}}">{{number_format($total_allowances,2)}}</a></td> --}}
                            {{-- <td>{{number_format($total_allowances,2)}}</td> --}}
                            <td>{{number_format($total_allowances+$de_minimis,2)}}</td>
                            {{-- <td>{{number_format($canteen,2)}}</td>
                            <td>{{number_format($emergency,2)}}</td>
                            <td>{{number_format($hdmf_calamity_loan,2)}}</td>
                            <td>{{number_format($hdmf_contribution_upgrade,2)}}</td>
                            <td>{{number_format($hdmf_loan,2)}}</td>
                            <td>{{number_format($motorcycle_loan,2)}}</td>
                            <td>{{number_format($rice_loan,2)}}</td>
                            <td>{{number_format($sss_calamity_loan,2)}}</td>
                            <td>{{number_format($sss_loan,2)}}</td>
                            <td>{{number_format($staff_loan,2)}}</td>
                            <td>{{number_format($wesla_loan,2)}}</td> --}}
                            @foreach($instructions as $ins)
                            @php
                                $totasions = 0;
                                if(!empty($name->employee->pay_instructions))
                                {
                                  $payroll_instructions = ($name->employee->pay_instructions);
                                  $every_cut_off_payroll_instructions = $payroll_instructions->where('benefit_name',$ins->benefit_name)->whereIn('frequency', ['Every cut off', 'This cut off'])->sum('amount');
                                  $other = $payroll_instructions->where('benefit_name',$ins->benefit_name)->where('frequency', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off')->sum('amount');
                                  $totasions = $other+$every_cut_off_payroll_instructions;
                                }
                            @endphp
                            <td>{{number_format($totasions,2)}}</td>
                            @endforeach
                            {{-- <td><a href='#' data-toggle="modal" data-target="#payroll_instruction{{$name->employee_no}}">{{number_format($total_payroll_instructions,2)}}</a></td> --}}
                            {{-- <td><a href='#' data-toggle="modal" data-target="#loan{{$name->employee_no}}">{{number_format($total_loans,2)}}</a></td> --}}
                            @foreach($loans_all as $loans_al)
                            @php
                            $lllloan = 0;
                            if(!empty($name->employee->loan))
                            {
                              $loa = ($name->employee->loan);
                              $every_cut_off_loan = $loa->where('loan_type_id',$loans_al->loan_type_id)->whereIn('schedule', ['Every cut off', 'This cut off'])->sum('monthly_ammort_amt');
                              $loans = $loa->where('loan_type_id',$loans_al->loan_type_id)->where('schedule', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off')->sum('monthly_ammort_amt');
                              $lllloan = $loans+$every_cut_off_loan;
                            }
                            @endphp
                            <td>{{number_format($lllloan,2)}}</td>
                            @endforeach
                            <td>{{number_format($total_loans,2)}}</td> 
                            <td>{{number_format($gross_taxable_income+$total_allowances+$de_minimis,2)}}</td>
                            <td>{{number_format($taxable_deductable_total+$total_loans+$tax,2)}}</td>
                            <td>{{number_format($gross_taxable_income+$total_allowances+$de_minimis-$taxable_deductable_total-$total_loans-$tax+$total_payroll_instructions,2)}}</td>
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
{{-- @foreach($names as $name)
@php
    $payroll_b = $dates->where('log_date',25)->first();
    $payroll_a = $dates->where('log_date',10)->first();
@endphp
@include('payroll.allowances')
@include('payroll.instructions')
@include('payroll.loans')
@endforeach --}}
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
    new DataTable('.table-db', {
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
    {{-- @foreach($payrolls as $payroll)
        @include('payroll.view_payroll')   
    @endforeach
    @include('payroll.upload_payroll') --}}
@endsection


