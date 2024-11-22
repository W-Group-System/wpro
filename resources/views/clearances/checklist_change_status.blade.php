<div class="modal fade" id="checklistStatus{{$checklist->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{$checklist->checklist}}</h5>
        </div>
        <form method='post' action="{{url('change-status-checklist/'.$checklist->id)}}" onsubmit='show();' enctype="multipart/form-data">
            <div class="modal-body">
                @csrf
                <div class=row>
                    <div class='col-md-12'>
                        Status
                        <input type='hidden' name='checklist' value='{{$checklist->checklist}}'>
                        <input type='hidden' name='old_status' value='{{$checklist->status}}'>
                        <select name='status' class='form-control'  onchange='required_proof({{$checklist->id}},this.value)' required>
                                <option value=""></option>
                                <option value="Pending" @if($checklist->status == "Pending") selected @endif>Pending</option>
                                <option value="N/A" @if($checklist->status == "N/A") selected @endif>N/A</option>
                                <option value="Completed" @if($checklist->status == "Completed") selected @endif>Completed</option>
                        </select>
                    </div>
                </div>
                <div class=row>
                    <div class='col-md-12'>
                        Proof
                       <input type='file' name='proof' id='proof{{$checklist->id}}' class='form-control' >
                    </div>
                </div>
                <div class=row>
                    <div class='col-md-12'>
                        Remarks
                       <input type='text' name='remarks' class='form-control' required>
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
  