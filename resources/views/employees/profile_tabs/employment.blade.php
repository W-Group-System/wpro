                        <div class="card">
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Employment Information 
                                                @if (checkUserPrivilege('employees_edit',auth()->user()->id) == 'yes')
                                                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Edit Employee Information" data-toggle="modal" data-target="#editEmpInfo"><i class="fa fa-pencil"></i></button>
                                                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Notice of Personnel Action" data-toggle="modal" data-target="#createNopa"><i class="fa fa-pencil-square-o"></i></button>
                                                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Edit employee no" data-toggle="modal" data-target="#editEmpNo">
                                                        <i class="fa fa-id-card-o"></i>
                                                    </button>
                                                @endif
                                            </h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Email </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->email}}
                                    </div>
                                </div>
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3 '>
                                        <small>Company </small>
                                    </div>
                                    <div class='col-md-9'>
                                        @if($user->employee->company) {{$user->employee->company->company_name}} @endif
                                    </div>
                                </div>
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3 '>
                                        <small>Deparment </small>
                                    </div>
                                    <div class='col-md-9'>
                                        @if($user->employee->department) {{$user->employee->department->name}} @endif
                                    </div>
                                </div>
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3 '>
                                        <small>Location </small>
                                    </div>
                                    <div class='col-md-9'>
                                        @if($user->employee->location) {{$user->employee->location}} @endif
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Classification </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->employee->classification_info ? $user->employee->classification_info->name : ""}}
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Level </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{$user->employee->level_info ? $user->employee->level_info->name : "" }}
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Date Hired </small>
                                    </div>
                                    <div class='col-md-3'>
                                        {{date('M d, Y',strtotime($user->employee->original_date_hired))}}
                                    </div>
                                    <div class='col-md-6'>
                                        @php
                                        $date_from = new DateTime($user->employee->original_date_hired);
                                        $date_diff = $date_from->diff(new DateTime(date('Y-m-d')));
                                        @endphp
                                        {{$date_diff->format('%y Year %m months %d days')}}
                                    </div>
                                </div>
                            </div>
                            @if (checkUserPrivilege('payroll_view',auth()->user()->id) == 'yes')
                            <div class="template-demo">
                                <div class='row m-2'>
                                    <div class='col-md-12 text-center mt-3 mb-3'>
                                        <strong>
                                            <h3>Salary Information 
                                                @if (checkUserPrivilege('employees_edit',auth()->user()->id) == 'yes')
                                                @if (empty($user->employee->employee_salary))
                                                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Employee Salary" data-toggle="modal" data-target="#createEmpSalary">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                @endif
                                                    <button class="btn btn-outline-primary btn-sm btn-icon-text" title="Salary Notice of Personnel Action" data-toggle="modal" data-target="#createSalaryNopa"><i class="fa fa-pencil-square-o"></i></button>
                                                @endif
                                            </h3>
                                        </strong>
                                    </div>
                                </div>
                                <div class='row  m-2 border-bottom'>
                                    <div class='col-md-3'>
                                        <small> Basic Salary </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{ $user->employee->employee_salary ? $user->employee->employee_salary->basic_salary : ""}}
                                    </div>
                                </div>
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3 '>
                                        <small>De Minimis</small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{ $user->employee->employee_salary ? $user->employee->employee_salary->de_minimis : ""}}
                                    </div>
                                </div>
                                <div class='row m-2 border-bottom'>
                                    <div class='col-md-3 '>
                                        <small>Other Allowances </small>
                                    </div>
                                    <div class='col-md-9'>
                                        {{ $user->employee->employee_salary ? $user->employee->employee_salary->other_allowance : ""}}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
