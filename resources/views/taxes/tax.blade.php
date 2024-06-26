@extends('layouts.header')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
            @if (count($errors))
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger alert-dismissible fade show " role="alert">
                        <span class="alert-inner--icon"><i class="ni ni-fat-remove"></i></span>
                        <span class="alert-inner--text"><strong>Error!</strong> {{ $error }}</span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endforeach
            @endif
        <div id="payroll" class="tab-pane  active">
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <p class="card-description">
                    <button type="button" class="btn btn-outline-success btn-icon-text" data-toggle="modal" data-target="#taxes">
                      <i class="ti-plus btn-icon-prepend"></i>                                                    
                      Create Tax
                    </button>
                  </p>

                <div class="table-responsive">
                  <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>From Gross</th>
                            <th>To Gross</th>
                            <th>Tax Plus</th>
                            <th>Percentage</th>
                            <th>Excess Over</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($taxes as $tax)
                            <tr>
                                <td>{{ $tax->from_gross }}</td>
                                <td>{{ $tax->to_gross }}</td>
                                <td>{{ $tax->tax_plus }}</td>
                                <td>{{ $tax->percentage }}</td>
                                <td>{{ $tax->excess_over }}</td>
                                <td><button type="button" id="edit{{ $tax->id }}" class="btn btn-info btn-rounded btn-icon"
                                    data-target="#edit_tax{{ $tax->id }}" data-toggle="modal" title='Edit'>
                                    <i class="ti-pencil-alt"></i>
                                  </button>
                                  <form action="{{ url('delete-tax/' . $tax->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-rounded btn-icon" title='Delete' onclick="return confirm('Are you sure you want to delete this tax?');">
                                        <i class="ti-trash"></i>
                                    </button>
                                </form></td>
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
    @foreach($taxes as $tax)
    @include('taxes.edit_tax')
    @endforeach
    @include('taxes.create_tax')   

@endsection


