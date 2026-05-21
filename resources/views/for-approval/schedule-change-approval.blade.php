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
              <a href="/for-schedule-change?status=Pending" class="h2 card-text text-white">{{ $for_approval }}</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-2 mt-2">
        <div class="card card-dark-blue">
          <div class="card-body">
            <div class="media-body">
              <h4 class="mb-4">Approved</h4>
              <a href="/for-schedule-change?status=Approved" class="h2 card-text text-white">{{ $approved }}</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-2 mt-2">
        <div class="card card-light-danger">
          <div class="card-body">
            <div class="media-body">
              <h4 class="mb-4">Declined</h4>
              <a href="/for-schedule-change?status=Declined" class="h2 card-text text-white">{{ $declined }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Filter --}}
    <div class="row">
      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Schedule Change Requests</h4>

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
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hrs</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($schedule_changes as $key => $sc)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ optional($sc->user)->name }}</td>
                      <td>{{ \Carbon\Carbon::parse($sc->date_from)->format('M d, Y') }}</td>
                      <td>{{ \Carbon\Carbon::parse($sc->date_to)->format('M d, Y') }}</td>
                      <td>
                        {{ $sc->time_in_from ? date('h:i A', strtotime($sc->time_in_from)) : '—' }}
                        – {{ $sc->time_in_to ? date('h:i A', strtotime($sc->time_in_to)) : '—' }}
                      </td>
                      <td>
                        {{ $sc->time_out_from ? date('h:i A', strtotime($sc->time_out_from)) : '—' }}
                        – {{ $sc->time_out_to ? date('h:i A', strtotime($sc->time_out_to)) : '—' }}
                      </td>
                      <td>{{ $sc->working_hours }} hrs</td>
                      <td>{{ Str::limit($sc->reason, 60) }}</td>
                      <td>
                        @if($sc->status == 'Approved')
                          <span class="badge badge-success">Approved</span>
                        @elseif($sc->status == 'Declined')
                          <span class="badge badge-danger">Declined</span>
                        @else
                          <span class="badge badge-warning">Pending</span>
                        @endif
                      </td>
                      <td>
                        @if($sc->status == 'Pending')
                          <button class="btn btn-success btn-sm" data-toggle="modal"
                            data-target="#approveModal-{{ $sc->id }}">Approve</button>
                          <button class="btn btn-danger btn-sm" data-toggle="modal"
                            data-target="#declineModal-{{ $sc->id }}">Decline</button>
                        @else
                          @if($sc->approval_remarks)
                            <small class="text-muted">{{ $sc->approval_remarks }}</small>
                          @else
                            —
                          @endif
                        @endif
                      </td>
                    </tr>

                    {{-- Approve Modal --}}
                    <div class="modal fade" id="approveModal-{{ $sc->id }}" tabindex="-1" role="dialog">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Approve Schedule Change</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                          </div>
                          <form method="POST" action="{{ url('approve-schedule-change/'.$sc->id) }}">
                            @csrf
                            <div class="modal-body">
                              <div class="mb-2">
                                <strong>{{ optional($sc->user)->name }}</strong><br>
                                <span class="text-muted">
                                  {{ \Carbon\Carbon::parse($sc->date_from)->format('M d') }}
                                  – {{ \Carbon\Carbon::parse($sc->date_to)->format('M d, Y') }}
                                  &nbsp;|&nbsp; {{ $sc->time_in_from }} – {{ $sc->time_out_to }}
                                  &nbsp;|&nbsp; {{ $sc->working_hours }} hrs
                                </span>
                              </div>
                              <div class="alert alert-info py-1"><small>{{ $sc->reason }}</small></div>
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
                    <div class="modal fade" id="declineModal-{{ $sc->id }}" tabindex="-1" role="dialog">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Decline Schedule Change</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                          </div>
                          <form method="POST" action="{{ url('decline-schedule-change/'.$sc->id) }}">
                            @csrf
                            <div class="modal-body">
                              <div class="mb-2">
                                <strong>{{ optional($sc->user)->name }}</strong><br>
                                <span class="text-muted">
                                  {{ \Carbon\Carbon::parse($sc->date_from)->format('M d') }}
                                  – {{ \Carbon\Carbon::parse($sc->date_to)->format('M d, Y') }}
                                </span>
                              </div>
                              <h4 class="badge badge-danger mt-1">Declined</h4>
                              <div class="form-group mt-2">
                                <label>Reason for Decline <span class="text-danger">*</span></label>
                                <textarea name="approval_remarks" class="form-control" rows="3" required placeholder="Explain why this is being declined..."></textarea>
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
                      <td colspan="10" class="text-center text-muted">No schedule change requests found.</td>
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
