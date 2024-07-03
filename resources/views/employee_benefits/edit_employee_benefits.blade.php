<div class="modal fade" id="editModal-{{$eb->id}}">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="card-title">Edit Employee Benefits</h5>
      </div>
      <form action="{{url('update-employee-benefits/'.$eb->id)}}" method="post" enctype="multipart/form-data" onsubmit="show()">
        {{csrf_field()}}

        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 mb-2">
              Employee:
              <select name="employee" class="form-control required js-example-basic-single" style="width: 100%" required>
                <option value="">-Employee-</option>
                @foreach ($employee as $emp)
                  <option value="{{$emp->id}}" {{$emp->id==$eb->id?'selected':''}}>{{$emp->employee_code}} - {{$emp->last_name.', '.$emp->first_name.' '.$emp->middle_name}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-12 mb-2">
              Benefits:
              <select name="benefits" class="form-control required js-example-basic-single" style="width: 100%" required>
                <option value="">-Benefits-</option>
                @foreach ($benefits as $key=>$b)
                  <option value="{{$key}}" {{$key==$eb->benefits_name?'selected':''}}>{{$b}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-12 mb-2">
              Amount:
              <input type="number" name="amount" class="form-control" placeholder="0.00" value="{{$eb->amount}}" required>
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