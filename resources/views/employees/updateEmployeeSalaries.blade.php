<div class="modal fade" id="uploadSalaries" tabindex="-1" role="dialog" aria-labelledby="uploadSalariesLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('upload.salaries') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadSalariesLabel">Import Employee Salaries</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>Upload File</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                    <small>Only Excel files (.xlsx, .xls) are allowed.</small>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>