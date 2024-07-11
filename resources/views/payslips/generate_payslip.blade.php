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
    .page-break 
    {
        page-break-after: always;
    }
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
                        <td>Employee Number:</td>
                        <td>Date Hired:</td>
                    </tr>
                    <tr>
                        <td>Employee Name:</td>
                        <td>SSS ID:</td>
                    </tr>
                    <tr>
                        <td>Department Name:</td>
                        <td>Pagibig ID:</td>
                    </tr>
                    <tr>
                        <td>TIN:</td>
                        <td>PhilHealth ID:</td>
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
                        <td>Payroll Period:</td>
                        <td>Rendered:</td>
                    </tr>
                    <tr>
                        <td>Pay Type:</td>
                        <td>Absent:</td>
                    </tr>
                    <tr>
                        <td>Rate:</td>
                        <td>Leave:</td>
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
                        <td style="text-align: right; font-weight: bold;">100 PHP</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Overtime</td>
                        <td style="text-align: right; font-weight: bold;">100 PHP</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Undertime</td>
                        <td style="text-align: right; font-weight: bold;">50 PHP</td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Gross Income</td>
                        <td style="text-align: right; font-weight: bold;">100 PHP</td>
                    </tr>
                </tbody>
            </table>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Less: Other Deductions</td>
                        <td style="font-weight: bold;">Hours</td>
                        <td style="text-align: right; font-weight: bold;">Amount</td>
                    </tr>
                    <tr>
                        <td>Absences</td>
                        <td>3 Hours</td>
                        <td style="text-align: right;">1000 PHP</td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Gross Taxable Income</td>
                        <td style="text-align: right; font-weight: bold;">100 PHP</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Sample</td>
                        <td style="text-align: right; font-weight: bold;">0.00 PHP</td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Net Taxable Income</td>
                        <td style="text-align: right; font-weight: bold;">100 PHP</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Less: Witholding Tax</td>
                        <td style="text-align: right; font-weight: bold;">0.00 PHP</td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Net Income After Tax</td>
                        <td style="text-align: right; font-weight: bold;">100 PHP</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Add: Non-Taxable Income</td>
                        <td style="text-align: right; font-weight: bold;">0.00 PHP</td>
                    </tr>
                    <tr>
                        <td>Other Allowance</td>
                        <td style="text-align: right; font-weight: bold;">220.00 PHP</td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Net Income</td>
                        <td style="text-align: right; font-weight: bold;">100 PHP</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Less: Non-Taxable Deductions</td>
                        <td style="text-align: right; font-weight: bold;">0.00 PHP</td>
                    </tr>
                    <tr>
                        <td>SSS LOAN</td>
                        <td style="text-align: right; font-weight: bold;">220.00 PHP</td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Net Pay</td>
                        <td style="text-align: right; font-weight: bold;">100 PHP</td>
                    </tr>
                </tbody>
            </table>

            <div class="year-to-date-container">
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
            </table>
        </div>
    </div>
</body>
</html>

