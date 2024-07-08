<div class="modal fade" id="accessModal-{{$user->id}}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Access</h5>
            </div>
            <form method="POST" action="{{url('module-access/'.$user->id)}}" onsubmit="show()">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-4">
                            Timekeeping
                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="checkbox" class="timekeepingAll">
                                    <span>All</span>
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                        <input type="checkbox" name="timekeeping_forms" class="timekeeping_checkbox" @if($user->user_privilege->timekeeping_forms == "on") checked @endif>
                                        <span>Forms</span>
                                    @else
                                        <input type="checkbox" name="timekeeping_forms" class="timekeeping_checkbox">
                                        <span>Forms</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                        <input type="checkbox" name="timekeeping_timekeeping" class="timekeeping_checkbox" @if($user->user_privilege->timekeeping_timekeeping == "on") checked @endif>
                                        <span>Timekeeping</span>
                                    @else
                                        <input type="checkbox" name="timekeeping_timekeeping" class="timekeeping_checkbox">
                                        <span>Timekeeping</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                        <input type="checkbox" name="timekeeping_generated_timekeeping" class="timekeeping_checkbox" @if($user->user_privilege->timekeeping_generated_timekeeping == "on") checked @endif>
                                        <span>Generated Timekeeping</span>
                                    @else
                                        <input type="checkbox" name="timekeeping_generated_timekeeping" class="timekeeping_checkbox">
                                        <span>Generated Timekeeping</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            Biometrics
                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="checkbox" name="biometrics_all" class="biometricsAll">
                                    <span>All</span>
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                        <input type="checkbox" name="biometrics_per_employee" class="biometrics_checkbox" @if($user->user_privilege->biometrics_per_employee == "on") checked @endif>
                                        <span>Per Employee</span>
                                    @else
                                        <input type="checkbox" name="biometrics_per_employee" class="biometrics_checkbox">
                                        <span>Per Employee</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                        <input type="checkbox" name="biometrics_per_location" class="biometrics_checkbox" @if($user->user_privilege->biometrics_per_location == "on") checked @endif>
                                        <span>Per Location</span>
                                    @else
                                        <input type="checkbox" name="biometrics_per_location" class="biometrics_checkbox">
                                        <span>Per Location</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                        <input type="checkbox" name="biometrics_per_company" class="biometrics_checkbox" @if($user->user_privilege->biometrics_per_company == "on") checked @endif>
                                        <span>Per Company</span>
                                    @else 
                                        <input type="checkbox" name="biometrics_per_company" class="biometrics_checkbox">
                                        <span>Per Company</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                        <input type="checkbox" name="biometrics_sync_biometrics" class="biometrics_checkbox" @if($user->user_privilege->biometrics_sync == "on") checked @endif>
                                        <span>Sync Biometrics</span>
                                    @else
                                        <input type="checkbox" name="biometrics_sync_biometrics" class="biometrics_checkbox">
                                        <span>Sync Biometrics</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            Settings
                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="checkbox" name="settings_all" class="settingsAll">
                                    <span>All</span>
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                        <input type="checkbox" name="settings_holiday" class="settings_checkbox" @if($user->user_privilege->settings_holiday == "on") checked @endif>
                                        <span>Holiday</span>
                                    @else
                                        <input type="checkbox" name="settings_holiday" class="settings_checkbox">
                                        <span>Holiday</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                        <input type="checkbox" name="settings_schedule" class="settings_checkbox" @if($user->user_privilege->settings_schedule == "on") checked @endif>
                                        <span>Schedules</span>
                                    @else
                                        <input type="checkbox" name="settings_schedule" class="settings_checkbox">
                                        <span>Schedules</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                        <input type="checkbox" name="settings_allowances" class="settings_checkbox" @if($user->user_privilege->settings_allowances == "on") checked @endif>
                                        <span>Allowances</span>
                                    @else
                                        <input type="checkbox" name="settings_allowances" class="settings_checkbox">
                                        <span>Allowances</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="settings_incentives" class="settings_checkbox" @if($user->user_privilege->settings_incentives == "on") checked @endif>
                                    <span>Incentives</span>
                                    @else
                                    <input type="checkbox" name="settings_incentives" class="settings_checkbox">
                                    <span>Incentives</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="settings_leave_type" class="settings_checkbox" @if($user->user_privilege->settings_leave_type == "on") checked @endif>
                                    <span>Leave Type</span>
                                    @else
                                    <input type="checkbox" name="settings_leave_type" class="settings_checkbox" >
                                    <span>Leave Type</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="settings_announcements" class="settings_checkbox" @if($user->user_privilege->settings_announcements == "on") checked @endif>
                                    <span>Announcements</span>
                                    @else
                                    <input type="checkbox" name="settings_announcements" class="settings_checkbox">
                                    <span>Announcements</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="settings_logos" class="settings_checkbox" @if($user->user_privilege->settings_logos == "on") checked @endif>
                                    <span>Logos</span>
                                    @else
                                    <input type="checkbox" name="settings_logos" class="settings_checkbox">
                                    <span>Logos</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="settings_hr_approver_setting" class="settings_checkbox" @if($user->user_privilege->settings_hr_approver_setting == "on") checked @endif>
                                    <span>HR Approver Setting</span>
                                    @else
                                    <input type="checkbox" name="settings_hr_approver_setting" class="settings_checkbox">
                                    <span>HR Approver Setting</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="settings_tax" class="settings_checkbox" @if($user->user_privilege->settings_tax == "on") checked @endif>
                                    <span>Tax</span>
                                    @else
                                    <input type="checkbox" name="settings_tax" class="settings_checkbox">
                                    <span>Tax</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            Payroll
                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="checkbox" name="payroll_all" class="payrollAll">
                                    <span>All</span>
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="payroll_payroll_register" class="payroll_checkbox" @if($user->user_privilege->payroll_payroll_register == "on") checked @endif>
                                    <span>Payroll Register</span>
                                    @else
                                    <input type="checkbox" name="payroll_payroll_register" class="payroll_checkbox">
                                    <span>Payroll Register</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="payroll_loan_register" class="payroll_checkbox" @if($user->user_privilege->payroll_loan_register == "on") checked @endif>
                                    <span>Loan Register</span>
                                    @else 
                                    <input type="checkbox" name="payroll_loan_register" class="payroll_checkbox">
                                    <span>Loan Register</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            Payroll Setting
                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="checkbox" name="payroll_setting_all" class="payrollRegisterAll">
                                    <span>All</span>
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="payroll_register_tax_mapping" class="payroll_settings_checkbox" @if($user->user_privilege->payroll_register_tax_mapping == "on") checked @endif>
                                    <span>Tax Mapping</span>
                                    @else
                                    <input type="checkbox" name="payroll_register_tax_mapping" class="payroll_settings_checkbox">
                                    <span>Tax Mapping</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            Masterfiles
                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="checkbox" name="masterfiles_all" class="masterFilesAll">
                                    <span>All</span>
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="masterfiles_companies" class="masterfiles_checkbox"  @if($user->user_privilege->masterfiles_companies == "on") checked @endif>
                                    <span>Companies</span>
                                    @else
                                    <input type="checkbox" name="masterfiles_companies" class="masterfiles_checkbox">
                                    <span>Companies</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="masterfiles_departments" class="masterfiles_checkbox" @if($user->user_privilege->masterfiles_departments == "on") checked @endif>
                                    <span>Departments</span>
                                    @else
                                    <input type="checkbox" name="masterfiles_departments" class="masterfiles_checkbox">
                                    <span>Departments</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="masterfiles_locations" class="masterfiles_checkbox"  @if($user->user_privilege->masterfiles_locations == "on") checked @endif>
                                    <span>Locations</span>
                                    @else
                                    <input type="checkbox" name="masterfiles_locations" class="masterfiles_checkbox">
                                    <span>Locations</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="masterfiles_projects" class="masterfiles_checkbox"  @if($user->user_privilege->masterfiles_projects == "on") checked @endif>
                                    <span>Projects</span>
                                    @else 
                                    <input type="checkbox" name="masterfiles_projects" class="masterfiles_checkbox">
                                    <span>Projects</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="masterfiles_loan_types" class="masterfiles_checkbox"  @if($user->user_privilege->masterfiles_loan_types == "on") checked @endif>
                                    <span>Loan Types</span>
                                    @else 
                                    <input type="checkbox" name="masterfiles_loan_types" class="masterfiles_checkbox">
                                    <span>Loan Types</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="masterfiles_employee_allowances" class="masterfiles_checkbox"  @if($user->user_privilege->masterfiles_employee_allowances == "on") checked @endif>
                                    <span>Employee Allowances</span>
                                    @else 
                                    <input type="checkbox" name="masterfiles_employee_allowances" class="masterfiles_checkbox">
                                    <span>Employee Allowances</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="masterfiles_employee_leave_credits" class="masterfiles_checkbox"  @if($user->user_privilege->masterfiles_employee_leave_credits == "on") checked @endif>
                                    <span>Employee Leave Credits</span>
                                    @else
                                    <input type="checkbox" name="masterfiles_employee_leave_credits" class="masterfiles_checkbox">
                                    <span>Employee Leave Credits</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="masterfiles_employee_leave_balances" class="masterfiles_checkbox"  @if($user->user_privilege->masterfiles_employee_leave_balances == "on") checked @endif>
                                    <span>Employee Leave Balances</span>
                                    @else 
                                    <input type="checkbox" name="masterfiles_employee_leave_balances" class="masterfiles_checkbox">
                                    <span>Employee Leave Balances</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="masterfiles_employee_leave_earned" class="masterfiles_checkbox"  @if($user->user_privilege->masterfiles_employee_leave_earned == "on") checked @endif>
                                    <span>Employee Earned Leaves</span>
                                    @else 
                                    <input type="checkbox" name="masterfiles_employee_leave_earned" class="masterfiles_checkbox">
                                    <span>Employee Earned Leaves</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="row">
                                Reports
                                <div class="col-lg-12">
                                    <input type="checkbox" name="reports_all" class="reportsAll">
                                    <span>All</span>
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="reports_leave" class="report_checkbox" @if($user->user_privilege->reports_leave == "on") checked @endif>
                                    <span>Leave Reports</span>
                                    @else 
                                    <input type="checkbox" name="reports_leave" class="report_checkbox">
                                    <span>Leave Reports</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="reports_overtime" class="report_checkbox" @if($user->user_privilege->reports_overtime == "on") checked @endif>
                                    <span>Overtime Reports</span>
                                    @else 
                                    <input type="checkbox" name="reports_overtime" class="report_checkbox">
                                    <span>Overtime Reports</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="reports_wfh" class="report_checkbox" @if($user->user_privilege->reports_wfh == "on") checked @endif>
                                    <span>WFH Reports</span>
                                    @else
                                    <input type="checkbox" name="reports_wfh" class="report_checkbox">
                                    <span>WFH Reports</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="reports_ob" class="report_checkbox" @if($user->user_privilege->reports_ob == "on") checked @endif>
                                    <span>OB Reports</span>
                                    @else
                                    <input type="checkbox" name="reports_ob" class="report_checkbox">
                                    <span>OB Reports</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="reports_dtr" class="report_checkbox" @if($user->user_privilege->reports_dtr == "on") checked @endif>
                                    <span>DTR Reports</span>
                                    @else
                                    <input type="checkbox" name="reports_dtr" class="report_checkbox">
                                    <span>DTR Reports</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="reports_total_expenses" class="report_checkbox" @if($user->user_privilege->reports_total_expenses == "on") checked @endif>
                                    <span>Total Expenses</span>
                                    @else
                                    <input type="checkbox" name="reports_total_expenses" class="report_checkbox">
                                    <span>Total Expenses</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="reports_loans" class="report_checkbox" @if($user->user_privilege->reports_loans == "on") checked @endif>
                                    <span>Loans Reports</span>
                                    @else
                                    <input type="checkbox" name="reports_loans" class="report_checkbox">
                                    <span>Loans Reports</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="reports_incentive" class="report_checkbox" @if($user->user_privilege->reports_incentive == "on") checked @endif>
                                    <span>Incentive Reports</span>
                                    @else
                                    <input type="checkbox" name="reports_incentive" class="report_checkbox">
                                    <span>Incentive Reports</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="reports_payroll" class="report_checkbox" @if($user->user_privilege->reports_payroll == "on") checked @endif>
                                    <span>Payroll Reports</span>
                                    @else 
                                    <input type="checkbox" name="reports_payroll" class="report_checkbox">
                                    <span>Payroll Reports</span>
                                    @endif
                                </div>
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="reports_attendance" class="report_checkbox" @if($user->user_privilege->reports_attendance == "on") checked @endif>
                                    <span>Attendance Reports</span>
                                    @else
                                    <input type="checkbox" name="reports_attendance" class="report_checkbox">
                                    <span>Attendance Reports</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            Daily Schedule
                            <div class="row">
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="daily_schedule" @if($user->user_privilege->upload_daily_schedule == "on") checked @endif>
                                    <span>Daily Schedule</span>
                                    @else
                                    <input type="checkbox" name="daily_schedule">
                                    <span>Daily Schedule</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            Upload OB/OT/Leaves
                            <div class="row">
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="upload_ob_ot_leaves" @if($user->user_privilege->upload_ob == "on") checked @endif>
                                    <span>Upload OB/OT/Leaves</span>
                                    @else
                                    <input type="checkbox" name="upload_ob_ot_leaves">
                                    <span>Upload OB/OT/Leaves</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            Employee
                            <div class="row">
                                <div class="col-lg-12">
                                    @if($user->user_privilege)
                                    <input type="checkbox" name="employees" @if($user->user_privilege->employees == "on") checked @endif>
                                    <span>Employee</span>
                                    @else
                                    <input type="checkbox" name="employees">
                                    <span>Employee</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>