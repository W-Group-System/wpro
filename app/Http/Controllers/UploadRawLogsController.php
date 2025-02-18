<?php

namespace App\Http\Controllers;

use App\AttendanceLog;
use App\Employee;
use App\Exports\AttendanceLogs;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Shuchkin\SimpleXLSX;

class UploadRawLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $header = 'upload_raw_logs';

        return view('upload_raw_logs.index', compact('header'));
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
        // dd($request->all());
        $path = $request->file('file')->getRealPath();
        $xlsx = SimpleXLSX::parse($path)->rows();
        // dd($xlsx);
        foreach($xlsx as $key => $row)
        {
            if ($key > 0)
            {
                if ($row[0])
                {
                    $attendance_logs = new AttendanceLog;
                    $attendance_logs->emp_code = $row[0];
                    $attendance_logs->date = date('Y-m-d', strtotime($row[1]));
                    $attendance_logs->datetime = date('Y-m-d H:i:s', strtotime($row[2]));
                    $attendance_logs->type = $row[3];
                    $attendance_logs->location = $row[4];
                    $attendance_logs->ip_address = $row[5];
                    $attendance_logs->save();
                }
            }
        }

        Alert::success('Successfully Uploaded')->persistent('Dismiss');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function export() 
    {
        return Excel::download(new AttendanceLogs, 'attendance_logs_template.xlsx');
    }
}
