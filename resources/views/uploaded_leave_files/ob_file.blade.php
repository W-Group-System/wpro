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

                            {!! $files->links() !!}
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