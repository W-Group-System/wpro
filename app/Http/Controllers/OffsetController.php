<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\OffsetRequest;
use App\EmployeeApprover;
use App\EmployeeOvertime;
use App\Employee;
use RealRashid\SweetAlert\Facades\Alert;

class OffsetController extends Controller
{
    public function index()
    {
        $approvers = EmployeeApprover::with('approver_info')
            ->where('user_id', Auth::user()->id)
            ->orderBy('level')
            ->get();

        // Show employee's approved OT records as reference
        $overtimes = EmployeeOvertime::where('user_id', Auth::user()->id)
            ->where('status', 'Approved')
            ->orderBy('ot_date', 'desc')
            ->get();

        $requests = OffsetRequest::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('forms.offset.file_offset', [
            'header'    => 'forms',
            'approvers' => $approvers,
            'overtimes' => $overtimes,
            'requests'  => $requests,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ot_date'     => 'required|date',
            'ot_hours'    => 'required|numeric|min:0.5|max:24',
            'date_to_use' => 'required|date|after_or_equal:today',
            'reason'      => 'required|string|max:1000',
            'attachment'  => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $name = time() . '_offset_' . $file->getClientOriginalName();
            $file->move(public_path('storage/offsets'), $name);
            $attachmentPath = 'storage/offsets/' . $name;
        }

        OffsetRequest::create([
            'user_id'     => Auth::user()->id,
            'ot_date'     => $request->ot_date,
            'ot_hours'    => $request->ot_hours,
            'date_to_use' => $request->date_to_use,
            'reason'      => $request->reason,
            'attachment'  => $attachmentPath,
            'status'      => 'Pending',
            'level'       => 0,
            'created_by'  => Auth::user()->id,
        ]);

        Alert::success('Offset request submitted successfully.')->persistent('Dismiss');
        return back();
    }

    public function forApproval(Request $request)
    {
        $approver_id = Auth::user()->id;
        $status = $request->status ?? 'Pending';
        $from   = $request->from ?? date('Y-m-01');
        $to     = $request->to   ?? date('Y-m-d');

        $query = OffsetRequest::with('user', 'approver.approver_info', 'approveBy')
            ->whereHas('approver', function ($q) use ($approver_id) {
                $q->where('approver_id', $approver_id);
            })
            ->whereBetween('date_to_use', [$from, $to]);

        $for_approval = (clone $query)->where('status', 'Pending')->count();
        $approved     = (clone $query)->where('status', 'Approved')->count();
        $declined     = (clone $query)->where('status', 'Declined')->count();

        $offset_requests = $query->where('status', $status)->orderBy('created_at', 'desc')->get();

        return view('for-approval.offset-approval', [
            'header'          => 'for-approval',
            'offset_requests' => $offset_requests,
            'for_approval'    => $for_approval,
            'approved'        => $approved,
            'declined'        => $declined,
            'status'          => $status,
            'from'            => $from,
            'to'              => $to,
        ]);
    }

    public function approve(Request $request, $id)
    {
        $offset      = OffsetRequest::findOrFail($id);
        $approver_id = Auth::user()->id;

        $employee_approver = EmployeeApprover::where('user_id', $offset->user_id)
            ->where('approver_id', $approver_id)
            ->first();

        if (!$employee_approver) {
            Alert::error('You are not an approver for this request.')->persistent('Dismiss');
            return back();
        }

        if ($offset->level == 0) {
            if ($employee_approver->as_final == 'on') {
                $this->finalApprove($offset, $request->approval_remarks);
            } else {
                $offset->update([
                    'level'            => 1,
                    'approval_remarks' => $request->approval_remarks,
                ]);
            }
        } elseif ($offset->level == 1) {
            $this->finalApprove($offset, $request->approval_remarks);
        }

        Alert::success('Offset request approved.')->persistent('Dismiss');
        return back();
    }

    private function finalApprove(OffsetRequest $offset, $remarks)
    {
        $offset->update([
            'status'           => 'Approved',
            'approved_by'      => Auth::user()->id,
            'approved_date'    => now()->toDateString(),
            'approval_remarks' => $remarks,
            'level'            => 2,
        ]);
    }

    public function decline(Request $request, $id)
    {
        $offset = OffsetRequest::findOrFail($id);

        $offset->update([
            'status'           => 'Declined',
            'approved_by'      => Auth::user()->id,
            'approved_date'    => now()->toDateString(),
            'approval_remarks' => $request->approval_remarks,
        ]);

        Alert::success('Offset request declined.')->persistent('Dismiss');
        return back();
    }
}
