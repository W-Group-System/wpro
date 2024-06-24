<div class="modal fade" id="addTrainingModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="card-title">Add Training</h5>
      </div>
      <form action="{{url('add-employee-training')}}" method="post" enctype="multipart/form-data">
        {{csrf_field()}}

        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 mb-2">
              Employee:
              <select name="employee" class="form-control required js-example-basic-single" style="width: 100%" required>
                <option value="">-Employee-</option>
                @foreach ($employee as $emp)
                  <option value="{{$emp->id}}">{{$emp->employee_code}} - {{$emp->last_name.', '.$emp->first_name.' '.$emp->middle_name}}</option>
                @endforeach
              </select>
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
              <input type="number" name="amount" class="form-control" value="{{$et->amount}}" required>
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