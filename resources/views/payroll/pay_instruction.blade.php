@extends('layouts.header')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
            @if (count($errors))
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger alert-dismissible fade show " role="alert">
                        <span class="alert-inner--icon"><i class="ni ni-fat-remove"></i></span>
                        <span class="alert-inner--text"><strong>Error!</strong> {{ $error }}</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endforeach
            @endif
        
        <div id="payroll" class="tab-pane  active">
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Payroll Instruction
                    <button type="button" class="btn btn-outline-danger btn-icon-text btn-sm"  data-toggle="modal" data-target="#payrollInstruction">
                        <i class="ti-upload btn-icon-prepend"></i>                                                    
                        Upload
                    </button>
                    
                    <button type="button" class="btn btn-outline-success btn-icon-text btn-sm" data-toggle="modal" data-target="#addInstruction">
                        <i class="ti-plus"></i>  
                    </button>
                </h4>
                <form method='get' onsubmit='show();' enctype="multipart/form-data">
                    <div class=row>
                    </div>
                  </form>
                <div class="table-responsive">
                  <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Location </th>
                            <th>Employee Code</th>
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Benefit Name</th>
                            <th>Amount</th>
                            <th>Frequency</th>
                            <th>Deductible</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($names as $key => $name)
                        <tr>
                            <td>{{$name->location}}</td>
                            <td>{{$name->site_id}}</td>
                            <td>{{$name->name}}</td>
                            <td>{{$name->start_date}}</td>
                            <td>{{$name->end_date}}</td>
                            <td>{{$name->benefit_name}}</td>
                            <td>{{$name->amount}}</td>
                            <td>{{$name->frequency}}</td>
                            <td>{{$name->deductible}}</td>
                            <td>{{$name->remarks}}</td>
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
<script>
    
</script>
@include('payroll.add_instruction')
@include('payroll.upload_pay_instruction')
@endsection


