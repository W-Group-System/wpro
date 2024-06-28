@extends('layouts.header')

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div id="payroll" class="tab-pane  active">
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <p class="card-description">
                  
                  </p>

                  {{-- @foreach ($computed_salary as $taxed) --}}
                      {{ $computed_taxes}}
                  {{-- @endforeach --}}
                
              </div>
            </div>
           </div>
        </div>
    </div>
</div>
   
@endsection


