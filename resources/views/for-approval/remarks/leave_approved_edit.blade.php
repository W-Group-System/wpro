<div class="modal fade" id="edit_leave-{{$leave->id}}" tabindex="-1" role="dialog" aria-labelledby="approvedLeaveremarks" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvedLeaveremarks">Are you sure you want to Edit this Leave?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method='POST' action='hr-edit-leave/{{$leave->id}}' onsubmit="btnApprove.disabled = true; return true;" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group row">
                          <div class='col-sm-5'>
                            <div class='row'>
                              <div class='col-md-6'>
                                <label class="form-check-label ">
                                  <input type="hidden" v-model="leave_balances" name="leave_balances" :value="leave_balances">
                                  <div>
                                    <label class="form-check-label ">
                                      @if($leave->withpay == 1)
                                        <input type="checkbox" name="withpay" class="form-check-input" :disabled="isAllowedWithPay" checked>  
                                      @else
                                          <input type="checkbox" name="withpay" class="form-check-input" :disabled="isAllowedWithPay">  
                                      @endif
                                        With Pay
                                  </label>
                                  </div>
                              </label>
                              </div>
                            </div>
                          </div>
                      </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="btnApprove" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
