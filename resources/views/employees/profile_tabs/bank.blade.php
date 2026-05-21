                        <div class="card">
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Bank Details
                                                <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#editAcctNo">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                            </h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> BANK NAME </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->employee->bank_name}}
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> ACCOUNT NUMBER </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->employee->bank_account_number}}
                                    </div>
                                </div>
                            </div>
                        </div>
