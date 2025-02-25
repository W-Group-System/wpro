@extends('layouts.header')

@section('css_header')
    <link rel="stylesheet" href="{{asset('body_css/vendors/fullcalendar/fullcalendar.min.css')}}">
@endsection

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Plot Leave Calendar</h4>
                            <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target='#new'>
                                <i class="ti-plus"></i>
                                Plan a leave
                            </button>
    
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Plot leaves this month</h4>
                            <hr>

                            @if(count($leave_plans_per_month) > 0)
                                @foreach ($leave_plans_per_month as $leave_plan)
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <small>Date From</small>
                                                </div>
                                                <div class="col-sm-9">
                                                    {{date('M d, Y', strtotime($leave_plan->date_from))}}
                                                </div>
                                                <div class="col-sm-3">
                                                    <small>Date To</small>
                                                </div>
                                                <div class="col-sm-9">
                                                    {{date('M d, Y', strtotime($leave_plan->date_to))}}
                                                </div>
                                                <div class="col-sm-3">
                                                    <small>Reason</small>
                                                </div>
                                                <div class="col-sm-9">
                                                    {!! nl2br(e($leave_plan->reason)) !!}
                                                </div>
                                                <div class="col-sm-3">
                                                    <small>Employee</small>
                                                </div>
                                                <div class="col-sm-9">
                                                    {{$leave_plan->user->name}}
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-lg-2">
                                            <div class="row">
                                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit{{$leave_plan->id}}">
                                                    <i class="ti-pencil-alt"></i>
                                                </button>
                                                <form method="POST" action="{{url('delete_plan_leave/'.$leave_plan->id)}}" class="d-inline-block" onsubmit="show()">
                                                    @csrf 

                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="ti-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div> --}}
                                    </div>
                                    
                                    <hr>

                                @endforeach
                            @else
                                <div style="font-style: italic;" class="text-secondary">No plot leaves</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('leave_calendar.new_leave_calendar')
    @foreach ($leave_plan_array as $leave_plan)
        @include('leave_calendar.edit_leave')
    @endforeach

    <script src="{{asset('body_css/vendors/moment/moment.min.js')}}"></script>
    <script src="{{asset('body_css/vendors/fullcalendar/fullcalendar.min.js')}}"></script>
    <script>
        var leave_plan = {!! json_encode($leave_plan_array) !!}
        
        $('#calendar').fullCalendar({
            defaultView: 'month',
            navLinks: true, // can click day/week names to navigate views
            // editable: true,
            eventLimit: true,
            displayEventTime: false,
            events: leave_plan,
            eventClick: function(calEvent, jsEvent, view)
            {
                $("#edit"+calEvent.leave_calendar_id).modal('show')
            }
        });
    </script>
@endsection