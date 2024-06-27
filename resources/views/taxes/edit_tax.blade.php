<!-- Modal -->
<div class="modal fade" id="edit_tax{{ $tax->id }}" tabindex="-1" role="dialog" aria-labelledby="editTaxlabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editTAXlabel">Edit Tax</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method='POST' action='edit-tax/{{ $tax->id }}' onsubmit='show()' enctype="multipart/form-data">
                  @csrf        
        <div class="modal-body text-right">
            <div class="form-group row">
               <div class="col-md-12 text-left">
                    <label for="from_gross" class="col-form-label">From Gross</label>
                    <input type="number" name='from_gross' class="form-control" value="{{ $tax->from_gross }}" v-model="form_gross"  required>
              </div>
            </div>
            <div class="form-group row">
                <div class="col-md-12 text-left">
                    <label for="to_gross" class="col-form-label">To Gross</label>
                    <input type="number" name='to_gross' class="form-control" value="{{ $tax->to_gross }}" v-model="to_gross"  required>
                </div>
              </div>

              <div class="form-group row">
                <div class="col-md-12 text-left">
                    <label for="tax_plus" class="col-form-label">Tax +</label>
                    <input type="number" name='tax_plus' class="form-control" value="{{ $tax->tax_plus }}" v-model="tax_plus"  required>
                </div>
              </div>

              <div class="form-group row">
                <div class="col-md-12 text-left">
                    <label for="percentage" class="col-form-label">Percentage</label>
                    <input type="number" name='percentage' class="form-control" value="{{ $tax->percentage }}" v-model="percentage"  required>
                </div>
              </div>

              <div class="form-group row">
                <div class="col-md-12 text-left">
                    <label for="excess_over" class="col-form-label">Excess Over</label>
                    <input type="number" name='excess_over' class="form-control" value="{{ $tax->excess_over }}" v-model="excess_over"  required>
                </div>
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
  
  