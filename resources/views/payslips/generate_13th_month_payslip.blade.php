<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>13th Month Payslip</title>
</head>
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
    }

    @page {
        margin: 8mm;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    td, th {
        padding: 6px;
    }

    .section-title {
        font-weight: bold;
        margin-top: 18px;
    }

    .line {
        border-top: 1px solid #000;
        margin: 6px 0 10px;
    }

    .bordered td,
    .bordered th {
        border: 1px solid #000;
    }

    .text-right {
        text-align: right;
    }

    .text-danger {
        color: #dc3545;
    }

    .signature {
        margin-top: 60px;
        text-align: center;
    }

    .signature .rule {
        border-top: 1px solid #000;
        margin: 0 auto 6px;
        width: 240px;
    }
</style>
<body>
    <div>
        <img src="{{ asset('images/m.png') }}" alt="wgroup" height="90" width="140">
    </div>

    <div class="section-title">EMPLOYEE INFORMATION</div>
    <div class="line"></div>
    <table>
        <tr>
            <td width="50%">Employee Number: {{ $posting->employee_no }}</td>
            <td>Date Hired: {{ $posting->employee ? date('M d, Y', strtotime($posting->employee->original_date_hired)) : '' }}</td>
        </tr>
        <tr>
            <td>Employee Name: {{ $posting->employee_name }}</td>
            <td>Company: {{ $posting->company ? $posting->company->company_code : '' }}</td>
        </tr>
        <tr>
            <td>Department: {{ $posting->department }}</td>
            <td>Account Number: {{ $posting->account_number }}</td>
        </tr>
    </table>

    <div class="section-title">13TH MONTH INFORMATION</div>
    <div class="line"></div>
    <table>
        <tr>
            <td width="50%">Year: {{ $posting->year }}</td>
            <td>Half: {{ $posting->half }} Half</td>
        </tr>
        <tr>
            <td>Basis: {{ $posting->year }}</td>
            <td>Date Posted: {{ date('F d, Y', strtotime($posting->created_at)) }}</td>
        </tr>
    </table>

    <div class="section-title">PAY DETAILS</div>
    <div class="line"></div>
    <table class="bordered">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Monthly Salary</td>
                <td class="text-right">{{ number_format($posting->monthly_salary, 2) }}</td>
            </tr>
            <tr>
                <td>Annual 13th Month</td>
                <td class="text-right">{{ number_format($posting->annual_thirteenth_month, 2) }}</td>
            </tr>
            <tr>
                <td>1st Half Released</td>
                <td class="text-right">{{ number_format($posting->first_half_released, 2) }}</td>
            </tr>
            <tr>
                <th>13th Month {{ $posting->half }} Half Release</th>
                <th class="text-right {{ $posting->release_amount < 0 ? 'text-danger' : '' }}">{{ number_format($posting->release_amount, 2) }}</th>
            </tr>
        </tbody>
    </table>

    <div class="signature">
        <div class="rule"></div>
        <div>Employee Signature</div>
        <div><em>I acknowledge receipt of the amount stated above.</em></div>
    </div>
</body>
</html>
