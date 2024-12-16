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
							<table class="table table-db table-hover table-bordered employee_attendance">
							<thead>
								<tr>
									<th>Company</th>
									{{-- <th>Name</th> --}}
                                    <th>Employee No</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Middle Name</th>
                                    <th>Department</th>
                                    <th>Cost Center</th>
                                    <th>Account No</th>
                                    
                                    <th>Tax Status</th>
                                    <th>Pay Rate</th>
                                    @for($i = 1; $i <= 12; $i++)
                                        <th>{{ date('M Y',strtotime($year."-".$i.'-01')) }}</th>
                                    @endfor
                                    <th>Total</th>
                                    <th>Salary Diff</th>
                                    <th>For Release(WLI)</th>
                                    <th>Withholding Tax</th>
									<th>Thirteenth Month Pay Nontaxable</th>
									<th>Non Taxable Benefits Total</th>
									<th>Gross Pay</th>
									<th>1st Released (May 2024)</th>
									<th>Net Pay</th>
								</tr>
							</thead>
							<tbody>
                                @foreach($employees->sortBy('last_name') as $key => $employee)
                                @php
                                    $total_Payroll = 0;
                                    $pay_rate = $employee->salary->basic_salary+$employee->salary->subliq+$employee->salary->de_minimis+$employee->salary->other_allowance;
                                    $previous= ($employee->benefits)->first();
                                    if($previous)
                                    {
                                        $previous = $previous->netpay;
                                    }
                                    else {
                                        
                                        $previous = 0.00;
                                    }
                                @endphp
                                <tr>
                                    <td>{{$employee->company->company_code}} <input type='hidden' name='company_code[{{$key+1}}]' value="{{$employee->company->company_code}}"></td>
                                    <td>{{$employee->employee_code}} <input type='hidden' name='employee_code[{{$key+1}}]' value="{{$employee->employee_code}}"></td>
                                    <td>{{$employee->last_name}} <input type='hidden' name='last_name[{{$key+1}}]' value="{{$employee->last_name}}"></td>
                                    <td>{{$employee->first_name}} <input type='hidden' name='first_name[{{$key+1}}]' value="{{$employee->first_name}}"></td>
                                    <td>{{$employee->middle_name}} <input type='hidden' name='middle_name[{{$key+1}}]' value="{{$employee->middle_name}}"></td>
                                    <td>{{$employee->department->name}} <input type='hidden' name='department_name[{{$key+1}}]' value="{{$employee->department->name}}"></td>
                                    <td></td>
                                    <td>{{$employee->bank_account_number}} <input type='hidden' name='bank_account_number[{{$key+1}}]' value="{{$employee->bank_account_number}}"></td>
                                    <td></td>
                                    <td>{{number_format($pay_rate,2)}}<input type='hidden' name='pay_rate[{{$key+1}}]' value="{{$pay_rate}}"></td>
								
									{{-- <td>{{$employee->last_name}}, {{$employee->first_name}}</td> --}}
									{{-- <td>{{$employee->employee_code}}</td> --}}
                                    @php
                                    $no_december = ['A3156322'];
                                    $for_release = 0;
                                    $salary_diff = 0;
                                    @endphp
                                    @for($i = 1; $i <= 12; $i++)
                                        @if($i == 12)
                                        @if((!in_array($employee->employee_code, $no_december)))
                                        @if($employee->salary)
                                        @if($company == 10)
                                        
                                            @php
                                                $date_need = date('Y-m-01', strtotime($year . "-" . $i . '-01 -1 month'));
                                                $date_need_next = date('Y-m-t', strtotime($year . "-" . $i . '-01 -1 month'));
                                                $payregs = $employee->get_payreg()
                                                    ->whereBetween('cut_off_date', [
                                                    $date_need, $date_need_next
                                                    ]);
                                                $pay_reg_id = $payregs->pluck('id')->toArray();
                                                $salary_adjustments_amount = $salary_adjustments->whereIn('pay_reg_id',$pay_reg_id)->sum('amount');
                                                // dd($salary_adjustments_amount);
                                                $pay_instructions_amount = $pay_instructions->whereIn('payreg_id',$pay_reg_id)->sum('amount');
                                                $total_Payroll  = $total_Payroll + $payregs->sum('basic_pay') 
                                                + $payregs->sum('deminimis') 
                                                + $payregs->sum('other_allowances_basic_pay') 
                                                + $payregs->sum('subliq')- $payregs->sum('absent_amount')-$payregs->sum('tardiness_amount')-$payregs->sum('undertime_amount')+$salary_adjustments_amount+$pay_instructions_amount;
                                                $total_Payroll  = $total_Payroll + (($employee->salary->basic_salary+$employee->salary->de_minimis+$employee->salary->subliq+$employee->salary->other_allowance)/2);
                                            @endphp
                                            <td>
                                                @php
                                                    $first = $payregs->sum('basic_pay') 
                                                + $payregs->sum('deminimis') 
                                                + $payregs->sum('other_allowances_basic_pay') 
                                                + $payregs->sum('subliq')- $payregs->sum('absent_amount')-$payregs->sum('tardiness_amount')-$payregs->sum('undertime_amount')+$salary_adjustments_amount+$pay_instructions_amount;
                                                $second = (($employee->salary->basic_salary+$employee->salary->de_minimis+$employee->salary->subliq+$employee->salary->other_allowance)/2);
                                                @endphp
                                                {{-- {{number_format($employee->salary->basic_salary+$employee->salary->de_minimis+$employee->salary->subliq+$employee->salary->other_allowance,2)}} --}}
                                                {{number_format($first+$second,2)}}
                                            
                                            </td>
                                        @else
                                        @php
                                                $total_Payroll  = $total_Payroll + $employee->salary->basic_salary+$employee->salary->de_minimis+$employee->salary->subliq+$employee->salary->other_allowance;
                                            @endphp
                                            <td>
                                                {{number_format($employee->salary->basic_salary+$employee->salary->de_minimis+$employee->salary->subliq+$employee->salary->other_allowance,2)}}
                                            
                                            </td>
                                        @endif
                                        @else
                                        <td>
                                            0.00
                                        </td>
                                        @endif
                                        @else
                                        <td>
                                            0.00
                                        </td>
                                        @endif
                                        @else
                                        @php
                                       
                                       if($company == 10)
                                        {
                                            $date_need = date('Y-m-01', strtotime($year . "-" . $i . '-01 -1 month'));
                                            $date_need_next = date('Y-m-t', strtotime($year . "-" . $i . '-01 -1 month'));
                                            $payregs = $employee->get_payreg()
                                                ->whereBetween('cut_off_date', [
                                                   $date_need, $date_need_next
                                                ]);
                                        }
                                        else {
                                            $payregs = $employee->get_payreg()
                                                ->whereBetween('cut_off_date', [
                                                    date('Y-m-01', strtotime($year . "-" . $i . '-01')), 
                                                    date('Y-m-t', strtotime($year . "-" . $i . '-01'))
                                                ]);
                                            
                                        }
                                        $pay_reg_id = $payregs->pluck('id')->toArray();
                                        $salary_adjustments_amount = $salary_adjustments->whereIn('pay_reg_id',$pay_reg_id)->sum('amount');
                                        // dd($salary_adjustments_amount);
                                        $pay_instructions_amount = $pay_instructions->whereIn('payreg_id',$pay_reg_id)->sum('amount');

                                        $total_Payroll  = $total_Payroll + $payregs->sum('basic_pay') 
                                            + $payregs->sum('deminimis') 
                                            + $payregs->sum('other_allowances_basic_pay') 
                                            + $payregs->sum('subliq')- $payregs->sum('absent_amount')-$payregs->sum('tardiness_amount')-$payregs->sum('undertime_amount')+$salary_adjustments_amount+$pay_instructions_amount;
                                        @endphp

                                        
                                        <td>
                                            @php
                                               
                                            @endphp
                                            {{number_format($payregs->sum('basic_pay') 
                                            + $payregs->sum('deminimis') 
                                            + $payregs->sum('other_allowances_basic_pay') 
                                            + $payregs->sum('subliq')-$payregs->sum('absent_amount')-$payregs->sum('tardiness_amount')-$payregs->sum('undertime_amount')+$salary_adjustments_amount+$pay_instructions_amount,2)
                                        }}
                                        </td>
                                        @endif
                                    @endfor
                                    @php
                                        if($employee->employee_code == "A3131019")
                                        {
                                            $salary_diff = 2848;
                                        }
                                        if($employee->employee_code == "A3167723")
                                        {
                                            $salary_diff = 26460.00;
                                        }
                                        if($employee->employee_code == "A3144920")
                                        {
                                            $salary_diff = 1290.08;
                                        }
                                        if($employee->employee_code == "A3150121")
                                        {
                                            $salary_diff =13080;
                                        }
                                        if($employee->employee_code == "A3167723")
                                        {
                                            $salary_diff =26460;
                                        }
                                        if($employee->employee_code == "A393916")
                                        {
                                            $salary_diff =38850;
                                        }
                                        if($employee->employee_code == "A3176524")
                                        {
                                            $for_release  =9914.41;
                                        }
	
                                        $tax = 0;
                                        // $pay_reg_id = ($employee->get_payreg())->pluck('id')->toArray();
                                        // dd($pay_reg_id);
                                       
                                     
                                        // $total_Payroll = $total_Payroll + $salary_adjustments_amount+$pay_instructions_amount;
                                        // $total_Payroll = $total_Payroll;
                                        
                                        $payroll = ($total_Payroll+$salary_diff)/12;
                                         
                                        if($employee->employee_code == "A287819")
                                        {
                                            $tax =($payroll-$previous)*.05;
                                        }
                                        if($employee->employee_code == "A178517")
                                        {
                                            $tax =($payroll-$previous)*.05;
                                        }
                                        if($employee->employee_code == "A180518")
                                        {
                                            $tax =($payroll-$previous)*.05;
                                        }
                                        $gross_pay = $payroll-$previous-$tax-$for_release;
                                        
                                    @endphp
                                    <td>{{number_format($total_Payroll,2)}}</td>
                                    <td>{{number_format($salary_diff,2)}}</td>
                                    <td>{{number_format($for_release,2)}}</td>
                                    <td>{{number_format($tax,2)}}</td>
									<td>{{number_format($payroll,2)}}</td>
									<td>{{number_format($payroll,2)}}</td>
									<td>{{number_format($payroll,2)}}</td>
									<td>{{number_format($previous,2)}}</td>
									<td>{{number_format($gross_pay,2)}}</td>
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
        $(document).ready(function() 
        {
            new DataTable('.employee_attendance', 
            {
                // pagelenth:25,
                fixedColumns: {
                    leftColumns: 1,  // 'start' and 'end' have been replaced with 'leftColumns' for clarity
                },
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
@endsection