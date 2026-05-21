                      <div class="card p-2">
                        <div class="template-demo">
                          <div class='row m-2'>
                            <div class='col-md-12 text-center mt-3 mb-3'>
                              <h3>Training
                                <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#addTrainingModal">
                                  <i class="fa fa-plus"></i>
                                </button>
                              </h3>
                            </div>
                          </div>
                          <div class="table-responsive">
                            <table class="table table-bordered table-hover tablewithSearch">
                                <thead>
                                <tr>
                                    <th>Training</th>
                                    <th>Training Period</th>
                                    <th>Bond Period</th>
                                    <th>Attachment</th>
                                    <th>Certificate</th>
                                    <th>Amount</th>
                                    <!-- <th>Action</th> -->
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($employeeTraining) > 0)
                                    @foreach ($employeeTraining as $et)
                                    <tr>
                                        <td>{{$et->training}}</td>
                                        <td>{{date('M. d, Y', strtotime($et->start_date))}} - {{date('M. d, Y', strtotime($et->end_date))}}</td>
                                        <td>{{date('M. d, Y', strtotime($et->bond_start_date))}} - {{date('M. d, Y', strtotime($et->bond_end_date))}}</td>
                                        <td> @if ($et->attachment)
                                            <a href="{{ url($et->attachment) }}" target="_blank">Attachment</a>
                                            @endif
                                        </td>
                                        <td> @if ($et->training_attachment)
                                            <a href="{{ url($et->training_attachment) }}" target="_blank">Certificate</a>
                                            @endif
                                        </td>
                                        <td><span>&#8369;</span>{{ number_format($et->amount,2)}}</td>
                                        <!-- <td></td> -->
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                    <td colspan="7" class="text-center">No data available.</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
