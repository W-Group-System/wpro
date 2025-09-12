<div class="modal fade" id="edit{{ $dtr_correction->id }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data</h5>
            </div>
            <form method="post" action="{{ route('update.timekeeping',['id' => $dtr_correction->id]) }}" enctype="multipart/form-data">
                @csrf
                
                <input type="hidden" name="date" value="{{ $dtr_correction->date }}">
                <input type="hidden" name="emp_id" value="{{ $dtr_correction->employee_id }}">

                <div class="modal-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <label for="employeeCode" class="col-form-label">Action</label>
                        </div>
                        <div class="col-md-9">
                            <select data-placeholder="Select action" name="status" class="form-select select2">
                                <option value=""></option>
                                <option value="Approved" @if($dtr_correction->status == "Approved") selected @endif>Approved</option>
                                {{-- <option value="Cancelled" @if($dtr_correction->status == "Cancelled") selected @endif>Cancelled</option> --}}
                                <option value="Returned" @if($dtr_correction->status == "Returned") selected @endif>Returned</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>