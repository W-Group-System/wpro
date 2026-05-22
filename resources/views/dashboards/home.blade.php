@extends('layouts.header')

@section('css_header')
<style>
  .home-dashboard {
    background: #f5f7fb;
  }

  .home-hero {
    align-items: center;
    background: #ffffff;
    border: 1px solid #e1e7ef;
    border-radius: 8px;
    box-shadow: 0 8px 22px rgba(31, 45, 61, .05);
    display: flex;
    gap: 18px;
    justify-content: space-between;
    margin-bottom: 18px;
    padding: 22px 24px;
  }

  .home-hero h3 {
    color: #172033;
    font-size: 1.45rem;
    font-weight: 800;
    margin: 0 0 5px;
  }

  .home-hero p {
    color: #64748b;
    margin: 0;
  }

  .home-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: flex-end;
  }

  .home-stat-grid {
    display: grid;
    gap: 14px;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    margin-bottom: 18px;
  }

  .home-stat {
    background: #fff;
    border: 1px solid #e1e7ef;
    border-left: 4px solid #248afd;
    border-radius: 8px;
    box-shadow: 0 8px 22px rgba(31, 45, 61, .05);
    min-height: 120px;
    padding: 16px;
  }

  .home-stat.success { border-left-color: #19a974; }
  .home-stat.warning { border-left-color: #ffb020; }
  .home-stat.danger { border-left-color: #ef476f; }

  .home-label {
    color: #718096;
    display: block;
    font-size: .72rem;
    font-weight: 800;
    text-transform: uppercase;
  }

  .home-value {
    color: #1f2d3d;
    display: block;
    font-size: 1.7rem;
    font-weight: 900;
    line-height: 1.15;
    margin-top: 8px;
    overflow-wrap: anywhere;
  }

  .home-note {
    color: #64748b;
    display: block;
    font-size: .78rem;
    font-weight: 700;
    margin-top: 8px;
  }

  .home-layout {
    display: block;
  }

  .home-panel {
    background: #fff;
    border: 1px solid #e1e7ef;
    border-radius: 8px;
    box-shadow: 0 8px 22px rgba(31, 45, 61, .05);
    margin-bottom: 18px;
  }

  .home-panel-body {
    padding: 18px;
  }

  .home-panel-title {
    align-items: flex-start;
    display: flex;
    gap: 12px;
    justify-content: space-between;
    margin-bottom: 14px;
  }

  .home-panel-title h4 {
    color: #1f2d3d;
    font-size: 1rem;
    font-weight: 900;
    margin: 3px 0 0;
  }

  .home-panel-title span {
    color: #248afd;
    display: block;
    font-size: .7rem;
    font-weight: 900;
    text-transform: uppercase;
  }

  .home-list {
    display: grid;
    gap: 10px;
    margin: 0;
    padding: 0;
  }

  .home-list-item {
    align-items: center;
    border: 1px solid #e5ebf2;
    border-radius: 8px;
    display: flex;
    gap: 12px;
    justify-content: space-between;
    min-height: 58px;
    padding: 10px 12px;
  }

  .home-list-main {
    min-width: 0;
  }

  .home-list-main strong,
  .home-list-main small {
    display: block;
  }

  .home-list-main strong {
    color: #24324b;
    font-weight: 850;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .home-list-main small {
    color: #718096;
    font-weight: 700;
    margin-top: 2px;
  }

  .home-avatar {
    border: 2px solid #edf4ff;
    border-radius: 50%;
    flex: 0 0 auto;
    height: 42px;
    object-fit: cover;
    width: 42px;
  }

  .home-person {
    display: flex;
    gap: 10px;
    min-width: 0;
  }

  .home-badge {
    border-radius: 999px;
    display: inline-flex;
    flex: 0 0 auto;
    font-size: .72rem;
    font-weight: 800;
    padding: 5px 9px;
    white-space: nowrap;
  }

  .home-badge.success {
    background: #e8f8f1;
    color: #0f7a54;
  }

  .home-badge.warning {
    background: #fff4d8;
    color: #8a5a00;
  }

  .home-badge.neutral {
    background: #edf2f7;
    color: #475569;
  }

  .home-badge.info {
    background: #e8f2ff;
    color: #1d4ed8;
  }

  .home-split {
    display: grid;
    gap: 14px;
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .home-calendar-head,
  .home-calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
  }

  .home-calendar-head {
    border-bottom: 1px solid #e5ebf2;
    color: #718096;
    font-size: .72rem;
    font-weight: 900;
    margin-bottom: 8px;
    padding-bottom: 8px;
    text-align: center;
    text-transform: uppercase;
  }

  .home-calendar-grid {
    gap: 8px;
  }

  .home-calendar-day {
    background: #f8fafc;
    border: 1px solid #e5ebf2;
    border-radius: 8px;
    min-height: 104px;
    padding: 8px;
  }

  .home-calendar-day.muted {
    background: #ffffff;
    opacity: .45;
  }

  .home-calendar-day.today {
    border-color: #248afd;
    box-shadow: 0 0 0 3px rgba(36, 138, 253, .12);
  }

  .home-calendar-number {
    align-items: center;
    color: #24324b;
    display: flex;
    font-size: .78rem;
    font-weight: 900;
    justify-content: space-between;
    margin-bottom: 6px;
  }

  .home-calendar-event {
    border: 0;
    border-radius: 5px;
    color: #24324b;
    display: block;
    font-size: .68rem;
    font-weight: 800;
    line-height: 1.2;
    margin-top: 4px;
    overflow: hidden;
    padding: 4px 6px;
    text-align: left;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 100%;
  }

  .home-calendar-event:hover {
    filter: brightness(.97);
  }

  .home-calendar-event.holiday { background: #e8f8f1; color: #0f7a54; }
  .home-calendar-event.birthday { background: #e8f2ff; color: #1d4ed8; }
  .home-calendar-event.anniversary { background: #fff4d8; color: #8a5a00; }
  .home-calendar-event.new-hire { background: #f1ecff; color: #5b21b6; }
  .home-calendar-event.request { background: #ffe9ef; color: #b4234c; }

  .home-calendar-more {
    background: transparent;
    border: 0;
    color: #64748b;
    display: block;
    font-size: .68rem;
    font-weight: 900;
    margin-top: 5px;
    padding: 0;
  }

  .home-calendar-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .home-calendar-legend span {
    align-items: center;
    color: #64748b;
    display: inline-flex;
    font-size: .72rem;
    font-weight: 800;
    gap: 5px;
  }

  .home-calendar-legend i {
    border-radius: 50%;
    display: inline-block;
    height: 8px;
    width: 8px;
  }

  .legend-holiday { background: #19a974; }
  .legend-birthday { background: #248afd; }
  .legend-anniversary { background: #ffb020; }
  .legend-new-hire { background: #7c3aed; }
  .legend-request { background: #ef476f; }

  .home-modal-events {
    display: grid;
    gap: 10px;
  }

  .home-modal-event {
    border: 1px solid #e5ebf2;
    border-radius: 8px;
    padding: 10px 12px;
  }

  .home-modal-event strong,
  .home-modal-event small {
    display: block;
  }

  .home-modal-event strong {
    color: #24324b;
    font-weight: 850;
  }

  .home-modal-event small {
    color: #64748b;
    font-weight: 800;
    margin-top: 3px;
  }

  .home-mini-table {
    margin-bottom: 0;
  }

  .home-empty {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    color: #64748b;
    font-weight: 700;
    margin: 0;
    padding: 14px;
  }

  @media (max-width: 1199.98px) {
    .home-stat-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 767.98px) {
    .home-hero {
      align-items: flex-start;
      flex-direction: column;
    }
    .home-actions {
      justify-content: flex-start;
    }
    .home-stat-grid,
    .home-split {
      grid-template-columns: 1fr;
    }
    .home-calendar-grid {
      gap: 6px;
    }
    .home-calendar-day {
      min-height: 78px;
      padding: 6px;
    }
    .home-calendar-event {
      font-size: .62rem;
      padding: 3px 4px;
    }
  }
</style>
@endsection

@section('content')
@php
  $employee = auth()->user()->employee;
  $pendingTotal = collect($personal_pending_requests)->sum();
  $todayTimeIn = $attendance_now && $attendance_now->time_in ? date('h:i A', strtotime($attendance_now->time_in)) : null;
  $todayTimeOut = $attendance_now && $attendance_now->time_out ? date('h:i A', strtotime($attendance_now->time_out)) : null;
@endphp

<div class="main-panel">
  @if($employee->status != "Inactive")
    <div class="content-wrapper home-dashboard">
      <div class="home-hero">
        <div>
          <h3>Welcome, {{ $employee->first_name }}</h3>
          <p>{{ date('l, F d, Y') }} snapshot for your attendance, requests, team, and people updates.</p>
        </div>
        <div class="home-actions">
          <a href="{{ url('/file-leave') }}" class="btn btn-primary btn-sm">File Leave</a>
          @if(checkUserAllowedOvertime(auth()->user()->id) == 'yes' || optional(auth()->user()->allowed_overtime)->allowed_overtime == 'on')
            <a href="{{ url('/overtime') }}" class="btn btn-outline-primary btn-sm">Overtime</a>
          @endif
          <a href="{{ url('/official-business') }}" class="btn btn-outline-primary btn-sm">Official Business</a>
        </div>
      </div>

      <div class="home-stat-grid">
        <div class="home-stat success">
          <span class="home-label">Today</span>
          <strong class="home-value">{{ $personal_attendance_summary['today_status'] }}</strong>
          <span class="home-note">
            @if($todayTimeIn)
              In {{ $todayTimeIn }}@if($todayTimeOut) - Out {{ $todayTimeOut }}@endif
            @else
              No attendance log yet
            @endif
          </span>
        </div>
        <div class="home-stat">
          <span class="home-label">Last 7 Days</span>
          <strong class="home-value">{{ $personal_attendance_summary['days_present'] }}/{{ count($date_ranges) }}</strong>
          <span class="home-note">{{ $personal_attendance_summary['completed_logs'] }} completed logs, {{ $personal_attendance_summary['missing_days'] }} missing days</span>
        </div>
        <div class="home-stat warning">
          <span class="home-label">Leave Balance</span>
          <strong class="home-value">VL {{ number_format(data_get($personal_leave_balances, 'vl', 0), 2) }}</strong>
          <span class="home-note">SL {{ number_format(data_get($personal_leave_balances, 'sl', 0), 2) }} available</span>
        </div>
        <div class="home-stat danger">
          <span class="home-label">Pending Requests</span>
          <strong class="home-value">{{ $pendingTotal }}</strong>
          <span class="home-note">Leave {{ $personal_pending_requests['Leaves'] }}, OT {{ $personal_pending_requests['Overtime'] }}, OB {{ $personal_pending_requests['Official Business'] }}</span>
        </div>
      </div>

      <div class="home-layout">
          <div class="home-panel">
            <div class="home-panel-body">
              <div class="home-panel-title">
                <div>
                  <span>Calendar</span>
                  <h4>{{ date('F Y') }}</h4>
                </div>
                <div class="home-calendar-legend">
                  <span><i class="legend-holiday"></i>Holiday</span>
                  <span><i class="legend-birthday"></i>Birthday</span>
                  <span><i class="legend-anniversary"></i>Anniversary</span>
                  <span><i class="legend-new-hire"></i>New Hire</span>
                  <span><i class="legend-request"></i>Request</span>
                </div>
              </div>
              <div class="home-calendar-head">
                <div>Sun</div>
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div>Sat</div>
              </div>
              <div class="home-calendar-grid">
                @foreach($calendarDays as $calendarDay)
                  <div class="home-calendar-day {{ $calendarDay->is_current_month ? '' : 'muted' }} {{ $calendarDay->is_today ? 'today' : '' }}">
                    <div class="home-calendar-number">
                      <span>{{ $calendarDay->day }}</span>
                      @if($calendarDay->is_today)
                        <span class="home-badge info">Today</span>
                      @endif
                    </div>
                    @foreach($calendarDay->events as $event)
                      <button type="button" class="home-calendar-event {{ $event->type }}" data-toggle="modal" data-target="#calendar_day_{{ str_replace('-', '', $calendarDay->date) }}" title="{{ $event->label }}: {{ $event->title }}">
                        {{ $event->title }}
                      </button>
                    @endforeach
                    @if($calendarDay->extra_events > 0)
                      <button type="button" class="home-calendar-more" data-toggle="modal" data-target="#calendar_day_{{ str_replace('-', '', $calendarDay->date) }}">+{{ $calendarDay->extra_events }} more</button>
                    @endif
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          @if(count($employees_under) > 0)
            <div class="home-panel">
              <div class="home-panel-body">
                <div class="home-panel-title">
                  <div>
                    <span>Manager View</span>
                    <h4>Subordinates Today</h4>
                  </div>
                  <span class="home-badge info">{{ count($employees_under) }} employee(s)</span>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover table-bordered home-mini-table">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Leave Balance</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($employees_under as $emp)
                        @php
                          $attendance = $attendance_employees_by_code->get($emp->employee_number);
                          $leaveWithPay = $emp->approved_leaves_with_pay->where('date_from', date('Y-m-d'))->first();
                          $leaveBalance = $subordinate_leave_balances->get($emp->user_id, ['vl' => 0, 'sl' => 0]);
                        @endphp
                        <tr>
                          <td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
                          <td>
                            @if($leaveWithPay)
                              <span class="home-badge warning">Leave</span>
                            @elseif($attendance && $attendance->time_in)
                              {{ date('h:i A', strtotime($attendance->time_in)) }}
                            @else
                              <span class="text-muted">No log</span>
                            @endif
                          </td>
                          <td>{{ $attendance && $attendance->time_out ? date('h:i A', strtotime($attendance->time_out)) : '-' }}</td>
                          <td>VL {{ number_format($leaveBalance['vl'], 2) }} / SL {{ number_format($leaveBalance['sl'], 2) }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          @endif
      </div>
    </div>
  @endif
</div>

@foreach($calendarDays->where('event_count', '>', 0) as $calendarDay)
  <div class="modal fade" id="calendar_day_{{ str_replace('-', '', $calendarDay->date) }}" tabindex="-1" role="dialog" aria-labelledby="calendar_day_label_{{ str_replace('-', '', $calendarDay->date) }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="calendar_day_label_{{ str_replace('-', '', $calendarDay->date) }}">{{ date('F d, Y', strtotime($calendarDay->date)) }}</h5>
          <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="home-modal-events">
            @foreach($calendarDay->all_events as $event)
              <div class="home-modal-event">
                <span class="home-badge {{ $event->type == 'holiday' ? 'success' : ($event->type == 'request' ? 'warning' : 'info') }}">{{ $event->label }}</span>
                <strong class="mt-2">{{ $event->title }}</strong>
                <small>{{ date('M d, Y', strtotime($calendarDay->date)) }}</small>
              </div>
            @endforeach
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endforeach
@endsection
