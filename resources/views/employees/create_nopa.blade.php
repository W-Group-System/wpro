<div class="modal fade" id="createNopa" tabindex="-1" role="dialog" aria-labelledby="createNopaInfoLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createNopaLabel">Create Notice of Personnel Action</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method='POST' action='updateEmpMovementHR/{{ $user->employee->id }}' onsubmit='show()' enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="form-card">
						<div class='row mb-2'>
							<div class='col-lg-12 mt-1'>
								<div class="table-responsive">
                                    <strong>Details</strong>
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center">
                                                    Item
                                                </th>
                                                <th class="text-center">
                                                    From
                                                </th>
                                                <th class="text-center">
                                                    To
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p>DEPARTMENT/ SECTION</p>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        @foreach($departments as $department)
                                                        @if ($user->employee->department_id == $department->id)
                                                            <input type="text" class="form-control" value="{{$department->name}}" readonly>
                                                            <input type="hidden" name="department_from" value="{{$department->id}}">
                                                        @endif
                                                        @endforeach
                                                      </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <select  class="form-control form-control-sm js-example-basic-single " style='width:100%;' name='department_to'>
                                                            <option value="">--Select Department--</option>
                                                            @foreach($departments as $department)
                                                              <option value="{{$department->id}}">{{$department->name}}</option>
                                                            @endforeach
                                                        </select>
                                                      </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>PROJECT NAME (if applicable)</p>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input type="text" class="form-control" name="project_name_from" value="{{ $user->employee->project }}" readonly>  
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input type="text" class="form-control" name="project_name_to" >  
                                                      </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>Position Title</p>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input type="text" class="form-control" name="position_from" value="{{ $user->employee->position }}" readonly>  
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input type="text" class="form-control" name="position_to">  
                                                      </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>Job Level</p>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        @foreach($levels as $level)
                                                        @if ($user->employee->level == $level->id)
                                                            <input type="text" class="form-control" value="{{$level->name}}" readonly>
                                                            <input type="hidden" name="level_from" value="{{$level->id}}">
                                                        @endif
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <select class="form-control form-control-sm js-example-basic-single" style='width:100%;' name='level_to' >
                                                            <option value="">--Select Level--</option>
                                                            @foreach($levels as $level)
                                                            <option value="{{$level->id}}">{{$level->name}}</option>
                                                            @endforeach
                                                        </select>
                                                      </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>Employment Status</p>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        @foreach($classifications as $classification)
                                                        @if ($user->employee->classification == $classification->id)
                                                            <input type="text" class="form-control" value="{{$classification->name}}" readonly>
                                                            <input type="hidden" name="classification_from" value="{{$classification->id}}">
                                                        @endif
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <select class="form-control form-control-sm js-example-basic-single" style='width:100%;' name='classification_to' >
                                                            <option value="">--Select Status--</option>
                                                            @foreach($classifications as $classification)
                                                            <option value="{{$classification->id}}">{{$classification->name}}</option>
                                                            @endforeach
                                                        </select>
                                                      </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>Immediate Supervisor</p>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        @foreach($users as $item)
                                                        @if ($user->employee->immediate_sup == $item->id)
                                                            <input type="text" class="form-control" value="{{$item->name}}" readonly>
                                                            <input type="hidden" name="immediate_supervisor_from" value="{{$item->id}}">
                                                        @endif
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <select class="form-control form-control-sm required js-example-basic-single " style='width:100%;' name='immediate_supervisor_to'>
                                                            <option value="">-- Select Immediate Supervisor--</option>
                                                            @foreach($users as $item)
                                                                <option value="{{$item->id}}">{{$item->name}}</option>
                                                            @endforeach
                                                        </select>  
                                                      </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>Effective Date</p>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input type="date" name="date_from" class="form-control form-control-sm required js-example-basic-single ">  
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input type="date" name="date_to" class="form-control form-control-sm required js-example-basic-single " required>  
                                                      </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>Attachment</p>
                                                </td>
                                                <td colspan="2">
                                                    <div class='col-md-12'>
                                                        <input type="file" name="file" class="form-control form-control-sm required js-example-basic-single ">  
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
							</div>
                            {{-- <div class='col-lg-12 mt-1'>
                            <hr class="divider" style="height: 2px; background-color: #958e8e; border: none; margin: 20px 0;">
                            </div>
                            <div class='col-lg-12 mt-1'>
								<div class="table-responsive">
                                    <strong>Salary Data</strong>
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center">
                                                    Item
                                                </th>
                                                <th class="text-center">
                                                    From
                                                </th>
                                                <th class="text-center">
                                                    To
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p>Basic</p>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input type="text" class="form-control" v-model="row.bdate" required>  
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input type="text" class="form-control" v-model="row.bdate" required>  
                                                      </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>De Minimis</p>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input type="text" class="form-control" v-model="row.bdate" required>  
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input type="text" class="form-control" v-model="row.bdate" required>  
                                                      </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>Other Allowances</p>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input type="text" class="form-control" v-model="row.bdate" required>  
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-md-12'>
                                                        <input type="text" class="form-control" v-model="row.bdate" required>  
                                                      </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
							</div> --}}
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

{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
    const othersRadioButton = document.getElementById('others');
    const othersInputField = document.getElementById('othersInputField');

    const radioButtons = document.querySelectorAll('input[name="nature_of_transactions"]');
    radioButtons.forEach(function (radioButton) {
        radioButton.addEventListener('change', function () {
            if (othersRadioButton.checked) {
                othersInputField.style.display = 'block';
            } else {
                othersInputField.style.display = 'none';
            }
        });
    });
});

</script> --}}