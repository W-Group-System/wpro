                        <div class="card">
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Beneficiaries
                                                @if (checkUserPrivilege('employees_edit',auth()->user()->id) == 'yes')
                                                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Edit Beneficiaries" data-toggle="modal" data-target="#editBeneficiaries"><i class="fa fa-pencil"></i></button>
                                                @endif
                                            </h3>
                                        </strong>
                                    </div>
                                </div>
                                
                                @foreach($user->employee->beneficiaries as $key => $value)
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small>{{$value->relation}}</small>
                                    </div>
                                    <div class='col-md-9'>
                                        <small>{{$value->first_name . ' ' . $value->last_name}}</small>
                                    </div>
                                </div>
                                @endforeach                            
                            </div>
                        </div>
