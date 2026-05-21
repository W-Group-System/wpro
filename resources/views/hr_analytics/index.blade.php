@extends('layouts.header')

@section('content')
@php
    $departmentLabels = $departmentAnalytics->take(8)->pluck('department_name')->values();
    $departmentTotals = $departmentAnalytics->take(8)->pluck('resignations')->values();
    $departmentRates = $departmentAnalytics->take(8)->pluck('turnover_rate')->values();
    $monthlyLabels = $monthlyTrend->pluck('label')->values();
    $monthlyTotals = $monthlyTrend->pluck('total')->values();
    $reasonLabels = $reasonBreakdown->take(8)->pluck('reason')->values();
    $reasonTotals = $reasonBreakdown->take(8)->pluck('total')->values();
@endphp

<div class="main-panel">
    <div class="content-wrapper hr-analytics">
        <div class="analytics-hero">
            <div>
                <span class="analytics-kicker">HR Analytics Report</span>
                <h2>Resignation insights by department</h2>
                <p>{{ date('M d, Y', strtotime($from)) }} to {{ date('M d, Y', strtotime($to)) }} resignation movement by last working date and turnover signals.</p>
            </div>
            <div class="analytics-hero-actions">
                <a href="{{ url('/dashboard-hr') }}" class="btn btn-light btn-sm">HR Dashboard</a>
                <a href="{{ url('/employees') }}" class="btn btn-outline-light btn-sm">Employees</a>
            </div>
        </div>

        <div class="card analytics-filter-card grid-margin">
            <div class="card-body">
                <form method="GET" action="{{ url('/hr-analytics') }}" class="analytics-filters" onsubmit="show();">
                    <div class="form-group mb-0">
                        <label>From</label>
                        <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
                    </div>
                    <div class="form-group mb-0">
                        <label>To</label>
                        <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
                    </div>
                    <div class="form-group mb-0">
                        <label>Department</label>
                        <select name="department_id" class="form-control form-control-sm js-example-basic-single" style="width:100%;">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" @if((string) $departmentId === (string) $department->id) selected @endif>
                                    {{ $department->name }}@if($department->code) - {{ $department->code }}@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="analytics-filter-actions">
                        <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                        <a href="{{ url('/hr-analytics') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card analytics-stat">
                    <div class="card-body">
                        <span>Total Resignations</span>
                        <strong>{{ number_format($totalResignations) }}</strong>
                        <small>Based on exit last date</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card analytics-stat accent-green">
                    <div class="card-body">
                        <span>Active Headcount</span>
                        <strong>{{ number_format($activeEmployees) }}</strong>
                        <small>Active and HBU employees</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card analytics-stat accent-orange">
                    <div class="card-body">
                        <span>Turnover Rate</span>
                        <strong>{{ $turnoverRate }}%</strong>
                        <small>Resignations vs active headcount</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card analytics-stat accent-red">
                    <div class="card-body">
                        <span>Most Resigning Dept.</span>
                        <strong class="analytics-stat-text">{{ $topDepartment ? $topDepartment->department_name : 'None' }}</strong>
                        <small>{{ $topDepartment ? number_format($topDepartment->resignations).' resignation(s)' : 'No resignation data' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card analytics-panel">
                    <div class="card-body">
                        <div class="analytics-panel-title">
                            <div>
                                <span>Department Ranking</span>
                                <h4>Where resignations are highest</h4>
                            </div>
                        </div>
                        <canvas id="departmentResignationChart" height="130"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 grid-margin stretch-card">
                <div class="card analytics-panel">
                    <div class="card-body">
                        <div class="analytics-panel-title">
                            <div>
                                <span>Monthly Trend</span>
                                <h4>Resignation volume</h4>
                            </div>
                        </div>
                        <canvas id="monthlyTrendChart" height="210"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 grid-margin stretch-card">
                <div class="card analytics-panel">
                    <div class="card-body">
                        <div class="analytics-panel-title">
                            <div>
                                <span>Reason</span>
                                <h4>Separation reason</h4>
                            </div>
                        </div>
                        <canvas id="reasonChart" height="210"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card analytics-panel">
                    <div class="card-body">
                        <div class="analytics-panel-title">
                            <div>
                                <span>Department Details</span>
                                <h4>Resignation count and turnover rate</h4>
                            </div>
                            @if($highestRateDepartment)
                                <div class="analytics-chip">Highest rate: {{ $highestRateDepartment->department_name }} {{ $highestRateDepartment->turnover_rate }}%</div>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Department</th>
                                        <th>Resigned</th>
                                        <th>Active Headcount</th>
                                        <th>Turnover Rate</th>
                                        <th>Latest Resignation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($departmentAnalytics as $department)
                                        <tr>
                                            <td>
                                                <strong>{{ $department->department_name }}</strong>
                                                @if($department->department_code)
                                                    <small class="d-block text-muted">{{ $department->department_code }}</small>
                                                @endif
                                            </td>
                                            <td>{{ number_format($department->resignations) }}</td>
                                            <td>{{ number_format($department->active_headcount) }}</td>
                                            <td>
                                                <div class="analytics-rate">
                                                    <span>{{ $department->turnover_rate }}%</span>
                                                    <div class="progress">
                                                        <div class="progress-bar" style="width: {{ min($department->turnover_rate, 100) }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $department->latest_resignation ? date('M d, Y', strtotime($department->latest_resignation)) : 'No resignation' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No department data for the selected filters.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card analytics-panel">
                    <div class="card-body">
                        <div class="analytics-panel-title">
                            <div>
                                <span>Recent Employees</span>
                                <h4>Latest resignations</h4>
                            </div>
                        </div>
                        <div class="analytics-person-list">
                            @forelse($recentResignations as $resign)
                                @php
                                    $employee = $resign->employee;
                                    $departmentName = optional($resign->department)->name ?: optional(optional($employee)->department)->name;
                                @endphp
                                <div class="analytics-person">
                                    <img src="{{ URL::asset(optional($employee)->avatar) }}" onerror="this.src='{{ URL::asset('/images/no_image.png') }}';">
                                    <div>
                                        <strong>{{ optional($employee)->first_name }} {{ optional($employee)->last_name }}</strong>
                                        <small>{{ $departmentName ?: 'No Department' }} - {{ date('M d, Y', strtotime($resign->last_date)) }}</small>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No resignations found for the selected period.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card analytics-panel">
            <div class="card-body">
                <div class="analytics-panel-title">
                    <div>
                        <span>Employee List</span>
                        <h4>Resigned employees in this report</h4>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered tablewithSearch">
                        <thead>
                            <tr>
                                <th>Employee Code</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Date Hired</th>
                                <th>Last Date</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resignedRecords as $resign)
                                @php
                                    $employee = $resign->employee;
                                    $company = $resign->company ?: optional($employee)->company;
                                    $department = $resign->department ?: optional($employee)->department;
                                    $employeeStatus = optional($employee)->status;
                                    $statusLabel = in_array($employeeStatus, ['Active', 'HBU']) ? ($employeeStatus ?: 'No Status') : 'Resigned';
                                    $statusClass = $statusLabel === 'Resigned' ? 'badge-danger' : 'badge-success';
                                @endphp
                                <tr>
                                    <td>{{ optional($employee)->employee_code ?: optional($employee)->employee_number }}</td>
                                    <td>{{ optional($employee)->last_name }}, {{ optional($employee)->first_name }}</td>
                                    <td>{{ optional($company)->company_code ?: optional($company)->company_name }}</td>
                                    <td>{{ optional($department)->name ?: 'No Department' }}</td>
                                    <td>{{ $resign->position ?: optional($employee)->position }}</td>
                                    <td>{{ $resign->date_hired ? date('M d, Y', strtotime($resign->date_hired)) : (optional($employee)->original_date_hired ? date('M d, Y', strtotime(optional($employee)->original_date_hired)) : '') }}</td>
                                    <td>{{ $resign->last_date ? date('M d, Y', strtotime($resign->last_date)) : '' }}</td>
                                    <td>{{ $resign->reason_for_separation ?: 'No Reason Encoded' }}</td>
                                    <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hr-analytics .card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(31, 45, 61, .06);
    }
    .analytics-hero {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
        padding: 28px;
        border-radius: 10px;
        color: #fff;
        background: linear-gradient(120deg, #164e63, #248afd);
        box-shadow: 0 12px 32px rgba(22, 78, 99, .22);
    }
    .analytics-hero h2 {
        color: #fff;
        font-size: 1.9rem;
        font-weight: 800;
        margin: 6px 0;
    }
    .analytics-hero p {
        margin: 0;
        color: rgba(255,255,255,.84);
    }
    .analytics-kicker,
    .analytics-panel-title span {
        display: block;
        color: #248afd;
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .analytics-kicker {
        color: rgba(255,255,255,.86);
    }
    .analytics-hero-actions,
    .analytics-filter-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .analytics-filters {
        display: grid;
        grid-template-columns: repeat(3, minmax(160px, 1fr)) auto;
        gap: 14px;
        align-items: end;
    }
    .analytics-filters label {
        color: #475569;
        font-size: .76rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .analytics-stat .card-body {
        min-height: 132px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border-left: 5px solid #248afd;
    }
    .analytics-stat span {
        color: #718096;
        font-size: .78rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .analytics-stat strong {
        color: #1f2d3d;
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.1;
    }
    .analytics-stat small {
        color: #667085;
        font-weight: 700;
    }
    .analytics-stat .analytics-stat-text {
        font-size: 1.1rem;
        overflow-wrap: anywhere;
    }
    .analytics-stat.accent-green .card-body { border-left-color: #19a974; }
    .analytics-stat.accent-orange .card-body { border-left-color: #ff9f1c; }
    .analytics-stat.accent-red .card-body { border-left-color: #ef476f; }
    .analytics-panel-title {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    .analytics-panel-title h4 {
        color: #1f2d3d;
        font-size: 1.05rem;
        font-weight: 800;
        margin: 4px 0 0;
    }
    .analytics-chip {
        padding: 7px 10px;
        border-radius: 999px;
        background: #eef6ff;
        color: #1d4ed8;
        font-size: .76rem;
        font-weight: 800;
        white-space: nowrap;
    }
    .analytics-rate span {
        display: block;
        color: #334155;
        font-weight: 800;
        margin-bottom: 6px;
    }
    .analytics-rate .progress {
        height: 7px;
        min-width: 110px;
        border-radius: 999px;
        background: #edf2f7;
    }
    .analytics-rate .progress-bar {
        background: #248afd;
    }
    .analytics-person-list {
        display: grid;
        gap: 12px;
        max-height: 390px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .analytics-person {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
    }
    .analytics-person img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #eef5ff;
    }
    .analytics-person strong,
    .analytics-person small {
        display: block;
    }
    .analytics-person strong {
        color: #1f2d3d;
        font-weight: 800;
    }
    .analytics-person small {
        color: #667085;
        font-weight: 700;
    }
    @media (max-width: 991.98px) {
        .analytics-filters {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 767.98px) {
        .analytics-hero,
        .analytics-panel-title {
            flex-direction: column;
        }
        .analytics-filters {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('footer')
<script>
    $(document).ready(function() {
        var chartColors = ['#248afd', '#19a974', '#ff9f1c', '#ef476f', '#06b6d4', '#64748b', '#84cc16', '#7c3aed'];

        new Chart(document.getElementById('departmentResignationChart'), {
            type: 'horizontalBar',
            data: {
                labels: {!! json_encode($departmentLabels) !!},
                datasets: [
                    {
                        label: 'Resignations',
                        data: {!! json_encode($departmentTotals) !!},
                        backgroundColor: '#248afd',
                        borderWidth: 0
                    },
                    {
                        label: 'Turnover %',
                        data: {!! json_encode($departmentRates) !!},
                        backgroundColor: '#19a974',
                        borderWidth: 0
                    }
                ]
            },
            options: {
                responsive: true,
                legend: { position: 'bottom' },
                scales: {
                    xAxes: [{ ticks: { beginAtZero: true, precision: 0 }, gridLines: { color: '#eef2f7' } }],
                    yAxes: [{ gridLines: { color: '#eef2f7' } }]
                }
            }
        });

        new Chart(document.getElementById('monthlyTrendChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyLabels) !!},
                datasets: [{
                    label: 'Resignations',
                    data: {!! json_encode($monthlyTotals) !!},
                    backgroundColor: 'rgba(36, 138, 253, .12)',
                    borderColor: chartColors[0],
                    borderWidth: 3,
                    pointBackgroundColor: chartColors[0],
                    pointRadius: 4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                legend: { display: false },
                scales: {
                    yAxes: [{ ticks: { beginAtZero: true, precision: 0 }, gridLines: { color: '#eef2f7' } }],
                    xAxes: [{ gridLines: { display: false } }]
                }
            }
        });

        new Chart(document.getElementById('reasonChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($reasonLabels) !!},
                datasets: [{
                    data: {!! json_encode($reasonTotals) !!},
                    backgroundColor: chartColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                legend: { position: 'bottom' }
            }
        });
    });
</script>
@endsection
