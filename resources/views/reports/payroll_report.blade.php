@extends('layouts.header')

@section('content')
	<div class="main-panel">
		<div class="content-wrapper">
			<div class="col-lg-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Payroll Reports</h4>
						<p class="card-description">
						<form method='get' onsubmit='show();' enctype="multipart/form-data">
							<div class=row>
								<div class="col-md-3">
                                    <div class="form-group">
                                        <select data-placeholder="Select Company" onchange='clear();' class="form-control form-control-sm required js-example-basic-single" style="width:100%;" name="company" id="companySelect" required>
                                            <option value="">-- Select Company --</option>
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
											<input type="date" value='' class="form-control form-control-sm" name="from"
												max='{{ date('Y-m-d') }}' onchange='get_min(this.value);' required />
										</div>
									</div>
								</div>
								<div class='col-md-3'>
									<div class="form-group row">
										<label class="col-sm-4 col-form-label text-right">To</label>
										<div class="col-sm-8">
											<input type="date" value='' class="form-control form-control-sm" id='to' name="to"
												max='{{ date('Y-m-d') }}' required />
										</div>
									</div>
								</div>
								<div class='col-md-3'>
									<button type="submit" class="form-control form-control-sm btn btn-primary mb-2 btn-sm">Generate</button>
								</div>
							</div>
						</form>
						</p>
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<table class="table table-db table-hover table-bordered">
				  <thead>
					  <tr>
						  <th>Employee No</th>
						  <th>Last Name</th>
						  <th>First Name</th>
						  <th>Middle Name</th>
						  <th>Cost Center</th>
						  <th>Department</th>
						  <th>Account No</th>
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
						  <th>Total SALARY ADJUSTMENT</th>
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
						  @foreach($allowances as $total_allow)
						  <th>{{$total_allow->allowance_type->name}}</th>
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
						  @foreach($loans as $loans_al)
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
						<td>{{$pay_reg->employee_no}}</td>
						<td>{{$pay_reg->employee->last_name}}</td>
						<td>{{$pay_reg->employee->first_name}}</td>
						<td>{{$pay_reg->employee->middle_name}}</td>
						<td>{{$pay_reg->cost_center}}</td>
						<td>{{$pay_reg->employee->department->name}}</td>
						<td>{{$pay_reg->employee->account_number}}</td>
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
						@foreach($salary_adjustments as $as => $salary_adjustment)
							@php
								$ids = $pay_register_ids_data->where('employee_no',$pay_reg->employee_no);
								$salaryaddjustment_all = 0;
								foreach($ids as $idffff)
								{
									$alll = ($idffff->salary_adjustments_data)->where('name',$salary_adjustment->name)->sum('amount');
									$salaryaddjustment_all = $salaryaddjustment_all + $alll;
								}
								
							@endphp
						<td>{{number_format($salaryaddjustment_all,2)}}</td>
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
						{{-- @php
							$ids = [];
						@endphp --}}
						@foreach($allowances as $as => $total_allow)
							@php
								$ids = $pay_register_ids_data->where('employee_no',$pay_reg->employee_no);
								$allllowance = 0;
								foreach($ids as $idffff)
								{
									$alll = ($idffff->pay_allowances)->where('allowance_id',$total_allow->allowance_id)->sum('amount');
									$allllowance = $allllowance + $alll;
								}
								
							@endphp
						<td>{{number_format($allllowance,2)}}</td>
						@endforeach
						<td>{{$pay_reg->nontaxable_benefits_total}}</td>
						@foreach($instructions as $instruction)
							@php
								$ids = $pay_register_ids_data->where('employee_no',$pay_reg->employee_no);
								$instruction_total = 0;
								foreach($ids as $idffff)
								{
									$instruction_tot = ($idffff->pay_instructions)->where('instruction_name',$instruction->instruction_name)->sum('amount');
									$instruction_total = $instruction_total + $instruction_tot;
								}
							@endphp
						<td>{{number_format($instruction_total,2)}}</td>
						  @endforeach
						@foreach($loans as $loans_al)
							@php
								$total_loans = 0;
								$ids = $pay_register_ids_data->where('employee_no',$pay_reg->employee_no);
								foreach($ids as $idffff)
								{
									$toloans = ($idffff->pay_loan)->where('loan_type_id',$loans_al->loan_type_id)->sum('amount');
									$total_loans = $total_loans + $toloans;
								}
							@endphp
						<td>{{number_format($total_loans,2)}}</td>
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
