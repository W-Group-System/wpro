<div class="modal fade" id="addTrainingModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="card-title">Add Training</h5>
      </div>
      <form action="{{url('add-employee-training')}}" method="post" onsubmit="show()">
        {{csrf_field()}}
        
        <input type="hidden" name="user_id" value="{{$user->id}}">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 mb-2">
              Training:
              <input type="text" name="training" id="training" class="form-control">
            </div>
            <div class="col-md-12 mb-2">
              Start Date:
              <input type="date" name="start_date" class="form-control" max="{{date('Y-m-d')}}" required>
            </div>
            <div class="col-md-12 mb-2">
              End Date:
              <input type="date" name="end_date" class="form-control" min="{{date('Y-m-d')}}" required>
            </div>
            <div class="col-md-12 mb-2">
              Amount:
              <input type="number" name="amount" class="form-control" placeholder="0.00" step=".01" required>
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