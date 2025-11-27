<div class="modal fade" id="editTimekeepingModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Data</h5>
            </div>
            <form method="post" enctype="multipart/form-data" id="editTimekeepingForm">
                @csrf
                
                <input type="hidden" name="employee_id">
                <input type="hidden" name="date">
                
                <div class="modal-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3 mb-2">
                            <label for="employeeCode" class="col-form-label">Employee Code</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" id="employeeCode" class="form-control" readonly>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="employeeCode" class="col-form-label">Name</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" id="employeeName" class="form-control" readonly>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="employeeCode" class="col-form-label">Date Logs</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" id="dateLogs" class="form-control" readonly>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="employeeCode" class="col-form-label">Time IN</label>
                        </div>
                        <div class="col-md-9">
                            {{-- min="{{ date('Y-m-d\T00:00', strtotime($date_r)) }}" max="{{ date('Y-m-d\T23:59', strtotime($date_r)) }}" --}}
                            <input type="datetime-local" id="timeIn" name="employee_time_in" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="employeeCode" class="col-form-label">Time OUT</label>
                        </div>
                        <div class="col-md-9">
                            {{-- min="{{ date('Y-m-d\T00:00', strtotime($date_r)) }}" max="{{ date('Y-m-d\T23:59', strtotime($date_r."+1 day")) }}" --}}
                            <input type="datetime-local" id="timeOut" name="employee_time_out" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="employeeCode" class="col-form-label">Remarks</label>
                        </div>
                        <div class="col-md-9">
                            <textarea name="remarks" class="form-control" cols="30" rows="10" required></textarea>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="employeeCode" class="col-form-label">Attachments</label>
                        </div>
                        <div class="col-md-9">
                            <input type="file" name="incident_report" accept=".pdf" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>