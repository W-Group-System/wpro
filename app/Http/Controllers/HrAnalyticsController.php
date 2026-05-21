<?php

namespace App\Http\Controllers;

use App\Department;
use App\Employee;
use App\ExitResign;
use Illuminate\Http\Request;

class HrAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from', date('Y-01-01'));
        $to = $request->get('to', date('Y-m-d'));
        $departmentId = $request->get('department_id');
        $activeStatuses = ['Active', 'HBU'];

        if (strtotime($from) === false) {
            $from = date('Y-01-01');
        }

        if (strtotime($to) === false) {
            $to = date('Y-m-d');
        }

        if (strtotime($from) > strtotime($to)) {
            $temporaryFrom = $from;
            $from = $to;
            $to = $temporaryFrom;
        }

        $resignedQuery = ExitResign::with(
            'employee:id,employee_code,employee_number,first_name,last_name,avatar,status,original_date_hired,position,department_id,company_id',
            'employee.department:id,name,code',
            'employee.company:id,company_code,company_name',
            'department:id,name,code',
            'company:id,company_code,company_name'
        )
            ->whereNotNull('last_date')
            ->whereBetween('last_date', [$from, $to])
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'Retracted');
            });

        if ($departmentId) {
            $resignedQuery->where('department_id', $departmentId);
        }

        $resignedRecords = $resignedQuery
            ->orderBy('last_date', 'desc')
            ->get();

        $departments = Department::orderBy('name')->get();
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::whereIn('status', $activeStatuses)->count();
        $totalResignations = $resignedRecords->count();
        $turnoverRate = $activeEmployees > 0 ? round(($totalResignations / $activeEmployees) * 100, 1) : 0;

        $resignationsByDepartment = $resignedRecords->groupBy('department_id');
        $departmentAnalytics = $departments->map(function ($department) use ($resignationsByDepartment, $activeStatuses) {
            $resignations = $resignationsByDepartment->get($department->id, collect());
            $activeHeadcount = Employee::where('department_id', $department->id)
                ->whereIn('status', $activeStatuses)
                ->count();
            $departmentPopulation = $activeHeadcount + $resignations->count();

            return (object) [
                'department_id' => $department->id,
                'department_name' => $department->name ?: 'No Department',
                'department_code' => $department->code,
                'resignations' => $resignations->count(),
                'active_headcount' => $activeHeadcount,
                'turnover_rate' => $departmentPopulation > 0 ? round(($resignations->count() / $departmentPopulation) * 100, 1) : 0,
                'latest_resignation' => $resignations->max('last_date'),
            ];
        })->filter(function ($item) {
            return $item->resignations > 0 || $item->active_headcount > 0;
        })->sortByDesc('resignations')->values();

        if ($resignationsByDepartment->has('')) {
            $noDepartmentResignations = $resignationsByDepartment->get('');
            $departmentAnalytics->push((object) [
                'department_id' => null,
                'department_name' => 'No Department',
                'department_code' => null,
                'resignations' => $noDepartmentResignations->count(),
                'active_headcount' => Employee::whereNull('department_id')->whereIn('status', $activeStatuses)->count(),
                'turnover_rate' => 0,
                'latest_resignation' => $noDepartmentResignations->max('last_date'),
            ]);
        }

        $topDepartment = $departmentAnalytics->sortByDesc('resignations')->first();
        $highestRateDepartment = $departmentAnalytics->where('resignations', '>', 0)->sortByDesc('turnover_rate')->first();

        $monthlyTrend = $resignedRecords
            ->groupBy(function ($resign) {
                return date('Y-m', strtotime($resign->last_date));
            })
            ->map(function ($items, $month) {
                return (object) [
                    'month' => $month,
                    'label' => date('M Y', strtotime($month . '-01')),
                    'total' => $items->count(),
                ];
            })
            ->sortBy('month')
            ->values();

        $reasonBreakdown = $resignedRecords
            ->groupBy(function ($resign) {
                return $resign->reason_for_separation ?: 'No Reason Encoded';
            })
            ->map(function ($items, $reason) {
                return (object) [
                    'reason' => $reason,
                    'total' => $items->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        $recentResignations = $resignedRecords->take(10);

        return view('hr_analytics.index', [
            'header' => 'hr-analytics',
            'from' => $from,
            'to' => $to,
            'departmentId' => $departmentId,
            'departments' => $departments,
            'totalEmployees' => $totalEmployees,
            'activeEmployees' => $activeEmployees,
            'totalResignations' => $totalResignations,
            'turnoverRate' => $turnoverRate,
            'departmentAnalytics' => $departmentAnalytics,
            'topDepartment' => $topDepartment,
            'highestRateDepartment' => $highestRateDepartment,
            'monthlyTrend' => $monthlyTrend,
            'reasonBreakdown' => $reasonBreakdown,
            'recentResignations' => $recentResignations,
            'resignedRecords' => $resignedRecords,
        ]);
    }
}
