@extends('layouts.header')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class='row'>
         
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">YTD Report</h4>
                <p class="card-description">
                  <form method='get' onsubmit='show();'  enctype="multipart/form-data">
                  <div class=row>
                    <div class='col-md-4'>
                        <div class="form-group">
                          <select data-placeholder="Filter By Employee" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='employee[]' required>
                              <option value="">-- Filter By Employee --</option>
                              @foreach($employees as $emp)
                                  <option value="{{$emp->employee_number}}" @if($emp_code) @if (in_array($emp->employee_number,$emp_code)) selected @endif @endif>{{$emp->employee_number}} - {{$emp->first_name}} {{$emp->last_name}}</option>
                              @endforeach
                          </select>
                        </div> 
                    </div>
                    <div class='col-md-3'>
                          <div class="form-group">
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
                          <input value='{{ date('Y', strtotime($from_date)) }}' class="form-control date-own" name="from" type="text" required/>
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

                    @foreach($emp_data as $emp)
                    <table border="1" class="table table-hover table-bordered employee_attendance" id='employee_attendance'>
                        <thead>
                            {{-- <tr>
                                <td colspan='5'>{{$emp->emp_code}} - {{$emp->first_name}} {{$emp->last_name}}</td>
                            </tr> --}}
                            <tr style="background-color: #81b0e6;">
                                <th>Description</th>
                                <th>January</th>
                                <th>February</th>
                                <th>March</th>
                                <th>April</th>
                                <th>May</th>
                                <th>June</th>
                                <th>July</th>
                                <th>August</th>
                                <th>September</th>
                                <th>October</th>
                                <th>November</th>
                                <th>December</th>
                                <th>Total</th>
                                
                            </tr>
                        </thead>

                        
                        <tbody>
                         
                            <tr>
                                <td>BASIC PAY</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                            </tr>
                            <tr>
                                <td>LH ND GE</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                            </tr>
                            <tr>
                                <td>LH OT</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                            </tr>
                            <tr>
                                <td>RST ND GE</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                            </tr>
                            <tr>
                                <td>RST OT</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                            </tr>
                            <tr>
                                <td>RST OT OVER 8</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                            </tr>
                            <tr>
                                <td>RSTSH ND</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                            </tr>
                            <tr>
                                <td>RSTSH OT</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                            </tr>
                            <tr>
                                <td>RSTSH OT Over 8</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                            </tr>
                            <tr>
                                <td>SH ND GE</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                            </tr>
                            <tr>
                                <td>SH OT</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                            </tr>
                            <tr>
                                <td>SH OT OVER 8</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                            </tr>
                            <tr>
                                <td>SL</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                            </tr>
                            <tr>
                                <td>VL</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Taxable Income</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>De Minimis</td>
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                <td>0.00</td>
                            </tr>
                            <tr>
                                <td>Load Allowance</td>
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                <td>0.00</td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Non-Taxable Income</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Total Earnings</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Absent</td>
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                <td>0.00</td>
                            </tr>
                            <tr>
                                <td>Tardiness</td>
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                <td>0.00</td>
                            </tr>
                            <tr>
                                <td>Undertime</td>
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                <td>0.00</td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Taxable Deduction</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>SSS EMPLOYEE SHARE</td>
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                <td>0.00</td>
                            </tr>
                            <tr>
                                <td>HDMF EMPLOYEE SHARE</td>
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                <td>0.00</td>
                            </tr>
                            <tr>
                                <td>PHIC EMPLOYEE SHARE</td>
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                <td>0.00</td>
                            </tr>
                            <tr>
                                <td>MPF EMPLOYEE SHARE</td>
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                <td>0.00</td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Statutory</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>WITHHOLDING TAX</td>
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                <td>0.00</td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Withholding Tax</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>SSS LOAN</td>
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                <td>0.00</td>
                            </tr>
                            <tr>
                                <td>TAX DUE</td>
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                <td>0.00</td>
                            </tr>
                            <tr>
                                <td>THIRTEENTH MONTH ADJUSTMENT DEDUCTION</td>
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                                <td>0.00</td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Non-Taxable Deduction</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Total Deductions</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
                            <tr style="background-color: #e0e0e0;">
                                <td colspan="1"><strong>Net Pay</strong></td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td>0.00</td>
                                <td></td>
                            </tr>
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
</div>
@php
function night_difference($start_work,$end_work)
{
    $start_night = mktime('22','00','00',date('m',$start_work),date('d',$start_work),date('Y',$start_work));
    $end_night   = mktime('06','00','00',date('m',$start_work),date('d',$start_work) + 1,date('Y',$start_work));

    if($start_work >= $start_night && $start_work <= $end_night)
    {
        if($end_work >= $end_night)
        {
            return ($end_night - $start_work) / 3600;
        }
        else
        {
            return ($end_work - $start_work) / 3600;
        }
    }
    elseif($end_work >= $start_night && $end_work <= $end_night)
    {
        if($start_work <= $start_night)
        {
            return ($end_work - $start_night) / 3600;
        }
        else
        {
            return ($end_work - $start_work) / 3600;
        }
    }
    else
    {
        if($start_work < $start_night && $end_work > $end_night)
        {
            return ($end_night - $start_night) / 3600;
        }
        return 0;
    }
}

@endphp
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
   $('.date-own').datepicker({
         minViewMode: 2,
         format: 'yyyy'
       });
    function get_min(value)
    {
        document.getElementById("to").min = value;
    }

    $(document).ready(function() 
    {
        new DataTable('.table', 
        {
            paginate:false,
            dom: 'Bfrtip',
            buttons: 
            [
                'copy', 
                'excel'
            ],
            columnDefs: 
            [
                {
                    "defaultContent": "-",
                    "targets": "_all"
                }
            ],
            order: [] 
        });
    });
</script>
@endsection
