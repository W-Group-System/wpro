<?php

namespace App\Http\Controllers;

use App\Audit;
use Illuminate\Http\Request;

class AuditLogsController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::query();

        if ($request->model) {
            $query->where('auditable_type', $request->model);
        } else {
            $query->whereIn('auditable_type', ['App\Employee', 'App\EmployeeAllowance']);
        }

        if ($request->id) {
            $query->where('auditable_id', $request->id);
        }

        if ($request->range) {

            [$start, $end] = explode('|', $request->range);

            $query->whereBetween('created_at', [
                $start . ' 00:00:00',
                $end . ' 23:59:59'
            ]);
        }

        $audits = $query->latest()->get();

        return view('audits.index', [
            'header' => 'audits',
            'audits' => $audits
        ]);
    }


}
