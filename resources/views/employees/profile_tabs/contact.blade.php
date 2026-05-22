                        <div class="card">
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Contact Person (In case of Emergency)
                                                @if (checkUserPrivilege('employees_edit',auth()->user()->id) == 'yes')
                                                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Edit Contact Person" data-toggle="modal" data-target="#editContactInfo"><i class="fa fa-pencil"></i></button>
                                                @endif
                                            </h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Contact Person </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->employee->contact_person ? $user->employee->contact_person->name : ""}}
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Contact Number </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->employee->contact_person ? $user->employee->contact_person->contact_number : ""}}
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Relation </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->employee->contact_person ? $user->employee->contact_person->relation : ""}}
                                    </div>
                                </div>
                            </div>
                        </div>
