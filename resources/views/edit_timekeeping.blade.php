<div class="modal fade" id="edit{{ $employee->id }}{{ $date_r }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Data</h5>
            </div>
            <form method="post" action="{{ route('update.timekeeping') }}">
                @csrf
                
                @if($time_in && $time_out)
                <input type="hidden" name="attendance_logs_in" value="{{ $time_in->id }}">
                <input type="hidden" name="attendance_logs_out" value="{{ $time_out->id }}">
                @endif
                <div class="modal-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <label for="employeeCode" class="col-form-label">Employee Code</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="{{ $employee->employee_code }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="employeeCode" class="col-form-label">Name</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="{{ $employee->user_info->name }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="employeeCode" class="col-form-label">Date Logs</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="{{ $date_r }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="employeeCode" class="col-form-label">Time IN</label>
                        </div>
                        <div class="col-md-9">
                            <input type="time" name="employee_time_in" class="form-control" value="{{ $time_in ? date('H:i', strtotime($time_in->datetime)) : null }}" >
                        </div>
                        <div class="col-md-3">
                            <label for="employeeCode" class="col-form-label">Time OUT</label>
                        </div>
                        <div class="col-md-9">
                            <input type="time" name="employee_time_out" class="form-control" value="{{ $time_out ? date('H:i', strtotime($time_out->datetime)) : null }}">
                        </div>
                        <div class="col-md-3">
                            <label for="employeeCode" class="col-form-label">Remarks</label>
                        </div>
                        <div class="col-md-9">
                            <textarea name="remarks" class="form-control" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>