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
                  <table class="table table-hover table-bordered">
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
                            <th>DEMINIMIS ADJUSTMENT</th>
                            <th>LOAD ALLOWANCE</th>
                            <th>OTHER ALLOWANCES</th>
                            <th>OTHER NTA</th>
                            <th>SSS LOAN REFUND</th>
                            <th>SUBLIQ</th>
                            <th>NONTAXABLE BENEFITS TOTAL</th>
                            <th>CANTEEN</th>
                            <th>EMERGENCY</th>
                            <th>HDMF CALAMITY LOAN</th>
                            <th>HDMF CONTRIBUTION UPGRADE</th>
                            <th>HDMF LOAN</th>
                            <th>MOTORCYCLE LOAN</th>
                            <th>RICE LOAN</th>
                            <th>SSS CALAMITY LOAN</th>
                            <th>SSS LOAN</th>
                            <th>STAFF HOUSE</th>
                            <th>WESLA LOAN</th>
                            <th>NONTAXABLE DEDUCTIBLE BENEFITS TOTAL</th>
                            <th>GROSS PAY</th>
                            <th>DEDUCTIONS TOTAL</th>
                            <th>NETPAY</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($names as $key => $name)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$name->employee_no}}</td>
                            <td>{{$name->employee->last_name}}</td>
                            <td>{{$name->employee->first_name}}</td>
                            <td>{{$name->employee->middle_name}}</td>
                            <td>{{$name->employee->department->name}}</td>
                            <td></td>
                            <td>{{$name->employee->bank_account_number}}</td>
                            <td>@if($name->employee->salary){{number_format($name->employee->salary->basic_salary,2)}}@else 0.00 @endif</td>
                            <td></td>
                            <td></td>
                            <td>@if($name->employee->salary){{number_format($name->employee->salary->basic_salary/2,2)}}@else 0.00 @endif</td>
                            <td>{{$name->total_lh_nd}}</td>
                            <td>LH ND AMOUNT	</td>
                            <td>{{$name->total_lh_nd_over_eight}}</td>
                            <td>LH ND GE AMOUNT	</td>
                            <td>{{$name->total_lh_ot}}</td>
                            <td>LH OT Amount	</td>
                            <td>{{$name->total_lh_ot_over_eight}}</td>
                            <td>LH OT OVER 8 AMOUNT</td>
                            <td>{{$name->total_reg_nd}}</td>
                            <td>REG ND AMOUNT	</td>
                            <td>{{$name->total_reg_ot}}</td>
                            <td>REG OT AMOUNT</td>
                            <td>{{$name->total_reg_ot_nd}}</td>
                            <td>REG OT ND AMOUNT	</td>
                            <td>{{$name->total_rst_nd}}</td>
                            <td>RST ND AMOUNT</td>
                            <td>{{$name->total_rst_nd_over_eight}}</td>
                            <td>RST ND GE AMOUNT	</td>
                            <td>{{$name->total_rst_ot}}</td>
                            <td>RST OT AMOUNT</td>
                            <td>{{$name->total_rst_ot_over_eight}}</td>
                            <td>RST OT OVER 8 AMOUNT	</td>
                            <td>OVERTIME TOTAL</td>
                            <td>PL</td>
                            <td>PL AMOUNT	</td>
                            <td>SL	</td>
                            <td>SL AMOUNT	</td>
                            <td>VL	</td>
                            <td>VL AMOUNT	</td>
                            <td>LEAVE AMOUNT TOTAL</td>
                            <td>SALARY ADJUSTMENT	</td>
                            <td>TAXABLE BENEFITS TOTAL</td>
                            <td>GROSS TAXABLE INCOME</td>
                            <td>{{$name->total_abs}}</td>
                            <td>ABSENT AMOUNT</td>
                            <td>{{$name->total_late_min}}</td>
                            <td>TARDINESS AMOUNT</td>
                            <td>{{$name->total_undertime_min}}</td>
                            <td>UNDERTIME AMOUNT	</td>
                            <td>SSS EC</td>
                            <td>SSS EMPLOYEE SHARE	</td>
                            <td>SSS EMPLOYER SHARE</td>
                            <td>HDMF EMPLOYEE SHARE</td>
                            <td>HDMF EMPLOYER SHARE	</td>
                            <td>PHIC EMPLOYEE SHARE	</td>
                            <td>PHIC EMPLOYER SHARE</td>
                            <td>MPF EMPLOYEE SHARE</td>
                            <td>MPF EMPLOYER SHARE	</td>
                            <td>STATUTORY TOTAL	</td>
                            <td>TAXABLE DEDUCTIBLE TOTAL	</td>
                            <td>NET TAXABLE INCOME	</td>
                            <td>WITHHOLDING TAX	</td>
                            <td>DEMINIMIS</td>
                            <td>DEMINIMIS ADJUSTMENT</td>
                            <td>LOAD ALLOWANCE</td>
                            <td>OTHER ALLOWANCES</td>
                            <td>OTHER NTA</td>
                            <td>SSS LOAN REFUND</td>
                            <td>SSS LOAN REFUND</td>
                            <td>NONTAXABLE BENEFITS TOTAL</td>
                            <td>CANTEEN</td>
                            <td>EMERGENCY</td>
                            <td>HDMF CALAMITY LOAN</td>
                            <td>HDMF CONTRIBUTION UPGRADE</td>
                            <td>HDMF LOAN</td>
                            <td>MOTORCYCLE LOAN</td>
                            <td>RICE LOAN</td>
                            <td>SSS CALAMITY LOAN</td>
                            <td>SSS LOAN</td>
                            <td>STAFF HOUSE</td>
                            <td>WESLA LOAN</td>
                            <td>NONTAXABLE DEDUCTIBLE BENEFITS TOTAL</td>
                            <td>GROSS PAY</td>
                            <td>DEDUCTIONS TOTAL</td>
                            <td>NETPAY</td>
                            {{-- <td>{{$name->company->company_code}}</td>
                            <td>@if($name->employee->salary){{number_format($name->employee->salary->de_minimis,2)}}@else 0.00 @endif</td>
                            <td>@if($name->employee->salary){{number_format(($name->employee->salary->basic_salary/313)*8,2)}}@else 0.00 @endif</td>
                            <td>@if($name->employee->salary){{number_format($name->employee->salary->basic_salary/313,2)}}@else 0.00 @endif</td>
                            <td>{{$name->total_abs}}</td>
                            <td>{{$name->total_lv_w_pay}}</td> --}}
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
    new DataTable('.table', {
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


