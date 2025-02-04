<?php

namespace App\Http\Controllers;

use App\AttendanceDetailedReport;
use Illuminate\Support\Facades\Auth;
use App\Imports\PayInstructionImport;
use App\Exports\PayInstructionExport;
use Excel;
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
use App\PayregLoan;
use App\PayregAllowance;
use App\PayregInstruction;
use App\ContributionSSS;
use App\EmployeeAllowance;
use App\SalaryAdjustment;
use App\Loan;
use Barryvdh\DomPDF\PDF;
use Dompdf\Options;
use Illuminate\Support\Facades\App;

class PayslipController extends Controller
{
    //
    public function view ()
    {
        $payslips = Payregs::where('employee_no',auth()->user()->employee->employee_code)->orderBy('id','desc')->get();
        return view('payslips.payslips',
        array(
            'header' => 'payslips',
            'payslips' => $payslips
            
        ));
    }
    public function ytd_report(Request $request)
    {
        ini_set('memory_limit', '-1');
    
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $allowed_locations = getUserAllowedLocations(auth()->user()->id);
        $allowed_projects = getUserAllowedProjects(auth()->user()->id);

        $employees = Employee::select('id','user_id','employee_number','first_name','last_name','employee_code')
                                ->whereIn('company_id', $allowed_companies)
                                ->when($allowed_locations,function($q) use($allowed_locations){
                                    $q->whereIn('location',$allowed_locations);
                                })
                                ->when($allowed_projects,function($q) use($allowed_projects){
                                    $q->whereIn('project',$allowed_projects);
                                })
                                ->get();
        $from_date = $request->from;
        $date_range =  [];
        $emp_code = $request->employee;
        $emp_data = [];
        $employee_data = $request->employee;
        $allowances = [];
        $allowances_data = [];
        $instructions = [];
        $instructions_data = [];
        $loans = [];
        $loans_data = [];
        $emp = null;
        

        $company = isset($request->company) ? $request->company : "";

        if ($from_date != null) {
            if ($request->company == null || $request->company === 'null') {
                $emp = Employee::with('company')->where('employee_code',$request->employee)->first();
                
                $emp_data = Payregs::with('pay_allowances','pay_loan')->where('employee_no',$request->employee)->whereYear('cut_off_date',$from_date)->get();

                // dd($emp_data);
                // $allowances = $emp_data->pay_allowances;
                
            }elseif ($request->company) {
                $emp = Employee::with('company')
                    ->where('company_id', $request->company)
                    ->paginate(50);
                
                $emp_data = Payregs::with('pay_allowances','pay_loan','pay_instructions')
                    ->whereIn('employee_no', $emp->pluck('employee_code')->toArray())
                    ->whereYear('cut_off_date', $from_date)
                    ->paginate(50);
                // dd($emp_data);

            }
            
            $allowances = PayregAllowance::with('allowance_type')->select('allowance_id')->whereIn('payreg_id',$emp_data->pluck('id')->toArray())->groupBy('allowance_id')->get();
            $loans = PayregLoan::with('loan_type')->select('loan_type_id')->whereIn('payreg_id',$emp_data->pluck('id')->toArray())->groupBy('loan_type_id')->get();
            $instructions = PayregInstruction::select('instruction_name')->where('amount','<',0)->whereIn('payreg_id',$emp_data->pluck('id')->toArray())->groupBy('instruction_name')->get();
            $allowances_data = PayregAllowance::with('allowance_type')->whereIn('payreg_id',$emp_data->pluck('id')->toArray())->get();
            $loans_data = PayregLoan::with('loan_type')->whereIn('payreg_id',$emp_data->pluck('id')->toArray())->get();
            $instructions_data = PayregInstruction::whereIn('payreg_id',$emp_data->pluck('id')->toArray())->get();
            // dd())
        }
        else

        {
            $from_date = date('Y');
        }
        $companies = Company::whereHas('employee_has_company')
                                ->whereIn('id',$allowed_companies)
                                ->get();
                                // dd($from_date)
        return view(
            'reports.ytd_report',
            array(
                'header' => 'biometrics',
                'employees' => $employees,
                'from_date' => $from_date,
                'date_range' => $date_range,
                'emp_code' => $emp_code,
                'emp_data' => $emp_data,
                'employee_data' => $employee_data,
                'companies' => $companies,
                'company' => $company,
                'allowances' => $allowances,
                'allowances_data' => $allowances_data,
                'loans' => $loans,
                'loans_data' => $loans_data,
                'empD' => $emp,
                'instructions' => $instructions,
                'instructions_data' => $instructions_data,
            )
        );
    }
    public function payroll_datas(Request $request)
    {
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $company = isset($request->company) ? $request->company : "";
        $cut_off = [];
        $from = $request->from;
        $to = $request->to;
        $cutoff = $request->cut_off;
        $names = [];
        $dates = [];
        $absents_data = [];
        $allowances_total = [];
        $salary_adjustments = [];
        $loans_all = [];
        $instructions = [];
        $shifts = [];
        
        $last_cut_off = [];
        $sss = ContributionSSS::orderBy('salary_to','asc')->get();
        if($request->company)
        {
            $dates = AttendanceDetailedReport::select(DB::raw('DAY(log_date) as log_date'))->groupBy('log_date')->where('cut_off_date', $cutoff)->where('company_id', $request->company)->get(); 
          
            $cut_off_pay_reg = Payregs::select('cut_off_date')->where('company_id',$request->company)->groupBy('cut_off_date')->pluck('cut_off_date')->toArray();
            $cut_off = AttendanceDetailedReport::select('company_id','cut_off_date')->groupBy('company_id','cut_off_date')->orderBy('cut_off_date','desc')->whereNotIn('cut_off_date',$cut_off_pay_reg)->where('company_id',$request->company)->get();
            // $names = AttendanceDetailedReport::with(['employee.salary','employee.loan','employee.allowances','employee.pay_instructions'])
            if($request->cut_off)
            {
                
            $last_cut_off = Payregs::with('pay_allowances')->where('cut_off_date','<',$cutoff)->orderBy('cut_off_date','desc')->where('company_id',$request->company)->get();
            $absents_data = AttendanceDetailedReport ::whereColumn('abs', '>', 'lv_w_pay')->where('company_id',$request->company)->where('cut_off_date', $cutoff)->get();
            $shifts = AttendanceDetailedReport ::where('shift', 'NOT LIKE', '%REST%')->where('company_id',$request->company)->where('cut_off_date', $cutoff)->get();
            $names = AttendanceDetailedReport::with([
                'employee' => function ($query) {
                        $query->where('status','Active');
                },
                'employee.salary',
                'employee.loan' => function ($query) {
                    $query->where('status','Active');
                },
                'employee.salary_adjustments' => function ($query) {
                    $query->where('cut_off_date',null);
                },
                'employee.allowances'=> function ($query) {
                    $query->where('status','Active');
            },
                'employee.pay_instructions'=> function ($query) use ($cutoff) {
                    $query->where('start_date', '>=', $cutoff)
                          ->where('end_date', '<=', $cutoff);
                },
            ])
            ->whereHas('employee', function ($query) {
                $query->where('status', 'Active');
            })
            ->select('company_id', 'employee_no', 'name', 'cut_off_date',
            DB::raw('COUNT(CASE WHEN shift NOT LIKE "%REST%" THEN 1 END) as shift_count'),
            DB::raw('SUM(abs) as total_abs'),
            DB::raw('SUM(lv_w_pay) as total_lv_w_pay'),
            DB::raw('SUM(lh_nd) as total_lh_nd'),
            DB::raw('SUM(lh_nd_over_eight) as total_lh_nd_over_eight'),
            DB::raw('SUM(lh_ot) as total_lh_ot'),
            DB::raw('SUM(lh_ot_over_eight) as total_lh_ot_over_eight'),
            DB::raw('SUM(sh_nd) as total_sh_nd'),
            DB::raw('SUM(sh_nd_over_eight) as total_sh_nd_over_eight'),
            DB::raw('SUM(sh_ot) as total_sh_ot'),
            DB::raw('SUM(sh_ot_over_eight) as total_sh_ot_over_eight'),
            DB::raw('SUM(reg_nd) as total_reg_nd'),
            DB::raw('SUM(reg_ot) as total_reg_ot'),
            DB::raw('SUM(reg_ot_nd) as total_reg_ot_nd'),
            DB::raw('SUM(rst_nd) as total_rst_nd'),
            DB::raw('SUM(rst_nd_over_eight) as total_rst_nd_over_eight'),
            DB::raw('SUM(rst_ot) as total_rst_ot'),
            DB::raw('SUM(rst_ot_over_eight) as total_rst_ot_over_eight'),
            DB::raw('SUM(abs) as total_abs'),
            DB::raw('SUM(late_min) as total_late_min'),
            DB::raw('SUM(undertime_min) as total_undertime_min')
            )->where('company_id', $request->company)
            ->where('cut_off_date', $cutoff)
            ->groupBy('company_id', 'employee_no', 'name','cut_off_date')
            // ->where('employee_no','A3170823')
            // ->whereDoesntHave('employee.salary')
            ->get(); 
            // dd($names);
            if(!empty($names))
            {
                if($cutoff != null)
                {
                    // dd($cutoff);
                $from = (AttendanceDetailedReport::where('company_id',$request->company)->where('cut_off_date',$cutoff)->orderBy('log_date','asc')->first())->log_date;
                $to = (AttendanceDetailedReport::where('company_id',$request->company)->where('cut_off_date',$cutoff)->orderBy('log_date','desc')->first())->log_date;
                $names_all = $names->pluck('employee.user_id')->toArray();
                $employee_ids = $names->pluck('employee.id')->toArray();
                $employee_codes = $names->pluck('employee.employee_code')->toArray();
                $allowances_total = EmployeeAllowance::with('allowance')->whereIn('user_id',$names_all)->select('allowance_id')->groupBy('allowance_id')->get();
                $salary_adjustments = SalaryAdjustment::whereIn('employee_id',$employee_ids)->where('cut_off_date',null)->select('name')->groupBy('name')->get();
                $loans_all = Loan::with('loan_type')->whereIn('employee_id',$employee_ids)->select('loan_type_id')->groupBy('loan_type_id')->get();
                $instructions = PayInstruction::whereIn('site_id',$employee_codes)
                ->where('start_date', '>=', $cutoff)
                              ->where('end_date', '<=', $cutoff)
                              ->select('benefit_name')->groupBy('benefit_name')->get();
                                        
                }
            }
          
                
        }
        }
       $companies = Company::whereHas('employee_has_company')
        ->whereIn('id', $allowed_companies)
        ->get();

      
        return view('payroll.pay_reg',
        array(
            'header' => 'Payroll',
            'cut_off' => $cut_off,
            'from' => $from,
            'to' => $to,
            'companies' => $companies,
            'company' => $company,
            'cutoff' => $cutoff,
            'names' => $names,
            'sss' => $sss,
            'dates' => $dates,
            'absents_data' => $absents_data,
            'allowances_total' => $allowances_total,
            'loans_all' => $loans_all,
            'instructions' => $instructions,
            'shifts' => $shifts,
            'last_cut_off' => $last_cut_off,
            'salary_adjustments' => $salary_adjustments,
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
        // dd($request->get_bbb);
        // dd($request->get_bbb);
        foreach($request->employee_no as $key => $employee_code)
        {
            $employee_data = Employee::where('employee_code',$employee_code)->first();

           
            $pay_register = new Payregs;
            $pay_register->employee_no = $employee_code;
            $pay_register->last_name = $request->last_name[$key];
            $pay_register->first_name = $request->first_name[$key];
            $pay_register->middle_name = $request->middle_name[$key];
            $pay_register->department = $request->department_name[$key];
            $pay_register->cost_center = $request->cost_center[$key];
            $pay_register->account_number = $request->bank_account_number[$key];
            $pay_register->pay_rate = $request->pay_rate[$key];
            $pay_register->tax_status = $request->tax_status[$key];
            $pay_register->days_rendered = $request->days_rendered[$key];
            $pay_register->basic_pay = $request->basic_pay[$key];
            $pay_register->lh_nd = $request->name_total_lh_nd[$key];
            $pay_register->lh_nd_amount = $request->total_lh_nd_amount[$key];
            $pay_register->lh_nd_ge = $request->name_total_lh_nd_over_eight[$key];
            $pay_register->lh_nd_ge_amount = $request->total_lh_nd_over_eight[$key];
            $pay_register->lh_ot = $request->name_total_lh_ot[$key];
            $pay_register->lh_ot_amount = $request->total_lh_ot[$key];
            $pay_register->lh_ot_ge = $request->name_total_lh_ot_over_eight[$key];
            $pay_register->lh_ot_ge_amount = $request->total_lh_ot_over_eight[$key];
            $pay_register->sh_nd = $request->name_total_sh_nd[$key];
            $pay_register->sh_nd_amount = $request->total_sh_nd_amount[$key];
            $pay_register->sh_nd_ge = $request->name_total_sh_nd_over_eight[$key];
            $pay_register->sh_nd_ge_amount = $request->total_sh_nd_over_eight[$key];
            $pay_register->sh_ot = $request->name_total_sh_ot[$key];
            $pay_register->sh_ot_amount = $request->total_sh_ot[$key];
            $pay_register->sh_ot_ge = $request->name_total_sh_ot_over_eight[$key];
            $pay_register->sh_ot_ge_amount = $request->total_sh_ot_over_eight[$key];
            $pay_register->reg_nd = $request->name_total_reg_nd[$key];
            $pay_register->reg_nd_amount = $request->total_reg_nd[$key];
            $pay_register->reg_ot = $request->name_total_reg_ot[$key];
            $pay_register->reg_ot_amount = $request->total_reg_ot[$key];
            $pay_register->reg_ot_nd = $request->name_total_reg_ot_nd[$key];
            $pay_register->reg_ot_nd_amount = $request->total_reg_ot_nd[$key];
            $pay_register->rst_nd = $request->name_total_rst_nd[$key];
            $pay_register->rst_nd_amount = $request->total_rst_nd[$key];
            $pay_register->rst_nd_ge = $request->name_total_rst_nd_over_eight[$key];
            $pay_register->rst_nd_ge_amount = $request->total_rst_nd_over_eight[$key];
            $pay_register->rst_ot = $request->name_total_rst_ot[$key];
            $pay_register->rst_ot_amount = $request->total_rst_ot[$key];
            $pay_register->rst_ot_ge = $request->name_total_rst_ot_over_eight[$key];
            $pay_register->rst_ot_ge_amount = $request->total_rst_ot_over_eight[$key];
            $pay_register->ot_total = $request->total_ot_pay[$key];
            $pay_register->salary_adjustment = $request->salary_adjustment[$key];
            $pay_register->taxable_benefits_total = $request->total_taxable_benefits[$key];
            $pay_register->gross_taxable_income = $request->gross_taxable_income[$key];
            $pay_register->days_absent = $request->total_abs_count[$key];
            $pay_register->absent_amount = $request->total_abs[$key];
            $pay_register->tardiness_total = $request->name_total_late_min[$key];
            $pay_register->tardiness_amount = $request->total_late_min[$key];
            $pay_register->undertime_total = $request->name_total_undertime_min[$key];
            $pay_register->undertime_amount = $request->total_undertime_min[$key];
            $pay_register->sss_ec = $request->sss_ecc[$key];
            $pay_register->sss_employee_share = $request->sss_ee[$key];
            $pay_register->sss_employer_share = $request->sss_er[$key];
            $pay_register->hdmf_employee_share = $request->hdmf_ee[$key];
            $pay_register->hdmf_employer_share = $request->hdmf_er[$key];
            $pay_register->phic_employee_share = $request->philhealth_ee[$key];
            $pay_register->phic_employer_share = $request->philhealth_er[$key];
            $pay_register->mpf_employee_share = $request->wisp_ee[$key];
            $pay_register->mpf_employer_share = $request->wisp_er[$key];
            $pay_register->statutory_total = $request->statutory[$key];
            $pay_register->taxable_deductible_total = $request->taxable_deductable_total[$key];
            $pay_register->net_taxable_income = $request->net_taxable_income[$key];
            $pay_register->withholding_tax = $request->tax[$key];
            $pay_register->deminimis = $request->de_minimis[$key];
            $pay_register->other_allowances_basic_pay = $request->other_allowances_basic_pay[$key];
            $pay_register->subliq = $request->subliq[$key];
            $pay_register->nontaxable_benefits_total = $request->nontaxable_benefits_total[$key];
            $pay_register->nontaxable_deductible_benefits_total = $request->nontaxable_deductible_benefits_total[$key];
            $pay_register->gross_pay = $request->gross_pay[$key];
            $pay_register->deductions_total = $request->deductions_total[$key];
            $pay_register->netpay = $request->netpay[$key];
            $pay_register->pay_period_from = $request->from;
            $pay_register->pay_period_to = $request->to;
            $pay_register->posting_date = $request->posting_date;
            $pay_register->posted_by = auth()->user()->id;
            $pay_register->cut_off_date = $request->cut_off;
            $pay_register->company_id = $request->company;
            $pay_register->save();

            $salary_adjustements = SalaryAdjustment::where('employee_id',$employee_data->id)->where('pay_reg_id',null)->get();
            foreach($salary_adjustements as $sad)
            {
                $sad->cut_off_date = $request->to;
                $sad->pay_reg_id = $pay_register->id;
                $sad->save();
            }
            
          
            if($request->get_every_cut_off)
            {
                if(array_key_exists($key,$request->get_every_cut_off))
                {
                    if(($request->get_every_cut_off[$key]) != "[]")
                    {
                     
                        $get_every_cut_off = json_decode($request->get_every_cut_off[$key]);
                        
                        foreach($get_every_cut_off as $bbb)
                        {
                            $ins = new PayregAllowance;
                            $ins->allowance_id = $bbb->allowance_id;
                            $ins->payreg_id = $pay_register->id;
                            $ins->amount = $bbb->allowance_amount;
                            $ins->user_id = $bbb->user_id;
                            $ins->remarks = $bbb->schedule;
                            $ins->save();
                        }
                    }
                }
            }
            if($request->get_bbb)
            {
                if(array_key_exists($key,$request->get_bbb))
                {
                    if(($request->get_bbb[$key]) != "[]")
                    {
                        $get_bbb = json_decode($request->get_bbb[$key]);
                        foreach($get_bbb as $bbb)
                        {
                            $ins = new PayregAllowance;
                            $ins->allowance_id = $bbb->allowance_id;
                            $ins->payreg_id = $pay_register->id;
                            $ins->amount = $bbb->allowance_amount;
                            $ins->user_id = $bbb->user_id;
                            $ins->remarks = $bbb->schedule;
                            $ins->save();
                        }
                    }
                }
            }
            if($request->get_every_cut_off_payroll_instructions)
            {
                if(array_key_exists($key,$request->get_every_cut_off_payroll_instructions))
                {
                    if(($request->get_every_cut_off_payroll_instructions[$key]) != "[]")
                    {
                        $get_every_cut_off_payroll_instructions = json_decode($request->get_every_cut_off_payroll_instructions[$key]);
                        foreach($get_every_cut_off_payroll_instructions as $instruction)
                        {
                            $ins = new PayregInstruction;
                            $ins->instruction_name = $instruction->benefit_name;
                            $ins->payreg_id = $pay_register->id;
                            $ins->amount = $instruction->amount;
                            $ins->employee_code = $instruction->site_id;
                            $ins->remarks = $instruction->frequency;
                            $ins->save();
                        }
                    }
                }
            }
            if($request->get_other)
            {
                if(array_key_exists($key,$request->get_other))
                {
                    if(($request->get_other[$key]) !="[]")
                    {
                        $get_other = json_decode($request->get_other[$key]);
                        foreach($get_other as $get_ot)
                        {
                            $ins = new PayregInstruction;
                            $ins->instruction_name = $get_ot->benefit_name;
                            $ins->payreg_id = $pay_register->id;
                            $ins->amount = $get_ot->amount;
                            $loa->employee_code = $get_ot->site_id;
                            $loa->remarks = $get_ot->frequency;
                            $loa->save();
                        }
                    }
                }
            }
            if($request->get_every_cut_off_loan)
            {
                if(array_key_exists($key,$request->get_every_cut_off_loan))
                {
                    if(($request->get_every_cut_off_loan[$key]) != "[]")
                    {
                        $get_every_cut_off_loan = json_decode($request->get_every_cut_off_loan[$key]);
                        // dd($get_every_cut_off_loan,$pay_register->id);
                        foreach($get_every_cut_off_loan as $loan)
                        {
                            $loa = new PayregLoan;
                            $loa->loan_type_id = $loan->loan_type_id;
                            $loa->payreg_id = $pay_register->id;
                            $loa->loan_id = $loan->id;
                            $loa->amount = $loan->monthly_ammort_amt;
                            $loa->employee_id = $loan->employee_id;
                            $loa->remarks = $loan->schedule;
                            $loa->save();
                        }
                    }
                }
            }
            if($request->get_loans)
            {
                if(array_key_exists($key,$request->get_loans))
                {
                    if(($request->get_loans[$key]) != "[]")
                    {
                        $get_loans = json_decode($request->get_loans[$key]);
                        foreach($get_loans as $loan)
                        {
                            $loa = new PayregLoan;
                            $loa->loan_type_id = $loan->loan_type_id;
                            $loa->payreg_id = $pay_register->id;
                            $loa->loan_id = $loan->id;
                            $loa->amount = $loan->monthly_ammort_amt;
                            $loa->employee_id = $loan->employee_id;
                            $loa->remarks = $loan->schedule;
                            $loa->save();
                        }
                    }
                }
            }

        }
        Alert::success('Successfully Generated')->persistent('Dismiss');
        return redirect('/pay-reg');
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
        $names = PayInstruction::all();
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
    function monthly_benefit(Request $request)
    {
        $company = $request->company;
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $companies = Company::whereHas('employee_has_company')
        ->whereIn('id', $allowed_companies)
        ->get();
        $year = date('Y');
        if($request->year)
        {
            $year = date('Y',strtotime($request->year."-01-01"));
        }
        
        if($company == 10)
        {
            $employees = Employee::select('employee_number','bank_account_number','user_id','first_name','last_name','middle_name','location','schedule_id','employee_code','company_id','work_description','original_date_hired','department_id')
            ->with(['get_payreg' => function ($query) use ($year) {
                $query->select('basic_pay', 'deminimis', 'other_allowances_basic_pay', 'subliq', 'cut_off_date')
                ->whereBetween('cut_off_date', [
                    date('Y-12-01', strtotime("$year-01-01 -1 month")),
                    date('Y-12-t', strtotime("$year-12-01"))
                ]);
            }])
            ->whereHas('salary')
            ->where('original_date_hired','<=',date('Y-11-30'))
            ->with('company','benefits','department')
            ->where('company_id', $request->company)
            ->where('classification','!=',8)
            ->where('status','Active')
            ->get();
        }
        else
        {
            $employees = Employee::select('employee_number','bank_account_number','user_id','first_name','last_name','middle_name','location','schedule_id','employee_code','company_id','work_description','original_date_hired','department_id')
            ->with(['get_payreg' => function ($query) use ($year) {
                $query->select('basic_pay', 'deminimis', 'other_allowances_basic_pay', 'subliq', 'cut_off_date')
                    ->whereBetween('cut_off_date', [
                        date('Y-01-01', strtotime("$year-01-01")),
                        date('Y-12-t', strtotime("$year-12-01"))
                    ]);
            }])
            ->whereHas('salary')
            ->with('company','benefits','department','get_payreg')
            ->where('original_date_hired','<=',date('Y-11-30'))
            ->where('company_id', $request->company)
            ->where('classification','!=',8)
            ->where('status','Active')
            // ->where('employee_code','A3140520')
            ->get();

        }
        $benefitIds = $employees->pluck('get_payreg')->flatten()->pluck('id')->toArray();
        // dd($benefitIds);
        $salary_adjustments = SalaryAdjustment::whereIn('pay_reg_id',$benefitIds)->where(function($query) {
            $query->where('name', 'like', '%Salary%')
                ->orWhere('name', 'like', '%Leave%')
                ->orWhere('name', 'like', '%Basic%')
                ->orWhere('name', 'like', '%tardiness%')
                ->orWhere('name', 'like', '%Undertime%')
                ->orWhere('name', 'like', '%Absent%')
                ->orWhere('name', 'like', '%Late%');
        })->get();

        $pay_instructions = PayregInstruction::whereIn('payreg_id',$benefitIds)->where(function($query) {
            $query->where('instruction_name', 'like', '%Minimis%')
                ->orWhere('instruction_name', 'like', '%Other Allowance%')
                ->orWhere('instruction_name', 'like', '%Subliq%');
        })->get();
       
                                // ->where('employee_code','A3121418')
                              
        $dates = [];
        return view('reports.month',
        array(
            'header' => 'Month-Benefit',
            'dates' => $dates,
            'year' => $year,
            'companies' => $companies,
            'company' => $company,
            'employees' => $employees,
            'salary_adjustments' => $salary_adjustments,
            'pay_instructions' => $pay_instructions,
            
        ));
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

        $companies = Company::whereHas('employee_has_company')
            ->whereIn('id', $allowed_companies)
            ->get();

        $company = isset($request->company) ? $request->company : "";

        $from_date = $request->from;
        $to_date = $request->to;

        $schedules = [];
        $attendances = [];

        if ($from_date != null) {
            $generated_timekeepings = AttendanceDetailedReport::with(['employee.approved_obs', 'employee.approved_leaves_with_pay', 'employee.approved_leaves'])
                ->where('company_id', $request->company)
                ->whereBetween('log_date', [$from_date, $to_date])
                ->get();
                foreach ($generated_timekeepings as $timekeeping) {
                    $employee = $timekeeping->employee;
                    if ($employee) {
                        $approved_obs = $employee->approved_obs;
                        $approved_leaves_with_pay = $employee->approved_leaves_with_pay;
                        $approved_leaves_without_pay = $employee->approved_leaves;
                        $timekeeping->leaves = "";
                        $timekeeping->OB = "";
            
                        if ($approved_obs) {
                            $approved_ob = $approved_obs->where('date_from', '<=', $timekeeping->log_date)
                                                        ->where('date_to', '>=', $timekeeping->log_date)
                                                        ->first();
            
                            if ($approved_ob) {
                                $timekeeping->OB = "OB";
                            }
                        }

                        if ($approved_leaves_with_pay) {
                            $approved_leave_with_pay = $approved_leaves_with_pay->where('date_from', '<=', $timekeeping->log_date)
                                                        ->where('date_to', '>=', $timekeeping->log_date)
                                                        ->first();
                            if ($approved_leave_with_pay) {
                                $timekeeping->LWP = $approved_leave_with_pay->leave->leave_type . " With Pay";
                            }
                        }
                        elseif($approved_leaves_without_pay) {
                            $approved_leave_without_pay = $approved_leaves_without_pay->where('date_from', '<=', $timekeeping->log_date)
                            ->where('date_to', '>=', $timekeeping->log_date)
                            ->first();
                            if ($approved_leave_without_pay) {
                                $timekeeping->LWP = $approved_leave_without_pay->leave->leave_type . " Without Pay";
                            }
                        }
                    }
                    
                }
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
