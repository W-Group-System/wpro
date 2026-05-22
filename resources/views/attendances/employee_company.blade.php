@extends('layouts.header')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class='row'>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Attendances</h4>
                        <p class="card-description">
                        <form method="get" onsubmit="show();" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select data-placeholder="Select Company" class="form-control form-control-sm required js-example-basic-single" style="width:100%;" name="company" id="companySelect" required>
                                            <option value="">-- Select Company --</option>
                                            @foreach($companies as $comp)
                                            <option value="{{$comp->id}}" @if ($comp->id == $company) selected @endif>{{$comp->company_code}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="fromDate" name="from" value="{{$from_date}}" placeholder="From Date" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="toDate" name="to" value="{{$to_date}}" placeholder="To Date" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ url('/biometrics-per-company') }}" class="btn btn-warning">Reset Filter</a>
                                </div>
                            </div>
                        </form>

                            <!-- <form method='get' onsubmit='show();' enctype="multipart/form-data">
                                <div class="row">
                                    <div class='col-md-4'>
                                        <div class="form-group">
                                            <select data-placeholder="Select Company" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='company' id='companySelect' onchange="checkLogDate();" required>
                                                <option value="">-- Select Company --</option>
                                                @foreach($companies as $comp)
                                                <option value="{{$comp->id}}" @if ($comp->id == $company) selected @endif>{{$comp->company_name}} - {{$comp->company_code}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class='col-md-2'>
                                        <div class="form-group">
                                            <input type="date" value='{{$from_date}}' class="form-control" name="from" id='fromDate' max='{{date('Y-m-d')}}' onchange="checkLogDate();" required />
                                        </div>
                                    </div>
                                    <div class='col-md-2'>
                                        <div class="form-group">
                                            <input type="date" value='{{$to_date}}' class="form-control" name="to" id='toDate' max='{{date('Y-m-d')}}' onchange="checkLogDate();" required />
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <button type="submit" class="btn btn-primary" id="btnFilter">Filter</button>
                                        <a href="{{ url('/biometrics-per-company') }}" class="btn btn-warning">Reset Filter</a>
                                    </div>
                                </div>
                            </form> -->
                        </p>
                        <form method="POST" action="{{ route('attendance.store', ['company' => $company, 'from' => $from_date, 'to' => $to_date, 'department' => $department, 'location' => $location, 'employee' => $employee_filter]) }}" id="attendanceForm" onsubmit="return showConfirmation()" enctype="multipart/form-data">
                            @csrf
                            @if($date_range)
                                @php
                                    $remainingEmployees = $posting_summary['total_employees'] - $posting_summary['posted_employees'];
                                @endphp
                                <div class="timekeeping-posting-board">
                                    <div class="posting-board-header">
                                        <div>
                                            <span class="posting-kicker">Posting Control</span>
                                            <h4>Employee Posting Lock</h4>
                                            <p>Post selected employees in batches. Completed employees are locked and available for pay-reg generation.</p>
                                        </div>
                                        <div class="posting-actions">
                                            <a href="attendance-per-company-export?company={{$company}}&from={{$from_date}}&to={{$to_date}}" class="btn btn-info btn-sm" id="exportButton">
                                                Export {{ $posting_summary['total_employees'] }} Employees
                                            </a>
                                            <button type="submit" class="btn btn-success btn-sm" id="postButton" disabled>
                                                Post Selected
                                            </button>
                                        </div>
                                    </div>
                                    <div class="posting-metrics">
                                        <div>
                                            <small>Total Employees</small>
                                            <strong>{{ $posting_summary['total_employees'] }}</strong>
                                        </div>
                                        <div>
                                            <small>Posted</small>
                                            <strong>{{ $posting_summary['posted_employees'] }}</strong>
                                        </div>
                                        <div>
                                            <small>Remaining</small>
                                            <strong>{{ $remainingEmployees }}</strong>
                                        </div>
                                        <div>
                                            <small>Completion</small>
                                            <strong>{{ $posting_summary['percent'] }}%</strong>
                                        </div>
                                    </div>
                                    <div class="posting-progress">
                                        <div style="width: {{ $posting_summary['percent'] }}%;"></div>
                                    </div>
                                    <div class="posting-status-line">
                                        @if($posting_summary['percent'] >= 100)
                                            <span class="posting-ready"><i class="fa fa-check-circle"></i> 100% posted. This cutoff is ready for pay-reg generation.</span>
                                        @else
                                            <span><i class="fa fa-lock"></i> Pay-reg readiness unlocks when all employees reach 100% posted.</span>
                                        @endif
                                        <span id="selectedPostingCount">0 selected</span>
                                    </div>
                                    <div class="posting-search-wrap">
                                        <i class="fa fa-search"></i>
                                        <input type="text" class="form-control" id="postingEmployeeSearch" placeholder="Search employee, code, or department">
                                    </div>
                                    <div class="posting-employee-grid">
                                        @foreach($posting_summary['employees'] as $postingEmployee)
                                            <div class="posting-employee-card @if($postingEmployee['is_posted']) is-locked @endif">
                                                <div class="posting-employee-check">
                                                    <input type="checkbox"
                                                        name="post_employee_codes[]"
                                                        value="{{ $postingEmployee['employee_code'] }}"
                                                        class="posting-checkbox"
                                                        @if($postingEmployee['is_posted']) disabled @endif>
                                                    <span>
                                                        @if($postingEmployee['is_posted'])
                                                            <i class="fa fa-lock"></i>
                                                        @else
                                                            <i class="fa fa-unlock"></i>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="posting-employee-info">
                                                    <strong>{{ $postingEmployee['name'] }}</strong>
                                                    <small>{{ $postingEmployee['employee_code'] }} · {{ $postingEmployee['department'] }}</small>
                                                </div>
                                                <div class="posting-employee-percent">
                                                    <strong>{{ $postingEmployee['percent'] }}%</strong>
                                                    <small>{{ $postingEmployee['posted_days'] }}/{{ $postingEmployee['total_days'] }} days</small>
                                                    @if($postingEmployee['is_posted'])
                                                        <button type="button"
                                                            class="btn btn-outline-danger btn-xs posting-unpost-btn"
                                                            data-code="{{ $postingEmployee['employee_code'] }}"
                                                            data-name="{{ $postingEmployee['name'] }}">
                                                            Unpost
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($remainingEmployees > 0)
                                        <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="selectAllUnlocked">
                                            Select all remaining employees
                                        </button>
                                    @endif
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table border="1" class="table table-hover table-bordered employee_attendance" id='employee_attendance'>
                                    <thead>
                                        {{-- <tr>
                                            <td colspan='5'>{{$emp->emp_code}} - {{$emp->first_name}} {{$emp->last_name}}</td>
                                        </tr> --}}
                                        <tr>
                                            <th>Company</th>
                                            <th>Employee #</th>
                                            <th>Name</th>
                                            <th>Log Date</th>
                                            <th>Shift</th>
                                            <th>IN</th>
                                            <th>OUT</th>
                                            <th>ABS</th>
                                            <th>LV W/ PAY</th>
                                            <th>REG HRS</th>
                                            <th>LATE (min)</th>
                                            <th>Undertime (min)</th>
                                            <th>REG OT</th>
                                            <th>REG ND</th>
                                            <th>REG OT ND</th>
                                            <th>RST OT</th>
                                            <th>RST OT > 8</th>
                                            <th>RST ND</th>
                                            <th>RST ND > 8</th>
                                            <th>LH OT</th>
                                            <th>LH OT > 8</th>
                                            <th>LH ND</th>
                                            <th>LH ND > 8</th>
                                            <th>SH OT</th>
                                            <th>SH OT > 8</th>
                                            <th>SH ND</th>
                                            <th>SH ND > 8</th>
                                            <th>RST LH OT</th>
                                            <th>RST LH OT > 8</th>
                                            <th>RST LH ND</th>
                                            <th>RST LH ND > 8</th>
                                            <th>RST SH OT</th>
                                            <th>RST SH OT > 8</th>
                                            <th>RST SH ND</th>
                                            <th>RST SH ND > 8</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>

                                    
                                    <tbody id="attendanceRows">
                                        @if($date_range)
                                            <tr id="attendanceLoadingRow">
                                                <td colspan="36" class="text-center text-muted">Select employees to preview attendance rows.</td>
                                            </tr>
                                        @else
                                            @include('attendances.partials.detailed_rows', ['attendance_groups' => $attendance_groups, 'show_company_column' => true])
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('attendance.unpost') }}" id="unpostAttendanceForm" style="display: none;">
                            @csrf
                            <input type="hidden" name="company" value="{{ $company }}">
                            <input type="hidden" name="from" value="{{ $from_date }}">
                            <input type="hidden" name="to" value="{{ $to_date }}">
                            <input type="hidden" name="department" value="{{ $department }}">
                            <input type="hidden" name="location" value="{{ $location }}">
                            <input type="hidden" name="employee_code" id="unpostEmployeeCode">
                            @foreach((array) $employee_filter as $filteredEmployee)
                                <input type="hidden" name="employee[]" value="{{ $filteredEmployee }}">
                            @endforeach
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timekeeping-posting-board {
        margin: 12px 0 18px;
        padding: 18px;
        border: 1px solid #dfe8f3;
        border-radius: 8px;
        background: #fbfdff;
        box-shadow: 0 8px 24px rgba(31, 45, 61, .06);
    }
    .posting-board-header,
    .posting-status-line,
    .posting-employee-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }
    .posting-kicker {
        display: block;
        color: #248afd;
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .posting-board-header h4 {
        margin: 4px 0;
        color: #172033;
        font-weight: 800;
    }
    .posting-board-header p,
    .posting-status-line,
    .posting-employee-info small,
    .posting-employee-percent small {
        margin: 0;
        color: #667085;
        font-weight: 600;
    }
    .posting-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }
    .posting-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-top: 16px;
    }
    .posting-metrics > div {
        min-height: 76px;
        padding: 12px;
        border: 1px solid #e4ebf3;
        border-radius: 8px;
        background: #fff;
    }
    .posting-metrics small {
        display: block;
        color: #7b8794;
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .posting-metrics strong {
        display: block;
        margin-top: 6px;
        color: #172033;
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1;
    }
    .posting-progress {
        height: 10px;
        margin: 14px 0 10px;
        overflow: hidden;
        border-radius: 999px;
        background: #e8eef7;
    }
    .posting-progress > div {
        height: 100%;
        border-radius: 999px;
        background: #28a745;
    }
    .posting-ready {
        color: #16833a;
        font-weight: 800;
    }
    .posting-employee-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        max-height: 360px;
        margin-top: 14px;
        overflow: auto;
        padding-right: 4px;
    }
    .posting-search-wrap {
        position: relative;
        margin-top: 14px;
    }
    .posting-search-wrap i {
        position: absolute;
        top: 50%;
        left: 12px;
        color: #7b8794;
        transform: translateY(-50%);
    }
    .posting-search-wrap input {
        padding-left: 36px;
    }
    .posting-employee-card {
        min-height: 78px;
        padding: 12px;
        border: 1px solid #dfe8f3;
        border-radius: 8px;
        background: #fff;
        cursor: pointer;
    }
    .posting-employee-card:hover {
        border-color: #9bc9ff;
        background: #f4faff;
    }
    .posting-employee-card.is-locked {
        cursor: default;
        background: #f3f6f9;
    }
    .posting-employee-check {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .posting-employee-check span {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #248afd;
        background: #eaf4ff;
    }
    .posting-employee-card.is-locked .posting-employee-check span {
        color: #667085;
        background: #e6ebf1;
    }
    .posting-employee-info {
        min-width: 0;
        flex: 1;
    }
    .posting-employee-info strong,
    .posting-employee-info small {
        display: block;
        word-break: break-word;
    }
    .posting-employee-percent {
        text-align: right;
    }
    .posting-employee-percent strong {
        display: block;
        color: #172033;
        font-weight: 800;
    }
    .posting-unpost-btn {
        margin-top: 8px;
        padding: 3px 10px;
        font-size: .72rem;
        font-weight: 800;
    }
    @media (max-width: 1199.98px) {
        .posting-employee-grid,
        .posting-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 767.98px) {
        .posting-board-header,
        .posting-status-line {
            align-items: flex-start;
            flex-direction: column;
        }
        .posting-actions {
            justify-content: flex-start;
        }
        .posting-employee-grid,
        .posting-metrics {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    function night_difference($start_work,$end_work) {
        $start_night = mktime('22','00','00',date('m',$start_work),date('d',$start_work),date('Y',$start_work));
        $end_night = mktime('06','00','00',date('m',$start_work),date('d',$start_work) + 1,date('Y',$start_work));

        if($start_work >= $start_night && $start_work <= $end_night) { if($end_work>= $end_night)
            {
                return ($end_night - $start_work) / 3600;
            } else {
                return ($end_work - $start_work) / 3600;
            }
        } elseif($end_work >= $start_night && $end_work <= $end_night) { 
            if($start_work <=$start_night) { 
                return ($end_work - $start_night) / 3600; 
            } else { 
                return ($end_work - $start_work) / 3600; 
            } 
        } else { 
            if($start_work < $start_night && $end_work> $end_night)
        {
            return ($end_night - $start_night) / 3600;
        }
            return 0;
        }
    }
    function roundDownToNearestHalf($number) {
            return floor($number * 2) / 2;
        }
@endphp

<!-- Include SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.12.5/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Include SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.12.5/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>

    // $(document).ready(function() {
    //     var fromDateInput = document.getElementById('fromDate');
    //     var toDateInput = document.getElementById('toDate');

    //     // Initialize Flatpickr on date inputs
    //     var fromFlatpickr = flatpickr(fromDateInput, {
    //         dateFormat: "Y-m-d",
    //         disable: [], // Initialize with an empty array of disabled dates
    //     });

    //     var toFlatpickr = flatpickr(toDateInput, {
    //         dateFormat: "Y-m-d",
    //         disable: [], // Initialize with an empty array of disabled dates
    //     });

    //     // Event listener for company select change
    //     $("#companySelect").on('change', function() {
    //         var selectedCompanyId = $(this).val();
    //         if (selectedCompanyId) {
    //             // Enable date inputs
    //             fromDateInput.disabled = false;
    //             toDateInput.disabled = false;

    //             fromDateInput.style.backgroundColor = 'transparent';
    //             toDateInput.style.backgroundColor = 'transparent';

    //             // Fetch disabled dates via AJAX
    //             $.ajax({
    //                 url: "{{ url('/fetch-disabled-dates') }}/" + selectedCompanyId,
    //                 type: 'GET',
    //                 success: function(response) {
    //                     var logDates = response.log_dates;

    //                     // Update Flatpickr options to disable fetched dates
    //                     fromFlatpickr.set('disable', logDates);
    //                     toFlatpickr.set('disable', logDates);
    //                 },
    //                 error: function(xhr, status, error) {
    //                     console.error('Failed to fetch disabled dates: ' + status + ' - ' + error);
    //                 }
    //             });
    //         } else {
    //             // Disable date inputs if no company is selected
    //             fromDateInput.disabled = true;
    //             toDateInput.disabled = true;

                
    //             // Reset Flatpickr options if no company is selected
    //             fromFlatpickr.set('disable', []);
    //             toFlatpickr.set('disable', []);
    //         }
    //     });
    //     // Initialize with the selected company (if any)
    //     var selectedCompanyId = $("#companySelect").val();
    //     if (selectedCompanyId) {
    //         // Trigger change event to fetch and disable dates for initially selected company
    //         $("#companySelect").trigger('change');
    //     }
    // });
  
    $(document).ready(function() {
        var fromDateInput = document.getElementById('fromDate');
        var toDateInput = document.getElementById('toDate');
        var companyMinDate = null;

        // Initialize Flatpickr on date inputs
        var fromFlatpickr = flatpickr(fromDateInput, {
            dateFormat: "Y-m-d",
            disable: [] // Initialize with an empty array of disabled dates
        });

        var toFlatpickr = flatpickr(toDateInput, {
            dateFormat: "Y-m-d",
            disable: [] // Initialize with an empty array of disabled dates
        });

        var initialCompanyId = $("#companySelect").val();

        // Event listener for company select change
        $("#companySelect").on('change', function() {
            var selectedCompanyId = $(this).val();
            if (selectedCompanyId) {
                // Enable date inputs
                fromDateInput.disabled = false;
                toDateInput.disabled = false;

                // Set background color to transparent
                fromDateInput.style.backgroundColor = 'transparent';
                toDateInput.style.backgroundColor = 'transparent';

                // Fetch dates that are locked because pay-reg was already generated.
                $.ajax({
                    url: "{{ url('/fetch-disabled-dates') }}/" + selectedCompanyId,
                    type: 'GET',
                    success: function(response) {
                        var logDates = response.log_dates;
                        companyMinDate = response.next_available_date || null;

                        // Update Flatpickr options to allow only dates after generated pay-reg.
                        fromFlatpickr.set('disable', logDates);
                        toFlatpickr.set('disable', logDates);
                        fromFlatpickr.set('minDate', companyMinDate);
                        toFlatpickr.set('minDate', companyMinDate);

                        if (companyMinDate && (!fromDateInput.value || fromDateInput.value < companyMinDate)) {
                            fromFlatpickr.setDate(companyMinDate, true);
                        }

                        if (companyMinDate && (!toDateInput.value || toDateInput.value < companyMinDate)) {
                            toFlatpickr.setDate(companyMinDate, true);
                        }

                        // Check if any log_date matches the fromDate and toDate
                        checkButtonStatus(logDates, fromDateInput.value, toDateInput.value);
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to fetch disabled dates: ' + status + ' - ' + error);
                    }
                });
            } else {
                // Disable date inputs if no company is selected
                fromDateInput.disabled = true;
                toDateInput.disabled = true;

                // Reset Flatpickr options if no company is selected
                fromFlatpickr.set('disable', []);
                toFlatpickr.set('disable', []);
                fromFlatpickr.set('minDate', null);
                toFlatpickr.set('minDate', null);
                companyMinDate = null;

                // Enable buttons if no company is selected
                enableButtons();
            }
        });

        // Event listener for date changes
        fromFlatpickr.config.onChange.push(function(selectedDates, dateStr, instance) {
            var toMinDate = dateStr || companyMinDate;
            toFlatpickr.set('minDate', toMinDate);
            if (toDateInput.value && dateStr && toDateInput.value < dateStr) {
                toFlatpickr.setDate(dateStr, true);
            }
            checkButtonStatus(instance.config.disable, dateStr, toDateInput.value);
        });

        toFlatpickr.config.onChange.push(function(selectedDates, dateStr, instance) {
            checkButtonStatus(instance.config.disable, fromDateInput.value, dateStr);
        });

        // Function to check and disable/enable buttons based on conditions
        function checkButtonStatus(logDates, fromDate, toDate) {
            updatePostingButton();
        }

        // Function to disable buttons
        function disableButtons() {
            updatePostingButton();
        }

        // Function to enable buttons
        function enableButtons() {
            updatePostingButton();
        }

        // Initialize with the selected company (if any)
        var selectedCompanyId = $("#companySelect").val();
        if (selectedCompanyId) {
            // Trigger change event to fetch and disable dates for initially selected company
            $("#companySelect").trigger('change');
        }

        function updatePostingButton() {
            var selectedCount = $('.posting-checkbox:checked').length;
            $('#selectedPostingCount').text(selectedCount + (selectedCount === 1 ? ' selected' : ' selected'));
            $('#postButton').prop('disabled', selectedCount === 0);
            if (typeof applyPostingTableFilter === 'function') {
                applyPostingTableFilter();
            }
        }

        $(document).on('change', '.posting-checkbox', updatePostingButton);
        $(document).on('click', '.posting-employee-card', function(e) {
            if ($(e.target).is('input, button, a') || $(e.target).closest('button, a').length) {
                return;
            }

            var checkbox = $(this).find('.posting-checkbox:not(:disabled)');
            if (checkbox.length) {
                checkbox.prop('checked', !checkbox.prop('checked'));
                updatePostingButton();
            }
        });

        $(document).on('click', '.posting-unpost-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var employeeCode = $(this).data('code');
            var employeeName = $(this).data('name');

            Swal.fire({
                title: 'Unpost employee?',
                text: 'This will remove posted attendance for ' + employeeName + ' in the selected date range.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, unpost'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("loader").style.display = "flex";
                    $('#unpostEmployeeCode').val(employeeCode);
                    document.getElementById('unpostAttendanceForm').submit();
                }
            });
        });

        $('#selectAllUnlocked').on('click', function() {
            $('.posting-employee-card:visible .posting-checkbox:not(:disabled)').prop('checked', true);
            updatePostingButton();
        });

        $('#postingEmployeeSearch').on('input', function() {
            var query = $(this).val().toLowerCase();
            $('.posting-employee-card').each(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(query) !== -1);
            });
        });

        updatePostingButton();
    });


    function get_min(value) {
        document.getElementById("to").min = value;
    }

    function showConfirmation() {
        var selectedCount = $('.posting-checkbox:checked').length;
        if (selectedCount === 0) {
            Swal.fire('No employees selected', 'Select at least one unlocked employee to post.', 'warning');
            return false;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "Post timekeeping for " + selectedCount + " selected employee(s)?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, submit it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("loader").style.display = "flex";
                document.getElementById('attendanceForm').submit();
            }
        });

        return false; // Prevent default form submission
    }

    // function checkLogDate() {
    //     var companyId = document.getElementById('companySelect').value;
    //     var fromDateInput = document.getElementById('fromDate');
    //     var toDateInput = document.getElementById('toDate');
    //     var btnFilter = document.getElementById('btnFilter');
    //     var fromDate = fromDateInput.value;
    //     var toDate = toDateInput.value;

    //     if (companyId && fromDate && toDate) {
    //         $.ajax({
    //             url: "{{ url('/check-log-date') }}/" + companyId,
    //             type: 'GET',
    //             data: {
    //                 from: fromDate,
    //                 to: toDate
    //             },
    //             success: function(response) {
    //                 var logDates = response.logDates;
    //                 var fromDateValue = new Date(fromDate);
    //                 var toDateValue = new Date(toDate);

    //                 var disableFilterButton = false;
    //                 if (logDates.length > 0) {
    //                     logDates.forEach(function(logDate) {
    //                         var logDateValue = new Date(logDate);

    //                         if (logDateValue >= fromDateValue && logDateValue <= toDateValue) {
    //                             // fromDateInput.value = ''; // Clear input value
    //                             // toDateInput.value = ''; // Clear input value
    //                             fromDateInput.disabled = false;
    //                             toDateInput.disabled = false;
    //                             disableFilterButton = true;
    //                         }
    //                     });
    //                 }
    //                     btnFilter.disabled = disableFilterButton;
    //             },
    //             error: function(xhr, status, error) {
    //                 console.error('AJAX Error: ' + status + ' - ' + error);
    //             }
    //         });
    //     } else {
    //         fromDateInput.disabled = false;
    //         toDateInput.disabled = false;
    //     }
    // }


        </script>
        @endsection

@section('js')
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
        window.hasAttendanceRowsToLoad = @json((bool) $date_range);
        window.postingSelectedEmployeeCodes = [];
        window.postingFilterActive = $('.timekeeping-posting-board').length > 0;
        window.attendanceRowsLoadedFor = '';
        window.attendanceRowsLoading = false;

        if ($.fn.dataTable && !window.postingTableFilterRegistered) {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'employee_attendance') {
                    return true;
                }

                if (!window.postingFilterActive) {
                    return true;
                }

                if (!window.postingSelectedEmployeeCodes.length) {
                    return false;
                }

                var rowNode = settings.aoData[dataIndex].nTr;
                var employeeCode = rowNode ? String(rowNode.getAttribute('data-employee-code')).trim() : '';
                return window.postingSelectedEmployeeCodes.indexOf(employeeCode) !== -1;
            });
            window.postingTableFilterRegistered = true;
        }

        if (window.hasAttendanceRowsToLoad && !window.postingFilterActive) {
            loadAttendanceRows(0);
        } else {
            initializeAttendanceTable();
        }
    });

    function initializeAttendanceTable() {
        if ($.fn.DataTable.isDataTable('.employee_attendance')) {
            return;
        }

        window.attendanceDataTable = new DataTable('.employee_attendance', {
            fixedColumns: {
                leftColumns: 1,
            },
            paginate: false,
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
        window.attendanceDataTable.on('draw', function() {
            filterAttendanceRowsBySelection(window.postingSelectedEmployeeCodes || []);
        });
        applyPostingTableFilter();
    }

    function selectedPostingCodes() {
        return $('.posting-checkbox:checked').map(function() {
            return String($(this).val()).trim();
        }).get();
    }

    function resetAttendanceTableForReload(message) {
        if ($.fn.DataTable.isDataTable('.employee_attendance')) {
            $('.employee_attendance').DataTable().destroy();
        }

        window.attendanceDataTable = null;
        $('#attendanceRows').html('<tr id="attendanceLoadingRow"><td colspan="36" class="text-center text-muted">' + message + '</td></tr>');
    }

    function loadAttendanceRowsForSelection() {
        var selectedCodes = selectedPostingCodes();
        var selectedKey = selectedCodes.slice().sort().join('|');

        if (!window.hasAttendanceRowsToLoad || !window.postingFilterActive) {
            return;
        }

        if (!selectedCodes.length) {
            window.attendanceRowsLoadedFor = '';
            resetAttendanceTableForReload('Select employees to preview attendance rows.');
            return;
        }

        if (window.attendanceRowsLoadedFor === selectedKey || window.attendanceRowsLoading) {
            return;
        }

        window.attendanceRowsLoadedFor = selectedKey;
        resetAttendanceTableForReload('Loading selected attendance rows...');
        loadAttendanceRows(0, selectedCodes);
    }

    function loadAttendanceRows(offset, selectedEmployeeCodes) {
        window.attendanceRowsLoading = true;

        $.ajax({
            url: "{{ url('biometrics-per-company/rows') }}",
            type: 'GET',
            data: {
                company: @json($company),
                department: @json($department),
                location: @json($location),
                employee: selectedEmployeeCodes || @json($employee_filter),
                from: @json($from_date),
                to: @json($to_date),
                offset: offset,
                limit: 10
            },
            success: function(response) {
                $('#attendanceLoadingRow').remove();
                $('#attendanceRows').append(response.html);

                if (response.has_more) {
                    loadAttendanceRows(response.next_offset, selectedEmployeeCodes);
                    return;
                }

                window.attendanceRowsLoading = false;
                initializeAttendanceTable();
                applyPostingTableFilter();
            },
            error: function() {
                window.attendanceRowsLoading = false;
                $('#attendanceLoadingRow').remove();
                $('#attendanceRows').append('<tr><td colspan="36">Unable to load attendance rows. Please try again.</td></tr>');
            }
        });
    }

    function applyPostingTableFilter() {
        var selectedCodes = selectedPostingCodes();

        window.postingSelectedEmployeeCodes = selectedCodes;

        loadAttendanceRowsForSelection();

        if (!window.postingFilterActive) {
            return;
        }

        if (window.attendanceDataTable) {
            window.attendanceDataTable.draw();
            filterAttendanceRowsBySelection(selectedCodes);
            return;
        }

        filterAttendanceRowsBySelection(selectedCodes);
    }

    function filterAttendanceRowsBySelection(selectedCodes) {
        $('#attendanceRows tr[data-employee-code]').each(function() {
            var employeeCode = String($(this).attr('data-employee-code')).trim();
            var shouldShow = selectedCodes.length > 0 && selectedCodes.indexOf(employeeCode) !== -1;
            $(this).toggle(shouldShow);
        });
    }
</script>
@endsection
