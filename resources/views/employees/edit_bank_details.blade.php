<div class="modal fade" id="editAcctNo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"></div>
            <form action="{{url('update-account-no/'.$user->employee->id)}}" method="post" onsubmit="show()">
                {{csrf_field()}}
                <div class="modal-body">
                    Account No
                    <input type="text" name="account_no" id="account_no" class="form-control" value="{{$user->employee->bank_account_number}}" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>