@extends('layouts.header')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class='row mb-3'>
            <div class='col-lg-2'>
              <div class="card card-tale">
                <div class="card-body">
                  <div class="media">
                    <div class="media-body">
                      <h4 class="mb-4">For Clearance</h4>
                      <a href="{{url('for-clearance?status=Pending')}}"> <h2 class="card-text">{{count($for_clearances->where('status','Pending'))}}</h2></a>
                    </div>
                  </div>
                </div>
              </div>
            </div> 
            <div class='col-lg-2'>
              <div class="card text-success">
                <div class="card-body">
                  <div class="media">
                    <div class="media-body">
                      <h4 class="mb-4">Cleared</h4>
                      <a href="{{url('for-clearance?status=Cleared')}}"><h2 class="card-text">{{count($for_clearances->where('status','Cleared'))}}</h2></a>
                    </div>
                  </div>
                </div>
              </div>
            </div> 
        </div>
        <div class='row'>
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Clearance</h4>
                  <p class="card-description">
            
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered tablewithSearch">
                            <thead>
                                <tr>
                                    <th >Name</th>
                                    <th >Company</th>
                                    <th >Department</th>
                                    <th >Position</th>
                                    <th>Date Started</th>
                                    <th >Effective Date</th>
                                    <th>Signatory as</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($for_clearances->where('status',$status) as $for_clearance)
                                {{-- <tr >
                                    <td><a href='{{url("view-as-signatory/".$for_clearance->id)}}'>{{$for_clearance->clearance->resign->employee->last_name}}, {{$for_clearance->clearance->resign->employee->first_name}}</a></td>
                                    <td>{{$for_clearance->clearance->resign->employee->company->company_code}}</td>
                                    <td>{{$for_clearance->clearance->resign->employee->department->name}}</td>
                                    <td>{{$for_clearance->clearance->resign->position}}</td>
                                    <td>{{$for_clearance->clearance->resign->employee->original_date_hired}}</td>
                                    <td>{{$for_clearance->clearance->resign->last_date}}</td>
                                    <td> 
                                        @if($for_clearance->department_id == "immediate_sup")
                                        Immediate Head
                                        @elseif($for_clearance->department_id == "dep_head")
                                        Department Head
                                        @endif
                                        @if($for_clearance->clearance->department)
                                                {{ $for_clearance->clearance->department->name }}
                                            @endif
                                    </td>
                                    
                                </tr> --}}
                                <tr>
                                  <td>
                                      <a href='{{ url("view-as-signatory/" . $for_clearance->id) }}'>
                                          @if(optional($for_clearance->clearance)->resign && optional($for_clearance->clearance->resign)->employee)
                                              {{ optional($for_clearance->clearance->resign->employee)->last_name }}, 
                                              {{ optional($for_clearance->clearance->resign->employee)->first_name }}
                                          @else
                                              <span class="text-danger">N/A</span>
                                          @endif
                                      </a>
                                  </td>
                                  <td>
                                      {{ optional(optional(optional($for_clearance->clearance)->resign)->employee)->company->company_code ?? 'N/A' }}
                                  </td>
                                  <td>
                                      {{ optional(optional(optional($for_clearance->clearance)->resign)->employee)->department->name ?? 'N/A' }}
                                  </td>
                                  <td>
                                      {{ optional(optional($for_clearance->clearance)->resign)->position ?? 'N/A' }}
                                  </td>
                                  <td>
                                      {{ optional(optional(optional($for_clearance->clearance)->resign)->employee)->original_date_hired ?? 'N/A' }}
                                  </td>
                                  <td>
                                      {{ optional(optional($for_clearance->clearance)->resign)->last_date ?? 'N/A' }}
                                  </td>
                                  <td> 
                                      @if($for_clearance->department_id == "immediate_sup")
                                          Immediate Head
                                      @elseif($for_clearance->department_id == "dep_head")
                                          Department Head
                                      @endif

                                      @if(optional($for_clearance->clearance)->department)
                                          {{ optional($for_clearance->clearance->department)->name }}
                                      @endif
                                  </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                   </p>
                </div>
              </div>
            </div>     
  
          </div>
    </div>
</div>

@endsection
