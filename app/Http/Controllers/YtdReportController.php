<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Employee;
use App\Company;
use App\Payregs;
use App\PayregAllowance;
use App\PayregLoan;
use App\PayregInstruction;
use App\SalaryAdjustment;
use App\ThirteenthMonthPosting;

class YtdReportController extends Controller
{
    public function index(Request $request)
    {
        ini_set('memory_limit', '-1');

        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $allowed_locations = getUserAllowedLocations(auth()->user()->id);
        $allowed_projects  = getUserAllowedProjects(auth()->user()->id);

        $employees = Employee::select('id', 'user_id', 'employee_number', 'first_name', 'last_name', 'employee_code')
            ->whereIn('company_id', $allowed_companies)
            ->when($allowed_locations, function ($q) use ($allowed_locations) {
                $q->whereIn('location', $allowed_locations);
            })
            ->when($allowed_projects, function ($q) use ($allowed_projects) {
                $q->whereIn('project', $allowed_projects);
            })
            ->get();

        $from_date              = $request->from ?? date('Y');
        $date_range             = [];
        $emp_code               = $request->employee;
        $emp_data               = [];
        $employee_data          = $request->employee;
        $allowances             = [];
        $allowances_data        = [];
        $instructions           = [];
        $instructions_data      = [];
        $non_instructions_data  = [];
        $salary_adjustments     = [];
        $salary_adjustments_data = [];
        $thirteenth_month_postings = collect();
        $loans                  = [];
        $loans_data             = [];
        $emp                    = null;
        $non_instructions       = [];
        $company                = $request->company ?? '';

        if ($from_date) {
            if (empty($request->company) || $request->company === 'null') {
                $emp = Employee::with('company')->where('employee_code', $request->employee)->first();

                $emp_data = Payregs::with(['pay_allowances', 'pay_loan', 'pay_instructions', 'salary_adjustments_data'])
                    ->where('employee_no', $request->employee)
                    ->whereYear('cut_off_date', $from_date)
                    ->get();

            } elseif ($request->company) {
                $emp = Employee::with('company')
                    ->where('company_id', $request->company)
                    ->get();

                $emp_data = Payregs::with(['pay_allowances', 'pay_loan', 'pay_instructions', 'salary_adjustments_data'])
                    ->whereIn('employee_no', $emp->pluck('employee_code')->toArray())
                    ->whereYear('cut_off_date', $from_date)
                    ->paginate(50);
            }

            $payreg_ids = $emp_data->pluck('id')->toArray();

            $allowances = PayregAllowance::with('allowance_type')
                ->select('allowance_id')
                ->whereIn('payreg_id', $payreg_ids)
                ->groupBy('allowance_id')
                ->get();

            $loans = PayregLoan::with('loan_type')
                ->select('loan_type_id')
                ->whereIn('payreg_id', $payreg_ids)
                ->groupBy('loan_type_id')
                ->get();

            $instructions = PayregInstruction::select('instruction_name')
                ->where('amount', '<', 0)
                ->whereIn('payreg_id', $payreg_ids)
                ->groupBy('instruction_name')
                ->get();

            $allowances_data = PayregAllowance::with('allowance_type')
                ->whereIn('payreg_id', $payreg_ids)
                ->get();

            $loans_data = PayregLoan::with('loan_type')
                ->whereIn('payreg_id', $payreg_ids)
                ->get();

            $instructions_data = PayregInstruction::whereIn('payreg_id', $payreg_ids)->get();

            $salary_adjustments = SalaryAdjustment::select('name')
                ->whereIn('pay_reg_id', $payreg_ids)
                ->groupBy('name')
                ->orderBy('name', 'ASC')
                ->get();

            $salary_adjustments_data = SalaryAdjustment::whereIn('pay_reg_id', $payreg_ids)->get();

            $non_instructions = PayregInstruction::select('instruction_name')
                ->where('amount', '>', 0)
                ->whereIn('payreg_id', $payreg_ids)
                ->groupBy('instruction_name')
                ->get();

            $non_instructions_data = PayregInstruction::whereIn('payreg_id', $payreg_ids)->get();

            if (Schema::hasTable('thirteenth_month_postings')) {
                $employeeCodes = collect(method_exists($emp_data, 'items') ? $emp_data->items() : $emp_data)
                    ->pluck('employee_no')
                    ->unique()
                    ->values()
                    ->toArray();

                if ($request->employee && trim($request->employee) != '') {
                    $employeeCodes[] = $request->employee;
                }
                if ($request->company && $emp) {
                    $employeeCodes = array_merge($employeeCodes, $emp->pluck('employee_code')->toArray());
                }

                $employeeCodes = collect($employeeCodes)->filter()->unique()->values()->toArray();

                $thirteenth_month_postings = ThirteenthMonthPosting::whereIn('employee_no', $employeeCodes)
                    ->where('year', $from_date)
                    ->get()
                    ->groupBy('employee_no');
            }
        }

        $companies = Company::whereHas('employee_has_company')
            ->whereIn('id', $allowed_companies)
            ->get();

        return view('reports.ytd_report', [
            'header'                  => 'biometrics',
            'employees'               => $employees,
            'from_date'               => $from_date,
            'date_range'              => $date_range,
            'emp_code'                => $emp_code,
            'emp_data'                => $emp_data,
            'employee_data'           => $employee_data,
            'companies'               => $companies,
            'company'                 => $company,
            'allowances'              => $allowances,
            'allowances_data'         => $allowances_data,
            'loans'                   => $loans,
            'loans_data'              => $loans_data,
            'empD'                    => $emp,
            'instructions'            => $instructions,
            'non_instructions'        => $non_instructions,
            'non_instructions_data'   => $non_instructions_data,
            'instructions_data'       => $instructions_data,
            'salary_adjustments'      => $salary_adjustments,
            'salary_adjustments_data' => $salary_adjustments_data,
            'thirteenth_month_postings' => $thirteenth_month_postings,
        ]);
    }
}
