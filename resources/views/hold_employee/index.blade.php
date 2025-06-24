@extends('layouts.header')

@section('content')
	<div class="main-panel">
		<div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <form method="get" action="" onsubmit="show()">
                                <div class="row mb-3">
                                    <div class="col-lg-3">
                                        Companies :
                                        <select data-placeholder="Select company" name="company" class="form-control js-example-basic-single" >
                                            <option value=""></option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}" @if($company_data == $company->id) selected @endif>{{ $company->company_code.' - '. $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-3">
                                        Cut Off Date :
                                        <select data-placeholder="Select cut off date" name="cut_off_date" class="form-control js-example-basic-single" >
                                            <option value=""></option>
                                            @foreach ($cut_off_pay_reg as $cut_off)
                                                <option value="{{ $cut_off }}" @if($cut_off_date == $cut_off) selected @endif>{{ $cut_off }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-3">
                                        Employee :
                                        <select data-placeholder="Select employee" name="employee[]" class="form-control js-example-basic-multiple" multiple >
                                            <option value=""></option>
                                            @if($employee_array)
                                                @foreach ($employees->where('company_id', $company_data) as $employee)
                                                    <option value="{{ $employee->employee_code }}" @if(in_array($employee->employee_code, $employee_array)) selected @endif>{{ $employee->employee_code .' - '. $employee->user_info->name }}</option>
                                                @endforeach
                                            @else
                                                @foreach ($employees->where('company_id', $company_data) as $employee)
                                                    <option value="{{ $employee->employee_code }}">{{ $employee->employee_code .' - '. $employee->user_info->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-lg-3">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                            
                            <h4 class="card-title">Hold Employee</h4>
                            <form method="post" action="{{ url('delete-employee') }}" onsubmit="show()">
                                @csrf 
                                <input type="hidden" name="company" value="{{ $company_data }}">
                                <input type="hidden" name="cut_off_date" value="{{ $cut_off_date }}">
                                
                                <p class="card-description">
                                    <button type="submit" class="btn btn-outline-danger btn-icon-text" id="removeBtn">
                                        <i class="ti-na btn-icon-prepend"></i>
                                        Remove
                                    </button>
                                </p>
        
                                @if ($errors->any())
                                    @foreach ($errors->all() as $error)
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            {{ $error }}
                                        </div>
                                    @endforeach
                                @endif
        
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="tablewithSearch">
                                        <thead>
                                            <tr>
                                                <th>Action
                                                    <input type="checkbox" name="" id="checkAll">
                                                </th>
                                                <th>Company</th>
                                                <th>Employee No</th>
                                                <th>Employee</th>
                                                <th>Cut Off Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($attendance_detailed_reports as $attendance_detailed_report)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="employee_no[]" value="{{ $attendance_detailed_report->employee_no }}" class="actionCheckbox" id="actionCheckbox{{ $attendance_detailed_report->id }}">
                                                    </td>
                                                    <td>{{ $attendance_detailed_report->company->company_name }}</td>
                                                    <td>{{ $attendance_detailed_report->employee->employee_code }}</td>
                                                    <td>{{ $attendance_detailed_report->employee->user_info->name }}</td>
                                                    <td>{{ $attendance_detailed_report->cut_off_date }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    {{-- <button type="submit" class="btn btn-outline-danger btn-icon-text" id="removeBtn">
                                        <i class="ti-na btn-icon-prepend"></i>
                                        Remove
                                    </button> --}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>

{{-- @include('employee_leave_list.new_employee_leave_list') --}}
{{-- @foreach ($employee_leave_lists as $employee_leave_list)
@include('employee_leave_list.edit_employee_leave_list')    
@endforeach --}}
<script>
    $(document).ready(function() {
        $('#tablewithSearch').DataTable({
            dom: 'Bfrtip',
            stateSave: true,
            pageLength: 25,
            "ordering": false,
            //"paging": false,
            //"fixedColumns": {
            //	"left": 2
            //}
        });

        $("#checkAll").on('change', function() {
            if($(this).is(":checked")) {
                $(".actionCheckbox").prop('checked', true)
            }
            else {
                $(".actionCheckbox").prop('checked', false)
            }
        })
    })
</script>
@endsection
