<div class="modal fade" id="editModal-{{$file->id}}">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="card-title">Edit NTE</h5>
      </div>
      <form action="{{url('update-nte/'.$file->id)}}" method="post" enctype="multipart/form-data">
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
              Violation:
              <textarea name="violation" rows="5" class="form-control" required>{{$file->violation}}</textarea>
            </div>
            <div class="col-md-12 mb-2">
              Files:
              <input type="file" name="file" class="form-control">
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