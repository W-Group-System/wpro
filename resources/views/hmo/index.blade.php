@extends('layouts.header')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title" style="text-transform: none;">Proof of Availment</h4>
                        <p class="card-description">
                            <button type="button" class="btn btn-outline-success btn-icon-text" data-toggle="modal" data-target="#newHmo"><i class="ti-plus btn-icon-prepend"></i>&nbsp;New Proof of Availment</button>
                        </p>
                        <div class="table-responsive">
                        <table class="table table-hover table-bordered tablewithSearch">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Company</th>
                                    <th>Department</th>
                                    <th>Date of Availment</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availments as $availment)
                                <tr>
                                    <td>{{$availment->employee_name}}</td>
                                    <td>{{$availment->email}}</td>
                                    <td>{{$availment->company}}</td>
                                    <td>{{$availment->department}}</td>
                                    <td>{{date('M. d, Y',strtotime($availment->date_availment))}}</td>
                                    <td>{{ $availment->status }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-rounded btn-icon" href="#edit_hmo{{$availment->id}}" data-toggle="modal" title='EDIT'>
                                            <i class="ti-pencil-alt"></i>
                                        </button>
                                        @if($availment->status != 'Approved')
                                            <button title='Delete Availment' id="{{ $availment->id }}" onclick="remove({{$availment->id}})"
                                                class="btn btn-rounded btn-danger btn-icon">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
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
@include('hmo.new_hmo')
@foreach($availments as $availment)
@include('hmo.edit')
@endforeach
<script>
    function remove(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "You want to remove this availment?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("loader").style.display = "block";

                $.ajax({
                    url: "delete-hmo/" + id,
                    method: "GET", 
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        document.getElementById("loader").style.display = "none";

                        Swal.fire("Deleted!", "The availment has been soft deleted.", "success")
                            .then(() => location.reload());
                    },
                    error: function (xhr) {
                        document.getElementById("loader").style.display = "none";
                        Swal.fire("Error!", "Something went wrong.", "error");
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    }
</script>

@endsection
