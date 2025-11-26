<!DOCTYPE html>
<html>
<head>
    <title>Employee Reviewed</title>
</head>
<body>
    <p>Hello {{ $employee->creator->name }},</p>

    <p>Your employee record for <b>{{ $employee->first_name .' '. $employee->last_name }}</b> has been reviewed by {{ $reviewer->name }}.</p>

    <p>Review Remarks: {{ $remarks }}</p>

    <p>Status: {{ $employee->status }}</p>
    <p>Action: {{ $actionType }}</p>
    <p>Thank you.</p>
</body>
</html>
