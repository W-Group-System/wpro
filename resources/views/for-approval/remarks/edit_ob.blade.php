<div class="modal fade" id="edit_ob-{{$ob->id}}" tabindex="-1" role="dialog" aria-labelledby="approvedLeaveremarks" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvedLeaveremarks">Are you sure you want to Edit this Official Business?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method='POST' action='hr-edit-ob/{{$ob->id}}' onsubmit="btnApprove.disabled = true; return true;" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group row">
                        <div class='align-self-center col-md-2 text-right'>
                          Date
                        </div>
                        <div class='col-md-4'>
                          <input type="date" name='applied_date' class="form-control" value="{{ $ob->applied_date }}" v-model="ob_date"  @change="validateDates" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class='align-self-center col-md-2 text-right'>
                          Date From
                        </div>
                        <div class='col-md-4'>
                          <input type="datetime-local" name='date_from' class="form-control" value="{{ $ob->date_from }}" v-model="start_time" :min="min_date" :max="ob_max_date" class="form-control" @change="validateDates" :value="start_time" required>
                        </div>
                        <div class='align-self-center col-md-2 text-right'>
                           Date To
                        </div>
                        <div class='col-md-4'>
                          <input type="datetime-local" name='date_to' class="form-control" value="{{ $ob->date_to }}" v-model="end_time"  :min="start_time" :max="max_date" @change="validateDates"  class="form-control" :value="end_time" required>
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
