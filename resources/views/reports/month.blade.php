@extends('layouts.header')

@section('content')
	<div class="main-panel">
		<div class="content-wrapper">
			<div class="col-lg-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">13th month </h4>
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
										<label class="col-sm-4 col-form-label text-right">Year</label>
										<div class="col-sm-8">
											<input type="year" class="form-control form-control-sm" name="year" max='{{ date('Y') }}' value="{{$year}}" required />
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
									<th>Company</th>
									{{-- <th>Name</th> --}}
									<th>Employee No</th>
                                    @for($i = 1; $i <= 12; $i++)
                                        <th>{{ date('M',strtotime($year."-".$i.'-01')) }}</th>
                                    @endfor
									<th>Total</th>
									<th>13th Month</th>
								</tr>
							</thead>
							<tbody>
                                @foreach($employees->sortBy('last_name') as $employee)
                                <tr>
									<td>{{$employee->company->company_code}}</td>
									{{-- <td>{{$employee->last_name}}, {{$employee->first_name}}</td> --}}
									<td>{{$employee->employee_code}}</td>
                                    @for($i = 1; $i <= 12; $i++)
                                    @php
                                            $payregs = $employee->get_payreg()
                                                ->whereBetween('cut_off_date', [
                                                    date('Y-m-01', strtotime($year . "-" . $i . '-01')), 
                                                    date('Y-m-t', strtotime($year . "-" . $i . '-01'))
                                                ]);
                                        @endphp

                                        
                                        <td>
                                            {{number_format($payregs->sum('basic_pay') 
                                            + $payregs->sum('deminimis') 
                                            + $payregs->sum('other_allowances_basic_pay') 
                                            + $payregs->sum('subliq'),2)
                                        }}
                                        </td>
                                    @endfor
                                    @php
                                        $payregs_total = $employee->get_payreg->whereBetween('cut_off_date',[date('Y-01-01',strtotime($year."-01-01")),date('Y-12-t',strtotime($year."-01-01"))]);
                                    @endphp
									<td>{{ number_format($payregs_total->sum('basic_pay') 
                                        + $payregs_total->sum('deminimis') 
                                        + $payregs_total->sum('other_allowances_basic_pay') 
                                        + $payregs_total->sum('subliq'),2)}}</td>
									<td>{{ number_format(($payregs_total->sum('basic_pay') 
                                        + $payregs_total->sum('deminimis') 
                                        + $payregs_total->sum('other_allowances_basic_pay') 
                                        + $payregs_total->sum('subliq'))/12,2)}}</td>
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
@endsection