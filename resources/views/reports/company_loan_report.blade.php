@extends('layouts.header')

@section('content')
	<div class="main-panel">
		<div class="content-wrapper">
			<div class="col-lg-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Company Loan Report</h4>
						<p class="card-description">
						<form method='get' onsubmit='show();' enctype="multipart/form-data">
							<div class=row>
								<div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label text-right">Company</label>
                                        <div class="col-sm-8">
                                        <select data-placeholder="Select Company" onchange='clear();' class="form-control form-control-sm required js-example-basic-single" style="width:100%;" name="companies[]" id="companySelect" multiple required>
                                            <option value="">-- Select Companies --</option>
                                            @foreach($companies as $comp)
                                            <option value="{{$comp->id}}" @if(in_array($comp->id,$company)) selected @endif>{{$comp->company_code}}</option>
                                            @endforeach
                                        </select>
                                        </div>
                                    </div>
                                </div>
								{{-- <div class='col-md-2'>
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
								</div> --}}
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label text-right">As of</label>
                                        <div class="col-sm-8">
                                            <input type="date" name="as_of" class="form-control" max="{{date('Y-m-d')}}" value="{{$as_of}}" required>
                                        </div>
                                    </div>
                                </div>
								<div class='col-md-4'>
									<div class="form-group row">
										<label class="col-sm-4 col-form-label text-right">Loans</label>
										<div class="col-sm-8">
											<select data-placeholder="Select Loans"  class="form-control form-control-sm required js-example-basic-single" style="width:100%;" name="loans[]" id="loan" multiple required>
												<option value="">-- Select Loan --</option>
												@foreach($loans as $loan)
												<option value="{{$loan->id}}" @if(in_array($loan->id,$loan_type)) selected @endif>{{$loan->loan_name}}</option>
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
			<div class="col-lg-12 grid-margin stretch-card">
				<div class="card">
                    <div class='card-title'>
                        SALARY LOAN DEDUCTION REPORT
                    </div>
					<div class="card-body">
						<div class="table-responsive">
                            
                            @foreach($companies_selected as $company)
							<table class="table  table-hover table-bordered company-loan-report">
							<thead>
								<tr>
									<th colspan='11'>Company: {{$company->company_code}}</th>
								</tr>
								<tr>
									<th>NO.</th>
									
									<th>COMPANY</th>
									<th>TYPE OF LOAN</th>
									<th>NAME OF EMPLOYEE</th>
									<th>LOAN AMOUNT</th>
									<th>AMORTIZATION</th>
									<th>TOTAL PAYMENT</th>
									<th>Balance</th>
									<th>Period Covered</th>
									<th>GUARANTOR </th>
									<th>REMARKS </th>
								</tr>
							</thead>
							<tbody>
                                @foreach($loan_all->where('employee.company_id', $company->id) as $loan_a)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $loan_a->employee->company->company_code }}</td>
                                        <td>{{ $loan_a->loan_type->loan_name }}</td>
                                        <td>{{ $loan_a->employee->last_name }},{{ $loan_a->employee->first_name }}</td>
                                        <td>{{ number_format($loan_a->amount,2) }}</td>
                                        <td>{{ number_format($loan_a->monthly_ammort_amt,2) }} </td>
                                        <td><a href="#view{{$loan_a->id}}" data-toggle="modal" title='View'>{{ number_format(($loan_a->pay)->sum('amount')-($loan_a->refund)->sum('amount'),2) }}</td>
                                        <td>{{ number_format($loan_a->initial_amount-($loan_a->pay)->sum('amount')+($loan_a->refund)->sum('amount'),2) }}</td>
                                        @if(($loan_a->pay->sortByDesc('id'))->first() != null)
                                        <td>{{$loan_a->pay->sortByDesc('id')->first()->pay_reg->pay_period_from}} - {{$loan_a->pay->sortByDesc('id')->first()->pay_reg->pay_period_to}}</td>
                                        @else
                                        <td></td>
                                        @endif
                                        <td> @foreach($loan_a->loan_beneficiaries as $loan_gua)
												{{$loan_gua->employee->last_name}}, {{$loan_gua->employee->first_name}} <br>
											@endforeach</td>
                                        <td>{{ $loan_a->remarks }}</td>
                                    </tr>
                                   
                                @endforeach

							</tbody>
							</table>
                            <br>
                            
                            @endforeach
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    @foreach($loan_all as $loan_a)
    @include('company_loan_view')
    @endforeach
	<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">

<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script>
    $(document).ready(function() {
    new DataTable('.company-loan-report', {
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

