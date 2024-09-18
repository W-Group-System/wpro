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
                      <label for="leave_type" class="col-sm-2 col-form-label">Leave Type</label>
                          <div class="col-sm-4">
                            <select v-on:change="validateLeave" v-model="leave_type" class="form-control"  id="leave_type" style='width:100%;' name='leave_type' required>
                              @foreach ($leave_types as $leave_type)
                                @if($leave_type->code == 'VL')
                                  <option value="{{$leave_type->id}}" {{ $leave_type->id == $leave->leave_type ? 'selected' : ''}}>{{$leave_type->leave_type}}</option>
                                @elseif($leave_type->code == 'SL')
                                  <option value="{{$leave_type->id}}" {{ $leave_type->id == $leave->leave_type ? 'selected' : ''}}>{{$leave_type->leave_type}}</option>
                                @endif
                              @endforeach                  
                            </select>
                          </div>
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
                              <div class='col-md-6'>
                                <label class="form-check-label ">
                                  @if($leave->halfday == 1)
                                      <input id="editViewleaveHalfday" type="checkbox" name="halfday" class="form-check-input" value="1" checked>  
                                  @else
                                      <input id="editViewleaveHalfday" type="checkbox" name="halfday" class="form-check-input" value="0">  
                                  @endif
                                  Halfday
                              </label>
      
                              <br>
                                @if($leave->halfday == 1)
                                  <div class="edithalfDayStatus">
                                    <select name="halfday_status" class="form-control" value="{{$leave->halfday_status}}">
                                        <option value="">Choose One</option>
                                        <option value="First Shift" {{ $leave->halfday_status == 'First Shift' ? 'selected' : ''}}>First Shift</option>
                                        <option value="Second Shift" {{ $leave->halfday_status == 'Second Shift' ? 'selected' : ''}}>Second Shift</option>
                                    </select>
                                  </div>
                                @else
                                <div class="edithalfDayStatus">
                                  <select name="halfday_status" class="form-control">
                                      <option value="">Choose One</option>
                                      <option value="First Shift">First Shift</option>
                                      <option value="Second Shift">Second Shift</option>
                                  </select>
                                </div>
                                @endif
                              </div>
                            </div>
                          </div>
                      </div>
                      <div class="form-group row">
                        <div class='col-md-2'>
                          Date From 
                        </div>
                        <div class='col-md-4'>
                          <input type="date" name='date_from' class="form-control" value="{{$leave->date_from}}" required>
                        </div>
                        <div class='col-md-2'>
                          Date To 
                        </div>
                        <div class='col-md-4'>
                          <input type="date" name='date_to' class="form-control" value="{{$leave->date_to}}" required>
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
