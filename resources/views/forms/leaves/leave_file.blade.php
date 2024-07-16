@foreach($employee_leaves as $employee_leave)
<div class="modal fade" id="upload_leave{{ $employee_leave->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Upload Document for Employee Leave</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="uploadForm{{ $employee_leave->id }}" enctype="multipart/form-data">
          @csrf
          <div class="form-group">
            <label>Choose file</label>
            <input type="file" class="form-control"  name="leave_file">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="uploadBtn{{ $employee_leave->id }}">Upload</button>
      </div>
    </div>
  </div>
</div>
@endforeach