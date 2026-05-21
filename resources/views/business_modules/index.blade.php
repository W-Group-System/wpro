@extends('layouts.header')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-1">Trading ERP Dashboard</h4>
                        <p class="text-muted mb-0">Procurement, inventory, sales, delivery, invoicing, collections, returns, and analytics for trading operations.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach([
                'Total Sales This Month' => $metrics['sales_this_month'],
                'Total Purchases This Month' => $metrics['purchases_this_month'],
                'Outstanding Receivables' => $metrics['outstanding_receivables'],
                'Outstanding Payables' => $metrics['outstanding_payables'],
                'Low Stock Items' => $metrics['low_stock_items'],
                'Pending Approvals' => $metrics['pending_approvals'],
                'Pending Deliveries' => $metrics['pending_deliveries'],
            ] as $label => $value)
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-1">{{ $label }}</p>
                            <h4 class="mb-0">{{ is_numeric($value) ? number_format($value, 2) : $value }}</h4>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Master Data</h4>
                        <div class="row">
                            @foreach([
                                'suppliers' => 'Suppliers',
                                'customers' => 'Customers',
                                'items' => 'Items / Products',
                                'categories' => 'Categories',
                                'tax-codes' => 'Tax Codes',
                            ] as $master => $label)
                                <div class="col-md-2 mb-2">
                                    <a href="{{ url('business-modules/master/'.$master) }}" class="btn btn-outline-primary btn-block btn-sm">{{ $label }}</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php $moduleMap = $modules->keyBy('slug'); @endphp
        @foreach($groups as $groupName => $slugs)
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ $groupName }}</h4>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width:80px">No.</th>
                                            <th>Module</th>
                                            <th>Purpose</th>
                                            <th style="width:120px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($slugs as $slug)
                                            @php $module = $moduleMap->get($slug); @endphp
                                            @if($module)
                                                <tr>
                                                    <td>{{ $module['no'] }}</td>
                                                    <td>{{ $module['name'] }}</td>
                                                    <td>{{ $module['purpose'] }}</td>
                                                    <td><a href="{{ url('business-modules/'.$module['slug']) }}" class="btn btn-outline-primary btn-sm">Open</a></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
