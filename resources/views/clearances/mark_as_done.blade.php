<div class="modal fade" id="markasdone" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Tag as Cleared</h5>
        </div>
        <form method='post' action="{{url('mark-as-cleared/'.$for_clearances->id)}}" onsubmit='show();' enctype="multipart/form-data">
            <div class="modal-body">
                @csrf
                <h4><i>Please ensure that all accountability checklist items are reviewed and all necessary documents are uploaded before clearing the resigning employee.</i></h4>
              
                <div class=row>
                    <div class='col-md-12'>
                        Remarks
                       <input type='hidden' name='name' value='{{$resignEmployee->employee->first_name}} {{$resignEmployee->employee->last_name}}' class='form-control' required>
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
  