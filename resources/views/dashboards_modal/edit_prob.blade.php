<style>
    .hidden {
        display: none;
    }
</style>

<div class="modal fade" id="edit_prob{{ $prob_emp->id }}" tabindex="-1" role="dialog" aria-labelledby="editProb" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProblabel">Edit Probationary Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method='POST' action='{{ url("edit-prob/" . $prob_emp->id) }}' onsubmit='show()' enctype="multipart/form-data">
                @csrf        
                <div class="modal-body">
                    <div class="form-group">
                            <label for="Type">Type</label>
                            <select id="emp_classification_{{ $prob_emp->id }}" data-placeholder="Classification" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='classification'>
                                <option value="">Select Probationary Action</option>
                                <option value="for_regularization">For Regularization</option>
                                <option value="for_resignation">For Resignation</option>
                            </select>
                    </div>
                    <div class="form-group  hidden" id="date_regular_div_{{ $prob_emp->id }}">
                            <label for="">Date Regularized</label>
                            <input type="date" id="date_regular_{{ $prob_emp->id }}" class="form-control form-control-sm" name="date_regular">
                    </div>
                    <div>
                        
                    </div>
                    <div class="form-group hidden" id="date_resigned_div_{{ $prob_emp->id }}">
                            <label for="">Date Resigned</label>
                            <input type="date" id="date_resigned_{{ $prob_emp->id }}" class="form-control form-control-sm" name="date_resigned">
                    </div>
                   <div class="row">
                     <div class='col-lg-6 form-group hidden' id="leaveType_{{ $prob_emp->id }}">
                        <label for="leaveType">Leave Type</label>
                        <select data-placeholder="Select Leave Type" class="form-control form-control-sm required js-example-basic-single " style='width:100%;' name='leave_type'>
                            <option value="">--Select Leave Type--</option>
                            @foreach ($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->id }}" {{ old('leaveType') == $leaveType->id ? 'selected' : '' }}>
                                    {{ $leaveType->leave_type }}</option>
                            @endforeach
                        </select>
                    </div>
					<div class="form-group hidden col-lg-6" id="leaveCount_{{ $prob_emp->id }}">
							<label for="count">Count (days)</label>
							<input type="number" class="form-control form-control-sm" name="count" id="count" required min=".00" step='0.01'
							value="{{ old('count') }}" placeholder="0.00">
					</div>
                   </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('emp_classification_{{ $prob_emp->id }}').addEventListener('change', function () {
        var dateRegularDiv = document.getElementById('date_regular_div_{{ $prob_emp->id }}');
        var dateResignedDiv = document.getElementById('date_resigned_div_{{ $prob_emp->id }}');
        var leaveType = document.getElementById('leaveType_{{ $prob_emp->id }}');
        var leaveCount = document.getElementById('leaveCount_{{ $prob_emp->id }}');

        if (this.value === 'for_regularization') {
            dateRegularDiv.classList.remove('hidden');
            dateResignedDiv.classList.add('hidden');
            leaveType.classList.remove('hidden');
            leaveCount.classList.remove('hidden');
        } else if (this.value === 'for_resignation') {
            leaveType.classList.add('hidden');
            leaveCount.classList.add('hidden');
            dateRegularDiv.classList.add('hidden');
            dateResignedDiv.classList.remove('hidden');
        } else {
            dateRegularDiv.classList.add('hidden');
            dateResignedDiv.classList.add('hidden');
            leaveType.classList.add('hidden');
            leaveCount.classList.add('hidden');
        }
    });
</script>
