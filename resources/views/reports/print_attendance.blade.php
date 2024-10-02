<div class="row">
    <div class="col-md-12" align="center" style="margin-top: -30px">
        <img src='{{ asset('images/wgroup.png')}}' width='150px'>
        <h2 style="margin-top: 0px"><b>Attendance Report of {{$from}} to {{$to}}</b></h2>
    </div>
</div>

<div class="col-12">
    <label><b>I. Tardiness</b></label>
    <table class="table table-bordered" width="100%" border="1" cellspacing="0" cellpadding="0" style="margin-bottom: 10px;">
        <thead>
            <tr>
                <th>No.</th>
                <th>Company</th>
                <th>Name</th>
                <th>No. of Days</th>
                <th>Remarks/ Recommendation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tardinessData as $index => $tardiness)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $tardiness['company_code'] }}</td>
                    <td>{{ $tardiness['name'] }}</td>
                    <td>{{ $tardiness['tardiness_days'] }}</td>
                    <td>Excessive; for NOD issuance</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <label><b>II. Undertime</b></label>
    <table class="table table-bordered" width="100%" border="1" cellspacing="0" cellpadding="0" style="margin-bottom: 10px;">
        <thead>
            <tr>
                <th>No.</th>
                <th>Company</th>
                <th>Name</th>
                <th>No. of Days</th>
                <th>Remarks/ Recommendation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($undertimeData as $index => $undertime)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $undertime['company_code'] }}</td>
                    <td>{{ $undertime['name'] }}</td>
                    <td>{{ $undertime['undertime_days'] }}</td>
                    <td>Excessive; for NOD issuance</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <label style="margin-bottom: 20px;"><b>II. Leaves</b></label><br>
    <label>A. Leave without Pay</label>
    <table class="table table-bordered" width="100%" border="1" cellspacing="0" cellpadding="0" style="margin-bottom: 10px;">
        <thead>
            <tr>
                <th>No.</th>
                <th>Company</th>
                <th>Name</th>
                <th>No. of LWOP days</th>
                <th>Reason</th>
                <th>Remarks/ Recommendation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaveWithoutData as $index => $withoutLeave)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $withoutLeave['company_code'] }}</td>
                    <td>{{ $withoutLeave['name'] }}</td>
                    <td>{{ $withoutLeave['no_lwop_days'] }}</td>
                    <td></td>
                    <td>No leave Credits balance</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <label><b>III. Overtime</b></label>
    <table class="table table-bordered" width="100%" border="1" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th>No.</th>
                <th>Company</th>
                <th>Regular Working Hours</th>
                <th>Overtime Hours Total</th>
                <th>% of Overtime</th>
                <th>Remarks/ Recommendation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($overtimeData as $index => $overtime)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $overtime['company_code'] }}</td>
                    <td>{{ number_format($overtime['total_reg_hrs'], 2) }}</td>
                    <td>{{ number_format($overtime['total_ot'], 2) }}</td>
                    <td>{{ number_format($overtime['percent_overtime'], 2) }}%</td>
                    <td>{{ $overtime['remarks'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table class="table" style="margin-top: 10em">
        <tr>
            <td>_____________________________</td>
        </tr>
        <tr>
            <td align="center">Prepared by</td>
        </tr>
    </table>
</div>

<style>
    @page {
        size: a4 portrait; 
    }
    body {
        background-color: #FFF;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: black;
    }
</style>


