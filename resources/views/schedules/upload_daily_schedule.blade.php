<div class="modal fade" id="uploadSchedule" tabindex="-1" role="dialog" aria-labelledby="uploadSchedule" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class='row'>
          <div class='col-md-12'>
            <h5 class="modal-title" id="uploadSchedule">Upload Daily Schedule</h5>
          </div>
        </div>
      </div>
      <form method='POST' action='{{url('upload-schedule')}}' onsubmit='show()' enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="modal-body">
          File:
          <input type="file" name="file" id="file" class="form-control" required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" id='submit1' class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>