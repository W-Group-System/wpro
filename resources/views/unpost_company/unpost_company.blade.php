@extends('layouts.header')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        @if($errors->any())
        <div class="form-group alert alert-danger alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <strong>{{$errors->first()}}</strong>
        </div>
        @endif
        <div class='row'>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Unpost Company</h4>
                        <form action="" method="get" onsubmit="show();">
                            <div class="row">
                                <div class="col-md-3">
                                    Companies
                                    <select data-placeholder="Select company" name="companies" class="form-control js-example-basic-single">
                                        <option value=""></option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}" @if($company_data == $company->id) selected @endif>{{ $company->company_code.' - '.$company->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Cutoff Date
                                    <select data-placeholder="Select cutoff date" name="cut_off_date" class="form-control js-example-basic-single" >
                                        <option value=""></option>
                                        @foreach ($atd_cutoff_date as $cutoff_date)
                                            <option value="{{ $cutoff_date->cut_off_date }}" @if($cut_off_date == $cutoff_date->cut_off_date) selected @endif>{{ $cutoff_date->cut_off_date }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <button type="reset" class="btn btn-warning" onclick="resetFilter()">Reset Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>Actions</th>
                                        <th>Company</th>
                                        <th>Cutoff Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendance_detailed_reports as $attendance_detailed_report)
                                        <tr>
                                            <td>
                                                <p>
                                                    <form method="post" action="{{ url('unpost_per_company') }}" onsubmit="show()" id="unpostForm">

                                                        <input type="hidden" name="company_id" value="{{ $attendance_detailed_report->company_id }}">
                                                        <input type="hidden" name="cut_off_date" value="{{ $attendance_detailed_report->cut_off_date }}">

                                                        @csrf 
                                                        <button type="button" class="btn btn-sm btn-danger" id="delete">
                                                            <i class="ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </p>
                                            </td>
                                            <td>{{ $attendance_detailed_report->company->company_code }}</td>
                                            <td>{{ $attendance_detailed_report->cut_off_date }}</td>
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

<script>
    function resetFilter()
    {
        $('[name="companies"]').val(null).trigger('change')
        $('[name="cut_off_date"]').val(null).trigger('change')
    }

    $("#delete").on('click', function() {
        Swal.fire({
            title: "Are you sure you want to unpost this company?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, unpost it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $("#unpostForm").submit()
            }
        });
    })
</script>
@endsection