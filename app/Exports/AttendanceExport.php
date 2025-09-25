<?php

namespace App\Exports;

use App\AttendanceDetailedReport;
use App\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected $company, $from, $to;

    public function __construct($company, $from_date, $to_date)
    {
        $this->company = $company;
        $this->from = $from_date;
        $this->to = $to_date;
    }

    public function collection()
    {
        $data = AttendanceDetailedReport::join('companies', 'attendance_detailed_reports.company_id', '=', 'companies.id')
            ->where('attendance_detailed_reports.company_id', $this->company)
            ->whereBetween('attendance_detailed_reports.log_date', [$this->from, $this->to])
            ->get([
                'companies.company_code',
                'attendance_detailed_reports.employee_no',
                'attendance_detailed_reports.name',
                'attendance_detailed_reports.log_date',
                'attendance_detailed_reports.shift',
                'attendance_detailed_reports.in',
                'attendance_detailed_reports.out',
                'attendance_detailed_reports.abs',
                'attendance_detailed_reports.lv_w_pay',
                'attendance_detailed_reports.reg_hrs',
                'attendance_detailed_reports.late_min',
                'attendance_detailed_reports.undertime_min',
                'attendance_detailed_reports.reg_ot',
                'attendance_detailed_reports.reg_nd',
                'attendance_detailed_reports.reg_ot_nd',
                'attendance_detailed_reports.rst_ot',
                'attendance_detailed_reports.rst_ot_over_eight',
                'attendance_detailed_reports.rst_nd',
                'attendance_detailed_reports.rst_nd_over_eight',
                'attendance_detailed_reports.lh_ot',
                'attendance_detailed_reports.lh_ot_over_eight',
                'attendance_detailed_reports.lh_nd',
                'attendance_detailed_reports.lh_nd_over_eight',
                'attendance_detailed_reports.sh_ot',
                'attendance_detailed_reports.sh_ot_over_eight',
                'attendance_detailed_reports.sh_nd',
                'attendance_detailed_reports.sh_nd_over_eight',
                'attendance_detailed_reports.rst_lh_ot',
                'attendance_detailed_reports.rst_lh_ot_over_eight',
                'attendance_detailed_reports.rst_lh_nd',
                'attendance_detailed_reports.rst_lh_nd_over_eight',
                'attendance_detailed_reports.rst_sh_ot',
                'attendance_detailed_reports.rst_sh_ot_over_eight',
                'attendance_detailed_reports.rst_sh_nd',
                'attendance_detailed_reports.rst_sh_nd_over_eight',
            ]);

        // 👉 Add remarks per row
        $data->transform(function ($row) {
            $employee = Employee::with(['approved_obs', 'approved_leaves_with_pay'])
                ->where('employee_code', $row->employee_no)
                ->first();

            $remarks = null;

            if ($employee) {
                // Check OB
                if ($employee->approved_obs()
                    ->whereDate('date_from', '<=', $row->log_date)
                    ->whereDate('date_to', '>=', $row->log_date)
                    ->exists()) {
                    $remarks = 'OB';
                }

                // Check Leave With Pay
                $leave = $employee->approved_leaves_with_pay()
                    ->whereDate('date_from', '<=', $row->log_date)
                    ->whereDate('date_to', '>=', $row->log_date)
                    ->first();

                if ($leave) {
                    $remarks = ($leave->leave_type == 13)
                        ? $leave->leave->leave_type
                        : $leave->leave->leave_type . ' With Pay';
                }
            }

            $row->remarks = $remarks ?: '';
            return $row;
        });

        // 👉 Group by employee
        $grouped = $data->groupBy('employee_no');
        $final = collect();

        foreach ($grouped as $employee_no => $rows) {
            // Add all rows
            $final = $final->merge($rows);

            // Add subtotal row per employee
            $subtotal = [
                'company_code'   => 'Subtotal',
                'employee_no'    => $employee_no,
                'name'           => $rows->first()->name,
                'log_date'       => null,
                'shift'          => null,
                'in'             => null, 
                'out'            => null,
                'abs'            => $rows->sum('abs'),
                'lv_w_pay'       => $rows->sum('lv_w_pay'),
                'reg_hrs'        => $rows->sum('reg_hrs'),
                'late_min'       => $rows->sum('late_min'),
                'undertime_min'  => $rows->sum('undertime_min'),
                'reg_ot'         => $rows->sum('reg_ot'),
                'reg_nd'         => $rows->sum('reg_nd'),
                'reg_ot_nd'      => $rows->sum('reg_ot_nd'),
                'rst_ot'         => $rows->sum('rst_ot'),
                'rst_ot_over_eight' => $rows->sum('rst_ot_over_eight'),
                'rst_nd'         => $rows->sum('rst_nd'),
                'rst_nd_over_eight' => $rows->sum('rst_nd_over_eight'),
                'lh_ot'          => $rows->sum('lh_ot'),
                'lh_ot_over_eight'  => $rows->sum('lh_ot_over_eight'),
                'lh_nd'          => $rows->sum('lh_nd'),
                'lh_nd_over_eight'  => $rows->sum('lh_nd_over_eight'),
                'sh_ot'          => $rows->sum('sh_ot'),
                'sh_ot_over_eight'  => $rows->sum('sh_ot_over_eight'),
                'sh_nd'          => $rows->sum('sh_nd'),
                'sh_nd_over_eight'  => $rows->sum('sh_nd_over_eight'),
                'rst_lh_ot'      => $rows->sum('rst_lh_ot'),
                'rst_lh_ot_over_eight' => $rows->sum('rst_lh_ot_over_eight'),
                'rst_lh_nd'      => $rows->sum('rst_lh_nd'),
                'rst_lh_nd_over_eight' => $rows->sum('rst_lh_nd_over_eight'),
                'rst_sh_ot'      => $rows->sum('rst_sh_ot'),
                'rst_sh_ot_over_eight' => $rows->sum('rst_sh_ot_over_eight'),
                'rst_sh_nd'      => $rows->sum('rst_sh_nd'),
                'rst_sh_nd_over_eight' => $rows->sum('rst_sh_nd_over_eight'),
                'remarks'        => 'Subtotal', // ✅ clear label
            ];

            $final->push((object) $subtotal);
        }

        // 👉 Add Grand Total row
        $grandTotal = [
            'company_code'   => 'Grand Total',
            'employee_no'    => null,
            'name'           => null,
            'log_date'       => null,
            'shift'          => null,
            'in'             => null, 
            'out'            => null,
            'abs'            => $data->sum('abs'),
            'lv_w_pay'       => $data->sum('lv_w_pay'),
            'reg_hrs'        => $data->sum('reg_hrs'),
            'late_min'       => $data->sum('late_min'),
            'undertime_min'  => $data->sum('undertime_min'),
            'reg_ot'         => $data->sum('reg_ot'),
            'reg_nd'         => $data->sum('reg_nd'),
            'reg_ot_nd'      => $data->sum('reg_ot_nd'),
            'rst_ot'         => $data->sum('rst_ot'),
            'rst_ot_over_eight' => $data->sum('rst_ot_over_eight'),
            'rst_nd'         => $data->sum('rst_nd'),
            'rst_nd_over_eight' => $data->sum('rst_nd_over_eight'),
            'lh_ot'          => $data->sum('lh_ot'),
            'lh_ot_over_eight'  => $data->sum('lh_ot_over_eight'),
            'lh_nd'          => $data->sum('lh_nd'),
            'lh_nd_over_eight'  => $data->sum('lh_nd_over_eight'),
            'sh_ot'          => $data->sum('sh_ot'),
            'sh_ot_over_eight'  => $data->sum('sh_ot_over_eight'),
            'sh_nd'          => $data->sum('sh_nd'),
            'sh_nd_over_eight'  => $data->sum('sh_nd_over_eight'),
            'rst_lh_ot'      => $data->sum('rst_lh_ot'),
            'rst_lh_ot_over_eight' => $data->sum('rst_lh_ot_over_eight'),
            'rst_lh_nd'      => $data->sum('rst_lh_nd'),
            'rst_lh_nd_over_eight' => $data->sum('rst_lh_nd_over_eight'),
            'rst_sh_ot'      => $data->sum('rst_sh_ot'),
            'rst_sh_ot_over_eight' => $data->sum('rst_sh_ot_over_eight'),
            'rst_sh_nd'      => $data->sum('rst_sh_nd'),
            'rst_sh_nd_over_eight' => $data->sum('rst_sh_nd_over_eight'),
            'remarks'        => 'Grand Total', // ✅ clear label
        ];

        $final->push((object) $grandTotal);

        return $final;
    }

    public function headings(): array
    {
        return [
            'Company',
            'Employee No',
            'Name',
            'Log Date',
            'Shift',
            'IN',
            'OUT',
            'Absent',
            'Leave w/ pay',
            'Reg Hrs',
            'Late (min)',
            'Undertime (min)',
            'REG OT',
            'REG ND',
            'REG OT ND',
            'RST OT',
            'RST OT > 8',
            'RST ND',
            'RST ND > 8',
            'LH OT',
            'LH OT > 8',
            'LH ND',
            'LH ND > 8',
            'SH OT',
            'SH OT > 8',
            'SH ND',
            'SH ND > 8',
            'RST LH OT',
            'RST LH OT > 8',
            'RST LH ND',
            'RST LH ND > 8',
            'RST SH OT',
            'RST SH OT > 8',
            'RST SH ND',
            'RST SH ND > 8',
            'Remarks'
        ];
    }
}
