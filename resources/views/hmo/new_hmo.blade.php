<div class="modal fade" id="newHmo" tabindex="-1" role="dialog" aria-labelledby="newHolidaylabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newHmolabel">New Proof of Availment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method='POST' action='new-hmo' onsubmit='show()' method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class='col-md-12 form-group'>
                            Name  
                            <input type="text" name='employee_name' class="form-control" value="{{ auth()->user()->name }}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class='col-md-12 form-group'>
                            Email  
                            <input type="text" name='email' class="form-control" value="{{ auth()->user()->email }}" readonly>
                        </div>
                    </div>
                    <input type="text" name='company' class="form-control" value="{{ auth()->user()->employee->company->company_name }}" hidden>
                    <input type="text" name='department' class="form-control" value="{{ auth()->user()->employee->department->name }}" hidden>
                    <div class="row">
                        <div class='col-md-12 form-group'>
                            Date of Actual HMO Availment
                            <input type="date" name='date_availment' class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class='col-md-12 form-group'>
                            Proof of Availment  
                            <input type="file" class="form-control attachments" name="path[]" id="path" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
                            <span><small>i.e. LOA (Letter of Authorization), hospital/clinic appointment slip or referral form, availment slip, or similar documents.</small></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form> 
        </div>
    </div>
</div>