                        <div class="card">
                            <div class="template-demo">
                                <div class='row'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Schedule</h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>Start Time</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>End Time</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>Total hours</small>
                                    </div>
                                </div>
                                @foreach($user->employee->ScheduleData as $schedule)
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small>{{$schedule->name}}</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>{{$schedule->time_in_from}}</small> <br>
                                        <small>{{$schedule->time_in_to}}</small>

                                    </div>
                                    <div class='col-md-3'>
                                        <small>{{$schedule->time_out_from}}</small> <br>
                                        <small>{{$schedule->time_out_to}}</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>{{number_format($schedule->working_hours,1)}} </small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
