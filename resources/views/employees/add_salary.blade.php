<div class="modal fade" id="createEmpSalary" tabindex="-1" role="dialog" aria-labelledby="createEmpSalaryLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createEmpSalaryLabel">Add Employee Salary</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method='POST' action='updateEmpSalary/{{ $user->employee->id }}' onsubmit='show()' enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="form-card">
						<div class='row mb-2'>
							<div class='col-lg-12 mt-1'>
								<div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center">
                                                    Basic Salary
                                                </th>
                                                <th class="text-center">
                                                    De Minimis
                                                </th>
                                                <th class="text-center">
                                                    Other Allowances
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class='col-md-12 form-group'>
                                                        <input class="form-control" name="basic_salary" value="{{ $user->employee->employee_salary ? $user->employee->employee_salary->basic_salary : ""}}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12 form-group'>
                                                        <input class="form-control" name="de_minimis" value="{{ $user->employee->employee_salary ? $user->employee->employee_salary->de_minimis : ""}}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12 form-group'>
                                                        <input class="form-control" name="other_allowance" value="{{ $user->employee->employee_salary ? $user->employee->employee_salary->other_allowance : ""}}">
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
							</div>
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
