                      <div class="card p-4">
                        <div class="template-demo">
                          <div class='row m-2'>
                            <div class='col-md-12 text-center mt-3 mb-3'>
                              <h3>Employee Documents
                                <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#empDocsModal">
                                  <i class="fa fa-plus"></i>
                                </button>
                              </h3>
                            </div>
                          </div>
                          @php
                            $documentTypes = documentTypes();
                          @endphp
                          @foreach ($documentTypes as $key=>$docs)
                            <div class="row">
                              <div class="col-md-4 border border-1 border-secondary border-top-bottom border-left-right" style="width: 100%;">
                                {{$docs}}
                              </div>
                                <div class="col-md-4 border border-1 border-secondary border-top-bottom border-left-right" style="width: 100%;">
                                  @php
                                    $empty = false;
                                  @endphp
                                  @foreach ($employeeDocuments as $item)
                                    @if($key === $item->document_type)
                                      Passed
                                      @php
                                        $empty = true;
                                      @endphp
                                    @endif
                                  @endforeach
                                  @if(!$empty)
                                    Not Yet Submitted
                                  @endif
                                </div>
                              <div class="col-md-4 border border-1 border-secondary border-top-bottom border-left-right" style="width: 100%;">
                                @foreach ($employeeDocuments as $item)
                                  @if($key == $item->document_type)
                                    <a href="{{url($item->file_path)}}" target="_blank">{{$item->file_name}}</a>
                                  @endif
                                @endforeach
                              </div>
                            </div>
                          @endforeach
                        </div>
                      </div>
