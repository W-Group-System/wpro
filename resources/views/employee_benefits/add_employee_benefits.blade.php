<div class="modal fade" id="addEmployeeBenefitsModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="card-title">Add Employee Benefits</h5>
      </div>
      <form action="{{url('add-employee-benefits')}}" method="post" enctype="multipart/form-data" onsubmit="show()">
        {{csrf_field()}}

        <input type="hidden" name="user_id" value="{{$user->id}}">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 mb-2">
              @php
                $benefits = benefits();
              @endphp
              Benefits:
              <select name="benefits" class="form-control required js-example-basic-single" style="width: 100%" required>
                <option value="">-Benefits-</option>
                @foreach ($benefits as $key=>$b)
                  <option value="{{$key}}">{{$b}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-12 mb-2">
              Amount:
              <input type="number" name="amount" class="form-control" placeholder="0.00" required>
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