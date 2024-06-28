<div class="modal fade" id="empDocsModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="card-title">Edit Employee Document</h5>
      </div>
      <form action="{{url('upload-employee-document')}}" method="post" enctype="multipart/form-data" onsubmit="show()">
        {{csrf_field()}}
        <input type="hidden" name="user_id" value="{{$user->id}}">
      
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 mb-2">
              Employee Documents:
              @php
                $documentTypes = documentTypes();
              @endphp
              <select data-placeholder="Select Document Types" name="document_type" class="form-control required js-example-basic-single" style="width: 100%" required>
                <option value="">-Document Types-</option>
                @foreach ($documentTypes as $key=>$item)
                  <option value="{{$key}}">{{$item}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-12 mb-2">
              Files:
              <input type="file" name="file" class="form-control" required>
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