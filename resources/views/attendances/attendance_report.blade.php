
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="icon" type="image/png" href="{{ asset('/images/icon.png')}}"/>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }}</title>
    <style>
        table { 
            border-spacing: 0;
            border-collapse: collapse;
        }
        body{
          font-family: sans-serif;
            border-collapse: collapse;
        }
        .page-break {
            page-break-after: always;
        }
        .green {
            width:10px;
            border: 10px solid green;
        }
        .violet {
            width:10px;
            border: 10px solid blueviolet;
        }
        .grey {
            width:10px;
            border: 10px solid gray;
        }
        @page {
  margin: 0;
}
@font-face {
        font-family: 'Helvetica';
        font-weight: normal;
        font-style: normal;
        font-variant: normal;
        /* src: url("font url"); */
      }

/* Bottom left text */
.bottom-left {
  position: absolute;
  bottom: 85px;
  left: 200px;
}
.next-bottom-left {
  position: absolute;
  top: 485px;
  left: 280px;
}
.email {
  position: absolute;
  top: 545px;
  left:280px;
}
.contact_person {
  position: absolute;
  top: 622px;
  left:280px;
}
.contact_number {
  position: absolute;
  top: 692px;
  left:280px;
}
.website {
  position: absolute;
  top: 752px;
  left:280px;
}
.qr-code {
  position: absolute;
  top: 1022px;
  left:40;
}

/* Top left text */
.top-left {
  position: absolute;
  top: 8px;
  left: 16px;
}

/* Top right text */
.top-right {
  position: absolute;
  top: 8px;
  right: 16px;
}

/* Bottom right text */
.name {
  position: absolute;
  bottom: 300px;
  left: 10px;
}
.nickname {
  position: absolute;
  bottom: 370px;
  left: 10px;
}
.position {
  position: absolute;
  bottom: 235px;
  left: 10px;
}

/* Centered text */
.centered {
  position: absolute;
  top: 46.5%;
  left: 50.5%;
  transform: translate(-50%, -50%);
}
.container {
  position: relative;
  text-align: center;
  color: white;
}
.page_break { page-break-before: always; }

    </style>
</head>
<body style="margin: 0; padding: 0; text-align: left; ">
  <header>
    <table style='width:100%;' border="1" cellspacing="0" cellpadding="0">
        <tr style='height:90px;'>
            <td  align='center' width='50px' style='width:30%;' rowspan='2'> 
                <img src='{{ asset('/images/wgroup.PNG')}}' width='170px' >
            </td>
            <td class='text-center' colspan='3' >
                <span  style='font-size:29;text-align: center;'><b>Attendance Report</b>
                </span>
            </td>
        </tr>
    </table>
</header>
<div class="col-12">
  <h3 id="reportTitle"></h3> <br>
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
          @foreach($tardinessData as $index => $tardiness)
              <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $tardiness['company_code'] }}</td>
                  <td>{{ $tardiness['name'] }}</td>
                  <td>{{ $tardiness['tardiness_days'] }}</td>
                  <td>{{ $tardiness['remarks'] }}</td>
              </tr>
          @endforeach
      </tbody>
  </table>
  <label style="margin-bottom: 20px;"><b>II. Leaves</b></label><br>
  <label>A. Leave without Pay</label>
  <table class="table table-hover table-bordered tablewithSearch">
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
                  <td>{{ $withoutLeave['leave_data'] }}</td>
                  <td></td>
                  <td>{{ $withoutLeave['remarks'] }}</td>
              </tr>
          @endforeach
      </tbody>
  </table>
  <label>B. Leave Deviations</label>
  <table class="table table-hover table-bordered tablewithSearch">
      <thead>
          <tr>
              <th>No.</th>
              <th>Company</th>
              <th>Name</th>
              <th>Leave Date(s)</th>
              <th>Leave Type</th>
              <th>Remarks/ Recommendation</th>
          </tr>
      </thead>
      <tbody>
      </tbody>
  </table>
  <label>C. Leaves more than 5 consecutive days</label>
  <table class="table table-hover table-bordered tablewithSearch">
      <thead>
          <tr>
              <th>No.</th>
              <th>Company</th>
              <th>Name</th>
              <th>Leave Date(s)</th>
              <th>Leave Type</th>
              <th>Remarks/ Recommendation</th>
          </tr>
      </thead>
      <tbody>
          
      </tbody>
  </table>
  <label><b>III. Overtime</b></label>
  <table class="table table-hover table-bordered tablewithSearch">
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
</div>

</body>
</html>


