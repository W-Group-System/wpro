<div class="modal fade" id="upload_turnover_list{{ $employee_leave->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload Turnover List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="text-muted">Upload your work handover document so your approver can process this Vacation Leave.</p>
        <form id="turnoverForm{{ $employee_leave->id }}" enctype="multipart/form-data">
          @csrf
          <div class="form-group">
            <label>Choose file (PDF, Word, or image — max 5MB)</label>
            <input type="file" class="form-control" name="turnover_list" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="uploadTurnoverBtn{{ $employee_leave->id }}">Upload</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  $('#uploadTurnoverBtn{{ $employee_leave->id }}').on('click', function () {
    var formData = new FormData($('#turnoverForm{{ $employee_leave->id }}')[0]);
    $.ajax({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      type: 'POST',
      url: '{{ url("upload-turnover-list/" . $employee_leave->id) }}',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        Swal.fire('Success', 'Turnover list uploaded successfully.', 'success')
          .then(function () { location.reload(); });
      },
      error: function (xhr) {
        var msg = xhr.responseJSON && xhr.responseJSON.errors
          ? Object.values(xhr.responseJSON.errors).flat().join(' ')
          : 'Upload failed. Please try again.';
        Swal.fire('Error', msg, 'error');
      }
    });
  });
</script>
@endpush
