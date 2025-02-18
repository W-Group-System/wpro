<div class="modal fade" id="new">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add new plan leave</h4>
            </div>
            <form method="POST" action="{{url('store_plan_leave')}}" onsubmit="show()">
                @csrf 

                <div class="modal-body">
                    <div class="form-group">
                        Date From
                        <input type="date" name="date_from" class="form-control" min="{{date('Y-m-d', strtotime('+3 weekdays'))}}" required>
                    </div>
                    <div class="form-group">
                        Date To
                        <input type="date" name="date_to" class="form-control" min="{{date('Y-m-d', strtotime('+3 weekdays'))}}" required>
                    </div>
                    <div class="form-group">
                        Reason
                        <textarea name="reason" class="form-control" cols="30" rows="10" required></textarea>
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