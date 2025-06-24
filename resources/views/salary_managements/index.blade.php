@extends('layouts.header')

@section('content')
	<div class="main-panel">
		<div class="content-wrapper">
			<div class="col-lg-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Salary Managements</h4>
						<p class="card-description">
							<button type="button" class="btn btn-outline-success btn-icon-text" data-toggle="modal"
								data-target="#newSalaryAdjustment">
								<i class="ti-plus btn-icon-prepend"></i>
								New Salary Adjustment
							</button>
						</p>

						<div class="table-responsive">
							<table class="table table-hover table-bordered tablewithSearch">
								<thead>
									<tr>
                                        <th>Action</th>
										<th>Employee No</th>
										<th>Employee</th>
										<th>Adjustment Name</th>
										<th>Amount</th>
										<th>Remarks</th>
										<th>Encoded by</th>
										<th>Cut-off </th>
									</tr>
								</thead>
								<tbody>
									@foreach($adjustments as $adjustment)
									<tr>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editSalaryAdjustment{{ $adjustment->id }}">
                                                <i class="ti-pencil-alt"></i>
                                            </button>
                                            <form method="post" action="{{ url('delete-salary-adjustment/'.$adjustment->id) }}" id="deleteForm{{ $adjustment->id }}" onsubmit="show()">
                                                @csrf 

                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteAdjustment({{ $adjustment->id }})">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            </form>
                                        </td>
										<td>{{$adjustment->employee->employee_code}}</td>
										<td>{{$adjustment->employee->last_name}}, {{$adjustment->employee->first_name}}</td>
										<td>{{$adjustment->name}}</td>
										<td>{{number_format($adjustment->amount,2)}}</td>
										<td>{{$adjustment->remarks}}</td>
										<td>{{$adjustment->encoded_by->name}}</td>
										<td>@if($adjustment->cut_off_date){{date('M d, Y',strtotime($adjustment->cut_off_date))}} @else <span class="badge badge-danger">For Posting</span> @endif</td>
									</tr>

									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			@include('salary_managements.new_salary_adjust')
		</div>

        @foreach ($adjustments as $adjustment)
            @include('salary_managements.edit_salary_adjust')
        @endforeach
	@endsection

    @section('js')
        <script>
            function deleteAdjustment(id)
            {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#deleteForm"+id).submit()
                    }
                });
            }
        </script>
    @endsection
