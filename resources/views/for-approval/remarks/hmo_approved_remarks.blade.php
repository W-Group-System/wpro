<div class="modal fade" id="hmo-approved-remarks-{{$availment->id}}" tabindex="-1" role="dialog" aria-labelledby="approvedOBremarks" aria-hidden="true" onsubmit="show()">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvedOBremarks">Are you sure you want to approve this proof of availment?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method='POST' action='approve-hmo/{{$availment->id}}' onsubmit="btnApprove.disabled = true; return true;" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="status" value="Approved">
                        <div class='col-md-12 form-group'>
                            <label>Remarks:</label>
                            <textarea class="form-control" name="approval_remarks" id="" cols="30" rows="5" placeholder="Input Approval Remarks"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="btnApprove" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>
