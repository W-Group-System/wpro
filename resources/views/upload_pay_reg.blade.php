	<form method='POST' onsubmit='show()' enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="row">
						<div class='col-md-12 form-group'>
							File:
							<input type="file" name='pay-reg' class="form-control" required>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Upload</button>
				</div>
			</form>