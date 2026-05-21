@extends('layouts.header')
@section('content')
<div class="main-panel">
  <div class="content-wrapper">

    {{-- Approved OT Reference Panel --}}
    <div class="row grid-margin">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">My Approved Overtime Records</h4>
            <p class="text-muted mb-2">These are your approved OT records you can convert to an offset day off.</p>
            @if($overtimes->count())
              <div class="table-responsive">
                <table class="table table-sm table-bordered">
                  <thead class="thead-light">
                    <tr>
                      <th>OT Date</th>
                      <th>Start Time</th>
                      <th>End Time</th>
                      <th>Break (hrs)</th>
                      <th>Approved Hrs</th>
                      <th>Remarks</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($overtimes as $ot)
                      <tr>
                        <td>{{ \Carbon\Carbon::parse($ot->ot_date)->format('M d, Y') }}</td>
                        <td>{{ $ot->start_time ? date('h:i A', strtotime($ot->start_time)) : '—' }}</td>
                        <td>{{ $ot->end_time   ? date('h:i A', strtotime($ot->end_time))   : '—' }}</td>
                        <td>{{ $ot->break_hrs ?? '—' }}</td>
                        <td>
                          <span class="badge badge-success">{{ $ot->ot_approved_hrs ?? '—' }} hrs</span>
                        </td>
                        <td>{{ Str::limit($ot->remarks, 60) }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <p class="text-muted">No approved overtime records found.</p>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Filing Form --}}
    <div class="row grid-margin">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">File an Offset Request</h4>

            @if($approvers->count())
              <div class="mb-3">
                <label class="font-weight-bold">Approver(s):</label><br>
                @foreach($approvers as $approver)
                  <span class="badge badge-secondary mr-1">{{ optional($approver->approver_info)->name }}</span>
                @endforeach
              </div>
            @else
              <div class="alert alert-warning py-1">
                <small>No approver set. Please contact HR.</small>
              </div>
            @endif

            <form method="POST" action="{{ url('new-offset') }}" enctype="multipart/form-data">
              @csrf

              <div class="form-group">
                <label>OT Date Worked <span class="text-danger">*</span></label>
                <input type="date" name="ot_date" class="form-control form-control-sm {{ $errors->has('ot_date') ? 'is-invalid' : '' }}"
                  value="{{ old('ot_date') }}" required>
                @if($errors->has('ot_date'))<div class="invalid-feedback">{{ $errors->first('ot_date') }}</div>@endif
              </div>

              <div class="form-group">
                <label>OT Hours Worked <span class="text-danger">*</span></label>
                <input type="number" name="ot_hours" class="form-control form-control-sm {{ $errors->has('ot_hours') ? 'is-invalid' : '' }}"
                  value="{{ old('ot_hours') }}" step="0.5" min="0.5" max="24"
                  placeholder="e.g. 4" required>
                @if($errors->has('ot_hours'))<div class="invalid-feedback">{{ $errors->first('ot_hours') }}</div>@endif
              </div>

              <div class="form-group">
                <label>Date to Use Offset <span class="text-danger">*</span></label>
                <input type="date" name="date_to_use" id="date_to_use"
                  class="form-control form-control-sm {{ $errors->has('date_to_use') ? 'is-invalid' : '' }}"
                  value="{{ old('date_to_use') }}"
                  min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                @if($errors->has('date_to_use'))<div class="invalid-feedback">{{ $errors->first('date_to_use') }}</div>@endif
                <small class="text-muted">Must be a future date.</small>
              </div>

              <div class="form-group">
                <label>Reason <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control form-control-sm {{ $errors->has('reason') ? 'is-invalid' : '' }}"
                  rows="3" maxlength="1000" required
                  placeholder="Briefly explain why you are using this offset...">{{ old('reason') }}</textarea>
                @if($errors->has('reason'))<div class="invalid-feedback">{{ $errors->first('reason') }}</div>@endif
              </div>

              <div class="form-group">
                <label>Supporting Attachment <span class="text-danger">*</span></label>
                <input type="file" name="attachment"
                  class="form-control form-control-sm {{ $errors->has('attachment') ? 'is-invalid' : '' }}"
                  accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
                @if($errors->has('attachment'))<div class="invalid-feedback">{{ $errors->first('attachment') }}</div>@endif
                <small class="text-muted">Accepted: JPG, PNG, PDF, DOC/DOCX (max 5MB)</small>
              </div>

              <button type="submit" class="btn btn-primary btn-sm">Submit Offset Request</button>
            </form>

          </div>
        </div>
      </div>

      {{-- My Offset Requests History --}}
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">My Offset Requests</h4>
            @if($requests->count())
              <div class="table-responsive">
                <table class="table table-sm table-bordered">
                  <thead class="thead-light">
                    <tr>
                      <th>OT Date</th>
                      <th>OT Hrs</th>
                      <th>Day Off</th>
                      <th>Status</th>
                      <th>Attachment</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($requests as $req)
                      <tr>
                        <td>{{ \Carbon\Carbon::parse($req->ot_date)->format('M d, Y') }}</td>
                        <td>{{ $req->ot_hours }} hrs</td>
                        <td>{{ \Carbon\Carbon::parse($req->date_to_use)->format('M d, Y') }}</td>
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
                          @if($req->attachment)
                            <a href="{{ url($req->attachment) }}" target="_blank">
                              <button type="button" class="btn btn-outline-info btn-sm">View</button>
                            </a>
                          @else
                            —
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <p class="text-muted">No offset requests filed yet.</p>
            @endif
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
