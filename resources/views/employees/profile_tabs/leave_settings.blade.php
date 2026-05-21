@php
    $setting = $employeeLeaveSetting;
    $months = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
               7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];

    // Build human-readable current date summaries
    $vlCurrentDate = null;
    $slCurrentDate = null;
    if ($setting) {
        if (!$setting->vl_is_accumulative) {
            if ($setting->vl_credit_month && $setting->vl_credit_day) {
                $vlCurrentDate = ($months[$setting->vl_credit_month] ?? '') . ' ' . $setting->vl_credit_day;
            }
        } else {
            if ($setting->vl_accumulate_day) {
                $vlCurrentDate = 'Every ' . \Carbon\Carbon::createFromFormat('j', $setting->vl_accumulate_day)->format('jS') . ' of the month';
            }
        }
        if (!$setting->sl_is_accumulative) {
            if ($setting->sl_credit_month && $setting->sl_credit_day) {
                $slCurrentDate = ($months[$setting->sl_credit_month] ?? '') . ' ' . $setting->sl_credit_day;
            }
        } else {
            if ($setting->sl_accumulate_day) {
                $slCurrentDate = 'Every ' . \Carbon\Carbon::createFromFormat('j', $setting->sl_accumulate_day)->format('jS') . ' of the month';
            }
        }
    }
@endphp

<div class="profile-about">
    <div class="profile-about-main">
        <div class="profile-panel">
            <div class="profile-panel-header">
                <div>
                    <span class="profile-panel-kicker">Leave Credits</span>
                    <h4>SL and VL Settings</h4>
                </div>
            </div>
            <form method="POST" action="{{ url('account-setting-hr/updateLeaveSettingsHR/'.$user->employee->id) }}" onsubmit="show()">
                @csrf
                <div class="p-3">
                    <div class="row">

                        {{-- VL --}}
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">VL Per Year</label>
                            <input type="number" name="vl_annual_credit" class="form-control form-control-sm" min="0" step="0.001" value="{{ optional($setting)->vl_annual_credit ?: 0 }}">
                            <small class="text-muted">Cron adds VL / 12 every 1st day of the month.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">VL Policy</label>
                            <select name="vl_policy" id="vl_policy" class="form-control form-control-sm" onchange="toggleLeaveFields('vl')">
                                <option value="renew" @if(!optional($setting)->vl_is_accumulative) selected @endif>Renew every year</option>
                                <option value="accumulative" @if(optional($setting)->vl_is_accumulative) selected @endif>Accumulative</option>
                            </select>
                            @if($vlCurrentDate)
                                <div class="mt-1">
                                    <span class="badge badge-info">
                                        <i class="ti-calendar"></i> VL Credit Date: {{ $vlCurrentDate }}
                                    </span>
                                </div>
                            @else
                                <small class="text-warning">No credit date set yet.</small>
                            @endif
                        </div>

                        {{-- VL Renew date --}}
                        <div class="col-md-12 mb-3" id="vl-renew-fields" style="display:none;">
                            <label class="font-weight-bold text-secondary">VL Credit Date (Month &amp; Day)</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <select name="vl_credit_month" class="form-control form-control-sm">
                                        <option value="">-- Month --</option>
                                        @foreach($months as $num => $name)
                                            <option value="{{ $num }}" @if(optional($setting)->vl_credit_month == $num) selected @endif>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Month</small>
                                </div>
                                <div class="col-md-3">
                                    <select name="vl_credit_day" class="form-control form-control-sm">
                                        <option value="">-- Day --</option>
                                        @for($d = 1; $d <= 31; $d++)
                                            <option value="{{ $d }}" @if(optional($setting)->vl_credit_day == $d) selected @endif>{{ $d }}</option>
                                        @endfor
                                    </select>
                                    <small class="text-muted">Day</small>
                                </div>
                            </div>
                        </div>

                        {{-- VL Accumulate day --}}
                        <div class="col-md-12 mb-3" id="vl-accumulate-fields" style="display:none;">
                            <label class="font-weight-bold text-secondary">VL Accumulation Day of Month</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="vl_accumulate_day" class="form-control form-control-sm">
                                        <option value="">-- Day --</option>
                                        @for($d = 1; $d <= 31; $d++)
                                            <option value="{{ $d }}" @if(optional($setting)->vl_accumulate_day == $d) selected @endif>{{ $d }}</option>
                                        @endfor
                                    </select>
                                    <small class="text-muted">Credits accumulate on this day every month.</small>
                                </div>
                            </div>
                        </div>

                        {{-- SL --}}
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">SL Per Year</label>
                            <input type="number" name="sl_annual_credit" class="form-control form-control-sm" min="0" step="0.001" value="{{ optional($setting)->sl_annual_credit ?: 0 }}">
                            <small class="text-muted">Cron adds the full SL value once per year.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">SL Policy</label>
                            <select name="sl_policy" id="sl_policy" class="form-control form-control-sm" onchange="toggleLeaveFields('sl')">
                                <option value="renew" @if(!optional($setting)->sl_is_accumulative) selected @endif>Renew every year</option>
                                <option value="accumulative" @if(optional($setting)->sl_is_accumulative) selected @endif>Accumulative</option>
                            </select>
                            @if($slCurrentDate)
                                <div class="mt-1">
                                    <span class="badge badge-info">
                                        <i class="ti-calendar"></i> SL Credit Date: {{ $slCurrentDate }}
                                    </span>
                                </div>
                            @else
                                <small class="text-warning">No credit date set yet.</small>
                            @endif
                        </div>

                        {{-- SL Renew date --}}
                        <div class="col-md-12 mb-3" id="sl-renew-fields" style="display:none;">
                            <label class="font-weight-bold text-secondary">SL Credit Date (Month &amp; Day)</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <select name="sl_credit_month" class="form-control form-control-sm">
                                        <option value="">-- Month --</option>
                                        @foreach($months as $num => $name)
                                            <option value="{{ $num }}" @if(optional($setting)->sl_credit_month == $num) selected @endif>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Month</small>
                                </div>
                                <div class="col-md-3">
                                    <select name="sl_credit_day" class="form-control form-control-sm">
                                        <option value="">-- Day --</option>
                                        @for($d = 1; $d <= 31; $d++)
                                            <option value="{{ $d }}" @if(optional($setting)->sl_credit_day == $d) selected @endif>{{ $d }}</option>
                                        @endfor
                                    </select>
                                    <small class="text-muted">Day</small>
                                </div>
                            </div>
                        </div>

                        {{-- SL Accumulate day --}}
                        <div class="col-md-12 mb-3" id="sl-accumulate-fields" style="display:none;">
                            <label class="font-weight-bold text-secondary">SL Accumulation Day of Month</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="sl_accumulate_day" class="form-control form-control-sm">
                                        <option value="">-- Day --</option>
                                        @for($d = 1; $d <= 31; $d++)
                                            <option value="{{ $d }}" @if(optional($setting)->sl_accumulate_day == $d) selected @endif>{{ $d }}</option>
                                        @endfor
                                    </select>
                                    <small class="text-muted">Credits accumulate on this day every month.</small>
                                </div>
                            </div>
                        </div>

                    </div>

                    @if (checkUserPrivilege('employees_edit',auth()->user()->id) == 'yes')
                        <button type="submit" class="btn btn-primary btn-sm">Save Leave Settings</button>
                    @else
                        <button type="button" class="btn btn-secondary btn-sm" disabled>Save Leave Settings</button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="profile-about-side">
        <div class="profile-panel">
            <div class="profile-panel-header compact">
                <div>
                    <span class="profile-panel-kicker">Cronjob</span>
                    <h4>Latest Accruals</h4>
                </div>
            </div>
            <div class="table-responsive p-3">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Leave</th>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employeeLeaveAccruals as $accrual)
                            <tr>
                                <td>{{ optional($accrual->leave)->code ?: optional($accrual->leave)->leave_type }}</td>
                                <td>{{ $accrual->year ?: '-' }}</td>
                                <td>{{ $accrual->month ?: '-' }}</td>
                                <td>{{ number_format($accrual->earned_per_month, 3) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No SL/VL accrual records yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function toggleLeaveFields(prefix) {
    var policy = document.getElementById(prefix + '_policy').value;
    document.getElementById(prefix + '-renew-fields').style.display     = (policy === 'renew')        ? 'block' : 'none';
    document.getElementById(prefix + '-accumulate-fields').style.display = (policy === 'accumulative') ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    toggleLeaveFields('vl');
    toggleLeaveFields('sl');
});
</script>
