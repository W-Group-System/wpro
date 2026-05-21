@extends('layouts.header')
@section('content')
<div class="main-panel">
  <div class="content-wrapper">

    {{-- Count Cards --}}
    <div class="row grid-margin">
      <div class="col-lg-2 mt-2">
        <div class="card card-tale">
          <div class="card-body">
            <div class="media-body">
              <h4 class="mb-4">For Approval</h4>
              <a href="/for-offset?status=Pending" class="h2 card-text text-white">{{ $for_approval }}</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-2 mt-2">
        <div class="card card-dark-blue">
          <div class="card-body">
            <div class="media-body">
              <h4 class="mb-4">Approved</h4>
              <a href="/for-offset?status=Approved" class="h2 card-text text-white">{{ $approved }}</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-2 mt-2">
        <div class="card card-light-danger">
          <div class="card-body">
            <div class="media-body">
              <h4 class="mb-4">Declined</h4>
              <a href="/for-offset?status=Declined" class="h2 card-text text-white">{{ $declined }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Filter + Table --}}
    <div class="row">
      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Offset Requests</h4>

            <form method="get" onsubmit="show()">
              <div class="row mb-3">
                <div class="col-md-2">
                  <label>From</label>
                  <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                  <label>To</label>
                  <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                  <label>Status</label>
                  <select name="status" class="form-control form-control-sm">
                    <option value="Pending"  @if($status=='Pending')  selected @endif>Pending</option>
                    <option value="Approved" @if($status=='Approved') selected @endif>Approved</option>
                    <option value="Declined" @if($status=='Declined') selected @endif>Declined</option>
                  </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                  <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                </div>
              </div>
            </form>

            <div class="table-responsive">
              <table class="table table-hover table-bordered">
                <thead class="thead-light">
                  <tr>
                    <th>#</th>
                    <th>Employee</th>
                    <th>OT Date</th>
                    <th>OT Hours</th>
                    <th>Day Off Requested</th>
                    <th>Reason</th>
                    <th>Attachment</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($offset_requests as $key => $req)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ optional($req->user)->name }}</td>
                      <td>{{ \Carbon\Carbon::parse($req->ot_date)->format('M d, Y') }}</td>
                      <td>{{ $req->ot_hours }} hrs</td>
                      <td>{{ \Carbon\Carbon::parse($req->date_to_use)->format('M d, Y') }}</td>
                      <td>{{ Str::limit($req->reason, 60) }}</td>
                      <td>
                        @if($req->attachment)
                          <a href="{{ url($req->attachment) }}" target="_blank">
                            <button type="button" class="btn btn-outline-info btn-sm">View</button>
                          </a>
                        @else
                          —
                        @endif
                      </td>
                      <td>
                        @if($req->status == 'Approved')
                          <span class="badge badge-success">Approved</span>
                        @elseif($req->status == 'Declined')
                          <span class="badge badge-danger">Declined</span>
                        @else
                          <span class="badge badge-warning">Pending</span>
                        @endif
                      </td>
                      <td>
                        @if($req->status == 'Pending')
                          <button class="btn btn-success btn-sm" data-toggle="modal"
                            data-target="#approveModal-{{ $req->id }}">Approve</button>
                          <button class="btn btn-danger btn-sm" data-toggle="modal"
                            data-target="#declineModal-{{ $req->id }}">Decline</button>
                        @else
                          @if($req->approval_remarks)
                            <small class="text-muted">{{ $req->approval_remarks }}</small>
                          @else
                            —
                          @endif
                        @endif
                      </td>
                    </tr>

                    {{-- Approve Modal --}}
                    <div class="modal fade" id="approveModal-{{ $req->id }}" tabindex="-1" role="dialog">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Approve Offset Request</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                          </div>
                          <form method="POST" action="{{ url('approve-offset/'.$req->id) }}">
                            @csrf
                            <div class="modal-body">
                              <div class="mb-2">
                                <strong>{{ optional($req->user)->name }}</strong><br>
                                <span class="text-muted">
                                  OT Date: {{ \Carbon\Carbon::parse($req->ot_date)->format('M d, Y') }}
                                  &nbsp;|&nbsp; {{ $req->ot_hours }} hrs OT
                                </span><br>
                                <span class="text-muted">
                                  Day Off: <strong>{{ \Carbon\Carbon::parse($req->date_to_use)->format('M d, Y') }}</strong>
                                </span>
                              </div>
                              @if($req->reason)
                                <div class="alert alert-info py-1"><small>{{ $req->reason }}</small></div>
                              @endif
                              @if($req->attachment)
                                <a href="{{ url($req->attachment) }}" target="_blank" class="btn btn-outline-secondary btn-sm mb-2">
                                  <i class="ti-paperclip"></i> View Attachment
                                </a>
                              @endif
                              <h4 class="badge badge-success mt-1">Approved</h4>
                              <div class="form-group mt-2">
                                <label>Remarks (optional)</label>
                                <textarea name="approval_remarks" class="form-control" rows="3" placeholder="Add remarks..."></textarea>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-success">Approve</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>

                    {{-- Decline Modal --}}
                    <div class="modal fade" id="declineModal-{{ $req->id }}" tabindex="-1" role="dialog">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Decline Offset Request</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                          </div>
                          <form method="POST" action="{{ url('decline-offset/'.$req->id) }}">
                            @csrf
                            <div class="modal-body">
                              <div class="mb-2">
                                <strong>{{ optional($req->user)->name }}</strong><br>
                                <span class="text-muted">
                                  Day Off Requested: {{ \Carbon\Carbon::parse($req->date_to_use)->format('M d, Y') }}
                                </span>
                              </div>
                              <h4 class="badge badge-danger mt-1">Declined</h4>
                              <div class="form-group mt-2">
                                <label>Reason for Decline <span class="text-danger">*</span></label>
                                <textarea name="approval_remarks" class="form-control" rows="3" required
                                  placeholder="Explain why this is being declined..."></textarea>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-danger">Decline</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>

                  @empty
                    <tr>
                      <td colspan="9" class="text-center text-muted">No offset requests found.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
