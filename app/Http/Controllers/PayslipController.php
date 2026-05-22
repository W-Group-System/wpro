<?php

namespace App\Http\Controllers;

use App\AttendanceDetailedReport;
use Illuminate\Support\Facades\Auth;
use App\Imports\PayInstructionImport;
use App\Exports\PayInstructionExport;
// use Excel;
use App\Payroll;
use App\Employee;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use App\AttSummary;
use App\Company;
use App\EmployeeOb;
use App\Imports\PayRegImport;
use App\PayInstruction;
use App\Location;
use App\PayrollRecord;
use App\ScheduleData;
use App\Payregs;
use App\PayregInstruction;
use App\PayregLoan;
use App\PayregAllowance;
use App\ContributionSSS;
use App\ThirteenthMonthPosting;
use App\Department;
use App\EmployeeAllowance;
use App\SalaryAdjustment;
use App\Loan;
use Barryvdh\DomPDF\PDF;
use Dompdf\Options;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\PayrollRegisterCalculator;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

class PayslipController extends Controller
{
    //
    public function view ()
    {
        $payslips = Payregs::where('employee_no',auth()->user()->employee->employee_code)->orderBy('id','desc')->get();
        $thirteenth_month_payslips = Schema::hasTable('thirteenth_month_postings')
            ? ThirteenthMonthPosting::where('employee_no', auth()->user()->employee->employee_code)
                ->orderBy('year', 'desc')
                ->orderBy('half', 'desc')
                ->orderBy('id', 'desc')
                ->get()
            : collect();
        return view('payslips.payslips',
        array(
            'header' => 'payslips',
            'payslips' => $payslips,
            'thirteenth_month_payslips' => $thirteenth_month_payslips
            
        ));
    }
    // public function ytd_report(Request $request)
    // {
    //     ini_set('memory_limit', '-1');
    
    //     $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
    //     $allowed_locations = getUserAllowedLocations(auth()->user()->id);
    //     $allowed_projects = getUserAllowedProjects(auth()->user()->id);

    //     $employees = Employee::select('id','user_id','employee_number','first_name','last_name','employee_code')
    //                             ->whereIn('company_id', $allowed_companies)
    //                             ->when($allowed_locations,function($q) use($allowed_locations){
    //                                 $q->whereIn('location',$allowed_locations);
    //                             })
    //                             ->when($allowed_projects,function($q) use($allowed_projects){
    //                                 $q->whereIn('project',$allowed_projects);
    //                             })
    //                             ->get();
    //     $from_date = $request->from;
    //     $date_range =  [];
    //     $emp_code = $request->employee;
    //     $emp_data = [];
    //     $employee_data = $request->employee;
    //     $allowances = [];
    //     $allowances_data = [];
    //     $instructions = [];
    //     $instructions_data = [];
    //     $loans = [];
    //     $loans_data = [];
    //     $emp = null;
        

    //     $company = isset($request->company) ? $request->company : "";

    //     if ($from_date != null) {
    //         if ($request->company == null || $request->company === 'null') {
    //             $emp = Employee::with('company')->where('employee_code',$request->employee)->first();
                
    //             $emp_data = Payregs::with('pay_allowances','pay_loan')->where('employee_no',$request->employee)->whereYear('cut_off_date',$from_date)->get();

    //             // dd($emp_data);
    //             // $allowances = $emp_data->pay_allowances;
                
    //         }elseif ($request->company) {
    //             $emp = Employee::with('company')
    //                 ->where('company_id', $request->company)
    //                 ->paginate(50);
                
    //             $emp_data = Payregs::with('pay_allowances','pay_loan','pay_instructions')
    //                 ->whereIn('employee_no', $emp->pluck('employee_code')->toArray())
    //                 ->whereYear('cut_off_date', $from_date)
    //                 ->paginate(50);
    //             // dd($emp_data);

    //         }
            
    //         $allowances = PayregAllowance::with('allowance_type')->select('allowance_id')->whereIn('payreg_id',$emp_data->pluck('id')->toArray())->groupBy('allowance_id')->get();
    //         $loans = PayregLoan::with('loan_type')->select('loan_type_id')->whereIn('payreg_id',$emp_data->pluck('id')->toArray())->groupBy('loan_type_id')->get();
    //         $instructions = PayregInstruction::select('instruction_name')->where('amount','<',0)->whereIn('payreg_id',$emp_data->pluck('id')->toArray())->groupBy('instruction_name')->get();
    //         $allowances_data = PayregAllowance::with('allowance_type')->whereIn('payreg_id',$emp_data->pluck('id')->toArray())->get();
    //         $loans_data = PayregLoan::with('loan_type')->whereIn('payreg_id',$emp_data->pluck('id')->toArray())->get();
    //         $instructions_data = PayregInstruction::whereIn('payreg_id',$emp_data->pluck('id')->toArray())->get();
    //         // dd())
    //     }
    //     else

    //     {
    //         $from_date = date('Y');
    //     }
    //     $companies = Company::whereHas('employee_has_company')
    //                             ->whereIn('id',$allowed_companies)
    //                             ->get();
    //                             // dd($from_date)
    //     return view(
    //         'reports.ytd_report',
    //         array(
    //             'header' => 'biometrics',
    //             'employees' => $employees,
    //             'from_date' => $from_date,
    //             'date_range' => $date_range,
    //             'emp_code' => $emp_code,
    //             'emp_data' => $emp_data,
    //             'employee_data' => $employee_data,
    //             'companies' => $companies,
    //             'company' => $company,
    //             'allowances' => $allowances,
    //             'allowances_data' => $allowances_data,
    //             'loans' => $loans,
    //             'loans_data' => $loans_data,
    //             'empD' => $emp,
    //             'instructions' => $instructions,
    //             'instructions_data' => $instructions_data,
    //         )
    //     );
    // }


    public function payroll_datas(Request $request)
    {
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        // Support single or multiple company selection
        $company = array_values(array_filter((array)($request->company ?? [])));
        $cut_off = collect();
        $from = $request->from;
        $to = $request->to;
        $cutoff = $request->cut_off;
        $names = collect();
        $dates = collect();
        $absents_data = collect();
        $allowances_total = collect();
        $salary_adjustments = collect();
        $loans_all = collect();
        $instructions = collect();
        $benefit_instructions = collect();
        $deduction_instructions = collect();
        $shifts = collect();

        $last_cut_off = collect();
        $sss = ContributionSSS::orderBy('salary_to','asc')->get();
        $payroll_rows = collect();

        if (count($company)) {
            // Cut-offs already posted for any of the selected companies
            $cut_off_pay_reg = Payregs::select('cut_off_date')
                ->whereIn('company_id', $company)
                ->groupBy('cut_off_date')
                ->pluck('cut_off_date')
                ->toArray();

            // Available (not yet posted) cut-offs across all selected companies
            $cut_off = AttendanceDetailedReport::select('cut_off_date')
                ->groupBy('cut_off_date')
                ->orderBy('cut_off_date', 'desc')
                ->whereNotIn('cut_off_date', $cut_off_pay_reg)
                ->whereIn('company_id', $company)
                ->get();

            if ($request->cut_off) {
                foreach ($company as $cid) {
                    $payrollData = app(PayrollRegisterCalculator::class)->build($cid, $cutoff);

                    // Merge rows from each company
                    $payroll_rows = $payroll_rows->merge($payrollData->rows);

                    // Merge dynamic column definitions, deduplicating by key
                    $allowances_total     = $allowances_total->merge($payrollData->allowances_total)->unique('allowance_id')->values();
                    $salary_adjustments   = $salary_adjustments->merge($payrollData->salary_adjustments)->unique('name')->values();
                    $loans_all            = $loans_all->merge($payrollData->loans_all)->unique('loan_type_id')->values();
                    $benefit_instructions = $benefit_instructions->merge($payrollData->benefit_instructions)->unique('benefit_name')->values();
                    $deduction_instructions = $deduction_instructions->merge($payrollData->deduction_instructions)->unique('benefit_name')->values();

                    // Period data is shared for the same cut-off
                    $dates       = $payrollData->dates;
                    $last_cut_off = $payrollData->last_cut_off;
                    $absents_data = $payrollData->absents_data;
                    $shifts      = $payrollData->shifts;
                    $names       = $payrollData->names;
                    $from        = $payrollData->from;
                    $to          = $payrollData->to;
                    $instructions = $payrollData->instructions;
                    $sss         = $payrollData->sss;
                }
                $payroll_rows = $payroll_rows->values();
            }
        }

        // $companies = Company::whereHas('employee_has_company')
        //     ->whereIn('id', $allowed_companies)
        //     ->get();
        $companies = Company::whereIn('id', $allowed_companies)->get();

        // $departments = Department::whereHas('employee_has_department')
        //     ->where('status', 1)
        //     ->get();

      
        return view('payroll.pay_reg',
        array(
            'header' => 'Payroll',
            'cut_off' => $cut_off,
            'from' => $from,
            'to' => $to,
            'companies' => $companies,
            'company' => $company,
            // 'departments' => $departments,
            // 'department' => $department,
            'cutoff' => $cutoff,
            'names' => $names,
            'sss' => $sss,
            'dates' => $dates,
            'absents_data' => $absents_data,
            'allowances_total' => $allowances_total,
            'loans_all' => $loans_all,
            'instructions' => $instructions,
            'benefit_instructions' => $benefit_instructions,
            'deduction_instructions' => $deduction_instructions,
            'shifts' => $shifts,
            'last_cut_off' => $last_cut_off,
            'salary_adjustments' => $salary_adjustments,
            'payroll_rows' => $payroll_rows,
        )
        );
    }
    public function generatedPayroll(Request $request)
    {
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $company = isset($request->company) ? $request->company : "";
        $cut_off = [];
        $from_date = $request->from;
        $to_date = $request->to;
        $cutoff = $request->cut_off;
        $pay_registers = [];
        $dates = [];
        $absents_data = [];
        $allowances_total = [];
        $salary_adjustments = [];
        $loans_all = [];
        $instructions = [];
        if($request->company)
        {
            $cut_off = Payregs::select('cut_off_date')->where('company_id',$request->company)->groupBy('cut_off_date')->get();

            if($cutoff)
            {
                $pay_registers = Payregs::with('pay_allowances','pay_loan','pay_instructions','salary_adjustments_data')->where('cut_off_date',$cutoff)->where('company_id',$request->company)->get();
                $pay_reg_ids = $pay_registers->pluck('id')->toArray();
                $salary_adjustments = SalaryAdjustment::whereIn('pay_reg_id',$pay_reg_ids)->select('name')->groupBy('name')->get();
                $instructions = PayregInstruction::whereIn('payreg_id',$pay_reg_ids)->select('instruction_name')->groupBy('instruction_name')->get();
                $allowances_total = PayregAllowance::with('allowance_type')->whereIn('payreg_id',$pay_reg_ids)->select('allowance_id')->groupBy('allowance_id')->get();
                $loans_all = PayregLoan::with('loan_type')->whereIn('payreg_id',$pay_reg_ids)->select('loan_type_id')->groupBy('loan_type_id')->get();
                // dd($pay_registers);

            }
        }
       $companies = Company::whereHas('employee_has_company')
        ->whereIn('id', $allowed_companies)
        ->get();

        // dd($pay_registers);
        return view('payroll.generated-payroll',
        array(
            'header' => 'Payroll',
            'cut_off' => $cut_off,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'companies' => $companies,
            'company' => $company,
            'cutoff' => $cutoff,
            'pay_registers' => $pay_registers,
            'dates' => $dates,
            'absents_data' => $absents_data,
            'allowances_total' => $allowances_total,
            'loans_all' => $loans_all,
            'instructions' => $instructions,
            'salary_adjustments' => $salary_adjustments,
        )
        );
    }
    public function postPayRoll(Request $request)
    {
        $payroll_a = AttendanceDetailedReport::select(DB::raw('DAY(log_date) as log_date'))
            ->where('company_id', $request->company)
            ->where('cut_off_date', $request->cut_off)
            ->groupBy('log_date')
            ->get()
            ->where('log_date', 10)
            ->first();

        $payrollData = app(PayrollRegisterCalculator::class)->build($request->company, $request->cut_off);

        foreach($payrollData->rows as $row)
        {
            $employee_code = $row->values['employee_no'];
            $employee_data = Employee::with([
                'allowances' => function ($query) {
                    $query->where('status', 'Active');
                },
                'pay_instructions' => function ($query) use ($request) {
                    $query->where('start_date', '>=', $request->cut_off)
                        ->where('end_date', '<=', $request->cut_off);
                },
                'loan' => function ($query) {
                    $query->where('status', 'Active');
                },
                'loan.pay',
            ])->where('employee_code',$employee_code)->first();
            $pay_register = new Payregs;
            $this->fillPayregFromCalculatedRow($pay_register, $row);
            $pay_register->pay_period_from = $request->from ?: $payrollData->from;
            $pay_register->pay_period_to = $request->to ?: $payrollData->to;
            $pay_register->posting_date = $request->posting_date;
            $pay_register->posted_by = auth()->user()->id;
            $pay_register->cut_off_date = $request->cut_off;
            $pay_register->company_id = $request->company;
            // $pay_register->department_id = $request->department;
            $pay_register->save();

            if ($employee_data) {
                $salary_adjustements = SalaryAdjustment::where('employee_id',$employee_data->id)->where('pay_reg_id',null)->get();
                foreach($salary_adjustements as $sad)
                {
                    $sad->cut_off_date = $request->to ?: $payrollData->to;
                    $sad->pay_reg_id = $pay_register->id;
                    $sad->save();
                }
            }

            $this->storeScheduledPayregItems($employee_data, $pay_register, (bool) $payroll_a);

        }
        Alert::success('Successfully Generated')->persistent('Dismiss');
        return redirect('/pay-reg');
    }

    private function fillPayregFromCalculatedRow($payRegister, $row)
    {
        $values = $row->values;
        $attendance = $row->attendance;

        $payRegister->employee_no = $values['employee_no'];
        $payRegister->last_name = $values['last_name'];
        $payRegister->first_name = $values['first_name'];
        $payRegister->middle_name = $values['middle_name'];
        $payRegister->department = $values['department'];
        $payRegister->cost_center = $values['cost_center'];
        $payRegister->account_number = $values['account_number'];
        $payRegister->pay_rate = $values['pay_rate'];
        $payRegister->tax_status = $values['tax_status'];
        $payRegister->days_rendered = $values['days_rendered'];
        $payRegister->basic_pay = $values['basic_pay'];
        $payRegister->lh_nd = $attendance->total_lh_nd;
        $payRegister->lh_nd_amount = $values['lh_nd_amount'];
        $payRegister->lh_nd_ge = $attendance->total_lh_nd_over_eight;
        $payRegister->lh_nd_ge_amount = $values['lh_nd_ge_amount'];
        $payRegister->lh_ot = $attendance->total_lh_ot;
        $payRegister->lh_ot_amount = $values['lh_ot_amount'];
        $payRegister->lh_ot_ge = $attendance->total_lh_ot_over_eight;
        $payRegister->lh_ot_ge_amount = $values['lh_ot_ge_amount'];
        $payRegister->sh_nd = $attendance->total_sh_nd;
        $payRegister->sh_nd_amount = $values['sh_nd_amount'];
        $payRegister->sh_nd_ge = $attendance->total_sh_nd_over_eight;
        $payRegister->sh_nd_ge_amount = $values['sh_nd_ge_amount'];
        $payRegister->sh_ot = $attendance->total_sh_ot;
        $payRegister->sh_ot_amount = $values['sh_ot_amount'];
        $payRegister->sh_ot_ge = $attendance->total_sh_ot_over_eight;
        $payRegister->sh_ot_ge_amount = $values['sh_ot_ge_amount'];
        $payRegister->reg_nd = $attendance->total_reg_nd;
        $payRegister->reg_nd_amount = $values['reg_nd_amount'];
        $payRegister->reg_ot = $attendance->total_reg_ot;
        $payRegister->reg_ot_amount = $values['reg_ot_amount'];
        $payRegister->reg_ot_nd = $attendance->total_reg_ot_nd;
        $payRegister->reg_ot_nd_amount = $values['reg_ot_nd_amount'];
        $payRegister->rst_nd = $attendance->total_rst_nd;
        $payRegister->rst_nd_amount = $values['rst_nd_amount'];
        $payRegister->rst_nd_ge = $attendance->total_rst_nd_over_eight;
        $payRegister->rst_nd_ge_amount = $values['rst_nd_ge_amount'];
        $payRegister->rst_ot = $attendance->total_rst_ot;
        $payRegister->rst_ot_amount = $values['rst_ot_amount'];
        $payRegister->rst_ot_ge = $attendance->total_rst_ot_over_eight;
        $payRegister->rst_ot_ge_amount = $values['rst_ot_ge_amount'];
        $payRegister->ot_total = $values['total_ot_pay'];
        $payRegister->salary_adjustment = $values['salary_adjustment'];
        $payRegister->taxable_benefits_total = $values['taxable_benefits_total'];
        $payRegister->gross_taxable_income = $values['gross_taxable_income'];
        $payRegister->days_absent = $values['days_absent'];
        $payRegister->absent_amount = $values['absent_amount'];
        $payRegister->tardiness_total = $values['tardiness_total'];
        $payRegister->tardiness_amount = $values['tardiness_amount'];
        $payRegister->undertime_total = $values['undertime_total'];
        $payRegister->undertime_amount = $values['undertime_amount'];
        $payRegister->sss_ec = $values['sss_ecc'];
        $payRegister->sss_employee_share = $values['sss_ee'];
        $payRegister->sss_employer_share = $values['sss_er'];
        $payRegister->hdmf_employee_share = $values['hdmf'];
        $payRegister->hdmf_employer_share = $values['hdmf'];
        $payRegister->phic_employee_share = $values['philhealth'];
        $payRegister->phic_employer_share = $values['philhealth'];
        $payRegister->mpf_employee_share = $values['wisp_ee'];
        $payRegister->mpf_employer_share = $values['wisp_er'];
        $payRegister->statutory_total = $values['statutory_total'];
        $payRegister->taxable_deductible_total = $values['taxable_deductible_total'];
        $payRegister->net_taxable_income = $values['net_taxable_income'];
        $payRegister->withholding_tax = $values['withholding_tax'];
        $payRegister->deminimis = $values['deminimis'];
        $payRegister->other_allowances_basic_pay = $values['other_allowances_basic_pay'];
        $payRegister->subliq = $values['subliq'];
        $payRegister->nontaxable_benefits_total = $values['nontaxable_benefits_total'];
        $payRegister->nontaxable_deductible_benefits_total = $values['nontaxable_deductible_benefits_total'];
        $payRegister->gross_pay = $values['gross_pay'];
        $payRegister->deductions_total = $values['deductions_total'];
        $payRegister->netpay = $values['netpay'];
    }

    private function storePayregLoanDeduction($loanData, $payregId)
    {
        $loanId = is_object($loanData) ? $loanData->id : $loanData;
        $loan = Loan::with('pay')->find($loanId);
        if (!$loan || $loan->status != 'Active') {
            return;
        }

        $loanBalance = payrollLoanBalance($loan);
        if ($loanBalance <= 0) {
            $loan->status = 'Inactive';
            $loan->save();
            return;
        }

        $deductionAmount = payrollLoanDeductionAmount($loan);
        if ($deductionAmount <= 0) {
            return;
        }

        $payregLoan = new PayregLoan;
        $payregLoan->loan_type_id = $loan->loan_type_id;
        $payregLoan->payreg_id = $payregId;
        $payregLoan->loan_id = $loan->id;
        $payregLoan->amount = $deductionAmount;
        $payregLoan->employee_id = $loan->employee_id;
        $payregLoan->remarks = $loan->schedule;
        $payregLoan->save();

        if (($loanBalance - $deductionAmount) <= 0) {
            $loan->status = 'Inactive';
            $loan->save();
        }
    }

    private function storeScheduledPayregItems($employee, $payRegister, $isFirstCutoff)
    {
        if (!$employee) {
            return;
        }

        foreach (payrollScheduledItems($employee->allowances, $isFirstCutoff) as $allowance) {
            $payregAllowance = new PayregAllowance;
            $payregAllowance->allowance_id = $allowance->allowance_id;
            $payregAllowance->payreg_id = $payRegister->id;
            $payregAllowance->amount = $allowance->allowance_amount;
            $payregAllowance->user_id = $allowance->user_id;
            $payregAllowance->remarks = $allowance->schedule;
            $payregAllowance->save();
        }

        foreach (payrollScheduledItems($employee->pay_instructions, $isFirstCutoff, 'frequency') as $instruction) {
            $payregInstruction = new PayregInstruction;
            $payregInstruction->instruction_name = $instruction->benefit_name;
            $payregInstruction->payreg_id = $payRegister->id;
            $payregInstruction->amount = $instruction->amount;
            $payregInstruction->employee_code = $instruction->site_id;
            $payregInstruction->remarks = $instruction->frequency;
            $payregInstruction->created_by = auth()->user()->id;
            $payregInstruction->save();
        }

        foreach (payrollScheduledItems($employee->loan, $isFirstCutoff) as $loan) {
            $this->storePayregLoanDeduction($loan->id, $payRegister->id);
        }
    }

    public function importPayRegExcel(Request $request)
    {
        Excel::import(new PayRegImport,request()->file('import_file'));
           
        return back();
    }

    public function payroll_instruction(Request $request)
    {
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $company = isset($request->company) ? $request->company : "";
        $cut_off = [];
        $from_date = $request->from;
        $to_date = $request->to;
        $cutoff = $request->cut_off;
        $names = [];
        $locations = Location::orderBy('location','ASC')->get();
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $employees_selection = Employee::whereIn('company_id',$allowed_companies)->where('status','Active')->get();
        $name_filter = $request->name;
        $company_filter = $request->company;
        $instruction_companies = PayInstruction::select('location')
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->groupBy('location')
            ->orderBy('location', 'ASC')
            ->pluck('location');
        $names = PayInstruction::when($name_filter, function ($query) use ($name_filter) {
                $query->where('name', 'LIKE', '%' . $name_filter . '%');
            })
            ->when($company_filter, function ($query) use ($company_filter) {
                $query->where('location', $company_filter);
            })
            ->orderBy('start_date', 'DESC')
            ->paginate(10);
       $companies = Company::whereHas('employee_has_company')
        ->whereIn('id', $allowed_companies)
        ->get();
        
      
        return view('payroll.pay_instruction',
        array(
            'header' => 'Payroll',
            // 'cut_off' => $cut_off,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'companies' => $companies,
            'company' => $company,
            'name_filter' => $name_filter,
            'company_filter' => $company_filter,
            'instruction_companies' => $instruction_companies,
            'cutoff' => $cutoff,
            'names' => $names,
            'locations' => $locations,
            'employees_selection' => $employees_selection,
        )
        );
    }

    public function add_payroll_instruction(Request $request)
    {
        $amount = $request->amount;
        if ($request->deductible=='YES'){
            $amount=-$amount;
        }
        $payroll_instruction = new PayInstruction;
        $payroll_instruction->location = $request->company;
        $payroll_instruction->site_id = $request->site_id;
        $payroll_instruction->name = $request->employee;
        $payroll_instruction->start_date = $request->start_date;
        $payroll_instruction->end_date = $request->start_date;
        $payroll_instruction->benefit_name = $request->benefit_name;
        $payroll_instruction->amount = $amount;
        $payroll_instruction->frequency = $request->frequency;
        $payroll_instruction->deductible = $request->deductible;
        $payroll_instruction->remarks = $request->remarks;
        $payroll_instruction->created_by = Auth::user()->id;
        $payroll_instruction->save();

        Alert::success('Successfully Stored')->persistent('Dismiss');
        return back();
    }

    public function importPayInstructionExcel(Request $request)
    {
        Excel::import(new PayInstructionImport,request()->file('import_file'));
           
        return back();
    }

    public function export(Request $request)
    {
        return Excel::download(new PayInstructionExport, 'Payroll Instruction.xlsx');
    }
    
    
    public function attendances()
    {
        $attendances =  AttSummary::orderBy('employee','asc')->get();
        return view('payroll.timekeeping',
        array(
            'header' => 'Timekeeping',
            'attendances' => $attendances,
            'attendances' => $attendances,
            
        ));
    }
    function upload_attendance(Request $request)
    {
        $path = $request->file('file')->getRealPath();
        $data = Excel::load($path)->get();

        dd($data);
        if($data->count() > 0)
        {
            // dd($data);
        foreach($data->toArray() as $key => $value)
        {
            $payroll = new AttSummary;
            $payroll->company = $value['company'];
            $payroll->badge_no = $value['badge_no'];
            $payroll->employee = $value['employee'];
            $payroll->location = $value['location'];
            $payroll->period_from = date('Y-m-d',strtotime($value['period_from']));
            $payroll->period_to = date('Y-m-d',strtotime($value['period_to']));
            $payroll->tot_days_absent = $value['tot_days_absent'];
            $payroll->tot_days_work = $value['tot_days_work'];
            $payroll->tot_lates = $value['tot_lates'];
            $payroll->total_adjstmenthrs = $value['total_adjstmenthrs'];
            $payroll->tot_overtime_reg = $value['tot_overtime_reg'];
            $payroll->night_differential = $value['night_differential'];
            $payroll->night_differential_ot = $value['night_differential_ot'];
            $payroll->tot_regholiday = $value['tot_regholiday'];
            $payroll->tot_overtime_regholiday = $value['tot_overtime_regholiday'];
            $payroll->tot_regholiday_nightdiff = $value['tot_regholiday_nightdiff'];
            $payroll->tot_overtime_regholiday_nightdiff = $value['tot_overtime_regholiday_nightdiff'];
            $payroll->tot_spholiday = $value['tot_spholiday'];
            $payroll->tot_overtime_spholiday = $value['tot_overtime_spholiday'];
            $payroll->tot_spholiday_nightdiff = $value['tot_spholiday_nightdiff'];
            $payroll->tot_overtime_spholiday_nightdiff = $value['tot_overtime_spholiday_nightdiff'];
            $payroll->tot_rest = $value['tot_rest'];
            $payroll->tot_overtime_rest = $value['tot_overtime_rest'];
            $payroll->night_differential_rest = $value['night_differential_rest'];
            $payroll->night_differential_ot_rest = $value['night_differential_ot_rest'];
            $payroll->tot_overtime_rest_regholiday = $value['tot_overtime_rest_regholiday'];
            $payroll->night_differential_rest_regholiday = $value['night_differential_rest_regholiday'];
            $payroll->tot_overtime_night_diff_rest_regholiday = $value['tot_overtime_night_diff_rest_regholiday'];
            $payroll->tot_sprestholiday = $value['tot_sprestholiday'];
            $payroll->tot_overtime_sprestholiday = $value['tot_overtime_sprestholiday'];
            $payroll->tot_sprestholiday_nightdiff = $value['tot_sprestholiday_nightdiff'];
            $payroll->tot_overtime_sprestholiday_nightdiff = $value['tot_overtime_sprestholiday_nightdiff'];
            $payroll->total_undertime = $value['total_undertime'];
            $payroll->sick_leave = $value['sick_leave'];
            $payroll->vacation_leave = $value['vacation_leave'];
            $payroll->sick_leave_nopay = $value['sick_leave_nopay'];
            $payroll->vacation_leave_nopay = $value['vacation_leave_nopay'];
            $payroll->workfromhome = $value['workfromhome'];
            $payroll->offbusiness = $value['offbusiness'];
            $payroll->save();
        }
        }
        Alert::success('Successfully Import Attendance')->persistent('Dismiss');
     return back();
    }
    function import(Request $request)
    {
        $path = $request->file('file')->getRealPath();

        $data = Excel::load($path)->get();
        if($data->count() > 0)
        {
            // dd($data);
        foreach($data->toArray() as $key => $value)
        {
            if($value['empno'] != null)
            {
            $payroll = new Payroll;
            $payroll->emp_code  = $value['empno'];
            $payroll->bank_acctno  = $value['bank_acount'];
            $payroll->bank  = $value['bank'];
            $payroll->name  = $value['name'];
            $payroll->position  = $value['position'];
            $payroll->emp_status  = $value['employment_status'];
            $payroll->company  = $value['group'];
            $payroll->department  = $value['department'];
            $payroll->location  = $value['location'];
            $payroll->date_hired  = date('Y-m-d',strtotime($value['datehired']));
            $payroll->date_from  = date('Y-m-d',strtotime($value['cut_off_from']));
            $payroll->date_to  = date('Y-m-d',strtotime($value['cut_off_to']));
            $payroll->month_pay  = $value['monthly_basic_pay'];
            $payroll->daily_pay  = $value['daily_rate'];
            $payroll->semi_month_pay  = $value['basicpay_halfmonth'];
            $payroll->absences  = $value['absences_amount'];
            $payroll->late  = $value['late_amount'];
            $payroll->undertime  = $value['undertime_amount'];
            $payroll->salary_adjustment  = $value['salary_adjustment_taxable'];
            $payroll->overtime  = $value['overtime_amount'];
            $payroll->meal_allowance  = $value['meal_allowance'];
            $payroll->salary_allowance  = $value['salary_allowance'];
            $payroll->oot_allowance  = $value['out_of_town_allowance'];
            $payroll->inc_allowance  = $value['incentives_allowance'];
            $payroll->rel_allowance  = $value['relocation_allowance'];
            $payroll->disc_allowance  = $value['discretionary_allowance'];
            $payroll->trans_allowance  = $value['transpo_allowance'];
            $payroll->load_allowance  = $value['load_allowance'];
            $payroll->gross_pay  = $value['grosspay'];
            $payroll->total_taxable  = $value['total_taxable'];
            $payroll->witholding_tax  = $value['witholding_tax'];
            $payroll->sick_leave  = $value['sick_leave'];
            $payroll->vacation_leave  = $value['vacation_leave'];
            $payroll->wfhome  = $value['work_from_home'];
            $payroll->offbusiness  = $value['official_business'];
            $payroll->sick_leave_nopay  = $value['sick_leave_no_pay'];
            $payroll->vacation_leave_nopay  = $value['vacation_leave_no_pay'];
            $payroll->sss_regee  = $value['sss_reg_ee_jan15'];
            $payroll->sss_mpfee = $value['sss_mpf_ee_jan15'];
            $payroll->phic_ee  = $value['phic_ee_jan15'];
            $payroll->hdmf_ee  = $value['hmdf_ee_jan15'];
            $payroll->hdmf_sal_loan  = $value['hdmf_salary_loan'];
            $payroll->hdmf_cal_loan  = $value['hdmf_calamity_loan'];
            $payroll->sss_sal_loan  = $value['sss_salary_loan'];
            $payroll->sss_cal_loan  = $value['sss_calamity_loan'];
            $payroll->sal_ded_tax  = $value['salary_deduction_taxable'];
            $payroll->sal_ded_nontax  = $value['salary_deduction_non_taxable'];
            $payroll->sal_loan  = $value['salary_loan'];
            $payroll->com_loan  = $value['company_loan'];
            $payroll->omhas  = $value['omhas_advances_from_mac'];
            $payroll->coop_cbu  = $value['coop_cbu'];
            $payroll->coop_reg_loan  = $value['coop_regular_loan'];
            $payroll->coop_messco  = $value['coop_mescco'];
            $payroll->uploan  = $value['uploan'];
            $payroll->others  = $value['others'];
            $payroll->total_deduction  = $value['total_deduction'];
            $payroll->netpay  = $value['netpay'];
            $payroll->sss_reg_er  = $value['sss_reg_er_jan15'];
            $payroll->sss_mpf_er  = $value['sss_mpf_er_jan15'];
            $payroll->sss_ec  = $value['sss_ecjan15'];
            $payroll->phic_er  = $value['phic_erjan15'];
            $payroll->hdmf_er  = $value['hdmf_erjan15'];
            $payroll->payroll_status  = "N";
            $payroll->tin_no  = $value['tin_no.'];
            $payroll->phil_no = $value['philhealth_no.'];
            $payroll->pagibig_no  = $value['pagibig_no.'];
            $payroll->sss_no  = $value['sss_no.'];
            $payroll->save();
            }
        }
        }
    
    
     Alert::success('Successfully Import')->persistent('Dismiss');
     return back();
    }

    public function uploadpayreg(Request $request)
    {
        return view('upload_pay_reg');
    }

    public function postuploadpayreg(Request $request)
    {
      $data =  Excel::import(new PayRegImport,request()->file('pay-reg'));
    //   dd($data);
    //   dd($data[0]);

       return back();
    }
   

    public function generatedAttendances(Request $request)
    {
        $generated_timekeepings = [];
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);

        // $companies = Company::whereHas('employee_has_company')
        //     ->whereIn('id', $allowed_companies)
        //     ->get();

        $companies = Company::whereIn('id', $allowed_companies)->get();
        
        $company = isset($request->company) ? $request->company : "";

        $from_date = $request->from;
        $to_date = $request->to;

        $schedules = [];
        $attendances = [];

        if ($from_date != null) {
            $generated_timekeepings = AttendanceDetailedReport::with(['employee.approved_obs', 'employee.approved_leaves_with_pay', 'employee.approved_leaves'])
                ->where('company_id', $request->company)
                ->whereBetween('log_date', [$from_date, $to_date])
                // ->where('employee_no', 'A285018')
                ->get();
                // foreach ($generated_timekeepings as $timekeeping) {
                //     $employee = $timekeeping->employee;
                //     if ($employee) {
                //         $approved_obs = $employee->approved_obs;
                //         $approved_leaves_with_pay = $employee->approved_leaves_with_pay;
                //         $approved_leaves_without_pay = $employee->approved_leaves;
                //         $timekeeping->leaves = "";
                //         $timekeeping->OB = "";
            
                //         if ($approved_obs) {
                //             $approved_ob = $approved_obs->where('date_from', '<=', $timekeeping->log_date)
                //                                         ->where('date_to', '>=', $timekeeping->log_date)
                //                                         ->first();
            
                //             if ($approved_ob) {
                //                 $timekeeping->OB = "OB";
                //             }
                //         }

                //         if ($approved_leaves_with_pay) {
                //             $approved_leave_with_pay = $approved_leaves_with_pay->where('date_from', '<=', $timekeeping->log_date)
                //                                         ->where('date_to', '>=', $timekeeping->log_date)
                //                                         ->first();
                //             if ($approved_leave_with_pay) {
                //                 $timekeeping->LWP = $approved_leave_with_pay->leave->leave_type . " With Pay";
                //             }
                //         }
                //         elseif($approved_leaves_without_pay) {
                //             $approved_leave_without_pay = $approved_leaves_without_pay->where('date_from', '<=', $timekeeping->log_date)
                //             ->where('date_to', '>=', $timekeeping->log_date)
                //             ->first();
                //             if ($approved_leave_without_pay) {
                //                 $timekeeping->LWP = $approved_leave_without_pay->leave->leave_type . " Without Pay";
                //             }
                //         }
                //     }
                    
                // }
        }

        $schedules = ScheduleData::all();
        return view('payroll.attendance_detailed_report', [
            'header' => 'Timekeeping',
            'from_date' => $from_date,
            'to_date' => $to_date,
            'companies' => $companies,
            'company' => $company,
            'attendances' => $attendances,
            'schedules' => $schedules,
            'generated_timekeepings' => $generated_timekeepings
        ]);
    }  

    public function exportAttendance(Request $request)
    {
        return Excel::download(
            new AttendanceExport($request->company, $request->from, $request->to),
            'Attendance Detailed Report.xlsx'
        );
    }   

    public function generatePayslip(Request $request)
    {
        $payroll = Payregs::with('salary_adjustments_data','pay_allowances.allowance_type','pay_loan.loan_type','pay_instructions')->findOrfail($request->id);
        // $allowances = PayregAllowance::where('payreg_id',$request->id);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('payslips.generate_payslip',
        array(
            'payroll' => $payroll,
        ))->setPaper('a4', 'Portrait');

        return $pdf->stream();
    }

    public function generatePayslipEmployee(Request $request)
    {
        $payroll = Payregs::with('pay_allowances.allowance_type','pay_loan.loan_type','pay_instructions')->where('pay_period_from',$request->id)->where('employee_no',auth()->user()->employee->employee_code)->first();
        // $allowances = PayregAllowance::where('payreg_id',$request->id);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('payslips.generate_payslip',
        array(
            'payroll' => $payroll,
        ))->setPaper('a4', 'Portrait');

        return $pdf->stream();
    }

// ances.allowance_type','pay_loan.loan_type','pay_instructions')->findOrfail($request->id);
//         // $allowances = PayregAllowance::where('payreg_id',$request->id);
//         $pdf = App::make('dompdf.wrapper');
//         $pdf->loadView('payslips.generate_payslip',
//         array(
//             'payroll' => $payroll,
//         ))->setPaper('a4', 'Portrait');

//         return $pdf->stream();
//     }

    // public function generatePayslipEmployee(Request $request)
    // {
    //     $payroll = Payregs::with('pay_allowances.allowance_type','pay_loan.loan_type','pay_instructions')->where('pay_period_from',$request->id)->where('employee_no',auth()->user()->employee->employee_code)->first();
    //     // $allowances = PayregAllowance::where('payreg_id',$request->id);
    //     $pdf = App::make('dompdf.wrapper');
    //     $pdf->loadView('payslips.generate_payslip',
    //     array(
    //         'payroll' => $payroll,
    //     ))->setPaper('a4', 'Portrait');

    //     return $pdf->stream();
    // }

    public function deletePayRegInstruction($id)
    {
        // dd($id);
        $payroll_instruction = PayInstruction::findOrFail($id);
        $payroll_instruction->deleted_by = auth()->user()->id;
        $payroll_instruction->save();
        
        $payroll_instruction->delete();

        Alert::success('Successfully Deleted')->persistent('Dismiss');
        return back();
    }
}
