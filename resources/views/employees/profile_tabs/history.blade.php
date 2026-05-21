                        <div class="card">
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Notice of Personnel Action
                                            </h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Changed By: </small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small> Changed At: </small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>View Changes</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>Attachment</small>
                                    </div>
                                </div>
                                @foreach ($user->employee->employeeMovement as $movement)
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        {{ optional($movement->user_info)->name ?? 'N/A' }}
                                    </div>
                                    <div class='col-md-3'>
                                        {{date('M d, Y',strtotime($movement->changed_at ))}}
                                    </div>
                                    <div class='col-md-3'>
                                        <a href='#' data-toggle="modal" data-target="#viewNopa{{$movement->id}}">View</a>
                                    </div>
                                    <div class='col-md-3'>
                                        @if ($movement->nopa_attachment)
                                        <a href="{{ url($movement->nopa_attachment) }}" target="_blank">Attachment</a>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @if (checkUserPrivilege('payroll_view',auth()->user()->id) == 'yes')
                        <div class="card">
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Salary Notice of Personnel Action
                                            </h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Changed By: </small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small> Changed At: </small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>View Changes</small>
                                    </div>
                                    <div class='col-md-3'>
                                        <small>Attachment</small>
                                    </div>
                                </div>
                                @foreach ($user->employee->salaryMovement as $movement)
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        {{ optional($movement->change_by)->name ?? 'N/A' }}
                                    </div>
                                    <div class='col-md-3'>
                                        {{date('M d, Y',strtotime($movement->changed_at ))}}
                                    </div>
                                    <div class='col-md-3'>
                                        <a href='#' data-toggle="modal" data-target="#viewSalaryNopa{{$movement->id}}">View</a>
                                    </div>
                                    <div class='col-md-3'>
                                        @if ($movement->salary_nopa_attachment)
                                        <a href="{{ url($movement->salary_nopa_attachment) }}" target="_blank">Attachment</a>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
