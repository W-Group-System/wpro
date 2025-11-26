<div class="modal fade" id="employee-review-modal-{{$employee->id}}" tabindex="-1" role="dialog" aria-labelledby="reviewedEmployee" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewedEmployee">Review Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method='POST' action='review-employee/{{$employee->id}}' onsubmit="btnReview.disabled = true; return true;" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="badge badge-success mt-1">Review</h4>
                        </div>
                        <input type="hidden" name="status" value="Pending">
                        <div class='col-md-12'>
                            <h4 class="mb-3">Employee Information</h4>
                            <p><strong>Name:</strong> {{ $employee->first_name .' '. $employee->last_name }}</p>
                            <p><strong>Department:</strong> {{ optional($employee->user->employee->department)->name }}</p>
                            <p><strong>Position:</strong> {{ $employee->user->employee->position }}</p>
                            <p><strong>Company:</strong> {{ $employee->company->company_name }}</p>
                            <p><strong>Date Hired:</strong> {{ date('M d, Y', strtotime($employee->original_date_hired)) }}</p>
                            <hr>
                        </div>
                        <div class='col-md-12'>
                            Review Remarks:
                            <textarea class="form-control" name="review_remarks" id="review_remarks_{{ $form_approval->id }}" cols="30" rows="5" placeholder="Input Review Remarks" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="btnReturn" class="btn btn-danger">Return</button>
                    <button type="submit" name="btnReview" class="btn btn-success">Reviewed</button>
                </div>
            </form>
        </div>
    </div>
</div>
