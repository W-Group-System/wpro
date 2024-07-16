@foreach($employee_leaves as $employee_leave)
  <div class="modal fade" id="upload_leave{{ $employee_leave->id }}" tabindex="-1" role="dialog" aria-labelledby="uploadLeaveLabel{{ $employee_leave->id }}" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="uploadLeaveLabel{{ $employee_leave->id }}">Upload Leave Document</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                  <form id="uploadForm{{ $employee_leave->id }}" onsubmit="show()" enctype="multipart/form-data">
                      <label >Choose file</label><br>
                      <input type="file" class="form-control" name="attachment" required>
                  </form>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" id="uploadBtn{{ $employee_leave->id }}" class="btn btn-primary">Upload</button>
              </div>
          </div>
      </div>
  </div>
@endforeach
