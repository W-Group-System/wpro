<div class="modal fade" id="changeStatusLeave{{$leave->id}}" tabindex="-1" role="dialog" aria-labelledby="declinedDTRremarks" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="declinedDTRremarks">Are you sure you want to change the status?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method='POST' action='{{ url('change-status-leave/'.$leave->id) }}'>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class='col-md-12 form-group'>
                            <select name="status" class="form-control">
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit"  class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
