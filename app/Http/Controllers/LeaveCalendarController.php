<?php

namespace App\Http\Controllers;

use App\LeavePlan;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use stdClass;

class LeaveCalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $header = 'leave_calendar';

        $leave_plans_per_month = LeavePlan::where('department_id', auth()->user()->employee->department_id)
            ->whereYear('date_from', date('Y'))
            ->whereMonth('date_to', date('m'))
            ->get();
        $leave_plan_array = [];
        $leave_plans = LeavePlan::where('department_id', auth()->user()->employee->department_id)->get();
        foreach($leave_plans as $leave_plan)
        {
            $object = new stdClass;
            $object->title = $leave_plan->reason;
            $object->start = date('Y-m-d h:i:s', strtotime($leave_plan->date_from));
            $object->end = date('Y-m-d h:i:s', strtotime($leave_plan->date_to));
            $object->color = '#57B657';
            $object->leave_calendar_id = $leave_plan->id;
            $object->reason = $leave_plan->reason;
            $object->date_from = $leave_plan->date_from;
            $object->date_to = $leave_plan->date_to;
            $leave_plan_array[] = $object;
        }
        
        return view('leave_calendar.leave_calendar', compact('header','leave_plans_per_month','leave_plan_array'));
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
        $leave_plan = new LeavePlan();
        $leave_plan->date_from = $request->date_from;
        $leave_plan->date_to = $request->date_to;
        $leave_plan->reason = $request->reason;
        $leave_plan->user_id = auth()->user()->id;
        $leave_plan->department_id = auth()->user()->employee->department_id;
        $leave_plan->save();

        Alert::success('Successfully Saved')->persistent('Dismiss');
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, $id)
    {
        $leave_plan = LeavePlan::findOrFail($id);
        $leave_plan->date_from = $request->date_from;
        $leave_plan->date_to = $request->date_to;
        $leave_plan->reason = $request->reason;
        $leave_plan->user_id = auth()->user()->id;
        $leave_plan->department_id = auth()->user()->employee->department_id;
        $leave_plan->save();

        Alert::success('Successfully Updated')->persistent('Dismiss');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $leave_plan = LeavePlan::findOrFail($id);
        $leave_plan->delete();

        Alert::success('Successfully Deleted')->persistent('Dismiss');
        return back();
    }
}
