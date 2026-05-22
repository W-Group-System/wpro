@extends('layouts.header')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        
          <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Payslips</h4>
                <ul class="nav nav-tabs mb-3" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#regularPayslips" role="tab">Regular Payroll</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#thirteenthMonthPayslips" role="tab">13th Month</a>
                  </li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane fade show active" id="regularPayslips" role="tabpanel">
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
                  <div class="tab-pane fade" id="thirteenthMonthPayslips" role="tabpanel">
                    <div class="table-responsive">
                      <table class="table table-hover table-bordered">
                        <thead>
                          <tr>
                            <th>Date Posted</th>
                            <th>Year</th>
                            <th>Half</th>
                            <th>Amount</th>
                            <th>Payslip</th>
                          </tr>
                        </thead>
                        <tbody>
                          @forelse($thirteenth_month_payslips as $payslip)
                            <tr>
                              <td>{{ date('F d, Y', strtotime($payslip->created_at)) }}</td>
                              <td>{{ $payslip->year }}</td>
                              <td>{{ $payslip->half }} Half</td>
                              <td>{{ number_format($payslip->release_amount, 2) }}</td>
                              <td><a href="{{ url('/13th-month-payslip?id='.$payslip->id) }}" target="_blank"><button type="button" class="btn btn-inverse-danger btn-icon">
                                <i class="ti-file"></i>
                              </button></a></td>
                            </tr>
                          @empty
                            <tr>
                              <td colspan="5" class="text-center">No 13th month payslips yet.</td>
                            </tr>
                          @endforelse
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        
        </div>
    </div>
</div>
@endsection
