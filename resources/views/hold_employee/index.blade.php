@extends('layouts.header')

@section('content')
	<div class="main-panel">
		<div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Hold Employee</h4>
                            <p class="card-description">
                                <button type="button" class="btn btn-outline-success btn-icon-text" data-toggle="modal"
                                    data-target="#new">
                                    <i class="ti-plus btn-icon-prepend"></i>
                                    New Leave Credit
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
                                            {{-- <th>Action</th> --}}
                                            <th>Employee</th>
                                            <th>Leave Entitlement</th>
                                            <th>Type of Leave</th>
                                            <th>Leave Credits</th>
                                            <th>Total Leaves</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
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

</script>
@endsection
