@extends('layouts.header')

@section('content')
@php
    $statusLabels = $statusBreakdown->map(function($item) { return $item->status ?: 'No Status'; })->values();
    $statusData = $statusBreakdown->pluck('total')->values();
    $genderLabels = $genderBreakdown->map(function($item) { return $item->gender ?: 'Not Set'; })->values();
    $genderData = $genderBreakdown->pluck('total')->values();
    $companyLabels = $companyHeadcount->map(function($item) {
        return optional($item->company)->company_code ?: (optional($item->company)->company_name ?: 'No Company');
    })->values();
    $companyData = $companyHeadcount->pluck('total')->values();
    $departmentLabels = $departmentHeadcount->map(function($item) {
        return optional($item->department)->name ?: 'No Department';
    })->values();
    $departmentData = $departmentHeadcount->pluck('total')->values();
    $classificationLabels = $classificationHeadcount->map(function($item) {
        return optional($item->classification_info)->name ?: 'Not Classified';
    })->values();
    $classificationData = $classificationHeadcount->pluck('total')->values();
    $leaveStatusLabels = $leaveStatusThisMonth->map(function($item) { return $item->status ?: 'No Status'; })->values();
    $leaveStatusData = $leaveStatusThisMonth->pluck('total')->values();
    $leaveTypeLabels = $leaveTypeThisMonth->map(function($item) {
        return optional($item->leave)->leave_type ?: 'Leave Type '.$item->leave_type;
    })->values();
    $leaveTypeData = $leaveTypeThisMonth->pluck('total')->values();
    $pendingTotal = collect($pendingApprovals)->sum();
@endphp

<div class="main-panel">
    <div class="content-wrapper hr-dashboard">
        <div class="hr-hero">
            <div>
                <span class="hr-kicker">HR Admin Dashboard</span>
                <h2>People operations overview</h2>
                <p>{{ date('F d, Y') }} snapshot for headcount, movement, pending workflows, and employee moments.</p>
            </div>
            <div class="hr-hero-actions">
                <a href="{{ url('/employees') }}" class="btn btn-light btn-sm">Employees</a>
                <a href="{{ url('/leave-report-per-employee') }}" class="btn btn-outline-light btn-sm">Leave Reports</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card hr-stat-card">
                    <div class="card-body">
                        <span>Total Headcount</span>
                        <strong>{{ number_format($totalEmployees) }}</strong>
                        <small>{{ $ratio['active'] }}% active / HBU</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card hr-stat-card accent-green">
                    <div class="card-body">
                        <span>Active Employees</span>
                        <strong>{{ number_format($activeEmployees) }}</strong>
                        <small>{{ number_format($inactiveEmployees) }} inactive or other status</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card hr-stat-card accent-blue">
                    <div class="card-body">
                        <span>New Hires This Month</span>
                        <strong>{{ number_format($newHiresThisMonth) }}</strong>
                        <small>{{ date('M d', strtotime($monthStart)) }} - {{ date('M d', strtotime($monthEnd)) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 stretch-card grid-margin">
                <div class="card hr-stat-card accent-orange">
                    <div class="card-body">
                        <span>Pending HR Actions</span>
                        <strong>{{ number_format($pendingTotal) }}</strong>
                        <small>Leaves, OT, OB, WFH, DTR</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card hr-panel">
                    <div class="card-body">
                        <div class="hr-panel-title">
                            <div>
                                <span>Ratios</span>
                                <h4>Workforce Health</h4>
                            </div>
                        </div>
                        <div class="hr-ratio">
                            <div class="d-flex justify-content-between"><span>Active / HBU</span><strong>{{ $ratio['active'] }}%</strong></div>
                            <div class="progress"><div class="progress-bar bg-success" style="width: {{ $ratio['active'] }}%"></div></div>
                        </div>
                        <div class="hr-ratio">
                            <div class="d-flex justify-content-between"><span>Inactive / Other</span><strong>{{ $ratio['inactive'] }}%</strong></div>
                            <div class="progress"><div class="progress-bar bg-secondary" style="width: {{ $ratio['inactive'] }}%"></div></div>
                        </div>
                        <div class="hr-ratio">
                            <div class="d-flex justify-content-between"><span>Probationary Share</span><strong>{{ $ratio['probationary'] }}%</strong></div>
                            <div class="progress"><div class="progress-bar bg-warning" style="width: {{ $ratio['probationary'] }}%"></div></div>
                        </div>
                        <div class="hr-mini-grid">
                            <div><small>Probationary</small><strong>{{ number_format($probationaryCount) }}</strong></div>
                            <div><small>Resigned This Month</small><strong>{{ number_format($resignedThisMonth) }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card hr-panel">
                    <div class="card-body">
                        <div class="hr-panel-title"><div><span>Headcount</span><h4>Status Mix</h4></div></div>
                        <canvas id="statusChart" height="210"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card hr-panel">
                    <div class="card-body">
                        <div class="hr-panel-title"><div><span>Diversity</span><h4>Gender Ratio</h4></div></div>
                        <canvas id="genderChart" height="210"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card hr-panel">
                    <div class="card-body">
                        <div class="hr-panel-title"><div><span>Distribution</span><h4>Company Headcount</h4></div></div>
                        <canvas id="companyChart" height="150"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card hr-panel">
                    <div class="card-body">
                        <div class="hr-panel-title"><div><span>Distribution</span><h4>Department Headcount</h4></div></div>
                        <canvas id="departmentChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card hr-panel">
                    <div class="card-body">
                        <div class="hr-panel-title"><div><span>Classification</span><h4>Employment Type</h4></div></div>
                        <canvas id="classificationChart" height="180"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card hr-panel">
                    <div class="card-body">
                        <div class="hr-panel-title"><div><span>This Month</span><h4>Leave Status</h4></div></div>
                        <canvas id="leaveStatusChart" height="180"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card hr-panel">
                    <div class="card-body">
                        <div class="hr-panel-title"><div><span>This Month</span><h4>Leave Type Volume</h4></div></div>
                        <canvas id="leaveTypeChart" height="180"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card hr-panel">
                    <div class="card-body">
                        <div class="hr-panel-title"><div><span>Queue</span><h4>Pending Workflows</h4></div></div>
                        <div class="hr-workflow-list">
                            @foreach($pendingApprovals as $label => $count)
                                <div>
                                    <span>{{ $label }}</span>
                                    <strong>{{ number_format($count) }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card hr-panel">
                    <div class="card-body">
                        <div class="hr-panel-title"><div><span>People Moments</span><h4>Birthdays This Month</h4></div></div>
                        <div class="hr-person-list">
                            @forelse($birthdays as $employee)
                                <div class="hr-person">
                                    <img src="{{ URL::asset($employee->avatar) }}" onerror="this.src='{{ URL::asset('/images/no_image.png') }}';">
                                    <div><strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong><small>{{ optional($employee->company)->company_code }} - {{ date('M d', strtotime($employee->birth_date)) }}</small></div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No birthdays this month.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card hr-panel">
                    <div class="card-body">
                        <div class="hr-panel-title"><div><span>People Moments</span><h4>Anniversaries</h4></div></div>
                        <div class="hr-person-list">
                            @forelse($anniversaries as $employee)
                                <div class="hr-person">
                                    <img src="{{ URL::asset($employee->avatar) }}" onerror="this.src='{{ URL::asset('/images/no_image.png') }}';">
                                    <div><strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong><small>{{ optional($employee->company)->company_code }} - {{ date('M d, Y', strtotime($employee->original_date_hired)) }}</small></div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No anniversaries this month.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card hr-panel">
                    <div class="card-body">
                        <div class="hr-panel-title"><div><span>Movement</span><h4>Recent New Hires</h4></div><a href="{{ url('/employees') }}" class="btn btn-outline-primary btn-sm">View Employees</a></div>
                        <div class="hr-person-list">
                            @forelse($newHires as $employee)
                                <div class="hr-person">
                                    <img src="{{ URL::asset($employee->avatar) }}" onerror="this.src='{{ URL::asset('/images/no_image.png') }}';">
                                    <div><strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong><small>{{ $employee->position ?: 'No position' }} - {{ date('M d, Y', strtotime($employee->original_date_hired)) }}</small></div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No new hires in the last 30 days.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card hr-panel">
                    <div class="card-body">
                        <div class="hr-panel-title"><div><span>Attention</span><h4>Probationary Employees</h4></div></div>
                        <div class="hr-person-list">
                            @forelse($probationaryEmployees as $employee)
                                <div class="hr-person">
                                    <img src="{{ URL::asset($employee->avatar) }}" onerror="this.src='{{ URL::asset('/images/no_image.png') }}';">
                                    <div><strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong><small>{{ optional($employee->company)->company_code }} - hired {{ date('M d, Y', strtotime($employee->original_date_hired)) }}</small></div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No active probationary employees.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hr-dashboard .card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(31, 45, 61, .06);
    }
    .hr-hero {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
        padding: 28px;
        border-radius: 10px;
        color: #fff;
        background: linear-gradient(120deg, #1f5f8f, #248afd);
        box-shadow: 0 12px 32px rgba(31, 95, 143, .22);
    }
    .hr-hero h2 {
        color: #fff;
        font-size: 1.9rem;
        font-weight: 800;
        margin: 6px 0;
    }
    .hr-hero p {
        margin: 0;
        color: rgba(255,255,255,.84);
    }
    .hr-kicker,
    .hr-panel-title span {
        display: block;
        color: #248afd;
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .hr-kicker {
        color: rgba(255,255,255,.86);
    }
    .hr-hero-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .hr-stat-card .card-body {
        min-height: 132px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border-left: 5px solid #248afd;
    }
    .hr-stat-card span,
    .hr-mini-grid small {
        color: #718096;
        font-size: .78rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .hr-stat-card strong {
        color: #1f2d3d;
        font-size: 2rem;
        font-weight: 800;
    }
    .hr-stat-card small {
        color: #667085;
        font-weight: 700;
    }
    .hr-stat-card.accent-green .card-body { border-left-color: #19a974; }
    .hr-stat-card.accent-blue .card-body { border-left-color: #248afd; }
    .hr-stat-card.accent-orange .card-body { border-left-color: #ff9f1c; }
    .hr-panel-title {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    .hr-panel-title h4 {
        color: #1f2d3d;
        font-size: 1.05rem;
        font-weight: 800;
        margin: 4px 0 0;
    }
    .hr-ratio {
        margin-bottom: 18px;
    }
    .hr-ratio span,
    .hr-ratio strong {
        color: #334155;
        font-weight: 800;
    }
    .hr-ratio .progress {
        height: 8px;
        margin-top: 8px;
        border-radius: 999px;
    }
    .hr-mini-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
    .hr-mini-grid div,
    .hr-workflow-list div {
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fbff;
    }
    .hr-mini-grid strong {
        display: block;
        color: #1f2d3d;
        font-size: 1.25rem;
        font-weight: 800;
    }
    .hr-workflow-list {
        display: grid;
        gap: 10px;
    }
    .hr-workflow-list div {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .hr-workflow-list span {
        color: #334155;
        font-weight: 800;
    }
    .hr-workflow-list strong {
        color: #248afd;
        font-size: 1.2rem;
        font-weight: 900;
    }
    .hr-person-list {
        display: grid;
        gap: 12px;
        max-height: 360px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .hr-person {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
    }
    .hr-person img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #eef5ff;
    }
    .hr-person strong,
    .hr-person small {
        display: block;
    }
    .hr-person strong {
        color: #1f2d3d;
        font-weight: 800;
    }
    .hr-person small {
        color: #667085;
        font-weight: 700;
    }
    @media (max-width: 767.98px) {
        .hr-hero {
            flex-direction: column;
        }
        .hr-mini-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        var chartColors = ['#248afd', '#19a974', '#ff9f1c', '#7c3aed', '#ef476f', '#06b6d4', '#64748b', '#84cc16'];

        function chartData(labels, data) {
            return {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: chartColors,
                    borderWidth: 0
                }]
            };
        }

        function doughnutChart(id, labels, data) {
            new Chart(document.getElementById(id), {
                type: 'doughnut',
                data: chartData(labels, data),
                options: {
                    responsive: true,
                    legend: { position: 'bottom' },
                    cutoutPercentage: 62
                }
            });
        }

        function barChart(id, labels, data, horizontal) {
            new Chart(document.getElementById(id), {
                type: horizontal ? 'horizontalBar' : 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: '#248afd',
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    legend: { display: false },
                    scales: {
                        xAxes: [{ ticks: { beginAtZero: true, precision: 0 }, gridLines: { color: '#eef2f7' } }],
                        yAxes: [{ ticks: { beginAtZero: true, precision: 0 }, gridLines: { color: '#eef2f7' } }]
                    }
                }
            });
        }

        doughnutChart('statusChart', {!! json_encode($statusLabels) !!}, {!! json_encode($statusData) !!});
        doughnutChart('genderChart', {!! json_encode($genderLabels) !!}, {!! json_encode($genderData) !!});
        barChart('companyChart', {!! json_encode($companyLabels) !!}, {!! json_encode($companyData) !!}, true);
        barChart('departmentChart', {!! json_encode($departmentLabels) !!}, {!! json_encode($departmentData) !!}, true);
        doughnutChart('classificationChart', {!! json_encode($classificationLabels) !!}, {!! json_encode($classificationData) !!});
        doughnutChart('leaveStatusChart', {!! json_encode($leaveStatusLabels) !!}, {!! json_encode($leaveStatusData) !!});
        barChart('leaveTypeChart', {!! json_encode($leaveTypeLabels) !!}, {!! json_encode($leaveTypeData) !!}, false);
    });
</script>
@endsection
