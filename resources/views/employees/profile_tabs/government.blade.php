@php
    $employee = $user->employee;
    $taxMapping = $employee->tax_mapping;
    $computationItems = [
        ['name' => 'sss', 'label' => 'SSS', 'checked' => !$taxMapping || $taxMapping->sss == 1],
        ['name' => 'philhealth', 'label' => 'PhilHealth', 'checked' => !$taxMapping || $taxMapping->philhealth == 1],
        ['name' => 'pagibig', 'label' => 'Pag-IBIG', 'checked' => !$taxMapping || $taxMapping->pagibig == 1],
        ['name' => 'tin', 'label' => 'Tax', 'checked' => !$taxMapping || $taxMapping->tin == 1],
    ];
    $taxPercent = $taxMapping ? round($taxMapping->tax_percent * 100, 2) : '';
@endphp

<div class="profile-about">
    <div class="profile-about-main">
        <div class="profile-panel">
            <div class="profile-panel-header">
                <div>
                    <span class="profile-panel-kicker">Records</span>
                    <h4>Government Records and Licenses</h4>
                </div>
            </div>
            <div class="profile-detail-grid pt-3">
                <div class="profile-detail-card">
                    <span class="profile-detail-icon"><i class="fa fa-id-card"></i></span>
                    <div>
                        <small>SSS</small>
                        <strong>{{$employee->sss_number ?: '-'}}</strong>
                    </div>
                </div>
                <div class="profile-detail-card">
                    <span class="profile-detail-icon"><i class="fa fa-id-card"></i></span>
                    <div>
                        <small>HDMF</small>
                        <strong>{{$employee->hdmf_number ?: '-'}}</strong>
                    </div>
                </div>
                <div class="profile-detail-card">
                    <span class="profile-detail-icon"><i class="fa fa-id-card"></i></span>
                    <div>
                        <small>PhilHealth</small>
                        <strong>{{$employee->phil_number ?: '-'}}</strong>
                    </div>
                </div>
                <div class="profile-detail-card">
                    <span class="profile-detail-icon"><i class="fa fa-id-card"></i></span>
                    <div>
                        <small>TIN</small>
                        <strong>{{$employee->tax_number ?: '-'}}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="profile-about-side">
        <div class="profile-panel">
            <div class="profile-panel-header compact">
                <div>
                    <span class="profile-panel-kicker">Pay-Reg</span>
                    <h4>Payroll Computation</h4>
                </div>
            </div>
            <form method="POST" action="{{ url('account-setting-hr/updatePayrollComputationHR/'.$employee->id) }}" onsubmit="show()">
                @csrf
                <div class="p-3">
                    @foreach($computationItems as $item)
                        <label class="d-flex align-items-center justify-content-between border-bottom py-2 mb-0">
                            <span class="font-weight-bold">{{$item['label']}}</span>
                            <input type="checkbox" name="{{$item['name']}}" value="1" @if($item['checked']) checked @endif>
                        </label>
                    @endforeach
                    <div class="form-group mt-3 mb-0">
                        <label class="font-weight-bold">Tax Percent</label>
                        <input type="number" name="tax_percent" class="form-control form-control-sm" min="0" max="100" step="0.01" value="{{$taxPercent}}" placeholder="Use tax table">
                    </div>
                    @if (checkUserPrivilege('employees_edit',auth()->user()->id) == 'yes')
                        <button type="submit" class="btn btn-primary btn-sm btn-block mt-3">Save Computation Settings</button>
                    @else
                        <button type="button" class="btn btn-secondary btn-sm btn-block mt-3" disabled>Save Computation Settings</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
