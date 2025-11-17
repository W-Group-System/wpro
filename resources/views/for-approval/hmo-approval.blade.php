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
                  <a href="/for-hmo?status=Pending" class="h2 card-text text-white">{{$for_approval}}</a>
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
                  <a href="/for-hmo?status=Approved" class="h2 card-text text-white">{{$approved}}</a>
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
                  <a href="/for-hmo?status=Declined" class="h2 card-text text-white">{{$declined}}</a>
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
              <h4 class="card-title">Approval for Proof of Availment</h4>

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
                        <option value="Approved" @if ('Approved' == $status) selected @endif>Approved</option>
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
                      <th>Date of Availment</th>
                      <th>Status</th>
                      <th>Attachments</th>
                    </tr>
                  </thead>
                  <tbody> 
                    @foreach ($availments as $form_approval)
                        <tr>
                            @if(empty($status) || $status == 'Pending')
                                <td align="center">
                                    <input type="checkbox" class="checkbox-item" data-id="{{$form_approval->id}}">
                                </td>
                            @endif
                            <td align="center" id="tdActionId{{ $form_approval->id }}" data-id="{{ $form_approval->id }}">
                                <button type="button" class="btn btn-success btn-sm" id="{{ $form_approval->id }}" data-target="#hmo-approved-remarks-{{ $form_approval->id }}" data-toggle="modal" title="Approve">
                                  <i class="ti-check btn-icon-prepend"></i>                                                    
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" id="{{ $form_approval->id }}" data-target="#hmo-declined-remarks-{{ $form_approval->id }}" data-toggle="modal" title="Decline">
                                <i class="ti-close btn-icon-prepend"></i>                                                    
                                </button> 
                            </td>
                            <td>{{date('M. d, Y h:i A', strtotime($form_approval->created_at))}}</td>
                            <td>
                                <strong>{{$form_approval->employee_name}}</strong> <br>
                                <small>Department : {{$form_approval->department ? $form_approval->department : ""}}</small><br>
                            </td>
                            <td>{{$form_approval->company}}</td>
                            <td>{{date('M. d, Y', strtotime($form_approval->date_availment))}}</td>
                            <td>
                              @if ($form_approval->status == 'Pending')
                              <label class="badge badge-warning">{{ $form_approval->status }}</label>
                              @elseif($form_approval->status == 'Approved')
                              <label class="badge badge-success" title="{{$form_approval->approval_remarks}}">{{ $form_approval->status }}</label>
                              @elseif($form_approval->status == 'Declined')
                              <label class="badge badge-danger" title="{{$form_approval->approval_remarks}}">{{ $form_approval->status }}</label>
                              @endif  
                            </td>
                            <td>
                              @if($form_approval->attachments && $form_approval->attachments->isNotEmpty())
                                @foreach($form_approval->attachments as $file)
                                    @php
                                        $storage = \Illuminate\Support\Facades\Storage::disk('public');

                                        if ($storage->exists($file->path)) {
                                            $fileUrl = asset('storage/' . $file->path); 
                                        } else {
                                            $fileUrl = $file->path; 
                                        }

                                        $extension = strtolower(pathinfo($file->path, PATHINFO_EXTENSION));
                                        switch ($extension) {
                                            case 'pdf':
                                                $icon = 'fa-file-pdf-o';
                                                break;
                                            case 'doc':
                                            case 'docx':
                                                $icon = 'fa-file-word-o';
                                                break;
                                            case 'xls':
                                            case 'xlsx':
                                                $icon = 'fa-file-excel-o';
                                                break;
                                            case 'jpg':
                                            case 'jpeg':
                                            case 'png':
                                            case 'gif':
                                                $icon = 'fa-file-image-o';
                                                break;
                                            default:
                                                $icon = 'fa-file-o';
                                        }
                                    @endphp

                                    <a href="{{ $fileUrl }}" target="_blank" title="View File">
                                        <i class="fa {{ $icon }} fa-lg mx-1"></i>
                                    </a>
                                @endforeach
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
        const approveUrl = "{{ url('/approve-hmo-all') }}";
        const disapproveUrl = "{{ url('/disapprove-hmo-all') }}";


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
                            Swal.fire("Success!", `Approved Proof of Availement records.`, "success")
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
                            Swal.fire("Success!", `Disapproved Proof of Availment records.`, "success")
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



@foreach ($availments as $availment)
  @include('for-approval.remarks.hmo_approved_remarks')
  @include('for-approval.remarks.hmo_declined_remarks')
@endforeach

@endsection

