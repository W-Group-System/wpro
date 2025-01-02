@extends('layouts.header')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
      <div class="row">
        <div class="col-md-4 grid-margin grid-margin-md-0 ">
          <div class="card">
            <div class="card-body text-center">
              <div>
                <img src="{{get_avatar($resignEmployee->employee->id)}}" onerror="this.src='{{ URL::asset('/images/no_image.png') }}';" class="img-lg rounded-circle mb-2" alt="profile image"/>
                <h4>{{$resignEmployee->employee->last_name}}, {{$resignEmployee->employee->first_name}}</h4>
                <p class="text-muted mb-0">{{$resignEmployee->employee->position}}</p>
              </div>
        
              <a href="https://clearance.wsystem.online{{$resignEmployee->resignation_letter}}" target='_blank'><button class="btn btn-success btn-sm mt-3 mb-4">Resignation Letter</button></a>
              <a href="https://clearance.wsystem.online{{$resignEmployee->acceptance_letter}}"  target='_blank'><button type="button" class="btn btn-danger btn-sm mt-3 mb-4">Acceptance Letter</button></a>
      
              <div class="border-top pt-3">
                <div class="row">
                  <div class="col-12">
                    <b><h5>Employment Information</h5></b>
                  </div>
                  <hr>
                </div>
                <div class='row text-left'>
                  <div class='col-md-12'>
                      Employee ID: {{$resignEmployee->employee->employee_code}}

                  </div>
                  {{-- <div class='col-md-12'>
                      Name: {{$resignEmployee->employee->last_name}}, {{$resignEmployee->employee->first_name}}
                  </div> --}}
                  <div class='col-md-12'>
                      Company: {{$resignEmployee->company->company_code}}
                  </div>
                  <div class='col-md-12'>
                      Department: {{$resignEmployee->department->name}}
                  </div>
                  <div class='col-md-12'>
                      Date Hired: {{date('M d, Y',strtotime($resignEmployee->date_hired))}}
                  </div>
                  <div class='col-md-12'>
                      Last date: <b>{{date('M d, Y',strtotime($resignEmployee->last_date))}}</b>
                  </div>

                </div>
                <hr>
                <div class="row ">
                  <div class="col-12">
                    <b><h5>Employee Contact Information</h5></b>
                  </div>
                  <hr>
                </div>
                <div class='row text-left'>
                  <div class='col-md-12'>
                      Personal Email Address: {{$resignEmployee->personal_email}}

                  </div>
                  <div class='col-md-12'>
                      Personal Phone #: {{$resignEmployee->personal_number}}
                  </div>
                  <div class='col-md-12'>
                    Address: {{$resignEmployee->address}}
                  </div>
                  

                </div>
                <hr>
                
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-8 grid-margin grid-margin-md-0 stretch-card">
          <div class="card">
            <div class="card-body">
              @php
              $total = 0;
              $cleared = 0;
          @endphp
          @foreach($resignEmployee->exit_clearance as $exit)
              @foreach($exit->signatories as $signatory)
                  @php
                      $total++;
                  @endphp
              @endforeach
              @php
                  $cleared = $cleared+count(($exit->signatories)->where('status','Cleared'));
                  // dd($cleared);
              @endphp
          @endforeach
          @if($cleared != 0)
              @php
                  $cleared = number_format(($cleared/$total)*100);
              @endphp
          @endif
          <div class="row ">
            <div class="col-12">
              <b><h5>Final Pay</h5></b>
            </div>
            <hr>
          </div>
          <div class='row text-left'>
            <div class='col-md-12'>
                Status: <span class='badge badge-warning'>Ongoing Computation</span>

            </div>
            <div class='col-md-12'>
                Assigned Personel: Marimar Carlos
            </div>
            <div class='col-md-12'>
                Last Update: {{date('F d, Y H:m a')}}
            </div>
            

          </div>
          <hr>
              <h4 class="card-title">Clearance 
                <div class="progress progress-md mt-2">
                  <div class="progress-bar @if($cleared > 80) bg-success @else bg-danger @endif progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{$cleared}}%" aria-valuenow="{{$cleared}}" aria-valuemin="0" aria-valuemax="100">{{$cleared}}%</div>
                </div>
              </h4>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th class="pt-1 ps-0">
                        Department
                      </th>
                      <th class="pt-1">
                        Checklist
                      </th>
                      <th class="pt-1">
                        Signatories
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($resignEmployee->exit_clearance as $exit)
                              <tr>
                                  
                                  <td >
                                    
                                  @if($exit->department_id == "immediate_sup")
                                  Immediate Sup
                                  @elseif($exit->department_id == "dep_head")
                                  Dept Head
                                  @endif
                                  @if($exit->department)
                                          {{ $exit->department->name }}
                                      @endif
                                      <br>
                                      <a href="{{url('view-comments/'.$exit->id)}}" target="_blank">
                                        <span class="text-nowrap mb-2 d-inline-block">
                                          <i class="mdi mdi-comment-multiple-outline text-muted"></i>
                                          <b>{{count($exit->comments)}}</b> Comments
                                        </span>
                                      </a>
                                      </td>
                                  <td>
                                    
                                      <small>
                                        
                                          @foreach($exit->checklists as $checklist)
                                          <div class="badge badge-pill mb-1  @if($checklist->status == "Pending") badge-danger @else badge-success @endif" data-toggle="modal" data-target="#checklistStatus{{$checklist->id}}">{{$checklist->status}}</div> {{$checklist->checklist}} <br>
                                        @endforeach
                                      </small>

                                  </td>
                                  <td>
                                      
                                      <small>
                                          @foreach($exit->signatories as $signatory)
                                              @if($signatory->status == "Pending")
                                              <div class="badge badge-pill badge-danger mb-1">{{$signatory->status}}</div>
                                              @else
                                              <div class="badge badge-white badge-success mb-1">{{$signatory->status}}</div>
                                              @endif
                                              {{$signatory->employee->last_name}}, {{$signatory->employee->first_name}} 
                                              <br>
                                          @endforeach
                                      </small>

                                  </td>
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
@endsection
