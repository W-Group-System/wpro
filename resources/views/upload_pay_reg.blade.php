	<form method='POST' onsubmit='show()' enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="row">
						<div class='col-md-12 form-group'>
							Cut off:
							<input type="date" name="cut_off" class="form-control">
						</div>
						<div class='col-md-12 form-group'>
							Company:
							<select name="company">
                                <option value=""></option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_code }}</option>
                                @endforeach
                            </select>
						</div>
						<div class='col-md-12 form-group'>
							Instruction name:
							<select name="instruction_name">
                                <option value=""></option>
                                <option value="KPI BONUS">KPI BONUS</option>
                                <option value="THIRTEEN MONTH">THIRTEEN MONTH</option>
                            </select>
						</div>
						<div class='col-md-12 form-group'>
							File:
							<input type="file" name='pay-reg' class="form-control" required>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Upload</button>
				</div>
			</form>