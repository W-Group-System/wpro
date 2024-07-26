<div class="modal fade" id="addTrainingModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="card-title">Add Training</h5>
      </div>
      <form action="{{url('add-employee-training')}}" method="post" onsubmit="show()"  enctype="multipart/form-data">
        {{csrf_field()}}
        
        <input type="hidden" name="user_id" value="{{$user->id}}">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 mb-2">
              Training:
              <input type="text" name="training" id="training" class="form-control">
            </div>
            <div class="col-md-6 mb-2">
              Training Start Date:
              <input type="date" name="start_date" class="form-control"  required>
            </div>
            <div class="col-md-6 mb-2">
              Training End Date:
              <input type="date" name="end_date" class="form-control"  required>
            </div>
            <div class="col-md-6 mb-2">
              Bond Start Date:
              <input type="date" name="bond_start_date" class="form-control"required>
            </div>
            <div class="col-md-6 mb-2">
              Bond End Date:
              <input type="date" name="bond_end_date" class="form-control" required>
            </div>
            <div class="col-md-12 mb-2">
              Amount:
              <input type="number" name="amount" class="form-control" placeholder="0.00" step=".01" required>
            </div>
            <div class="col-md-12 mb-2">
              Training Supporting Documents:
              <input type="file" name="file" class="form-control" required>
            </div>
            <div class="col-md-12 mb-2">
               Training Certificate:
              <input type="file" name="training_attachment" class="form-control" >
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
          <button class="btn btn-primary" type="submit">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>