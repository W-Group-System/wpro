<div class="modal fade" id="payrollInstruction" tabindex="-1" role="dialog" aria-labelledby="payrolldata" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="payrolldata">Upload Payroll Instruction</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{ url('importPayinstructionExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="row">
              <div class='col-md-12 form-group'>
                 Upload File
                 @csrf
                 <input type="file" name="import_file" accept=".xlsx, .csv" />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Import Payroll</button>
          </div>
        </form> 
      </div>
    </div>
</div>



