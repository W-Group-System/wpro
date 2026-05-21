@extends('layouts.header')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class='row'>
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Attendances</h4>
                <p class="card-description">
                  <form method='get' onsubmit='show();'  enctype="multipart/form-data">
                  <div class=row>
                    <div class='col-md-3'>
                      <div class="form-group row">
                        <label class="col-sm-4 col-form-label text-right">From</label>
                        <div class="col-sm-8">
                          <input type="date" value='{{$from_date}}' class="form-control" name="from" max='{{date('Y-m-d')}}' onchange='get_min(this.value);' required/>
                        </div>
                      </div>
                    </div>
                    <div class='col-md-3'>
                      <div class="form-group row">
                        <label class="col-sm-4 col-form-label text-right">To</label>
                        <div class="col-sm-8">
                          <input type="date" value='{{$to_date}}'  class="form-control"  id='to' name="to" required/>
                        </div>
                      </div>
                    </div>
                    <div class='col-md-3'>
                      <button type="submit" class="btn btn-primary mb-2">Submit</button>
                    </div>
                  </div>
                  </form>
                </p>
                <div class="table-responsive">
                    <table border="1" class="table table-hover table-bordered employee_attendance" id='employee_attendance'>
                        <thead>
                            {{-- <tr>
                                <td colspan='5'>{{$emp->emp_code}} - {{$emp->first_name}} {{$emp->last_name}}</td>
                            </tr> --}}
                            <tr>                                <th>Employee #</th>
                                <th>Name</th>
                                <th>Log Date</th>
                                <th>Shift</th>
                                <th>IN</th>
                                <th>OUT</th>
                                <th>ABS</th>
                                <th>LV W/ PAY</th>
                                <th>REG HRS</th>
                                <th>LATE (min)</th>
                                <th>Undertime (min)</th>
                                <th>REG OT</th>
                                <th>REG ND</th>
                                <th>REG OT ND</th>
                                <th>RST OT</th>
                                <th>RST OT > 8</th>
                                <th>RST ND</th>
                                <th>RST ND > 8</th>
                                <th>LH OT</th>
                                <th>LH OT > 8</th>
                                <th>LH ND</th>
                                <th>LH ND > 8</th>
                                <th>SH OT</th>
                                <th>SH OT > 8</th>
                                <th>SH ND</th>
                                <th>SH ND > 8</th>
                                <th>RST LH OT</th>
                                <th>RST LH OT > 8</th>
                                <th>RST LH ND</th>
                                <th>RST LH ND > 8</th>
                                <th>RST SH OT</th>
                                <th>RST SH OT > 8</th>
                                <th>RST SH ND</th>
                                <th>RST SH ND > 8</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>

                        
                        <tbody>
    @include('attendances.partials.detailed_rows', ['attendance_groups' => $attendance_groups, 'show_company_column' => false])
</tbody>
                    </table>
                </div>
                </div>
              </div>
            </div>
          </div>
        
        </div>
    </div>
</div>
@php
function night_difference($start_work,$end_work)
{
    $start_night = mktime('22','00','00',date('m',$start_work),date('d',$start_work),date('Y',$start_work));
    $end_night   = mktime('06','00','00',date('m',$start_work),date('d',$start_work) + 1,date('Y',$start_work));

    if($start_work >= $start_night && $start_work <= $end_night)
    {
        if($end_work >= $end_night)
        {
            return ($end_night - $start_work) / 3600;
        }
        else
        {
            return ($end_work - $start_work) / 3600;
        }
    }
    elseif($end_work >= $start_night && $end_work <= $end_night)
    {
        if($start_work <= $start_night)
        {
            return ($end_work - $start_night) / 3600;
        }
        else
        {
            return ($end_work - $start_work) / 3600;
        }
    }
    else
    {
        if($start_work < $start_night && $end_work > $end_night)
        {
            return ($end_night - $start_night) / 3600;
        }
        return 0;
    }
}

@endphp
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">

<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script>
  function get_min(value)
  {
    document.getElementById("to").min = value;
  }
  $(document).ready(function() 
    {
        new DataTable('.employee_attendance', 
        {
            // pagelenth:25,
            fixedColumns: {
                leftColumns: 1,  // 'start' and 'end' have been replaced with 'leftColumns' for clarity
            },
            paginate:false,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'excel'
            ],
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }],
            order: [] 
        });
    });
</script>
@endsection
