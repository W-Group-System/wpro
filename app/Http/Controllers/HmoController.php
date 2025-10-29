<?php

namespace App\Http\Controllers;
use App\Hmo;
use App\HmoAttachment;
use App\Company;
use App\User;
use App\Employee;
use App\Notifications\HmoNotif;
use Illuminate\Http\Request;
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

        if ($request->hasFile('path')) {
            foreach ($request->file('path') as $file) {
                $filePath = $file->store('hmo_files', 'public');

                $attachment = new HmoAttachment;
                $attachment->hmo_id = $new_hmo->id;
                $attachment->path = $filePath;
                $attachment->save();
            }
        }
        
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
            'subject'    => 'Document Submission: Proof of Availment',
            'greeting'   => 'Dear ' . $employee->first_name . ',',
            'body'       => 'Kindly review the document and confirm once received. Should you have any questions or require further assistance, feel free to reach out to us.',
            'thanks'     => 'Thank you for your time and continued trust in our services.',
            'actionText' => 'Click Here',
            'actionURL'  => url('/hmo')
        ];

        $user->notify(new HmoNotif($details));

        Alert::success('Success', 'Email sent successfully to ' . $recipientEmail)
            ->persistent('Dismiss');

        return back();
    }

}
