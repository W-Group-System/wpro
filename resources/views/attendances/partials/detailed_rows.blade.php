@foreach($attendance_groups as $group)
    @foreach($group['rows'] as $row)
        <tr data-employee-code="{{ $row['employee_no'] }}">
            @if(!empty($show_company_column))
                <td>{{ $row['company_code'] }}</td>
            @endif
            <td>{{ $row['employee_no'] }}</td>
            <td>{{ $row['display_name'] }}</td>
            <td>{{ date('d/m/Y', strtotime($row['log_date'])) }}</td>
            <td><small>{{ $row['shift'] }}</small></td>
            <td @if($row['has_ob']) class="bg-info" @endif>{{ $row['in'] }}</td>
            <td @if($row['has_ob']) class="bg-info" @endif>{{ $row['out'] }}</td>
            <td @if(($row['abs'] - $row['lv_w_pay']) > 0) class="bg-danger" @endif>{{ number_format($row['abs'], 2) }}</td>
            <td>{{ number_format($row['lv_w_pay'], 2) }}</td>
            <td>{{ number_format($row['reg_hrs'], 2) }}</td>
            <td @if($row['late_min'] > 0) class="bg-danger" @endif>{{ number_format($row['late_min'], 2) }}</td>
            <td @if($row['undertime_min'] > 0) class="bg-danger" @endif>{{ number_format($row['undertime_min'], 2) }}</td>
            <td @if($row['reg_ot'] > 0) class="bg-warning" @endif>{{ number_format($row['reg_ot'], 2) }}</td>
            <td @if($row['reg_nd'] > 0) class="bg-warning" @endif>{{ number_format($row['reg_nd'], 2) }}</td>
            <td @if($row['reg_ot_nd'] > 0) class="bg-warning" @endif>{{ number_format($row['reg_ot_nd'], 2) }}</td>
            <td @if($row['rst_ot'] > 0) class="bg-warning" @endif>{{ number_format($row['rst_ot'], 2) }}</td>
            <td @if($row['rst_ot_over_eight'] > 0) class="bg-warning" @endif>{{ number_format($row['rst_ot_over_eight'], 2) }}</td>
            <td @if($row['rst_nd'] > 0) class="bg-warning" @endif>{{ number_format($row['rst_nd'], 2) }}</td>
            <td @if($row['rst_nd_over_eight'] > 0) class="bg-warning" @endif>{{ number_format($row['rst_nd_over_eight'], 2) }}</td>
            <td @if($row['lh_ot'] > 0) class="bg-warning" @endif>{{ number_format($row['lh_ot'], 2) }}</td>
            <td @if($row['lh_ot_over_eight'] > 0) class="bg-warning" @endif>{{ number_format($row['lh_ot_over_eight'], 2) }}</td>
            <td @if($row['lh_nd'] > 0) class="bg-warning" @endif>{{ number_format($row['lh_nd'], 2) }}</td>
            <td @if($row['lh_nd_over_eight'] > 0) class="bg-warning" @endif>{{ number_format($row['lh_nd_over_eight'], 2) }}</td>
            <td @if($row['sh_ot'] > 0) class="bg-warning" @endif>{{ number_format($row['sh_ot'], 2) }}</td>
            <td @if($row['sh_ot_over_eight'] > 0) class="bg-warning" @endif>{{ number_format($row['sh_ot_over_eight'], 2) }}</td>
            <td @if($row['sh_nd'] > 0) class="bg-warning" @endif>{{ number_format($row['sh_nd'], 2) }}</td>
            <td @if($row['sh_nd_over_eight'] > 0) class="bg-warning" @endif>{{ number_format($row['sh_nd_over_eight'], 2) }}</td>
            <td @if($row['rst_lh_ot'] > 0) class="bg-warning" @endif>{{ number_format($row['rst_lh_ot'], 2) }}</td>
            <td @if($row['rst_lh_ot_over_eight'] > 0) class="bg-warning" @endif>{{ number_format($row['rst_lh_ot_over_eight'], 2) }}</td>
            <td @if($row['rst_lh_nd'] > 0) class="bg-warning" @endif>{{ number_format($row['rst_lh_nd'], 2) }}</td>
            <td @if($row['rst_lh_nd_over_eight'] > 0) class="bg-warning" @endif>{{ number_format($row['rst_lh_nd_over_eight'], 2) }}</td>
            <td @if($row['rst_sh_ot'] > 0) class="bg-warning" @endif>{{ number_format($row['rst_sh_ot'], 2) }}</td>
            <td @if($row['rst_sh_ot_over_eight'] > 0) class="bg-warning" @endif>{{ number_format($row['rst_sh_ot_over_eight'], 2) }}</td>
            <td @if($row['rst_sh_nd'] > 0) class="bg-warning" @endif>{{ number_format($row['rst_sh_nd'], 2) }}</td>
            <td @if($row['rst_sh_nd_over_eight'] > 0) class="bg-warning" @endif>{{ number_format($row['rst_sh_nd_over_eight'], 2) }}</td>
            <td>{{ $row['remarks'] }}</td>
        </tr>
    @endforeach
    <tr data-employee-code="{{ $group['employee']->employee_code }}">
        @if(!empty($show_company_column))
            <td colspan="7"><strong>Subtotal - {{ $group['employee']->employee_code }}</strong></td>
        @else
            <td colspan="6"><strong>Subtotal - {{ $group['employee']->first_name }} {{ $group['employee']->last_name }}</strong></td>
        @endif
        <td>{{ number_format($group['subtotal']['abs'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['lv_w_pay'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['reg_hrs'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['late_min'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['undertime_min'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['reg_ot'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['reg_nd'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['reg_ot_nd'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['rst_ot'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['rst_ot_over_eight'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['rst_nd'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['rst_nd_over_eight'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['lh_ot'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['lh_ot_over_eight'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['lh_nd'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['lh_nd_over_eight'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['sh_ot'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['sh_ot_over_eight'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['sh_nd'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['sh_nd_over_eight'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['rst_lh_ot'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['rst_lh_ot_over_eight'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['rst_lh_nd'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['rst_lh_nd_over_eight'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['rst_sh_ot'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['rst_sh_ot_over_eight'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['rst_sh_nd'], 2) }}</td>
        <td>{{ number_format($group['subtotal']['rst_sh_nd_over_eight'], 2) }}</td>
        <td></td>
    </tr>
@endforeach
