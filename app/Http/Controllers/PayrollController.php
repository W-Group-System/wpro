<?php

namespace App\Http\Controllers;
use App\PayRegs;
use App\Company;
use App\PayregAllowance;
use App\PayregLoan;
use App\PayregInstruction;
use App\Employee;
use App\SalaryAdjustment;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    //
    public function totalExpense_report()
    {
        return view('reports.totalExpense_report', array(
            'header' => 'reports',
        ));
    }
    public function government_reports(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $benefits = $request->benefits;
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $companies = Company::whereHas('employee_has_company')
        ->whereIn('id', $allowed_companies)
        ->get();
        $company = $request->company;
        $pay_registers = Payregs::with('employee')->selectRaw('
        employee_no,
        last_name,
        first_name,
        middle_name,
        SUM(sss_ec) as sss_ec,
        SUM(sss_employee_share) as sss_employee_share,
        SUM(sss_employer_share) as sss_employer_share,
        SUM(hdmf_employee_share) as hdmf_employee_share,
        SUM(hdmf_employer_share) as hdmf_employer_share,
        SUM(phic_employee_share) as phic_employee_share,
        SUM(phic_employer_share) as phic_employer_share,
        SUM(mpf_employee_share) as mpf_employee_share,
        SUM(mpf_employer_share) as mpf_employer_share,
        SUM(statutory_total) as statutory_total
    ')
    ->whereBetween('pay_period_from', [$request->from, $request->to])
    ->where('company_id', $request->company)
    ->groupBy('employee_no', 'last_name', 'first_name', 'middle_name')
    ->orderBy('last_name')
    ->get();
        return view('reports.government_reports', array(
            'header' => 'reports',
            'companies' => $companies,
            'company' => $company,
            'from' => $from,
            'to' => $to,
            'benefits' => $benefits,
            'pay_registers' => $pay_registers,
        ));
    }
    public function payroll_report(Request $request)
    {
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $companies = Company::whereHas('employee_has_company')
        ->whereIn('id', $allowed_companies)
        ->get();
        $employees = Employee::where('company_id',$request->company)->pluck('employee_code')->toArray();
        $company = $request->company;
        $pay_registers = Payregs::with('pay_allowances','pay_loan','pay_instructions','salary_adjustments_data','employee')->selectRaw('
        employee_no,
        SUM(days_rendered) as days_rendered,
        SUM(basic_pay) as basic_pay,
        SUM(other_allowances_basic_pay) as other_allowances_basic_pay,
        SUM(sh_nd) as sh_nd,
        SUM(sh_amount) as sh_amount,
        SUM(sh_ge) as sh_ge,
        SUM(sh_nd_ge) as sh_nd_ge,
        SUM(sh_nd_ge_amount) as sh_nd_ge_amount,
        SUM(sh_ot) as sh_ot,
        SUM(sh_ot_amount) as sh_ot_amount,
        SUM(sh_ot_ge) as sh_ot_ge,
        SUM(sh_ot_ge_amount) as sh_ot_ge_amount,
        SUM(sh_nd_amount) as sh_nd_amount,
        SUM(lh_nd) as lh_nd,
        SUM(lh_nd_amount) as lh_nd_amount,
        SUM(lh_nd_ge) as lh_nd_ge,
        SUM(lh_nd_ge_amount) as lh_nd_ge_amount,
        SUM(lh_ot) as lh_ot,
        SUM(lh_ot_amount) as lh_ot_amount,
        SUM(lh_ot_ge) as lh_ot_ge,
        SUM(lh_ot_ge_amount) as lh_ot_ge_amount,
        SUM(reg_nd) as reg_nd,
        SUM(reg_nd_amount) as reg_nd_amount,
        SUM(reg_ot) as reg_ot,
        SUM(reg_ot_amount) as reg_ot_amount,
        SUM(reg_ot_nd) as reg_ot_nd,
        SUM(reg_ot_nd_amount) as reg_ot_nd_amount,
        SUM(rst_nd) as rst_nd,
        SUM(rst_nd_amount) as rst_nd_amount,
        SUM(rst_nd_ge) as rst_nd_ge,
        SUM(rst_nd_ge_amount) as rst_nd_ge_amount,
        SUM(rst_ot) as rst_ot,
        SUM(rst_ot_amount) as rst_ot_amount,
        SUM(rst_ot_ge) as rst_ot_ge,
        SUM(rst_ot_ge_amount) as rst_ot_ge_amount,
        SUM(ot_total) as ot_total,
        SUM(pl) as pl,
        SUM(pl_amount) as pl_amount,
        SUM(sl) as sl,
        SUM(sl_amount) as sl_amount,
        SUM(vl) as vl,
        SUM(vl_amount) as vl_amount,
        SUM(leave_amount_total) as leave_amount_total,
        SUM(salary_adjustment) as salary_adjustment,
        SUM(taxable_benefits_total) as taxable_benefits_total,
        SUM(gross_taxable_income) as gross_taxable_income,
        SUM(days_absent) as days_absent,
        SUM(absent_amount) as absent_amount,
        SUM(tardiness_total) as tardiness_total,
        SUM(tardiness_amount) as tardiness_amount,
        SUM(undertime_total) as undertime_total,
        SUM(undertime_amount) as undertime_amount,
        SUM(sss_ec) as sss_ec,
        SUM(subliq) as subliq,
        SUM(sss_employee_share) as sss_employee_share,
        SUM(sss_employer_share) as sss_employer_share,
        SUM(hdmf_employee_share) as hdmf_employee_share,
        SUM(hdmf_employer_share) as hdmf_employer_share,
        SUM(phic_employee_share) as phic_employee_share,
        SUM(phic_employer_share) as phic_employer_share,
        SUM(mpf_employee_share) as mpf_employee_share,
        SUM(mpf_employer_share) as mpf_employer_share,
        SUM(statutory_total) as statutory_total,
        SUM(taxable_deductible_total) as taxable_deductible_total,
        SUM(net_taxable_income) as net_taxable_income,
        SUM(withholding_tax) as withholding_tax,
        SUM(deminimis) as deminimis,
        SUM(nontaxable_benefits_total) as nontaxable_benefits_total,
        SUM(nontaxable_deductible_benefits_total) as nontaxable_deductible_benefits_total,
        SUM(gross_pay) as gross_pay,
        SUM(deductions_total) as deductions_total,
        SUM(netpay) as netpay
    ')
    ->whereBetween('pay_period_from', [$request->from, $request->to])
    ->whereIn('employee_no', $employees)
    ->groupBy('employee_no')
    ->whereHas('employee')
    ->get();
    $pay_register_ids = Payregs::whereBetween('pay_period_from', [$request->from, $request->to])
    ->where('company_id', $request->company)
    ->pluck('id')
    ->toArray();
    $pay_register_ids_data = Payregs::whereBetween('pay_period_from', [$request->from, $request->to])
    ->where('company_id', $request->company)
    ->get();
    $allowances = PayregAllowance::with('allowance_type')->select('allowance_id')->whereIn('payreg_id',$pay_register_ids)->groupBy('allowance_id')->get();
    // $allowances_data = PayregAllowance::with('pay_reg_emp')->whereIn('payreg_id',$pay_register_ids)->get();
    $loans = PayregLoan::with('loan_type')->select('loan_type_id')->whereIn('payreg_id',$pay_register_ids)->groupBy('loan_type_id')->get();
    $instructions = PayregInstruction::select('instruction_name')->whereIn('payreg_id',$pay_register_ids)->groupBy('instruction_name')->get();
    $salary_adjustments = SalaryAdjustment::select('name')->whereIn('pay_reg_id',$pay_register_ids)->groupBy('name')->get();
    // dd($instructions);
        return view('reports.payroll_report', array(
            'header' => 'reports',
            'companies' => $companies,
            'company' => $company,
            'pay_registers' => $pay_registers,
            'allowances' => $allowances,
            'loans' => $loans,
            'instructions' => $instructions,
            // 'allowances_data' => $allowances_data,
            'pay_register_ids_data' => $pay_register_ids_data,
            'salary_adjustments' => $salary_adjustments,
        ));
    }
}
