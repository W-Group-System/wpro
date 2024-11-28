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
                  <div class="table-responsive">
                    <table class="table table-db table-hover table-bordered">
                      <thead>
                          <tr>
                              <th>Payslip</th>
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
                              <th>{{($total_allow->allowance_type->name)}}</th>
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
                              <th>{{$ins->instruction_name}}</th>
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
                        @foreach($pay_registers as $key => $pay_reg)
                        <tr>
                            <td><a href="{{url('/payslip?id='.$pay_reg->id)}}" target="_blank"><button type="button" class="btn btn-inverse-danger btn-icon">
                                <i class="ti-file"></i>
                              </button></a></td>
                            <td>{{$pay_reg->employee_no}}</td>
                            <td>{{$pay_reg->last_name}}</td>
                            <td>{{$pay_reg->first_name}}</td>
                            <td>{{$pay_reg->middle_name}}</td>
                            <td>{{$pay_reg->department}}</td>
                            <td>{{$pay_reg->cost_center}}</td>
                            <td>{{$pay_reg->account_number}}</td>
                            <td>{{number_format($pay_reg->pay_rate,2)}}</td>
                            <td>{{$pay_reg->tax_status}}</td>
                            <td>{{$pay_reg->days_rendered}}</td>
                            <td>{{$pay_reg->basic_pay}}</td>
                            <td>{{$pay_reg->lh_nd}}</td>
                            <td>{{$pay_reg->lh_nd_amount}}</td>
                            <td>{{$pay_reg->lh_nd_ge}}</td>
                            <td>{{$pay_reg->lh_nd_ge_amount}}</td>
                            <td>{{$pay_reg->lh_ot}}</td>
                            <td>{{$pay_reg->lh_ot_amount}}</td>
                            <td>{{$pay_reg->lh_ot_ge}}</td>
                            <td>{{$pay_reg->lh_ot_ge_amount}}</td>
                            <td>{{$pay_reg->reg_nd}}</td>
                            <td>{{$pay_reg->reg_nd_amount}}</td>
                            <td>{{$pay_reg->reg_ot}}</td>
                            <td>{{$pay_reg->reg_ot_amount}}</td>
                            <td>{{$pay_reg->reg_ot_nd}}</td>
                            <td>{{$pay_reg->reg_ot_nd_amount}}</td>
                            <td>{{$pay_reg->rst_nd}}</td>
                            <td>{{$pay_reg->rst_nd_amount}}</td>
                            <td>{{$pay_reg->rst_nd_ge}}</td>
                            <td>{{$pay_reg->rst_nd_ge_amount}}</td>
                            <td>{{$pay_reg->rst_ot}}</td>
                            <td>{{$pay_reg->rst_ot_amount}}</td>
                            <td>{{$pay_reg->rst_ot_ge}}</td>
                            <td>{{$pay_reg->rst_ot_ge_amount}}</td>
                            <td>{{$pay_reg->ot_total}}</td>
                            @foreach($salary_adjustments as $sadjustment)
                            @php
                                 $adjustments = ($pay_reg->salary_adjustments_data)->where('name',$sadjustment->name)->sum('amount');
                            @endphp
                            <td>{{number_format($adjustments,2)}}</td>
                            @endforeach
                            <td>{{$pay_reg->salary_adjustment}}</td>
                            <td>{{$pay_reg->taxable_benefits_total}}</td>
                            <td>{{$pay_reg->gross_taxable_income}}</td>
                            <td>{{$pay_reg->days_absent}}</td>
                            <td>{{$pay_reg->absent_amount}}</td>
                            <td>{{$pay_reg->tardiness_total}}</td>
                            <td>{{$pay_reg->tardiness_amount}}</td>
                            <td>{{$pay_reg->undertime_total}}</td>
                            <td>{{$pay_reg->undertime_amount}}</td>
                            <td>{{$pay_reg->sss_ec}}</td>
                            <td>{{$pay_reg->sss_employee_share}}</td>
                            <td>{{$pay_reg->sss_employer_share}}</td>
                            <td>{{$pay_reg->hdmf_employee_share}}</td>
                            <td>{{$pay_reg->hdmf_employer_share}}</td>
                            <td>{{$pay_reg->phic_employee_share}}</td>
                            <td>{{$pay_reg->phic_employer_share}}</td>
                            <td>{{$pay_reg->mpf_employee_share}}</td>
                            <td>{{$pay_reg->mpf_employer_share}}</td>
                            <td>{{$pay_reg->statutory_total}}</td>
                            <td>{{$pay_reg->taxable_deductible_total}}</td>
                            <td>{{$pay_reg->net_taxable_income}}</td>
                            <td>{{$pay_reg->withholding_tax}}</td>
                            <td>{{$pay_reg->deminimis}}</td>
                            <td>{{$pay_reg->other_allowances_basic_pay}}</td>
                            <td>{{$pay_reg->subliq}}</td>
                            
                            @foreach($allowances_total as $allow)
                            @php
                                 $allowances_total_amount = ($pay_reg->pay_allowances)->where('allowance_id',$allow->allowance_id)->sum('amount');
                            @endphp
                            <td>{{number_format($allowances_total_amount,2)}}</td>
                            @endforeach
                            
                            <td>{{$pay_reg->nontaxable_benefits_total}}</td>
                            @foreach($instructions as $ins)
                            @php
                            $inst = ($pay_reg->pay_instructions)->where('instruction_name',$ins->instruction_name)->sum('amount');
                              @endphp
                             <td>
                              {{number_format($inst,2)}}
                             </td>
                            @endforeach
                            @foreach($loans_all as $loans_al)
                            @php
                            $loan_total = ($pay_reg->pay_loan)->where('loan_type_id',$loans_al->loan_type_id)->sum('amount');
                              @endphp
                            <td>{{number_format($loan_total,2)}}</td>
                            @endforeach
                            <td>{{$pay_reg->nontaxable_deductible_benefits_total}}</td>
                            <td>{{$pay_reg->gross_pay}}</td>
                            <td>{{$pay_reg->deductions_total}}</td>
                            <td>{{$pay_reg->netpay}}</td>
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


