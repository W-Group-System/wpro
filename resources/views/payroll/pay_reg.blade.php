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
                <h4 class="card-title">Payroll Register
                    @if($cutoff)
                        <small class="text-muted font-weight-normal"> &mdash; Cut-off: {{ $cutoff }} &nbsp;|&nbsp; Period: {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</small>
                    @endif
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
                          <select data-placeholder="Select Company" class="form-control form-control-sm required js-example-basic-multiple" style='width:100%;' name='company[]' multiple="multiple">
                            @foreach($companies as $comp)
                            <option value="{{$comp->id}}" @if(in_array($comp->id, (array)$company)) selected @endif>{{$comp->company_code}}</option>
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
                <form method="POST" action="{{ url('payreg?'.http_build_query(['company' => (array)$company, 'cut_off' => $cutoff, 'from' => $from, 'to' => $to])) }}" id='PagRegForm' onsubmit="return showConfirmation()" enctype="multipart/form-data">
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
                              <th>RST LH OT</th>
                              <th>RST LH AMOUNT</th>
                              <th>RST LH OT OVER 8</th>
                              <th>RST LH OT OVER 8 AMOUNT</th>
                              <th>RST LH ND</th>
                              <th>RST LH ND AMOUNT</th>
                              <th>RST LH ND OVER 8</th>
                              <th>RST LH ND OVER AMOUNT</th>
                              <th>RST SH OT</th>
                              <th>RST SH OT AMOUNT</th>
                              <th>RST SH OT OVER 8</th>
                              <th>RST SH OT OVER 8 AMOUNT</th>
                              <th>RST SH ND</th>
                              <th>RST SH ND AMOUNT</th>
                              <th>RST SH ND OVER 8</th>
                              <th>RST SH ND OVER 8 AMOUNT</th>
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
                              @foreach($benefit_instructions as $ins)
                              <th>{{$ins->benefit_name}}</th>
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
                              @foreach($deduction_instructions as $ins)
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
                            $payroll_b = collect($dates)->where('log_date',25)->first();
                        @endphp
                        @foreach($payroll_rows as $key => $row)
                          @php
                              $name = $row->attendance;
                              $employee = $row->employee;
                              $values = $row->values;
                              $netpay = $values['netpay'] ?? 0;
                          @endphp
                          <tr>
                              <td>{{ $key + 1 }}</td>
                              <td>{{ $values['employee_no'] ?? '' }}</td>
                              <td>{{ $values['last_name'] ?? '' }}</td>
                              <td>{{ $values['first_name'] ?? '' }}</td>
                              <td>{{ $values['middle_name'] ?? '' }}</td>
                              <td>{{ $values['department'] ?? '' }}</td>
                              <td>{{ $values['cost_center'] ?? '' }}</td>
                              <td>{{ $values['account_number'] ?? '' }}</td>
                              <td>{{ number_format($values['pay_rate'] ?? 0, 2) }}</td>
                              <td>{{ $values['tax_status'] ?? '' }}</td>
                              <td>{{ number_format($values['days_rendered'] ?? 0, 2) }}</td>
                              <td>{{ number_format($values['basic_pay'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_lh_nd, 2) }}</td>
                              <td>{{ number_format($values['lh_nd_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_lh_nd_over_eight, 2) }}</td>
                              <td>{{ number_format($values['lh_nd_ge_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_lh_ot, 2) }}</td>
                              <td>{{ number_format($values['lh_ot_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_lh_ot_over_eight, 2) }}</td>
                              <td>{{ number_format($values['lh_ot_ge_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_sh_nd, 2) }}</td>
                              <td>{{ number_format($values['sh_nd_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_sh_nd_over_eight, 2) }}</td>
                              <td>{{ number_format($values['sh_nd_ge_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_sh_ot, 2) }}</td>
                              <td>{{ number_format($values['sh_ot_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_sh_ot_over_eight, 2) }}</td>
                              <td>{{ number_format($values['sh_ot_ge_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_reg_nd, 2) }}</td>
                              <td>{{ number_format($values['reg_nd_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_reg_ot, 2) }}</td>
                              <td>{{ number_format($values['reg_ot_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_reg_ot_nd, 2) }}</td>
                              <td>{{ number_format($values['reg_ot_nd_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_rst_nd, 2) }}</td>
                              <td>{{ number_format($values['rst_nd_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_rst_nd_over_eight, 2) }}</td>
                              <td>{{ number_format($values['rst_nd_ge_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_rst_ot, 2) }}</td>
                              <td>{{ number_format($values['rst_ot_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_rst_ot_over_eight, 2) }}</td>
                              <td>{{ number_format($values['rst_ot_ge_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_rst_lh_ot, 2) }}</td>
                              <td>{{ number_format($values['rst_lh_ot_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_rst_lh_ot_over_eight, 2) }}</td>
                              <td>{{ number_format($values['rst_lh_ot_ge_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_rst_lh_nd, 2) }}</td>
                              <td>{{ number_format($values['rst_lh_nd_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_rst_lh_nd_over_eight, 2) }}</td>
                              <td>{{ number_format($values['rst_lh_nd_ge_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_rst_sh_ot, 2) }}</td>
                              <td>{{ number_format($values['rst_sh_ot_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_rst_sh_ot_over_eight, 2) }}</td>
                              <td>{{ number_format($values['rst_sh_ot_ge_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_rst_sh_nd, 2) }}</td>
                              <td>{{ number_format($values['rst_sh_nd_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($name->total_rst_sh_nd_over_eight, 2) }}</td>
                              <td>{{ number_format($values['rst_sh_nd_ge_amount'] ?? 0, 2) }}</td>
                              <td>{{ number_format($values['total_ot_pay'] ?? 0, 2) }}</td>
                              @foreach($salary_adjustments as $sadjustment)
                                <td>{{ number_format($row->salary_adjustment_amounts[$sadjustment->name] ?? 0, 2) }}</td>
                              @endforeach
                              <td>{{ number_format($values['salary_adjustment'] ?? 0, 2) }}</td>
                              <td>{{ number_format($values['taxable_benefits_total'] ?? 0, 2) }}</td>
                              <td>{{ number_format($values['gross_taxable_income'] ?? 0, 2) }}</td>
                              <td>{{ number_format($values['days_absent'] ?? 0, 2) }}</td>
                              <td>{{ number_format(-1 * ($values['absent_amount'] ?? 0), 2) }}</td>
                              <td>{{ number_format($values['tardiness_total'] ?? 0, 2) }}</td>
                              <td>{{ number_format(-1 * ($values['tardiness_amount'] ?? 0), 2) }}</td>
                              <td>{{ number_format($values['undertime_total'] ?? 0, 2) }}</td>
                              <td>{{ number_format(-1 * ($values['undertime_amount'] ?? 0), 2) }}</td>
                              <td>{{ number_format(-1 * ($values['sss_ecc'] ?? 0), 2) }}</td>
                              <td>{{ number_format(-1 * ($values['sss_ee'] ?? 0), 2) }}</td>
                              <td>{{ number_format(-1 * ($values['sss_er'] ?? 0), 2) }}</td>
                              <td>{{ number_format(-1 * ($values['hdmf'] ?? 0), 2) }}</td>
                              <td>{{ number_format(-1 * ($values['hdmf'] ?? 0), 2) }}</td>
                              <td>{{ number_format(-1 * ($values['philhealth'] ?? 0), 2) }}</td>
                              <td>{{ number_format(-1 * ($values['philhealth'] ?? 0), 2) }}</td>
                              <td>{{ number_format(-1 * ($values['wisp_ee'] ?? 0), 2) }}</td>
                              <td>{{ number_format(-1 * ($values['wisp_er'] ?? 0), 2) }}</td>
                              <td>{{ number_format(-1 * ($values['statutory_total'] ?? 0), 2) }}</td>
                              <td>{{ number_format(-1 * ($values['taxable_deductible_total'] ?? 0), 2) }}</td>
                              <td>{{ number_format($values['net_taxable_income'] ?? 0, 2) }}</td>
                              <td>{{ number_format(-1 * ($values['withholding_tax'] ?? 0), 2) }}</td>
                              <td>{{ number_format($values['deminimis'] ?? 0, 2) }}</td>
                              <td>{{ number_format($values['other_allowances_basic_pay'] ?? 0, 2) }}</td>
                              <td>{{ number_format($values['subliq'] ?? 0, 2) }}</td>
                              @foreach($allowances_total as $total_allow)
                                <td>{{ number_format($row->allowance_amounts[$total_allow->allowance_id] ?? 0, 2) }}</td>
                              @endforeach
                              @foreach($benefit_instructions as $ins)
                                <td>{{ number_format($row->instruction_amounts[$ins->benefit_name] ?? 0, 2) }}</td>
                              @endforeach
                              <td>{{ number_format($values['nontaxable_benefits_total'] ?? 0, 2) }}</td>
                              @foreach($deduction_instructions as $ins)
                                <td>{{ number_format($row->instruction_amounts[$ins->benefit_name] ?? 0, 2) }}</td>
                              @endforeach
                              @foreach($loans_all as $loans_al)
                                <td>{{ number_format(-1 * ($row->loan_amounts[$loans_al->loan_type_id] ?? 0), 2) }}</td>
                              @endforeach
                              <td>{{ number_format(-1 * ($values['nontaxable_deductible_benefits_total'] ?? 0), 2) }}</td>
                              <td>{{ number_format($values['gross_pay'] ?? 0, 2) }}</td>
                              <td>{{ number_format($values['deductions_total'] ?? 0, 2) }}</td>
                              <td>{{ number_format($netpay, 2) }}</td>
                              @if($payroll_b)
                                <td>{{ $values['last_contribution_amount'] ?? 0 }}</td>
                                <td>{{ $values['current_contribution_amount'] ?? 0 }}</td>
                                @if($employee && $employee->work_description == 'Non-Monthly')
                                  <td>{{ $values['previous_basic_pay_rate'] ?? 0 }}</td>
                                  <td>{{ number_format($values['basic_pay'] ?? 0, 2) }}</td>
                                @else
                                  <td>{{ $values['previous_pay_rate'] ?? 0 }}</td>
                                  <td>{{ number_format(($values['pay_rate'] ?? 0) / 2, 2) }}</td>
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
                              $object->bank = str_pad($values['account_number'] ?? '', 13, '0', STR_PAD_LEFT);
                              $object->amount = str_pad(number_format($netpay, 2, '', ''), 13, '0', STR_PAD_LEFT);
                              array_push($paytext, $object);
                              $total_net += number_format($netpay, 2, '.', '');
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
    $companyData = $companies->whereIn('id', (array)$company)->pluck('company_code')->implode('-');
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
