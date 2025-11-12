<div class="modal fade" id="edit_hmo{{$availment->id}}" tabindex="-1" role="dialog" aria-labelledby="EditHoldayData" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class='row'>
                    <div class='col-md-12'>
                        <h5 class="modal-title" id="EditHoldayData">Edit Holiday</h5>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ url('edit-hmo/' . $availment->id) }}" enctype="multipart/form-data" onsubmit="show()">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class='col-md-12 form-group'>
                            <label>Name</label>
                            <input type="text" name="employee_name" class="form-control" value="{{ $availment->employee_name }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class='col-md-12 form-group'>
                            <label>Email</label>
                            <input type="text" name="email" class="form-control" value="{{ $availment->email }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class='col-md-12 form-group'>
                            <label>Date of Actual HMO Availment</label>
                            <input type="date" name="date_availment" class="form-control" value="{{ $availment->date_availment }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class='col-md-12 form-group'>
                            <label>Proof of Availment</label>
                            <input type="file" class="form-control attachments" name="path[]" id="path" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <small>Upload receipts, copy of HMO agreement, etc.</small>
                            <br>
                            {{-- @if ($availment->attachments->count() > 0)
                                <div class="row col-md-12 mt-2">
                                    <label>Existing Files:</label>&nbsp;
                                    @foreach ($availment->attachments as $attachment)
                                        <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank"><i class="fa fa-file-pdf-o"></i></a>&nbsp;
                                    @endforeach
                                </div>
                            @endif --}}
                            @if($availment->attachments && $availment->attachments->isNotEmpty())
                                @foreach($availment->attachments as $file)
                                    @php
                                        $storage = \Illuminate\Support\Facades\Storage::disk('public');

                                        if ($storage->exists($file->path)) {
                                            $fileUrl = asset('storage/' . $file->path); 
                                        } else {
                                            $fileUrl = $file->path; 
                                        }

                                        $extension = strtolower(pathinfo($file->path, PATHINFO_EXTENSION));
                                        switch ($extension) {
                                            case 'pdf':
                                                $icon = 'fa-file-pdf-o';
                                                break;
                                            case 'doc':
                                            case 'docx':
                                                $icon = 'fa-file-word-o';
                                                break;
                                            case 'xls':
                                            case 'xlsx':
                                                $icon = 'fa-file-excel-o';
                                                break;
                                            case 'jpg':
                                            case 'jpeg':
                                            case 'png':
                                            case 'gif':
                                                $icon = 'fa-file-image-o';
                                                break;
                                            default:
                                                $icon = 'fa-file-o';
                                        }
                                    @endphp

                                    <a href="{{ $fileUrl }}" target="_blank" title="View File">
                                        <i class="fa {{ $icon }} fa-lg mx-1"></i>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" id='submit1' class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>