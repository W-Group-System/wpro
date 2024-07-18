<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payslip</title>
</head>
<style>
    body
    {
        font-family: Arial, Helvetica, sans-serif;
    }
    @page 
    {
        margin: 5mm;
    }
    /* .page-break 
    {
        page-break-after: always;
    } */
    .employee-info-container p,
    .payroll-info-container p,
    .payroll-details-container p,
    .year-to-date-container p,
    .leave-container p
    {
        font-size: 11px;
        font-weight: bold
    }

    table
    {
        width: 100%;
    }

    hr
    {
        border: 1px solid black;
    }

    .employee-info-table,
    .payroll-info-table,
    .payroll-details-table
    {
        font-size: 11px;
    }

    .signature hr
    {
        position: relative;
        top: 11px;
        width: 30%;
    }
    .signature p:nth-of-type(1)
    {
        position: relative;
        top: 3px;
        font-size: 10px;
        text-align: center
    }
    .signature p:nth-of-type(2)
    {
        position: relative;
        top: 3px;
        font-size: 11px;
        text-align: center;
        font-style: italic;
    }
</style>
<body>
    <div class="page-break">
        <div class="image-container bg-primary">
            <img src="{{asset('images/m.png')}}" alt="wgroup" height="100" width="150">
        </div>
    
        <div class="employee-info-container">
            <p>EMPLOYEE INFORMATION</p>
            <hr>
        </div>

        <div class="employee-info-table">
            <table>
                <thead>
    
                </thead>
                <tbody>
                    <tr>
                        <td width='50%'>Employee Number: {{$payroll->employee_no}}</td>
                        <td>Date Hired: {{date('M d, Y',strtotime($payroll->employee->original_date_hired))}}</td>
                    </tr>
                    <tr>
                        <td>Employee Name: {{$payroll->last_name.", ".$payroll->first_name." ". $payroll->middle_name}}</td>
                        <td>SSS ID: {{$payroll->employee->sss_number}}</td>
                    </tr>
                    <tr>
                        <td>Department Name: {{$payroll->department}}</td>
                        <td>Pagibig ID: {{$payroll->employee->hdmf_number}}</td>
                    </tr>
                    <tr>
                        <td>TIN: {{$payroll->employee->tax_number}}</td>
                        <td>PhilHealth ID: {{$payroll->employee->phil_number}}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="payroll-info-container">
            <p>PAYROLL INFORMATION</p>
            <hr>
        </div>

        <div class="payroll-info-table">
            <table>
                <tbody>
                    <tr>
                        <td width='50%'>Payroll Period: {{date('F d, Y',strtotime($payroll->pay_period_from))}} - {{date('F d, Y',strtotime($payroll->pay_period_to))}}</td>
                        <td>Rendered: {{$payroll->days_rendered}}</td>
                    </tr>
                    <tr>
                        <td>Pay Type: @if($payroll->work_description == "Non-Monthly") Daily @else Monthly @endif</td>
                        <td>Absent: {{$payroll->days_absent}}</td>
                    </tr>
                    <tr>
                        <td>Rate: {{number_format($payroll->pay_rate,2)}}</td>
                        <td>Leave: {{$payroll->leave_count}}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p style="font-size: 12px; text-align: center; margin-top: 15px;">I acknowledge to have received from W Group Inc. the amount stated below and have no further claims for services rendered.</p>

        <div class="signature">
            <hr>
            <p>Signature over printed name</p>
            <p>Stricly confidential. Sharing this information is prohibited under our Employee Code of Conduct</p>
        </div>

        <div class="payroll-details-container">
            <p>PAYROLL DETAILS <span style="font-style:italic; font-weight:normal;">(All Amounts in PHP)</span></p>
            <hr>
        </div>

        <div class="payroll-details-table">
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Basic Pay</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($payroll->basic_pay,2)}}</td>
                    </tr>
                    @if($payroll->lh_nd_amount > 0)
                    <tr>
                        <td style=""> - LH ND</td>
                        <td style="text-align: right;">{{number_format($payroll->lh_nd_amount ,2)}}</td>
                    </tr>
                    @endif
                    @if($payroll->lh_nd_ge_amount > 0)
                    <tr>
                        <td style=""> - LH ND GE</td>
                        <td style="text-align: right; ">{{number_format($payroll->lh_nd_ge_amount ,2)}}</td>
                    </tr>
                    @endif
                    @if($payroll->lh_ot_amount > 0)
                    <tr>
                        <td style=""> - LH OT</td>
                        <td style="text-align: right; ">{{number_format($payroll->lh_ot_amount ,2)}}</td>
                    </tr>
                    @endif
                    @if($payroll->lh_ot_ge_amount > 0)
                    <tr>
                        <td style=""> - LH OT GE</td>
                        <td style="text-align: right; ">{{number_format($payroll->lh_ot_ge_amount ,2)}}</td>
                    </tr>
                    @endif
                    @if($payroll->reg_nd_amount > 0)
                    <tr>
                        <td style=""> - REG ND</td>
                        <td style="text-align: right;">{{number_format($payroll->reg_nd_amount ,2)}}</td>
                    </tr>
                    @endif
                    @if($payroll->reg_ot_amount > 0)
                    <tr>
                        <td style=""> - REG OT</td>
                        <td style="text-align: right;">{{number_format($payroll->reg_ot_amount ,2)}}</td>
                    </tr>
                    @endif
                    @if($payroll->reg_ot_nd_amount > 0)
                    <tr>
                        <td style=""> - REG OT ND</td>
                        <td style="text-align: right;">{{number_format($payroll->reg_ot_nd_amount ,2)}}</td>
                    </tr>
                    @endif
                    @if($payroll->rst_nd_amount > 0)
                    <tr>
                        <td style=""> - RD ND</td>
                        <td style="text-align: right;">{{number_format($payroll->rst_nd_amount ,2)}}</td>
                    </tr>
                    @endif
                    @if($payroll->rst_nd_ge_amount > 0)
                    <tr>
                        <td style=""> - RD ND GE</td>
                        <td style="text-align: right;">{{number_format($payroll->rst_nd_ge_amount ,2)}}</td>
                    </tr>
                    @endif
                    @if($payroll->rst_ot_amount > 0)
                    <tr>
                        <td style=""> - RD OT</td>
                        <td style="text-align: right;">{{number_format($payroll->rst_ot_amount ,2)}}</td>
                    </tr>
                    @endif
                    @if($payroll->rst_ot_ge_amount > 0)
                    <tr>
                        <td style=""> - RD OT GE</td>
                        <td style="text-align: right;">{{number_format($payroll->rst_ot_ge_amount ,2)}}</td>
                    </tr>
                    @endif

                    <tr>
                        <td style="font-weight: bold;"></td>
                        <td style="text-align: right; font-weight: bold;"></td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Gross Income</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($payroll->gross_taxable_income,2)}}</td>
                    </tr>
                </tbody>
            </table>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Less: Other Deductions</td>
                        <td style="text-align: right; font-weight: bold;"></td>
                    </tr>
                    @if($payroll->absent_amount > 0)
                    <tr>
                        <td>Absences</td>
                        <td style="text-align: right;">{{number_format($payroll->absent_amount,2)}}</td>
                    </tr>
                    @endif
                    @if($payroll->tardiness_amount > 0)
                    <tr>
                        <td>Lates</td>
                        <td style="text-align: right;">{{number_format($payroll->tardiness_amount,2)}}</td>
                    </tr>
                    @endif
                    @if($payroll->undertime_amount > 0)
                    <tr>
                        <td>Undertime</td>
                        <td style="text-align: right;">{{number_format($payroll->undertime_amount,2)}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <hr>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Gross Taxable Income</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($payroll->gross_taxable_income-$payroll->absent_amount-$payroll->tardiness_amount-$payroll->undertime_amount,2)}}</td>
                    </tr>
                    @if($payroll->sss_employee_share >0)
                    <tr>
                        <td style="font-weight: bold;">SSS</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($payroll->sss_employee_share,2)}}</td>
                    </tr>
                    @endif
                    @if($payroll->hdmf_employee_share >0)
                    <tr>
                        <td style="font-weight: bold;">HDMF</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($payroll->hdmf_employee_share,2)}}</td>
                    </tr>
                    @endif
                    @if($payroll->phic_employee_share >0)
                    <tr>
                        <td style="font-weight: bold;">PHIC</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($payroll->phic_employee_share,2)}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <hr>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Net Taxable Income</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($payroll->net_taxable_income,2)}}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Less: Witholding Tax</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($payroll->withholding_tax,2)}}</td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Net Income After Tax</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($payroll->net_taxable_income-$payroll->withholding_tax,2)}}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Add: Non-Taxable Income</td>
                        {{-- <td style="text-align: right; font-weight: bold;">{{number_format($payroll->nontaxable_benefits_total,2)}}</td> --}}
                        <td style="text-align: right; font-weight: bold;"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">De-minimis</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($payroll->deminimis,2)}}</td>
                    </tr>
                    @if($payroll->other_allowances_basic_pay>0)
                    <tr>
                        <td style="font-weight: bold;">Other Allowances</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($payroll->other_allowances_basic_pay,2)}}</td>
                    </tr>
                    @endif
                    @foreach($payroll->pay_allowances as $allow)
                    <tr>
                        <td style="font-weight: bold;"> - {{$allow->allowance_type->name}}</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($allow->amount,2)}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <hr>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Net Income</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($payroll->net_taxable_income-$payroll->withholding_tax+$payroll->nontaxable_benefits_total,2)}}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Less: Non-Taxable Deductions</td>
                        <td style="text-align: right; font-weight: bold;"></td>
                        {{-- <td style="text-align: right; font-weight: bold;">{{number_format($payroll->nontaxable_deductible_benefits_total,2)}}</td> --}}
                    </tr>
                    {{-- {{dd($payroll->id)}} --}}
                    @foreach($payroll->pay_loan as $loan)
                    <tr>
                        <td style="font-weight: bold;"> - {{$loan->loan_type->loan_name}}</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($loan->amount,2)}}</td>
                    </tr>
                    @endforeach
                    
                </tbody>
            </table>
            <hr>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Net Pay</td>
                        <td style="text-align: right; font-weight: bold;">{{number_format($payroll->netpay,2)}}</td>
                    </tr>
                </tbody>
            </table>

            {{-- <div class="year-to-date-container">
                <p>YEAR TO DATE EARNINGS</p>
                <hr>
            </div>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Gross Income</td>
                        <td style="text-align: right; font-weight: bold;">100 PHP</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Tax Withheld</td>
                        <td style="text-align: right; font-weight: bold;">100 PHP</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Statutories</td>
                        <td style="text-align: right; font-weight: bold;">100 PHP</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Taxable Income</td>
                        <td style="text-align: right; font-weight: bold;">100 PHP</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Non-Taxable Income</td>
                        <td style="text-align: right; font-weight: bold;">100 PHP</td>
                    </tr>
                </tbody>
            </table>

            <div class="leave-container">
                <p>LEAVE BALANCES</p>
                <hr>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Carry Over</th>
                        <th>Earned</th>
                        <th>Used</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">0.00</td>
                        <td style="text-align: center;">0.00</td>
                        <td style="text-align: center;">0.00</td>
                        <td style="text-align: center;">0.00</td>
                        <td style="text-align: center;">0.00</td>
                    </tr>
                </tbody>
            </table> --}}
        </div>
    </div>
</body>
</html>

