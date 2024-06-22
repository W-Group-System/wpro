<!-- Modal -->
<div class="modal fade" id="upload_obForm{{ $ob->id }}" tabindex="-1" role="dialog" aria-labelledby="viewOBslabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="uploadOBslabel">Upload Filled OB Form</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method='POST' action='upload-ob-file/{{ $ob->id }}' onsubmit='show()' enctype="multipart/form-data">
                  @csrf        
        <div class="modal-body text-right">
            <div class="form-group row">
                <div class='col-md-2'>
                   Attachment
                </div>  
                <div class='col-md-10'>
                  <input type="file" name="obfile" class="form-control"  placeholder="Upload Supporting Documents" multiple>
                </div>
              </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" name="btnOB" class="btn btn-primary">Save</button>
          </div>
      </form>
      </div>
    </div>
  </div>