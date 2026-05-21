<!DOCTYPE html>
<html>
<body>
<p>Hi {{ $details['approver_info']['name'] }},</p>

<p>
    This is a reminder that <strong>{{ $details['user_info']['name'] }}</strong> has a pending
    <strong>Vacation Leave</strong> starting on
    <strong>{{ date('F d, Y', strtotime($details['details']['date_from'])) }}</strong>
    ({{ $details['days_before'] }} day(s) from today), but has not yet uploaded a
    <strong>turnover list</strong>.
</p>

<p>
    You will <strong>not be able to approve</strong> this leave until the turnover list is submitted.
    Please remind the employee to upload it at the earliest.
</p>

<p><strong>Leave Details:</strong></p>
<ul>
    <li>Employee: {{ $details['user_info']['name'] }}</li>
    <li>Leave Type: Vacation Leave</li>
    <li>Start Date: {{ date('F d, Y', strtotime($details['details']['date_from'])) }}</li>
    <li>End Date: {{ date('F d, Y', strtotime($details['details']['date_to'])) }}</li>
    <li>Reason: {{ $details['details']['reason'] }}</li>
</ul>

<p>
    <a href="{{ url('/for-leave') }}">Click here to view the leave application</a>
</p>

<p>Thank you,<br>HRIS System</p>
</body>
</html>
