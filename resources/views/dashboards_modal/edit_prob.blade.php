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
                <div class="modal-body text-right">
                    <div class="form-group row">
                        <div class='col-md-12 text-left'>
                            Type
                            <select id="emp_classification_{{ $prob_emp->id }}" data-placeholder="Classification" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='classification'>
                                <option value="">Select Probationary Action</option>
                                <option value="for_regularization">For Regularization</option>
                                <option value="for_resignation">For Resignation</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row hidden" id="date_regular_div_{{ $prob_emp->id }}">
                        <div class='col-md-12 text-left'>
                            Date Regularized
                            <input type="date" id="date_regular_{{ $prob_emp->id }}" class="form-control form-control-sm" name="date_regular">
                        </div>
                    </div>
                    <div class="form-group row hidden" id="date_resigned_div_{{ $prob_emp->id }}">
                        <div class='col-md-12 text-left'>
                            Date Resigned
                            <input type="date" id="date_resigned_{{ $prob_emp->id }}" class="form-control form-control-sm" name="date_resigned">
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
        if (this.value === 'for_regularization') {
            dateRegularDiv.classList.remove('hidden');
            dateResignedDiv.classList.add('hidden');
        } else if (this.value === 'for_resignation') {
            dateRegularDiv.classList.add('hidden');
            dateResignedDiv.classList.remove('hidden');
        } else {
            dateRegularDiv.classList.add('hidden');
            dateResignedDiv.classList.add('hidden');
        }
    });
</script>
