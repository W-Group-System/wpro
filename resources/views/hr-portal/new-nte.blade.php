<div class="modal fade" id="uploadNteModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="card-title">Upload NTE</h5>
      </div>
      <form action="{{url('add-nte')}}" method="post" enctype="multipart/form-data" onsubmit="show()">
        {{csrf_field()}}

        <input type="hidden" name="user_id" value="{{$user->id}}">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 mb-2">
              Violation:
              <textarea name="violation" rows="5" class="form-control" required></textarea>
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