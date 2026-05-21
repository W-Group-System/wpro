@extends('layouts.header')
@section('content')
@php
    $selectedCompany = $companies->firstWhere('id', $company);
    $selectedDepartment = $departments->firstWhere('id', $department);
    $activeFilters = collect([
        $search ? 'Search: '.$search : null,
        $selectedCompany ? 'Company: '.$selectedCompany->company_code : null,
        $selectedDepartment ? 'Department: '.$selectedDepartment->code : null,
        $status ? 'Status: '.$status : null,
        request('classification') ? 'Classification: '.request('classification') : null,
        request('gender') ? 'Gender: '.request('gender') : null,
    ])->filter();
@endphp
<div class="main-panel">
    <div class="content-wrapper">
        <div class="employees-page">
            <div class="employees-hero">
                <div>
                    <span class="employees-kicker">People Directory</span>
                    <h2>Employees</h2>
                    <p>Search, filter, export, and open employee HR records from one workspace.</p>
                    <div class="employees-filter-tags">
                        @forelse($activeFilters as $filter)
                            <span>{{$filter}}</span>
                        @empty
                            <span>No filters applied</span>
                        @endforelse
                    </div>
                </div>
                <div class="employees-actions">
                    @if (checkUserPrivilege('employees_add',auth()->user()->id) == 'yes')
                        <button type="button" class="btn btn-success btn-sm btn-icon-text employees-new-btn" data-toggle="modal" data-target="#newEmployee"><i class="ti-plus btn-icon-prepend"></i> New Employee</button>
                    @endif
                    @if(in_array(auth()->user()->id, [875, 17, 272, 781]))
                        <button type="button" class="btn btn-outline-primary btn-sm btn-icon-text" title="Export Template Employees Salaries" onclick="window.location.href='{{ route('export.salaries') }}'"><i class="ti-download btn-icon-prepend"></i> Salary Template</button>
                    @endif
                    @if (checkUserPrivilege('employees_export_hr',auth()->user()->id) == 'yes')
                        <a href="/employees-export-hr?company={{$company}}&department={{$department}}&status={{$status}}" class="btn btn-outline-primary btn-sm btn-icon-text" title="Export HR Details"><i class="ti-arrow-down btn-icon-prepend"></i> HR Export</a>
                    @endif
                    @if (checkUserPrivilege('employees_export',auth()->user()->id) == 'yes')
                        <a href="/employees-export?company={{$company}}&department={{$department}}&status={{$status}}" class="btn btn-outline-danger btn-sm btn-icon-text" title="Export OTPMS"><i class="ti-arrow-down btn-icon-prepend"></i> OTPMS</a>
                        @if(auth()->user()->id == '660' || auth()->user()->id == '1' || auth()->user()->id == '1202')
                            <a href="/associate-employees-export?company={{$company}}&department={{$department}}&status={{$status}}" class="btn btn-outline-warning btn-sm btn-icon-text" title="Export Employee Associates"><i class="ti-arrow-down btn-icon-prepend"></i> Associates</a>
                        @endif
                    @endif
                </div>
            </div>

            <div class="employees-metrics">
                <div class="employees-metric-card is-active">
                    <span class="employees-metric-icon"><i class="fa fa-users"></i></span>
                    <small>Active Employees</small>
                    <strong>{{$employees_active}}</strong>
                    <p>Across allowed companies</p>
                </div>
                <div class="employees-metric-card">
                    <span class="employees-metric-icon"><i class="fa fa-address-card"></i></span>
                    <small>Current Results</small>
                    <strong>{{$employees->total()}}</strong>
                    <p>{{$employees->count()}} shown on this page</p>
                </div>
                <div class="employees-metric-card">
                    <span class="employees-metric-icon"><i class="fa fa-building"></i></span>
                    <small>Companies</small>
                    <strong>{{$companies->count()}}</strong>
                    <p>Available to your access</p>
                </div>
                <div class="employees-metric-card">
                    <span class="employees-metric-icon"><i class="fa fa-filter"></i></span>
                    <small>Filter State</small>
                    <strong>{{$status ?: 'All'}}</strong>
                    <p>{{$activeFilters->count()}} active filters</p>
                </div>
            </div>

            <div class="employees-insights">
                <div class="employees-insight-card">
                    <div class="employees-card-header">
                        <div>
                            <span class="employees-kicker">Breakdown</span>
                            <h4>Classification</h4>
                        </div>
                    </div>
                    <div class="employees-breakdown">
                        @forelse($employees_classification as $item)
                            @php
                                $classificationLabel = $item->classification_info ? $item->classification_info->name : ($item->classification ?: 'N/A');
                                $classificationValue = $item->classification_info ? $item->classification_info->id : 'N/A';
                            @endphp
                            <a href="{{url('/employees?classification=' . $classificationValue)}}" class="employees-breakdown-row">
                                <span>{{$classificationLabel}}</span>
                                <strong>{{$item->total}}</strong>
                            </a>
                        @empty
                            <p class="employees-empty">No classification data found.</p>
                        @endforelse
                    </div>
                </div>
                <div class="employees-insight-card">
                    <div class="employees-card-header">
                        <div>
                            <span class="employees-kicker">Breakdown</span>
                            <h4>Gender</h4>
                        </div>
                    </div>
                    <div class="employees-breakdown is-chips">
                        @forelse($employees_gender as $item)
                            @php
                                $genderLabel = $item->gender ?: 'N/A';
                            @endphp
                            <a href="{{url('/employees?gender=' . ($item->gender ?: 'N/A'))}}" class="employees-gender-chip">
                                <span>{{$genderLabel}}</span>
                                <strong>{{$item->total}}</strong>
                            </a>
                        @empty
                            <p class="employees-empty">No gender data found.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="employees-filter-card">
                <div class="employees-card-header">
                    <div>
                        <span class="employees-kicker">Refine</span>
                        <h4>Find Employees</h4>
                    </div>
                    <a href="/employees" class="btn btn-light btn-sm">Reset</a>
                </div>
                <form method="get" onsubmit="show();" enctype="multipart/form-data">
                    <div class="employees-filter-grid">
                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" class="form-control" name="search" placeholder="Name, employee code, biometric code" value="{{$search}}">
                        </div>
                        <div class="form-group">
                            <label>Company</label>
                            <select data-placeholder="Select Company" class="form-control form-control-sm required js-example-basic-single" style="width:100%;" name="company">
                                <option value="">All Companies</option>
                                @foreach($companies as $comp)
                                    <option value="{{$comp->id}}" @if ($comp->id == $company) selected @endif>{{$comp->company_code}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Department</label>
                            <select data-placeholder="Select Department" class="form-control form-control-sm required js-example-basic-single" style="width:100%;" name="department">
                                <option value="">All Departments</option>
                                @foreach($departments as $dep)
                                    <option value="{{$dep->id}}" @if ($dep->id == $department) selected @endif>{{$dep->name}} - {{$dep->code}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select data-placeholder="Select Status" class="form-control form-control-sm required js-example-basic-single" style="width:100%;" name="status">
                                <option value="">-- Select Status --</option>
                                <option value="Active" @if ($status == 'Active') selected @endif>Active</option>
                                <option value="Inactive" @if ($status == 'Inactive') selected @endif>Inactive</option>
                                <option value="Resigned" @if ($status == 'Resigned') selected @endif>Resigned</option>
                                <option value="Terminated" @if ($status == 'Terminated') selected @endif>Terminated</option>
                                <option value="HBU" @if ($status == 'HBU') selected @endif>HBU</option>
                                <option value="Pending" @if ($status == 'Pending') selected @endif>Pending</option>
                            </select>
                        </div>
                        <div class="employees-filter-submit">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="employees-directory-card">
                <div class="employees-card-header">
                    <div>
                        <span class="employees-kicker">Directory</span>
                        <h4>{{$employees->total()}} employees found</h4>
                    </div>
                    <small>Page {{$employees->currentPage()}} of {{$employees->lastPage()}}</small>
                </div>
                <div class="employees-grid">
                    @forelse($employees as $employee)
                        @php
                            $employeeName = trim($employee->last_name.', '.$employee->first_name);
                            $companyCode = optional($employee->company)->company_code ?: '-';
                            $departmentName = optional($employee->department)->name ?: 'No department';
                            $classificationName = optional($employee->classification_info)->name ?: 'Unclassified';
                            $statusClass = $employee->status == 'Active' ? 'success' : ($employee->status == 'Pending' ? 'warning' : 'secondary');
                        @endphp
                        <div class="employee-card">
                            <div class="employee-card-top">
                                <img class="employee-avatar" src="{{URL::asset($employee->avatar)}}" onerror="this.src='{{URL::asset('/images/no_image.png')}}';">
                                <div class="employee-title">
                                    <h5>{{$employeeName}}</h5>
                                    <span>{{$companyCode}} · {{$departmentName}}</span>
                                </div>
                                <span class="badge badge-{{$statusClass}}">{{$employee->status}}</span>
                            </div>
                            <div class="employee-card-details">
                                <div>
                                    <small>Employee Code</small>
                                    <strong>{{$employee->employee_code ?: '-'}}</strong>
                                </div>
                                <div>
                                    <small>Biometric Code</small>
                                    <strong>{{$employee->employee_number ?: '-'}}</strong>
                                </div>
                                <div>
                                    <small>User ID</small>
                                    <strong>{{$employee->user_id ?: '-'}}</strong>
                                </div>
                                <div>
                                    <small>Classification</small>
                                    <strong>{{$classificationName}}</strong>
                                </div>
                            </div>
                            <div class="employee-supervisor">
                                <small>Immediate Supervisor</small>
                                @if($employee->immediate_sup_data)
                                    <div>
                                        <img src="{{URL::asset(optional($employee->immediate_sup_data->employee)->avatar)}}" onerror="this.src='{{URL::asset('/images/no_image.png')}}';">
                                        <span>{{$employee->immediate_sup_data->name}}</span>
                                    </div>
                                @else
                                    <span>No supervisor set</span>
                                @endif
                            </div>
                            <div class="employee-card-actions">
                                @if (checkUserPrivilege('employees_view',auth()->user()->id) == 'yes')
                                    <a href="/account-setting-hr/{{$employee->user_id}}" class="btn btn-outline-success btn-sm"><i class="ti-pencil"></i> View Profile</a>
                                @endif
                                @if (checkUserPrivilege('employees_availment',auth()->user()->id) == 'yes')
                                    <button type="button"
                                        class="btn btn-outline-info btn-sm"
                                        title="Send Email Notification"
                                        data-toggle="modal"
                                        data-target="#sendEmailModal"
                                        data-id="{{ $employee->id }}"
                                        data-name="{{ $employee->first_name }}"
                                        data-email="{{ $employee->email ?? ($employee->user->email ?? '') }}">
                                        <i class="fa fa-envelope"></i> HMO
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="employees-empty-state">
                            <i class="fa fa-search"></i>
                            <h4>No employees found</h4>
                            <p>Try another search term or reset the filters.</p>
                            <a href="/employees" class="btn btn-primary btn-sm">Reset Filters</a>
                        </div>
                    @endforelse
                </div>
                <div class="employees-pagination">
                    {{ $employees->appends(request()->only(['department', 'company', 'status','search', 'classification', 'gender']))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .employees-page {
        display: grid;
        gap: 18px;
    }
    .employees-hero,
    .employees-filter-card,
    .employees-directory-card,
    .employees-insight-card,
    .employees-metric-card {
        border: 1px solid #e4ebf3;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(31, 45, 61, .06);
    }
    .employees-hero {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        padding: 24px;
        border: 0;
        color: #fff;
        background: linear-gradient(120deg, #184a81, #248afd);
    }
    .employees-hero h2 {
        margin: 6px 0;
        color: #fff;
        font-size: 1.9rem;
        font-weight: 800;
        line-height: 1.2;
    }
    .employees-hero p {
        margin: 0;
        color: rgba(255,255,255,.84);
        font-weight: 600;
    }
    .employees-kicker {
        display: block;
        color: #248afd;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: 0;
        text-transform: uppercase;
    }
    .employees-hero .employees-kicker {
        color: rgba(255,255,255,.78);
    }
    .employees-filter-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 14px;
    }
    .employees-filter-tags span,
    .employees-gender-chip,
    .employee-card-details > div {
        border: 1px solid #dbe7f5;
        border-radius: 8px;
        background: #f8fbff;
    }
    .employees-filter-tags span {
        padding: .35rem .6rem;
        border-color: rgba(255,255,255,.32);
        background: rgba(255,255,255,.14);
        color: #fff;
        font-size: .78rem;
        font-weight: 700;
    }
    .employees-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
        max-width: 520px;
    }
    .employees-actions .btn {
        background: #fff;
    }
    .employees-actions .employees-new-btn {
        color: #000 !important;
    }
    .employees-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }
    .employees-metric-card {
        position: relative;
        min-height: 132px;
        padding: 18px;
        overflow: hidden;
    }
    .employees-metric-card small,
    .employee-card-details small,
    .employee-supervisor small {
        display: block;
        color: #7b8794;
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .employees-metric-card strong {
        display: block;
        margin-top: 8px;
        color: #172033;
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }
    .employees-metric-card p {
        margin: 10px 0 0;
        color: #667085;
        font-weight: 600;
    }
    .employees-metric-card.is-active {
        border-color: #b9defa;
        background: #f4faff;
    }
    .employees-metric-icon {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #248afd;
        background: #eaf4ff;
    }
    .employees-insights {
        display: grid;
        grid-template-columns: minmax(0, 1.3fr) minmax(280px, .7fr);
        gap: 14px;
    }
    .employees-insight-card,
    .employees-filter-card,
    .employees-directory-card {
        padding: 18px;
    }
    .employees-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }
    .employees-card-header h4 {
        margin: 3px 0 0;
        color: #172033;
        font-size: 1.1rem;
        font-weight: 800;
    }
    .employees-breakdown {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
    .employees-breakdown-row,
    .employees-gender-chip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        min-height: 42px;
        padding: .65rem .75rem;
        color: #334155;
        font-weight: 700;
        text-decoration: none;
    }
    .employees-breakdown-row {
        border-bottom: 1px solid #edf2f7;
    }
    .employees-breakdown-row:hover,
    .employees-gender-chip:hover {
        color: #0f62b5;
        text-decoration: none;
        background: #eef7ff;
    }
    .employees-breakdown-row strong,
    .employees-gender-chip strong {
        color: #248afd;
        font-size: 1rem;
    }
    .employees-breakdown.is-chips {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .employees-filter-grid {
        display: grid;
        grid-template-columns: minmax(240px, 1.4fr) repeat(3, minmax(170px, 1fr)) minmax(130px, .6fr);
        gap: 12px;
        align-items: end;
    }
    .employees-filter-grid .form-group {
        margin-bottom: 0;
    }
    .employees-filter-grid label {
        color: #475467;
        font-size: .74rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .employees-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }
    .employee-card {
        display: grid;
        gap: 14px;
        padding: 16px;
        border: 1px solid #e3ebf5;
        border-radius: 8px;
        background: #fff;
    }
    .employee-card-top {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .employee-avatar {
        flex: 0 0 52px;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 6px 18px rgba(31, 45, 61, .16);
    }
    .employee-title {
        min-width: 0;
        flex: 1;
    }
    .employee-title h5 {
        margin: 0 0 4px;
        color: #172033;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.25;
        word-break: break-word;
    }
    .employee-title span,
    .employee-supervisor span {
        color: #667085;
        font-weight: 600;
        word-break: break-word;
    }
    .employee-card-details {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
    .employee-card-details > div {
        min-height: 62px;
        padding: .65rem .75rem;
    }
    .employee-card-details strong {
        display: block;
        margin-top: 4px;
        color: #263238;
        font-weight: 800;
        line-height: 1.25;
        word-break: break-word;
    }
    .employee-supervisor {
        padding-top: 2px;
    }
    .employee-supervisor div {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 6px;
    }
    .employee-supervisor img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
    }
    .employee-card-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding-top: 2px;
    }
    .employees-pagination {
        margin-top: 18px;
    }
    .employees-empty,
    .employees-empty-state {
        color: #667085;
        font-weight: 600;
    }
    .employees-empty-state {
        grid-column: 1 / -1;
        padding: 42px 18px;
        text-align: center;
        border: 1px dashed #cbd8e6;
        border-radius: 8px;
        background: #fbfdff;
    }
    .employees-empty-state i {
        color: #248afd;
        font-size: 2rem;
        margin-bottom: 12px;
    }
    @media (max-width: 1199.98px) {
        .employees-metrics,
        .employees-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .employees-filter-submit {
            grid-column: span 2;
        }
    }
    @media (max-width: 991.98px) {
        .employees-hero,
        .employees-card-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .employees-actions {
            justify-content: flex-start;
            max-width: none;
        }
        .employees-insights,
        .employees-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 767.98px) {
        .employees-metrics,
        .employees-filter-grid,
        .employees-breakdown,
        .employees-breakdown.is-chips,
        .employee-card-details {
            grid-template-columns: 1fr;
        }
        .employees-filter-submit {
            grid-column: auto;
        }
        .employees-hero {
            padding: 20px;
        }
        .employee-card-top {
            align-items: flex-start;
        }
    }
</style>

<div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="sendEmailForm" action="{{ route('send.hmo') }}" method="POST" onsubmit='show()'>
            @csrf
            <input type="hidden" name="employee_id" id="employee_id">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="sendEmailModalLabel">Send HMO Email Notification</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="availment_date">Availment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="availment_date" id="availment_date" required>
                    </div>

                    <div class="form-group">
                        <label for="recipient_email">Recipient Email</label>
                        <input type="email" class="form-control" id="recipient_email" readonly>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info" id="sendEmailBtn">Send Email</button>
                </div>
            </div>
        </form>
    </div>
</div>


@include('employees.updateEmployeeRate')
@include('employees.updateEmployeeSalaries')
@include('employees.uploadEmployee')
@include('employees.newEmployee')
{{-- @include('employees.capture_image') --}}
<script>
    function show() {
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while the request is being processed.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
    $(document).ready(function () {
        $('#sendEmailModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var employeeId = button.data('id');
            var email = button.data('email');

            var modal = $(this);
            modal.find('#employee_id').val(employeeId);
            modal.find('#recipient_email').val(email ? email : 'No email available');
        }); 
    });
</script>

@endsection
@section('footer')
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/js/inputmask.js') }}"></script>
@endsection
