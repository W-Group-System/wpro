@extends('layouts.header')

@section('content')
	<div class="main-panel">
		<div class="content-wrapper">
			<div class="col-lg-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Sync Biometrics</h4>
						
                        <form method='POST' action="{{url('sync-bio')}}" onsubmit='show();'>
                            {{csrf_field()}}
							<div class=row>
								{{-- <div class='col-md-2'>
                                    Company
                                    <select name="companies" class="form-control form-control-sm required js-example-basic-single" multiple required>
                                        <option value="">- Companies -</option>
                                        @foreach ($companies as $c)
                                        <option value="{{$c->id}}">{{$c->company_name}}</option>
                                        @endforeach
                                    </select>
								</div> --}}
								<div class='col-md-2'>
                                    Employee
                                    <select name="employees" class="form-control form-control-sm required js-example-basic-single" required>
                                        <option value="">- Employees -</option>
                                        @foreach ($employees as $e)
                                        <option value="{{$e->employee_number}}">{{$e->employee_code}} - {{$e->first_name}} {{$e->last_name}}</option>
                                        @endforeach
                                    </select>
								</div>
								<div class='col-md-2'>
                                    From
                                    <input type="date" class="form-control form-control-sm" id='from' name="from" required />
								</div>
								<div class='col-md-2'>
                                    To
                                    <input type="date" class="form-control form-control-sm" id='to' name="to" required />
								</div>
								<div class='col-md-2'>
									<button type="submit" class="form-control form-control-sm btn btn-primary mb-2 btn-sm">Sync BioTime</button>
								</div>
							</div>
						</form>
						
						
						{{-- <p class="card-description">
							<form method='get' action='sync-biometric-per-employee' onsubmit='show();' enctype="multipart/form-data">
								<div class=row>
									<div class='col-md-4'>
										<div class="form-group row">
										<label class="col-sm-4 col-form-label text-right">Employee Biotime</label>
										<div class="col-sm-8">
											<select data-placeholder="Select Employee" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='employee[]' multiple required>
												<option value="">-- Select Employee --</option>
												@foreach($employees as $emp)
													<option value="{{$emp->employee_number}}" >{{$emp->employee_number}} - {{$emp->first_name}} {{$emp->last_name}}</option>
												@endforeach
												</select>
										</div>
										</div>
									</div>
									<div class='col-md-3'>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label text-right">From</label>
											<div class="col-sm-8">
												<input type="date" value='' class="form-control form-control-sm" name="from_biotime"
													onchange='get_min(this.value);' required />
											</div>
										</div>
									</div>
									<div class='col-md-3'>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label text-right">To</label>
											<div class="col-sm-8">
												<input type="date" value='' class="form-control form-control-sm" id='to' name="to_biotime"
													required />
											</div>
										</div>
									</div>
									<div class='col-md-2'>
										<button type="submit" class="form-control form-control-sm btn btn-primary mb-2 btn-sm">Sync Employee</button>
									</div>
								</div>
							</form>
						</p> --}}
						
						{{-- @if (checkUserPrivilege('biometrics_per_location_hik',auth()->user()->id) == 'yes')
						<p class="card-description">
							<form method='get' action='sync-hik-att-logs' onsubmit='show();' enctype="multipart/form-data">
								<div class=row>
									<div class='col-md-4'>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label text-right">HIK Terminals</label>
											<div class="col-sm-8">
												<select data-placeholder="Terminal" class="form-control form-control-sm required js-example-basic-single " style='width:100%;' name='terminal_hik' required>
													<option value="">--Terminal--</option>
													  @foreach($terminals_hik as $terminal)
														<option value="{{$terminal->device}}">{{$terminal->device}}</option>
													  @endforeach
												  </select>
											</div>
										</div>
									</div>
									<div class='col-md-3'>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label text-right">From</label>
											<div class="col-sm-8">
												<input type="date" value='' class="form-control form-control-sm" name="from_hik"
													 onchange='get_min(this.value);' required />
											</div>
										</div>
									</div>
									<div class='col-md-3'>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label text-right">To</label>
											<div class="col-sm-8">
												<input type="date" value='' class="form-control form-control-sm" id='to' name="to_hik"
													required />
											</div>
										</div>
									</div>
									<div class='col-md-2'>
										<button type="submit" class="form-control form-control-sm btn btn-primary mb-2 btn-sm">Sync HIK Vision</button>
									</div>
								</div>
							</form>
						</p>
						
						<p class="card-description">
							<form method='get' action='sync-biometric-per-employee-hik' onsubmit='show();' enctype="multipart/form-data">
								<div class=row>
									<div class='col-md-4'>
										<div class="form-group row">
										<label class="col-sm-4 col-form-label text-right">Employee HIK</label>
										<div class="col-sm-8">
											<select data-placeholder="Select Employee" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='employee[]' multiple required>
												<option value="">-- Select Employee --</option>
												@foreach($employees as $emp)
													<option value="{{$emp->employee_number}}" >{{$emp->employee_number}} - {{$emp->first_name}} {{$emp->last_name}}</option>
												@endforeach
												</select>
										</div>
										</div>
									</div>
									<div class='col-md-3'>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label text-right">From</label>
											<div class="col-sm-8">
												<input type="date" value='' class="form-control form-control-sm" name="from_hik"
													onchange='get_min(this.value);' required />
											</div>
										</div>
									</div>
									<div class='col-md-3'>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label text-right">To</label>
											<div class="col-sm-8">
												<input type="date" value='' class="form-control form-control-sm" id='to' name="to_hik"
													required />
											</div>
										</div>
									</div>
									<div class='col-md-2'>
										<button type="submit" class="form-control form-control-sm btn btn-primary mb-2 btn-sm">Sync Employee</button>
									</div>
								</div>
							</form>
						</p>
						@endif --}}
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
