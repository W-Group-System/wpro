@extends('layouts.header')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card grid-margin stretch-card">
                    <div class="card-body">
                        <button type="button" class="btn btn-outline-success" data-toggle="modal"
                            data-target="#uploadNteModal">Upload NTE</button>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered tablewithSearch">
                                <thead>
                                    <tr>
                                        <th>Employee Number</th>
                                        <th>Employee Name</th>
                                        <th>Company</th>
                                        <th>Department</th>
                                        <th>Position</th>
                                        <th>File</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nteFile as $file)
                                    <tr>
                                        <td>{{$file->employee->employee_number}}</td>
                                        <td>{{$file->employee->first_name.' '.$file->employee->middle_name.'
                                            '.$file->employee->last_name}}</td>
                                        <td>{{$file->employee->company->company_name}}</td>
                                        <td>{{$file->employee->department->name}}</td>
                                        <td>{{$file->employee->position}}</td>
                                        <td>
                                            <a href="{{url($file->file_path)}}"
                                                class="btn btn-info btn-sm btn-rounded ti-eye" title="View file"
                                                target="_blank"></a>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" type="button" data-toggle="modal"
                                                data-target="#editModal-{{$file->id}}">
                                                <i class="ti-pencil-alt"></i>
                                                Edit
                                            </button>
                                        </td>
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

@include('hr-portal.new-nte')

@foreach ($nteFile as $item)
@include('hr-portal.edit-nte-file')
@endforeach
@endsection
@section('footer')
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/js/inputmask.js') }}"></script>
@endsection