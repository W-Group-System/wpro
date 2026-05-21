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
                                <h4 class="card-title mb-1">{{ $definition['title'] }}</h4>
                                <p class="text-muted mb-0">Create, maintain, search, filter, and manage ERP master records.</p>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createMaster">New Record</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="form-inline mb-3">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm mr-2" placeholder="Search">
                            <select name="status" class="form-control form-control-sm mr-2">
                                <option value="">All Status</option>
                                <option value="active" @if(request('status') == 'active') selected @endif>Active</option>
                                <option value="inactive" @if(request('status') == 'inactive') selected @endif>Inactive</option>
                            </select>
                            <button type="submit" class="btn btn-outline-primary btn-sm mr-2">Filter</button>
                            <a href="{{ url('business-modules/master/'.$master) }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        @foreach($definition['fields'] as $field)
                                            <th>{{ $field['label'] }}</th>
                                        @endforeach
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($records as $record)
                                        <tr>
                                            @foreach($definition['fields'] as $field)
                                                <td>
                                                    @if($field['name'] == 'status')
                                                        <span class="badge badge-{{ $record->{$field['name']} == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($record->{$field['name']} ?? '') }}</span>
                                                    @else
                                                        {{ $record->{$field['name']} }}
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td>
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#editMaster{{ $record->id }}">Edit</button>
                                                <form method="POST" action="{{ url('business-modules/master/'.$master.'/'.$record->id.'/delete') }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($definition['fields']) + 1 }}" class="text-center text-muted">No records found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $records->appends(request()->query())->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('business_modules.partials.master_form', ['modalId' => 'createMaster', 'title' => 'New '.$definition['title'], 'action' => url('business-modules/master/'.$master), 'record' => null])
@foreach($records as $record)
    @include('business_modules.partials.master_form', ['modalId' => 'editMaster'.$record->id, 'title' => 'Edit '.$definition['title'], 'action' => url('business-modules/master/'.$master.'/'.$record->id), 'record' => $record])
@endforeach
@endsection
