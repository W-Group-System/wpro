<div class="modal fade" id="editDailySchedule-{{$ds->id}}" tabindex="-1" role="dialog" aria-labelledby="editDailySchedule" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class='row'>
          <div class='col-md-12'>
            <h5 class="modal-title" id="editDailySchedule">Edit Daily Schedule</h5>
          </div>
        </div>
      </div>
      <form method='POST' action='{{url('update-schedule/'.$ds->id)}}' onsubmit='show()' >
        {{ csrf_field() }}
        <div class="modal-body">
          Company:
          <div class="form-group">
            <input type="text" name="company" class="form-control" value="{{$ds->company}}" readonly>
          </div>
          Employee Number:
          <div class="form-group">
            <input type="text" name="employee_number" class="form-control" value="{{$ds->employee_number}}" readonly>
          </div>
          Employee Code:
          <div class="form-group">
            <input type="text" name="employee_code" class="form-control" value="{{$ds->employee_code}}" readonly>
          </div>
          Name:
          <div class="form-group">
            <input type="text" name="name" class="form-control" value="{{$ds->employee_name}}" readonly>
          </div>
          Log Date:
          <div class="form-group">
            <input type="date" name="log_date" class="form-control" value="{{$ds->log_date}}" required>
          </div>
          Time In From:
          <div class="form-group">
            <input type="time" name="time_in_from" class="form-control" value="{{$ds->time_in_from}}" required>
          </div>
          Time In To:
          <div class="form-group">
            <input type="time" name="time_in_to" class="form-control" value="{{$ds->time_in_to}}" required>
          </div>
          Time Out From:
          <div class="form-group">
            <input type="time" name="time_out_from" class="form-control" value="{{$ds->time_out_from}}" required>
          </div>
          Time Out To:
          <div class="form-group">
            <input type="time" name="time_out_to" class="form-control" value="{{$ds->time_out_to}}" required>
          </div>
          Working Hours:
          <div class="form-group">
            <input type="text" name="working_hours" class="form-control" value="{{$ds->working_hours}}" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" id='submit1' class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>