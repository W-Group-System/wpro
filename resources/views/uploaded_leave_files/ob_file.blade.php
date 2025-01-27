@extends('layouts.header')
@section('css_header')
    <style>
        .pagination
        {
            float: right;
            margin-top: 12px;
        }
    </style>
@endsection

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class='row'>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="get" class="mb-3" onsubmit="show()">
                            <div class="row">
                                <div class="col-lg-3">
                                    Company 
                                    <select data-placeholder="Choose company" name="company" class="js-example-basic-single" style="width: 100%;" required>
                                        <option value=""></option>
                                        @foreach ($companies as $company)
                                            <option value="{{$company->id}}" @if($company->id == $company_filter) selected @endif>{{$company->company_code}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    From
                                    <input type="date" name="date_from" id="dateFrom" class="form-control" value="{{$date_from}}" required>
                                </div>
                                <div class="col-lg-3">
                                    To
                                    <input type="date" name="date_to" id="dateTo" class="form-control" value="{{$date_to}}" required>
                                </div>
                                <div class="col-lg-3">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                            </div>
                        </form>
                        <h4 class="card-title">Official Business Files Report</h4>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>File</th>
                                        <th>Uploaded At</th>
                                        <th>Uploaded By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($files as $file)
                                    <tr>
                                        <td>
                                            <a href="{{url($file->file_path)}}" target="_blank">
                                                {{$file->file_name}}
                                            </a>
                                        </td>
                                        <td>{{date('M d Y', strtotime($file->uploaded_at))}}</td>
                                        <td>{{$file->user->name}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {!! $files->appends(['company' => $company_filter])->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @include('upload.new-upload')
@include('upload.export-template') --}}

@endsection