@extends('layouts.header')
@section('content')
	<div class="main-panel">
		<div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Audit Logs</h4>
                            <p class="card-description">
                                {{-- <form method="GET" action="" class="mb-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Date Range Filter</label>
                                            <select name="range" class="form-control" onchange="this.form.submit()">
                                                <option value="">-- Select Date Range --</option>

                                                <option value="2025-10-26|2025-11-10" {{ request('range') == '2025-10-26|2025-11-10' ? 'selected' : '' }}>
                                                    Oct 26 → Nov 10, 2025
                                                </option>

                                                <option value="2025-11-11|2025-11-25" {{ request('range') == '2025-11-11|2025-11-25' ? 'selected' : '' }}>
                                                    Nov 11 → Nov 25, 2025
                                                </option>
                                            </select>

                                        </div>
                                    </div>
                                </form> --}}
                            </p>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="audit_logs_table">
                                    <thead>
                                        <tr>
                                            <th>Action By</th>
                                            <th>Before</th>
                                            <th>After</th>
                                            <th>Action Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($audits as $audit)
                                            <tr>
                                                <td>{{ $audit->user->name }}</td>
                                                <td>
                                                    @if($audit->old_values)
                                                        <ul class="mb-0 pl-3">
                                                            @foreach(json_decode($audit->old_values, true) as $key => $value)
                                                                <li>
                                                                    <strong>{{ ucfirst($key) }}:</strong> 
                                                                    @if(strtolower($key) == 'rate') 
                                                                        Confidential
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else 
                                                        None
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($audit->new_values)
                                                        <ul class="mb-0 pl-3">
                                                            @foreach(json_decode($audit->new_values, true) as $key => $value)
                                                                <li>
                                                                    <strong>{{ ucfirst($key) }}:</strong> 
                                                                    @if(strtolower($key) == 'rate') 
                                                                        Confidential
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else 
                                                        None
                                                    @endif
                                                </td>

                                                <td>{{ \Carbon\Carbon::parse($audit->created_at)->format('F d, Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">

<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script>
$(document).ready(function() {
    new DataTable('#audit_logs_table', {
        fixedColumns: {
            leftColumns: 1
        },
        paging: true,       
        pageLength: 10,   
        dom: 'Bfrtip',
        buttons: ['copy', 'excel'],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        order: []
    });
});
</script>

@endsection
