<?php

namespace App\Http\Controllers;
use App\ExitResign;
use App\Employee;
use App\ExitClearance;
use App\ExitClearanceComment;
use App\Mail\comment;
use App\Mail\Cleared;
use App\ExitClearanceChecklist;
use App\ExitClearanceSignatory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
class ExitClearanceController extends Controller
{
    //

    public function index(Request $request)
    {
        $resigns = ExitResign::with('exit_clearance.signatories')->where('status','Ongoing Clearance')->get();
        // dd($resigns);
        return view('ongoing_clearances',array(
            'resigns'=>$resigns
        ));
    }

    public function view(Request $request,$id)
    {
        $resign = ExitResign::with('exit_clearance.department','exit_clearance.checklists','exit_clearance.signatories')->findOrfail($id);
        // dd($resigns);
        return view('clearances.view_clearance',array(
            'resignEmployee'=>$resign
        ));
    }
    public function forClearance(Request $request)
    {
        $status = $request->status;
        if(empty($status))
        {
            $status = "Pending";
        }
        $for_clearances = ExitClearanceSignatory::with('clearance.department')->where('employee_id',auth()->user()->employee->id)->get();
        // dd($for_clearances);
        return view('clearances.for_clearance',array(
            'for_clearances'=>$for_clearances,
            'status'=>$status,
            'header' => 'For Clearance',
        ));
    }

    public function viewAsSignatory (Request $request,$id)
    {   
        $for_clearances = ExitClearanceSignatory::with('clearance.department')->where('employee_id',auth()->user()->employee->id)->where('id',$id)->first();   
        
        $exitClearanceIds = ExitClearanceSignatory::where('exit_clearance_id', $for_clearances->exit_clearance_id)->pluck('employee_id')->toArray();
        $resign = ExitResign::with('exit_clearance.department','exit_clearance.checklists','exit_clearance.signatories')->findOrfail($for_clearances->clearance->resign_id);
        return view('clearances.view_as_signatory',array(
            'for_clearances'=>$for_clearances,
            'resignEmployee'=>$resign,
            'exitClearanceIds'=>$exitClearanceIds,
            'header'=>"For Clearance",
        ));

    }
    public function viewComments(Request $request,$id)
    {   
        $employeeId = auth()->user()->employee->id;
        $exitClearanceIds = ExitClearanceSignatory::where('exit_clearance_id', $id)->pluck('employee_id')->toArray();
        $exit = ExitClearance::findOrFail($id);

        // Check if the current user is authorized to view the page
        if ($exit->employee_id == $employeeId || in_array($employeeId, $exitClearanceIds) || auth()->user()->clearance_admin == 1) {
            // Retrieve the necessary data once
            $for_clearances = ExitClearanceSignatory::with('clearance.department')
                ->where('exit_clearance_id', $exit->id)
                ->first();

            $resign = ExitResign::with('exit_clearance.department', 'exit_clearance.checklists', 'exit_clearance.signatories')
                ->findOrFail($for_clearances->clearance->resign_id);

            return view('clearances.view_as_signatory', [
                'for_clearances' => $for_clearances,
                'exitClearanceIds' => $exitClearanceIds,
                'resignEmployee' => $resign,
                'header' => 'View Comments',
            ]);
        }

        // If user is not authorized
        Alert::error('You are not allowed to view this page.')->persistent('Dismiss');
        return redirect('home');

    }
    public function viewMyClearance()
    {   
        $employeeId = auth()->user()->employee->id;
        // dd($employeeId); 
        $resignExit = ExitResign::with('exit_clearance.department','exit_clearance.checklists','exit_clearance.signatories')->where('employee_id',auth()->user()->employee->id)->where('status','!=','Retracted')->first();
        
        if($resignExit)
        {
            return view('clearances.view_clearance',array(
                'resignEmployee'=>$resignExit,
                'header' => 'my-clearance',
            ));
        }
        else
        {
            Alert::error('Your clearance is not yet processed, please coordinate with HR.')->persistent('Dismiss');
            return redirect('home');
        }
       

        // If user is not authorized
        // Alert::error('You are not allowed to view this page.')->persistent('Dismiss');
        // return redirect('home');

    }
    public function submitComment(Request $request,$id)
    {
        // dd($request->all());
        $comment = new ExitClearanceComment;
        $remarks = $request->observation."<br> ";
        if($request->file('file')){
            $proof = $request->file('file');
            $original_name = $proof->getClientOriginalName();
            $name = time() . '_' . $proof->getClientOriginalName();
            $proof->move(public_path() . '/files/', $name);
            $file_name = '/files/' . $name;
            $remarks = $remarks."<a class='btn btn-sm btn-success' href='".url($file_name)."'  target='blank'>".$original_name."</a> ";
        }
        $comment->remarks = $remarks;
        $comment->exit_clearance_id = $id;
        $comment->user_id = auth()->user()->id;
        $comment->save();

        $exitClearance = ExitClearance::with('department')->findOrfail($id);
        // dd($exitClearance);
        $exitResign = ExitResign::findOrfail($exitClearance->resign_id);
        $employee = Employee::findOrfail($exitClearance->employee_id);
        $signatories = ExitClearanceSignatory::with('employee')->where('exit_clearance_id',$id)->get();
     
        $cc_emails = [];
        foreach($signatories as $signatory)
        {
            // dd($signatory->employee->user_info->email);
            $cc_emails[] = $signatory->employee->user_info->email;
        }
        $data = [];
        $data['employee_info'] = $exitResign->employee;
        $data['comment'] = $comment;
        $data['department'] = $exitClearance;
        $send_update = Mail::to([$exitResign->personal_email, $employee->user_info->email])->cc($cc_emails)->send(new comment($data));
        Alert::success('Successfully Posted')->persistent('Dismiss');

        return back();
    }

    public function changestatus(Request $request,$id)
    {
        $checklist = ExitClearanceChecklist::findOrfail($id);
        $checklist->status = $request->status;
        $checklist->save();

        $comment = new ExitClearanceComment;
        $remarks = "<span>Checklist: ".$request->checklist." <br>".$request->old_status." &#x2192; ".$request->status."</span> <br> Remarks : ".$request->remarks."<br> ";
        if($request->file('proof')){
            $proof = $request->file('proof');
            $original_name = $proof->getClientOriginalName();
            $name = time() . '_' . $proof->getClientOriginalName();
            $proof->move(public_path() . '/proof/', $name);
            $file_name = '/proof/' . $name;
            $remarks = $remarks."<a class='btn btn-sm btn-success' href='".url($file_name)."'  target='blank'>".$original_name."</a> ";
        }
        $comment->remarks = $remarks;
        $comment->exit_clearance_id = $checklist->exit_clearance_id;
        $comment->user_id = auth()->user()->id;
        $comment->save();


        $exitClearance = ExitClearance::with('department')->findOrfail($checklist->exit_clearance_id);
        // dd($exitClearance);
        $exitResign = ExitResign::findOrfail($exitClearance->resign_id);
        $employee = Employee::findOrfail($exitClearance->employee_id);
        $signatories = ExitClearanceSignatory::with('employee')->where('exit_clearance_id',$exitClearance->id)->get();
     
        $cc_emails = [];
        foreach($signatories as $signatory)
        {
            // dd($signatory->employee->user_info->email);
            $cc_emails[] = $signatory->employee->user_info->email;
        }
        $data = [];
        $data['employee_info'] = $exitResign->employee;
        $data['comment'] = $comment;
        $data['department'] = $exitClearance;
        $send_update = Mail::to([$exitResign->personal_email, $employee->user_info->email])->cc($cc_emails)->send(new comment($data));


        Alert::success('Successfully Change Status')->persistent('Dismiss');

        return back();

    }
    public function cleared(Request $request,$id)
    {
        $exit_signatory = ExitClearanceSignatory::findOrfail($id);
        $signatories = ExitClearanceSignatory::where('exit_clearance_id',$exit_signatory->exit_clearance_id)->where('id','!=',$id)->where('status','Pending')->count();
        if($signatories == 0)
        {
            $exitChecklist = ExitClearanceChecklist::where('status','Pending')->where('exit_clearance_id',$exit_signatory->exit_clearance_id)->count();
            if($exitChecklist > 0 )
            {
                Alert::error('Kindly complete the entire checklist, or mark items as "N/A" if they are not applicable.')->persistent('Dismiss');
                return back();
            }
        }
        $resign_employee = ExitClearance::where('resign_id',$exit_signatory->clearance->resign_id)->pluck('id')->toArray();
 
       
        
        $exit_signatory = ExitClearanceSignatory::findOrfail($id);
        $exit_signatory->status = "Cleared";
        $exit_signatory->save();

        $comment = new ExitClearanceComment;
        $remarks = "<span>Tag ".$request->name." as Cleared <br> Remarks : ".$request->remarks."<br> ";
        $comment->remarks = $remarks;
        $comment->exit_clearance_id = $exit_signatory->exit_clearance_id;
        $comment->user_id = auth()->user()->id;
        $comment->save();

        $all_signatories = ExitClearanceSignatory::whereIn('exit_clearance_id',$resign_employee)->where('status',"Pending")->count();
        if($all_signatories == 0)
        {
            $update = ExitResign::where('id',$exit_signatory->clearance->resign_id)->first();
            $update->status = 'Cleared';
            $update->date_cleared = date('Y-m-d');
            $update->save();

        }

        
        $exitClearance = ExitClearance::with('department')->findOrfail($exit_signatory->exit_clearance_id);
        // dd($exitClearance);
        $exitResign = ExitResign::findOrfail($exitClearance->resign_id);
        $employee = Employee::findOrfail($exitClearance->employee_id);
        $signatories = ExitClearanceSignatory::with('employee')->where('exit_clearance_id',$exitClearance->id)->get();
     
        $cc_emails = [];
        foreach($signatories as $signatory)
        {
            // dd($signatory->employee->user_info->email);
            $cc_emails[] = $signatory->employee->user_info->email;
        }
        $data = [];
        $data['employee_info'] = $exitResign->employee;
        $data['comment'] = $comment;
        $data['department'] = $exitClearance;
        $send_update = Mail::to([$exitResign->personal_email, $employee->user_info->email])->cc($cc_emails)->send(new comment($data));
        if($all_signatories == 0)
        {
            $send_update = Mail::to([$exitResign->personal_email, $employee->user_info->email])->send(new Cleared($data));
        }

        Alert::success('Successfully Change Status')->persistent('Dismiss');

        return back();

    }


    public function clear_index(Request $request)
    {
        $resigns = ExitResign::with('exit_clearance.signatories')->where('status','Cleared')->get();
        // dd($resigns);
        return view('cleared',array(
            'resigns'=>$resigns
        ));
    }
}
