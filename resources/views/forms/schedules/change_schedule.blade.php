@extends('layouts.header')
@section('content')
<div class="main-panel">
  <div class="content-wrapper">

    {{-- Current Schedule Panel --}}
    <div class="row grid-margin">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">My Current Schedule</h4>
            @if($employee && $employee->ScheduleData && $employee->ScheduleData->count())
              <div class="table-responsive">
                <table class="table table-sm table-bordered">
                  <thead class="thead-light">
                    <tr>
                      <th>Day</th>
                      <th>Time In (From)</th>
                      <th>Time In (To)</th>
                      <th>Time Out (From)</th>
                      <th>Time Out (To)</th>
                      <th>Working Hours</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($employee->ScheduleData as $sched)
                      <tr>
                        <td><strong>{{ $sched->name }}</strong></td>
                        <td>{{ $sched->time_in_from ? date('h:i A', strtotime($sched->time_in_from)) : '—' }}</td>
                        <td>{{ $sched->time_in_to   ? date('h:i A', strtotime($sched->time_in_to))   : '—' }}</td>
                        <td>{{ $sched->time_out_from ? date('h:i A', strtotime($sched->time_out_from)) : '—' }}</td>
                        <td>{{ $sched->time_out_to   ? date('h:i A', strtotime($sched->time_out_to))   : '—' }}</td>
                        <td>
                          @if($sched->time_in_from && $sched->time_out_to)
                            <span class="badge @if(($sched->working_hours ?? 0) == 4) badge-warning @else badge-success @endif">
                              {{ $sched->working_hours ?? '—' }} hrs
                            </span>
                          @else
                            <span class="badge badge-secondary">Rest Day</span>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <p class="text-muted">No schedule assigned yet.</p>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- File Change Schedule Form --}}
    <div class="row grid-margin">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Request Schedule Change</h4>
            <p class="text-muted mb-3">
              Approver:
              @foreach($approvers as $ap)
                <strong>{{ optional($ap->approver_info)->name }}</strong>@if(!$loop->last), @endif
              @endforeach
            </p>

            <form method="POST" action="{{ url('new-schedule-change') }}" onsubmit="btnSchedule.disabled=true; return true;">
              @csrf
              <div class="form-group row">
                <label class="col-md-3 col-form-label">Date From</label>
                <div class="col-md-4">
                  <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" required
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>
                <label class="col-md-2 col-form-label">Date To</label>
                <div class="col-md-3">
                  <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" required
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-md-3 col-form-label">Time In (From–To)</label>
                <div class="col-md-4">
                  <input type="time" name="time_in_from" id="time_in_from" class="form-control form-control-sm" required placeholder="e.g. 08:00">
                  <small class="text-muted">Earliest allowed clock-in</small>
                </div>
                <div class="col-md-4">
                  <input type="time" name="time_in_to" id="time_in_to" class="form-control form-control-sm" required placeholder="e.g. 09:00">
                  <small class="text-muted">Latest allowed clock-in</small>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-md-3 col-form-label">Time Out (From–To)</label>
                <div class="col-md-4">
                  <input type="time" name="time_out_from" id="time_out_from" class="form-control form-control-sm" required placeholder="e.g. 17:00">
                  <small class="text-muted">Earliest allowed clock-out</small>
                </div>
                <div class="col-md-4">
                  <input type="time" name="time_out_to" id="time_out_to" class="form-control form-control-sm" required placeholder="e.g. 18:00">
                  <small class="text-muted">Latest allowed clock-out</small>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-md-3 col-form-label">Working Hours</label>
                <div class="col-md-3">
                  <input type="number" name="working_hours" id="working_hours" class="form-control form-control-sm"
                    step="0.5" min="0.5" max="24" required placeholder="e.g. 8">
                  <small class="text-muted">Auto-calculated below or enter manually</small>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-md-3 col-form-label">Reason</label>
                <div class="col-md-9">
                  <textarea name="reason" class="form-control" rows="3" required placeholder="Briefly explain why you need a schedule change"></textarea>
                </div>
              </div>

              <button type="submit" name="btnSchedule" class="btn btn-primary">Submit Request</button>
            </form>
          </div>
        </div>
      </div>

      {{-- History Panel --}}
      <div class="col-lg-5">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">My Requests</h4>
            @if($requests->isEmpty())
              <p class="text-muted">No schedule change requests yet.</p>
            @else
              <div class="table-responsive">
                <table class="table table-sm table-bordered">
                  <thead class="thead-light">
                    <tr>
                      <th>Date Range</th>
                      <th>Time In</th>
                      <th>Time Out</th>
                      <th>Hrs</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($requests as $req)
                      <tr>
                        <td>
                          {{ \Carbon\Carbon::parse($req->date_from)->format('M d') }}
                          @if($req->date_from != $req->date_to)
                            – {{ \Carbon\Carbon::parse($req->date_to)->format('M d, Y') }}
                          @else
                            , {{ \Carbon\Carbon::parse($req->date_from)->format('Y') }}
                          @endif
                        </td>
                        <td>{{ $req->time_in_from ? date('h:i A', strtotime($req->time_in_from)) : '—' }}</td>
                        <td>{{ $req->time_out_to  ? date('h:i A', strtotime($req->time_out_to))  : '—' }}</td>
                        <td>{{ $req->working_hours }}</td>
                        <td>
                          @if($req->status == 'Approved')
                            <span class="badge badge-success">Approved</span>
                          @elseif($req->status == 'Declined')
                            <span class="badge badge-danger">Declined</span>
                          @else
                            <span class="badge badge-warning">Pending</span>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  // Auto-calculate working hours from time_out_to minus time_in_from, subtract 1hr break if >= 8 hrs
  function calcHours() {
    var inFrom  = document.getElementById('time_in_from').value;
    var outTo   = document.getElementById('time_out_to').value;
    if (!inFrom || !outTo) return;
    var start = new Date('1970-01-01T' + inFrom + ':00');
    var end   = new Date('1970-01-01T' + outTo  + ':00');
    if (end <= start) end.setDate(end.getDate() + 1); // overnight
    var hours = (end - start) / 3600000;
    if (hours > 8) hours -= 1; // deduct 1hr lunch
    document.getElementById('working_hours').value = Math.round(hours * 2) / 2; // round to 0.5
  }

  document.getElementById('time_in_from').addEventListener('change', calcHours);
  document.getElementById('time_out_to').addEventListener('change', calcHours);

  // Keep date_to >= date_from
  document.getElementById('date_from').addEventListener('change', function () {
    document.getElementById('date_to').min = this.value;
    if (document.getElementById('date_to').value < this.value) {
      document.getElementById('date_to').value = this.value;
    }
  });
</script>
@endsection
