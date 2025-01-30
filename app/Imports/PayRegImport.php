<?php

namespace App\Imports;

use App\Payregs;
use App\Employee;
use App\SalaryAdjustment;
use App\PayregInstruction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;

class PayRegImport implements ToModel, WithHeadingRow
{
    // public function model(array $row)
    // {
    
    //     $payroll_date_from = $row['pay_period_from'];
    //     $payroll_date_to = $row['pay_period_to'];
    //     $posting_date = $row['posting_date'];
        
    //     if (is_numeric($payroll_date_from)) {
    //         $payroll_date_from = Date::excelToDateTimeObject($payroll_date_from)->format('Y-m-d');
    //     }

    //     if (is_numeric($payroll_date_to)) {
    //         $payroll_date_to = Date::excelToDateTimeObject($payroll_date_to)->format('Y-m-d');
    //     }
    //     if (is_numeric($posting_date)) {
    //         $posting_date = Date::excelToDateTimeObject($posting_date)->format('Y-m-d');
    //     }
    //     // dd($row['employee_no']);
    //     if (isset($row['employee_no'])) {
    //     return new Payregs([
    //         "employee_no" => $row['employee_no']?? null,
    //         "last_name" => $row['last_name']?? null,
    //         "first_name" => $row['first_name']?? null,
    //         "middle_name" => $row['middle_name']?? null,
    //         "department" => $row['department']?? null,
    //         "cost_center" => $row['cost_center']?? null,
    //         "account_number" =>$row['account_no']?? null,
    //         "pay_rate" => $row['pay_rate']?? null,
    //         "tax_status" => $row['tax_status']?? null,
    //         "days_rendered" => $row['days_rendered']?? null,
    //         "basic_pay" => $row['basic_pay']?? null,
    //         "lh_nd" => $row['lh_nd'] ?? null,
    //         "lh_nd_amount" => $row['lh_nd_amount']?? null,
    //         "lh_nd_ge" => $row['lh_nd_ge']?? null,
    //         "lh_nd_ge_amount" => $row['lh_nd_ge_amount']?? null,
    //         "lh_ot" => $row['lh_ot']?? null,
    //         "lh_ot_amount" => $row['lh_ot_amount']?? null,
    //         "lh_ot_ge" => $row['lh_ot_ge']?? null,
    //         "lh_ot_ge_amount" => $row['lh_ot_ge_amount']?? null,
    //         "sh_nd" => $row['sh_nd']?? null,
    //         "sh_amount" => $row['sh_amount']?? null,
    //         "sh_ge" => $row['sh_ge']?? null,
    //         "sh_nd_ge" => $row['sh_nd_ge']?? null,
    //         "sh_nd_ge_amount" => $row['sh_nd_ge_amount']?? null,
    //         "sh_ot" => $row['sh_ot']?? null,
    //         "sh_ot_amount" => $row['sh_ot_amount']?? null,
    //         "sh_ot_ge" => $row['sh_ot_ge']?? null,
    //         "sh_ot_ge_amount" => $row['sh_ot_ge_amount']?? null,
    //         "sh_nd_amount" => $row['sh_nd_amount']?? null,
    //         "reg_nd" => $row['reg_nd']?? null,
    //         "reg_nd_amount" => $row['reg_nd_amount']?? null,
    //         "reg_ot" => $row['reg_ot']?? null,
    //         "reg_ot_amount" => $row['reg_ot_amount']?? null,
    //         "reg_ot_nd" => $row['reg_ot_nd']?? null,
    //         "reg_ot_nd_amount" => $row['reg_ot_nd_amount']?? null,
    //         "rst_nd" => $row['rst_nd']?? null,
    //         "rst_nd_amount" => $row['rst_nd_amount']?? null,
    //         "rst_nd_ge" => $row['rst_nd_ge']?? null,
    //         "rst_nd_ge_amount" => $row['rst_nd_ge_amount']?? null,
    //         "rst_ot" => $row['rst_ot']?? null,
    //         "rst_ot_amount" => $row['rst_ot_amount']?? null,
    //         "rst_ot_ge" => $row['rst_ot_ge']?? null,
    //         "rst_ot_ge_amount" => $row['rst_ot_ge_amount']?? null,
    //         "ot_total" => $row['ot_total']?? null,
    //         "pl" => $row['pl']?? null,
    //         "pl_amount" => $row['pl_amount']?? null,
    //         "leave_amount_total" => $row['leave_amount_total']?? null,
    //         "salary_adjustment" => $row['salary_adjustment']?? null,
    //         "taxable_benefits_total" => $row['taxable_benefits_total']?? null,
    //         "gross_taxable_income" => $row['gross_taxable_income']?? null,
    //         "days_absent" => $row['days_absent']?? null,
    //         "absent_amount" => (($row['absent_amount'] ?? 0) + ($row['leave_amount_total'] ?? 0)) * -1,
    //         "tardiness_total" => $row['tardiness_total']?? null,
    //         "tardiness_amount" => ($row['tardiness_amount'] ?? null) * -1,
    //         "undertime_total" => $row['undertime_total']?? null,
    //         "undertime_amount" => ($row['undertime_amount'] ?? null) * -1,
    //         "sss_ec" => ($row['sss_ec']?? null) * -1,
    //         "sss_employee_share" => ($row['sss_employee_share']?? null) * -1,
    //         "sss_employer_share" => ($row['sss_employer_share']?? null) * -1,
    //         "hdmf_employee_share" => ($row['hdmf_employee_share']?? null) * -1,
    //         "hdmf_employer_share" => ($row['hdmf_employer_share']?? null) * -1,
    //         "phic_employee_share" => ($row['phic_employee_share']?? null) * -1,
    //         "phic_employer_share" => ($row['phic_employer_share']?? null) * -1,
    //         "mpf_employee_share" => ($row['mpf_employee_share']?? null) * -1,
    //         "mpf_employer_share" => ($row['mpf_employer_share']?? null) * -1,
    //         "statutory_total" => ($row['statutory_total']?? null) * -1,
    //         "taxable_deductible_total" =>($row['taxable_deductible_total']?? null) * -1,
    //         "net_taxable_income" => $row['net_taxable_income']?? null,
    //         "withholding_tax" => $row['withholding_tax']?? null,
    //         "deminimis" => $row['deminimis']?? null,
    //         "nontaxable_benefits_total" => $row['nontaxable_benefits_total']?? null,
    //         "nontaxable_deductible_benefits_total" => ($row['nontaxable_deductible_benefits_total']?? null) * -1,
    //         "gross_pay" => $row['gross_pay']?? null,
    //         "deductions_total" => ($row['deductions_total']?? null) * -1,
    //         "netpay" => $row['netpay']?? null,
    //         "pay_period_from" => $payroll_date_from?? null,
    //         "pay_period_to" => $payroll_date_to?? null,
    //         "posting_date" => $posting_date?? null,
    //         "posted_by" => "272",
    //         "cut_off_date" => $payroll_date_to?? null,
    //         "company_id" => $row['company_id']?? null,
    //         "other_allowances_basic_pay" => $row['other_allowances']?? null,
    //         "subliq" => $row['subliq']?? null,
    //     ]);
    //     }
    // }
    public function model(array $row)
    {
    

        $posting_date = $row['posting_date'];

        if (is_numeric($posting_date)) {
            $posting_date = Date::excelToDateTimeObject($posting_date)->format('Y-m-d');
        }
        $input = 'retro';
        $retro = [];
        $i = 0;
        $i_count = 0;
        // dd($row);
        $data = [];
        foreach($row as $index => $r)
        {
            if(str_contains($index,"nontaxable_deductible_benefits_total"))
            {
                $i_count = 1;
            }
            
    
                if($i_count == 2)
                {
                    // dd('renz');
                    if($row[$index] != 0)
                        {
                            $employee_info = Employee::where('employee_code',$row['employee_no'])->first();
                            if($employee_info)
                                {
                                    $pay_reg = Payregs::where('employee_no',$row['employee_no'])->where('posting_date',$posting_date)->first();
                                   if($pay_reg != null)
                                   {
                                    $data[] = new PayregInstruction([
                                        "employee_code" => $row['employee_no'],
                                        "instruction_name" => str_replace('_', ' ', $index),
                                        "amount" => $row[$index],
                                        "created_by" => "272",
                                        "payreg_id" => $pay_reg->id,
                                    ]);
                                   }
                                //    else
                                //    {
                                //     dd($row['employee_no']);
                                //    }
                                
                                    // dd($row['employee_no']);
                                  
                                
                            
                                    
                                }        
                        }
                    
                }
                
                // dd($index);
            if (str_contains($index, 'nontaxable_benefits'))
            {
                // dd('renz');
                $i_count = 2;
            }
        
    }
        // dd($data);
       
        return $data;
     
    }
}

