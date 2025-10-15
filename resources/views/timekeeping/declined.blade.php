<div class="modal fade" id="cancelled{{ $dtr_correction->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Are you sure you want to cancel?</h5>
            </div>

            <form method="post" action="{{ url('timekeeping-official/update/'.$dtr_correction->id) }}" enctype="multipart/form-data" onsubmit="show()">
                @csrf
                
                <input type="hidden" name="date" value="{{ $dtr_correction->date }}">
                <input type="hidden" name="emp_id" value="{{ $dtr_correction->employee_id }}">
                <input type="hidden" name="status" value="Cancelled">

                <div class="modal-body">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th>Approvers</th>
                            <th>Status</th>
                            <th>Date Approved</th>
                        </tr>
                        @foreach ($dtr_correction->dtr_correction_approver as $approver)
                        <tr>
                            <td>{{ $approver->user->name }}</td>
                            <td>{{ $approver->status }}</td>
                            <td>
                                @if($approver->status == "Approved")
                                    {{ date('M d Y', strtotime($approver->updated_at)) }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>