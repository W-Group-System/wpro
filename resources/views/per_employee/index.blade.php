@extends('layouts.header')

@section('css_header')
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css"> --}}

<style>
    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url("{{ asset('login_css/images/loader.gif') }}") 50% 50% no-repeat white;
        opacity: .8;
        background-size: 120px 120px;
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
                        <h4 class="card-title">Timekeeping Per Employee</h4>
                        <form method="get" id="filterForm">
                            <div class="row mb-5">
                                <div class="col-md-2">
                                    Employee :
                                    <select class="form-control js-example-basic-multiple" name="employee" data-placeholder="Select employee" style="width: 100%;" multiple required>
                                        <option></option>
                                        @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{$employee->employee_code.' - '. $employee->last_name.', '.$employee->first_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    Date From :
                                    <input type="date" name="date_from" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    Date To :
                                    <input type="date" name="date_to" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    &nbsp;
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            Filter
                                        </button>
                                        <a href="{{ url('timekeeping-official') }}" class="btn btn-warning">
                                            Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered mt-5 forPostingTable">
                                        <thead>
                                            <tr>
                                                <th>COMPANY</th>
                                                <th>EMPLOYEE CODE</th>
                                                <th>NAME</th>
                                                <th>DATE LOGS</th>
                                                <th>SCHEDULE</th>
                                                <th>TIME IN</th>
                                                <th>TIME OUT</th>
                                                <th>ABSENT</th>
                                                <th>REG HRS (HRS)</th>
                                                <th>LATE (MIN)</th>
                                                <th>UNDERTIME(min)</th>
                                                <th>LV W/ PAY</th>
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
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        var dtrTable = $(".forPostingTable").DataTable({
            paging: true,
            lengthChange: true,
            ordering: false,
            info: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            stateSave:true,
            ajax: {
                type: "POST",
                url: "{{ url('timekeeping-official/per_employee') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function(d) {
                    d.employee = $("[name='employee']").val()
                    d.date_from = $("[name='date_from']").val()
                    d.date_to = $("[name='date_to']").val()
                }
            },
            language: {
                processing: "⏳ Loading data, please wait..."
            },
            columns: [
                {data:'company'},
                {data:'employee_code'},
                {data:'name'},
                {data:'date_logs'},
                {
                    render: function(data, type, row) {
                        return `<small>${row.schedule}</small>`
                    }
                },
                {data:"time_in"},
                {data:'time_out'},
                {data:"absent"},
                {data:"reg_hrs"},
                {data:"late"},
                {data:"undertime"},
                {data:"leave"},
                {data:"overtime"},
                {data:"reg_nd"},
                {data:"reg_ot_nd"},
                {data:"restday_ot"},
                {data:"restday_ot_ge"},
                {data:"restnd"},
                {data:"restnd_ge"},
                {data:"lh_ot"},
                {data:"lh_ot_ge"},
                {data:"lh_nd"},
                {data:"lh_nd_ge"},
                {data:"sh_ot"},
                {data:"sh_ot_ge"},
                {data:"sh_ot_nd"},
                {data:"sh_ot_nd_ge"},
                {data:"rst_lh_ot"},
                {data:"rst_lh_ot_ge"},
                {data:"rst_lh_ot_nd"},
                {data:"rst_lh_ot_nd_ge"},
                {data:"rst_sh_ot"},
                {data:"rst_sh_ot_ge"},
                {data:"rst_sh_ot_nd"},
                {data:"rst_sh_ot_nd_ge"},
                {data:"remarks"},
            ],
            createdRow: function(row, data, dataIndex) {
                if (data.if_has_ob == "Yes") {
                    $(row).find('td:eq(5)').addClass('bg-info');
                }
                if (data.if_has_ob == "Yes") {
                    $(row).find('td:eq(6)').addClass('bg-info');
                }
                if (parseFloat(data.absent)-parseFloat(data.leave_count) > 0) {
                    $(row).find('td:eq(7)').addClass('bg-danger');
                }
                if (data.late > 0) {
                    $(row).find('td:eq(9)').addClass('bg-danger');
                }
                if (data.undertime > 0) {
                    $(row).find('td:eq(10)').addClass('bg-danger');
                }
                if (data.overtime > 0) {
                    $(row).find('td:eq(12)').addClass('bg-warning');
                }
                if (data.reg_nd > 0) {
                    $(row).find('td:eq(13)').addClass('bg-warning');
                }
                if (data.reg_ot_nd > 0) {
                    $(row).find('td:eq(14)').addClass('bg-warning');
                }
                if (data.restday_ot > 0) {
                    $(row).find('td:eq(15)').addClass('bg-warning');
                }
                if (data.restday_ot_ge > 0) {
                    $(row).find('td:eq(16)').addClass('bg-warning');
                }
                if (data.restnd > 0) {
                    $(row).find('td:eq(17)').addClass('bg-warning');
                }
                if (data.restnd_ge > 0) {
                    $(row).find('td:eq(18)').addClass('bg-warning');
                }
                if (data.lh_ot > 0) {
                    $(row).find('td:eq(19)').addClass('bg-warning');
                }
                if (data.lh_ot_ge > 0) {
                    $(row).find('td:eq(20)').addClass('bg-warning');
                }
                if (data.lh_nd > 0) {
                    $(row).find('td:eq(21)').addClass('bg-warning');
                }
                if (data.lh_nd_ge > 0) {
                    $(row).find('td:eq(22)').addClass('bg-warning');
                }
                if (data.sh_ot > 0) {
                    $(row).find('td:eq(23)').addClass('bg-warning');
                }
                if (data.sh_ot_ge > 0) {
                    $(row).find('td:eq(24)').addClass('bg-warning');
                }
                if (data.sh_nd > 0) {
                    $(row).find('td:eq(25)').addClass('bg-warning');
                }
                if (data.sh_nd_ge > 0) {
                    $(row).find('td:eq(26)').addClass('bg-warning');
                }
                if (data.rst_lh_ot > 0) {
                    $(row).find('td:eq(27)').addClass('bg-warning');
                }
                if (data.rst_lh_ot_ge > 0) {
                    $(row).find('td:eq(28)').addClass('bg-warning');
                }
                if (data.rst_lh_ot_nd > 0) {
                    $(row).find('td:eq(29)').addClass('bg-warning');
                }
                if (data.rst_lh_ot_nd_ge > 0) {
                    $(row).find('td:eq(30)').addClass('bg-warning');
                }
                if (data.rst_sh_ot > 0) {
                    $(row).find('td:eq(31)').addClass('bg-warning');
                }
                if (data.rst_sh_ot_ge > 0) {
                    $(row).find('td:eq(32)').addClass('bg-warning');
                }
                if (data.rst_sh_ot_nd > 0) {
                    $(row).find('td:eq(33)').addClass('bg-warning');
                }
                if (data.rst_sh_ot_nd_ge > 0) {
                    $(row).find('td:eq(34)').addClass('bg-warning');
                }
            }
        })

        $("#filterForm").on('submit', function(e) {
            e.preventDefault()

            dtrTable.ajax.reload()
        })
    })
</script>
@endsection