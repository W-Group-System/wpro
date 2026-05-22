<?php

namespace App\Http\Controllers;
use App\Http\Controllers\AttendanceController;
use Illuminate\Http\Request;
use App\Handbook;
use App\Employee;
use App\Announcement;
use App\ScheduleData;
use App\Holiday;
use App\EmployeeLeave;
use App\EmployeeOvertime;
use App\EmployeeWfh;
use App\EmployeeOb;
use App\EmployeeDtr;
use App\EmployeeLeaveCredit;
use App\EmployeeLeaveList;
use App\DailySchedule;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $user = auth()->user();
        $employee = $user->employee;
        $schedules = [];
        $attendance_controller = new AttendanceController;
        $current_day = date('d');
        $employee_birthday_celebrants = Employee::select('id', 'first_name', 'last_name', 'avatar', 'birth_date', 'company_id')
        ->with('company:id,company_code')
        ->whereMonth('birth_date', date('m'))
        ->where(function($query) {
            $query->where('status', 'Active')
                  ->orWhere('status', 'HBU');
        })
        ->orderByRaw("DAY(birth_date) >= ? DESC, DAY(birth_date)", [$current_day])
        ->limit(12)
        ->get();
        
        $employees_new_hire = Employee::select('id', 'first_name', 'last_name', 'avatar', 'original_date_hired', 'position', 'company_id', 'department_id')
            ->with('company:id,company_code', 'department:id,name')
            ->where('original_date_hired',">=",date("Y-m-d", strtotime("-1 months")))
            ->orderBy('original_date_hired','desc')
            ->where('status', '!=', 'Declined')
            ->limit(8)
            ->get();
        $sevendays = date('Y-m-d',strtotime("-7 days"));
        if($employee){
            if($employee->employee_number){
                $attendance_now = $attendance_controller->get_attendance_now($employee->employee_number);
                $attendances = $attendance_controller->get_attendances($sevendays,date('Y-m-d',strtotime("-1 day")),$employee->employee_number);
            }else{
                $attendance_now = null;
                $attendances = null;
            }

            $schedules = ScheduleData::where('schedule_id',$employee->schedule_id)->get();
        }else{
            $attendance_now = null;
            $attendances = null;
        }
        // dd($attendances->unique('time_in','Y-m-d'));
        $date_ranges = $attendance_controller->dateRange($sevendays,date('Y-m-d',strtotime("-1 day")));
        $personal_leave_balances = collect();
        $personal_pending_requests = [
            'Leaves' => 0,
            'Overtime' => 0,
            'Official Business' => 0,
            'Work From Home' => 0,
            'DTR Corrections' => 0,
        ];
        $personal_attendance_summary = [
            'days_present' => 0,
            'completed_logs' => 0,
            'missing_days' => count($date_ranges),
            'today_status' => $attendance_now ? ($attendance_now->time_out ? 'Completed' : 'Timed In') : 'No Time In',
        ];
        $personal_next_requests = collect();

        if ($employee) {
            $personal_leave_balances = $this->subordinateLeaveBalances(collect([$employee]))->get($employee->user_id, ['vl' => 0, 'sl' => 0]);

            $attendance_dates = $attendances ? $attendances->filter(function($attendance) {
                return $attendance->time_in;
            })->map(function($attendance) {
                return date('Y-m-d', strtotime($attendance->time_in));
            })->unique() : collect();

            $personal_attendance_summary['days_present'] = $attendance_dates->count();
            $personal_attendance_summary['completed_logs'] = $attendances ? $attendances->filter(function($attendance) {
                return $attendance->time_in && $attendance->time_out;
            })->count() : 0;
            $personal_attendance_summary['missing_days'] = max(count($date_ranges) - $personal_attendance_summary['days_present'], 0);

            $personal_pending_requests = [
                'Leaves' => EmployeeLeave::where('user_id', $employee->user_id)->where('status', 'Pending')->count(),
                'Overtime' => EmployeeOvertime::where('user_id', $employee->user_id)->where('status', 'Pending')->count(),
                'Official Business' => EmployeeOb::where('user_id', $employee->user_id)->where('status', 'Pending')->count(),
                'Work From Home' => EmployeeWfh::where('user_id', $employee->user_id)->where('status', 'Pending')->count(),
                'DTR Corrections' => EmployeeDtr::where('user_id', $employee->user_id)->where('status', 'Pending')->count(),
            ];

            $personal_next_requests = collect([
                EmployeeLeave::with('leave:id,leave_type')
                    ->where('user_id', $employee->user_id)
                    ->whereIn('status', ['Approved', 'Pending'])
                    ->whereDate('date_from', '>=', date('Y-m-d'))
                    ->orderBy('date_from')
                    ->first(),
                EmployeeOvertime::where('user_id', $employee->user_id)
                    ->whereIn('status', ['Approved', 'Pending'])
                    ->whereDate('ot_date', '>=', date('Y-m-d'))
                    ->orderBy('ot_date')
                    ->first(),
                EmployeeOb::where('user_id', $employee->user_id)
                    ->whereIn('status', ['Approved', 'Pending'])
                    ->whereDate('applied_date', '>=', date('Y-m-d'))
                    ->orderBy('applied_date')
                    ->first(),
                EmployeeWfh::where('user_id', $employee->user_id)
                    ->whereIn('status', ['Approved', 'Pending'])
                    ->whereDate('applied_date', '>=', date('Y-m-d'))
                    ->orderBy('applied_date')
                    ->first(),
                EmployeeDtr::where('user_id', $employee->user_id)
                    ->whereIn('status', ['Approved', 'Pending'])
                    ->whereDate('dtr_date', '>=', date('Y-m-d'))
                    ->orderBy('dtr_date')
                    ->first(),
            ])->filter()->sortBy(function($request) {
                if ($request instanceof EmployeeLeave) {
                    return $request->date_from;
                }

                if ($request instanceof EmployeeOvertime) {
                    return $request->ot_date;
                }

                if ($request instanceof EmployeeDtr) {
                    return $request->dtr_date;
                }

                return $request->applied_date;
            })->take(3)->values();
        }
        $handbook = null;
        $employees_under = $user->subbordinates()
            ->select('id', 'user_id', 'employee_number', 'employee_code', 'first_name', 'last_name', 'original_date_hired', 'schedule_id')
            ->with([
                'ScheduleData:id,schedule_id,name',
                'approved_leaves_with_pay' => function ($query) {
                    $query->select('id', 'user_id', 'date_from')
                        ->whereDate('date_from', date('Y-m-d'));
                },
            ])
            ->get();
        // dd(auth()->user()->employee);
        $attendance_employees = $employees_under->isNotEmpty()
            ? $attendance_controller->get_attendances_employees(date('Y-m-d'),date('Y-m-d'),$employees_under->pluck('employee_number')->toArray())
            : collect();
        $attendance_employees_by_code = $attendance_employees->where('time_in','!=',null)->keyBy('employee_code');
        $subordinate_leave_balances = $employees_under->isNotEmpty() ? $this->subordinateLeaveBalances($employees_under) : collect();
        // dd($attendance_employees);
        $announcements = collect();
        

        $holidays = Holiday::select('id', 'holiday_name', 'location', 'holiday_date')
        ->where(function ($query) {
            $query->where(function ($q) {
                $q->where('status','Permanent')
                    ->whereMonth('holiday_date',date('m'));
            })->orWhere(function ($q) {
                $q->where('status',null)
                    ->whereYear('holiday_date', '=', date('Y'))
                    ->whereMonth('holiday_date',date('m'));
            });
        })
        ->orderBy('holiday_date','asc')->get();

        $employee_anniversaries = Employee::select('id', 'first_name', 'last_name', 'avatar', 'original_date_hired', 'company_id', 'department_id')
          ->with('department:id,name', 'company:id,company_code')
          ->where(function($query) {
            $query->where('status', 'Active');
        })
          ->whereYear('original_date_hired','!=',date('Y'))
          ->whereMonth('original_date_hired', date('m'))
          ->limit(8)
          ->get();

        $calendarStart = date('Y-m-01');
        $calendarEnd = date('Y-m-t');
        $calendarEvents = collect();

        foreach ($holidays as $holiday) {
            $calendarEvents->push((object) [
                'date' => date('Y-m-d', strtotime($holiday->holiday_date)),
                'type' => 'holiday',
                'label' => 'Holiday',
                'title' => $holiday->holiday_name,
            ]);
        }

        foreach ($employee_birthday_celebrants as $celebrant) {
            $birthdayDate = date('Y') . '-' . date('m-d', strtotime($celebrant->birth_date));
            if ($birthdayDate >= $calendarStart && $birthdayDate <= $calendarEnd) {
                $calendarEvents->push((object) [
                    'date' => $birthdayDate,
                    'type' => 'birthday',
                    'label' => 'Birthday',
                    'title' => $celebrant->first_name . ' ' . $celebrant->last_name,
                ]);
            }
        }

        foreach ($employee_anniversaries as $anniversary) {
            $anniversaryDate = date('Y') . '-' . date('m-d', strtotime($anniversary->original_date_hired));
            if ($anniversaryDate >= $calendarStart && $anniversaryDate <= $calendarEnd) {
                $calendarEvents->push((object) [
                    'date' => $anniversaryDate,
                    'type' => 'anniversary',
                    'label' => 'Anniversary',
                    'title' => $anniversary->first_name . ' ' . $anniversary->last_name,
                ]);
            }
        }

        foreach ($employees_new_hire as $newHire) {
            if ($newHire->original_date_hired >= $calendarStart && $newHire->original_date_hired <= $calendarEnd) {
                $calendarEvents->push((object) [
                    'date' => date('Y-m-d', strtotime($newHire->original_date_hired)),
                    'type' => 'new-hire',
                    'label' => 'New Hire',
                    'title' => $newHire->first_name . ' ' . $newHire->last_name,
                ]);
            }
        }

        foreach ($personal_next_requests as $request) {
            if ($request instanceof EmployeeLeave) {
                $requestType = $request->leave ? $request->leave->leave_type : 'Leave';
                $requestDate = $request->date_from;
            } elseif ($request instanceof EmployeeOvertime) {
                $requestType = 'Overtime';
                $requestDate = $request->ot_date;
            } elseif ($request instanceof EmployeeDtr) {
                $requestType = 'DTR Correction';
                $requestDate = $request->dtr_date;
            } else {
                $requestType = $request instanceof EmployeeOb ? 'Official Business' : 'Work From Home';
                $requestDate = $request->applied_date;
            }

            if ($requestDate >= $calendarStart && $requestDate <= $calendarEnd) {
                $calendarEvents->push((object) [
                    'date' => date('Y-m-d', strtotime($requestDate)),
                    'type' => 'request',
                    'label' => $request->status,
                    'title' => $requestType,
                ]);
            }
        }

        $calendarDays = collect();
        $calendarCursor = date('Y-m-d', strtotime($calendarStart . ' -' . (date('w', strtotime($calendarStart))) . ' days'));
        $calendarLast = date('Y-m-d', strtotime($calendarEnd . ' +' . (6 - date('w', strtotime($calendarEnd))) . ' days'));
        $calendarEventsByDate = $calendarEvents->sortBy('title')->groupBy('date');

        while ($calendarCursor <= $calendarLast) {
            $dayEvents = $calendarEventsByDate->get($calendarCursor, collect())->values();

            $calendarDays->push((object) [
                'date' => $calendarCursor,
                'day' => date('j', strtotime($calendarCursor)),
                'is_current_month' => date('m', strtotime($calendarCursor)) == date('m'),
                'is_today' => $calendarCursor == date('Y-m-d'),
                'events' => $dayEvents->take(3)->values(),
                'all_events' => $dayEvents,
                'event_count' => $dayEvents->count(),
                'extra_events' => max($dayEvents->count() - 3, 0),
            ]);

            $calendarCursor = date('Y-m-d', strtotime($calendarCursor . ' +1 day'));
        }

        return view('dashboards.home',
        array(
            'header' => '',
            'date_ranges' => $date_ranges,
            'handbook' => $handbook,
            'attendance_now' => $attendance_now,
            'attendances' => $attendances,
            'schedules' => $schedules,
            'announcements' => $announcements ,
            'attendance_employees' => $attendance_employees ,
            'attendance_employees_by_code' => $attendance_employees_by_code,
            'holidays' => $holidays ,
            'employee_birthday_celebrants' => $employee_birthday_celebrants ,
            'employees_new_hire' => $employees_new_hire ,
            'employee_anniversaries' => $employee_anniversaries,
            'employees_under' => $employees_under,
            'subordinate_leave_balances' => $subordinate_leave_balances,
            'personal_leave_balances' => $personal_leave_balances,
            'personal_pending_requests' => $personal_pending_requests,
            'personal_attendance_summary' => $personal_attendance_summary,
            'personal_next_requests' => $personal_next_requests,
            'calendarDays' => $calendarDays,
            'calendarEvents' => $calendarEvents,
        ));
    }

    private function subordinateLeaveBalances($employees)
    {
        if ($employees->isEmpty()) {
            return collect();
        }

        $userIds = $employees->pluck('user_id')->filter()->values();
        $employeeCodes = $employees->pluck('employee_code')->filter()->values();

        $credits = EmployeeLeaveList::select('user_id', 'leave_id', DB::raw('SUM(earned_per_month) as total'))
            ->whereIn('user_id', $userIds)
            ->whereIn('leave_id', [1, 2])
            ->groupBy('user_id', 'leave_id')
            ->get()
            ->groupBy('user_id');

        $leaves = EmployeeLeave::select('id', 'user_id', 'leave_type', 'date_from', 'date_to', 'halfday')
            ->whereIn('user_id', $userIds)
            ->whereIn('leave_type', [1, 2])
            ->whereIn('status', ['Approved', 'Pending'])
            ->where('withpay', 1)
            ->where(function($q) {
                $q->whereYear('date_from', date('Y'))
                    ->orWhereYear('date_from', date('Y', strtotime('+1 year')));
            })
            ->whereYear('created_at', date('Y'))
            ->whereNull('is_previous_year')
            ->get();

        $dailySchedules = DailySchedule::select('employee_code', 'log_date')
            ->whereIn('employee_code', $employeeCodes)
            ->whereNotNull('working_hours')
            ->where(function ($query) {
                $query->whereYear('log_date', date('Y'))
                    ->orWhereYear('log_date', date('Y', strtotime('+1 year')));
            })
            ->get()
            ->groupBy('employee_code');

        $balances = collect();

        foreach ($employees as $employee) {
            $leaveTallies = getEmployeeLeaveCreditTallies($employee)->keyBy('leave_id');

            $balances[$employee->user_id] = [
                'vl' => optional($leaveTallies->get(1))->balance ?: 0,
                'sl' => optional($leaveTallies->get(2))->balance ?: 0,
            ];
        }

        return $balances;
    }

    private function usedLeaveDays($employee, $leaves, $dailyDates)
    {
        $count = 0;
        $workingDays = $employee->ScheduleData->pluck('name')->toArray();

        foreach ($leaves as $leave) {
            if ($leave->halfday == 1) {
                $count += 0.5;
                continue;
            }

            foreach (dateRangeHelperLeaveCount($leave->date_from, $leave->date_to) as $date) {
                $leaveDate = date('Y-m-d', strtotime($date));
                if ($dailyDates->has($leaveDate) || in_array(date('l', strtotime($leaveDate)), $workingDays)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    public function managerDashboard()
    {

        
        $handbook = Handbook::orderBy('id','desc')->first();
        return view('dashboards.dashboard_manager',
        array(
            'header' => 'dashboard-manager',
            'handbook' => $handbook,
        ));
    }

    public function hrDashboard()
    {
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');
        $activeStatuses = ['Active', 'HBU'];

        $totalEmployees = Employee::count();
        $activeEmployees = Employee::whereIn('status', $activeStatuses)->count();
        $inactiveEmployees = Employee::whereNotIn('status', $activeStatuses)->count();
        $newHiresThisMonth = Employee::whereBetween('original_date_hired', [$monthStart, $monthEnd])
            ->where('status', '!=', 'Declined')
            ->count();
        $resignedThisMonth = Employee::whereBetween('date_resigned', [$monthStart, $monthEnd])->count();
        $probationaryCount = Employee::where('classification', '1')
            ->whereIn('status', $activeStatuses)
            ->count();

        $statusBreakdown = Employee::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('total', 'desc')
            ->get();

        $genderBreakdown = Employee::select('gender', DB::raw('count(*) as total'))
            ->whereIn('status', $activeStatuses)
            ->groupBy('gender')
            ->orderBy('total', 'desc')
            ->get();

        $companyHeadcount = Employee::select('company_id', DB::raw('count(*) as total'))
            ->with('company:id,company_name,company_code')
            ->whereIn('status', $activeStatuses)
            ->groupBy('company_id')
            ->orderBy('total', 'desc')
            ->limit(8)
            ->get();

        $departmentHeadcount = Employee::select('department_id', DB::raw('count(*) as total'))
            ->with('department:id,name')
            ->whereIn('status', $activeStatuses)
            ->groupBy('department_id')
            ->orderBy('total', 'desc')
            ->limit(8)
            ->get();

        $classificationHeadcount = Employee::select('classification', DB::raw('count(*) as total'))
            ->with('classification_info:id,name')
            ->whereIn('status', $activeStatuses)
            ->groupBy('classification')
            ->orderBy('total', 'desc')
            ->get();

        $pendingApprovals = [
            'Leaves' => EmployeeLeave::where('status', 'Pending')->count(),
            'Overtime' => EmployeeOvertime::where('status', 'Pending')->count(),
            'Official Business' => EmployeeOb::where('status', 'Pending')->count(),
            'Work From Home' => EmployeeWfh::where('status', 'Pending')->count(),
            'DTR Corrections' => EmployeeDtr::where('status', 'Pending')->count(),
        ];

        $leaveStatusThisMonth = EmployeeLeave::select('status', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$monthStart.' 00:00:00', $monthEnd.' 23:59:59'])
            ->groupBy('status')
            ->orderBy('total', 'desc')
            ->get();

        $leaveTypeThisMonth = EmployeeLeave::select('leave_type', DB::raw('count(*) as total'))
            ->with('leave:id,leave_type')
            ->whereBetween('created_at', [$monthStart.' 00:00:00', $monthEnd.' 23:59:59'])
            ->groupBy('leave_type')
            ->orderBy('total', 'desc')
            ->limit(8)
            ->get();

        $birthdays = Employee::select('id', 'first_name', 'last_name', 'avatar', 'birth_date', 'company_id')
            ->with('company:id,company_code')
            ->whereIn('status', $activeStatuses)
            ->whereMonth('birth_date', date('m'))
            ->orderByRaw("DAY(birth_date) >= ? DESC, DAY(birth_date)", [date('d')])
            ->limit(10)
            ->get();

        $anniversaries = Employee::select('id', 'first_name', 'last_name', 'avatar', 'original_date_hired', 'company_id', 'department_id')
            ->with('company:id,company_code', 'department:id,name')
            ->whereIn('status', $activeStatuses)
            ->whereYear('original_date_hired', '!=', date('Y'))
            ->whereMonth('original_date_hired', date('m'))
            ->orderByRaw("DAY(original_date_hired) >= ? DESC, DAY(original_date_hired)", [date('d')])
            ->limit(10)
            ->get();

        $newHires = Employee::select('id', 'first_name', 'last_name', 'avatar', 'original_date_hired', 'position', 'company_id', 'department_id')
            ->with('company:id,company_code', 'department:id,name')
            ->where('original_date_hired', '>=', date('Y-m-d', strtotime('-30 days')))
            ->where('status', '!=', 'Declined')
            ->orderBy('original_date_hired', 'desc')
            ->limit(10)
            ->get();

        $probationaryEmployees = Employee::select('id', 'first_name', 'last_name', 'avatar', 'position', 'original_date_hired', 'company_id')
            ->with('company:id,company_code')
            ->where('classification', '1')
            ->whereIn('status', $activeStatuses)
            ->orderBy('original_date_hired')
            ->limit(10)
            ->get();

        $ratio = [
            'active' => $totalEmployees > 0 ? round(($activeEmployees / $totalEmployees) * 100, 1) : 0,
            'inactive' => $totalEmployees > 0 ? round(($inactiveEmployees / $totalEmployees) * 100, 1) : 0,
            'probationary' => $activeEmployees > 0 ? round(($probationaryCount / $activeEmployees) * 100, 1) : 0,
        ];

        return view('dashboards.hr_dashboard', [
            'header' => 'dashboard-hr',
            'today' => $today,
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
            'totalEmployees' => $totalEmployees,
            'activeEmployees' => $activeEmployees,
            'inactiveEmployees' => $inactiveEmployees,
            'newHiresThisMonth' => $newHiresThisMonth,
            'resignedThisMonth' => $resignedThisMonth,
            'probationaryCount' => $probationaryCount,
            'statusBreakdown' => $statusBreakdown,
            'genderBreakdown' => $genderBreakdown,
            'companyHeadcount' => $companyHeadcount,
            'departmentHeadcount' => $departmentHeadcount,
            'classificationHeadcount' => $classificationHeadcount,
            'pendingApprovals' => $pendingApprovals,
            'leaveStatusThisMonth' => $leaveStatusThisMonth,
            'leaveTypeThisMonth' => $leaveTypeThisMonth,
            'birthdays' => $birthdays,
            'anniversaries' => $anniversaries,
            'newHires' => $newHires,
            'probationaryEmployees' => $probationaryEmployees,
            'ratio' => $ratio,
        ]);
    }

    public function pending_leave_count($approver_id){

        $today = date('Y-m-d');
        $from_date = date('Y-m-d',(strtotime ( '-1 month' , strtotime ( $today) ) ));
        $to_date = date('Y-m-d');
    
        return EmployeeLeave::select('user_id')->with('approver.approver_info')
                                    ->whereHas('approver',function($q) use($approver_id) {
                                        $q->where('approver_id',$approver_id);
                                    })
                                    ->where('status','Pending')
                                    // ->whereDate('created_at','>=',$from_date)
                                    // ->whereDate('created_at','<=',$to_date)
                                    ->count();
    }
    public function pending_overtime_count($approver_id){
    
        $today = date('Y-m-d');
        $from_date = date('Y-m-d',(strtotime ( '-1 month' , strtotime ( $today) ) ));
        $to_date = date('Y-m-d');
    
        return EmployeeOvertime::select('user_id')->whereHas('approver',function($q) use($approver_id) {
                                        $q->where('approver_id',$approver_id);
                                    })
                                    ->where('status','Pending')
                                    // ->whereDate('created_at','>=',$from_date)
                                    // ->whereDate('created_at','<=',$to_date)
                                    ->count();
    }
    public function edit_prob(Request $request, $id) {
        $employee = Employee::findOrFail($id);
    
        $classification = $request->input('classification');
    
        if ($classification) {
            if ($classification == 'for_regularization') {
                $employee->classification = '2';
                $employee->date_regularized = $request->input('date_regular');

                // $leave_credit = EmployeeLeaveCredit::where('user_id',$id)
                //                             ->where('leave_type',$request->leave_type)
                //                             ->first();
                // if($leave_credit){
                //     $leave_credit->count = $request->count;
                //     $leave_credit->save();
                // }else{
                //     $leave_credit = new EmployeeLeaveCredit;
                //     $leave_credit->leave_type = $request->leave_type;
                //     $leave_credit->user_id = $id;
                //     $leave_credit->count = $request->count;
                //     $leave_credit->save();
                // }

                $leave_credit_sick = EmployeeLeaveCredit::where('user_id', $id)
                                                ->where('leave_type', '2')
                                                ->first();
                if ($leave_credit_sick) {
                    $leave_credit_sick->count = $request->input('sl_count');
                    $leave_credit_sick->save();
                } else {
                    $leave_credit_sick = new EmployeeLeaveCredit;
                    $leave_credit_sick->leave_type = '2';
                    $leave_credit_sick->user_id = $id;
                    $leave_credit_sick->count = $request->input('sl_count');
                    $leave_credit_sick->save();
                }

                $leave_credit_vacation = EmployeeLeaveCredit::where('user_id', $id)
                                                    ->where('leave_type', '1')
                                                    ->first();
                if ($leave_credit_vacation) {
                    $leave_credit_vacation->count = $request->input('vl_count');
                    $leave_credit_vacation->save();
                } else {
                    $leave_credit_vacation = new EmployeeLeaveCredit;
                    $leave_credit_vacation->leave_type = '1';
                    $leave_credit_vacation->user_id = $id;
                    $leave_credit_vacation->count = $request->input('vl_count');
                    $leave_credit_vacation->save();
                }

            } elseif ($classification == 'for_resignation') {
                $employee->status = 'Inactive';
                $employee->date_resigned = $request->input('date_resigned');
            }
    
            $employee->save();
            Alert::success('Successfully Updated')->persistent('Dismiss');
            return back();
        } else {
            return back()->withErrors(['classification' => 'Classification is required.']);


        }
    }
    
    public function pending_wfh_count($approver_id){
    
        $today = date('Y-m-d');
        $from_date = date('Y-m-d',(strtotime ( '-1 month' , strtotime ( $today) ) ));
        $to_date = date('Y-m-d');
    
        return EmployeeWfh::select('user_id')->whereHas('approver',function($q) use($approver_id) {
                                        $q->where('approver_id',$approver_id);
                                    })
                                    ->where('status','Pending')
                                    // ->whereDate('created_at','>=',$from_date)
                                    // ->whereDate('created_at','<=',$to_date)
                                    ->count();
    }
    
    public function pending_ob_count($approver_id){
    
        $today = date('Y-m-d');
        $from_date = date('Y-m-d',(strtotime ( '-1 month' , strtotime ( $today) ) ));
        $to_date = date('Y-m-d');
    
        return EmployeeOb::select('user_id')->whereHas('approver',function($q) use($approver_id) {
                                        $q->where('approver_id',$approver_id);
                                    })
                                    ->where('status','Pending')
                                    // ->whereDate('created_at','>=',$from_date)
                                    // ->whereDate('created_at','<=',$to_date)
                                    ->count();
    }
    
    public function pending_dtr_count($approver_id){
    
        $today = date('Y-m-d');
        $from_date = date('Y-m-d',(strtotime ( '-1 month' , strtotime ( $today) ) ));
        $to_date = date('Y-m-d');
    
        return EmployeeDtr::select('user_id')->whereHas('approver',function($q) use($approver_id) {
                                        $q->where('approver_id',$approver_id);
                                    })
                                    ->where('status','Pending')
                                    // ->whereDate('created_at','>=',$from_date)
                                    // ->whereDate('created_at','<=',$to_date)
                                    ->count();
    }
}
