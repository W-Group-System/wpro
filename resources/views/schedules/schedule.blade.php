@extends('layouts.header')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class='row'>
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Schedules</h4>
                <p class="card-description">
                  @if (checkUserPrivilege('settings_add',auth()->user()->id) == 'yes')
                    <button type="button" class="btn btn-outline-success btn-icon-text" data-toggle="modal" data-target="#newSchedule">
                      <i class="ti-plus btn-icon-prepend"></i>                                                    
                      Add new schedule
                    </button>
                  @endif
                </p>
                <div class="table-responsive">
                  <table class="table table-hover table-bordered tablewithSearch">
                    <thead>
                      <tr>
                        <th>Schedule Name</th>
                        <th>Sunday</th> 
                        <th>Monday</th> 
                        <th>Tuesday</th> 
                        <th>Wednesday</th> 
                        <th>Thursday</th> 
                        <th>Friday</th> 
                        <th>Saturday</th> 
                      </tr>
                    </thead>
                    <tbody>
                  
                        @foreach($schedules as $schedule)
                        <tr class='cursor-pointer' data-toggle="modal" data-target="#editSchedule{{$schedule->id}}">
                            <td>{{$schedule->schedule_name}}</td>
                            <td>
                               <small> {{set_data_final("Sunday",$schedule)}} </small>
                            </td> 
                            <td>
                                <small>  {{set_data_final("Monday",$schedule)}} </small>
                            </td> 
                            <td>
                                <small>  {{set_data_final("Tuesday",$schedule)}} </small>
                            </td> 
                            <td>
                                <small>  {{set_data_final("Wednesday",$schedule)}} </small>
                            </td>
                            <td>
                                <small>  {{set_data_final("Thursday",$schedule)}} </small>
                            </td>
                            <td>
                                <small>  {{set_data_final("Friday",$schedule)}} </small>
                            </td> 
                            <td>
                                <small>   {{set_data_final("Saturday",$schedule)}} </small>
                            </td>
                          </tr>
                          
                        @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          @foreach($schedules as $schedule)
            @include('schedules.edit_schedule')
          @endforeach
          @include('schedules.new_schedule')
          @php
            function set_data_final($data,$schedule)
                {
                    $dataperDay = $schedule->ScheduleData->where('name',$data)->first();
                    if($dataperDay == null)
                    {
                        echo "REST DAY";
                    }
                    else 
                    {
                        echo "Start Time : ".date('h:i a',strtotime($dataperDay->time_in_from))." - ".date('h:i a',strtotime($dataperDay->time_in_to))."<br>";
                        echo "End Time : ".date('h:i a',strtotime($dataperDay->time_out_from))." - ".date('h:i a',strtotime($dataperDay->time_out_to))."<br>";
                        echo "Working Hours : ".number_format($dataperDay->working_hours,1)." hrs<br>";
                    }
                }
            @endphp
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('[name="restday[Monday]"]').on('click', function() {
            
            if ($(this).is(":checked"))
            {
                $("[name='time_in_from[Monday]']").val('').prop('required', false);
                $("[name='time_in_to[Monday]']").val('').prop('required', false);
                $("[name='time_out_from[Monday]']").val('').prop('required', false);
                $("[name='time_out_to[Monday]']").val('').prop('required', false);
                $("[name='working_hours[Monday]']").prop('required', false);
            }
            else
            {
                $("[name='time_in_from[Monday]']").val('07:00').prop('required', true);
                $("[name='time_in_to[Monday]']").val('10:00').prop('required', true);
                $("[name='time_out_from[Monday]']").val('17:30').prop('required', true);
                $("[name='time_out_to[Monday]']").val('20:30').prop('required', true);
                $("[name='working_hours[Monday]']").prop('required', true);
            }
        })

        $('[name="restday[Tuesday]"]').on('click', function() {
            
            if ($(this).is(":checked"))
            {
                $("[name='time_in_from[Tuesday]']").val('').prop('required', false);
                $("[name='time_in_to[Tuesday]']").val('').prop('required', false);
                $("[name='time_out_from[Tuesday]']").val('').prop('required', false);
                $("[name='time_out_to[Tuesday]']").val('').prop('required', false);
                $("[name='working_hours[Tuesday]']").prop('required', false);
            }
            else
            {
                $("[name='time_in_from[Tuesday]']").val('07:00').prop('required', true);
                $("[name='time_in_to[Tuesday]']").val('10:00').prop('required', true);
                $("[name='time_out_from[Tuesday]']").val('17:30').prop('required', true);
                $("[name='time_out_to[Tuesday]']").val('20:30').prop('required', true);
                $("[name='working_hours[Tuesday]']").prop('required', true);
            }
        })

        $('[name="restday[Wednesday]"]').on('click', function() {
            
            if ($(this).is(":checked"))
            {
                $("[name='time_in_from[Wednesday]']").val('').prop('required', false);
                $("[name='time_in_to[Wednesday]']").val('').prop('required', false);
                $("[name='time_out_from[Wednesday]']").val('').prop('required', false);
                $("[name='time_out_to[Wednesday]']").val('').prop('required', false);
                $("[name='working_hours[Wednesday]']").prop('required', false);
            }
            else
            {
                $("[name='time_in_from[Wednesday]']").val('07:00').prop('required', true);
                $("[name='time_in_to[Wednesday]']").val('10:00').prop('required', true);
                $("[name='time_out_from[Wednesday]']").val('17:30').prop('required', true);
                $("[name='time_out_to[Wednesday]']").val('20:30').prop('required', true);
                $("[name='working_hours[Wednesday]']").prop('required', true);
            }
        })

        $('[name="restday[Thursday]"]').on('click', function() {
            
            if ($(this).is(":checked"))
            {
                $("[name='time_in_from[Thursday]']").val('').prop('required', false);
                $("[name='time_in_to[Thursday]']").val('').prop('required', false);
                $("[name='time_out_from[Thursday]']").val('').prop('required', false);
                $("[name='time_out_to[Thursday]']").val('').prop('required', false);
                $("[name='working_hours[Thursday]']").prop('required', false);
            }
            else
            {
                $("[name='time_in_from[Thursday]']").val('07:00').prop('required', true);
                $("[name='time_in_to[Thursday]']").val('10:00').prop('required', true);
                $("[name='time_out_from[Thursday]']").val('17:30').prop('required', true);
                $("[name='time_out_to[Thursday]']").val('20:30').prop('required', true);
                $("[name='working_hours[Thursday]']").prop('required', true);
            }
        })

        $('[name="restday[Friday]"]').on('click', function() {
            
            if ($(this).is(":checked"))
            {
                $("[name='time_in_from[Friday]']").val('').prop('required', false);
                $("[name='time_in_to[Friday]']").val('').prop('required', false);
                $("[name='time_out_from[Friday]']").val('').prop('required', false);
                $("[name='time_out_to[Friday]']").val('').prop('required', false);
                $("[name='working_hours[Friday]']").prop('required', false);
            }
            else
            {
                $("[name='time_in_from[Friday]']").val('07:00').prop('required', true);
                $("[name='time_in_to[Friday]']").val('10:00').prop('required', true);
                $("[name='time_out_from[Friday]']").val('17:30').prop('required', true);
                $("[name='time_out_to[Friday]']").val('20:30').prop('required', true);
                $("[name='working_hours[Friday]']").prop('required', true);
            }
        })
    })
</script>
@endsection
