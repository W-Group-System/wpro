                      <div class="card p-2">
                        <div class="template-demo">
                          <div class='row m-2'>
                            <div class='col-md-12 text-center mt-3 mb-3'>
                              <h3>Employee NTE
                                <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#uploadNteModal">
                                  <i class="fa fa-plus"></i>
                                </button>
                              </h3>
                            </div>
                          </div>
                          <div class="table-responsive">
                            <table class="table table-hover table-bordered tablewithSearch">
                              <thead>
                                <tr>
                                  <th>File</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach ($employeeNte as $nte)
                                  <tr>
                                    <td>
                                      <a href="{{url($nte->file_path)}}" title="View file" target="_blank">
                                        {{$nte->file_name}}
                                      </a>
                                    </td>
                                  </tr>
                                @endforeach
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
