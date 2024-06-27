<!-- Modal -->
<div class="modal fade" id="taxes" tabindex="-1" role="dialog" aria-labelledby="TAXDATA" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="TAXDATA">Create Tax</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
              <form method='POST' action='new-tax' onsubmit="btnTAX.disabled = true; return true;"  enctype="multipart/form-data">
                  @csrf        
        <div class="modal-body text-right">
          
            <div class="form-group row">
                <div class="col-md-12 text-left">
                    <label for="from_gross" class="col-form-label">From Gross</label>
                    <input type="number" name='from_gross' class="form-control" v-model="from_gross" required>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12 text-left">
                    <label for="to_gross" class="col-form-label">To Gross</label>
                    <input type="number" name='to_gross' class="form-control" v-model="to_gross" required>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12 text-left">
                    <label for="tax_plus" class="col-form-label">Tax +</label>
                    <input type="number" name='tax_plus' class="form-control" v-model="tax_plus" required>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12 text-left">
                    <label for="percentage" class="col-form-label">Percentage</label>
                    <input type="number" name='percentage' class="form-control" v-model="percentage" required>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-12 text-left">
                    <label for="excess_over" class="col-form-label">Excess Over</label>
                    <input type="number" name='excess_over' class="form-control" v-model="excess_over" required>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" name="btnTAX" class="btn btn-primary">Save</button>
        </div>
      </form>
      </div>
    </div>
  </div>
  