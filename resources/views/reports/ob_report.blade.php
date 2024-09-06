@extends('layouts.header')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class='row'>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Filter</h4>
                        <p class="card-description">
                            <form method='get' onsubmit='show();' enctype="multipart/form-data">
                                <div class=row>
                                    <div class='col-md-2'>
                                        <div class="form-group">
                                            <label class="text-right">Company</label>
                                            <select data-placeholder="Select Company" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='company[]' multiple required>
                                                <option value="">-- Select Employee --</option>
                                                @foreach($companies as $comp)
                                                <option value="{{$comp->id}}" @if (in_array($comp->id,$company)) selected @endif>{{$comp->company_code}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class='col-md-2'>
                                        <div class="form-group">
                                            <label class="text-right">From</label>
                                            <input type="date" value='{{$from}}' class="form-control" name="from" max='{{date('Y-m-d')}}' onchange='get_min(this.value);' required />
                                        </div>
                                    </div>
                                    <div class='col-md-2'>
                                        <div class="form-group">
                                            <label class="text-right">To</label>
                                            <input type="date" value='{{$to}}' class="form-control" name="to" id='to' max='{{date('Y-m-d')}}' required />
                                            
                                        </div>
                                    </div>
                                    <div class='col-md-2 mr-2'>
                                        <div class="form-group">
                                            <label class="text-right">Status</label>
                                            <select data-placeholder="Select Status" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='status' required>
                                                <option value=""></option>
                                                <option value="ALL" @if ('ALL' == $status) selected  @endif>All</option>
                                                <option value="Approved" @if ('Approved' == $status) selected @endif>Approved</option>
                                                <option value="Pending" @if ('Pending' == $status) selected @endif>Pending</option>
                                                <option value="Cancelled" @if ('Cancelled' == $status) selected @endif>Cancelled</option>
                                                <option value="Declined" @if ('Declined' == $status) selected @endif>Declined</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class='col-md-2'>
                                        <button type="submit" class="form-control form-control-sm btn btn-primary mb-2 btn-sm">Generate</button>
                                    </div>
                                </div>
                            </form>
                        </p>
                        <h4 class="card-title">OB Report 
                            {{-- <a href="/ob-report-export?company={{$company}}&from={{$from}}&to={{$to}}" title="Export" class="btn btn-outline-primary btn-icon-text btn-sm text-center"><i class="ti-arrow-down btn-icon-prepend"></i></a> --}}
                        
                        </h4>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-detailed" id="ob_report">
                                <thead>
                                    <tr>
                                        {{-- <th>User ID</th> --}}
                                        <th>Employee Code</th>
                                        <th>Employee Name</th>
                                        
                                        <th>Filed By </th>
                                        <th>Date Filed</th>
                                        <th>Date</th>
                                        <th>Time In-Out</th>
                                        {{-- <th>OB Count</th>  --}}
                                        <th>Approved Date </th>
                                        <th>Remarks </th>
                                        <th>Status </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employee_obs as $item)
                                    <tr>
                                        {{-- <td>{{$item->employee->user_id}}</td> --}}
                                        <td>{{$item->employee->employee_code}}</td>
                                        <td>{{$item->user->name}}</td>
                                        
                                        <td>{{$item->created_by_info->name}}</td>
                                        <td>{{date('d/m/Y h:i A', strtotime($item->created_at))}}</td>
                                        <td>{{ date('d/m/Y ', strtotime($item->applied_date)) }}</td>
                                        <td>{{ date('d/m/Y h:i A', strtotime($item->date_from)) }} - {{ date('d/m/Y h:i A', strtotime($item->date_to)) }}  </td>
                                        {{-- <td>{{get_count_days($item->schedule,$item->date_from,$item->date_to)}}</td> --}}
                                        <td>{{ $item->approved_date ? date('d/m/Y', strtotime($item->approved_date)) : ""}}</td>
                                        <td>{{$item->remarks}}
                                            <br>
                                            @if($item->attachment)
                                                <a href="{{url($item->attachment)}}" target='_blank' class="text-start"><button type="button" class="btn btn-outline-info btn-sm ">View Attachment</button></a>
                                            @endif
                                            <br>
                                            @if($item->ob_file)
                                                <a href="{{ url($item->ob_file) }}" target='_blank' class="text-start"><button type="button" class="btn btn-outline-info btn-sm ">View Attachment</button></a>
                                            @endif
                                        </td>
                                        <td>{{$item->status}}</td>
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
<!-- DataTables CSS and JS includes -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">

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
@section('footer')
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/js/inputmask.js') }}"></script>
@endsection
