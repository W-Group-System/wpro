                        <div class="card p-5">
                          <div class="template-demo">
                            <div class='row m-2'>
                                <div class='col-md-12 text-center mt-3 mb-3'>
                                    <strong>
                                        <h3>Employee Benefits
                                          <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#addEmployeeBenefitsModal">
                                            <i class="fa fa-plus"></i>
                                          </button>
                                        </h3>
                                    </strong>
                                </div>
                            </div>
                            <div class="table-responsive">
                              <table class="table table-hover table-bordered tablewithSearch">
                                <thead>
                                  <tr>
                                    <th>Employee Name</th>
                                    <th>Benefits</th>
                                    <th>Amount</th>
                                    <th>Date Posted</th>
                                    <th>Posted By</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach ($employeeBenefits as $eb)
                                    <tr>
                                      <td>{{$eb->user->name}}</td>
                                      <td>
                                        @switch($eb->benefits_name)
                                            @case('SL')
                                              Salary Loan
                                                @break
                                            @case('EA')
                                              Educational Assistance
                                                @break
                                            @case('WG')
                                              Wedding Gift
                                                @break
                                            @case('BA')
                                              Bereavement Assistance
                                                @break
                                            @case('HMO')
                                              Health Card (HMO)
                                                @break
                                            @default
                                        @endswitch
                                      </td>
                                      <td><span>&#8369;</span>{{$eb->amount}}</td>
                                      <td>{{date('M. d, Y', strtotime($eb->date))}}</td>
                                      <td>{{$eb->postedBy->name}}</td>
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
