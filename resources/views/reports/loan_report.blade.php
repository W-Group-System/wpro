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
								<div class="col-md-2">
                                    <div class="form-group">
                                        <select data-placeholder="Select Company" onchange='clear();' class="form-control form-control-sm required js-example-basic-single" style="width:100%;" name="company[]" id="companySelect" required multiple>
                                            <option value="">-- Select Company --</option>
                                            @foreach($companies as $comp)
                                            <option value="{{$comp->id}}" @if ($comp->id == $company) selected @endif>{{$comp->company_code}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
								<div class='col-md-2'>
									<div class="form-group row">
										<label class="col-sm-4 col-form-label text-right">From</label>
										<div class="col-sm-8">
											<input type="date" class="form-control form-control-sm" name="from"
												max='{{ date('Y-m-d') }}' value="{{$from}}" onchange='get_min(this.value);' required />
										</div>
									</div>
								</div>
								<div class='col-md-2'>
									<div class="form-group row">
										<label class="col-sm-4 col-form-label text-right">To</label>
										<div class="col-sm-8">
											<input type="date"  class="form-control form-control-sm" id='to' name="to"
												max='{{ date('Y-m-d') }}' value="{{$to}}" required />
										</div>
									</div>
								</div>
								<div class='col-md-2'>
									<div class="form-group row">
										<label class="col-sm-4 col-form-label text-right">Loans</label>
										<div class="col-sm-8">
											<select data-placeholder="Select Loans"  class="form-control form-control-sm required js-example-basic-single" style="width:100%;" name="loan[]" id="loan" required multiple>
												<option value="">-- Select Loan --</option>
												@foreach($loans as $loan)
												<option value="{{$loan->id}}" @if(in_array($loan->id, $loanType)) selected @endif>{{$loan->loan_name}}</option>
												@endforeach

                                                @foreach ($payRegs as $payReg)
                                                    <option value="{{$payReg->instruction_name}}" @if(in_array($payReg->instruction_name, $loanType)) selected @endif>{{$payReg->instruction_name}}</option>
                                                @endforeach
												
											</select>
										</div>
									</div>
								</div>
								<div class='col-md-2'>
									<button type="submit" class="form-control form-control-sm btn btn-primary mb-2 btn-sm">Generate</button>
								</div>
							</div>
						</form>
						</p>
					</div>
				</div>
			</div>
			<div class="row">
			    <div class="col-lg-6 grid-margin stretch-card">
    				<div class="card">
    					<div class="card-body">
    						<div class="table-responsive">
    							<table class="table table-db table-hover table-bordered">
    							<thead>
                                    <tr>
    									<th colspan="7">Government </th>
    								</tr>
    								<tr>
    									<th>Name</th>
    									
    									<th>Employee No</th>
    									<th>Birth Date</th>
    									<th>Government Number</th>
    									<th>Cutoff</th>
    									<th>Loan</th>
    									<th>Amount</th>
    								</tr>
    							</thead>
    							<tbody>
    								@foreach($loan_all as $loan_al)
    								<tr>
    									<td>{{$loan_al->pay_reg->last_name}}, {{$loan_al->pay_reg->first_name}}</td>
    									
    									<td>{{$loan_al->pay_reg->employee_no}}</td>
    									<td>{{date('M d, Y',strtotime($loan_al->employee->birth_date))}}</td>
    									@if(str_contains($loan_al->loan_type->loan_name,"SSS"))
    									<td>{{$loan_al->employee->sss_number}}</td>
    									@elseif(str_contains($loan_al->loan_type->loan_name,"HDMF"))
    									<td>{{$loan_al->employee->hdmf_number}}</td>
    									@else
    									<td></td>
    									@endif
    									<td>{{date('M d, Y',strtotime($loan_al->pay_reg->cut_off_date))}}</td>
    									<td>{{$loan_al->loan_type->loan_name}}</td>
    									<td>{{$loan_al->amount}}</td>
    								</tr>
    								@endforeach
    							</tbody>
    							</table>
    						</div>
    					</div>
    				</div>
    			</div>
                <div class="col-lg-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-db table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="5">Pay Instructions </th>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        
                                        <th>Employee No</th>
                                        {{-- <th>Birth Date</th> --}}
                                        <th>Instruction Name</th>
                                        <th>Cutoff</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pay_regs as $loan_al)
                                    <tr>
                                        <td>
                                            {{$loan_al->pay_reg->last_name}}, {{$loan_al->pay_reg->first_name}}
                                        </td>
                                        
                                        <td>{{$loan_al->pay_reg->employee_no}}</td>
                                        {{-- <td>{{date('M d, Y',strtotime($loan_al->employee->birth_date))}}</td> --}}
                                        <td>{{$loan_al->instruction_name}}</td>
                                        <td>{{date('M d, Y',strtotime($loan_al->pay_reg->cut_off_date))}}</td>
                                        <td>{{$loan_al->amount}}</td>
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

