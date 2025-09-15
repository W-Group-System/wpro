<div class="modal fade" id="new{{ $employee->id }}{{ $date_r }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Data</h5>
            </div>
            <form method="post" action="{{ route('for_approval.timekeeping') }}" enctype="multipart/form-data" onsubmit="show()">
                @csrf
                
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <input type="hidden" name="date" value="{{ $date_r }}">
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
                        <div class="col-md-3">
                            <label for="employeeCode" class="col-form-label">Upload File</label>
                        </div>
                        <div class="col-md-9">
                            <input type="file" name="incident_report" class="form-control" accept=".pdf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>