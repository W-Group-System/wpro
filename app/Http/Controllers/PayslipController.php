<?php

namespace App\Http\Controllers;

use App\AttendanceDetailedReport;
use Excel;
use App\Payroll;
use App\Employee;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use App\AttSummary;
use App\Company;
use App\Imports\PayRegImport;
use App\PayrollRecord;
use App\ScheduleData;

class PayslipController extends Controller
{
    //
    public function view ()
    {
      
        return view('payslips.payslips',
        array(
            'header' => 'payslips',
            
        ));
    }
    public function payroll_datas()
    {
        // $payrolls = Payroll::select('date_from','date_to','auditdate','created_at')->orderBy('date_from','desc')->get()->unique('date_from');
        // $payroll_employees = Payroll::orderBy('name','asc')->get();
        // $attendances =  AttSummary::orderBy('employee','asc')->get();
        // // dd($payrolls);
        // return view('payroll.pay_reg',
        // array(
        //     'header' => 'Payroll',
        //     'payrolls' => $payrolls,
        //     'payroll_employees' => $payroll_employees,
        //     'attendances' => $attendances,
            
        // ));
        $payrolls = PayrollRecord::select('payroll_date_from','payroll_date_to')->orderBy('payroll_date_from','desc')->get()->unique('payroll_date_from');
        $payroll_employees = PayrollRecord::orderBy('name','asc')->get();
        $pay_reg = PayrollRecord::all();
        return view('payroll.pay_reg',
        array(
            'header' => 'Payroll',
            'payrolls' => $payrolls,
            'payroll_employees' => $payroll_employees,
        )
        );
    }

    public function importPayRegExcel(Request $request)
    {
        Excel::import(new PayRegImport,request()->file('import_file'));
           
        return back();
    }
    
    
    public function attendances()
    {
        $attendances =  AttSummary::orderBy('employee','asc')->get();
        return view('payroll.timekeeping',
        array(
            'header' => 'Timekeeping',
            'attendances' => $attendances,
            'attendances' => $attendances,
            
        ));
    }
    function upload_attendance(Request $request)
    {
        $path = $request->file('file')->getRealPath();
        $data = Excel::load($path)->get();

        dd($data);
        if($data->count() > 0)
        {
            // dd($data);
        foreach($data->toArray() as $key => $value)
        {
            $payroll = new AttSummary;
            $payroll->company = $value['company'];
            $payroll->badge_no = $value['badge_no'];
            $payroll->employee = $value['employee'];
            $payroll->location = $value['location'];
            $payroll->period_from = date('Y-m-d',strtotime($value['period_from']));
            $payroll->period_to = date('Y-m-d',strtotime($value['period_to']));
            $payroll->tot_days_absent = $value['tot_days_absent'];
            $payroll->tot_days_work = $value['tot_days_work'];
            $payroll->tot_lates = $value['tot_lates'];
            $payroll->total_adjstmenthrs = $value['total_adjstmenthrs'];
            $payroll->tot_overtime_reg = $value['tot_overtime_reg'];
            $payroll->night_differential = $value['night_differential'];
            $payroll->night_differential_ot = $value['night_differential_ot'];
            $payroll->tot_regholiday = $value['tot_regholiday'];
            $payroll->tot_overtime_regholiday = $value['tot_overtime_regholiday'];
            $payroll->tot_regholiday_nightdiff = $value['tot_regholiday_nightdiff'];
            $payroll->tot_overtime_regholiday_nightdiff = $value['tot_overtime_regholiday_nightdiff'];
            $payroll->tot_spholiday = $value['tot_spholiday'];
            $payroll->tot_overtime_spholiday = $value['tot_overtime_spholiday'];
            $payroll->tot_spholiday_nightdiff = $value['tot_spholiday_nightdiff'];
            $payroll->tot_overtime_spholiday_nightdiff = $value['tot_overtime_spholiday_nightdiff'];
            $payroll->tot_rest = $value['tot_rest'];
            $payroll->tot_overtime_rest = $value['tot_overtime_rest'];
            $payroll->night_differential_rest = $value['night_differential_rest'];
            $payroll->night_differential_ot_rest = $value['night_differential_ot_rest'];
            $payroll->tot_overtime_rest_regholiday = $value['tot_overtime_rest_regholiday'];
            $payroll->night_differential_rest_regholiday = $value['night_differential_rest_regholiday'];
            $payroll->tot_overtime_night_diff_rest_regholiday = $value['tot_overtime_night_diff_rest_regholiday'];
            $payroll->tot_sprestholiday = $value['tot_sprestholiday'];
            $payroll->tot_overtime_sprestholiday = $value['tot_overtime_sprestholiday'];
            $payroll->tot_sprestholiday_nightdiff = $value['tot_sprestholiday_nightdiff'];
            $payroll->tot_overtime_sprestholiday_nightdiff = $value['tot_overtime_sprestholiday_nightdiff'];
            $payroll->total_undertime = $value['total_undertime'];
            $payroll->sick_leave = $value['sick_leave'];
            $payroll->vacation_leave = $value['vacation_leave'];
            $payroll->sick_leave_nopay = $value['sick_leave_nopay'];
            $payroll->vacation_leave_nopay = $value['vacation_leave_nopay'];
            $payroll->workfromhome = $value['workfromhome'];
            $payroll->offbusiness = $value['offbusiness'];
            $payroll->save();
        }
        }
        Alert::success('Successfully Import Attendance')->persistent('Dismiss');
     return back();
    }
    function import(Request $request)
    {
        $path = $request->file('file')->getRealPath();

        $data = Excel::load($path)->get();
        if($data->count() > 0)
        {
            // dd($data);
        foreach($data->toArray() as $key => $value)
        {
            if($value['empno'] != null)
            {
            $payroll = new Payroll;
            $payroll->emp_code  = $value['empno'];
            $payroll->bank_acctno  = $value['bank_acount'];
            $payroll->bank  = $value['bank'];
            $payroll->name  = $value['name'];
            $payroll->position  = $value['position'];
            $payroll->emp_status  = $value['employment_status'];
            $payroll->company  = $value['group'];
            $payroll->department  = $value['department'];
            $payroll->location  = $value['location'];
            $payroll->date_hired  = date('Y-m-d',strtotime($value['datehired']));
            $payroll->date_from  = date('Y-m-d',strtotime($value['cut_off_from']));
            $payroll->date_to  = date('Y-m-d',strtotime($value['cut_off_to']));
            $payroll->month_pay  = $value['monthly_basic_pay'];
            $payroll->daily_pay  = $value['daily_rate'];
            $payroll->semi_month_pay  = $value['basicpay_halfmonth'];
            $payroll->absences  = $value['absences_amount'];
            $payroll->late  = $value['late_amount'];
            $payroll->undertime  = $value['undertime_amount'];
            $payroll->salary_adjustment  = $value['salary_adjustment_taxable'];
            $payroll->overtime  = $value['overtime_amount'];
            $payroll->meal_allowance  = $value['meal_allowance'];
            $payroll->salary_allowance  = $value['salary_allowance'];
            $payroll->oot_allowance  = $value['out_of_town_allowance'];
            $payroll->inc_allowance  = $value['incentives_allowance'];
            $payroll->rel_allowance  = $value['relocation_allowance'];
            $payroll->disc_allowance  = $value['discretionary_allowance'];
            $payroll->trans_allowance  = $value['transpo_allowance'];
            $payroll->load_allowance  = $value['load_allowance'];
            $payroll->gross_pay  = $value['grosspay'];
            $payroll->total_taxable  = $value['total_taxable'];
            $payroll->witholding_tax  = $value['witholding_tax'];
            $payroll->sick_leave  = $value['sick_leave'];
            $payroll->vacation_leave  = $value['vacation_leave'];
            $payroll->wfhome  = $value['work_from_home'];
            $payroll->offbusiness  = $value['official_business'];
            $payroll->sick_leave_nopay  = $value['sick_leave_no_pay'];
            $payroll->vacation_leave_nopay  = $value['vacation_leave_no_pay'];
            $payroll->sss_regee  = $value['sss_reg_ee_jan15'];
            $payroll->sss_mpfee = $value['sss_mpf_ee_jan15'];
            $payroll->phic_ee  = $value['phic_ee_jan15'];
            $payroll->hdmf_ee  = $value['hmdf_ee_jan15'];
            $payroll->hdmf_sal_loan  = $value['hdmf_salary_loan'];
            $payroll->hdmf_cal_loan  = $value['hdmf_calamity_loan'];
            $payroll->sss_sal_loan  = $value['sss_salary_loan'];
            $payroll->sss_cal_loan  = $value['sss_calamity_loan'];
            $payroll->sal_ded_tax  = $value['salary_deduction_taxable'];
            $payroll->sal_ded_nontax  = $value['salary_deduction_non_taxable'];
            $payroll->sal_loan  = $value['salary_loan'];
            $payroll->com_loan  = $value['company_loan'];
            $payroll->omhas  = $value['omhas_advances_from_mac'];
            $payroll->coop_cbu  = $value['coop_cbu'];
            $payroll->coop_reg_loan  = $value['coop_regular_loan'];
            $payroll->coop_messco  = $value['coop_mescco'];
            $payroll->uploan  = $value['uploan'];
            $payroll->others  = $value['others'];
            $payroll->total_deduction  = $value['total_deduction'];
            $payroll->netpay  = $value['netpay'];
            $payroll->sss_reg_er  = $value['sss_reg_er_jan15'];
            $payroll->sss_mpf_er  = $value['sss_mpf_er_jan15'];
            $payroll->sss_ec  = $value['sss_ecjan15'];
            $payroll->phic_er  = $value['phic_erjan15'];
            $payroll->hdmf_er  = $value['hdmf_erjan15'];
            $payroll->payroll_status  = "N";
            $payroll->tin_no  = $value['tin_no.'];
            $payroll->phil_no = $value['philhealth_no.'];
            $payroll->pagibig_no  = $value['pagibig_no.'];
            $payroll->sss_no  = $value['sss_no.'];
            $payroll->save();
            }
        }
        }
    
    
     Alert::success('Successfully Import')->persistent('Dismiss');
     return back();
    }
    function monthly_benefit(Request $request)
    {
        $employees = Payroll::select('emp_code','name','semi_month_pay','month_pay','department','location','bank_acctno','bank')->orderBy('name','asc')->orderBy('date_from','desc')->get()->unique('emp_code');
        $payrolls = Payroll::whereYear('date_to',date('Y'))->get();
        $year = date('Y-01-01');
        $dates = [];
        for($m=0;$m<12 ;$m++)
        {
            $data_date = date('Y-m-15',strtotime($year));
            $data_date_2 = date('Y-m-t',strtotime($year));
            array_push($dates,$data_date);
            array_push($dates,$data_date_2);
            $year = date("Y-m-d",strtotime("+1 month",strtotime($year)));
        }
        return view('payroll.benefit',
        array(
            'header' => 'Month-Benefit',
            'employees' => $employees,
            'payrolls' => $payrolls,
            'dates' => $dates,
            
        ));
    }
    // public function generatedAttendances(Request $request)
    // {
    //     //02-20-24 JunJihad Commented This Code 

    //     // $attendances =  AttSummary::orderBy('employee','asc')->get();
    //     // return view('payroll.timekeeping',
    //     // array(
    //     //     'header' => 'Timekeeping',
    //     //     'attendances' => $attendances,
    //     //     'attendances' => $attendances,
            
    //     // ));
    //     $allowed_companies = getUserAllowedCompanies(auth()->user()->id);

    //     $companies = Company::whereHas('employee_has_company')
    //     ->whereIn('id',$allowed_companies)
    //     ->get();

    //     $attendance_controller = new AttendanceController;
    //     $company = isset($request->company) ? $request->company : "";

    //     $from_date = $request->from;
    //     $to_date = $request->to;

    //     $date_range =  [];
    //     $schedules = [];
    //     $emp_data = [];
    //     $attendances = [];
       
    //     if ($from_date != null) {
    //         $emp_data = Employee::select('employee_number','user_id','first_name','last_name','schedule_id','employee_code')
    //                             ->with(['attendances' => function ($query) use ($from_date, $to_date) {
    //                                 $query->whereBetween('time_in', [$from_date." 00:00:01", $to_date." 23:59:59"])
    //                                 ->orWhereBetween('time_out', [$from_date." 00:00:01", $to_date." 23:59:59"])
    //                                 ->orderBy('time_in','asc')
    //                                 ->orderby('time_out','desc')
    //                                 ->orderBy('id','asc');
    //                             }])
    //                             ->with(['approved_leaves' => function ($query) use ($from_date, $to_date) {
    //                                 $query->whereBetween('date_from', [$from_date, $to_date])
    //                                 ->where('status','Approved')
    //                                 ->orderBy('id','asc');
    //                             },'approved_leaves.leave'])
    //                             ->with(['approved_wfhs' => function ($query) use ($from_date, $to_date) {
    //                                 $query->whereBetween('applied_date', [$from_date, $to_date])
    //                                 ->where('status','Approved')
    //                                 ->orderBy('id','asc');
    //                             }])
    //                             ->with(['approved_obs' => function ($query) use ($from_date, $to_date) {
    //                                 $query->whereBetween('applied_date', [$from_date, $to_date])
    //                                 ->where('status','Approved')
    //                                 ->orderBy('id','asc');
    //                             }])
    //                             ->with(['approved_dtrs' => function ($query) use ($from_date, $to_date) {
    //                                 $query->whereBetween('dtr_date', [$from_date, $to_date])
    //                                 ->where('status','Approved')
    //                                 ->orderBy('id','asc');
    //                             }])->where('company_id', $company);
                                
    //         $emp_data =  $emp_data->where('status','Active')->get();
            
    //         $date_range =  $attendance_controller->dateRange($from_date, $to_date);
    //     }
    //     $schedules = ScheduleData::all();
    //     return view('payroll.attendance_detailed_report',
    //     array(
    //             'header' => 'Timekeeping',
    //             'from_date' => $from_date,
    //             'to_date' => $to_date,
    //             'companies' => $companies,
    //             'company' => $company,
    //             'date_range' => $date_range,
    //             'attendances' => $attendances,
    //             'schedules' => $schedules,
    //             'emp_data' => $emp_data,
    //         ));
    // }

    public function generatedAttendances(Request $request)
    {
        
        //02-20-24 JunJihad Commented This Code 

        // $attendances =  AttSummary::orderBy('employee','asc')->get();
        // return view('payroll.timekeeping',
        // array(
        //     'header' => 'Timekeeping',
        //     'attendances' => $attendances,
        //     'attendances' => $attendances,
            
        // ));
        $generated_timekeepings = [];
        $allowed_companies = getUserAllowedCompanies(auth()->user()->id);

        $companies = Company::whereHas('employee_has_company')
        ->whereIn('id',$allowed_companies)
        ->get();

        $attendance_controller = new AttendanceDetailedReport;
        $company = isset($request->company) ? $request->company : "";

        $from_date = $request->from;
        $to_date = $request->to;

        $schedules = [];
        $attendances = [];
       
        if ($from_date != null) {
           
            $generated_timekeepings = AttendanceDetailedReport::where('company_id',$request->company)->whereBetween('log_date',[$from_date,$to_date])->get();
       }
        $schedules = ScheduleData::all();
        return view('payroll.attendance_detailed_report',
        array(
                'header' => 'Timekeeping',
                'from_date' => $from_date,
                'to_date' => $to_date,
                'companies' => $companies,
                'company' => $company,
                'attendances' => $attendances,
                'schedules' => $schedules,
                'generated_timekeepings' => $generated_timekeepings
            ));
    }
}
