<?php

namespace App\Http\Controllers;
use App\Hmo;
use App\HmoAttachment;
use App\Company;
use App\User;
use App\Employee;
use App\Notifications\HmoHrNotif;
use App\Notifications\HmoNotif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use RealRashid\SweetAlert\Facades\Alert;

class HmoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $availments = Hmo::where('employee_name', auth()->user()->name)
                    ->orderBy('created_at','asc')
                    ->get();

        // dd();
        return view('hmo.index',
        array(
            'header' => 'forms',
            'availments' => $availments,
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $new_hmo = Hmo::create([
            'employee_name' => $request->employee_name,
            'email'         => $request->email,
            'company'       => $request->company,
            'department'    => $request->department,
            'date_availment'=> $request->date_availment,
            'user_id'       => auth()->user()->employee->user_id
        ]);

        // Store attachments
        $attachments = [];
        if ($request->hasFile('path')) {
            foreach ($request->file('path') as $file) {
                $filePath = $file->store('hmo_files', 'public');

                HmoAttachment::create([
                    'hmo_id' => $new_hmo->id,
                    'path'   => $filePath,
                ]);

                $attachments[] = $filePath;
            }
        }

            $detailsHr = [
                'subject'    => 'Document Submission: HMO Proof of Availment',
                'greeting'   => 'Dear HR Team,',
                'body'       => 'Please review the uploaded proof of availment.<br><br>Clicking the <a href="' . url('/hmo-report') . '">View</a> button will direct you to W Pro to view the details.',
                'thanks'     => 'If you have any questions or require further assistance, feel free to reach out to us.',
                // 'actionText' => 'Click Here',
                // 'actionURL'  => url('/hmo-report'),
            ];

        // $hrEmails = [
        //     'reyzie.repia@rico.com.ph',
        //     'julie.reamillo@rico.com.ph',
        //     'hr.generalist@rico.com.ph', 
        // ];

        $hrEmails = [
            'mark.bautista@wgroup.space', 
        ];

        Notification::route('mail', $hrEmails)->notify(new HmoHrNotif($detailsHr, $attachments));

        Alert::success('Successfully Stored')->persistent('Dismiss');
        return back();

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $availment = Hmo::with('attachments')->findOrFail($id);

        foreach ($availment->attachments as $attachment) {
            $attachment->delete();
        }

        $availment->delete();

        return response()->json(['success' => true]);
    }

    public function report(Request $request)
    {
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);
        $companies = Company::whereHas('employee_has_company')
                                ->whereIn('id',$allowed_companies)
                                ->get();
        // dd($companies);
        
        $company = isset($request->company) ? $request->company : [];
        $from = isset($request->from) ? $request->from : "";
        $to =  isset($request->to) ? $request->to : "";
        $employee_hmo = [];
        // $employee_hmo = Hmo::get();
        // dd($employee_hmo);
        if(isset($request->from) && isset($request->to)){
            $employee_hmo = Hmo::with('attachments', 'employee')
                                        ->whereDate('created_at','>=', $from)
                                        ->whereDate('created_at','<=', $to)
                                        ->whereHas('employee',function($q) use($company){
                                            $q->whereIn('company_id',$company);
                                        })
                                        ->get();
           
        };
        return view('reports.hmo_report', array(
            'header' => 'reports',
            'company'=>$company,
            'from'=>$from,
            'to'=>$to,
            'companies' => $companies,
            'employee_hmo' => $employee_hmo
        ));
    }

    public function email($id)
    {
        $employee = Employee::with('user')->findOrFail($id);

        $recipientEmail = $employee->email ?? ($employee->user->email ?? null);
        if (!$recipientEmail) {
            Alert::error('Error', 'No email address found for this employee.')->persistent('Dismiss');
            return back();
        }

        $user = User::where('email', $recipientEmail)->first();
        if (!$user) {
            Alert::error('Error', 'User not found for this employee email.')->persistent('Dismiss');
            return back();
        }

        $details = [
            'subject'    => 'Document Submission: HMO Proof of Availment',
            'greeting'   => 'Hi ' . $employee->first_name . ',',
            'body'       => 'We are reviewing our HMO billiing and have identified an availment on date. Please provide documentation to confirm the use as part of our validation. Acceptable documents include any of the following: LOA (Letter of Authorization), hospital/clinic appointment slip or referral form, availment slip, or similar documents.<br><br>If you click the <a href="' . url('/hmo') . '">Submit</a> button, you will be directed to W Pro to attach the required documents',
            'thanks'     => 'If you have any questions or concerns, please contact the HR Department.',
            // 'actionText' => 'Click Here',
            // 'actionURL'  => url('/hmo')
        ];

        $user->notify(new HmoNotif($details));

        Alert::success('Success', 'Email sent successfully to ' . $recipientEmail)
            ->persistent('Dismiss');

        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date_availment' => 'required|date',
            'path.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $hmo = Hmo::findOrFail($id);

        $hmo->update([
            'date_availment' => $request->date_availment,
        ]);

        if ($request->hasFile('path')) {
            foreach ($hmo->attachments as $attachment) {
                $attachment->delete(); 
            }

            foreach ($request->file('path') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/hmo_proofs', $filename);

                HmoAttachment::create([
                    'hmo_id' => $hmo->id,
                    'path'   => 'hmo_proofs/' . $filename,
                ]);
            }
        }

        Alert::success('Proof(s) of availment updated successfully!')->persistent('Dismiss');
        return back();
    }

}
