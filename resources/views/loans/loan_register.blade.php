@extends('layouts.header')

@section('content')
	<div class="main-panel">
		<div class="content-wrapper">

			<div class="col-lg-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Loan Register</h4>
						<p class="card-description">
							<button type="button" class="btn btn-outline-success btn-icon-text" data-toggle="modal" data-target="#newLoan">
								<i class="ti-plus btn-icon-prepend"></i>
								New loan
							</button>
						</p>
						@if ($errors->any())
							@foreach ($errors->all() as $error)
								<div class="alert alert-danger alert-dismissible fade show" role="alert">
									{{ $error }}
								</div>
							@endforeach
						@endif
						<form >
							<div class="row">
								<div class="col-lg-4 form-group">
									<input type="text" class="form-control form-control-sm" placeholder="Search Name" name="search" id="search" required value="{{$search}}">
								</div>
								<div class="col-lg-2 form-group">
									<label for="name">&nbsp;<br></label>
									<button type="submit" class="btn btn-primary">Search</button>
								</div>
							</div>
						</form>
						<div class="table-responsive">
							<table class="table table-hover table-bordered" id="loanTbl">
								<thead>
									<tr>
										<th>Loan Type</th>
										<th>Employee</th>
										<th>Amount</th>
										<th>Start Date</th>
										<th>End Date</th>
										<th >Loan Balance </th>
										<th >Frequency</th>
										<th>Status</th>
										<th>Actions</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($loans as $loan)
										<tr>
											<td data-title="Loan Type">{{ $loan->loan_type->loan_name }}</td>
											<td data-title="Full Name">
                                                @if($loan->employee != null)
												{{ $loan->employee->last_name . ', ' . $loan->employee->first_name . ' ' . $loan->employee->middle_name }}
                                                @endif
											</td>
											<td data-title="Amount">{{ number_format($loan->amount) }}</td>
											<td data-title="Start Date">{{ date('M d, Y', strtotime($loan->start_date)) }}</td>
											<td data-title="End Date">{{ date('M d, Y', strtotime($loan->expiry_date)) }}</td>
											<td data-title="Loan Balance" >{{ number_format($loan->initial_amount-($loan->pay)->sum('amount'),2) }}</td>
											<td data-title="Frequency">{{$loan->schedule}}</td>
											<td>
                                                @if($loan->status == "Active")
                                                <div class="badge badge-success">Active</div>
                                                @else
                                                <div class="badge badge-danger">Inactive</div>
                                                @endif
                                            </td>
											<td>
												<button title='View loan details' id="" data-toggle="modal" data-target="#loanDetails{{$loan->id}}"
													data-id="{{ $loan->id }}" class="btn  btn-rounded btn-primary btn-icon">
													<i class="fa fa-info"></i>
												</button>
												<button title='View loan details' id="edit{{ $loan->id }}" data-toggle="modal" data-target="#loanDetailsedit{{ $loan->id }}"
													data-id="{{ $loan->id }}" class="btn  btn-rounded btn-primary btn-icon">
													<i class="fa fa-edit"></i>
												</button>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							{{ $loans->appends(['search' => $search])->links() }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	@include('loans.new_loan')
	{{-- @include('loans.loan_details') --}}
	@foreach ($loans as $loan)
    @include('loans.loan_details')
	@include('loans.loan_details_edit')
	@endforeach 

@endsection
@section('loanRegScripts')
	<script>
		$(document).ready(function() {
            // $('#loanTbl').DataTable();

			$('#loanDetails').on('show.bs.modal', function(e) {
				var _button = $(e.relatedTarget);
				var result = "";
				var $row = $(_button).closest("tr"); // Find the row
				var $tds = $row.find("td");
				$.each($tds, function() {
					var t = $(this).attr('data-title');
					var v = $(this).text();
					if (t != undefined) {
						result += '<div>' + t + ' : ' + v + '</div>';
					}

				});
				$(this).find("#container").html(result);
			});

            // $("#loanType").on('change', function() {
            //     console.log('asdas');
            //     $("#loanBeneficiariesParent").removeAttr('hidden');
            // })
		});
	</script>
@endsection
