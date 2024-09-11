@extends('layouts.header')

@section('content')
	<div class="main-panel">
		<div class="content-wrapper">
			<div class="col-lg-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Government Reports</h4>
						<p class="card-description">
						<form method='get' onsubmit='show();' enctype="multipart/form-data">
							<div class=row>
								<div class="col-md-2">
                                    <div class="form-group">
                                        <select data-placeholder="Select Company" onchange='clear();' class="form-control form-control-sm required js-example-basic-single" style="width:100%;" name="company" id="companySelect" required>
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
											<input type="date"  class="form-control form-control-sm" name="from"
												max='{{ date('Y-m-d') }}' value="{{$from}}"  onchange='get_min(this.value);' required />
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
										<label class="col-sm-4 col-form-label text-right">Benefits</label>
										<div class="col-sm-8">
											<select data-placeholder="Select Benefits"  class="form-control form-control-sm required js-example-basic-single" style="width:100%;" name="benefits" id="benefits" required>
												<option value="">-- Select Benefits --</option>
												<option value="SSS">SSS</option>
												<option value="HDMF">HDMF</option>
												<option value="PHIC">PHIC</option>
												
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
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-db table-hover table-bordered">
							<thead>
								<tr>
									
									<th>Employee No</th>
									<th>Name</th>
									
									<th>Birth Date</th>
									<th>Government Number</th>
									<th>Employee</th>
									<th>Employer</th>
									<th>Total</th>
									
									<th>EC</th>
								</tr>
							</thead>
							<tbody>
								@foreach($pay_registers as $pay)
								<tr>
									
									<td>{{$pay->employee_no}}</td>
									<td>{{$pay->last_name.", ".$pay->first_name}}</td>
									
									<td>{{date('M d, Y',strtotime($pay->employee->birth_date))}}</td>
									@if($benefits == "SSS")
									
									<td>{{str_replace('-',"",$pay->employee->sss_number)}}</td>
									<td>{{number_format($pay->mpf_employee_share+$pay->sss_employee_share,2)}}</td>
									<td>{{number_format($pay->sss_employer_share+$pay->mpf_employer_share,2)}}</td>
									<td>{{number_format($pay->sss_employer_share+$pay->sss_employee_share+$pay->mpf_employer_share+$pay->mpf_employee_share,2)}}</td>
									
									<td>{{number_format($pay->sss_ec,2)}}</td>
									@elseif($benefits == "HDMF")
									
									<td>{{str_replace('-',"",$pay->employee->hdmf_number)}}</td>
									<td>{{number_format($pay->hdmf_employee_share,2)}}</td>
									<td>{{number_format($pay->hdmf_employer_share,2)}}</td>
									<td>{{number_format($pay->hdmf_employee_share+$pay->hdmf_employer_share,2)}}</td>
									
									<td></td>
									@elseif($benefits == "PHIC")
									
									<td>{{str_replace('-',"",$pay->employee->phil_number)}}</td>
									<td>{{number_format($pay->phic_employee_share,2)}}</td>
									<td>{{number_format($pay->phic_employer_share,2)}}</td>
									<td>{{number_format($pay->phic_employee_share+$pay->phic_employer_share,2)}}</td>
									
									<td></td>
									@endif
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
