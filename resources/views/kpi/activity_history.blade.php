@extends('layouts.header')
@section('content')
@php
    $fieldLabels = [
        'department_id' => 'Department',
        'project' => 'Project Name',
        'position' => 'Position',
        'level' => 'Job Level',
        'classification' => 'Employment Status',
        'immediate_sup' => 'Immediate Supervisor',
        'date_from' => 'Date From',
        'date_to' => 'Effective Date',
    ];

    $formatMovementValue = function ($key, $value) use ($classificationMap, $departments, $levels, $users) {
        if ($key == 'department_id') {
            return $departments[$value] ?? $value;
        }
        if ($key == 'classification') {
            return $classificationMap[$value] ?? $value;
        }
        if ($key == 'level') {
            return $levels[$value] ?? $value;
        }
        if ($key == 'immediate_sup') {
            return $users[$value] ?? $value;
        }
        return $value;
    };
@endphp
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">Employee Activity History</h4>
                                <p class="text-muted mb-0">Track employee movement changes and NOPA attachments.</p>
                            </div>
                            <form method="GET" action="{{ url('kpi/activity-history') }}" class="form-inline mt-3 mt-md-0">
                                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm mr-2" placeholder="Search employee">
                                <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm mr-2">
                                <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm mr-2">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="ti-search"></i> Filter</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Employee</th>
                                        <th>Changed By</th>
                                        <th>Changes</th>
                                        <th>NOPA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($movements as $movement)
                                        @php
                                            $oldValues = json_decode($movement->old_values, true) ?: [];
                                            $newValues = json_decode($movement->new_values, true) ?: [];
                                            $keys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
                                        @endphp
                                        <tr>
                                            <td>{{ $movement->changed_at ? date('M d, Y', strtotime($movement->changed_at)) : '' }}</td>
                                            <td>
                                                @if($movement->employee)
                                                    <a href="{{ url('account-setting-hr/'.$movement->employee->user_id) }}">
                                                        {{ $movement->employee->last_name }}, {{ $movement->employee->first_name }}
                                                    </a>
                                                    <br><small class="text-muted">{{ $movement->employee->employee_number }}</small>
                                                @endif
                                            </td>
                                            <td>{{ optional($movement->user_info)->name }}</td>
                                            <td>
                                                @foreach($keys as $key)
                                                    <div class="mb-1">
                                                        <strong>{{ $fieldLabels[$key] ?? ucwords(str_replace('_', ' ', $key)) }}:</strong>
                                                        {{ $formatMovementValue($key, $oldValues[$key] ?? '') }}
                                                        <i class="ti-arrow-right mx-1"></i>
                                                        {{ $formatMovementValue($key, $newValues[$key] ?? '') }}
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($movement->nopa_attachment)
                                                    <a href="{{ url($movement->nopa_attachment) }}" target="_blank">View</a>
                                                @else
                                                    <span class="text-muted">No file</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No activity history found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $movements->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
