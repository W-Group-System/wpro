@extends('layouts.header')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class='row'>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Users 
                            @if (count($users) > 0)
                            <a href='/users-export' class='btn btn-info'>Export</a>
                            @endif
                            
                        </h4>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback" style="display: block;margin:0px 0px 10px 0px;" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif

                        <h4 class="card-title">Filter</h4>
						<form method='get' onsubmit='show();' enctype="multipart/form-data">
							<div class=row>
                                <div class='col-md-6'>
									<div class="form-group">
                                        <input type="text" class="form-control" name="search" placeholder="Search Name / Biometric Code" value="{{$search}}">
                                    </div>
                                </div>
                                <div class='col-md-6'>
									<button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="/users" class="btn btn-warning">Reset Filter</a>
								</div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table border="1" class="table table-hover table-bordered users_table" id='users_table'>
                                <thead>
                                    <tr>
                                        {{-- <th>ID</th> --}}
                                        <th>Employee Code</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        {{-- <td>{{$user->id}}</td> --}}
                                        <td>{{$user->employee->employee_code}}</td>
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->role}}</td>
                                        <td>
                                            <a href="/edit-user-role/{{$user->id}}" target="_blank" class="btn btn-outline-info btn-icon-text btn-sm">
                                                Edit
                                                <i class="ti-file btn-icon-append"></i>
                                            </a>
                                            <a href="/change-password/{{$user->id}}" target="_blank" class="btn btn-outline-info btn-icon-text btn-sm">
                                                Change Password
                                                <i class="ti-key btn-icon-append"></i>
                                            </a>
                                            <button class="btn btn-outline-info btn-icon-text btn-sm" data-toggle="modal" data-target="#accessModal-{{$user->id}}">
                                                Module Access
                                                <i class="ti-key btn-icon-append"></i>
                                            </button>
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
</div>

@foreach($users as $user)
{{-- @include('users.edit_user_role') --}}
@include('users.user_change_password')
@include('users.user_access')
@endforeach

@endsection

@section('js')
<script>
    $(document).ready(function() {

        $(".timekeepingAll").on('change', function() {
            if($(this).is(':checked')) {
                $('.timekeeping_checkbox').prop('checked', true)
            }
            else {
                $('.timekeeping_checkbox').prop('checked', false)
            }
        })
        
        $(".biometricsAll").on('change', function() {
            if($(this).is(':checked')) {
                $('.biometrics_checkbox').prop('checked', true)
            }
            else {
                $('.biometrics_checkbox').prop('checked', false)
            }
        })

        $(".settingsAll").on('change', function() {
            if ($(this).is(":checked")) {
                $('.settings_checkbox').prop('checked', true)
            }
            else {
                $('.settings_checkbox').prop('checked', false)
            }
        })

        $(".payrollAll").on('change', function() {
            if ($(this).is(":checked")) {
                $('.payroll_checkbox').prop('checked', true)
            }
            else {
                $('.payroll_checkbox').prop('checked', false)
            }
        })

        $(".payrollRegisterAll").on('change', function() {
            if ($(this).is(":checked")) {
                $('.payroll_settings_checkbox').prop('checked', true)
            }
            else {
                $('.payroll_settings_checkbox').prop('checked', false)
            }
        })

        $(".masterFilesAll").on('change', function() {
            if ($(this).is(":checked")) {
                $('.masterfiles_checkbox').prop('checked', true)
            }
            else {
                $('.masterfiles_checkbox').prop('checked', false)
            }
        })

        $(".reportsAll").on('change', function() {
            if ($(this).is(":checked")) {
                $('.report_checkbox').prop('checked', true)
            }
            else {
                $('.report_checkbox').prop('checked', false)
            }
        })

    })
</script>
@endsection

@section('footer')
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/js/inputmask.js') }}"></script>
@endsection
