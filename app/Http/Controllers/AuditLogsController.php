<?php

namespace App\Http\Controllers;

use App\Audit;
use Illuminate\Http\Request;

class AuditLogsController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::where('auditable_type', 'App\Employee', 'App\EmployeeAllowance');

        // if ($request->range) {

        //     [$start, $end] = explode('|', $request->range);

        //     $query->whereBetween('created_at', [
        //         $start . ' 00:00:00',
        //         $end . ' 23:59:59'
        //     ]);
        // }

        $audits = $query->whereBetween('created_at', ['2025-10-11 00:00:00', '2025-11-25 23:59:59'])->get();

        return view('audits.index', [
            'header' => 'audits',
            'audits' => $audits
        ]);
    }


}
