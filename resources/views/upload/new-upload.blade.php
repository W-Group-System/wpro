<div class="modal fade" id="uploadModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="card-title">Upload OB/OT/Leaves</h5>
            </div>
            <form action="{{url('upload-ob')}}" method="post" enctype="multipart/form-data" onsubmit="show()">
                {{csrf_field()}}

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            Type:
                            <select name="type" class="form-control required js-example-basic-single"
                                style="width: 100%" required>
                                <option value="">-Type-</option>
                                {{-- <option value="VL/SL">Vacation Leave & Sick Leave</option> --}}
                                <option value="VL/SL">Leaves</option>
                                <option value="OB">Official Business</option>
                                <option value="OT">Overtime</option>
                                <option value="DTR">Daily Time Record</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            Files:
                            <input type="file" name="file" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>