@extends('layouts.header')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin-stretch">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">For Approval</h5>

                        <ul class="nav nav-tabs mt-5">
                            <li class="nav-item">
                                <a class="nav-link active" href="#pills-for-approval" data-toggle="tab" >For Approval DTR<span class="badge badge-danger" id="totalForApproval">0</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#pills-approved-cancelled" data-toggle="tab" >Approved / Cancelled DTR<span class="badge badge-warning" id="totalApprovedCancelled">0</span></a>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-for-approval" role="tabpanel" aria-labelledby="pills-for-approval">
                                <div class="row mt-5">
                                    <div class="table-responsive">
                                        <table class="table table-bordered timekeepingTable">
                                            <thead>
                                                <tr>
                                                    <th>ACTION</th>
                                                    <th>EMPLOYEE</th>
                                                    <th>DATE</th>
                                                    <th>TIME IN</th>
                                                    <th>TIME OUT</th>
                                                    <th>REMARKS</th>
                                                    <th>INCIDENT REPORT</th>
                                                    <th>STATUS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dtr_corrections->where('status', 'Pending') as $dtr_correction)
                                                    <tr>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-primary">Actions</button>
                                                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" id="dropdownMenuSplitButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <span class="sr-only">Toggle Dropdown</span>
                                                                </button>
                                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuSplitButton1">
                                                                    <h6 class="dropdown-header">Settings</h6>
                                                                    <button type="button" class="dropdown-item" onclick="approveBtn({{ $dtr_correction->id }})">Approve</button>
                                                                    <button type="button" class="dropdown-item" onclick="cancelBtn({{ $dtr_correction->id }})">Cancel</button>
                                                                </div>
                                                            </div>

                                                            {{-- Approved --}}
                                                            <form method="post" action="{{ url('timekeeping-official/update/'.$dtr_correction->id) }}" id="approvedForm{{ $dtr_correction->id }}" onsubmit="show()">
                                                                @csrf
                                                                
                                                                <input type="hidden" name="date" value="{{ $dtr_correction->date }}">
                                                                <input type="hidden" name="emp_id" value="{{ $dtr_correction->employee_id }}">
                                                                <input type="hidden" name="status" value="Approved">

                                                            </form>

                                                            {{-- Declined --}}
                                                            <form method="post" action="{{ url('timekeeping-official/update/'.$dtr_correction->id) }}" id="cancelledForm{{ $dtr_correction->id }}" onsubmit="show()">
                                                                @csrf
                                                                
                                                                <input type="hidden" name="date" value="{{ $dtr_correction->date }}">
                                                                <input type="hidden" name="emp_id" value="{{ $dtr_correction->employee_id }}">
                                                                <input type="hidden" name="status" value="Cancelled">

                                                            </form>
                                                        </td>
                                                        <td>{{ $dtr_correction->employee->user_info->name }}</td>
                                                        <td>{{ $dtr_correction->date }}</td>
                                                        <td>{{ date('h:i A', strtotime($dtr_correction->time_in)) }}</td>
                                                        <td>{{ date('h:i A', strtotime($dtr_correction->time_out)) }}</td>
                                                        <td style="white-space: pre-line;">{{ $dtr_correction->remarks }}</td>
                                                        <td>
                                                            <a href="{{ url($dtr_correction->file) }}" target="_blank">
                                                                <i class="ti-file"></i>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @if($dtr_correction->status == 'Pending')
                                                            <span class="badge badge-warning">
                                                            @elseif($dtr_correction->status == 'Approved')
                                                            <span class="badge badge-success">
                                                            @elseif($dtr_correction->status == 'Cancelled')
                                                            <span class="badge badge-danger">
                                                            @endif
                                                            
                                                            {{ $dtr_correction->status }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-approved-cancelled" role="tabpanel" aria-labelledby="pills-approved-cancelled">
                                <div class="row mt-5">
                                    <div class="table-responsive">
                                        <table class="table table-bordered timekeepingTable">
                                            <thead>
                                                <tr>
                                                    <th>EMPLOYEE</th>
                                                    <th>DATE</th>
                                                    <th>TIME IN</th>
                                                    <th>TIME OUT</th>
                                                    <th>REMARKS</th>
                                                    <th>INCIDENT REPORT</th>
                                                    <th>STATUS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dtr_corrections->where('status','!=','Pending') as $dtr_correction)
                                                    <tr>
                                                        <td>{{ $dtr_correction->employee->user_info->name }}</td>
                                                        <td>{{ $dtr_correction->date }}</td>
                                                        <td>{{ date('h:i A', strtotime($dtr_correction->time_in)) }}</td>
                                                        <td>{{ date('h:i A', strtotime($dtr_correction->time_out)) }}</td>
                                                        <td style="white-space: pre-line;">{{ $dtr_correction->remarks }}</td>
                                                        <td>
                                                            <a href="{{ url($dtr_correction->file) }}" target="_blank">
                                                                <i class="ti-file"></i>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @if($dtr_correction->status == 'Pending')
                                                            <span class="badge badge-warning">
                                                            @elseif($dtr_correction->status == 'Approved')
                                                            <span class="badge badge-success">
                                                            @elseif($dtr_correction->status == 'Cancelled')
                                                            <span class="badge badge-danger">
                                                            @endif
                                                            
                                                            {{ $dtr_correction->status }}
                                                            </span>
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
        </div>
    </div>
</div>

@foreach ($dtr_corrections as $dtr_correction)
    @include('timekeeping.declined')
    @include('timekeeping.approved')
@endforeach
@endsection

@section('js')
<script>
    function approveBtn(id)
    {
        document.getElementById('approvedForm'+id).submit()
    }

    function cancelBtn(id)
    {
        document.getElementById('cancelledForm'+id).submit()
    }

    $(document).ready(function() {
        $(".timekeepingTable").DataTable({
            // pagelength:15,
            fixedColumns: {
                leftColumns: 1,  // 'start' and 'end' have been replaced with 'leftColumns' for clarity
            },
            paginate:false,
            dom: 'Bfrtip',
            // buttons: [
            //     'copy', 'excel'
            // ],
            // columnDefs: [{
            //     "defaultContent": "-",
            //     "targets": "_all"
            // }],
            order: [] 
        })
    })
</script>
@endsection