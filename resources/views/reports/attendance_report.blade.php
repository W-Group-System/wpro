@extends('layouts.header')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Attendance Reports</h4>
                    <p class="card-description">
                    <form method='GET' action="{{ route('reports') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class='col-md-4'>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label text-right" for="monthSelect">Month</label>
                                    <div class="col-sm-8">
                                        <select id="monthSelect" name="month" class="selectpicker form-control" data-live-search="true" title="Choose a month">
                                            <option value="01">January</option>
                                            <option value="02">February</option>
                                            <option value="03">March</option>
                                            <option value="04">April</option>
                                            <option value="05">May</option>
                                            <option value="06">June</option>
                                            <option value="07">July</option>
                                            <option value="08">August</option>
                                            <option value="09">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class='col-md-4'>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label text-right" for="yearSelect">Year</label>
                                    <div class="col-sm-8">
                                        <select id="yearSelect" name="year" class="selectpicker form-control" data-live-search="true" title="Choose a year">
                                            <!-- Dynamically populate year options with JavaScript -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class='col-md-4'>
                                <button type="submit" id="submitBtn" class="form-control btn btn-primary mb-2 btn-sm">Generate</button>
                            </div>
                        </div>
                    </form>
                    </p>
                    <div class="col-12">
                        <h3 id="reportTitle"></h3><br>
                        <label><b>I. Tardiness</b></label>
                        <table class="table table-hover table-bordered tablewithSearch">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Company</th>
                                    <th>Name</th>
                                    <th>No. of Days with Tardiness</th>
                                    <th>Remarks/ Recommendation</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                $tardinessData = $data->filter(function ($item) {
                                    return $item->late_min > 0;
                                });
                            @endphp
                            @foreach($tardinessData as $tardiness)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $tardiness->company->company_code }}</td>
                                    <td>{{ $tardiness->name }}</td>
                                    <td>{{ $tardiness->tardiness_days }}</td>
                                    <td>{{ $tardiness->remarks }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <!-- Rest of the tables remain unchanged -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Populate year options dynamically
        var startYear = 2000;
        var endYear = new Date().getFullYear();
        for (var i = endYear; i >= startYear; i--) {
            $('#yearSelect').append('<option value="' + i + '">' + i + '</option>');
        }

        // Set the current month and year in the report title
        var currentDate = new Date();
        var currentMonth = currentDate.getMonth(); // 0-based index
        var currentYear = currentDate.getFullYear();
        var monthNames = ["January", "February", "March", "April", "May", "June", 
                          "July", "August", "September", "October", "November", "December"];
        
        var selectedMonth = "{{ $selectedMonth }}";
        var selectedYear = "{{ $selectedYear }}";

        if(selectedMonth && selectedYear) {
            var monthName = monthNames[parseInt(selectedMonth) - 1];
            var reportTitle = monthName + ' ' + selectedYear;
            $('#reportTitle').text(reportTitle);
        } else {
            $('#reportTitle').text(monthNames[currentMonth] + ' ' + currentYear);
        }

        $('#submitBtn').on('click', function(event) {
            var selectedMonth = $('#monthSelect').val();
            var selectedYear = $('#yearSelect').val();
            
            if(selectedMonth && selectedYear) {
                var monthName = monthNames[parseInt(selectedMonth) - 1];
                var reportTitle = monthName + ' ' + selectedYear;
                $('#reportTitle').text(reportTitle);
            } else {
                alert('Please select both month and year.');
                event.preventDefault();
            }
        });
    });
</script>
@endsection
