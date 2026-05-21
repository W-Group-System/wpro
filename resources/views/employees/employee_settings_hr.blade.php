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
        @php
            $employeeFullName = trim($user->employee->first_name.' '.($user->employee->middle_initial ? $user->employee->middle_initial.'. ' : '').$user->employee->last_name);
            $companyName = optional($user->employee->company)->company_name ?? optional($user->employee->company)->company_code;
            $departmentName = optional($user->employee->department)->name;
        @endphp
        <div class="card employee-profile-card mb-3">
            <div class="employee-profile-cover">
                <div>
                    <span class="employee-profile-cover-label">Employee Profile</span>
                </div>
                <span class="employee-profile-status badge badge-{{ $user->employee->status == 'Active' ? 'success' : 'secondary' }}">{{ $user->employee->status }}</span>
            </div>
            <div class="card-body">
                <div class="employee-profile-header">
                    <div class="employee-profile-identity">
                        <button type="button" class="employee-profile-avatar-button" data-toggle="modal" data-target="#uploadAvatar" title="Upload Avatar">
                            <img class="employee-profile-avatar" src='{{URL::asset($user->employee->avatar)}}' onerror="this.src='{{URL::asset('/images/no_image.png')}}';">
                            <span class="employee-profile-avatar-edit"><i class="fa fa-camera"></i></span>
                        </button>
                        <div>
                            <h3 class="mb-1">
                                {{$employeeFullName}}
                            </h3>
                            <div class="employee-profile-subtitle">
                                <span><i class="fa fa-briefcase"></i> {{$user->employee->position ?: 'No position set'}}</span>
                                @if($departmentName)
                                    <span><i class="fa fa-users"></i> {{$departmentName}}</span>
                                @endif
                            </div>
                            <div class="employee-profile-pills">
                                <span><i class="fa fa-id-badge"></i> {{$user->employee->employee_code ?: 'No employee code'}}</span>
                                <span><i class="fa fa-building"></i> {{$companyName ?: 'No company set'}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="employee-profile-meta">
                        <div>
                            <small>Employee Code</small>
                            <strong>{{$user->employee->employee_code ?: '-'}}</strong>
                        </div>
                        <div>
                            <small>Biometric Code</small>
                            <strong>{{$user->employee->employee_number ?: '-'}}</strong>
                        </div>
                        <div>
                            <small>Company</small>
                            <strong>{{$companyName ?: '-'}}</strong>
                        </div>
                        <div>
                            <small>Department</small>
                            <strong>{{$departmentName ?: '-'}}</strong>
                        </div>
                    </div>
                    <div class="employee-profile-actions">
                        @if($user->employee->signature)
                            <img class="employee-profile-signature" src='{{URL::asset($user->employee->signature)}}' onerror="this.src='{{URL::asset('/images/signature.png')}}';">
                        @endif
                        <div class="btn-group-sm">
                            <button class="btn btn-info" data-toggle="modal" data-target="#uploadSignature">
                                Upload Signature
                            </button>
                            <a class="btn btn-warning" href='{{url("print-id/".$user->employee->id)}}' target="_blank">
                                Print ID
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="employee-profile-tabs">
                <div class="nav nav-pills" id="v-pills-tab" role="tablist">
                    <a class="nav-link active show" id="v-pills-personal-tab" data-toggle="pill" href="#v-pills-personal" role="tab" aria-controls="v-pills-personal" aria-selected="true"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Personal Information</a>
                    <a class="nav-link" id="v-pills-employment-tab" data-toggle="pill" href="#v-pills-employment" role="tab" aria-controls="v-pills-employment" aria-selected="true" data-url="{{url('account-setting-hr/'.$user->id.'/tab/employment')}}"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp;Employment Information</a>
                    <a class="nav-link" id="v-pills-schedule-tab" data-toggle="pill" href="#v-pills-schedule" role="tab" aria-controls="v-pills-schedule" aria-selected="false" data-url="{{url('account-setting-hr/'.$user->id.'/tab/schedule')}}"><i class="fa fa-calendar" aria-hidden="true"></i>&nbsp;Schedule</a>
                    <a class="nav-link" id="v-pills-contact-tab" data-toggle="pill" href="#v-pills-contact" role="tab" aria-controls="v-pills-contact" aria-selected="false" data-url="{{url('account-setting-hr/'.$user->id.'/tab/contact')}}"><i class="fa fa-phone" aria-hidden="true"></i>&nbsp;Contact</a>
                    <a class="nav-link" id="v-pills-beneficiaries-tab" data-toggle="pill" href="#v-pills-beneficiaries" role="tab" aria-controls="v-pills-beneficiaries" aria-selected="false" data-url="{{url('account-setting-hr/'.$user->id.'/tab/beneficiaries')}}"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;Beneficiaries</a>
                    <a class="nav-link" id="v-pills-history-tab" data-toggle="pill" href="#v-pills-history" role="tab" aria-controls="v-pills-history" aria-selected="false" data-url="{{url('account-setting-hr/'.$user->id.'/tab/history')}}"><i class="fa fa-history" aria-hidden="true"></i>&nbsp;History</a>
                    <a class="nav-link" id="v-pills-benefits-tab" data-toggle="pill" href="#v-pills-benefits" role="tab" aria-controls="v-pills-benefits" aria-selected="false" data-url="{{url('account-setting-hr/'.$user->id.'/tab/benefits')}}"><i class="fa fa-star" aria-hidden="true"></i>&nbsp;Benefits</a>
                    <a class="nav-link" id="v-pills-leave-settings-tab" data-toggle="pill" href="#v-pills-leave-settings" role="tab" aria-controls="v-pills-leave-settings" aria-selected="false" data-url="{{url('account-setting-hr/'.$user->id.'/tab/leave-settings')}}"><i class="fa fa-calendar-check-o" aria-hidden="true"></i>&nbsp;Leave Settings</a>
                    <a class="nav-link" id="v-pills-bank-tab" data-toggle="pill" href="#v-pills-bank" role="tab" aria-controls="v-pills-bank" aria-selected="false" data-url="{{url('account-setting-hr/'.$user->id.'/tab/bank')}}"><i class="fa fa-bank" aria-hidden="true"></i>&nbsp;Bank Details</a>
                    <a class="nav-link" id="v-pills-government-tab" data-toggle="pill" href="#v-pills-government" role="tab" aria-controls="v-pills-government" aria-selected="false" data-url="{{url('account-setting-hr/'.$user->id.'/tab/government')}}"><i class="fa fa-files-o" aria-hidden="true"></i>&nbsp;Government Records and Licenses</a>
                    <a class="nav-link" id="v-pills-training-tab" data-toggle="pill" href="#v-pills-training" role="tab" aria-controls="v-pills-training" aria-selected="false" data-url="{{url('account-setting-hr/'.$user->id.'/tab/training')}}"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;Training</a>
                    <a class="nav-link" id="v-pills-nte-tab" data-toggle="pill" href="#v-pills-nte" role="tab" aria-controls="v-pills-nte" aria-selected="false" data-url="{{url('account-setting-hr/'.$user->id.'/tab/nte')}}"><i class="fa fa-ban" aria-hidden="true"></i>&nbsp;NTE Uploads</a>
                    <a class="nav-link" id="v-employee-documents-tab" data-toggle="pill" href="#v-employee-documents" role="tab" aria-controls="v-employee-documents" aria-selected="false" data-url="{{url('account-setting-hr/'.$user->id.'/tab/documents')}}"><i class="fa fa-file" aria-hidden="true"></i>&nbsp;201 Files</a>
                    <a class="nav-link" id="v-employee-org-chart-tab" data-toggle="pill" href="#v-employee-org-chart" role="tab" aria-controls="v-employee-org-chart" aria-selected="false" data-url="{{url('account-setting-hr/'.$user->id.'/tab/org-chart')}}"><i class="fa fa-sitemap" aria-hidden="true"></i>&nbsp;Org Chart</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="tab-content tab-employee" style="border: 1px solid #CED4DA" id="v-pills-tabContent">
                    <div class="tab-pane fade active show" id="v-pills-personal" role="tabpanel" aria-labelledby="v-pills-personal-tab" data-loaded="true">
                        @include('employees.profile_tabs.personal')
                    </div>
                    <div class="tab-pane fade" id="v-pills-employment" role="tabpanel" aria-labelledby="v-pills-employment-tab" data-loaded="false">
                        <div class="employee-tab-loader">Loading...</div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-schedule" role="tabpanel" aria-labelledby="v-pills-schedule-tab" data-loaded="false">
                        <div class="employee-tab-loader">Loading...</div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-contact" role="tabpanel" aria-labelledby="v-pills-contact-tab" data-loaded="false">
                        <div class="employee-tab-loader">Loading...</div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-beneficiaries" role="tabpanel" aria-labelledby="v-pills-beneficiaries-tab" data-loaded="false">
                        <div class="employee-tab-loader">Loading...</div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-history" role="tabpanel" aria-labelledby="v-pills-history-tab" data-loaded="false">
                        <div class="employee-tab-loader">Loading...</div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-bank" role="tabpanel" aria-labelledby="v-pills-bank-tab" data-loaded="false">
                        <div class="employee-tab-loader">Loading...</div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-benefits" role="tabpanel" aria-labelledby="v-pills-benefits" data-loaded="false">
                        <div class="employee-tab-loader">Loading...</div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-leave-settings" role="tabpanel" aria-labelledby="v-pills-leave-settings-tab" data-loaded="false">
                        <div class="employee-tab-loader">Loading...</div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-government" role="tabpanel" aria-labelledby="v-pills-government-tab" data-loaded="false">
                        <div class="employee-tab-loader">Loading...</div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-training" role="tabpanel" aria-labelledby="v-pills-training" data-loaded="false">
                        <div class="employee-tab-loader">Loading...</div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-nte" role="tabpanel" aria-labelledby="v-pills-nte" data-loaded="false">
                        <div class="employee-tab-loader">Loading...</div>
                    </div>
                    <div class="tab-pane fade" id="v-employee-documents" role="tabpanel" aria-labelledby="v-employee-documents" data-loaded="false">
                        <div class="employee-tab-loader">Loading...</div>
                    </div>
                    <div class="tab-pane fade" id="v-employee-org-chart" role="tabpanel" aria-labelledby="v-employee-org-chart" data-loaded="false">
                        <div class="employee-tab-loader">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .employee-profile-card {
        border: 0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 12px 32px rgba(31, 45, 61, .10);
    }
    .employee-profile-cover {
        position: relative;
        min-height: 190px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 28px 32px 64px;
        color: #fff;
        background:
            linear-gradient(120deg, rgba(24, 74, 129, .94), rgba(36, 138, 253, .82)),
            url('{{URL::asset('/images/wgroup.png')}}') center right 32px / 260px auto no-repeat;
    }
    .employee-profile-cover h2 {
        margin: 8px 0 4px;
        color: #fff;
        font-size: 2rem;
        font-weight: 700;
        line-height: 1.15;
    }
    .employee-profile-cover p {
        margin: 0;
        color: rgba(255, 255, 255, .82);
        font-size: 1rem;
    }
    .employee-profile-cover-label {
        display: inline-flex;
        align-items: center;
        min-height: 26px;
        padding: .25rem .65rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, .16);
        color: #fff;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .employee-profile-status {
        padding: .45rem .7rem;
        border: 2px solid rgba(255, 255, 255, .45);
    }
    .employee-profile-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
        margin-top: -76px;
    }
    .employee-profile-identity {
        display: flex;
        align-items: flex-start;
        gap: 18px;
        min-width: 260px;
    }
    .employee-profile-identity > div {
        padding-top: 76px;
    }
    .employee-profile-identity h3 {
        color: #172033;
        line-height: 1.25;
        word-break: break-word;
    }
    .employee-profile-avatar {
        width: 132px;
        height: 132px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #ffffff;
        box-shadow: 0 10px 26px rgba(31, 45, 61, .18);
    }
    .employee-profile-avatar-button {
        position: relative;
        display: inline-flex;
        width: 132px;
        height: 132px;
        padding: 0;
        border: 0;
        border-radius: 50%;
        background: transparent;
        cursor: pointer;
    }
    .employee-profile-avatar-edit {
        position: absolute;
        right: 4px;
        bottom: 6px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        background: #248AFD;
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
    }
    .employee-profile-subtitle {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 16px;
        color: #667085;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .employee-profile-subtitle i,
    .employee-profile-pills i {
        margin-right: 5px;
    }
    .employee-profile-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .employee-profile-pills span {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: .35rem .65rem;
        border: 1px solid #d7e2ef;
        border-radius: 999px;
        background: #f8fbff;
        color: #334155;
        font-size: .8rem;
        font-weight: 700;
    }
    .employee-profile-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(160px, 1fr));
        gap: 12px 24px;
        flex: 1;
        min-width: 300px;
        padding-top: 76px;
    }
    .employee-profile-meta > div {
        padding: .75rem .85rem;
        border: 1px solid #e4ebf3;
        border-radius: 8px;
        background: #ffffff;
    }
    .employee-profile-meta small {
        display: block;
        color: #6c757d;
        font-size: 11px;
        text-transform: uppercase;
    }
    .employee-profile-meta strong {
        color: #263238;
        font-weight: 600;
    }
    .employee-profile-actions {
        text-align: right;
    }
    .employee-profile-signature {
        display: block;
        width: 220px;
        height: 70px;
        object-fit: contain;
        margin: 0 0 8px auto;
    }
    .employee-profile-tabs {
        padding: 0 18px 14px;
        border-top: 1px solid #edf2f7;
        background: #fbfdff;
    }
    .employee-profile-tabs .nav-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding-top: 14px;
    }
    .nav-pills .nav-link {
        margin-bottom: 0;
        border-radius: 6px;
        color: #000f21;
        border: 1px solid #b6d0ed;
        padding: .55rem .85rem;
        white-space: nowrap;
    }
    .nav-pills {
        border-bottom: 0px solid #CED4DA;
    }
    .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
        color: #fff;
        background-color: #248AFD;
    }
    .tab-employee {
        padding: 18px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(31, 45, 61, .06);
    }
    .employee-tab-loader {
        padding: 42px 16px;
        text-align: center;
        color: #6c757d;
        font-weight: 600;
    }
    .profile-about {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 18px;
        align-items: start;
    }
    .profile-panel {
        border: 1px solid #e3ebf5;
        border-radius: 10px;
        background: #ffffff;
        box-shadow: 0 8px 22px rgba(31, 45, 61, .05);
        overflow: hidden;
    }
    .profile-panel + .profile-panel {
        margin-top: 18px;
    }
    .profile-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid #edf2f7;
        background: #fbfdff;
    }
    .profile-panel-header.compact {
        padding-bottom: 14px;
    }
    .profile-panel-kicker {
        display: block;
        color: #248AFD;
        font-size: .72rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 6px;
        text-transform: uppercase;
    }
    .profile-panel h4 {
        margin: 0;
        color: #1f2d3d;
        font-size: 1.05rem;
        font-weight: 800;
    }
    .profile-intro-card {
        display: flex;
        gap: 16px;
        align-items: center;
        margin: 20px;
        padding: 18px;
        border: 1px solid #e3ebf5;
        border-radius: 10px;
        background: linear-gradient(135deg, #f8fbff, #ffffff);
    }
    .profile-intro-card img {
        width: 86px;
        height: 86px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 8px 20px rgba(31, 45, 61, .12);
    }
    .profile-intro-card h3 {
        margin: 0 0 4px;
        color: #172033;
        font-size: 1.25rem;
        font-weight: 800;
    }
    .profile-intro-card p {
        margin: 0 0 10px;
        color: #667085;
        font-weight: 600;
    }
    .profile-quick-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .profile-quick-tags span {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: .3rem .6rem;
        border: 1px solid #d8e6f5;
        border-radius: 999px;
        background: #ffffff;
        color: #334155;
        font-size: .78rem;
        font-weight: 700;
    }
    .profile-quick-tags i {
        margin-right: 5px;
        color: #248AFD;
    }
    .profile-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        padding: 0 20px 20px;
    }
    .profile-detail-card {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        min-height: 82px;
        padding: 14px;
        border: 1px solid #e3ebf5;
        border-radius: 10px;
        background: #ffffff;
    }
    .profile-detail-icon {
        flex: 0 0 36px;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #eef5ff;
        color: #248AFD;
    }
    .profile-detail-card small,
    .profile-address-card small {
        display: block;
        color: #7b8794;
        font-size: .72rem;
        font-weight: 800;
        margin-bottom: 4px;
        text-transform: uppercase;
    }
    .profile-detail-card strong {
        display: block;
        color: #1f2d3d;
        font-size: .92rem;
        font-weight: 700;
        line-height: 1.35;
        word-break: break-word;
    }
    .profile-contact-list {
        padding: 8px 20px 20px;
    }
    .profile-contact-list div {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 0;
        border-bottom: 1px solid #edf2f7;
        color: #334155;
        font-weight: 700;
        word-break: break-word;
    }
    .profile-contact-list div:last-child {
        border-bottom: 0;
    }
    .profile-contact-list i {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #eef5ff;
        color: #248AFD;
    }
    .profile-address-card {
        margin: 14px 20px;
        padding: 14px;
        border: 1px solid #e3ebf5;
        border-radius: 10px;
        background: #fbfdff;
    }
    .profile-address-card p {
        margin: 0;
        color: #334155;
        font-weight: 700;
        line-height: 1.45;
    }
    @media (max-width: 767.98px) {
        .employee-profile-header,
        .employee-profile-identity,
        .employee-profile-actions {
            text-align: center;
            justify-content: center;
            width: 100%;
        }
        .employee-profile-cover {
            min-height: 210px;
            padding: 24px 20px 78px;
            text-align: center;
            justify-content: center;
            background:
                linear-gradient(120deg, rgba(24, 74, 129, .96), rgba(36, 138, 253, .86)),
                url('{{URL::asset('/images/wgroup.png')}}') center bottom 22px / 200px auto no-repeat;
        }
        .employee-profile-status {
            position: absolute;
            top: 16px;
            right: 16px;
        }
        .employee-profile-cover h2 {
            font-size: 1.55rem;
        }
        .employee-profile-identity {
            flex-direction: column;
        }
        .employee-profile-identity > div {
            padding-top: 0;
        }
        .employee-profile-subtitle,
        .employee-profile-pills {
            justify-content: center;
        }
        .employee-profile-meta {
            grid-template-columns: 1fr;
            min-width: 100%;
            padding-top: 0;
        }
        .employee-profile-signature {
            margin-right: auto;
        }
        .employee-profile-tabs .nav-link {
            flex: 1 1 calc(50% - 8px);
            text-align: center;
            white-space: normal;
        }
        .profile-about {
            grid-template-columns: 1fr;
        }
        .profile-panel-header,
        .profile-intro-card {
            align-items: flex-start;
            flex-direction: column;
        }
        .profile-detail-grid {
            grid-template-columns: 1fr;
        }
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
<script>
    $(document).ready(function() {
        $('#v-pills-tab a[data-toggle="pill"]').on('shown.bs.tab', function (event) {
            var tabLink = $(event.target);
            var url = tabLink.data('url');
            var target = $(tabLink.attr('href'));

            if (!url || target.data('loaded') === true || target.data('loaded') === 'true') {
                return;
            }

            target.html('<div class="employee-tab-loader">Loading...</div>');

            $.get(url)
                .done(function (html) {
                    target.html(html);
                    target.data('loaded', true);
                    if ($.fn.DataTable) {
                        target.find('.tablewithSearch').DataTable();
                    }
                })
                .fail(function () {
                    target.html('<div class="employee-tab-loader text-danger">Unable to load this tab. Please refresh and try again.</div>');
                });
        });

        $("[name='status']").on('change', function() {
            var value = $(this).val()
            // console.log(value);
            if (value == 'Resigned')
            {
                $("#clearancePortalEmail").removeAttr('hidden')
            }
            else
            {
                $("#clearancePortalEmail").prop('hidden', true)
            }
        })
        
    })
</script>
@endsection
