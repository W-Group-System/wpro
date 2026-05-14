<?php

namespace App\Http\Controllers;

use App\Company;
use App\Employee;
use App\PayInstruction;
use App\PayregInstruction;
use App\SalaryAdjustment;
use App\ThirteenthMonthPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RealRashid\SweetAlert\Facades\Alert;

class ThirteenthMonthController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->company;
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $companies = Company::whereHas('employee_has_company')
            ->whereIn('id', $allowed_companies)
            ->get();

        $year = date('Y');
        if ($request->year) {
            $year = date('Y', strtotime($request->year . '-01-01'));
        }

        $posted_halves = $company && Schema::hasTable('thirteenth_month_postings')
            ? ThirteenthMonthPosting::where('company_id', $company)
                ->where('year', $year)
                ->groupBy('half')
                ->pluck('half')
                ->toArray()
            : [];
        $available_halves = collect([
            '1st' => '1st Half',
            '2nd' => '2nd Half',
        ])->reject(function($label, $key) use ($posted_halves) {
            return in_array($key, $posted_halves);
        })->toArray();
        $defaultHalf = count($available_halves) > 0 ? key($available_halves) : '1st';
        $half = $request->half ?: $defaultHalf;
        if ($company && !array_key_exists($half, $available_halves) && count($available_halves) > 0) {
            $half = key($available_halves);
        }

        $employees = $company ? $this->thirteenthMonthEmployees($company, $year, $half) : collect();
        $benefitIds = $half == '1st' ? [] : $employees->pluck('get_payreg')->flatten()->pluck('id')->toArray();

        $salary_adjustments = SalaryAdjustment::whereIn('pay_reg_id', $benefitIds)->where(function($query) {
            $query->where('name', 'like', '%Salary%')
                ->orWhere('name', 'like', '%Leave%')
                ->orWhere('name', 'like', '%Basic%')
                ->orWhere('name', 'like', '%tardiness%')
                ->orWhere('name', 'like', '%Undertime%')
                ->orWhere('name', 'like', '%Absent%')
                ->orWhere('name', 'like', '%Late%');
        })->get();

        $pay_instructions = PayregInstruction::whereIn('payreg_id', $benefitIds)->where(function($query) {
            $query->where('instruction_name', 'like', '%Minimis%')
                ->orWhere('instruction_name', 'like', '%Other Allowance%')
                ->orWhere('instruction_name', 'like', '%Subliq%');
        })->get();
        $thirteenth_month_rows = $this->buildThirteenthMonthRows($employees, $salary_adjustments, $pay_instructions, $year, $company, $half);

        return view('reports.month', [
            'header' => 'Month-Benefit',
            'dates' => [],
            'year' => $year,
            'companies' => $companies,
            'company' => $company,
            'employees' => $employees,
            'salary_adjustments' => $salary_adjustments,
            'pay_instructions' => $pay_instructions,
            'half' => $half,
            'available_halves' => $available_halves,
            'posted_halves' => $posted_halves,
            'thirteenth_month_rows' => $thirteenth_month_rows,
        ]);
    }

    public function post(Request $request)
    {
        $request->validate([
            'company' => 'required',
            'year' => 'required',
            'half' => 'required|in:1st,2nd',
        ]);

        $previewRequest = new Request([
            'company' => $request->company,
            'year' => $request->year,
            'half' => $request->half,
        ]);

        $year = date('Y', strtotime($request->year . '-01-01'));
        if (!Schema::hasTable('thirteenth_month_postings')) {
            Alert::error('13th month posting table is not yet created. Please run the 13th month migration first.')->persistent('Dismiss');
            return back();
        }

        $alreadyPosted = ThirteenthMonthPosting::where('company_id', $request->company)
            ->where('year', $year)
            ->where('half', $request->half)
            ->exists();

        if ($alreadyPosted) {
            Alert::warning('13th month ' . $request->half . ' half is already posted for ' . $year . '.')->persistent('Dismiss');
            return redirect('/13th-register?' . http_build_query([
                'company' => $request->company,
                'year' => $year,
            ]));
        }

        $employees = $this->thirteenthMonthEmployees($request->company, $year, $request->half);
        $benefitIds = $request->half == '1st' ? [] : $employees->pluck('get_payreg')->flatten()->pluck('id')->toArray();
        $salaryAdjustments = SalaryAdjustment::whereIn('pay_reg_id', $benefitIds)->where(function($query) {
            $query->where('name', 'like', '%Salary%')
                ->orWhere('name', 'like', '%Leave%')
                ->orWhere('name', 'like', '%Basic%')
                ->orWhere('name', 'like', '%tardiness%')
                ->orWhere('name', 'like', '%Undertime%')
                ->orWhere('name', 'like', '%Absent%')
                ->orWhere('name', 'like', '%Late%');
        })->get();
        $payInstructions = PayregInstruction::whereIn('payreg_id', $benefitIds)->where(function($query) {
            $query->where('instruction_name', 'like', '%Minimis%')
                ->orWhere('instruction_name', 'like', '%Other Allowance%')
                ->orWhere('instruction_name', 'like', '%Subliq%');
        })->get();
        $rows = $this->buildThirteenthMonthRows($employees, $salaryAdjustments, $payInstructions, $year, $request->company, $request->half);
        $posted = 0;

        foreach ($rows as $row) {
            if ($row['release_amount'] == 0) {
                continue;
            }

            ThirteenthMonthPosting::updateOrCreate(
                [
                    'employee_no' => $row['employee_code'],
                    'half' => $request->half,
                    'year' => $year,
                ],
                [
                    'company_id' => $request->company,
                    'employee_name' => $row['name'],
                    'department' => $row['department'],
                    'account_number' => $row['account_number'],
                    'monthly_salary' => $row['monthly_salary'],
                    'annual_thirteenth_month' => $row['annual_thirteenth'],
                    'first_half_released' => $row['first_released'],
                    'release_amount' => $row['release_amount'],
                    'posted_by' => auth()->user()->id,
                ]
            );
            $posted++;
        }

        Alert::success('Successfully posted ' . $posted . ' 13th month payslip(s).')->persistent('Dismiss');

        return redirect('/month-benefit-generated?' . http_build_query($previewRequest->query->all()));
    }

    public function generated(Request $request)
    {
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $companies = Company::whereHas('employee_has_company')
            ->whereIn('id', $allowed_companies)
            ->get();
        $company = $request->company;
        $year = $request->year;
        $half = $request->half;
        $postings = collect();

        if (Schema::hasTable('thirteenth_month_postings')) {
            $postings = ThirteenthMonthPosting::with('company')
                ->whereIn('company_id', $allowed_companies)
                ->when($company, function($query) use ($company) {
                    $query->where('company_id', $company);
                })
                ->when($year, function($query) use ($year) {
                    $query->where('year', $year);
                })
                ->when($half, function($query) use ($half) {
                    $query->where('half', $half);
                })
                ->orderBy('year', 'desc')
                ->orderBy('half', 'desc')
                ->orderBy('employee_name', 'asc')
                ->paginate(100);
        }

        return view('reports.month_generated', [
            'header' => 'Generated 13th Month',
            'companies' => $companies,
            'company' => $company,
            'year' => $year,
            'half' => $half,
            'postings' => $postings,
            'total_postings' => method_exists($postings, 'total') ? $postings->total() : $postings->count(),
            'total_amount' => method_exists($postings, 'getCollection') ? $postings->getCollection()->sum('release_amount') : $postings->sum('release_amount'),
        ]);
    }

    public function payslip(Request $request)
    {
        $posting = ThirteenthMonthPosting::with('employee', 'company')->findOrFail($request->id);

        if ($posting->employee_no != auth()->user()->employee->employee_code && checkUserPrivilege('payroll_view', auth()->user()->id) != 'yes') {
            abort(403);
        }

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('payslips.generate_13th_month_payslip', [
            'posting' => $posting,
        ])->setPaper('a4', 'Portrait');

        return $pdf->stream();
    }

    private function thirteenthMonthEmployees($company, $year, $half)
    {
        $dateFrom = $company == 10
            ? date('Y-12-01', strtotime("$year-01-01 -1 month"))
            : date('Y-01-01', strtotime("$year-01-01"));
        $dateTo = date('Y-12-t', strtotime("$year-12-01"));

        return Employee::select('employee_number','bank_account_number','user_id','first_name','last_name','middle_name','location','schedule_id','employee_code','company_id','work_description','original_date_hired','department_id')
            ->whereHas('salary')
            ->with('company:id,company_code','department:id,name','salary:user_id,basic_salary,de_minimis,other_allowance,subliq')
            ->when($half != '1st', function($query) use ($dateFrom, $dateTo) {
                $query->with(['get_payreg' => function($query) use ($dateFrom, $dateTo) {
                    $query->select('id', 'employee_no', 'basic_pay', 'deminimis', 'other_allowances_basic_pay', 'subliq', 'absent_amount', 'tardiness_amount', 'undertime_amount', 'cut_off_date')
                        ->whereBetween('cut_off_date', [$dateFrom, $dateTo])
                        ->with('pay_instructions');
                }]);
            })
            ->where('original_date_hired','<=',date('Y-11-30', strtotime("$year-01-01")))
            ->where('company_id', $company)
            ->where('classification','!=',8)
            ->when($company == 10, function($query) {
                $query->where('classification', '!=', 1);
            })
            ->when($half == '1st', function($query) {
                $query->where('classification', 2);
            })
            ->where('status','Active')
            ->get();
    }

    private function buildThirteenthMonthRows($employees, $salaryAdjustments, $payInstructions, $year, $company, $half)
    {
        $salaryAdjustmentsByPayreg = $salaryAdjustments->groupBy('pay_reg_id')->map(function($items) {
            return $items->sum('amount');
        });
        $payInstructionsByPayreg = $payInstructions->groupBy('payreg_id')->map(function($items) {
            return $items->sum('amount');
        });
        $firstReleasedByEmployee = $half == '1st' ? [] : $this->firstReleasedThirteenthMonthMap($employees, $year);

        return $employees->sortBy('last_name')->values()->map(function($employee) use ($salaryAdjustmentsByPayreg, $payInstructionsByPayreg, $firstReleasedByEmployee, $year, $company, $half) {
            $monthlyAmounts = [];
            $annualPayroll = 0;
            $salary = $employee->salary;
            $monthlySalary = $salary
                ? (float) $salary->basic_salary + (float) $salary->de_minimis + (float) $salary->subliq + (float) $salary->other_allowance
                : 0;

            if ($half == '1st') {
                for ($i = 1; $i <= 12; $i++) {
                    $monthlyAmounts[$i] = 0;
                }

                return [
                    'company' => optional($employee->company)->company_code,
                    'employee_code' => $employee->employee_code,
                    'last_name' => $employee->last_name,
                    'first_name' => $employee->first_name,
                    'middle_name' => $employee->middle_name,
                    'department' => optional($employee->department)->name,
                    'account_number' => $employee->bank_account_number,
                    'name' => trim($employee->last_name . ', ' . $employee->first_name),
                    'monthly_salary' => $monthlySalary,
                    'monthly_amounts' => $monthlyAmounts,
                    'annual_payroll' => 0,
                    'annual_thirteenth' => 0,
                    'first_released' => 0,
                    'release_amount' => $monthlySalary / 2,
                ];
            }

            for ($i = 1; $i <= 12; $i++) {
                if ($company == 10) {
                    $monthStart = date('Y-m-01', strtotime($year . '-' . $i . '-01 -1 month'));
                    $monthEnd = date('Y-m-t', strtotime($year . '-' . $i . '-01 -1 month'));
                } else {
                    $monthStart = date('Y-m-01', strtotime($year . '-' . $i . '-01'));
                    $monthEnd = date('Y-m-t', strtotime($year . '-' . $i . '-01'));
                }

                $payregs = $employee->get_payreg->whereBetween('cut_off_date', [$monthStart, $monthEnd]);
                $amount = $payregs->sum(function($payreg) use ($salaryAdjustmentsByPayreg, $payInstructionsByPayreg) {
                    return (float) $payreg->basic_pay
                        + (float) $payreg->deminimis
                        + (float) $payreg->other_allowances_basic_pay
                        + (float) $payreg->subliq
                        - (float) $payreg->absent_amount
                        - (float) $payreg->tardiness_amount
                        - (float) $payreg->undertime_amount
                        + (float) ($salaryAdjustmentsByPayreg[$payreg->id] ?? 0)
                        + (float) ($payInstructionsByPayreg[$payreg->id] ?? 0);
                });

                if ($i == 12 && $company != 10 && $monthlySalary > 0) {
                    $amount = $monthlySalary;
                }

                $monthlyAmounts[$i] = $amount;
                $annualPayroll += $amount;
            }

            $annualThirteenth = $annualPayroll / 12;
            $firstReleased = $firstReleasedByEmployee[$employee->employee_code] ?? 0;

            return [
                'company' => optional($employee->company)->company_code,
                'employee_code' => $employee->employee_code,
                'last_name' => $employee->last_name,
                'first_name' => $employee->first_name,
                'middle_name' => $employee->middle_name,
                'department' => optional($employee->department)->name,
                'account_number' => $employee->bank_account_number,
                'name' => trim($employee->last_name . ', ' . $employee->first_name),
                'monthly_salary' => $monthlySalary,
                'monthly_amounts' => $monthlyAmounts,
                'annual_payroll' => $annualPayroll,
                'annual_thirteenth' => $annualThirteenth,
                'first_released' => $firstReleased,
                'release_amount' => $annualThirteenth - $firstReleased,
            ];
        });
    }

    private function firstReleasedThirteenthMonthMap($employees, $year)
    {
        $employeeCodes = $employees->pluck('employee_code')->toArray();
        $released = Schema::hasTable('thirteenth_month_postings')
            ? ThirteenthMonthPosting::select('employee_no', DB::raw('SUM(release_amount) as total'))
                ->whereIn('employee_no', $employeeCodes)
                ->where('year', $year)
                ->where('half', '1st')
                ->groupBy('employee_no')
                ->pluck('total', 'employee_no')
                ->map(function($amount) {
                    return (float) $amount;
                })
                ->toArray()
            : [];

        foreach ($employees as $employee) {
            if (isset($released[$employee->employee_code]) && $released[$employee->employee_code] > 0) {
                continue;
            }

            $released[$employee->employee_code] = $employee->get_payreg->sum(function($payreg) use ($year) {
                if (date('Y', strtotime($payreg->cut_off_date)) != $year) {
                    return 0;
                }

                return $payreg->pay_instructions
                    ->where('instruction_name', 'THIRTEENTH MONTH PAY NONTAXABLE')
                    ->filter(function($instruction) use ($payreg, $year) {
                        return stripos($instruction->remarks, '1st Half') !== false
                            || $payreg->cut_off_date <= date('Y-06-30', strtotime("$year-01-01"));
                    })
                    ->sum('amount');
            });
        }

        $missingEmployeeCodes = collect($released)
            ->filter(function($amount) {
                return $amount <= 0;
            })
            ->keys()
            ->values();

        if ($missingEmployeeCodes->isNotEmpty()) {
            $instructionAmounts = PayInstruction::select('site_id', DB::raw('SUM(amount) as total'))
                ->whereIn('site_id', $missingEmployeeCodes)
                ->where('benefit_name', 'THIRTEENTH MONTH PAY NONTAXABLE')
                ->whereYear('start_date', $year)
                ->where('remarks', 'like', '%13th Month 1st Half%')
                ->groupBy('site_id')
                ->pluck('total', 'site_id');

            foreach ($missingEmployeeCodes as $employeeCode) {
                $released[$employeeCode] = (float) ($instructionAmounts[$employeeCode] ?? 0);
            }
        }

        return $released;
    }
}
