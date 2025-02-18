@extends('layouts.header')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Upload Raw Logs</h4>

                        <a href="{{url('export_template')}}" class="btn btn-outline-success mb-3">
                            <i class="ti-export"></i>
                            Export Template
                        </a>

                        <form method="POST" action="{{url('store_raw_logs')}}" enctype="multipart/form-data" onsubmit="show()">
                            @csrf 

                            <div class="row">
                                <div class="col-md-6">
                                    Upload Logs
                                    <input type="file" name="file" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection