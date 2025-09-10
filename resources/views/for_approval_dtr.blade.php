<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Timekeeping Beta</title>
    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    {{-- Datatable CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.min.css">
    {{-- Date Picker CSS --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
    {{-- Bootstrap Icon --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Select2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Or for RTL support -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
</head>

<body>
    <div class="container-fluid p-4">
        <h2>TIMEKEEPING MONITORING</h2>
        
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered mt-5 myTable">
                        <thead>
                            <tr>
                                <th>EMPLOYEE</th>
                                <th>DATE</th>
                                <th>TIME IN</th>
                                <th>TIME OUT</th>
                                <th>REMARKS</th>
                                <th>INCIDENT REPORT</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dtr_corrections as $dtr_correction)
                                <tr>
                                    <td>{{ $dtr_correction->employee->first_name.' '.$dtr_correction->employee->last_name }}</td>
                                    <td>{{ $dtr_correction->date }}</td>
                                    <td>{{ date('h:i A', strtotime($dtr_correction->time_in)) }}</td>
                                    <td>{{ date('h:i A', strtotime($dtr_correction->time_out)) }}</td>
                                    <td>{!! nl2br(e($dtr_correction->remarks)) !!}</td>
                                    <td>
                                        @if($dtr_correction->file)
                                        <a href="{{ url($dtr_correction->file) }}" target="_blank">IR File</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dtr_correction->status == 'Pending')
                                        <span class="badge bg-warning">
                                        @elseif($dtr_correction->status == 'Returned') 
                                        <span class="badge bg-danger">
                                        @elseif($dtr_correction->status == 'Approved') 
                                        <span class="badge bg-success">
                                        @endif
                                            {{ $dtr_correction->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($dtr_correction->status == "Pending")
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#edit{{ $dtr_correction->id }}"><i class="bi bi-pencil-square h3 text-dark"></i></a>
                                        @endif
                                    </td>
                                </tr>

                                @include('action')
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('sweetalert::alert')
    {{-- Jquery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    {{-- Datatable JS --}}
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.min.js"></script>
    {{-- Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous">
    </script>
    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    {{-- Date Picker JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js">
    </script>
    <script>
        $( '.select2' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        } );

        let table = new DataTable('.myTable');
    </script>
</body>

</html>