@extends('layouts.header')

@section('content')
	<div class="main-panel">
		<div class="content-wrapper">
			<div class="col-lg-12 grid-margin stretch-card">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Filter</h4>
						<p class="card-description">
						<form method='get' onsubmit='show();' enctype="multipart/form-data">
							<div class=row>
								<div class='col-md-3'>
									<div class="form-group">
										<label class="text-right">Employee</label>
										<select data-placeholder="Select Employee" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='employee[]' multiple required>
											<option value="">-- Select Employee --</option>
											@foreach($employees as $emp)
											<option value="{{$emp->id}}" @if(in_array($emp->id,$employee)) selected @endif>{{$emp->employee_code .' - '.$emp->user_info->name}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class='col-md-2'>
									<div class="form-group">
										<label class="text-right">From</label>
										<input type="date" value='{{$from}}' class="form-control form-control-sm" name="from"
											 onchange='get_min(this.value);' required />
									</div>
								</div>
								<div class='col-md-2'>
									<div class="form-group">
										<label class="text-right">To</label>
										<input type="date" value='{{$to}}' class="form-control form-control-sm" id='to' name="to"
											 required />
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
						<div class='row'>
							<div class="col-lg-12 grid-margin stretch-card">
							  <div class="card">
								<div class="card-body">
								  <h4 class="card-title">Leave Report 
									{{-- <a href="/leave-report-export?company={{$company}}&from={{$from}}&to={{$to}}&status={{$status}}" title="Export" class="btn btn-outline-primary btn-icon-text btn-sm text-center"><i class="ti-arrow-down btn-icon-prepend"></i></a> --}}
									</h4>
								  <div class="table-responsive">
									<table class="table table-hover table-bordered table-detailed">
									  <thead>
										<tr>
										  
										  {{-- <th>User ID</th> --}}
                                          <th>Company</th>
										  <th>Employee Code</th>
										  <th>Employee Name</th>
										  
										  <th>Filed By</th> 
										  <th>Date Filed</th>
										  <th>Leave Type</th>
										  <th>From</th>
										  <th>To</th>
										  <th>With Pay </th>
										  <th>Half Day </th>
										  <th>Leave Count</th>
										  <th>Leave Approver</th>
										  <th>Status</th> 
										  <th>Approved Date</th> 
										  <th>Reason/Remarks</th> 
										</tr>
									  </thead>
									  <tbody> 
										@foreach ($employee_leaves as $form_approval)
										<tr>
                                            <td>{{$form_approval->employee->company->company_name}}</td>
                                            <td>{{$form_approval->employee->employee_code}}</td>
                                            <td>{{$form_approval->user->name}}</td>
                                            
                                            <td>{{$form_approval->created_by_info->name}}</td>
                                            <td>{{date('d/m/Y h:i A', strtotime($form_approval->created_at))}}</td>
                                            <td>{{$form_approval->leave->leave_type}}</td>
                                            <td>{{date('d/m/Y', strtotime($form_approval->date_from))}}</td>
                                            <td>{{date('d/m/Y', strtotime($form_approval->date_to))}}</td>
                                            @if($form_approval->withpay == 1)   
                                                <td>Yes</td>
                                            @else
                                                <td>No</td>
                                            @endif  
                                            @if($form_approval->halfday == 1)   
                                                <td>Yes</td>
                                            @else
                                                <td></td>
                                            @endif 
                                            <td>
                                                @if($form_approval->leave->id != 13)
                                                {{get_count_days($form_approval->dailySchedules, $form_approval->employee->ScheduleData, $form_approval->date_from, $form_approval->date_to, $form_approval->halfday,$form_approval->withpay)}}
                                                @endif
                                            </td>
                                            <td>@foreach($form_approval->approver as $approvers)
                                                    {{$approvers->approver_info->name}}
                                                
                                                @endforeach</td>
                                            <td>
                                                {{$form_approval->status}}
                                            </td>
                                            <td>{{ $form_approval->approved_date ? date('d/m/Y', strtotime($form_approval->approved_date)) : ""}}</td>
                                            <td>
                                                    {{$form_approval->reason}} <br>
                                                    @if($form_approval->attachment)
                                                        <a href="{{url($form_approval->attachment)}}" target='_blank' class="text-start"><button type="button" class="btn btn-outline-info btn-sm ">View Attachment</button></a>
                                                    @endif
                                                    <br>
                                                    @if($form_approval->leave_file)
                                                        <a href="{{url('storage/'.$form_approval->leave_file)}}" target='_blank' class="text-start"><button type="button" class="btn btn-outline-info btn-sm ">View Attachment</button></a>
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
    function get_min(value)
    {
      document.getElementById("to").min = value;
    }
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

@php
function get_count_days_report($data,$date_from,$date_to,$halfday)
 {

    if($date_from == $date_to){
        $count = 1;
    }else{
      $data = ($data->pluck('name'))->toArray();
      $count = 0;
      $startTime = strtotime($date_from);
      $endTime = strtotime($date_to);

      for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
        $thisDate = date( 'l', $i ); // 2010-05-01, 2010-05-02, etc
        if(in_array($thisDate,$data)){
            $count= $count+1;
        }
      }
    }

    if($count == 1 && $halfday == 1){
      return '0.5';
    }else{
      return($count);
    }
    
 } 
@endphp 
@endsection
