@extends('layouts.header')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        {{-- <div class='row'>
            <div class="col-lg-4 grid-margin ">
                <div class="card">
                    <div class="card-body text-center">
                        <img class="rounded-circle" style='width:170px;height:170px;' src='{{URL::asset($user->employee->avatar)}}' onerror="this.src='{{URL::asset('/images/no_image.png')}}';">
                        <h3 class="card-text mt-3">{{$user->employee->first_name}} @if($user->employee->middle_initial != null){{$user->employee->middle_initial}}.@endif {{$user->employee->last_name}}</h3>
                        <h4 class="card-text mt-2">{{$user->employee->position}}</h4>
                        <h5 class="card-text mt-2">Biometric Code : {{$user->employee->employee_number}}</h5>
                        <h5 class="card-text mt-2">Employee Code : {{$user->employee->employee_code}}</h5>
                        @if($user->employee->signature)
                            <img src='{{URL::asset($user->employee->signature)}}' onerror="this.src='{{URL::asset('/images/signature.png')}}';" height='80px;' width='250px'><br>
                        @endif
                        <button class="btn btn-primary btn-sm mt-3" data-toggle="modal" data-target="#uploadAvatar">
                            Upload Avatar
                        </button>
                        <button class="btn btn-info btn-sm mt-3" data-toggle="modal" data-target="#uploadSignature">
                            Upload Signature
                        </button>
                        <a class="btn btn-warning btn-sm mt-3" href='{{url("print-id/".$user->employee->id)}}' target="_blank">
                            Print ID
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 grid-margin">
                <div class="card">
                    <div class="card-body text-left">
                        <div class="template-demo">
                            <div class='row m-2'>
                                <div class='col-md-12 text-center'>
                                    <strong>
                                        <h3><i class="fa fa-user" aria-hidden="true"></i> Personal Information
                                            @if (checkUserPrivilege('employees_edit',auth()->user()->id) == 'yes')
                                                <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Edit Information" data-toggle="modal" data-target="#editInfo"><i class="fa fa-pencil"></i></button>
                                            @endif
                                        </h3>
                                    </strong>
                                </div>
                            </div>
                            <div class='row m-2 border-bottom'>
                                <div class='col-md-3 '>
                                    <small>Nickname </small>
                                </div>
                                <div class='col-md-9'>
                                    {{$user->employee->nick_name}}
                                </div>
                            </div>
                            <div class='row  m-2 border-bottom'>
                                <div class='col-md-3'>
                                    <small> Full Name </small>
                                </div>
                                <div class='col-md-9'>
                                    {{$user->employee->first_name}} @if($user->employee->middle_initial != null){{$user->employee->middle_initial}}.@endif {{$user->employee->last_name}}
                                </div>
                            </div>
                            <div class='row  m-2 border-bottom'>
                                <div class='col-md-3'>
                                    <small> Email </small>
                                </div>
                                <div class='col-md-9'>
                                    {{$user->employee->personal_email}}
                                </div>
                            </div>
                            <div class='row  m-2 border-bottom'>
                                <div class='col-md-3'>
                                    <small> Phone</small>
                                </div>
                                <div class='col-md-9'>
                                    {{$user->employee->personal_number}}
                                </div>
                            </div>
                            <div class='row  m-2 border-bottom'>
                                <div class='col-md-3'>
                                    <small>Marital Status </small>
                                </div>
                                <div class='col-md-9'>
                                    {{$user->employee->marital_status}}
                                </div>
                            </div>
                            <div class='row  m-2 border-bottom'>
                                <div class='col-md-3'>
                                    <small>Religion </small>
                                </div>
                                <div class='col-md-3'>
                                    {{$user->employee->religion}}
                                </div>
                                <div class='col-md-3'>
                                    <small>Gender </small>
                                </div>
                                <div class='col-md-3'>
                                    {{$user->employee->gender}}
                                </div>
                            </div>
                            <div class='row  m-2 border-bottom'>
                                <div class='col-md-3'>
                                    <small>Address </small>
                                </div>
                                <div class='col-md-9'>
                                    <small> Present : {{$user->employee->present_address}} </small><br>
                                    <small> Permanent : {{$user->employee->permanent_address}} </small>
                                </div>
                            </div>
                            <div class='row  m-2 border-bottom'>
                                <div class='col-md-3'>
                                    <small>Birth </small>
                                </div>
                                <div class='col-md-9'>
                                    @php
                                    $d1 = new DateTime($user->employee->birth_date);
                                    $d2 = new DateTime();
                                    $diff = $d2->diff($d1);
                                    @endphp
                                    <small> Date : {{date('F d, Y',strtotime($user->employee->birth_date))}} : {{$diff->y}} Years Old</small><br>
                                    <small> Place : {{$user->employee->birth_place}} </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($user->employee->payment_info)
                <div class="card mt-3">
                    <div class="card-body text-left">
                        <div class="template-demo">
                            <div class='row m-2'>
                                <div class='col-md-12 text-center'>
                                    <strong>
                                        <h3><i class="fa fa-money" aria-hidden="true"></i> Payment Information</h3>
                                    </strong>
                                </div>
                            </div>
                            <div class='row  m-2 border-bottom'>
                                <div class='col-md-3'>
                                    <small>Payment Period</small>
                                </div>
                                <div class='col-md-3'>
                                    {{$user->employee->payment_info->payment_period}}
                                </div>
                                <div class='col-md-3'>
                                    <small>Payment Type</small>
                                </div>
                                <div class='col-md-3'>
                                    {{$user->employee->payment_info->payment_type}}
                                </div>
                            </div>
                            <div class='row  m-2 border-bottom'>
                                <div class='col-md-3'>
                                    <small>Bank Name</small>
                                </div>
                                <div class='col-md-3'>
                                    {{$user->employee->bank_name}}
                                </div>
                                <div class='col-md-3'>
                                    <small>Account Number</small>
                                </div>
                                <div class='col-md-3'>
                                    {{$user->employee->bank_account_number}}
                                </div>
                            </div>
                            <div class='row  m-2 border-bottom'>
                                <div class='col-md-3'>
                                    <small>Rate</small>
                                </div>
                                <div class='col-md-3'>
                                    <a href="#" data-toggle="modal" onclick='reset_data();' data-target="#rateData">********</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                @endif 
            </div>
        </div> --}}
        <div class="row">
            <div class="col-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active show" id="v-pills-personal-tab" data-toggle="pill" href="#v-pills-personal" role="tab" aria-controls="v-pills-personal" aria-selected="true"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Personal Information</a>
                    <a class="nav-link" id="v-pills-employment-tab" data-toggle="pill" href="#v-pills-employment" role="tab" aria-controls="v-pills-employment" aria-selected="true"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp;Employment Information</a>
                    <a class="nav-link" id="v-pills-schedule-tab" data-toggle="pill" href="#v-pills-schedule" role="tab" aria-controls="v-pills-schedule" aria-selected="false"><i class="fa fa-calendar" aria-hidden="true"></i>&nbsp;Schedule</a>
                    <a class="nav-link" id="v-pills-contact-tab" data-toggle="pill" href="#v-pills-contact" role="tab" aria-controls="v-pills-contact" aria-selected="false"><i class="fa fa-phone" aria-hidden="true"></i>&nbsp;Contact</a>
                    <a class="nav-link" id="v-pills-beneficiaries-tab" data-toggle="pill" href="#v-pills-beneficiaries" role="tab" aria-controls="v-pills-beneficiaries" aria-selected="false"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;Beneficiaries</a>
                    <a class="nav-link" id="v-pills-history-tab" data-toggle="pill" href="#v-pills-history" role="tab" aria-controls="v-pills-history" aria-selected="false"><i class="fa fa-history" aria-hidden="true"></i>&nbsp;History</a>
                    <a class="nav-link" id="v-pills-benefits-tab" data-toggle="pill" href="#v-pills-benefits" role="tab" aria-controls="v-pills-benefits" aria-selected="false"><i class="fa fa-star" aria-hidden="true"></i>&nbsp;Benefits</a>
                    <a class="nav-link" id="v-pills-bank-tab" data-toggle="pill" href="#v-pills-bank" role="tab" aria-controls="v-pills-bank" aria-selected="false"><i class="fa fa-bank" aria-hidden="true"></i>&nbsp;Bank Details</a>
                    <a class="nav-link" id="v-pills-government-tab" data-toggle="pill" href="#v-pills-government" role="tab" aria-controls="v-pills-government" aria-selected="false"><i class="fa fa-files-o" aria-hidden="true"></i>&nbsp;Government Records and Licenses</a>
                    <a class="nav-link" id="v-pills-training-tab" data-toggle="pill" href="#v-pills-training" role="tab" aria-controls="v-pills-training" aria-selected="false">
                      <i class="fa fa-book" aria-hidden="true"></i>
                      &nbsp;Training
                    </a>
                    <a class="nav-link" id="v-pills-nte-tab" data-toggle="pill" href="#v-pills-nte" role="tab" aria-controls="v-pills-nte" aria-selected="false">
                      <i class="fa fa-ban" aria-hidden="true"></i>
                      &nbsp;NTE Uploads
                    </a>
                    <a class="nav-link" id="v-employee-documents-tab" data-toggle="pill" href="#v-employee-documents" role="tab" aria-controls="v-employee-documents" aria-selected="false">
                      <i class="fa fa-file" aria-hidden="true"></i>
                      &nbsp;201 Files
                    </a>
                    <a class="nav-link" id="v-employee-org-chart-tab" data-toggle="pill" href="#v-employee-org-chart" role="tab" aria-controls="v-employee-org-chart" aria-selected="false">
                      <i class="fa fa-file" aria-hidden="true"></i>
                      &nbsp;Org Chart
                    </a>
                </div>
            </div>
            <div class="col-9">
                <div class="tab-content tab-employee" style="border: 1px solid #CED4DA" id="v-pills-tabContent">
                    <div class="tab-pane fade active show" id="v-pills-personal" role="tabpanel" aria-labelledby="v-pills-personal-tab">
                        <div class="card">
                            <div class="template-demo">
                                <div class="template-demo">
                                    <div class='row m-2'>
                                        <div class='col-md-12 text-center mt-3 mb-3'>
                                            <img class="rounded-circle" style='width:120px;height:120px;' src='{{URL::asset($user->employee->avatar)}}' onerror="this.src='{{URL::asset('/images/no_image.png')}}';">
                                            <h3 class="card-text mt-3">{{$user->employee->first_name}} @if($user->employee->middle_initial != null){{$user->employee->middle_initial}}.@endif {{$user->employee->last_name}}</h3>
                                            <h4 class="card-text mt-2">{{$user->employee->position}}</h4>
                                            <h5 class="card-text mt-2">Biometric Code : {{$user->employee->employee_number}}</h5>
                                            {{-- <h5 class="card-text mt-2">Employee Code : {{$user->employee->employee_code}}</h5> --}}
                                            @if($user->employee->signature)
                                                <img src='{{URL::asset($user->employee->signature)}}' onerror="this.src='{{URL::asset('/images/signature.png')}}';" height='80px;' width='250px'><br>
                                            @endif
                                            <button class="btn btn-primary btn-sm mt-3" data-toggle="modal" data-target="#uploadAvatar">
                                                Upload Avatar
                                            </button>
                                            <button class="btn btn-info btn-sm mt-3" data-toggle="modal" data-target="#uploadSignature">
                                                Upload Signature
                                            </button>
                                            <a class="btn btn-warning btn-sm mt-3" href='{{url("print-id/".$user->employee->id)}}' target="_blank">
                                                Print ID
                                            </a>
                                            <strong>
                                                <h3 class="mt-3">Personal Information
                                                    @if (checkUserPrivilege('employees_edit',auth()->user()->id) == 'yes')
                                                        <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Edit Information" data-toggle="modal" data-target="#editInfo"><i class="fa fa-pencil"></i></button>
                                                    @endif
                                                </h3>
                                            </strong>
                                        </div>
                                    </div>
                                    <div class='row m-2 border-bottom'>
                                        <div class='col-md-3 '>
                                            <small>Nickname </small>
                                        </div>
                                        <div class='col-md-9'>
                                            {{$user->employee->nick_name}}
                                        </div>
                                    </div>
                                    <div class='row  m-2 border-bottom'>
                                        <div class='col-md-3'>
                                            <small> Full Name </small>
                                        </div>
                                        <div class='col-md-9'>
                                            {{$user->employee->first_name}} @if($user->employee->middle_initial != null){{$user->employee->middle_initial}}.@endif {{$user->employee->last_name}}
                                        </div>
                                    </div>
                                    <div class='row  m-2 border-bottom'>
                                        <div class='col-md-3'>
                                            <small> Email </small>
                                        </div>
                                        <div class='col-md-9'>
                                            {{$user->employee->personal_email}}
                                        </div>
                                    </div>
                                    <div class='row  m-2 border-bottom'>
                                        <div class='col-md-3'>
                                            <small> Phone</small>
                                        </div>
                                        <div class='col-md-9'>
                                            {{$user->employee->personal_number}}
                                        </div>
                                    </div>
                                    <div class='row  m-2 border-bottom'>
                                        <div class='col-md-3'>
                                            <small>Marital Status </small>
                                        </div>
                                        <div class='col-md-9'>
                                            {{$user->employee->marital_status}}
                                        </div>
                                    </div>
                                    <div class='row  m-2 border-bottom'>
                                        <div class='col-md-3'>
                                            <small>Religion </small>
                                        </div>
                                        <div class='col-md-3'>
                                            {{$user->employee->religion}}
                                        </div>
                                        <div class='col-md-3'>
                                            <small>Gender </small>
                                        </div>
                                        <div class='col-md-3'>
                                            {{$user->employee->gender}}
                                        </div>
                                    </div>
                                    <div class='row  m-2 border-bottom'>
                                        <div class='col-md-3'>
                                            <small>Address </small>
                                        </div>
                                        <div class='col-md-9'>
                                            <small> Present : {{$user->employee->present_address}} </small><br>
                                            <small> Permanent : {{$user->employee->permanent_address}} </small>
                                        </div>
                                    </div>
                                    <div class='row  m-2 border-bottom'>
                                        <div class='col-md-3'>
                                            <small>Birth </small>
                                        </div>
                                        <div class='col-md-9'>
                                            @php
                                            $d1 = new DateTime($user->employee->birth_date);
                                            $d2 = new DateTime();
                                            $diff = $d2->diff($d1);
                                            @endphp
                                            <small> Date : {{date('F d, Y',strtotime($user->employee->birth_date))}} : {{$diff->y}} Years Old</small><br>
                                            <small> Place : {{$user->employee->birth_place}} </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-employment" role="tabpanel" aria-labelledby="v-pills-employment-tab">
                        <div class="card">
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Employment Information 
                                                @if (checkUserPrivilege('employees_edit',auth()->user()->id) == 'yes')
                                                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Edit Employee Information" data-toggle="modal" data-target="#editEmpInfo"><i class="fa fa-pencil"></i></button>
                                                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Notice of Personnel Action" data-toggle="modal" data-target="#createNopa"><i class="fa fa-pencil-square-o"></i></button>
                                                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Edit employee no" data-toggle="modal" data-target="#editEmpNo">
                                                        <i class="fa fa-id-card-o"></i>
                                                    </button>
                                                @endif
                                            </h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Email </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->email}}
                                    </div>
                                </div>
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3 '>
                                        <small>Company </small>
                                    </div>
                                    <div class='col-md-9'>
                                        @if($user->employee->company) {{$user->employee->company->company_name}} @endif
                                    </div>
                                </div>
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3 '>
                                        <small>Deparment </small>
                                    </div>
                                    <div class='col-md-9'>
                                        @if($user->employee->department) {{$user->employee->department->name}} @endif
                                    </div>
                                </div>
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3 '>
                                        <small>Location </small>
                                    </div>
                                    <div class='col-md-9'>
                                        @if($user->employee->location) {{$user->employee->location}} @endif
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Classification </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->employee->classification_info ? $user->employee->classification_info->name : ""}}
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Level </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->employee->level_info ? $user->employee->level_info->name : "" }}
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Date Hired </small>
                                    </div>
                                    <div class='col-md-3'>
                                        {{date('M d, Y',strtotime($user->employee->original_date_hired))}}
                                    </div>
                                    <div class='col-md-6'>
                                        @php
                                        $date_from = new DateTime($user->employee->original_date_hired);
                                        $date_diff = $date_from->diff(new DateTime(date('Y-m-d')));
                                        @endphp
                                        {{$date_diff->format('%y Year %m months %d days')}}
                                    </div>
                                </div>
                            </div>
                            @if (checkUserPrivilege('payroll_view',auth()->user()->id) == 'yes')
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Salary Information 
                                                @if (checkUserPrivilege('employees_edit',auth()->user()->id) == 'yes')
                                                @if (empty($user->employee->employee_salary))
                                                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Employee Salary" data-toggle="modal" data-target="#createEmpSalary">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                @endif
                                                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Salary Notice of Personnel Action" data-toggle="modal" data-target="#createSalaryNopa"><i class="fa fa-pencil-square-o"></i></button>
                                                @endif
                                            </h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Basic Salary </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{ $user->employee->employee_salary ? $user->employee->employee_salary->basic_salary : ""}}
                                    </div>
                                </div>
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3 '>
                                        <small>De Minimis</small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{ $user->employee->employee_salary ? $user->employee->employee_salary->de_minimis : ""}}
                                    </div>
                                </div>
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3 '>
                                        <small>Other Allowances </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{ $user->employee->employee_salary ? $user->employee->employee_salary->other_allowance : ""}}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-schedule" role="tabpanel" aria-labelledby="v-pills-schedule-tab">
                        <div class="card">
                            <div class="template-demo">
                                <div class='row'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Schedule</h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>Start Time</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>End Time</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>Total hours</small>
                                    </div>
                                </div>
                                @foreach($user->employee->ScheduleData as $schedule)
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small>{{$schedule->name}}</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>{{$schedule->time_in_from}}</small> <br>
                                        <small>{{$schedule->time_in_to}}</small>

                                    </div>
                                    <div class='col-md-3'>
                                        <small>{{$schedule->time_out_from}}</small> <br>
                                        <small>{{$schedule->time_out_to}}</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>{{number_format($schedule->working_hours,1)}} </small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-contact" role="tabpanel" aria-labelledby="v-pills-contact-tab">
                        <div class="card">
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Contact Person (In case of Emergency)
                                                @if (checkUserPrivilege('employees_edit',auth()->user()->id) == 'yes')
                                                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Edit Contact Person" data-toggle="modal" data-target="#editContactInfo"><i class="fa fa-pencil"></i></button>
                                                @endif
                                            </h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Contact Person </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->employee->contact_person ? $user->employee->contact_person->name : ""}}
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Contact Number </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->employee->contact_person ? $user->employee->contact_person->contact_number : ""}}
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Relation </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->employee->contact_person ? $user->employee->contact_person->relation : ""}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-beneficiaries" role="tabpanel" aria-labelledby="v-pills-beneficiaries-tab">
                        <div class="card">
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Beneficiaries
                                                @if (checkUserPrivilege('employees_edit',auth()->user()->id) == 'yes')
                                                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Edit Beneficiaries" data-toggle="modal" data-target="#editBeneficiaries"><i class="fa fa-pencil"></i></button>
                                                @endif
                                            </h3>
                                        </strong>
                                    </div>
                                </div>
                                
                                @foreach($user->employee->beneficiaries as $key => $value)
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small>{{$value->relation}}</small>
                                    </div>
                                    <div class='col-md-9'>
                                        <small>{{$value->first_name . ' ' . $value->last_name}}</small>
                                    </div>
                                </div>
                                @endforeach                            
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-history" role="tabpanel" aria-labelledby="v-pills-contact-tab">
                        <div class="card">
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Notice of Personnel Action
                                            </h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Changed By: </small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small> Changed At: </small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>View Changes</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>Attachment</small>
                                    </div>
                                </div>
                                @foreach ($user->employee->employeeMovement as $movement)
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        {{ optional($movement->user_info)->name ?? 'N/A' }}
                                    </div>
                                    <div class='col-md-3'>
                                        {{date('M d, Y',strtotime($movement->changed_at ))}}
                                    </div>
                                    <div class='col-md-3'>
                                        <a href='#' data-toggle="modal" data-target="#viewNopa{{$movement->id}}">View</a>
                                    </div>
                                    <div class='col-md-3'>
                                        @if ($movement->nopa_attachment)
                                        <a href="{{ url($movement->nopa_attachment) }}" target="_blank">Attachment</a>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @if (checkUserPrivilege('payroll_view',auth()->user()->id) == 'yes')
                        <div class="card">
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Salary Notice of Personnel Action
                                            </h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Changed By: </small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small> Changed At: </small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>View Changes</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>Attachment</small>
                                    </div>
                                </div>
                                @foreach ($user->employee->salaryMovement as $movement)
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        {{ optional($movement->change_by)->name ?? 'N/A' }}
                                    </div>
                                    <div class='col-md-3'>
                                        {{date('M d, Y',strtotime($movement->changed_at ))}}
                                    </div>
                                    <div class='col-md-3'>
                                        <a href='#' data-toggle="modal" data-target="#viewSalaryNopa{{$movement->id}}">View</a>
                                    </div>
                                    <div class='col-md-3'>
                                        @if ($movement->salary_nopa_attachment)
                                        <a href="{{ url($movement->salary_nopa_attachment) }}" target="_blank">Attachment</a>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="v-pills-bank" role="tabpanel" aria-labelledby="v-pills-bank-tab">
                        <div class="card">
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Bank Details
                                                <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#editAcctNo">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                            </h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> BANK NAME </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->employee->bank_name}}
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> ACCOUNT NUMBER </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->employee->bank_account_number}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-benefits" role="tabpanel" aria-labelledby="v-pills-benefits">
                        <div class="card p-5">
                          <div class="template-demo">
                            <div class='row m-2'>
                                <div class='col-md-12 text-center mt-3 mb-3'>
                                    <strong>
                                        <h3>Employee Benefits
                                          <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#addEmployeeBenefitsModal">
                                            <i class="fa fa-plus"></i>
                                          </button>
                                        </h3>
                                    </strong>
                                </div>
                            </div>
                            <div class="table-responsive">
                              <table class="table table-hover table-bordered tablewithSearch">
                                <thead>
                                  <tr>
                                    <th>Employee Name</th>
                                    <th>Benefits</th>
                                    <th>Amount</th>
                                    <th>Date Posted</th>
                                    <th>Posted By</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach ($employeeBenefits as $eb)
                                    <tr>
                                      <td>{{$eb->user->name}}</td>
                                      <td>
                                        @switch($eb->benefits_name)
                                            @case('SL')
                                              Salary Loan
                                                @break
                                            @case('EA')
                                              Educational Assistance
                                                @break
                                            @case('WG')
                                              Wedding Gift
                                                @break
                                            @case('BA')
                                              Bereavement Assistance
                                                @break
                                            @case('HMO')
                                              Health Card (HMO)
                                                @break
                                            @default
                                        @endswitch
                                      </td>
                                      <td><span>&#8369;</span>{{$eb->amount}}</td>
                                      <td>{{date('M. d, Y', strtotime($eb->date))}}</td>
                                      <td>{{$eb->postedBy->name}}</td>
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-government" role="tabpanel" aria-labelledby="v-pills-government-tab">
                        <div class="card">
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Government Records and Licenses</h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small>SSS</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>{{$user->employee->sss_number}}</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>HDMF</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>{{$user->employee->hdmf_number}}</small>
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small>PHILHEALTH</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>{{$user->employee->phil_number}}</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>TIN</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>{{$user->employee->tax_number}}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-training" role="tabpanel" aria-labelledby="v-pills-training">
                      <div class="card p-2">
                        <div class="template-demo">
                          <div class='row m-2'>
                            <div class='col-md-12 text-center mt-3 mb-3'>
                              <h3>Training
                                <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#addTrainingModal">
                                  <i class="fa fa-plus"></i>
                                </button>
                              </h3>
                            </div>
                          </div>
                          <div class="table-responsive">
                            <table class="table table-bordered table-hover tablewithSearch">
                                <thead>
                                <tr>
                                    <th>Training</th>
                                    <th>Training Period</th>
                                    <th>Bond Period</th>
                                    <th>Attachment</th>
                                    <th>Certificate</th>
                                    <th>Amount</th>
                                    <!-- <th>Action</th> -->
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($employeeTraining) > 0)
                                    @foreach ($employeeTraining as $et)
                                    <tr>
                                        <td>{{$et->training}}</td>
                                        <td>{{date('M. d, Y', strtotime($et->start_date))}} - {{date('M. d, Y', strtotime($et->end_date))}}</td>
                                        <td>{{date('M. d, Y', strtotime($et->bond_start_date))}} - {{date('M. d, Y', strtotime($et->bond_end_date))}}</td>
                                        <td> @if ($et->attachment)
                                            <a href="{{ url($et->attachment) }}" target="_blank">Attachment</a>
                                            @endif
                                        </td>
                                        <td> @if ($et->training_attachment)
                                            <a href="{{ url($et->training_attachment) }}" target="_blank">Certificate</a>
                                            @endif
                                        </td>
                                        <td><span>&#8369;</span>{{ number_format($et->amount,2)}}</td>
                                        <!-- <td></td> -->
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                    <td colspan="7" class="text-center">No data available.</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-nte" role="tabpanel" aria-labelledby="v-pills-nte">
                      <div class="card p-2">
                        <div class="template-demo">
                          <div class='row m-2'>
                            <div class='col-md-12 text-center mt-3 mb-3'>
                              <h3>Employee NTE
                                <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#uploadNteModal">
                                  <i class="fa fa-plus"></i>
                                </button>
                              </h3>
                            </div>
                          </div>
                          <div class="table-responsive">
                            <table class="table table-hover table-bordered tablewithSearch">
                              <thead>
                                <tr>
                                  <th>File</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach ($employeeNte as $nte)
                                  <tr>
                                    <td>
                                      <a href="{{url($nte->file_path)}}" title="View file" target="_blank">
                                        {{$nte->file_name}}
                                      </a>
                                    </td>
                                  </tr>
                                @endforeach
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="v-employee-documents" role="tabpanel" aria-labelledby="v-employee-documents">
                      <div class="card p-4">
                        <div class="template-demo">
                          <div class='row m-2'>
                            <div class='col-md-12 text-center mt-3 mb-3'>
                              <h3>Employee Documents
                                <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#empDocsModal">
                                  <i class="fa fa-plus"></i>
                                </button>
                              </h3>
                            </div>
                          </div>
                          @php
                            $documentTypes = documentTypes();
                          @endphp
                          @foreach ($documentTypes as $key=>$docs)
                            <div class="row">
                              <div class="col-md-4 border border-1 border-secondary border-top-bottom border-left-right" style="width: 100%;">
                                {{$docs}}
                              </div>
                                <div class="col-md-4 border border-1 border-secondary border-top-bottom border-left-right" style="width: 100%;">
                                  @php
                                    $empty = false;
                                  @endphp
                                  @foreach ($employeeDocuments as $item)
                                    @if($key === $item->document_type)
                                      Passed
                                      @php
                                        $empty = true;
                                      @endphp
                                    @endif
                                  @endforeach
                                  @if(!$empty)
                                    Not Yet Submitted
                                  @endif
                                </div>
                              <div class="col-md-4 border border-1 border-secondary border-top-bottom border-left-right" style="width: 100%;">
                                @foreach ($employeeDocuments as $item)
                                  @if($key == $item->document_type)
                                    <a href="{{url($item->file_path)}}" target="_blank">{{$item->file_name}}</a>
                                  @endif
                                @endforeach
                              </div>
                            </div>
                          @endforeach
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="v-employee-org-chart" role="tabpanel" aria-labelledby="v-employee-org-chart">
                      <div class="card p-4">
                        <div class="template-demo">
                          <div class='row m-2'>
                            <div class='col-md-12 text-center mt-3 mb-3 h-100'>
                              <h3>Org Chart
                                
                            </h3>
                           
                        
                            
                         
                            <div id="orgChart"/>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link {
        margin-bottom: 10px;
        border-radius: 15px;
        color: #000f21;
        border: 1px solid #b6d0ed;
        padding: .75rem 1.75rem;
    }
    .nav-pills {
        border-bottom: 0px solid #CED4DA;
    }
    .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
        color: #fff;
        background-color: #248AFD;
    }
    .tab-employee {
        padding: 10px;
    }
</style>
@include('employees.upload_avatar')
@include('employees.upload_signature')
@include('employees.edit_info')
@include('employees.edit_employee_info')
@include('employees.edit_contact_info')
@include('employees.edit_beneficiaries')
@include('employees.create_nopa')
@include('employees.create_salary_nopa')
@include('employees.add_salary')
@foreach($user->employee->employeeMovement as $movement)
        @include('employees.view_nopa')   
@endforeach
@foreach($user->employee->salaryMovement as $movement)
        @include('employees.view_salary_nopa')   
@endforeach
@include('employee_benefits.add_employee_benefits')
@include('hr-portal.new-training')
@include('hr-portal.new-nte')
@include('hr-portal.edit-employee-document')
@include('employees.edit_employee_no_modal')
@include('employees.edit_bank_details')
@endsection
@section('js')

@endsection
