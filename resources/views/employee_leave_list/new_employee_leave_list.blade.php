<div class="modal fade" id="new">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add new employee leave</h5>
            </div>
            <form method="post" action="{{ url('store_employee_leaves_list') }}" onsubmit="show()">
                @csrf

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            Type
                            <select data-placeholder="Select type" name="type" class="form-control js-example-basic-single" style="width: 100%; position: relative;" required>
                                <option value=""></option>
                                <option value="1">New Leave Credit</option>
                                <option value="2">For Tenure</option>
                            </select>
                        </div>
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            Employee
                            <select data-placeholder="Select employee" name="employee" class="form-control js-example-basic-single" style="width: 100%; position: relative;" required>
                                <option value=""></option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->user_id }}">{{ $employee->employee_code.' - '.$employee->user_info->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            Leaves
                            <select data-placeholder="Select leave type" name="leave" class="form-control js-example-basic-single" style="width: 100%; position: relative;" required>
                                <option value=""></option>
                                @foreach ($leaves as $leave)
                                    <option value="{{ $leave->id }}">{{ $leave->leave_type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            Rank / Level
                            <select data-placeholder="Select rank/level" name="level" class="form-control js-example-basic-single" style="width: 100%; position: relative;" required>
                                <option value=""></option>
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            Date Hired
                            <input type="date" name="date_hired" class="form-control" required>
                        </div>
                        <div class="col-md-6" id="dateRegularization" hidden>
                            Date Regularization
                            <input type="date" name="date_regularization" class="form-control" >
                        </div>
                        <div class="col-md-6" id="leaveCredit" hidden>
                            Leave Credit
                            <input type="text" name="leave_credit" class="form-control" readonly>
                        </div>
                        <div class="col-md-6" id="addLeave" hidden>
                            Add Leave Balance
                            <input type="number" step=".01" name="earned_per_month" class="form-control" >
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>