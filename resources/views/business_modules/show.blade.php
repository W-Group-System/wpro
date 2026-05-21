@extends('layouts.header')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ url('business-modules') }}" class="btn btn-outline-secondary btn-sm mb-3">Back to Dashboard</a>
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="card-title mb-1">{{ $module['name'] }}</h4>
                                <p class="text-muted mb-0">{{ $module['purpose'] }}</p>
                            </div>
                            <span class="badge badge-primary">Module {{ $module['no'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h5>Workflow Workspace</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Status</th>
                                    <td><span class="badge badge-info">Ready for workflow build-out</span></td>
                                </tr>
                                <tr>
                                    <th>Standard Actions</th>
                                    <td>Create, Read, Update, Delete, Submit, Approve, Reject, Cancel, Convert, Export, Print</td>
                                </tr>
                                <tr>
                                    <th>Audit Trail</th>
                                    <td>ERP audit log table is available for all document changes and financial/stock movements.</td>
                                </tr>
                            </table>
                        </div>
                        <p class="text-muted mt-3 mb-0">The database tables for this workflow are included in the ERP migration. Master data CRUD is available now; transaction screens can be expanded from this workspace module by module.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h5>ERP Groups</h5>
                        @php $moduleMap = $modules->keyBy('slug'); @endphp
                        @foreach($groups as $groupName => $slugs)
                            <strong class="d-block mt-2">{{ $groupName }}</strong>
                            <div class="list-group mb-2">
                                @foreach($slugs as $slug)
                                    @php $navModule = $moduleMap->get($slug); @endphp
                                    @if($navModule)
                                        <a href="{{ url('business-modules/'.$navModule['slug']) }}" class="list-group-item list-group-item-action @if($navModule['slug'] == $module['slug']) active @endif">
                                            {{ $navModule['no'] }}. {{ $navModule['name'] }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
