@extends('layouts.header')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class='row'>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Filter</h4>
                        <p class="card-description">
                            <form method='get' onsubmit='show();' enctype="multipart/form-data">
                                <div class=row>
                                    <div class='col-md-2'>
                                        <div class="form-group">
                                            <label class="text-right">Company</label>
                                            <select data-placeholder="Select Company" class="form-control form-control-sm required js-example-basic-single" style='width:100%;' name='company[]' multiple required>
                                                <option value="">-- Select Employee --</option>
                                                @foreach($companies as $comp)
                                                <option value="{{$comp->id}}" @if (in_array($comp->id,$company)) selected @endif>{{$comp->company_code}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class='col-md-2'>
                                        <div class="form-group">
                                            <label class="text-right">From</label>
                                            <input type="date" value='{{$from}}' class="form-control" name="from" max='{{date('Y-m-d')}}' onchange='get_min(this.value);' required />
                                        </div>
                                    </div>
                                    <div class='col-md-2'>
                                        <div class="form-group">
                                            <label class="text-right">To</label>
                                            <input type="date" value='{{$to}}' class="form-control" name="to" id='to' max='{{date('Y-m-d')}}' required />
                                        </div>
                                    </div>
                                    <div class='col-md-2'>
                                        <button type="submit" class="form-control form-control-sm btn btn-primary mb-2 btn-sm">Generate</button>
                                    </div>
                                </div>
                            </form>
                        </p>
                        <h4 class="card-title">Proof of Availment Report</h4>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-detailed" id="ob_report">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Company</th>
                                        <th>Department</th>
                                        <th>Date of Availment</th>
                                        <th>Attachments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employee_hmo as $item)
                                    <tr>
                                        <td>{{ $item->employee_name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->company }}</td>
                                        <td>{{ $item->department }}</td>
                                        <td>{{date('M. d, Y',strtotime($item->date_availment))}}</td>
                                        <td>
                                            @if($item->attachments && $item->attachments->isNotEmpty())
                                                @foreach($item->attachments as $file)
                                                    <a href="{{ asset('storage/' .$file->path) }}" target="_blank">
                                                        <i class="fa fa-file-pdf-o"></i>
                                                    </a>
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- DataTables CSS and JS includes -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.css">

<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>

<script>
  $(document).ready(function() {
    new DataTable('.table-detailed', {
      // pagelenth:25,
      paginate:false,
      dom: 'Bfrtip',
      buttons: [
          'copy', 'excel'
      ],
      columnDefs: [{
        "defaultContent": "-",
        "targets": "_all"
      }],
      order: [] 
    });
  });
</script>



@endsection
@section('footer')
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('body_css/js/inputmask.js') }}"></script>
@endsection
