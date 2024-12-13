@extends('layouts.header')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class='row'>
         
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Salary As of</h4>
                <p class="card-description">
                  <form method='get' onsubmit='show();'  enctype="multipart/form-data">
                  <div class=row>
                    <div class='col-md-3'>
                          <div class="form-group">
                            Company
                              <select data-placeholder="Filter By Company" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='company'>
                                  <option value="">-- Filter By Company --</option>
                                  @foreach($companies as $comp)
                                  <option value="{{$comp->id}}" @if ($comp->id == $company) selected @endif>{{$comp->company_code}}</option>
                                  @endforeach
                              </select>
                          </div>
                    </div>
                    
                    <div class='col-md-2'>
                        <div class="form-group">
                            As of
                          <input value='{{ date('Y-m-d', strtotime($date_from)) }}'  class="form-control date-own" name="date_from" type='date' required/>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <button type="submit" class="btn btn-primary mb-2">Submit</button>
                        {{-- @if($date_range)
                            <button class='btn btn-info mb-2' onclick="exportTableToExcel('employee_attendance','{{$from_date}} - {{$to_date}}')">Export</button>
                        @endif --}}
                    
                    </div>
                      
                  </div>
                  
                  </form>
                </p>
                
                
                <div class="table-responsive">
                    <table border="1" class="table table-hover table-bordered employee_attendance" id='employee_attendance'>
                        <thead>
                            {{-- <tr>
                                <td colspan='5'>{{$emp->emp_code}} - {{$emp->first_name}} {{$emp->last_name}}</td>
                            </tr> --}}
                            <tr style="background-color: #81b0e6;text:center;" class='text-center'>
                                <th rowspan='2'>Name</th>
                                <th colspan='5' class='text-center'>As of {{date('M d, Y',strtotime($date_from))}}</th>
                                <th colspan='5' class='text-center'>Latest Salary</th>
                                
                            </tr>
                            <tr style="background-color: #81b0e6;text:center;" class='text-center'>
                                <td>Basic</td>
                                <td>De Minimis</td>
                                <td>Other Allowances</td>
                                <td>Subliq</td>
                                <td>Total</td>
                                <td>Basic</td>
                                <td>De Minimis</td>
                                <td>Other Allowances</td>
                                <td>Subliq</td>
                                <td>Total</td>
                                
                            </tr>
                            
                        </thead>
                        <tbody>
                           
                            @foreach($employees as $employee)
                            <tr>
                                <td>{{$employee->last_name.", ".$employee->first_name}}</td>
                                @php
                                    $as_of_basic = $employee->salary->basic_salary;
                                    $as_of_de_minimis = $employee->salary->de_minimis;
                                    $as_of_other_allowance = $employee->salary->other_allowance;
                                    $as_of_subliq = $employee->salary->subliq;
                                    $employeeMovement = ($employee->salaryMovement)->first();
                                    if($employeeMovement != null)
                                    {

                                        
                                        $newValues = json_decode($employeeMovement->new_values, true);
                                        // dd($newValues);
                                        if(array_key_exists('basic_salary',$newValues))
                                        {
                                          
                                            $as_of_basic = $newValues['basic_salary'];
                                            // dd($as_of_basic);
                                        }
                                        if(array_key_exists('de_minimis',$newValues))
                                        {
                                            // dd('renz');
                                           
                                            $as_of_de_minimis = $newValues['de_minimis'];
                                           
                                      
                                        }
                                        if(array_key_exists('other_allowance',$newValues))
                                        {
                                            $as_of_other_allowance = $newValues['other_allowance'];
                                        }
                                        if(array_key_exists('subliq',$newValues))
                                        {
                                            $as_of_subliq = $newValues['subliq'];
                                        }
                                    }
                                    
                                    $as_of_total = $as_of_basic+$as_of_de_minimis+$as_of_other_allowance+$as_of_subliq;
                                    $current_total = $employee->salary->subliq+$employee->salary->basic_salary+$employee->salary->de_minimis+$employee->salary->other_allowance;
                                @endphp
                                <td>{{number_format($as_of_basic,2)}}</td>
                                <td>{{number_format($as_of_de_minimis,2)}}</td>
                                <td>{{number_format($as_of_other_allowance,2)}}</td>
                                <td>{{number_format($as_of_subliq,2)}}</td>
                                
                                <td>{{number_format($as_of_total,2)}}</td>
                                <td>{{number_format($employee->salary->basic_salary,2)}}</td>
                                <td>{{number_format($employee->salary->de_minimis,2)}}</td>
                                <td>{{number_format($employee->salary->other_allowance,2)}}</td>
                                <td>{{number_format($employee->salary->subliq,2)}}</td>
                                <td @if($as_of_total != $current_total) class='bg-danger' @endif>{{number_format($current_total,2)}}</td>
                            </tr>
                            @endforeach
                        </tbody>

                  </div>
                </div>

            </div>
          </div>
        
        </div>
    </div>
</div>

{{-- Datatable --}}
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">

<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
$(document).ready(function() {
    new DataTable('.table', 
    {
        paginate: false,
        dom: 'Bfrtip',
        buttons: [
            'copy',
            'excel'
        ],
        columnDefs: [
            {
                "defaultContent": "-",
                "targets": "_all"
            }
        ],
        order: [],        // No default sorting order
        ordering: false   // Disables column sorting interaction
    });
});
</script>
@endsection
