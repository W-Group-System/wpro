<div class="modal fade" id="import">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="card-title">Import SL Bank</h5>
            </div>
            <form method="POST" action="{{url('store_sl_bank')}}" onsubmit="show()" enctype="multipart/form-data">
                @csrf 

                <div class="modal-body">
                    Upload a file 
                    <input type="file" name="sl_bank_file" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>