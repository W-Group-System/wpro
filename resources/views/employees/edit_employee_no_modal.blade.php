<div class="modal fade" id="editEmpNo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Employee No.</h5>
            </div>
            <form action="{{url('update-employee-code/'.$user->employee->id)}}" method="post" onsubmit="show()">
                {{csrf_field()}}
                <div class="modal-body">
                    Employee No
                    <input type="text" name="employee_no" id="employee_no" class="form-control" value="{{$user->employee->employee_code}}" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>