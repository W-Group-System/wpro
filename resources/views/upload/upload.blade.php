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
                        <h4 class="card-title">Upload OB/OT/Leaves</h4>
                        <p class="card-description">
                            <button type="button" class="btn btn-outline-warning btn-icon-text" data-toggle="modal"
                                data-target="#uploadModal">
                                <i class="ti-plus btn-icon-prepend"></i>
                                Upload
                            </button>
                            <button class="btn btn-outline-success btn-icon-text" data-toggle="modal"
                                data-target="#exportModal">
                                <i class="ti-plus btn-icon-prepend"></i>
                                Export Template
                            </button>
                        </p>
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
                                    @foreach ($uploadTypes as $ut)
                                    <tr>
                                        <td>
                                            <a href="{{url($ut->file_path)}}" target="_blank">
                                                {{$ut->file_name}}
                                            </a>
                                        </td>
                                        <td>{{date('M d Y', strtotime($ut->uploaded_at))}}</td>
                                        <td>{{$ut->user->name}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {!! $uploadTypes->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('upload.new-upload')
@include('upload.export-template')

@endsection