@extends('layouts.header')

@section('css_header')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">
<style>
    .leave-report-filter .select2-container {
        width: 100% !important;
    }
    .select2-search--dropdown {
        display: block !important;
        padding: 8px !important;
    }
    .select2-search--dropdown .select2-search__field {
        width: 100% !important;
        min-height: 36px;
        border: 1px solid #d6dee8 !important;
        border-radius: 6px;
        padding: .45rem .65rem;
    }
</style>
@endsection

@section('content')
	<div class="main-panel">
		<div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Leave Report (as of {{ date('M Y') }})</h4>
                            {{-- <p class="card-description">
                                <button type="button" class="btn btn-outline-success btn-icon-text" data-toggle="modal"
                                    data-target="#new">
                                    <i class="ti-plus btn-icon-prepend"></i>
                                    New Leave Credit
                                </button>
                            </p> --}}

                            <div class="leave-report-filter mb-4">
                                <label for="employeeFilter" class="font-weight-bold mb-2">Filter Employee</label>
                                <select id="employeeFilter" class="form-control" style="width: 100%;"></select>
                                <small class="text-muted d-block mt-2">Type at least 2 letters to search employees, then select one to show leave records.</small>
                            </div>
    
                            <div class="table-responsive" id="leaveReportResult" style="display: none;">
                                <table class="table table-hover table-bordered " id="leaveReportTable">
                                    <thead>
                                        <tr>
                                            <th>Employee ID</th>
                                            <th>Employee Name</th>
                                            <th>Leave Type</th>
                                            <th>Leave Entitlement</th>
                                            <th>Used Leave</th>
                                            <th>Leave Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script>
    $(document).ready(function() {
        var leaveReportTable = $("#leaveReportTable").DataTable({
            paginate: false,
            searching: false,
            sDom: 'Brtip',
            processing: true,
            data: [],
            columns: [
                { data: 'employee_id' },
                { data: 'name' },
                { data: 'leave_type' },
                { data: 'leave_entitlement' },
                { data: 'used_leave' },
                {
                    data: 'balance'
                }
            ],
            buttons: [
                {
                    extend: 'copy',
                    title: 'Leave Report'
                },
                {
                    extend: 'excel',
                    title: 'Leave Report', // Sets the Excel title
                    filename: 'Leave Report'// Formats filename
                }
            ],
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }],
            language: {
                emptyTable: 'Select an employee first to show leave records.',
                processing: 'Loading leave records...'
            },
            order: []
        });

        $('#employeeFilter').select2({
            placeholder: 'Search employee name or code',
            allowClear: true,
            minimumResultsForSearch: 0,
            minimumInputLength: 2,
            ajax: {
                url: "{{ url('leave_report/employees') }}",
                dataType: 'json',
                delay: 350,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(data) {
                    return data;
                },
                cache: true
            },
            language: {
                inputTooShort: function() {
                    return 'Type at least 2 letters to search employees.';
                },
                noResults: function() {
                    return 'No employees found.';
                },
                searching: function() {
                    return 'Searching employees...';
                }
            }
        });

        $('#employeeFilter').on('change', function() {
            var employeeId = $(this).val();

            leaveReportTable.clear().draw();

            if (!employeeId) {
                $('#leaveReportResult').hide();
                return;
            }

            $('#leaveReportResult').show();

            $.ajax({
                url: "{{ url('leave_report/search') }}",
                data: {
                    employee_id: employeeId
                },
                success: function(response) {
                    leaveReportTable.clear().rows.add(response).draw();
                },
                error: function() {
                    leaveReportTable.clear().draw();
                }
            });
        });

        $('#employeeFilter').on('select2:open', function() {
            setTimeout(function() {
                $('.select2-container--open .select2-search__field').focus();
            }, 0);
        });
    })
</script>
@endsection
