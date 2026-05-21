<?php

namespace App\Services;

use App\AttendanceDetailedReport;
use App\ContributionSSS;
use App\EmployeeAllowance;
use App\Loan;
use App\PayInstruction;
use App\Payregs;
use App\SalaryAdjustment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class PayrollRegisterCalculator
{
    public function build($companyId, $cutoff)
    {
        $context = new stdClass;
        $context->company = $companyId;
        $context->cutoff = $cutoff;
        $context->from = null;
        $context->to = null;
        $context->dates = collect();
        $context->names = collect();
        $context->absents_data = collect();
        $context->shifts = collect();
        $context->last_cut_off = collect();
        $context->allowances_total = collect();
        $context->salary_adjustments = collect();
        $context->loans_all = collect();
        $context->instructions = collect();
        $context->benefit_instructions = collect();
        $context->deduction_instructions = collect();
        $context->sss = ContributionSSS::orderBy('salary_to', 'asc')->get();
        $context->rows = collect();

        if (!$companyId || !$cutoff) {
            return $context;
        }

        $context->dates = AttendanceDetailedReport::select(DB::raw('DAY(log_date) as log_date'))
            ->groupBy('log_date')
            ->where('cut_off_date', $cutoff)
            ->where('company_id', $companyId)
            ->get();

        $context->last_cut_off = Payregs::with(['pay_allowances', 'pay_instructions'])
            ->where('cut_off_date', '<', $cutoff)
            ->orderBy('cut_off_date', 'desc')
            ->where('company_id', $companyId)
            ->get();

        $context->absents_data = AttendanceDetailedReport::whereColumn('abs', '>', 'lv_w_pay')
            ->where('company_id', $companyId)
            ->where('cut_off_date', $cutoff)
            ->get();

        $context->shifts = AttendanceDetailedReport::where('shift', 'NOT LIKE', '%REST%')
            ->where('company_id', $companyId)
            ->where('cut_off_date', $cutoff)
            ->get();

        $context->names = $this->attendanceRows($companyId, $cutoff);

        if ($context->names->isEmpty()) {
            return $context;
        }

        $firstAttendance = AttendanceDetailedReport::where('company_id', $companyId)
            ->where('cut_off_date', $cutoff)
            ->orderBy('log_date', 'asc')
            ->first();
        $lastAttendance = AttendanceDetailedReport::where('company_id', $companyId)
            ->where('cut_off_date', $cutoff)
            ->orderBy('log_date', 'desc')
            ->first();

        $context->from = $firstAttendance ? $firstAttendance->log_date : null;
        $context->to = $lastAttendance ? $lastAttendance->log_date : null;

        $userIds = $context->names->pluck('employee.user_id')->filter()->toArray();
        $employeeIds = $context->names->pluck('employee.id')->filter()->toArray();
        $employeeCodes = $context->names->pluck('employee.employee_code')->filter()->toArray();

        $context->allowances_total = EmployeeAllowance::with('allowance')
            ->whereIn('user_id', $userIds)
            ->select('allowance_id')
            ->groupBy('allowance_id')
            ->get();

        $context->salary_adjustments = SalaryAdjustment::whereIn('employee_id', $employeeIds)
            ->where('cut_off_date', null)
            ->select('name')
            ->groupBy('name')
            ->get();

        $context->loans_all = Loan::with('loan_type')
            ->whereIn('employee_id', $employeeIds)
            ->where('status', 'Active')
            ->select('loan_type_id')
            ->groupBy('loan_type_id')
            ->get();

        $context->instructions = $this->instructionNames($employeeCodes, $cutoff);
        $context->benefit_instructions = $this->instructionNames($employeeCodes, $cutoff, '>');
        $context->deduction_instructions = $this->instructionNames($employeeCodes, $cutoff, '<');

        $context->rows = $context->names->map(function ($attendance) use ($context) {
            return $this->calculateRow($attendance, $context);
        });

        return $context;
    }

    private function instructionNames(array $employeeCodes, $cutoff, $amountOperator = null)
    {
        $query = PayInstruction::whereIn('site_id', $employeeCodes)
            ->where('start_date', '>=', $cutoff)
            ->where('end_date', '<=', $cutoff);

        if ($amountOperator) {
            $query->where('amount', $amountOperator, 0);
        }

        return $query->select('benefit_name')
            ->groupBy('benefit_name')
            ->get();
    }

    private function attendanceRows($companyId, $cutoff)
    {
        return AttendanceDetailedReport::with([
                'employee' => function ($query) {
                    $query->where('status', 'Active');
                },
                'employee.department',
                'employee.tax_mapping',
                'employee.salary',
                'employee.loan' => function ($query) {
                    $query->where('status', 'Active');
                },
                'employee.loan.pay',
                'employee.salary_adjustments' => function ($query) {
                    $query->where('cut_off_date', null);
                },
                'employee.allowances' => function ($query) {
                    $query->where('status', 'Active');
                },
                'employee.pay_instructions' => function ($query) use ($cutoff) {
                    $query->where('start_date', '>=', $cutoff)
                        ->where('end_date', '<=', $cutoff);
                },
            ])
            ->whereHas('employee', function ($query) {
                $query->where('status', 'Active');
            })
            ->select(
                'company_id',
                'employee_no',
                'name',
                'cut_off_date',
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
                DB::raw('SUM(rst_lh_ot) as total_rst_lh_ot'),
                DB::raw('SUM(rst_lh_ot_over_eight) as total_rst_lh_ot_over_eight'),
                DB::raw('SUM(rst_lh_nd) as total_rst_lh_nd'),
                DB::raw('SUM(rst_lh_nd_over_eight) as total_rst_lh_nd_over_eight'),
                DB::raw('SUM(rst_sh_ot) as total_rst_sh_ot'),
                DB::raw('SUM(rst_sh_ot_over_eight) as total_rst_sh_ot_over_eight'),
                DB::raw('SUM(rst_sh_nd) as total_rst_sh_nd'),
                DB::raw('SUM(rst_sh_nd_over_eight) as total_rst_sh_nd_over_eight'),
                DB::raw('SUM(late_min) as total_late_min'),
                DB::raw('SUM(undertime_min) as total_undertime_min')
            )
            ->where('company_id', $companyId)
            ->where('cut_off_date', $cutoff)
            ->groupBy('company_id', 'employee_no', 'name', 'cut_off_date')
            ->get();
    }

    private function calculateRow($attendance, $context)
    {
        $employee = $attendance->employee;
        $isFirstCutoff = (bool) $context->dates->where('log_date', 10)->first();
        $isSecondCutoff = (bool) $context->dates->where('log_date', 25)->first();

        $row = new stdClass;
        $row->attendance = $attendance;
        $row->employee = $employee;
        $row->allowance_amounts = [];
        $row->instruction_amounts = [];
        $row->loan_amounts = [];
        $row->salary_adjustment_amounts = [];

        $daysRendered = $attendance->shift_count - $attendance->total_abs + $attendance->total_lv_w_pay;
        if ($employee && $employee->employee_code == 'A278816') {
            $daysRendered = $this->specialRenderedDays($context->shifts->where('employee_no', $attendance->employee_no));
        }

        $payRate = 0;
        $basicPay = 0;
        $otherAllowancesBasicPay = 0;
        $subliq = 0;
        $hourlyRate = 0;
        $hourlyRateOtherAllowance = 0;
        $hourlyRateSubliq = 0;
        $dailyRate = 0;
        $deMinimis = 0;

        if ($employee && $employee->salary) {
            $salary = $employee->salary;
            $payRate = $salary->basic_salary;
            $basicPay = $salary->basic_salary / 2;
            $otherAllowancesBasicPay = $salary->other_allowance / 2;
            $subliq = $salary->subliq / 2;
            $dailyRate = ($salary->basic_salary * 12) / 313;
            $hourlyRate = $dailyRate / 8;
            $hourlyRateOtherAllowance = (($salary->other_allowance) * 12 / 313) / 8;
            $hourlyRateSubliq = (($salary->subliq) * 12 / 313) / 8;
            $deMinimis = $salary->de_minimis / 2;

            if ($employee->work_description == 'Non-Monthly') {
                $dailyDeMinimis = ($salary->de_minimis ?: 0) * 12 / 313;
                $basicPay = $dailyRate * number_format($daysRendered, 2);
                $deMinimis = $dailyDeMinimis * $daysRendered;
            }

            if ($employee->level == 4) {
                $basicPay = $salary->basic_salary;
                $deMinimis = $salary->de_minimis;
                $subliq = $salary->subliq;
            }
        }

        $absentHours = $this->absentHours($context->absents_data->where('employee_no', $attendance->employee_no));

        $scheduledLoans = $employee ? payrollScheduledItems($employee->loan, $isFirstCutoff) : collect();
        $totalLoans = $scheduledLoans->sum(function ($loan) {
            return payrollLoanDeductionAmount($loan);
        });

        $salaryAdjustment = $employee && $employee->salary_adjustments
            ? $employee->salary_adjustments->sum('amount')
            : 0;

        $allowances = $employee && $employee->allowances ? $employee->allowances : collect();
        $scheduledAllowances = payrollScheduledItems($allowances, $isFirstCutoff);
        $totalAllowances = $scheduledAllowances->sum('allowance_amount');
        $totalAllowancesSss = $scheduledAllowances->where('allowance_id', '!=', 9)->sum('allowance_amount');

        $instructions = $employee && $employee->pay_instructions ? $employee->pay_instructions : collect();
        $scheduledInstructions = payrollScheduledItems($instructions, $isFirstCutoff, 'frequency');
        $totalPayrollInstructions = $scheduledInstructions->sum('amount');
        $totalNonTaxableInstructionBenefits = $scheduledInstructions->where('amount', '>', 0)->sum('amount');
        $totalPayrollInstructionDeductions = abs($scheduledInstructions->where('amount', '<', 0)->sum('amount'));

        $ot = $this->overtimeAmounts($attendance, $payRate, $hourlyRate);
        $leaveTotalAmount = 0;
        $totalTaxableBenefits = $salaryAdjustment;
        $grossTaxableIncome = $basicPay + $ot['total_ot_pay'] + $leaveTotalAmount + $totalTaxableBenefits;
        $totalLateAmount = ($attendance->total_late_min / 60) * $hourlyRate;
        $totalUndertimeAmount = ($attendance->total_undertime_min / 60) * $hourlyRate;
        $totalAbsCount = $attendance->total_abs - $attendance->total_lv_w_pay;
        $totalAbsAmount = $employee && $employee->work_description == 'Non-Monthly' ? 0 : $absentHours * $hourlyRate;

        if ($employee && $employee->work_description != 'Non-Monthly') {
            $deductibleHours = $absentHours + $attendance->total_late_min / 60 + $attendance->total_undertime_min / 60;
            if ($otherAllowancesBasicPay > 0) {
                $otherAllowancesBasicPay -= $deductibleHours * $hourlyRateOtherAllowance;
            }
            if ($subliq > 0) {
                $subliq -= $deductibleHours * $hourlyRateSubliq;
            }
        }

        $governmentAmount = $grossTaxableIncome - $totalAbsAmount - $totalLateAmount - $totalUndertimeAmount + $deMinimis + $otherAllowancesBasicPay + $subliq;
        $lastContributionAmount = 0;
        $previousPayRate = 0;
        $previousBasicPay = 0;

        if ($isSecondCutoff) {
            $last = $context->last_cut_off
                ->where('employee_no', $attendance->employee_no)
                ->where('cut_off_date', '>', date('Y-m-d', strtotime($attendance->cut_off_date . ' -17 days')))
                ->first();

            if ($last) {
                $deMinimisDeduction = $last->pay_instructions
                    ->where('payreg_id', $last->id)
                    ->where('instruction_name', 'De Minimis Deduction')
                    ->sum('amount');
                $deMinimisAdjustment = $last->pay_instructions
                    ->where('payreg_id', $last->id)
                    ->where('instruction_name', 'De Minimis Adjustment')
                    ->sum('amount');

                $lastContributionAmount = $last->gross_taxable_income - $last->absent_amount - $last->tardiness_amount - abs($deMinimisDeduction) - $last->undertime_amount + $last->deminimis + $last->other_allowances_basic_pay + $last->subliq + $deMinimisAdjustment;
                $deMinimisCurrentAdjustment = $scheduledInstructions->where('benefit_name', 'De Minimis Adjustment')->sum('amount');
                $governmentAmount = round($governmentAmount + $deMinimisCurrentAdjustment + $lastContributionAmount, 2);
                $previousPayRate = $last->pay_rate / 2;
                $previousBasicPay = $last->basic_pay;
            }
        }

        $contributions = $this->contributions($employee, $attendance, $context->sss, $governmentAmount, $payRate, $basicPay, $previousPayRate, $previousBasicPay);
        $statutory = $contributions['sss_ee'] + $contributions['wisp_ee'] + $contributions['hdmf'] + $contributions['philhealth'];
        if ($employee && $employee->employee_code == 'A197326') {
            $statutory += $contributions['sss_er'] + $contributions['hdmf'] + $contributions['philhealth'] + $contributions['sss_ecc'];
        }

        $taxableDeductibleTotal = $statutory + $totalAbsAmount + $totalLateAmount + $totalUndertimeAmount;
        $netTaxableIncome = $grossTaxableIncome - $taxableDeductibleTotal;
        if ($employee && $employee->employee_code == 'A162313') {
            $netTaxableIncome = $grossTaxableIncome;
        }

        $tax = $employee ? compute_tax($netTaxableIncome, $employee->level) : 0;
        if ($employee && in_array($employee->employee_code, ['A162313', 'A287819', 'A188323', 'A180518', 'A178517', 'A2114926'])) {
            $tax = $netTaxableIncome * .05;
        }
        if ($employee && $employee->employee_code == 'M1010') {
            $tax = 0;
        }
        if ($employee && $employee->tax_mapping) {
            if ($employee->tax_mapping->tin != 1) {
                $tax = 0;
            } elseif ($employee->tax_mapping->tax_percent > 0) {
                $tax = $netTaxableIncome * $employee->tax_mapping->tax_percent;
            }
        }

        foreach ($context->allowances_total as $allowanceType) {
            $row->allowance_amounts[$allowanceType->allowance_id] = $scheduledAllowances
                ->where('allowance_id', $allowanceType->allowance_id)
                ->sum('allowance_amount');
        }

        foreach ($context->instructions as $instructionType) {
            $row->instruction_amounts[$instructionType->benefit_name] = $scheduledInstructions
                ->where('benefit_name', $instructionType->benefit_name)
                ->sum('amount');
        }

        foreach ($context->loans_all as $loanType) {
            $row->loan_amounts[$loanType->loan_type_id] = $scheduledLoans
                ->where('loan_type_id', $loanType->loan_type_id)
                ->sum(function ($loan) {
                    return payrollLoanDeductionAmount($loan);
                });
        }

        foreach ($context->salary_adjustments as $adjustmentType) {
            $row->salary_adjustment_amounts[$adjustmentType->name] = $employee && $employee->salary_adjustments
                ? $employee->salary_adjustments->where('name', $adjustmentType->name)->sum('amount')
                : 0;
        }

        $nontaxableBenefitsTotal = $totalAllowances + $deMinimis + $otherAllowancesBasicPay + $subliq + $totalNonTaxableInstructionBenefits;
        $nontaxableDeductibleBenefitsTotal = $totalLoans + $totalPayrollInstructionDeductions;
        $grossPay = $grossTaxableIncome + $nontaxableBenefitsTotal;
        $deductionsTotal = $taxableDeductibleTotal + $totalLoans + $tax + $totalPayrollInstructionDeductions;
        $netpay = $grossPay - $taxableDeductibleTotal - $totalLoans - $tax - $totalPayrollInstructionDeductions;

        $row->values = array_merge($ot, $contributions, [
            'employee_no' => $attendance->employee_no,
            'last_name' => $employee ? $employee->last_name : '',
            'first_name' => $employee ? $employee->first_name : '',
            'middle_name' => $employee ? $employee->middle_name : '',
            'department' => $employee && $employee->department ? $employee->department->name : '',
            'cost_center' => '',
            'account_number' => $employee ? $employee->bank_account_number : '',
            'pay_rate' => $payRate,
            'tax_status' => '',
            'days_rendered' => $daysRendered,
            'basic_pay' => $basicPay,
            'salary_adjustment' => $salaryAdjustment,
            'taxable_benefits_total' => $totalTaxableBenefits,
            'gross_taxable_income' => $grossTaxableIncome,
            'days_absent' => $totalAbsCount,
            'absent_amount' => $totalAbsAmount,
            'tardiness_total' => $attendance->total_late_min,
            'tardiness_amount' => $totalLateAmount,
            'undertime_total' => $attendance->total_undertime_min,
            'undertime_amount' => $totalUndertimeAmount,
            'statutory_total' => $statutory,
            'taxable_deductible_total' => $taxableDeductibleTotal,
            'net_taxable_income' => $netTaxableIncome,
            'withholding_tax' => $tax,
            'deminimis' => $deMinimis,
            'other_allowances_basic_pay' => $otherAllowancesBasicPay,
            'subliq' => $subliq,
            'nontaxable_benefits_total' => $nontaxableBenefitsTotal,
            'nontaxable_deductible_benefits_total' => $nontaxableDeductibleBenefitsTotal,
            'gross_pay' => $grossPay,
            'deductions_total' => $deductionsTotal,
            'netpay' => $netpay,
            'government_amount' => $governmentAmount,
            'last_contribution_amount' => $lastContributionAmount,
            'current_contribution_amount' => $governmentAmount - $lastContributionAmount,
            'previous_pay_rate' => $previousPayRate,
            'previous_basic_pay_rate' => $previousBasicPay,
            'total_allowances' => $totalAllowances,
            'total_payroll_instructions' => $totalPayrollInstructions,
            'total_nontaxable_instruction_benefits' => $totalNonTaxableInstructionBenefits,
            'total_payroll_instruction_deductions' => $totalPayrollInstructionDeductions,
            'total_loans' => $totalLoans,
        ]);

        return $row;
    }

    private function specialRenderedDays(Collection $shifts)
    {
        $days = 0;
        foreach ($shifts as $shiftData) {
            $shift = str_replace(['(Semi-Flexi)', '(Semi - flexi)', '(Flexi)'], '', $shiftData->shift);
            $parts = explode('-', $shift);
            if (count($parts) < 2) {
                continue;
            }

            $start = strtotime($parts[0]);
            $end = strtotime($parts[1]);
            $shiftCount = ($end - $start) / 3600;
            if ($start > $end) {
                $shiftCount = (($start - $end) / 3600) - 8;
            }
            if ($shiftCount > 8) {
                $shiftCount -= 1;
            }
            $days += $shiftCount / 8;
        }

        return $days;
    }

    private function absentHours(Collection $absents)
    {
        $hours = 0;
        foreach ($absents as $absent) {
            $shift = trim(str_replace(['(Semi-Flexi)', '(Semi - flexi)', '(Flexi)'], '', $absent->shift));
            $parts = explode('-', $shift);
            if (count($parts) < 2) {
                continue;
            }

            $start = strtotime($absent->log_date . ' ' . $parts[0]);
            $end = strtotime($absent->log_date . ' ' . $parts[1]);
            $hoursCount = ($end - $start) / 3600;
            if ($hoursCount < 0) {
                $hoursCount = (($end + 86400 - $start) / 3600);
            }
            if ($hoursCount > 8) {
                $hoursCount -= 1;
            }
            if ($absent->employee_no == 'A340612' && $hoursCount > 6) {
                $hoursCount -= 1;
            }

            $hours += $hoursCount * ($absent->abs - $absent->lv_w_pay);
        }

        return $hours;
    }

    private function overtimeAmounts($attendance, $payRate, $hourlyRate)
    {
        $values = [
            'lh_nd_amount' => $attendance->total_lh_nd * $hourlyRate * .2,
            'lh_nd_ge_amount' => $attendance->total_lh_nd_over_eight * $hourlyRate * .26,
            'lh_ot_amount' => $attendance->total_lh_ot * $hourlyRate,
            'lh_ot_ge_amount' => $attendance->total_lh_ot_over_eight * $hourlyRate * 2.6,
            'sh_nd_amount' => $attendance->total_sh_nd * $hourlyRate * .13,
            'sh_nd_ge_amount' => $attendance->total_sh_nd_over_eight * $hourlyRate * .169,
            'sh_ot_amount' => $attendance->total_sh_ot * $hourlyRate * .3,
            'sh_ot_ge_amount' => $attendance->total_sh_ot_over_eight * $hourlyRate * 1.69,
            'reg_nd_amount' => $attendance->total_reg_nd * $hourlyRate * .1,
            'reg_ot_amount' => $attendance->total_reg_ot * $hourlyRate * 1.25,
            'reg_ot_nd_amount' => $attendance->total_reg_ot_nd * $hourlyRate * .125,
            'rst_nd_amount' => $attendance->total_rst_nd * $hourlyRate * .13,
            'rst_nd_ge_amount' => $attendance->total_rst_nd_over_eight * $hourlyRate * .169,
            'rst_ot_amount' => $attendance->total_rst_ot * $hourlyRate * 1.3,
            'rst_ot_ge_amount' => $attendance->total_rst_ot_over_eight * $hourlyRate * 1.69,
            'rst_lh_ot_amount' => $payRate * 12 / 313 / 8 * 2.6 * $attendance->total_rst_lh_ot,
            'rst_lh_ot_ge_amount' => $payRate * 12 / 313 / 8 * 3.38 * $attendance->total_rst_lh_ot_over_eight,
            'rst_lh_nd_amount' => $payRate * 12 / 313 / 8 * 0.26 * $attendance->total_rst_lh_nd,
            'rst_lh_nd_ge_amount' => $payRate * 12 / 313 / 8 * 0.34 * $attendance->total_rst_lh_nd_over_eight,
            'rst_sh_ot_amount' => $payRate * 12 / 313 / 8 * 1.5 * $attendance->total_rst_sh_ot,
            'rst_sh_ot_ge_amount' => $payRate * 12 / 313 / 8 * 1.95 * $attendance->total_rst_sh_ot_over_eight,
            'rst_sh_nd_amount' => $payRate * 12 / 313 / 8 * 0.26 * $attendance->total_rst_sh_nd,
            'rst_sh_nd_ge_amount' => $payRate * 12 / 313 / 8 * 0.34 * $attendance->total_rst_sh_nd_over_eight,
        ];

        $values['total_ot_pay'] = array_sum($values);

        return $values;
    }

    private function contributions($employee, $attendance, Collection $sss, $governmentAmount, $payRate, $basicPay, $previousPayRate, $previousBasicPay)
    {
        $values = [
            'sss_ecc' => 0,
            'sss_ee' => 0,
            'sss_er' => 0,
            'hdmf' => 0,
            'philhealth' => 0,
            'wisp_ee' => 0,
            'wisp_er' => 0,
        ];

        if (!$employee) {
            return $values;
        }

        $sssAmount = $sss->where('salary_to', '>=', $governmentAmount)->first();
        if ($sssAmount) {
            $values['sss_ecc'] = $sssAmount->ecc;
            $values['sss_ee'] = $sssAmount->regular_ee;
            $values['sss_er'] = $sssAmount->regular_er;
            $values['wisp_ee'] = $sssAmount->wisp_ee;
            $values['wisp_er'] = $sssAmount->wisp_er;
        }

        if ($employee->employee_code == 'A287819') {
            $values = array_merge($values, ['sss_ecc' => 0, 'sss_ee' => 1190, 'sss_er' => 0, 'wisp_ee' => 0, 'wisp_er' => 0]);
        }
        if ($employee->employee_code == 'A180518') {
            $values = array_merge($values, ['sss_ecc' => 0, 'sss_ee' => 2590, 'sss_er' => 0, 'wisp_ee' => 0, 'wisp_er' => 0]);
        }
        if ($employee->employee_code == 'A178517') {
            $values = array_merge($values, ['sss_ecc' => 0, 'sss_ee' => 2800, 'sss_er' => 0, 'wisp_ee' => 0, 'wisp_er' => 0]);
        }
        if ($employee->employee_code == 'A162313') {
            $values = array_merge($values, ['sss_ecc' => 0, 'sss_ee' => 4200, 'sss_er' => 0, 'wisp_ee' => 0, 'wisp_er' => 0]);
        }
        if (in_array($employee->employee_code, ['A190524', 'A194825'])) {
            $values = array_merge($values, ['sss_ecc' => 0, 'sss_ee' => 0, 'sss_er' => 0, 'wisp_ee' => 0, 'wisp_er' => 0]);
        }
        if ($employee->employee_code == 'A197326') {
            $values = array_merge($values, ['sss_ecc' => 10, 'sss_ee' => 675, 'sss_er' => 1350, 'wisp_ee' => 0, 'wisp_er' => 0]);
        }

        $values['hdmf'] = 200;
        if (in_array($employee->employee_code, ['A190524', 'A194825'])) {
            $values['hdmf'] = 0;
        }

        $values['philhealth'] = (($previousPayRate + ($payRate / 2)) * .05) / 2;
        if ($employee->employee_code == 'A197326') {
            $values['philhealth'] = 250;
        }
        if ($employee->work_description == 'Non-Monthly') {
            $values['philhealth'] = (($basicPay + $previousBasicPay) * .05) / 2;
        }
        if ($values['philhealth'] >= 2500) {
            $values['philhealth'] = 2500;
        }
        if ($values['philhealth'] <= 250) {
            $values['philhealth'] = 250;
        }
        if (in_array($employee->employee_code, ['A287819', 'A178517', 'A180518'])) {
            $values['philhealth'] = 500;
        }
        if ($employee->employee_code == 'A162313') {
            $values['philhealth'] = 5000;
        }
        if (in_array($employee->employee_code, ['A190524', 'A194825', 'M1010', 'A188323', 'A2114926', 'A185221', 'A118109'])) {
            $values = array_merge($values, ['philhealth' => 0, 'hdmf' => 0, 'sss_ecc' => 0, 'sss_ee' => 0, 'sss_er' => 0, 'wisp_ee' => 0, 'wisp_er' => 0]);
        }
        if ($employee->employee_code == 'A3182024' && $attendance->cut_off_date == '2026-01-25') {
            $values = array_merge($values, ['philhealth' => 0, 'hdmf' => 0, 'sss_ecc' => 0, 'sss_ee' => 0, 'sss_er' => 0, 'wisp_ee' => 0, 'wisp_er' => 0]);
        }
        if ($employee->tax_mapping) {
            if ($employee->tax_mapping->sss != 1) {
                $values = array_merge($values, ['sss_ecc' => 0, 'sss_ee' => 0, 'sss_er' => 0, 'wisp_ee' => 0, 'wisp_er' => 0]);
            }
            if ($employee->tax_mapping->pagibig != 1) {
                $values['hdmf'] = 0;
            }
            if ($employee->tax_mapping->philhealth != 1) {
                $values['philhealth'] = 0;
            }
        }

        return $values;
    }
}
