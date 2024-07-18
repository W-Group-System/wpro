<div class="modal fade" id="addInstruction" tabindex="-1" role="dialog" aria-labelledby="addInstruction" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addInstructionlabel">Edit Payroll Instruction Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method='POST' action='add-payroll-instruction' onsubmit='show()' enctype="multipart/form-data">
                @csrf        
                <div class="modal-body">
                    <div class="form-group">
                        <label>Company</label>
                        <select data-placeholder="Company" class="form-control form-control-sm required js-example-basic-single " style='width:100%;' name='company' required>
                          <option value="">--Select Location--</option>
                          @foreach($companies as $company)
                            <option value="{{$company->company_name}} - {{$company->company_code}}">{{$company->company_name}} - {{$company->company_code}}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Employee</label>
                        <select data-placeholder="Select Employee" class="form-control form-control-sm required js-example-basic-multiple " id="employee-select"
								style='width:100%;' name='employee' required>
								<option value="">--Select Employee--</option>
								@foreach ($employees_selection as $employee)
									<option value="{{ $employee->last_name . ', ' . $employee->first_name . ' ' . $employee->middle_name}}" data-employee-code="{{ $employee->employee_code}}">
										{{ $employee->last_name . ', ' . $employee->first_name . ' ' . $employee->middle_name}}</option>
								@endforeach
							</select>
                    </div>
                    <div class="form-group">
                        <label>Employee Code</label>
                        <input type="text" class="form-control form-control-sm required js-example-basic-multiple" name="site_id" id="site-id-input" readonly>
                    </div>
                    <div class="row">
                        <div class='col-lg-6 form-group'>
                            <label for="">Start Date</label>
                            <input type="date" class="form-control form-control-sm required js-example-basic-multiple" name="start_date">
                        </div>
                         <div class='col-lg-6 form-group'>
                            <label for="">End Date</label>
                            <input type="date" class="form-control form-control-sm required js-example-basic-multiple" name="end_date">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Benefit Name</label>
                        <input type="text" class="form-control form-control-sm required js-example-basic-multiple" name="benefit_name">
                    </div>
                    <div class="row">
                        <div class='col-lg-6 form-group'>
                            <label>Amount</label>
                            <input type="number" class="form-control form-control-sm required js-example-basic-multiple" step='0.01' name="amount">
                        </div>
                        <div class='col-lg-6 form-group'>
                            <label>Frequency</label>
                            <select data-placeholder="Select Frequency" class="form-control form-control-sm required js-example-basic-multiple"
                                    style='width:100%;' name='frequency' required>
                                    <option value="">--Select Frequency--</option>
                                    <option value="Every 1st cut off">Every 1st Cut-off</option>
                                    <option value="Every 2nd cut off">Every 2nd Cut-off</option>
                                    <option value="Every cut off">Every Cut-off</option>
                                    <option value="This cut off">This Cut-off</option>
                                </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class='col-lg-6 form-group'>
                            <label>Deductible</label>
                            <select data-placeholder="Deductible" class="form-control form-control-sm required js-example-basic-multiple"
                                    style='width:100%;' name='deductible' required>
                                    <option value="">--Deductible--</option>
                                    <option value="YES">Yes</option>
                                    <option value="NO">No</option>
                                </select>
                        </div>
                        <div class='col-lg-6 form-group'>
                            <label>Remarks</label>
                            <input type="text" class="form-control form-control-sm required js-example-basic-multiple" name="remarks">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#employee-select').on('change', function() {
            var selectedEmployeeCode = $(this).find('option:selected').data('employee-code');
            $('#site-id-input').val(selectedEmployeeCode);
        });
    });
</script>