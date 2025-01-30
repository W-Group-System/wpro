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
        
              @if(($for_clearances->department_id == "immediate_sup") || ($for_clearances->department_id == "dept_head"))
                  <a href="https://clearance.wsystem.online{{$resignEmployee->resignation_letter}}" target='_blank'><button class="btn btn-success btn-sm mt-3 mb-4">Resignation Letter</button></a>
                  <a href="https://clearance.wsystem.online{{$resignEmployee->acceptance_letter}}"  target='_blank'><button type="button" class="btn btn-danger btn-sm mt-3 mb-4">Acceptance Letter</button></a>
              @endif
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
                {{-- <hr>
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
                  

                </div> --}}
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4 grid-margin grid-margin-md-0 ">
          <div class="card">
            <div class="card-body text-left">
                <h6>@if($for_clearances->department_id == "immediate_sup")
                    Immediate Head
                    @elseif($for_clearances->department_id == "dep_head")
                    Department Head
                    @endif
                    @if($for_clearances->clearance->department)
                            {{ $for_clearances->clearance->department->name }}
                        @endif 
                        (Checklist)</h6>
                </div>
                <div class="card-body ">
                    <div class='row'>
                        <div class='col-md-12'>
                            <small>
                                @foreach($for_clearances->clearance->checklists as $checklist)
                                    {{-- <input type='checkbox' onclick="return false;"> --}}
                                    <span class="mb-2 badge badge-white btn @if($checklist->status == "Pending") btn-danger @else btn-success @endif" data-toggle="modal" data-target="#checklistStatus{{$checklist->id}}">{{$checklist->status}}</span>{{$checklist->checklist}} <br>
                                    @if(in_array(auth()->user()->employee->id, $exitClearanceIds))
                                    {{-- @include('clearances.checklist_change_status') --}}
                                    @endif
                                @endforeach
                                
                            </small>

                        </div>
                        

                    </div>
                </div>
          </div>
          <div class="card">
            <div class="card-body text-left">
                <h6>Signatories in 
                    @if($for_clearances->department_id == "immediate_sup")
                    Immediate Head
                    @elseif($for_clearances->department_id == "dep_head")
                    Department Head
                    @endif
                    @if($for_clearances->clearance->department)
                            {{ $for_clearances->clearance->department->name }}
                        @endif</h6>
                </div>
                <div class="card-body ">
                <div class='row'>
                    <div class='col-md-12'>
                        <ul class="icon-data-list">
                            @foreach($for_clearances->clearance->signatories as $signatory)
                            <li>
                              <div class="d-flex">
                                <img src="{{get_avatar($signatory->employee->id)}}" onerror="this.src='{{ URL::asset('/images/no_image.png') }}';"  class="rounded-circle img-thumbnail avatar-sm @if($signatory->status == "Pending") border-danger @else border-success @endif" alt="profile-image">
                                <div>
                                  <p class="text-info mb-1"> {{$signatory->employee->last_name}}, {{$signatory->employee->first_name}} @if($signatory->employee->user_id == auth()->user()->id) (You) @endif</p>
                                  <p class="mb-0">{{$signatory->employee->position}}</p>
                                  {{-- <small>9:30 am</small> --}}
                                </div>
                              </div>
                            </li>
                            @endforeach
                          </ul>
                        
                    </div>
                </div>
                @if(Request::route()->getName() != "Comments")
           
                {{-- @if(auth()->user()->employee->id != $resignEmployee->employee->id) --}}
                    @if(in_array(auth()->user()->employee->id, $exitClearanceIds))
                        @if($for_clearances->status == "Pending")
                        <div class='row'>
                            <div class='col-md-12'>
                                <button type="button" class="btn btn-success btn-sm align-items-right" data-toggle="modal" data-target="#markasdone">Confirm '<i>{{$resignEmployee->employee->first_name}}</i>' as CLEARED</button>
                            </div>
                        </div>
                    
                        @endif
                    @endif
                {{-- @endif --}}
                @endif
            </div>
          </div>
        </div>
        <div class="col-md-4 grid-margin grid-margin-md-0 ">
            <div class="card">
                <div class="card-body text-left">
                   
                    <form method='post' action="{{url('new-comment/'.$for_clearances->clearance->id)}}" onsubmit='show();' enctype="multipart/form-data">
                        @csrf
                        <h4 class="mt-0 mb-3">Comments ({{count($for_clearances->clearance->comments)}})</h4>
        
                        <textarea class="form-control form-control-light mb-2" placeholder="Write message" id="example-textarea" name='observation' rows="5" required></textarea>
                        <div class="text-end mb-2">
                             
                                <input type="file"  name='file' class="form-control">
                            <div class="btn-group mt-2 mb-2 ms-2">
                                <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                            </div>
                        </div>
                      </form>
                      <div class="profile-feed">
                        @foreach($for_clearances->clearance->comments->sortByDesc('created_at') as $comment)
                        <div class="d-flex align-items-start profile-feed-item">
                          <img src="{{get_avatar($comment->user->employee->id)}}" onerror="this.src='{{ URL::asset('/images/no_image.png') }}';"alt="profile" class="img-sm rounded-circle">
                          <div class="ms-4">
                            <h6>
                                {{$comment->user->name}}
                              <small class="ms-4 text-muted"><i class="ti-time me-1"></i> {{date('h:i A - M d, Y',strtotime($comment->updated_at))}}</small>
                            </h6>
                            <p>
                                {!! $comment->remarks !!}
                            </p>
                           
                          </div>
                        </div>
                        @endforeach
                        
                      </div>
                     
                </div>
            </div>
        </div>
      </div>
      <div class="row">
        
        <div class='col-4'>
            <div class="card">
                <div class="card-body">
               

                </div> <!-- end card-body-->
            </div>
        </div>
    </div>
</div>
@if($for_clearances->status == "Pending")
@if(Request::route()->getName() != "Comments")
@include('clearances.mark_as_done')
@foreach($for_clearances->clearance->checklists as $checklist)
    @if(in_array(auth()->user()->employee->id, $exitClearanceIds))
    @include('clearances.checklist_change_status')
    @endif
@endforeach
@endif
@endif
<script>
function displayFileName(event) {
  const fileInput = event.target;  // The file input element
  const fileName = fileInput.files[0]?.name || 'No file selected';  // Get the file name or default text
  
  // Update the text input with the file name
  document.getElementById('fileName').value = fileName;
}
function required_proof(id,value)
{
  if(value == "Completed")
  {
    document.getElementById("proof"+id).required = true;
  }
else
  {
    document.getElementById("proof"+id).required = false;
  }

}
</script>
@endsection
