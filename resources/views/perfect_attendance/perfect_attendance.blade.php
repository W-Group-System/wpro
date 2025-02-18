@extends('layouts.header')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Perfect Attendance</h4>

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
                                    Month
                                    <input type="month" name="month" class="form-control" value="{{$month}}" max="{{date('Y-m', strtotime('-2 month'))}}">
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
                            <table class="table table-bordered table-hover table-detailed">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Employee No.</th>
                                        <th>Employee Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendances as $attendance)
                                        <tr>
                                            <td>{{$attendance->company->company_name}}</td>
                                            <td>{{optional($attendance->employee)->employee_code}}</td>
                                            <td>{{$attendance->name}}</td>
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
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }],
            order: [] 
            });
        });
    </script>

@endsection