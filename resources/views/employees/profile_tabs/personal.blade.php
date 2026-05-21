@php
    $employee = $user->employee;
    $employeeFullName = trim($employee->first_name.' '.($employee->middle_initial ? $employee->middle_initial.'. ' : '').$employee->last_name);
    $birthDate = $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date) : null;
    $profileDetails = [
        ['icon' => 'fa-user', 'label' => 'Nickname', 'value' => $employee->nick_name],
        ['icon' => 'fa-id-card', 'label' => 'Full Name', 'value' => $employeeFullName],
        ['icon' => 'fa-envelope', 'label' => 'Email', 'value' => $employee->personal_email],
        ['icon' => 'fa-phone', 'label' => 'Phone', 'value' => $employee->personal_number],
        ['icon' => 'fa-heart', 'label' => 'Marital Status', 'value' => $employee->marital_status],
        ['icon' => 'fa-venus-mars', 'label' => 'Gender', 'value' => $employee->gender],
        ['icon' => 'fa-star', 'label' => 'Religion', 'value' => $employee->religion],
        ['icon' => 'fa-birthday-cake', 'label' => 'Birth Date', 'value' => $birthDate ? $birthDate->format('F d, Y').' ('.$birthDate->age.' years old)' : null],
        ['icon' => 'fa-map-marker', 'label' => 'Birth Place', 'value' => $employee->birth_place],
    ];
@endphp

<div class="profile-about">
    <div class="profile-about-main">
        <div class="profile-panel profile-intro-panel">
            <div class="profile-panel-header">
                <div>
                    <span class="profile-panel-kicker">About</span>
                    <h4>Personal Information</h4>
                </div>
                @if (checkUserPrivilege('employees_edit',auth()->user()->id) == 'yes')
                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Edit Information" data-toggle="modal" data-target="#editInfo">
                        <i class="fa fa-pencil"></i> Edit
                    </button>
                @endif
            </div>

            <div class="profile-intro-card">
                <img src='{{URL::asset($employee->avatar)}}' onerror="this.src='{{URL::asset('/images/no_image.png')}}';" alt="Employee avatar">
                <div>
                    <h3>{{$employeeFullName}}</h3>
                    <p>{{$employee->position ?: 'No position set'}}</p>
                    <div class="profile-quick-tags">
                        <span><i class="fa fa-user-circle"></i> {{$employee->status ?: 'No status'}}</span>
                        <span><i class="fa fa-building"></i> {{optional($employee->company)->company_name ?? optional($employee->company)->company_code ?? 'No company set'}}</span>
                        <span><i class="fa fa-users"></i> {{optional($employee->department)->name ?? 'No department set'}}</span>
                    </div>
                </div>
            </div>

            <div class="profile-detail-grid">
                @foreach($profileDetails as $detail)
                    <div class="profile-detail-card">
                        <span class="profile-detail-icon"><i class="fa {{$detail['icon']}}"></i></span>
                        <div>
                            <small>{{$detail['label']}}</small>
                            <strong>{{$detail['value'] ?: '-'}}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="profile-about-side">
        <div class="profile-panel">
            <div class="profile-panel-header compact">
                <div>
                    <span class="profile-panel-kicker">Contact</span>
                    <h4>Reach Details</h4>
                </div>
            </div>
            <div class="profile-contact-list">
                <div>
                    <i class="fa fa-envelope"></i>
                    <span>{{$employee->personal_email ?: '-'}}</span>
                </div>
                <div>
                    <i class="fa fa-phone"></i>
                    <span>{{$employee->personal_number ?: '-'}}</span>
                </div>
            </div>
        </div>

        <div class="profile-panel">
            <div class="profile-panel-header compact">
                <div>
                    <span class="profile-panel-kicker">Location</span>
                    <h4>Address</h4>
                </div>
            </div>
            <div class="profile-address-card">
                <small>Present Address</small>
                <p>{{$employee->present_address ?: '-'}}</p>
            </div>
            <div class="profile-address-card">
                <small>Permanent Address</small>
                <p>{{$employee->permanent_address ?: '-'}}</p>
            </div>
        </div>
    </div>
</div>
