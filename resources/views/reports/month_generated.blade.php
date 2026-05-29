@extends('layouts.header')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="col-lg-12 stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Generated 13th Month</h4>
                        <form method="get" onsubmit="show();">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select data-placeholder="Select Company" class="form-control form-control-sm js-example-basic-single" style="width:100%;" name="company">
                                            <option value="">All Companies</option>
                                            @foreach($companies as $comp)
                                                <option value="{{ $comp->id }}" @if($comp->id == $company) selected @endif>{{ $comp->company_code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="year" class="form-control form-control-sm" name="year" value="{{ $year }}" placeholder="Year">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select data-placeholder="Select Half" class="form-control form-control-sm js-example-basic-single" style="width:100%;" name="half">
                                            <option value="">All Halves</option>
                                            <option value="1st" @if($half == '1st') selected @endif>1st Half</option>
                                            <option value="2nd" @if($half == '2nd') selected @endif>2nd Half</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="form-control form-control-sm btn btn-primary mb-2 btn-sm">Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="mt-2">
                            <strong>Generated people:</strong> {{ $total_postings }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date Posted</th>
                                        <th>Company</th>
                                        <th>Year</th>
                                        <th>Half</th>
                                        <th>Employee No</th>
                                        <th>Employee Name</th>
                                        <th>Department</th>
                                        <th>Account No</th>
                                        <th>Monthly Salary</th>
                                        <th>Annual 13th Month</th>
                                        <th>1st Half Released</th>
                                        <th>Release Amount</th>
                                        <th>Payslip</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($postings as $posting)
                                        @php
                                            $releaseClass = $posting->release_amount < 0 ? 'text-danger' : '';
                                        @endphp
                                        <tr>
                                            <td>{{ date('F d, Y', strtotime($posting->created_at)) }}</td>
                                            <td>{{ optional($posting->company)->company_code }}</td>
                                            <td>{{ $posting->year }}</td>
                                            <td>{{ $posting->half }} Half</td>
                                            <td>{{ $posting->employee_no }}</td>
                                            <td>{{ $posting->employee_name }}</td>
                                            <td>{{ $posting->department }}</td>
                                            <td>{{ $posting->account_number }}</td>
                                            <td>{{ number_format($posting->monthly_salary, 2) }}</td>
                                            <td>{{ number_format($posting->annual_thirteenth_month, 2) }}</td>
                                            <td>{{ number_format($posting->first_half_released, 2) }}</td>
                                            <td class="{{ $releaseClass }}">{{ number_format($posting->release_amount, 2) }}</td>
                                            <td>
                                                <a href="{{ url('/13th-month-payslip?id='.$posting->id) }}" target="_blank">
                                                    <button type="button" class="btn btn-inverse-danger btn-icon">
                                                        <i class="ti-file"></i>
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="13" class="text-center">No generated 13th month records yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($postings, 'links'))
                            <div class="mt-3">
                                {{ $postings->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
