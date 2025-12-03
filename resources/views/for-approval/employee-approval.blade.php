@extends('layouts.header')
@section('content')
<div class="main-panel">
  <div class="content-wrapper">
      <div class='row grid-margin'>
        <div class='col-lg-2 mt-2'>
          <div class="card card-tale">
            <div class="card-body">
              <div class="media">                
                <div class="media-body">
                  <h4 class="mb-4">For Approval</h4>
                  <a href="/for-employee?status=Pending" class="h2 card-text text-white">{{$for_approval}}</a>
                </div>
              </div>
            </div>
          </div>
        </div> 
        <div class='col-lg-2 mt-2'>
          <div class="card card-dark-blue">
            <div class="card-body">
              <div class="media">                
                <div class="media-body">
                  <h4 class="mb-4">Approved</h4>
                  <a href="/for-employee?status=Approved" class="h2 card-text text-white">{{$approved}}</a>
                </div>
              </div>
            </div>
          </div>
        </div> 
        <div class='col-lg-3 mt-2'>
          <div class="card card-light-danger">
            <div class="card-body">
              <div class="media">                
                <div class="media-body">
                  <h4 class="mb-4">Declined / Rejected</h4>
                  <a href="/for-employee?status=Declined" class="h2 card-text text-white">{{$declined}}</a>
                </div>
              </div>
            </div>
          </div>
        </div>            
      </div>
      <div class='row'>
        <div class="col-lg-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">For Approval New Employee</h4>

              <form method='get' onsubmit='show();' enctype="multipart/form-data">
                <div class=row>
                  <div class='col-md-2'>
                    <div class="form-group">
                      <label class="text-right">From</label>
                      <input type="date" value='{{$from}}' class="form-control form-control-sm" name="from" onchange='get_min(this.value);' required />
                    </div>
                  </div>
                  <div class='col-md-2'>
                    <div class="form-group">
                      <label class="text-right">To</label>
                      <input type="date" value='{{$to}}' class="form-control form-control-sm" id='to' name="to" required />
                    </div>
                  </div>
                  <div class='col-md-2 mr-2'>
                    <div class="form-group">
                      <label class="text-right">Status</label>
                      <select data-placeholder="Select Status" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='status' required>
                        <option value="">-- Select Status --</option>
                        <option value="Active" @if ('Active' == $status) selected @endif>Approved</option>
                        <option value="Pending" @if ('Pending' == $status) selected @endif>Pending</option>
                        <!-- <option value="Cancelled" @if ('Cancelled' == $status) selected @endif>Cancelled</option> -->
                        <option value="Declined" @if ('Declined' == $status) selected @endif>Declined</option>
                      </select>
                    </div>
                  </div>
                  <div class='col-md-2'>
                    <button type="submit" class="form-control form-control-sm btn btn-primary mb-2 btn-sm">Filter</button>
                  </div>
                </div>
              </form>

              @if(empty($status) || $status == 'Pending')
                <label>
                  <input type="checkbox" id="selectAll">
                  <span id="labelSelectAll">Select All</span> 
                </label>
              @endif

            <button id="approveAllBtn" class="btn btn-success btn-sm mb-2" style="display: none;">Approve</button>
            <button id="disApproveAllBtn" class="btn btn-danger btn-sm mb-2" style="display: none;">Disapprove</button>

              <div class="table-responsive">
                <table class="table table-hover table-bordered tablewithSearch">
                  <thead>
                    <tr>
                      @if(empty($status) || $status == 'Pending')
                        <th>
                          Select
                        </th>
                      @endif
                      <th>Action </th> 
                      <th>Date Created</th>
                      <th>Employee Name</th>
                      <th>Company</th>
                      <th>Date Hired</th>
                      <th>Job Offer</th>
                      <th>Employee Contract</th>
                      <th>Immediate Supervisor</th>
                      <th>Created By</th>
                      <!-- <th>Approver</th> -->
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody> 
                    @foreach ($employees as $form_approval)
                        <tr>
                            @if(empty($status) || $status == 'Pending')
                                <td align="center">
                                    <input type="checkbox" class="checkbox-item" data-id="{{$form_approval->id}}">
                                </td>
                            @endif
                            <td align="center" id="tdActionId{{ $form_approval->id }}" data-id="{{ $form_approval->id }}">
                                @if(Auth::id() == 875 && ($form_approval->is_review != 1 || $form_approval->is_review == NULL))
                                <button type="button" class="btn btn-info btn-sm" data-id="{{ $form_approval->id }}" data-target="#employee-review-modal-{{ $form_approval->id }}" data-toggle="modal" title="Review">
                                  <i class="ti-eye btn-icon-prepend"></i>
                                </button>
                                @endif
                                @if(Auth::id() == 593 || $form_approval->is_review == 1)
                                <button type="button" class="btn btn-success btn-sm" id="{{ $form_approval->id }}" data-target="#employee-approved-remarks-{{ $form_approval->id }}" data-toggle="modal" title="Approve">
                                  <i class="ti-check btn-icon-prepend"></i>                                                    
                                </button>
                                @endif
                                <button type="button" class="btn btn-danger btn-sm" id="{{ $form_approval->id }}" data-target="#employee-declined-remarks-{{ $form_approval->id }}" data-toggle="modal" title="Decline">
                                  <i class="ti-close btn-icon-prepend"></i>                                                    
                                </button> 
                            </td>
                            <td>{{date('M. d, Y h:i A', strtotime($form_approval->created_at))}}</td>
                            <td>
                              <a href="/account-setting-hr/{{$form_approval->user_id}}" style="color: #212529 !important; text-decoration: none">
                                <strong>{{$form_approval->user->name}}</strong> <br>
                                <small>Department : {{$form_approval->user->employee->department ? $form_approval->user->employee->department->name : ""}}</small><br>
                                <small>Position : {{$form_approval->user->employee->position}}</small><br>
                                <small>Location : {{$form_approval->user->location}}</small>
                              </a>
                            </td>
                            <td>{{$form_approval->company->company_name}}</td>
                            <td>{{date('M. d, Y', strtotime($form_approval->original_date_hired))}}</td>
                            <td>
                                @if($form_approval->job_offer)
                                    <a href="{{ asset($form_approval->job_offer) }}" target="_blank">
                                        View Job Offer
                                    </a>
                                @else
                                    No Contract
                                @endif
                            </td>
                            <td>
                                @if($form_approval->contract)
                                    <a href="{{ asset($form_approval->contract) }}" target="_blank">
                                        View Contract
                                    </a>
                                @else
                                    No Contract
                                @endif
                            </td>
                            <td>{{ optional($form_approval->immediate_sup_data)->name ?? '' }}</td>
                            {{-- <td id="tdStatus{{ $form_approval->id }}">
                                @foreach($form_approval->approver as $approver)
                                @if($form_approval->level >= $approver->level)
                                    @if ($form_approval->level == 0 && $form_approval->status == 'Declined')
                                    {{$approver->approver_info->name}} -  <label class="badge badge-danger mt-1">Declined</label>
                                    @else
                                        {{$approver->approver_info->name}} -  <label class="badge badge-success mt-1">Approved</label>
                                    @endif
                                @else
                                    @if ($form_approval->status == 'Declined')
                                    {{$approver->approver_info->name}} -  <label class="badge badge-danger mt-1">Declined</label>
                                    @else
                                    {{$approver->approver_info->name}} -  <label class="badge badge-warning mt-1">Pending</label>
                                    @endif
                                @endif<br> 
                                @endforeach
                            </td> --}}
                            {{-- <td>{{ optional($form_approval->creator->createdBy)->first_name. ' ' .optional($form_approval->user->createdBy)->last_name }}</td> --}}
                            <td>{{ optional($form_approval->creator)->name }}</td>
                            <td>
                                @if ($form_approval->status == 'Pending')
                                <label class="badge badge-warning">{{ $form_approval->status }}</label>
                                @elseif($form_approval->status == 'Approved')
                                <label class="badge badge-success" title="{{$form_approval->approval_remarks}}">{{ $form_approval->status }}</label>
                                @elseif($form_approval->status == 'Declined')
                                <label class="badge badge-danger" title="{{$form_approval->approval_remarks}}">{{ $form_approval->status }}</label>
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

<script>
    $(document).ready(function () {
        // Handle "Select All"
        $('#selectAll').on('click', function () {
            const isChecked = $(this).prop('checked');
            $('.checkbox-item').prop('checked', isChecked);
            updateSelectedCount();
            toggleButtons();
        });

        // Handle individual checkbox change
        $('.checkbox-item').on('change', function () {
            updateSelectedCount();
            toggleButtons();
        });

        // Update selected count and button labels
        function updateSelectedCount() {
            const count = $('.checkbox-item:checked').length;
            $('#approveAllBtn').text(`(${count}) Approve`);
            $('#disApproveAllBtn').text(`(${count}) Disapprove`);
        }

        // Show/hide action buttons
        function toggleButtons() {
            const count = $('.checkbox-item:checked').length;
            if (count > 0) {
                $('#approveAllBtn').show();
                $('#disApproveAllBtn').show();
            } else {
                $('#approveAllBtn').hide();
                $('#disApproveAllBtn').hide();
            }
        }
        const approveUrl = "{{ url('/approve-employee-all') }}";
        const disapproveUrl = "{{ url('/disapprove-employee-all') }}";


        $('#approveAllBtn').on('click', function() {
            Swal.fire({
                title: "Are you sure?",
                text: "You want to approved these employees?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, approved them!"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("loader").style.display = "block";

                    const selectedItems = [];
                    $('.checkbox-item:checked').each(function () {
                        selectedItems.push($(this).data('id')); // ✅ FIXED: send flat array
                    });

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: approveUrl, // Use the Blade-generated URL
                        data: {
                            ids: JSON.stringify(selectedItems),
                            approval_remarks: $('#approval_remarks').val() || ''
                        },
                        dataType: 'json',
                        success: function (response) {
                            document.getElementById("loader").style.display = "none";
                            Swal.fire("Success!", `Approved Employees records.`, "success")
                                .then(() => location.reload());
                        },
                        error: function (error) {
                            document.getElementById("loader").style.display = "none";
                            console.error(error);
                            Swal.fire("Error", "Approved failed.", "error");
                        }
                    });
                }
            });
        });

        
        $('#disApproveAllBtn').on('click', function () {
            Swal.fire({
                title: "Are you sure?",
                text: "You want to disapprove these employees?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, disapprove them!"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("loader").style.display = "block";

                    const selectedItems = [];
                    $('.checkbox-item:checked').each(function () {
                        selectedItems.push($(this).data('id')); // ✅ FIXED: send flat array
                    });

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        url: disapproveUrl, // Use the Blade-generated URL
                        data: {
                            ids: JSON.stringify(selectedItems),
                            approval_remarks: $('#approval_remarks').val() || ''
                        },
                        dataType: 'json',
                        success: function (response) {
                            document.getElementById("loader").style.display = "none";
                            Swal.fire("Success!", `Disapproved Employees records.`, "success")
                                .then(() => location.reload());
                        },
                        error: function (error) {
                            document.getElementById("loader").style.display = "none";
                            console.error(error);
                            Swal.fire("Error", "Disapproval failed.", "error");
                        }
                    });
                }
            });
        });
    });
</script>



@foreach ($employees as $employee)
  @include('for-approval.remarks.employee_reviewed_remarks')
  @include('for-approval.remarks.employee_approved_remarks')
  @include('for-approval.remarks.employee_declined_remarks')
@endforeach 

@endsection

