<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ScheduleChangeRequest;
use App\EmployeeApprover;
use App\Employee;
use App\DailySchedule;
use RealRashid\SweetAlert\Facades\Alert;

class ScheduleChangeController extends Controller
{
    public function index()
    {
        $employee = Employee::with('ScheduleData', 'schedule_info')
            ->where('user_id', Auth::user()->id)
            ->first();

        $approvers = EmployeeApprover::with('approver_info')
            ->where('user_id', Auth::user()->id)
            ->orderBy('level')
            ->get();

        $requests = ScheduleChangeRequest::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('forms.schedules.change_schedule', [
            'header'    => 'forms',
            'employee'  => $employee,
            'approvers' => $approvers,
            'requests'  => $requests,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_from'      => 'required|date',
            'date_to'        => 'required|date|after_or_equal:date_from',
            'time_in_from'   => 'required',
            'time_in_to'     => 'required',
            'time_out_from'  => 'required',
            'time_out_to'    => 'required',
            'working_hours'  => 'required|numeric|min:0.5|max:24',
            'reason'         => 'required|string|max:1000',
        ]);

        ScheduleChangeRequest::create([
            'user_id'       => Auth::user()->id,
            'date_from'     => $request->date_from,
            'date_to'       => $request->date_to,
            'time_in_from'  => $request->time_in_from,
            'time_in_to'    => $request->time_in_to,
            'time_out_from' => $request->time_out_from,
            'time_out_to'   => $request->time_out_to,
            'working_hours' => $request->working_hours,
            'reason'        => $request->reason,
            'status'        => 'Pending',
            'level'         => 0,
            'created_by'    => Auth::user()->id,
        ]);

        Alert::success('Schedule change request submitted successfully.')->persistent('Dismiss');
        return back();
    }

    public function forApproval(Request $request)
    {
        $approver_id = Auth::user()->id;
        $status = $request->status ?? 'Pending';
        $from   = $request->from ?? date('Y-m-01');
        $to     = $request->to   ?? date('Y-m-d');

        $query = ScheduleChangeRequest::with('user', 'approver.approver_info', 'approveBy')
            ->whereHas('approver', function ($q) use ($approver_id) {
                $q->where('approver_id', $approver_id);
            })
            ->whereBetween('date_from', [$from, $to]);

        $for_approval = (clone $query)->where('status', 'Pending')->count();
        $approved     = (clone $query)->where('status', 'Approved')->count();
        $declined     = (clone $query)->where('status', 'Declined')->count();

        $schedule_changes = $query->where('status', $status)->orderBy('created_at', 'desc')->get();

        return view('for-approval.schedule-change-approval', [
            'header'           => 'for-approval',
            'schedule_changes' => $schedule_changes,
            'for_approval'     => $for_approval,
            'approved'         => $approved,
            'declined'         => $declined,
            'status'           => $status,
            'from'             => $from,
            'to'               => $to,
        ]);
    }

    public function approve(Request $request, $id)
    {
        $schedule_change = ScheduleChangeRequest::findOrFail($id);
        $approver_id     = Auth::user()->id;

        $employee_approver = EmployeeApprover::where('user_id', $schedule_change->user_id)
            ->where('approver_id', $approver_id)
            ->first();

        if (!$employee_approver) {
            Alert::error('You are not an approver for this request.')->persistent('Dismiss');
            return back();
        }

        if ($schedule_change->level == 0) {
            if ($employee_approver->as_final == 'on') {
                $this->finalApprove($schedule_change, $request->approval_remarks);
            } else {
                $schedule_change->update([
                    'level'            => 1,
                    'approval_remarks' => $request->approval_remarks,
                ]);
            }
        } elseif ($schedule_change->level == 1) {
            $this->finalApprove($schedule_change, $request->approval_remarks);
        }

        Alert::success('Schedule change request approved.')->persistent('Dismiss');
        return back();
    }

    private function finalApprove(ScheduleChangeRequest $scheduleChange, $remarks)
    {
        $scheduleChange->update([
            'status'           => 'Approved',
            'approved_by'      => Auth::user()->id,
            'approved_date'    => now()->toDateString(),
            'approval_remarks' => $remarks,
            'level'            => 2,
        ]);

        // Write approved schedule to daily_schedules for each date in range
        $employee = Employee::with('company_info')->where('user_id', $scheduleChange->user_id)->first();
        if (!$employee) return;

        $dates = dateRange($scheduleChange->date_from, $scheduleChange->date_to);
        foreach ($dates as $date) {
            DailySchedule::updateOrCreate(
                [
                    'employee_code' => $employee->employee_code,
                    'log_date'      => $date,
                ],
                [
                    'company'         => optional($employee->company_info)->company_code ?? '',
                    'employee_number' => $employee->employee_number,
                    'employee_code'   => $employee->employee_code,
                    'employee_name'   => $employee->user->name ?? '',
                    'log_date'        => $date,
                    'time_in_from'    => $scheduleChange->time_in_from,
                    'time_in_to'      => $scheduleChange->time_in_to,
                    'time_out_from'   => $scheduleChange->time_out_from,
                    'time_out_to'     => $scheduleChange->time_out_to,
                    'working_hours'   => $scheduleChange->working_hours,
                    'created_by'      => Auth::user()->id,
                ]
            );
        }
    }

    public function decline(Request $request, $id)
    {
        $schedule_change = ScheduleChangeRequest::findOrFail($id);

        $schedule_change->update([
            'status'           => 'Declined',
            'approved_by'      => Auth::user()->id,
            'approved_date'    => now()->toDateString(),
            'approval_remarks' => $request->approval_remarks,
        ]);

        Alert::success('Schedule change request declined.')->persistent('Dismiss');
        return back();
    }
}
