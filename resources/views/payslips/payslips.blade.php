@extends('layouts.header')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Payslips</h4>
                <div class="table-responsive">
                  <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            
                            <th>Date Generated</th>
                            <th>Payroll Period</th>
                            <th>Payslip</th>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach($payslips->sortByDesc('pay_period_from') as $payslip)
                        <tr>
                              
                          <td>{{date('F d, Y',strtotime($payslip->created_at))}}</td>
                          <td>{{date('F d, Y',strtotime($payslip->pay_period_from))}} - {{date('F d, Y',strtotime($payslip->pay_period_to))}}</td>
                          <td><a href="{{url('/payslip-employee?id='.$payslip->pay_period_from)}}" target="_blank"><button type="button" class="btn btn-inverse-danger btn-icon">
                            <i class="ti-file"></i>
                          </button></a></td>
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
@endsection
