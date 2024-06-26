<div class="modal fade" id="viewModal-{{$emp->id}}">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="card-title">View Employee Document</h5>
      </div>
      <div class="modal-body">
        @php
          $documentTypes = documentTypes();
        @endphp
        <div class="row">
          <div class="col-md-4 border border-1 border-secondary border-top-bottom border-left-right" style="width: 100%;">
            <strong>Documents</strong>
          </div>
          <div class="col-md-4 border border-1 border-secondary border-top-bottom border-left-right" style="width: 100%;">
            <strong>Status</strong>
          </div>
          <div class="col-md-4 border border-1 border-secondary border-top-bottom border-left-right" style="width: 100%;">
            <strong>Files</strong>
          </div>
        </div>
        @foreach ($documentTypes as $key=>$docs)
        <div class="row">
          <div class="col-md-4 border border-1 border-secondary border-top-bottom border-left-right" style="width: 100%;">
            {{$docs}}
          </div>
            <div class="col-md-4 border border-1 border-secondary border-top-bottom border-left-right" style="width: 100%;">
              @php
                $empty = false;
              @endphp
              @foreach ($emp->employeeDocuments as $item)
                @if($key === $item->document_type)
                  Passed
                  @php
                    $empty = true;
                  @endphp
                @endif
              @endforeach
              @if(!$empty)
              Not Yet Submitted
              @endif
            </div>
          <div class="col-md-4 border border-1 border-secondary border-top-bottom border-left-right" style="width: 100%;">
            @foreach ($emp->employeeDocuments as $item)
              @if($key == $item->document_type)
                <a href="{{url($item->file_path)}}" target="_blank">{{$item->file_name}}</a>
              @endif
            @endforeach
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>