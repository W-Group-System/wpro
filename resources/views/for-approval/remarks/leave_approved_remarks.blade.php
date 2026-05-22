<div class="modal fade" id="leave-approved-remarks-{{$leave->id}}" tabindex="-1" role="dialog" aria-labelledby="approvedLeaveremarks" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvedDTRremarks">Are you sure you want to Approve this Leave?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method='POST' action='approve-leave/{{$leave->id}}' onsubmit="btnApprove.disabled = true; return true;" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if($leave->leave_type == 1 && empty($leave->turnover_list))
                    <div class="alert alert-danger">
                        <strong><i class="ti-alert"></i> Turnover List Required</strong><br>
                        This Vacation Leave cannot be approved until the employee uploads a turnover list.
                        Please ask the employee to upload it first.
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="badge badge-success mt-1">Approved</h4>
                        </div>
                        <input type="hidden" name="status" value="Approved">
                        <div class='col-md-12 form-group'>
                            Remarks:
                            <textarea class="form-control" name="approval_remarks" id="" cols="30" rows="5" placeholder="Input Approval Remarks"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    @if($leave->leave_type == 1 && empty($leave->turnover_list))
                        <button type="submit" name="btnApprove" class="btn btn-success" disabled title="Upload turnover list first">Approve</button>
                    @else
                        <button type="submit" name="btnApprove" class="btn btn-success">Approve</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
