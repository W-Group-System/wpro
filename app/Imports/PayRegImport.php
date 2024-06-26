<?php

namespace App\Imports;

use App\PayrollRecord;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;

class PayRegImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $payroll_date_from = $row['payroll_date_from'];
        $payroll_date_to = $row['payroll_date_to'];
        $date_hired = $row['date_hired'];


        if (is_numeric($payroll_date_from)) {
            $payroll_date_from = Date::excelToDateTimeObject($payroll_date_from)->format('Y-m-d');
        }

        if (is_numeric($payroll_date_to)) {
            $payroll_date_to = Date::excelToDateTimeObject($payroll_date_to)->format('Y-m-d');
        }
        if (is_numeric($date_hired)) {
            $date_hired = Date::excelToDateTimeObject($date_hired)->format('Y-m-d');
        }
        
        return new PayrollRecord([
            'payroll_date_from' => $payroll_date_from,
            'payroll_date_to' => $payroll_date_to,
            'name' => $row['name'],
            'employee_code' => $row['employee_code'],
            'bank_account_number' => $row['bank_account_number'],
            'bank' => $row['bank'],
            'position' => $row['position'],
            'department' => $row['department'],
            'location' => $row['location'],
            'date_hired' => $date_hired,
            'monthly_pay' => $row['monthly_pay'],
            'semi_monthly_pay' => $row['semi_monthly_pay'],
            'daily_pay' => $row['daily_pay'],
            'gross_pay' => $row['gross_pay'],
            'taxable' => $row['taxable'],
            'total_deduction' => $row['total_deduction'],
            'net_pay' => $row['net_pay'],
            'witholding_tax' => $row['witholding_tax'],
            'absences' => $row['absences'],
            'late' => $row['late'],
            'undertime' => $row['undertime'],
            'salary_adjustment' => $row['salary_adjustment'],
            'overime' => $row['overime'],
            'meal_allowance' => $row['meal_allowance'],
            'salary_allowance' => $row['salary_allowance'],
            'out_of_town_allowance' => $row['out_of_town_allowance'],
            'incentive_allowance' => $row['incentive_allowance'],
            'rellocation_allowance' => $row['rellocation_allowance'],
            'disc_allowance' => $row['disc_allowance'],
            'transporatation_allowance' => $row['transporatation_allowance'],
            'load_allowance' => $row['load_allowance'],
            'sick_leave' => $row['sick_leave'],
            'vacation_leave' => $row['vacation_leave'],
            'work_from_home' => $row['work_from_home'],
            'official_business' => $row['official_business'],
            'sl_no_pay' => $row['sl_no_pay'],
            'vl_no_pay' => $row['vl_no_pay'],
            'sss_reg_ee' => $row['sss_reg_ee'],
            'sss_mpf_ee' => $row['sss_mpf_ee'],
            'phic_ee' => $row['phic_ee'],
            'hdmf_ee' => $row['hdmf_ee'],
            'hdmf_salary_loan' => $row['hdmf_salary_loan'],
            'sss_salary_loan' => $row['sss_salary_loan'],
            'sss_calamity_loan' => $row['sss_calamity_loan'],
            'salary_non_tax' => $row['salary_non_tax'],
            'salary_loan' => $row['salary_loan'],
            'company_loan' => $row['company_loan'],
            'others' => $row['others'],
            'sss_reg_er' => $row['sss_reg_er'],
            'sss_mpf_er' => $row['sss_mpf_er'],
            'sss_ec' => $row['sss_ec'],
            'phic_er' => $row['phic_er'],
            'hdmf_er' => $row['hdmf_er'],
            'payroll_status' => $row['payroll_status'],
        ]);
    }
}

