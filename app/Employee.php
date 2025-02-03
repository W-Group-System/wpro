<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Employee extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    //

    public function beneficiaries(){
        return $this->hasMany(EmployeeBeneficiary::class,'user_id','user_id');
    }

    public function contact_person(){
        return $this->hasOne(EmployeeContactPerson::class,'user_id','user_id');
    }

    public function employee_salary(){
        return $this->hasOne(EmployeeSalary::class,'user_id','user_id');
    }

    public function classification_info()
    {
        return $this->belongsTo(Classification::class,'classification','id');
    }
    public function employee_vessel()
    {
        return $this->hasOne(EmployeeVessel::class,'user_id','user_id');
    }
    public function level_info()
    {
        return $this->belongsTo(Level::class,'level','id');
    }
    public function schedule_info()
    {
        return $this->belongsTo(Schedule::class,'schedule_id','id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function payment_info()
    {
        return $this->hasOne(PaymentInformation::class);
    }
    public function ScheduleData()
    {
        return $this->hasMany(ScheduleData::class,'schedule_id','schedule_id');
    }
    public function immediate_sup_data()
    {
        return $this->belongsTo(User::class,'immediate_sup','id');
    }
    public function user_info()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function attendances() {
        return $this->hasMany(Attendance::class,'employee_code','employee_number');
    }

    public function leaves() {
        return $this->hasMany(EmployeeLeave::class,'user_id','user_id');
    }

    public function obs() {
        return $this->hasMany(EmployeeOb::class,'user_id','user_id');
    }

    public function dtrs() {
        return $this->hasMany(EmployeeDtr::class,'user_id','user_id');
    }

    public function wfhs() {
        return $this->hasMany(EmployeeWfh::class,'user_id','user_id');
    }

    public function approved_leaves() {
        return $this->hasMany(EmployeeLeave::class,'user_id','user_id')->where('status','Approved');
    }

    public function approved_leaves_with_pay() {
        return $this->hasMany(EmployeeLeave::class,'user_id','user_id')->where('withpay','1')->where('status','Approved');
    }

    public function approved_obs() {
        return $this->hasMany(EmployeeOb::class,'user_id','user_id')->where('status','Approved');
    }

    public function approved_wfhs() {
        return $this->hasMany(EmployeeWfh::class,'user_id','user_id')->where('status','Approved');
    }

    public function approved_dtrs() {
        return $this->hasMany(EmployeeDtr::class,'user_id','user_id')->where('status','Approved');
    }

    public function approved_ots() {
        return $this->hasMany(EmployeeOvertime::class,'user_id','user_id')->where('status','Approved');
    }

    public function employee_leave_credits() {
        return $this->hasMany(EmployeeLeaveCredit::class,'user_id','user_id')->orderBy('leave_type','ASC');
    }
    public function setEmployeeCodeAttribute($value)
    {
        $this->attributes['employee_code'] = str_replace('-', '', $value);
    }
    public function employeeDocuments() {
      return $this->hasMany(EmployeeDocument::class);
    }
    public function employeeTraining() {
      return $this->hasMany(EmployeeTraining::class);
    }

    public function employeeMovement(){
        return $this->hasMany(EmployeeMovement::class,'user_id','id');
    }

    public function salaryMovement(){
        return $this->hasMany(SalaryMovement::class,'user_id','user_id');
    }
    public function salary()
    {
        return $this->belongsTo(EmployeeSalary::class,'user_id','user_id');
    }
    public function loan()
    {
        return $this->hasMany(Loan::class);
    }
    public function allowances()
    {
        return $this->hasMany(EmployeeAllowance::class,'user_id','user_id');
    }
    public function pay_instructions()
    {
        return $this->hasMany(PayInstruction::class,'site_id','employee_code');
    }
    public function attendance_generated()
    {
        return $this->hasMany(AttendanceDetailedReport::class,'employee_no','employee_code');
    }
    public function salary_adjustments()
    {
        return $this->hasMany(SalaryAdjustment::class);
    }
    public function immediateSupervisor()
    {
        return $this->belongsTo(Employee::class, 'immediate_sup', 'user_id');
    }

    // Recursive relationship to get all supervisors up to a certain level
    public function allSupervisors($level = 5)
    {
        $query = $this->immediateSupervisor();
        for ($i = 1; $i < $level; $i++) {
            $query = $query->with('immediateSupervisor');
        }
        return $query;
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'immediate_sup', 'user_id');
    }

    // Recursive relationship to get all subordinates up to a certain level
    public function allSubordinates($level = 2)
    {
        $query = $this->subordinates();
        for ($i = 1; $i < $level; $i++) {
            $query = $query->with('subordinates');
        }
        return $query;
    }
    public function get_payreg()
    {
        return $this->hasMany(PayReg::class,'employee_no','employee_code');
    }
    public function resign(){
        return $this->hasOne(ExitResign::class);
    }
    public function as_resign(){
        return $this->hasMany(ExitClearanceSignatory::class);
    }
    public function benefits()
    {
        return $this->hasMany(PayReg::class, 'employee_no', 'employee_code')
        ->whereYear('pay_period_from', now()->year)
        ->whereColumn('pay_period_from', 'posting_date');
    }
    public function employee_earned_credits()
    {
        return $this->hasMany(EmployeeEarnedLeave::class,'user_id','user_id');
    }
    protected $fillable = [
        'department_id', 
        'project',
        'position',
        'level',
        'classification',
        'immediate_sup',
        'avatar',
    ];
}
