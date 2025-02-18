@extends('layouts.header')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Employee SL Bank</h4>
                        
                        <form action="" method="get" onsubmit="show()">
                            <div class="row">
                                <div class="col-lg-3">
                                    Company
                                    <select data-placeholder="Select company" name="company" class="form-control js-example-basic-single">
                                        <option value=""></option>
                                        @foreach ($companies as $company)
                                            <option value="{{$company->id}}" @if($company_id == $company->id) selected @endif>{{$company->company_code}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    Department
                                    <select data-placeholder="Select department" name="department" class="form-control js-example-basic-single">
                                        <option value=""></option>
                                        @foreach ($departments as $department)
                                            <option value="{{$department->id}}" @if($department_id == $department->id) selected @endif>{{$department->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <div class="col-lg-3">
                                    Date From
                                    <input type="date" name="date_from" class="form-control" required>
                                </div>
                                <div class="col-lg-3">
                                    Date To
                                    <input type="date" name="date_to" class="form-control" required>
                                </div> --}}
                                <div class="col-lg-3">
                                    <button type="submit" class="btn btn-primary">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-detailed">
                                <thead>
                                    <tr>
                                        <td>Company</td>
                                        <td>Employee No.</td>
                                        <td>Employee Name</td>
                                        <td>SL Balance ({{date('Y', strtotime('-1 year'))}})</td>
                                        <td>SL Balance ({{date('Y')}})</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employees as $employee)
                                        <tr>
                                            <td>{{$employee->company->company_name}}</td>
                                            <td>{{$employee->employee_code}}</td>
                                            <td>{{$employee->user_info->name}}</td>
                                            <td>
                                                @php
                                                    $sl_beginning_balance = checkEmployeeLeaveCredits($employee->user_id,2);
                                                    $earned_sl = checkEarnedLeave($employee->user_id,2,$employee->original_date_hired);
                                                    $used_sl = checkUsedSLVLSILLeave($employee->user_id,2,$employee->original_date_hired,$employee->ScheduleData);

                                                    $total_sl = $sl_beginning_balance + $earned_sl;
                                                    $sl_balance = $total_sl - $used_sl;

                                                    $sl_balance_previous_year = $sl_balance - $earned_sl;
                                                    if ($sl_balance_previous_year <= 0.000 || $sl_balance_previous_year <= 0.00 )
                                                    {
                                                        $sl_balance_previous_year = 0.00;
                                                    }
                                                @endphp
                                                {{$sl_balance_previous_year}}

                                            </td>
                                            <td>
                                                @php
                                                    $sl_balance_final = $sl_balance - $sl_balance_previous_year;
                                                @endphp
                                                {{$sl_balance_final}}
                                            </td>
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

<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function() {
        new DataTable('.table-detailed', {
        // pagelenth:25,
        paginate:false,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'excel'
        ],
        // columnDefs: [{
        //     "defaultContent": "-",
        //     "targets": "_all"
        // }],
        // order: [] 
        });
    });
</script>

@endsection