<div class="modal fade" id="edit_hmo{{$availment->id}}" tabindex="-1" role="dialog" aria-labelledby="EditHoldayData" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='row'>
                    <div class='col-md-12'>
                        <h5 class="modal-title" id="EditHoldayData">Edit Holiday</h5>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ url('edit-hmo/' . $availment->id) }}" enctype="multipart/form-data" onsubmit="show()">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class='col-md-12 form-group'>
                            <label>Name</label>
                            <input type="text" name="employee_name" class="form-control" value="{{ $availment->employee_name }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class='col-md-12 form-group'>
                            <label>Email</label>
                            <input type="text" name="email" class="form-control" value="{{ $availment->email }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class='col-md-12 form-group'>
                            <label>Date of Actual HMO Availment</label>
                            <input type="date" name="date_availment" class="form-control" value="{{ $availment->date_availment }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class='col-md-12 form-group'>
                            <label>Proof of Availment</label>
                            <input type="file" class="form-control attachments" name="path[]" id="path" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <small>Upload receipts, copy of HMO agreement, etc.</small>

                            @if ($availment->attachments->count() > 0)
                                <div class="row col-md-12 mt-2">
                                    <label>Existing Files:</label>&nbsp;
                                    @foreach ($availment->attachments as $attachment)
                                        <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank"><i class="fa fa-file-pdf-o"></i></a>&nbsp;
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" id='submit1' class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>