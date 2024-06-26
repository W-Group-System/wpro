<div class="modal fade" id="editModal-{{$tm->id}}">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Tax Mapping</h5>
      </div>
      <form method="POST" action="{{url('update-tax-mapping/'.$tm->id)}}" onsubmit="show()">
        {{csrf_field()}}
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 mb-2">
              Employee:
              <select data-placeholder="Select Employee" name="employee" class="form-control js-example-basic-single" style="width: 100%;" required>
                <option value=""></option>
                @foreach ($employee as $e)
                  <option value="{{$e->id}}" {{$e->id==$tm->employee_id?'selected':''}}>{{$e->employee_code}} - {{$e->first_name.' '.$e->middle_name.', '.$e->last_name}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-2">
              Benefits
              <input type="checkbox" class="ml-2" name="sss" value="1" {{$tm->sss==1?'checked':''}}> SSS
              <input type="checkbox" class="ml-2" name="philhealth" value="1" {{$tm->philhealth==1?'checked':''}}> PhilHealth
              <input type="checkbox" class="ml-2" name="pagibig" value="1" {{$tm->pagibig==1?'checked':''}}> PAG-IBIG
              <input type="checkbox" class="ml-2" name="tin" value="1" {{$tm->tin==1?'checked':''}}> TIN
            </div>
            <div class="col-md-12">
              Tax Percent:
              <input type="number" name="tax_percent" placeholder="%" min="1" max="100" class="form-control" value="{{round($tm->tax_percent * 100)}}" required>
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