@extends('layouts.header')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">Probi Regularization KPI</h4>
                                <p class="text-muted mb-0">Monitor probationary employees, movement to regular, and NOPA attachments.</p>
                            </div>
                            <form method="GET" action="{{ url('kpi/probationary-regularization') }}" class="form-inline mt-3 mt-md-0">
                                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm mr-2" placeholder="Search employee">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="ti-search"></i> Search</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-1">Active Probi Employees</p>
                        <h3 class="mb-0">{{ $probationaryEmployees->total() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-1">Probi to Regular Movements</p>
                        <h3 class="mb-0">{{ $regularizationMovements->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-1">This Month / Missing NOPA</p>
                        <h3 class="mb-0">{{ $currentMonthRegularizations }} / {{ $missingNopaCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Probationary Employees</h4>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Employee No</th>
                                        <th>Name</th>
                                        <th>Company</th>
                                        <th>Department</th>
                                        <th>Date Hired</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($probationaryEmployees as $employee)
                                        <tr>
                                            <td>{{ $employee->employee_number }}</td>
                                            <td>{{ $employee->last_name }}, {{ $employee->first_name }}</td>
                                            <td>{{ optional($employee->company)->company_code ?? optional($employee->company)->name }}</td>
                                            <td>{{ optional($employee->department)->name }}</td>
                                            <td>{{ $employee->original_date_hired ? date('M d, Y', strtotime($employee->original_date_hired)) : '' }}</td>
                                            <td>
                                                <a href="{{ url('account-setting-hr/'.$employee->user_id) }}" class="btn btn-outline-primary btn-sm">Open Profile / NOPA</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No probationary employees found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $probationaryEmployees->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Probi to Regular Movement History</h4>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Employee</th>
                                        <th>Changed By</th>
                                        <th>NOPA File</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($regularizationMovements->take(50) as $movement)
                                        <tr>
                                            <td>{{ $movement->changed_at ? date('M d, Y', strtotime($movement->changed_at)) : '' }}</td>
                                            <td>
                                                @if($movement->employee)
                                                    {{ $movement->employee->last_name }}, {{ $movement->employee->first_name }}
                                                    <br><small class="text-muted">{{ $movement->employee->employee_number }}</small>
                                                @endif
                                            </td>
                                            <td>{{ optional($movement->user_info)->name }}</td>
                                            <td>
                                                @if($movement->nopa_attachment)
                                                    <a href="{{ url($movement->nopa_attachment) }}" target="_blank" class="btn btn-outline-success btn-sm">View NOPA</a>
                                                @else
                                                    <form method="POST" action="{{ url('kpi/probationary-regularization/'.$movement->id.'/upload-nopa') }}" enctype="multipart/form-data" class="form-inline">
                                                        @csrf
                                                        <input type="file" name="file" class="form-control form-control-sm mr-2" required>
                                                        <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No probi to regular movement yet.</td>
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
