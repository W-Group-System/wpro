<?php

namespace App\Http\Controllers;

use App\Classification;
use App\Department;
use App\Employee;
use App\EmployeeMovement;
use App\Level;
use App\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class KpiController extends Controller
{
    public function probationaryRegularization(Request $request)
    {
        $header = 'kpi';
        $search = $request->input('search');
        $classificationMap = Classification::pluck('name', 'id');
        $departments = Department::pluck('name', 'id');
        $levels = Level::pluck('name', 'id');
        $users = User::pluck('name', 'id');

        $probationaryQuery = Employee::with(['company', 'department', 'classification_info'])
            ->where('status', 'Active')
            ->where('classification', 1)
            ->orderBy('last_name')
            ->orderBy('first_name');

        if ($search) {
            $probationaryQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('employee_number', 'like', '%' . $search . '%')
                    ->orWhereRaw("CONCAT(`first_name`, ' ', `last_name`) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("CONCAT(`last_name`, ', ', `first_name`) LIKE ?", ["%{$search}%"]);
            });
        }

        $probationaryEmployees = $probationaryQuery->paginate(20);

        $regularizationMovements = EmployeeMovement::with(['employee.company', 'employee.department', 'user_info'])
            ->where('old_values', 'like', '%classification%')
            ->where('new_values', 'like', '%classification%')
            ->orderBy('changed_at', 'desc')
            ->get()
            ->filter(function ($movement) {
                $oldValues = json_decode($movement->old_values, true) ?: [];
                $newValues = json_decode($movement->new_values, true) ?: [];

                return (string) ($oldValues['classification'] ?? '') === '1'
                    && (string) ($newValues['classification'] ?? '') === '2';
            })
            ->values();

        $currentMonthRegularizations = $regularizationMovements->filter(function ($movement) {
            return $movement->changed_at && date('Y-m', strtotime($movement->changed_at)) === date('Y-m');
        })->count();

        $missingNopaCount = $regularizationMovements->filter(function ($movement) {
            return empty($movement->nopa_attachment);
        })->count();

        return view('kpi.probationary_regularization', compact(
            'header',
            'search',
            'classificationMap',
            'departments',
            'levels',
            'users',
            'probationaryEmployees',
            'regularizationMovements',
            'currentMonthRegularizations',
            'missingNopaCount'
        ));
    }

    public function activityHistory(Request $request)
    {
        $header = 'kpi';
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $classificationMap = Classification::pluck('name', 'id');
        $departments = Department::pluck('name', 'id');
        $levels = Level::pluck('name', 'id');
        $users = User::pluck('name', 'id');

        $movements = EmployeeMovement::with(['employee.company', 'employee.department', 'user_info'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('employee_number', 'like', '%' . $search . '%')
                        ->orWhereRaw("CONCAT(`first_name`, ' ', `last_name`) LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("CONCAT(`last_name`, ', ', `first_name`) LIKE ?", ["%{$search}%"]);
                });
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('changed_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('changed_at', '<=', $dateTo);
            })
            ->orderBy('changed_at', 'desc')
            ->paginate(25);

        return view('kpi.activity_history', compact(
            'header',
            'search',
            'dateFrom',
            'dateTo',
            'classificationMap',
            'departments',
            'levels',
            'users',
            'movements'
        ));
    }

    public function uploadNopa(Request $request, EmployeeMovement $movement)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $attachment = $request->file('file');
        $name = time() . '_' . $attachment->getClientOriginalName();
        $attachment->move(public_path() . '/nopa_att/', $name);

        $movement->nopa_attachment = '/nopa_att/' . $name;
        $movement->save();

        Alert::success('NOPA file uploaded.')->persistent('Dismiss');
        return back();
    }
}
