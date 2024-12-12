<div class="modal fade" id="newSalaryAdjustment" tabindex="-1" role="dialog" aria-labelledby="newSalaryAdjustment"
	aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="newSalaryAdjustmentlabel">New Salary Adjustment</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method='POST' action='new-employee-adjustment' onsubmit='show()'>
				@csrf
				<div class="modal-body">
					<div class="row">
						<div class="col-lg-4 form-group">
							<label for="employee">Employee:*</label>
							<select data-placeholder="Select Employee" class="form-control form-control-sm required js-example-basic-single "
								style='width:100%;' name='employee' required>
								<option value="">--Select Employee--</option>
								@foreach ($employees as $employee)
									<option value="{{ $employee->id }}" {{ old('employee') == $employee->id ? 'selected' : '' }}>
										{{ $employee->last_name . ', ' . $employee->first_name . ' ' . $employee->middle_name }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-lg-4 form-group">
							<label for="name">Adjustment Name*:</label>
							<select name='name' data-placeholder="Select Name" style='width:100%;'  class='form-control form-control-sm required js-example-basic-single' required>
								<option value=''></option>
								@foreach($names as $name)
								<option value='{{$name->name}}'>{{$name->name}}</option>
								@endforeach

							</select>
						</div>
						<div class="col-lg-4 form-group">
							<label for="amount">Amount:*</label>
							<input type="number" class="form-control form-control-sm" name="amount" id="amount" required 
								value="{{ old('amount') }}" placeholder="0.00" step=".01" required>
						</div>

					</div>

					<div class="row">
						
						<div class="col-lg-12 form-group">
							<label for="remarks">Remarks:</label>
							<textarea class="form-control form-control-sm" id="exampleTextarea1" rows="3" name="remarks"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
			</form>
		</div>
	</div>

</div>
