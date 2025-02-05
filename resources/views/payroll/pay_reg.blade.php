@extends('layouts.header')

@section('content')
<script src = "https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.0/FileSaver.min.js" integrity="sha512-csNcFYJniKjJxRWRV1R7fvnXrycHP6qDR21mgz1ZP55xY5d+aHLfo9/FcGDQLfn2IfngbAHd8LdfsagcCqgTcQ==" crossorigin = "anonymous" referrerpolicy = "no-referrer"> </script>
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
                            <option value="{{$comp->id}}" @if ($comp->id == $company) selected @endif>{{$comp->company_code}}</option>
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
                <form method="POST" action="{{ url('payreg?company='.$company.'&cut_off='.$cutoff.'&from='.$from.'&to='.$to) }}" id='PagRegForm' onsubmit="return showConfirmation()" enctype="multipart/form-data">
                  @csrf
                  <div class="table-responsive">
                    @if($cutoff)
                    <div class='row'>
                      <div class='col-md-4'>
                        Posting Date <input name='posting_date' id='posting_date'  class="form-control form-control-sm" type='date' value='{{date('Y-m-d')}}' required>
                      </div>
                      <div class='col-md-4'>
                        <button type="submit" class="btn btn-success mb-1">Post</button>
                      </div>

                    </div>
                    
                    <button  class='btn btn-info btn-sm' type = "button" onclick = "CreateTextFile();">Download Txt</button>
                    @endif
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
                              <th>SH ND</th>
                              <th>SH ND AMOUNT</th>
                              <th>SH ND GE</th>
                              <th>SH ND GE AMOUNT</th>
                              <th>SH OT</th>
                              <th>SH OT Amount</th>
                              <th>SH OT OVER 8</th>
                              <th>SH OT OVER 8 AMOUNT</th>
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
                              {{-- <th>PL</th>
                              <th>PL AMOUNT</th>
                              <th>SL</th>
                              <th>SL AMOUNT</th>
                              <th>VL</th>
                              <th>VL AMOUNT</th>
                              <th>LEAVE AMOUNT TOTAL</th> --}}
                              @foreach($salary_adjustments as $salary_adjustment)
                              <th>{{$salary_adjustment->name}}</th>
                              @endforeach
                              <th>TOTAL SALARY ADJUSTMENT</th>
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
                              <th>Other Allowances</th>
                              <th>SUBLIQ</th>
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
                              <th>SSS 1st</th>
                              <th>SSS 2nd</th>
                              <th>PHIC 1st</th>
                              <th>PHIC 2nd</th>
                          </tr>
                      </thead>
                      <tbody>
                        @php
                            $paytext = [];
                            $total_net = 0;
                        @endphp
                          @foreach($names as $key => $name)
                          @php
                              $payroll_b = $dates->where('log_date',25)->first();
                              $payroll_a = $dates->where('log_date',10)->first();
                              $days_rendered = $name->shift_count-$name->total_abs+$name->total_lv_w_pay;
                              $pay_rate = 0.00;
                              $basic_pay = 0.00;
                              $other_allowances_basic_pay = 0.00;
                              $subliq = 0.00;
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
                              $basic_other_allowances = 0.00;
                              $hourly_rate_other_allowance = 0.00;
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
                              if($name->employee->employee_code == "A278816")
                                {
                                  $days_rendered =0 ;
                                foreach($shifts->where('employee_no',$name->employee_no) as $shift_data)
                                {
                                  // dd($absent_dat->shift);
                                
                                  $shift = $shift_data->shift;
                                  $shift = str_replace("(Semi-Flexi)", '', $shift);
                                  $shift = str_replace("(Semi - flexi)", '', $shift);
                                  $shift = str_replace("(Flexi)", '', $shift);
                                  // $shift = str_replace(" ", '', $shift);
                                  $shift = explode("-",$shift);
                                  $start = strtotime($shift[0]);
                                  $end = strtotime($shift[1]);
                                  $shift_count = ($end - $start)/3600;
                                  if($start > $end)
                                  {
                                    
                                    $shift_count = ((($start - $end))/3600)-8;
                                  }
                                  if($shift_count >8)
                                  {
                                    $shift_count = $shift_count -1;
                                  }
                                    $shift_count = $shift_count/8;
                                  
                                    // dd($shift_count);
                                  $days_rendered = $days_rendered+$shift_count;
                                }
                                
                              }
                              if(!empty($name->employee->salary))
                              {
                                $pay_rate = $name->employee->salary->basic_salary;
                               
                                $basic_pay = $name->employee->salary->basic_salary/2;
                                $other_allowances_basic_pay = $name->employee->salary->other_allowance/2;
                                // dd($name->employee->salary);
                                $subliq = $name->employee->salary->subliq/2;
                               
                                $daily_rate = (($name->employee->salary->basic_salary)*12)/313;
                              
                                $hourly_rate = $daily_rate/8;
                                $hourly_rate_other_allowance = (($name->employee->salary->other_allowance)*12/313)/8;
                                $hourly_rate_subliq = (($name->employee->salary->subliq)*12/313)/8;
                                $de_minimis = $name->employee->salary->de_minimis/2;
                                
                                // dd($name->employee);
                                // if($name->employee->work_description == "Non-Monthly")
                                // {
                                //  $d = ($name->employee->salary->de_minimis*12)/313;
                                  
                                //  $basic_pay = $daily_rate*(number_format($days_rendered,2));
                                  // dd($days_rendered);
                                  
                                  
                                //  $de_minimis = $d*($days_rendered);
                                // }

                                if (isset($name->employee) && $name->employee->work_description == "Non-Monthly") {
                                  $d = ($name->employee->salary->de_minimis ?? 0) * 12 / 313;

                                  $days_rendered = $days_rendered ?? 0; // Default to 0 if not set
                                  $daily_rate = $daily_rate ?? 0; // Default to 0 if not set

                                  $basic_pay = $daily_rate * number_format($days_rendered, 2);
                                  $de_minimis = $d * $days_rendered;
                                }
                                
                                if($name->employee->level == 4)
                                {
                                  $basic_pay = $name->employee->salary->basic_salary;
                                  $de_minimis = $name->employee->salary->de_minimis;
                                  $subliq = $name->employee->salary->subliq;
                                }
                              }
                              $hours = 0;
                              // $hours_count =0;
                              foreach($absents_data->where('employee_no',$name->employee_no) as $absent_dat)
                              {
                                // dd($absent_dat->shift);
                                $shift = $absent_dat->shift;
                                $shift = str_replace("(Semi-Flexi)", '', $shift);
                                $shift = str_replace("(Semi - flexi)", '', $shift);
                                $shift = str_replace("(Flexi)", '', $shift);
                                // $shift = str_replace(" ", '', $shift);
                                $shift = explode("-",$shift);
                                $start = strtotime($absent_dat->log_date." ".$shift[0]);
                                $end = strtotime($absent_dat->log_date." ".$shift[1]);
                                $hours_count = ($end - $start)/3600;
                                
                                // dd($hours_count);
                                if($hours_count < 0)
                                {
                                  
                                  $hours_count = ((($end+86400 - $start))/3600);
                                
                                }
                                // dd($hours_count);
                                if($hours_count>8)
                                {
                                  // dd($hours_count);
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
                              $salary_adjustment = 0;
                              if(!empty($name->employee->salary_adjustments))
                              {
                                $adjustments = ($name->employee->salary_adjustments)->sum('amount');

                                $salary_adjustment = $salary_adjustment+$adjustments;
                              }
                              $total_allowances=0;
                              $total_allowances_sss=0;
                              if (!empty($name->employee->allowances)) {
                                  $allow = $name->employee->allowances;
                                  $every_cut_off = $allow->whereIn('schedule', ['Every cut off', 'This cut off'])->sum('allowance_amount');
                                  $total_allowances_sss_every_cut_off=$allow->whereIn('schedule', ['Every cut off', 'This cut off'])->where('allowance_id','!=',9)->sum('allowance_amount');
                          
                                  $allowances = $allow->where('schedule', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off')->sum('allowance_amount');
                                  $total_allowances_sss_allowances = $allow->where('schedule', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off')->where('allowance_id','!=',9)->sum('allowance_amount');
                                  $total_allowances = $allowances + $every_cut_off;
                                  $total_allowances_sss = $total_allowances_sss_allowances + $total_allowances_sss_every_cut_off;
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
                              $total_sh_nd_amount = $name->total_sh_nd*$hourly_rate*.13;
                              $total_sh_nd_over_eight = $name->total_sh_nd_over_eight*$hourly_rate*.169;
                              $total_sh_ot = $name->total_sh_ot*$hourly_rate*.3;
                              $total_sh_ot_over_eight = $name->total_sh_ot_over_eight*$hourly_rate*1.69;
                              $total_reg_nd = $name->total_reg_nd*$hourly_rate*.1;
                              $total_reg_ot = $name->total_reg_ot*$hourly_rate*1.25;
                              $total_reg_ot_nd = $name->total_reg_ot_nd*$hourly_rate*.125;
                              $total_rst_nd = $name->total_rst_nd*$hourly_rate*.13;
                              $total_rst_nd_over_eight = $name->total_rst_nd_over_eight*$hourly_rate*.169;
                              $total_rst_ot = $name->total_rst_ot*$hourly_rate*1.3;
                              $total_rst_ot_over_eight = $name->total_rst_ot_over_eight*$hourly_rate*1.69;
                              $total_ot_pay = $total_lh_ot+$total_lh_ot_over_eight+$total_reg_ot+$total_reg_ot_nd+$total_rst_ot+$total_rst_ot_over_eight+$total_rst_nd+$total_rst_nd_over_eight+$total_lh_nd_amount+$total_lh_nd_over_eight+$total_reg_nd+$total_sh_nd_amount+$total_sh_nd_over_eight+$total_sh_ot+$total_sh_ot_over_eight;
                              // if($name->employee->employee_code == "A3177924")
                              // {
                              //   $salary_adjustment = 2070.28;
                              // }
                              // if($name->employee->employee_code == "A3178124")
                              // {
                              //   $salary_adjustment = 2472.84;
                              // }
                              // if($name->employee->employee_code == "A189423")
                              // {
                              //   $salary_adjustment = 11530.49;
                              // }
                               
                              // if($name->employee->employee_code == "A366523")
                              // {
                              //   $salary_adjustment = 3463.29;
                              // }
                              $total_taxable_benefits = $salary_adjustment;
                              
                              $gross_taxable_income = $basic_pay+$total_ot_pay+$leave_total_amount+$total_taxable_benefits;
                              
                              $total_late_min = $name->total_late_min/60*$hourly_rate;
                              $total_undertime_min = $name->total_undertime_min/60*$hourly_rate;
                              
                              $total_abs_count = $name->total_abs-$name->total_lv_w_pay;
                              $total_abs = $hours*$hourly_rate;
                              if($name->employee->work_description == "Non-Monthly")
                              {
                                $total_abs = 0;
                              }
                              if($name->employee->work_description != "Non-Monthly")
                              {
                                
                                if($other_allowances_basic_pay > 0)
                                {
                                  
                                  // dd($other_allowances_basic_pay);
                                  $other_allowances_basic_pay = ($other_allowances_basic_pay)-(($hours+$name->total_late_min/60+$name->total_undertime_min/60)*$hourly_rate_other_allowance);
                                }
                                if($subliq > 0)
                                {
                                  $subliq = ($subliq)-(($hours+$name->total_late_min/60+$name->total_undertime_min/60)*$hourly_rate_subliq);
                                }
                               
                              }

                              // dd($de_minimis);
                              $government_amount = $gross_taxable_income-$total_abs-$total_late_min- $total_undertime_min+$de_minimis+$other_allowances_basic_pay+$subliq;
                              $lastccc = 0;
                              if($payroll_b)
                              {
                                $last_c = $last_cut_off->where('employee_no',$name->employee_no)->where('cut_off_date','>',date('Y-m-d', strtotime($name->cut_off_date . ' -17 days')))->first();
                                // dd($name->cut_off_date." - ".date('Y-m-d', strtotime($name->cut_off_date . ' -17 days')));
                                if($last_c)
                                {
                                 
                                  $sss_allowance=($last_c->pay_allowances)->where('allowance_id','!=',9)->sum('amount');
                                  
                                  $lastccc = $last_c->gross_taxable_income-$last_c->absent_amount-$last_c->tardiness_amount-$last_c->undertime_amount+$last_c->deminimis+$last_c->other_allowances_basic_pay+$last_c->subliq;
                                  // dd($government_amount);
                                  // dd($last_c);
                                // if($name->employee->employee_code == "A3177424")
                                //  {
                                //    $lastccc = $lastccc +500;
                                //  }
                                //  if($name->employee->employee_code == "A3172023")
                                //  {
                                //    $lastccc = $lastccc +3150;
                                //  }
                                //   if($name->employee->employee_code == "A2104524")
                                // {
                                //   $government_amount = $government_amount +1000;
                                // }
                                //   if($name->employee->employee_code == "A2104224")
                                // {
                                //   $government_amount = $government_amount +1000;
                                // }
                              //     if($name->employee->employee_code == "A2104424")
                              // {
                              //     $government_amount = $government_amount +1020;
                              //   }
                                //   if($name->employee->employee_code == "A2104324")
                                // {
                                //   $government_amount = $government_amount +1020;
                                // }
                                // dd($lastccc);
                                // dd($government_amount);
                                  // $ot_adjustments= ($name->employee->salary_adjustments)->where('name',"OT Adjustment")->sum('amount');
                                  // $salary_adjustment= ($name->employee->salary_adjustments)->where('name',"Salary Adjustment")->sum('amount');
                                  $payroll_instructions_adjustment = ($name->employee->pay_instructions);
                                  $every_cut_off_payroll_instructions_adjustment = $payroll_instructions_adjustment->where('benefit_name',"De Minimis Adjustment")->whereIn('frequency', ['Every cut off', 'This cut off'])->sum('amount');
                                  $other_adjustment = $payroll_instructions_adjustment->where('benefit_name',"De Minimis Adjustment")->where('frequency', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off')->sum('amount');
                                  $totasions_adjustment_adjustment = $other_adjustment+$every_cut_off_payroll_instructions_adjustment;
                                  $government_amount = $government_amount+$totasions_adjustment_adjustment;
                                  $government_amount = round($government_amount+$lastccc,2);
                                 
                                
                                }
                                // dd($government_amount);
                                $sss_amount = $sss->where('salary_to','>=',$government_amount)->first();
                                // dd($sss_amount);
                                if($sss_amount)
                                {
                                  // dd($sss_amount);
                                  $sss_ecc = $sss_amount->ecc;
                                  $sss_ee = $sss_amount->regular_ee;
                                  $sss_er = $sss_amount->regular_er;
                                  $wisp_ee = $sss_amount->wisp_ee;
                                  $wisp_er = $sss_amount->wisp_er;
                                }
                                if($name->employee->employee_code == "A287819")
                                {
                                  $sss_ecc = 0;
                                  $sss_ee = 1190;
                                  $sss_er = 0;
                                  $wisp_ee = 0;
                                  $wisp_er = 0;
                                }
                                if($name->employee->employee_code == "A180518")
                                {
                                  $sss_ecc = 0;
                                  $sss_ee = 2590;
                                  $sss_er = 0;
                                  $wisp_ee = 0;
                                  $wisp_er = 0;
                                }
                               
                                if($name->employee->employee_code == "A178517")
                                {
                                  $sss_ecc = 0;
                                  $sss_ee = 2800;
                                  $sss_er = 0;
                                  $wisp_ee = 0;
                                  $wisp_er = 0;
                                }
                                if($name->employee->employee_code == "A162313")
                                {
                                  $sss_ecc = 0;
                                  $sss_ee = 4200;
                                  $sss_er = 0;
                                  $wisp_ee = 0;
                                  $wisp_er = 0;
                                }
                                if($name->employee->employee_code == "A190524")
                                {
                                  $sss_ecc = 0;
                                  $sss_ee = 0;
                                  $sss_er = 0;
                                  $wisp_ee = 0;
                                  $wisp_er = 0;
                                }
                               
                                
                                $hdmf = 200.00;
                                if($name->employee->employee_code == "A190524")
                                {
                                  $hdmf = 0;
                                }
                                $previous_pay_rate = 0;
                                $previous_basic_pay_rate = 0;
                                if($last_c)
                                {
                                  $previous_pay_rate = $last_c->pay_rate/2;
                                  $previous_basic_pay_rate = $last_c->basic_pay;
                                
                                }
                                // dd($basic_pay);
                                if($previous_pay_rate > 0)
                                {
                                  $philhealth = (($previous_pay_rate+($pay_rate/2))*.05)/2;
                                }
                                else {
                                  
                                  $philhealth = (($previous_pay_rate+($pay_rate/2))*.05)/2;
                                }
                                // dd($philhealth);
                              
                                if($name->employee->work_description == "Non-Monthly")
                                {
                                  // dd($basic_pay);
                                  $philhealth = (($basic_pay + $previous_basic_pay_rate)*.05)/2;
                                }
                                if($philhealth >= 2500)
                                {
                                  $philhealth = 2500;
                                }
                                if($philhealth <= 250)
                                {
                                  $philhealth = 250;
                                }
                                if($name->employee->employee_code == "A287819")
                                {
                                  $philhealth = 500;
                                }
                                if($name->employee->employee_code == "A178517")
                                {
                                  $philhealth = 500;
                                }
                                if($name->employee->employee_code == "A180518")
                                {
                                  $philhealth = 500;
                                }
                                if($name->employee->employee_code == "A162313")
                              {
                                $philhealth = 5000;
                              }
                                if($name->employee->employee_code == "A190524")
                                {
                                  $philhealth = 0;
                                }
                                if($name->employee->employee_code == "M1010")
                                {
                                  $philhealth = 0;
                                  $hdmf = 0;
                                  $sss_ecc = 0;
                                  $sss_ee = 0;
                                  $sss_er = 0;
                                  $wisp_ee = 0;
                                  $wisp_er = 0;
                                }
                                if($name->employee->employee_code == "A188323")
                                {
                                  $philhealth = 0;
                                  $hdmf = 0;
                                  $sss_ecc = 0;
                                  $sss_ee = 0;
                                  $sss_er = 0;
                                  $wisp_ee = 0;
                                  $wisp_er = 0;
                                }
                                if($name->employee->employee_code == "A185221")
                                {
                                  $philhealth = 0;
                                  $hdmf = 0;
                                  $sss_ecc = 0;
                                  $sss_ee = 0;
                                  $sss_er = 0;
                                  $wisp_ee = 0;
                                  $wisp_er = 0;
                                }
                              }
                              
                              $statutory = $sss_ee+$wisp_ee+$hdmf+$philhealth;
                              $taxable_deductable_total = $statutory+$total_abs+$total_late_min+$total_undertime_min;
                              $net_taxable_income = $gross_taxable_income-$taxable_deductable_total;
                         
                              if($name->employee->employee_code == "A162313")
                              {
                                
                                $net_taxable_income = $gross_taxable_income;
                              }
                              $tax = compute_tax($net_taxable_income,$name->employee->level);
                              if($name->employee->employee_code == "A162313")
                              {
                                $tax = $net_taxable_income*.05;
                              }
                              if($name->employee->employee_code == "A287819")
                              {
                                $tax = $net_taxable_income*.05;
                              }
                              if($name->employee->employee_code == "A188323")
                              {
                                $tax = $net_taxable_income*.05;
                              }
                              if($name->employee->employee_code == "A180518")
                              {
                                $tax = $net_taxable_income*.05;
                              }
                              if($name->employee->employee_code == "A178517")
                              {
                                $tax = $net_taxable_income*.05;
                              }
                              if($name->employee->employee_code == "M1010")
                              {
                                $tax = 0;
                              }
                              $load_allowance = 0.00;
                              $other_nta = 0.00;
                              $sss_loan_refund = 0.00;
                              $non_taxable_benefits = $load_allowance+$other_allowances+$other_nta+$sss_loan_refund+$subliq+$other_allowances_basic_pay;
                          @endphp
                          <tr>
                              <td>{{$key+1}}</td>
                              <td>{{$name->employee_no}} <input type='hidden' name='employee_no[{{$key+1}}]' value="{{$name->employee_no}}"></td>
                              <td>{{$name->employee->last_name}} <input type='hidden' name='last_name[{{$key+1}}]' value="{{$name->employee->last_name}}"><input type='hidden' name='work_description[{{$key+1}}]' value="{{$name->employee->work_description}}"></td>
                              <td>{{$name->employee->first_name}} <input type='hidden' name='first_name[{{$key+1}}]' value="{{$name->employee->first_name}}"></td>
                              <td>{{$name->employee->middle_name}} <input type='hidden' name='middle_name[{{$key+1}}]' value="{{$name->employee->middle_name}}"></td>
                              <td>{{$name->employee->department->name}} <input type='hidden' name='department_name[{{$key+1}}]' value="{{$name->employee->department->name}}"></td>
                              <td><input type='hidden' name='cost_center[{{$key+1}}]' value=""></td>
                              <td>{{$name->employee->bank_account_number}} <input type='hidden' name='bank_account_number[{{$key+1}}]' value="{{$name->employee->bank_account_number}}"></td>
                              <td>{{number_format($pay_rate,2)}}<input type='hidden' name='pay_rate[{{$key+1}}]' value="{{$pay_rate}}"></td>
                              <td><input type='hidden' name='tax_status[{{$key+1}}]' value=""></td>
                              <td><input type='hidden' name='days_rendered[{{$key+1}}]' value="{{$days_rendered}}">{{number_format($days_rendered,2)}}</td>
                              <td>{{number_format($basic_pay,2)}}<input type='hidden' name='basic_pay[{{$key+1}}]' value="{{$basic_pay}}"></td>
                              <td>{{number_format($name->total_lh_nd,2)}}<input type='hidden' name='name_total_lh_nd[{{$key+1}}]' value="{{$name->total_lh_nd}}"></td>
                              <td>{{number_format($total_lh_nd_amount,2)}}<input type='hidden' name='total_lh_nd_amount[{{$key+1}}]' value="{{$total_lh_nd_amount}}"></td>
                              <td>{{number_format($name->total_lh_nd_over_eight,2)}}<input type='hidden' name='name_total_lh_nd_over_eight[{{$key+1}}]' value="{{$name->total_lh_nd_over_eight}}"></td>
                              <td>{{number_format($total_lh_nd_over_eight,2)}}<input type='hidden' name='total_lh_nd_over_eight[{{$key+1}}]' value="{{$total_lh_nd_over_eight}}"></td>
                              <td>{{number_format($name->total_lh_ot,2)}}<input type='hidden' name='name_total_lh_ot[{{$key+1}}]' value="{{$name->total_lh_ot}}"></td>
                              <td>{{number_format($total_lh_ot,2)}}<input type='hidden' name='total_lh_ot[{{$key+1}}]' value="{{$total_lh_ot}}"></td>
                              <td>{{number_format($name->total_lh_ot_over_eight,2)}}<input type='hidden' name='name_total_lh_ot_over_eight[{{$key+1}}]' value="{{$name->total_lh_ot_over_eight}}"></td>
                              <td>{{number_format($total_lh_ot_over_eight,2)}}<input type='hidden' name='total_lh_ot_over_eight[{{$key+1}}]' value="{{$total_lh_ot_over_eight}}"></td>
                              <td>{{number_format($name->total_sh_nd,2)}}<input type='hidden' name='name_total_sh_nd[{{$key+1}}]' value="{{$name->total_sh_nd}}"></td>
                              <td>{{number_format($total_sh_nd_amount,2)}}<input type='hidden' name='total_sh_nd_amount[{{$key+1}}]' value="{{$total_sh_nd_amount}}"></td>
                              <td>{{number_format($name->total_sh_nd_over_eight,2)}}<input type='hidden' name='name_total_sh_nd_over_eight[{{$key+1}}]' value="{{$name->total_sh_nd_over_eight}}"></td>
                              <td>{{number_format($total_sh_nd_over_eight,2)}}<input type='hidden' name='total_sh_nd_over_eight[{{$key+1}}]' value="{{$total_sh_nd_over_eight}}"></td>
                              <td>{{number_format($name->total_sh_ot,2)}}<input type='hidden' name='name_total_sh_ot[{{$key+1}}]' value="{{$name->total_sh_ot}}"></td>
                              <td>{{number_format($total_sh_ot,2)}}<input type='hidden' name='total_sh_ot[{{$key+1}}]' value="{{$total_sh_ot}}"></td>
                              <td>{{number_format($name->total_sh_ot_over_eight,2)}}<input type='hidden' name='name_total_sh_ot_over_eight[{{$key+1}}]' value="{{$name->total_sh_ot_over_eight}}"></td>
                              <td>{{number_format($total_sh_ot_over_eight,2)}}<input type='hidden' name='total_sh_ot_over_eight[{{$key+1}}]' value="{{$total_sh_ot_over_eight}}"></td>
                              <td>{{number_format($name->total_reg_nd,2)}}<input type='hidden' name='name_total_reg_nd[{{$key+1}}]' value="{{$name->total_reg_nd}}"></td>
                              <td>{{number_format($total_reg_nd,2)}}<input type='hidden' name='total_reg_nd[{{$key+1}}]' value="{{$total_reg_nd}}"></td>
                              <td>{{number_format($name->total_reg_ot,2)}}<input type='hidden' name='name_total_reg_ot[{{$key+1}}]' value="{{$name->total_reg_ot}}"></td>
                              <td>{{number_format($total_reg_ot,2)}}<input type='hidden' name='total_reg_ot[{{$key+1}}]' value="{{$total_reg_ot}}"></td>
                              <td>{{number_format($name->total_reg_ot_nd,2)}}<input type='hidden' name='name_total_reg_ot_nd[{{$key+1}}]' value="{{$name->total_reg_ot_nd}}"></td>
                              <td>{{number_format($total_reg_ot_nd,2)}}<input type='hidden' name='total_reg_ot_nd[{{$key+1}}]' value="{{$total_reg_ot_nd}}"></td>
                              <td>{{number_format($name->total_rst_nd,2)}}<input type='hidden' name='name_total_rst_nd[{{$key+1}}]' value="{{$name->total_rst_nd}}"></td>
                              <td>{{number_format($total_rst_nd,2)}}<input type='hidden' name='total_rst_nd[{{$key+1}}]' value="{{$total_rst_nd}}"></td>
                              <td>{{number_format($name->total_rst_nd_over_eight,2)}}<input type='hidden' name='name_total_rst_nd_over_eight[{{$key+1}}]' value="{{$name->total_rst_nd_over_eight}}"></td>
                              <td>{{number_format($total_rst_nd_over_eight,2)}}<input type='hidden' name='total_rst_nd_over_eight[{{$key+1}}]' value="{{$total_rst_nd_over_eight}}"></td>
                              <td>{{number_format($name->total_rst_ot,2)}}<input type='hidden' name='name_total_rst_ot[{{$key+1}}]' value="{{$name->total_rst_ot}}"></td>
                              <td>{{number_format($total_rst_ot,2)}}<input type='hidden' name='total_rst_ot[{{$key+1}}]' value="{{$total_rst_ot}}"></td>
                              <td>{{number_format($name->total_rst_ot_over_eight,2)}}<input type='hidden' name='name_total_rst_ot_over_eight[{{$key+1}}]' value="{{$name->total_rst_ot_over_eight}}"></td>
                              <td>{{number_format($total_rst_ot_over_eight,2)}}<input type='hidden' name='total_rst_ot_over_eight[{{$key+1}}]' value="{{$total_rst_ot_over_eight}}"></td>
                              <td>{{number_format($total_ot_pay,2)}}<input type='hidden' name='total_ot_pay[{{$key+1}}]' value="{{$total_ot_pay}}"></td>
                              {{-- <td>{{number_format($pl,2)}}</td>
                              <td>{{number_format($pl_amount,2)}}</td>
                              <td>{{number_format($sl,2)}}</td>
                              <td>{{number_format($sl_amount,2)}}</td>
                              <td>{{number_format($vl,2)}}</td>
                              <td>{{number_format($vl_amount,2)}}</td>
                              <td>{{number_format($leave_total_amount,2)}}</td> --}}
                              @foreach($salary_adjustments as $sadjustment)
                              @php
                                   $adjustments = ($name->employee->salary_adjustments)->where('name',$sadjustment->name)->sum('amount');
                              @endphp
                              <td>{{number_format($adjustments,2)}}</td>
                              @endforeach
                              <td>{{number_format($salary_adjustment,2)}}<input type='hidden' name='salary_adjustment[{{$key+1}}]' value="{{$salary_adjustment}}"></td>
                              <td>{{number_format($total_taxable_benefits,2)}}<input type='hidden' name='total_taxable_benefits[{{$key+1}}]' value="{{$total_taxable_benefits}}"></td>
                              <td>{{number_format($gross_taxable_income,2)}}<input type='hidden' name='gross_taxable_income[{{$key+1}}]' value="{{$gross_taxable_income}}"></td>
                              <td>{{number_format($total_abs_count,2)}}<input type='hidden' name='total_abs_count[{{$key+1}}]' value="{{$total_abs_count}}"></td>
                              {{-- <td>{{number_format(-1*($total_abs),2)}} and {{$hours}}</td> --}}
                              <td>{{number_format(-1*($total_abs),2)}}<input type='hidden' name='total_abs[{{$key+1}}]' value="{{$total_abs}}"></td>
                              <td>{{number_format($name->total_late_min,2)}}<input type='hidden' name='name_total_late_min[{{$key+1}}]' value="{{$name->total_late_min}}"></td>
                              <td>{{number_format(-1*($total_late_min),2)}}<input type='hidden' name='total_late_min[{{$key+1}}]' value="{{$total_late_min}}"></td>
                              <td>{{number_format($name->total_undertime_min,2)}}<input type='hidden' name='name_total_undertime_min[{{$key+1}}]' value="{{$name->total_undertime_min}}"></td>
                              <td>{{number_format(-1*($total_undertime_min),2)}}<input type='hidden' name='total_undertime_min[{{$key+1}}]' value="{{$total_undertime_min}}"></td>
                              <td>{{number_format(-1*($sss_ecc),2)}}<input type='hidden' name='sss_ecc[{{$key+1}}]' value="{{$sss_ecc}}"></td>
                              <td>{{number_format(-1*($sss_ee),2)}}<input type='hidden' name='sss_ee[{{$key+1}}]' value="{{$sss_ee}}"></td>
                              <td>{{number_format(-1*($sss_er),2)}}<input type='hidden' name='sss_er[{{$key+1}}]' value="{{$sss_er}}"></td>
                              <td>{{number_format(-1*($hdmf),2)}}<input type='hidden' name='hdmf_ee[{{$key+1}}]' value="{{$hdmf}}"></td>
                              <td>{{number_format(-1*($hdmf),2)}}<input type='hidden' name='hdmf_er[{{$key+1}}]' value="{{$hdmf}}"></td>
                              <td>{{number_format(-1*($philhealth),2)}}<input type='hidden' name='philhealth_ee[{{$key+1}}]' value="{{$philhealth}}"></td>
                              <td>{{number_format(-1*($philhealth),2)}}<input type='hidden' name='philhealth_er[{{$key+1}}]' value="{{$philhealth}}"></td>
                              <td>{{number_format(-1*($wisp_ee),2)}}<input type='hidden' name='wisp_ee[{{$key+1}}]' value="{{$wisp_ee}}"></td>
                              <td>{{number_format(-1*($wisp_er),2)}}<input type='hidden' name='wisp_er[{{$key+1}}]' value="{{$wisp_er}}"></td>
                              <td>{{number_format(-1*($statutory),2)}}<input type='hidden' name='statutory[{{$key+1}}]' value="{{$statutory}}"></td>
                              <td>{{number_format(-1*($taxable_deductable_total),2)}}<input type='hidden' name='taxable_deductable_total[{{$key+1}}]' value="{{$taxable_deductable_total}}"></td>
                              <td>{{number_format($net_taxable_income,2)}}<input type='hidden' name='net_taxable_income[{{$key+1}}]' value="{{$net_taxable_income}}"></td>
                              <td>{{number_format(-1*($tax),2)}}<input type='hidden' name='tax[{{$key+1}}]' value="{{$tax}}"></td>
                              <td>{{number_format($de_minimis,2)}}<input type='hidden' name='de_minimis[{{$key+1}}]' value="{{$de_minimis}}"></td>
                              <td>{{number_format($other_allowances_basic_pay,2)}}<input type='hidden' name='other_allowances_basic_pay[{{$key+1}}]' value="{{$other_allowances_basic_pay}}"></td>
                              <td>{{number_format($subliq,2)}}<input type='hidden' name='subliq[{{$key+1}}]' value="{{$subliq}}"></td>
                              {{-- <td>{{number_format($de_minimis_adj,2)}}</td> --}}
                              {{-- <td>{{number_format($load_allowance,2)}}</td>
                              <td>{{number_format($other_allowances,2)}}</td>
                              <td>{{number_format($other_nta,2)}}</td>
                              <td>{{number_format($sss_loan_refund,2)}}</td>
                              <td>{{number_format($subliq,2)}}</td> --}}
                              @foreach($allowances_total as $as => $total_allow)
                              
                              @php
                                  $get_bbb = [];
                                  $get_every_cut_off = [];
                                  $allllowance = 0;
                                  if (!empty($name->employee->allowances)) {
                                  $allow = $name->employee->allowances;
                                  $every_cut_off = $allow->where('allowance_id',$total_allow->allowance_id)->whereIn('schedule', ['Every cut off', 'This cut off'])->sum('allowance_amount');
                                  $get_every_cut_off = $allow->where('allowance_id',$total_allow->allowance_id)->whereIn('schedule', ['Every cut off', 'This cut off']);
                                    
                                  $bbb = $allow->where('allowance_id',$total_allow->allowance_id)->where('schedule', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off')->sum('allowance_amount');
                                  $get_bbb = $allow->where('allowance_id',$total_allow->allowance_id)->where('schedule', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off');
                                  $allllowance = $bbb+$every_cut_off;
                                  }
                              @endphp
                              <td>{{number_format($allllowance,2)}}
                                
                              </td>
                              @endforeach
                              @php
                              $every_cut_off_allowance = [];
                              $allowances_allowance = [];
                              $every_cut_off_loan_display_loans = [];
                              $loans_loans = [];
                              $every_cut_off_payroll_instructions_instructions = [];
                              $other_instructions = [];
                               if (!empty($name->employee->allowances)) {
                                    $allow_a = $name->employee->allowances;
                                    $every_cut_off_allowance = $allow_a->whereIn('schedule', ['Every cut off', 'This cut off']);
                                    $allowances_allowance = $allow_a->where('schedule', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off');
                                }
                                  if(!empty($name->employee->loan))
                                {
                                  $loa_a = ($name->employee->loan);
                                  $every_cut_off_loan_display_loans = $loa_a->whereIn('schedule', ['Every cut off', 'This cut off']);
                                  $loans_loans = $loa_a->where('schedule', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off');
                                }
                                if(!empty($name->employee->pay_instructions))
                                {
                                  $payroll_instructions_a = ($name->employee->pay_instructions);
                                  $every_cut_off_payroll_instructions_instructions = $payroll_instructions_a->whereIn('frequency', ['Every cut off', 'This cut off']);
                                  $other_instructions = $payroll_instructions_a->where('frequency', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off');
                                }
                              @endphp
                              {{-- <td><a href='#' data-toggle="modal" data-target="#allowances{{$name->employee_no}}">{{number_format($total_allowances,2)}}</a></td> --}}
                              {{-- <td>{{number_format($total_allowances,2)}}</td> --}}
                              <td>
                                <input type='hidden' name='get_every_cut_off[{{$key+1}}]' value="{{$every_cut_off_allowance}}">
                                <input type='hidden' name='get_bbb[{{$key+1}}]' value="{{$allowances_allowance}}">

                                <input type='hidden' name='get_every_cut_off_payroll_instructions[{{$key+1}}]' value="{{$every_cut_off_payroll_instructions_instructions}}">
                                <input type='hidden' name='get_other[{{$key+1}}]' value="{{$other_instructions}}">
                                
                                <input type='hidden' name='get_every_cut_off_loan[{{$key+1}}]' value="{{$every_cut_off_loan_display_loans}}">
                                <input type='hidden' name='get_loans[{{$key+1}}]' value="{{$loans_loans}}">
                            

                                {{number_format($total_allowances+$de_minimis+$other_allowances_basic_pay+$subliq,2)}}<input type='hidden' name='nontaxable_benefits_total[{{$key+1}}]' value="{{$total_allowances+$de_minimis+$other_allowances_basic_pay+$subliq}}"></td>
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
                              @foreach($instructions as $asd => $ins)
                              @php
                                  $totasions = 0;
                                  $get_bget_otherbb = [];
                                  $get_every_cut_off_payroll_instructions = [];
                                  if(!empty($name->employee->pay_instructions))
                                  {
                                    $payroll_instructions = ($name->employee->pay_instructions);
                                    $every_cut_off_payroll_instructions = $payroll_instructions->where('benefit_name',$ins->benefit_name)->whereIn('frequency', ['Every cut off', 'This cut off'])->sum('amount');
                                    $get_every_cut_off_payroll_instructions = $payroll_instructions->where('benefit_name',$ins->benefit_name)->whereIn('frequency', ['Every cut off', 'This cut off']);
                                    $other = $payroll_instructions->where('benefit_name',$ins->benefit_name)->where('frequency', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off')->sum('amount');
                                    $get_other = $payroll_instructions->where('benefit_name',$ins->benefit_name)->where('frequency', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off');
                                    $totasions = $other+$every_cut_off_payroll_instructions;
                                  }
                              @endphp
                              <td>{{number_format($totasions,2)}}
                              </td>
                              @endforeach
                              {{-- <td><a href='#' data-toggle="modal" data-target="#payroll_instruction{{$name->employee_no}}">{{number_format($total_payroll_instructions,2)}}</a></td> --}}
                              {{-- <td><a href='#' data-toggle="modal" data-target="#loan{{$name->employee_no}}">{{number_format($total_loans,2)}}</a></td> --}}
                              @foreach($loans_all as $asd =>  $loans_al)
                              @php
                              $lllloan = 0;
                              $get_loans = [];
                              $get_every_cut_off_loan = [];
                              if(!empty($name->employee->loan))
                              {
                                $loa = ($name->employee->loan);
                                $every_cut_off_loan = $loa->where('loan_type_id',$loans_al->loan_type_id)->whereIn('schedule', ['Every cut off', 'This cut off'])->sum('monthly_ammort_amt');
                                $get_every_cut_off_loan = $loa->where('loan_type_id',$loans_al->loan_type_id)->whereIn('schedule', ['Every cut off', 'This cut off']);
                                $loans = $loa->where('loan_type_id',$loans_al->loan_type_id)->where('schedule', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off')->sum('monthly_ammort_amt');
                                $get_loans = $loa->where('loan_type_id',$loans_al->loan_type_id)->where('schedule', $payroll_a ? 'Every 1st cut off' : 'Every 2nd cut off');
                                $lllloan = $loans+$every_cut_off_loan;
                              }
                              @endphp
                              <td>{{number_format(-1*($lllloan),2)}}  </td>
                              @endforeach
                              <td>{{number_format(-1*($total_loans),2)}}<input type='hidden' name='nontaxable_deductible_benefits_total[{{$key+1}}]' value="{{$total_loans}}"></td> 
                              <td>{{number_format($gross_taxable_income+$total_allowances+$de_minimis+$other_allowances_basic_pay+$subliq,2)}}<input type='hidden' name='gross_pay[{{$key+1}}]' value="{{$gross_taxable_income+$total_allowances+$de_minimis+$other_allowances_basic_pay+$subliq}}"></td>
                              <td>{{number_format($taxable_deductable_total+$total_loans+$tax,2)}}<input type='hidden' name='deductions_total[{{$key+1}}]' value="{{$taxable_deductable_total+$total_loans+$tax}}"></td>
                              <td>{{number_format($gross_taxable_income+$total_allowances+$de_minimis+$other_allowances_basic_pay+$subliq-$taxable_deductable_total-$total_loans-$tax+$total_payroll_instructions,2)}}<input type='hidden' name='netpay[{{$key+1}}]' value="{{$gross_taxable_income+$total_allowances+$de_minimis+$other_allowances_basic_pay+$subliq-$taxable_deductable_total-$total_loans-$tax+$total_payroll_instructions}}"></td>
                              @if($payroll_b)
                              <td>{{$lastccc}}</td>
                              <td>{{$government_amount-$lastccc}}</td>
                              
                              @if($name->employee->work_description == "Non-Monthly")
                              {
                                <td>{{$previous_basic_pay_rate}}</td>
                                <td>{{$basic_pay}}</td>
                              }
                              @else
                              
                              <td>{{$previous_pay_rate}}</td>
                              <td>{{$pay_rate/2}}</td>
                              @endif
                              @else
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              @endif
                            </tr>
                            @php
                            $object = new stdClass();
                            $object->bank = str_pad($name->employee->bank_account_number, 13, '0', STR_PAD_LEFT);;
                            $object->amount = str_pad(number_format($gross_taxable_income+$total_allowances+$de_minimis+$other_allowances_basic_pay+$subliq-$taxable_deductable_total-$total_loans-$tax+$total_payroll_instructions,2, "", ""), 13, '0', STR_PAD_LEFT);
                            array_push($paytext,$object);
                            $total_net = $total_net + number_format($gross_taxable_income+$total_allowances+$de_minimis+$other_allowances_basic_pay+$subliq-$taxable_deductable_total-$total_loans-$tax+$total_payroll_instructions,2,".","");
                            @endphp
                          @endforeach
                      </tbody>
                    </table>
                  </div>
                </form>
              </div>
            </div>
           </div>
        </div>
    </div>
</div>
@php
    $total_net = str_pad(number_format($total_net,2, "", ""), 13, '0', STR_PAD_LEFT);
    $companyData = $companies->where('id',$company)->first();
    if($companyData)
    {

      $companyData = $companyData->company_code;
    }

@endphp
<script>
   var get_date = document.getElementById("posting_date").value;
   var paytext = {!! json_encode($paytext) !!};
   var total_net = {!! json_encode($total_net) !!};
   var company = {!! json_encode($companyData) !!};
    
   function CreateTextFile() {
    
    var get_date_original = document.getElementById("posting_date").value;
    get_date = get_date_original.replace("-", "");
    get_date = get_date.replace("-", "");
    get_date=get_date.slice(2);
    get_date=get_date.slice(2)+""+get_date.substring(0,2);
    console.log(get_date);
    var text="PHP010000038248832"+get_date+"2"+total_net+"\n";
   for (var key in paytext) {
    if(paytext[key] != undefined){
      text += "PHP10"+paytext[key].bank+"0000007"+paytext[key].amount+"\n";
    }
     
  }
      var blob = new Blob([text], {
         type: "text/plain;charset=utf-8",
      });
      saveAs(blob, company+"-"+get_date_original+".txt");
   }
</script>
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

  
  function showConfirmation() {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to submit this Pay Reg?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, submit it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("loader").style.display = "flex";
                document.getElementById('PagRegForm').submit();
            }
        });

        return false; // Prevent default form submission
    }
</script>
    {{-- @foreach($payrolls as $payroll)
        @include('payroll.view_payroll')   
    @endforeach
    @include('payroll.upload_payroll') --}}
@endsection


