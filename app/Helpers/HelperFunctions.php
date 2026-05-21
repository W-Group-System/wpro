<?php
use App\ApplicantSystemNotification;
use App\UserAllowedCompany;
use App\UserAllowedLocation;
use App\UserAllowedProject;
use App\UserPrivilege;
use App\Employee;
use App\EmployeeLeave;
use App\EmployeeEarnedLeave;
use App\EmployeeLeaveCredit;
use App\EmployeeLeaveList;
use App\EmployeeLeaveSetting;
use App\Holiday;
use App\Attendance;
use App\Classification;
use App\DailySchedule;
use App\EmployeeOvertime;
use App\User;
use App\EmployeeOb;
use App\EmployeeDtr;
use App\ScheduleData;
use App\Tax;
use App\ExitClearanceSignatory;
use App\ExitResign;
use App\Leave;
use App\Level;
use App\Hmo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

function employee_name($employee_names,$employee_number){
    foreach($employee_names as $item){
        if($item['employee_number'] == $employee_number){
            return $item->last_name . ' ' . $item->first_name;
        }
    }
}
function getInitial($text) {
    preg_match_all('#([A-Z]+)#', $text, $capitals);
    if (count($capitals[1]) >= 2) {
        return substr(implode('', $capitals[1]), 0, 1);
    }
    return strtoupper(substr($text, 0, 1));
}

function appFormatDate($date) {
    return date("Y-m-d", strtotime($date));
}

function appFormatFullDate($date) {
    return date("F d, Y h:i A", strtotime($date));
}

function roleValidation(){
    if(session('role_ids'))
    {
        if(in_array(1,session('role_ids')) || in_array(3,session('role_ids')) || in_array(9,session('role_ids'))){ //Administrator,DCO and ADCO
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}
function roleValidationAsAdministrator(){
    if(session('role_ids'))
    {
        if(in_array(1,session('role_ids'))){ //Administrator,DCO and ADCO
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}


function getLeaveIncrementForDay($date, $scheduleDatas, $dailySchedules = null)
{
    // Priority 1: daily schedule override
    if ($dailySchedules && count($dailySchedules) > 0) {
        $dailySchedule = leaveDailyScheduleForDate($dailySchedules, $date);
        if ($dailySchedule) {
            $hours = $dailySchedule->working_hours ?? null;
            return ($hours !== null && (float)$hours == 4.0) ? 0.5 : 1;
        }
    }
    // Priority 2: default weekly schedule
    if ($scheduleDatas && count($scheduleDatas) > 0) {
        $dayName = date('l', strtotime($date));
        foreach ($scheduleDatas as $schedule) {
            $name = $schedule->name ?? ($schedule['name'] ?? null);
            if ($name == $dayName) {
                $hours = $schedule->working_hours ?? null;
                return ($hours !== null && (float)$hours == 4.0) ? 0.5 : 1;
            }
        }
    }
    return 1;
}

function get_count_days_leave($data,$date_from,$date_to,$location = null)
 {
    $count = 0;
    $startTime = strtotime($date_from);
    $endTime = strtotime($date_to);

    for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
      $thisDate = date('Y-m-d', $i);
      if(isLeaveCountableDay($thisDate, collect(), $data, $location)){
          $count += getLeaveIncrementForDay($thisDate, $data);
      }
    }
    return($count);
 }
 
function dateRangeHelper( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
    $dates = [];
    $current = strtotime( $first );
    $last = strtotime( $last );

    while( $current <= $last ) {
        $curr = date('D',$current);
      
            $dates[] = date( $format, $current);
            $current = strtotime( $step, $current );
        
    }

    return $dates;
}

function dateRangeHelperLeaveCount( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
    $dates = [];
    $current = strtotime( $first );
    $last = strtotime( $last );

    while( $current <= $last ) {
        $curr = date('D',$current);
      
        $dates[] = date( $format, $current);
        $current = strtotime( $step, $current );
        
    }

    return $dates;
}
function dateRangeHelperLeave( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
    $dates = [];
    $current = strtotime( $first );
    $last = strtotime( $last );

    while( $current <= $last ) {
        $curr = date('D',$current);
        if ($curr == 'Sun') {
            $current = strtotime( $step, $current);
        }else{
            $dates[] = date( $format, $current);
            $current = strtotime( $step, $current );
        }
    }

    return $dates;
}

function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
    $dates = [];
    $current = strtotime( $first );
    $last = strtotime( $last );

    while( $current <= $last ) {

        $dates[] = date( $format, $current );
        $current = strtotime( $step, $current );
    }

    return $dates;
}


function employeeSchedule($schedules = array(), $check_date, $schedule_id, $empNum=""){
    $dailySchedule = DailySchedule::where('employee_code', $empNum)
        ->where('log_date', $check_date)
        ->orderBy('id', 'DESC')
        ->first();

    if (!empty($dailySchedule)) {
        if($dailySchedule['log_date'] == $check_date && $dailySchedule['employee_code'] == $empNum){
        
        return $dailySchedule;
        }
    }
    
    $schedule_name = date('l',strtotime($check_date));
    if(count($schedules) > 0){
        foreach($schedules as $item){
            if($item['schedule_id'] == $schedule_id && $item['name'] == $schedule_name){
                return $item;
            }
        }
    }
}

function isRestDay( $date ) {
    
    $check_day = date('D',strtotime($date));
    $check = 0;
    if ($check_day == 'Sat' || $check_day == 'Sun') {
        $check = 1;
    }else{
        $check = 0;
    }
    return $check;
}

function employeeHasLeave($employee_leaves = array(), $check_date,$schedule = array()){
    
    $halfday=1;
    if($check_date == "2024-07-21")

    {
    }
    if(count($employee_leaves) > 0 && $schedule){
        foreach($employee_leaves as $item){
            if($check_date <= $item['date_to']){
                if($check_date >= $item['date_from'])
                {
                    // if(date('Y-m-d',strtotime($check_date)) == date('Y-m-d',strtotime($item['date_from']))){
                        $status = 'Without-Pay';
                        if($item['withpay'] == 1){
                            $status = 'With-Pay';
                        }
                        if($item['halfday'] == '1'){
                            $halfday=.5;
                            return $item['leave']['code'] . '-' . $halfday . '-' . $status;
                        }else{
                            return $item['leave']['code'] . '-' . $halfday . '-' . $status;
                        }
                    // }
                }
            }else{
                $date_range = dateRangeHelperLeave($item['date_from'],$item['date_to']);
                if(count($date_range) > 0){
                    foreach($date_range as $date_r){
                        if(date('Y-m-d',strtotime($date_r)) == date('Y-m-d',strtotime($check_date))){
                            $status = 'Without-Pay';
                            if($item['withpay'] == 1){
                                $status = 'With-Pay';
                            }
                            if($item['halfday'] == '1'){
                                $halfday=.5;
                                
                                return $item['leave']['code'] . '-' . $halfday . ' ' . $status;
                            }else{
                                return $item['leave']['code'] . '-' . $halfday . ' ' .$status;
                            }
                        }
                    }
                }
            }
        }
    }
}

function employeeHasOB($employee_obs = array(), $check_date){
    if(count($employee_obs) > 0){
        foreach($employee_obs as $item){
            if(date('Y-m-d',strtotime($item['applied_date'])) == date('Y-m-d',strtotime($check_date))){
                return 'OB';
            }
        }
    }
}

function employeeHasOBDetails($employee_obs = array(), $check_date){
    $final_data = "";
    if(count($employee_obs) > 0){
        $collect = collect($employee_obs);
        foreach($collect->sortBy('date_from') as $item){
            // dd($item);
            if(date('Y-m-d',strtotime($item->applied_date)) == date('Y-m-d',strtotime($check_date))){
                $final_data = $item;
                break;
            }
        }
        foreach($collect->sortByDesc('date_to') as $item){
            if(date('Y-m-d',strtotime($item->applied_date)) == date('Y-m-d',strtotime($check_date))){
                $final_data->date_to = $item->date_to;
                break;
            }
        }
    }
    return $final_data;
}

function employeeHasWFH($employee_wfhs = array(), $check_date){
    if(count($employee_wfhs) > 0){
        foreach($employee_wfhs as $item){
            if(date('Y-m-d',strtotime($item['applied_date'])) == date('Y-m-d',strtotime($check_date))){
                return 'WFH';
            }
        }
    }
}

function employeeHasWFHDetails($employee_wfhs = array(), $check_date){
    if(count($employee_wfhs) > 0){
        foreach($employee_wfhs as $item){
            if(date('Y-m-d',strtotime($item['applied_date'])) == date('Y-m-d',strtotime($check_date))){
                return $item;
            }
        }
    }
}

function employeeHasOTDetails($employee_ots = array(), $check_date){
    $total_approved_overtime = 0;
    if(count($employee_ots) > 0){
        foreach($employee_ots as $item){
            if(date('Y-m-d',strtotime($item['ot_date'])) == date('Y-m-d',strtotime($check_date))){

                $total =(float) $item['ot_approved_hrs'] - (float)$item['break_hrs'];

                $total_approved_overtime += max(0, $total);
            }
        }
    }

    return $total_approved_overtime;
}

function employeeHasDTRDetails($employee_dtrs = array(), $check_date){
    if($employee_dtrs){
        foreach($employee_dtrs as $item){
            if(date('Y-m-d',strtotime($item['dtr_date'])) == date('Y-m-d',strtotime($check_date))){
                return $item;
            }
        }
    }
}

function getUserAllowedCompanies($user_id){
    $user_allowed_companies = UserAllowedCompany::where('user_id',$user_id)->first();

    if($user_allowed_companies){
        return json_decode($user_allowed_companies->company_ids);
    }else{
        return [];
    }
}
function getUserAllowedLocations($user_id){
    $user_allowed_locations = UserAllowedLocation::where('user_id',$user_id)->first();

    if($user_allowed_locations){
        return json_decode($user_allowed_locations->location_ids);
    }else{
        return [];
    }
}
function getUserAllowedProjects($user_id){
    $user_allowed_projects = UserAllowedProject::where('user_id',$user_id)->first();

    if($user_allowed_projects){
        return json_decode($user_allowed_projects->project_ids);
    }else{
        return [];
    }
}

function checkUserPrivilege($field,$user_id){
    if (!\Illuminate\Support\Facades\Schema::hasColumn('user_privileges', $field)) {
        return 'no';
    }

    $user_privilege = UserPrivilege::select('id')->where($field,'on')->where('user_id',$user_id)->first();
    if($user_privilege){
        return 'yes';
    }else{
        return 'no';
    }
}

function businessModules()
{
    return collect([
        ['no' => 1, 'slug' => 'supplier-master', 'permission' => 'module_supplier_master', 'name' => 'Supplier Master', 'purpose' => 'Maintain supplier/vendor details, payment terms, tax details, bank details, and contact information.'],
        ['no' => 2, 'slug' => 'customer-master', 'permission' => 'module_customer_master', 'name' => 'Customer Master', 'purpose' => 'Maintain client/customer details, billing address, delivery address, credit terms, tax details, and contact information.'],
        ['no' => 3, 'slug' => 'item-product-master', 'permission' => 'module_item_product_master', 'name' => 'Item / Product Master', 'purpose' => 'Maintain products being traded, item codes, descriptions, units, cost price, selling price, category, and tax codes.'],
        ['no' => 4, 'slug' => 'purchase-requisition', 'permission' => 'module_purchase_requisition', 'name' => 'Purchase Requisition', 'purpose' => 'Internal request to purchase goods from suppliers.'],
        ['no' => 5, 'slug' => 'purchase-approval', 'permission' => 'module_purchase_approval', 'name' => 'Purchase Approval', 'purpose' => 'Approval of purchase requests based on budget, amount, product type, or management authority.'],
        ['no' => 6, 'slug' => 'supplier-quotation-rfq', 'permission' => 'module_supplier_quotation_rfq', 'name' => 'Supplier Quotation / RFQ', 'purpose' => 'Request and compare supplier prices, delivery terms, and availability.'],
        ['no' => 7, 'slug' => 'purchase-order', 'permission' => 'module_purchase_order', 'name' => 'Purchase Order', 'purpose' => 'Official order sent to supplier for goods to be purchased.'],
        ['no' => 8, 'slug' => 'goods-receipt-grn', 'permission' => 'module_goods_receipt_grn', 'name' => 'Goods Receipt / GRN', 'purpose' => 'Record goods received from supplier.'],
        ['no' => 9, 'slug' => 'quality-quantity-check', 'permission' => 'module_quality_quantity_check', 'name' => 'Quality / Quantity Check', 'purpose' => 'Check if received goods match the PO in terms of quantity, quality, and specifications.'],
        ['no' => 10, 'slug' => 'inventory-stock-management', 'permission' => 'module_inventory_stock_management', 'name' => 'Inventory / Stock Management', 'purpose' => 'Update stock after receiving goods. Tracks available, reserved, damaged, and sold stock.'],
        ['no' => 11, 'slug' => 'supplier-invoice', 'permission' => 'module_supplier_invoice', 'name' => 'Supplier Invoice', 'purpose' => 'Record invoice received from supplier.'],
        ['no' => 12, 'slug' => 'purchase-invoice-matching', 'permission' => 'module_purchase_invoice_matching', 'name' => 'Purchase Invoice Matching', 'purpose' => 'Match supplier invoice with PO and goods receipt.'],
        ['no' => 13, 'slug' => 'accounts-payable', 'permission' => 'module_accounts_payable', 'name' => 'Accounts Payable', 'purpose' => 'Record liability to supplier and prepare supplier payment.'],
        ['no' => 14, 'slug' => 'supplier-payment', 'permission' => 'module_supplier_payment', 'name' => 'Supplier Payment', 'purpose' => 'Pay supplier based on agreed payment terms.'],
        ['no' => 15, 'slug' => 'sales-inquiry', 'permission' => 'module_sales_inquiry', 'name' => 'Sales Inquiry', 'purpose' => 'Client asks for product availability, price, or quotation.'],
        ['no' => 16, 'slug' => 'sales-quotation', 'permission' => 'module_sales_quotation', 'name' => 'Sales Quotation', 'purpose' => 'Send price quotation to client, including product, quantity, price, tax, delivery date, and payment terms.'],
        ['no' => 17, 'slug' => 'sales-order', 'permission' => 'module_sales_order', 'name' => 'Sales Order', 'purpose' => 'Confirm client order after quotation acceptance.'],
        ['no' => 18, 'slug' => 'sales-approval-credit-check', 'permission' => 'module_sales_approval_credit_check', 'name' => 'Sales Approval / Credit Check', 'purpose' => 'Check customer credit limit, overdue invoices, pricing approval, or management approval before dispatch.'],
        ['no' => 19, 'slug' => 'stock-reservation', 'permission' => 'module_stock_reservation', 'name' => 'Stock Reservation', 'purpose' => 'Reserve available stock for the confirmed customer order.'],
        ['no' => 20, 'slug' => 'picking-and-packing', 'permission' => 'module_picking_and_packing', 'name' => 'Picking and Packing', 'purpose' => 'Warehouse prepares the goods for delivery based on the sales order.'],
        ['no' => 21, 'slug' => 'dispatch-delivery-to-client', 'permission' => 'module_dispatch_delivery_to_client', 'name' => 'Dispatch / Delivery to Client', 'purpose' => 'Goods are dispatched to the customer using a delivery note, dispatch note, or delivery challan.'],
        ['no' => 22, 'slug' => 'proof-of-delivery', 'permission' => 'module_proof_of_delivery', 'name' => 'Proof of Delivery', 'purpose' => 'Customer confirms receipt of goods through signed delivery note, receipt confirmation, or system update.'],
        ['no' => 23, 'slug' => 'sales-invoice', 'permission' => 'module_sales_invoice', 'name' => 'Sales Invoice', 'purpose' => 'Invoice is issued to the customer for the delivered goods.'],
        ['no' => 24, 'slug' => 'accounts-receivable', 'permission' => 'module_accounts_receivable', 'name' => 'Accounts Receivable', 'purpose' => 'Record customer receivable and monitor outstanding balances.'],
        ['no' => 25, 'slug' => 'customer-payment-collection', 'permission' => 'module_customer_payment_collection', 'name' => 'Customer Payment Collection', 'purpose' => 'Receive payment from customer by bank transfer, cash, check, card, or other method.'],
        ['no' => 26, 'slug' => 'receipt-reconciliation', 'permission' => 'module_receipt_reconciliation', 'name' => 'Receipt Reconciliation', 'purpose' => 'Match customer payment with sales invoice and close the receivable.'],
        ['no' => 27, 'slug' => 'sales-return-credit-note', 'permission' => 'module_sales_return_credit_note', 'name' => 'Sales Return / Credit Note', 'purpose' => 'Handle returned goods, damaged items, wrong deliveries, or customer claims.'],
        ['no' => 28, 'slug' => 'purchase-return-debit-note', 'permission' => 'module_purchase_return_debit_note', 'name' => 'Purchase Return / Debit Note', 'purpose' => 'Return damaged or incorrect goods to supplier and adjust supplier balance.'],
        ['no' => 29, 'slug' => 'reporting-and-analytics', 'permission' => 'module_reporting_and_analytics', 'name' => 'Reporting and Analytics', 'purpose' => 'Reports for purchases, sales, stock, profit margin, supplier aging, customer aging, and inventory movement.'],
    ]);
}

function userBusinessModules($user_id)
{
    return businessModules();
}

function canAccessBusinessModule($slug, $user_id)
{
    return businessModules()->where('slug', $slug)->isNotEmpty();
}

function erpNavigationGroups()
{
    return [
        'Masters' => ['supplier-master', 'customer-master', 'item-product-master'],
        'Purchasing' => ['purchase-requisition', 'purchase-approval', 'supplier-quotation-rfq', 'purchase-order', 'goods-receipt-grn', 'quality-quantity-check', 'supplier-invoice', 'purchase-invoice-matching', 'accounts-payable', 'supplier-payment'],
        'Sales' => ['sales-inquiry', 'sales-quotation', 'sales-order', 'sales-approval-credit-check', 'stock-reservation', 'picking-and-packing', 'dispatch-delivery-to-client', 'proof-of-delivery', 'sales-invoice', 'accounts-receivable', 'customer-payment-collection', 'receipt-reconciliation'],
        'Inventory' => ['inventory-stock-management'],
        'Returns' => ['sales-return-credit-note', 'purchase-return-debit-note'],
        'Reports' => ['reporting-and-analytics'],
    ];
}

function businessModuleBySlug($slug)
{
    return businessModules()->firstWhere('slug', $slug);
}

function checkUserAllowedOvertime($user_id){
    $employee = Employee::select('level')->where('user_id',$user_id)->first();
    if($employee->level == 'RANK&FILE' || $employee->level == '1'){
        return 'yes';
    }else{
        return 'no';
    }
}
function night_difference_per_company($start_work, $end_work)
{
    // Convert timestamps to Unix timestamps if they are not already
    if (!is_numeric($start_work)) {
        $start_work = strtotime($start_work);
    }
    if (!is_numeric($end_work)) {
        $end_work = strtotime($end_work);
    }

    // Define night shift boundaries
    $night_start = mktime(22, 0, 0, date('m', $start_work), date('d', $start_work), date('Y', $start_work));
    // if (date('H', $start_work) < 6) {
    //     $night_end = mktime(6, 0, 0, date('m', $start_work), date('d', $start_work), date('Y', $start_work));
    // } else {
    //     $night_end = mktime(6, 0, 0, date('m', $start_work), date('d', $start_work) + 1, date('Y', $start_work));
    // }
    $night_end = mktime(6, 0, 0, date('m', $start_work), date('d', $start_work) + 1, date('Y', $start_work));

    // Ensure $end_work is compared with the correct night boundaries
    if ($start_work >= $night_start && $start_work < $night_end) {
        if ($end_work >= $night_end) {
            return ($night_end - $start_work) / 3600;
        } else {
            return ($end_work - $start_work) / 3600;
        }
    } elseif ($end_work >= $night_start && $end_work < $night_end) {
        if ($start_work < $night_start) {
            return ($end_work - $night_start) / 3600;
        } else {
            return ($end_work - $start_work) / 3600;
        }
    } elseif ($start_work < $night_start && $end_work >= $night_end) {
        if ($start_work < $night_start)
        {
            return ($night_end - $night_start) / 3600;
        }
        else 
        {
            return ($night_end - $start_work) / 3600;
        }
    }

    return 0; // Default case when no night shift overlap
}

function night_difference_per_company_per_employee($start_work, $end_work, $date_r)
{
    if (!is_numeric($start_work)) {
        $start_work = strtotime($start_work);
    }
    if (!is_numeric($end_work)) {
        $end_work = strtotime($end_work);
    }
    if (!is_numeric($date_r)) {
        $date_r = strtotime($date_r);
    }
    if ($start_work < $date_r) {
        $start_work = $date_r;
    }

    if (date('H', $start_work) < 6) {
        $base_date = strtotime('-1 day', $start_work);
    } else {
        $base_date = $start_work;
    }

    $night_start = mktime(22, 0, 0, date('m', $base_date), date('d', $base_date), date('Y', $base_date));
   
    $night_end = mktime(6, 0, 0, date('m', $base_date), date('d', $base_date) + 1, date('Y', $base_date));

    if ($start_work >= $night_start && $start_work < $night_end) {
        if ($end_work >= $night_end) {
            return ($night_end - $start_work) / 3600;
        } else {
            return ($end_work - $start_work) / 3600;
        }
    } elseif ($end_work >= $night_start && $end_work < $night_end) {
        if ($start_work < $night_start) {
            return ($end_work - $night_start) / 3600;
        } else {
            return ($end_work - $start_work) / 3600;
        }
    } elseif ($start_work < $night_start && $end_work >= $night_end) {
        if ($start_work < $night_start)
        {
            return ($night_end - $night_start) / 3600;
        }
        else 
        {
            return ($night_end - $start_work) / 3600;
        }
    }

    return 0; 
}

// function get_count_days($dailySchedules, $scheduleDatas, $date_from, $date_to, $halfday)
// {
//     $date_from = Carbon::parse($date_from);
//     $date_to = Carbon::parse($date_to);

//     // Initialize count
//     $count = 0;
    
//     if ($date_from->equalTo($date_to)) {
//         // Single-day period
//         $count = 1;
//         foreach ($dailySchedules as $schedule) {
//             $log_date = $schedule->log_date ? Carbon::parse($schedule->log_date) : null;

//             if ($log_date && $log_date->between($date_from, $date_to)) {
//                 if (is_null($schedule->working_hours)) {
//                     // If working_hours is null, set count to 0 and break out of the loop
//                     return 0;
//                 } else {
//                     $count++;
//                 }
//             }
//         }
//     } else {
//         // Multiple-day period
//         foreach ($dailySchedules as $schedule) {
//             $log_date = Carbon::parse($schedule->log_date); // Parse log_date to Carbon instance

//             if ($log_date->between($date_from, $date_to)) {
//                 if (is_null($schedule->working_hours)) {
//                     // If working_hours is null, set count to 0 and break out of the loop
//                     $count;
//                 } else {
//                     $count++;
//                 }
//             }
//         }

//         // If no entries found with non-empty time_in_from, count based on scheduleDatas
//         if ($count === 0) {
//             $data = $scheduleDatas->pluck('name')->toArray();
//             $startTime = strtotime($date_from);
//             $endTime = strtotime($date_to);
            
//             for ($i = $startTime; $i <= $endTime; $i += 86400) {
//                 $thisDate = Carbon::createFromTimestamp($i)->format('l'); // Get the day name
//                 if (in_array($thisDate, $data)) {
//                     $count++;
//                 }
//             }
//         }
//     }

//     // Adjust count for half-day if applicable
//     if ($count == 1 && $halfday == 1) {
//         return 0.5;
//     } else {
//         return $count;
//     }
// }

function get_count_days($dailySchedules, $scheduleDatas, $date_from, $date_to, $halfday,$withpay = 0, $location = null)
{
    if($withpay == 0)
{
    return 0;
}
else
{
    $date_from = date('Y-m-d', strtotime($date_from));
    $date_to = date('Y-m-d', strtotime($date_to));
    
    // Initialize count
    $count = 0;
    
    // Create DateTime objects from string dates
    $dateFromObj = new DateTime($date_from);
    $dateToObj = new DateTime($date_to);
    
    // Loop over each day in the date range
    while ($dateFromObj <= $dateToObj) {
        if (isLeaveCountableDay($dateFromObj->format('Y-m-d'), $dailySchedules, $scheduleDatas, $location)) {
            $count++;
        }
    
        // Increment the date by one day
        $dateFromObj->modify('+1 day');
    }
    
    // Adjust count for half-day if applicable
    if ($count == 1 && $halfday == 1) {
        return 0.5;
    } else {
        return $count;
    }
}
   
}


function checkUsedSLVLSILLeave($user_id, $leave_type, $date_hired,$scheduleDatas = [])
{
    
    $count = 0;
    $all_days = [];
    $workingDays = [];
    if ($date_hired) {
        if($scheduleDatas != [])
        {
            $workingDays = $scheduleDatas->pluck('name')->toArray();
        }
        // dd($workingDays);
        $today = date('Y-m-d');
        $date_hired_md = date('m-d', strtotime($date_hired));
        $last_year = date('Y', strtotime('-1 year', strtotime($today)));
        $this_year = date('Y');

        $date_hired_this_year = $this_year . '-' . $date_hired_md;

        if ($today > $date_hired_this_year) {
            $filter_date_leave = $this_year . '-' . $date_hired_md;
        } else {
            $filter_date_leave = $last_year . '-' . $date_hired_md;
        }

        // Fetch the employee_number from the Employee model
        $employee = Employee::where('user_id', $user_id)->first();
        if (!$employee) {
            return $count; // If no employee found, return the count as 0
        }
        $employee_number = $employee->employee_number;

        $employee_vl = EmployeeLeave::where('user_id', $user_id)
            ->where('leave_type', $leave_type)
            ->where(function ($query) {
                $query->where('status', 'Approved')
                      ->orWhere('status', 'Pending');
            })
            ->where('withpay',1)
            ->where('status','!=','Cancelled')
            // ->where('date_from', '>', $filter_date_leave)
            ->get();
            // dd($employee_vl);
        if ($employee_vl) {
            foreach ($employee_vl as $leave) {
                if ($leave->withpay == 1 && $leave->halfday == 1) {
                    if (isLeaveCountableDay($leave->date_from, collect(), $scheduleDatas, $employee->location)) {
                        $count += 0.5;
                    }
                } else {
                    // Fetch daily schedules where log_date is within the leave date range
                    $dailySchedules = DailySchedule::where('employee_number', $employee_number)
                        ->whereBetween('log_date', [$leave->date_from, $leave->date_to])
                        ->get();
                    
                    // Iterate through each date in the date range
                    $date_range = dateRangeHelperLeaveCount($leave->date_from, $leave->date_to);
                    
                    if ($date_range) {
                        
                        foreach ($date_range as $date_r) {
                            $leave_Date = date('Y-m-d', strtotime($date_r));
                            // Check if withpay is 1 and leave_Date is valid
                            if ($leave->withpay == 1) {
                                if (isLeaveCountableDay($leave_Date, $dailySchedules, $scheduleDatas, $employee->location)) {
                                    $count += getLeaveIncrementForDay($leave_Date, $scheduleDatas, $dailySchedules);
                                    $all_days[]=$leave_Date;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $count;
}


// function checkUsedSLVLSILLeave($user_id,$leave_type,$date_hired){

//     $count = 0;
//     if($date_hired){
//         $today  = date('Y-m-d');
//         $date_hired_md = date('m-d',strtotime($date_hired));
//         $date_hired_m = date('m',strtotime($date_hired));
//         $last_year = date('Y', strtotime('-1 year', strtotime($today)) );
//         $this_year = date('Y');

//         $date_hired_this_year = $this_year . '-'. $date_hired_md;

//         if($today > $date_hired_this_year  ){
//             $filter_date_leave = $this_year . '-'. $date_hired_md;
//         }else{
//             $filter_date_leave = $last_year . '-'. $date_hired_md;
//         }
        
//         $employee_vl = EmployeeLeave::where('user_id',$user_id)
//                                         ->where('leave_type',$leave_type)
//                                         ->where('status','Approved')
//                                         // ->where('date_from','>',$filter_date_leave)
//                                         ->get();
        
//         $date_today = date('Y-m-d');
//         if($employee_vl){
//             foreach($employee_vl as $leave){
//                 if($leave->withpay == 1 && $leave->halfday == 1){
//                     if(date('Y-m-d',strtotime($leave->date_from))){
//                         $count += 0.5;
//                     }
//                 }else{
//                     $date_range = dateRange($leave->date_from,$leave->date_to);
//                     if($date_range){
//                         foreach($date_range as $date_r){
//                             $leave_Date = date('Y-m-d', strtotime($date_r));
//                             if($leave->withpay == 1 && $leave_Date){
//                                 $count += 1;
//                             }
//                         }
//                     }
//                 }
                
//             }
//         }
//     }
//     return $count;
// }

function checkEarnedLeave($user_id,$leave_type,$date_hired){

    //Get From Last Year Earned
    // if($date_hired){
    //     $today  = date('Y-m-d');
    //     $date_hired_md = date('m-d',strtotime($date_hired));
    //     $date_hired_m = date('m',strtotime($date_hired));
    //     $last_year = date('Y', strtotime('-1 year', strtotime($today)) );
    //     $this_year = date('Y');

    //     $date_hired_this_year = $this_year . '-'. $date_hired_md;
    //     $date_hired_last_year = $last_year . '-'. $date_hired_md;

    //     if($today >= $date_hired_this_year){ //if Date hired meets todays date get earned leaves from last year to this year date_hired
    //         $date_hired_this_minus_1_month = date('Y-m-d', strtotime('-1 month', strtotime($date_hired_this_year)) );
            return $vl_earned = EmployeeEarnedLeave::where('user_id',$user_id)
                                                        ->where('leave_type',$leave_type)
                                                        ->whereNull('converted_to_cash')
                                                        // ->whereBetween('earned_date', [$date_hired_last_year, $date_hired_this_minus_1_month])
                                                        ->sum('earned_leave');
        // }else{
        //     return 0;
        // }
    // }

    
    
}

function checkUsedSickLeave($user_id){
    $employee = Employee::with('ScheduleData')->where('user_id',$user_id)->first();
    $employee_sl = EmployeeLeave::where('user_id',$user_id)
                                    ->where('leave_type','2')
                                    ->where('status','Approved')
                                    ->get();

    $count = 0;
    if($employee_sl){
        foreach($employee_sl as $leave){
            if($leave->withpay == 1 && $leave->halfday == 1){
                if (!$employee || isLeaveCountableDay($leave->date_from, collect(), $employee->ScheduleData, $employee->location)) {
                    $count += 0.5;
                }
            }else{
                $date_range = dateRangeHelper($leave->date_from,$leave->date_to);
                if($date_range){
                    foreach($date_range as $date_r){
                        if($leave->withpay == 1
                            && (!$employee || isLeaveCountableDay($date_r, collect(), $employee->ScheduleData, $employee->location))){
                            $count += 1;
                        }
                    }
                }
            }
        }
    }

    return $count;
}

function checkUsedServiceIncentiveLeave($user_id){
    $employee = Employee::with('ScheduleData')->where('user_id',$user_id)->first();
    $employee_sil = EmployeeLeave::where('user_id',$user_id)
                                    ->where('leave_type','10')
                                    ->where('status','Approved')
                                    ->get();

    $count = 0;
    if($employee_sil){
        foreach($employee_sil as $leave){
            if($leave->withpay == 1 && $leave->halfday == 1){
                if (!$employee || isLeaveCountableDay($leave->date_from, collect(), $employee->ScheduleData, $employee->location)) {
                    $count += 0.5;
                }
            }else{
                $date_range = dateRangeHelper($leave->date_from,$leave->date_to);
                if($date_range){
                    foreach($date_range as $date_r){
                        if($leave->withpay == 1
                            && (!$employee || isLeaveCountableDay($date_r, collect(), $employee->ScheduleData, $employee->location))){
                            $count += 1;
                        }
                    }
                }
            }
        }
    }

    return $count;
}

function checkUsedLeave($user_id,$leave_type){
    $employee = Employee::with('ScheduleData')->where('user_id',$user_id)->first();
    $employee_leave = EmployeeLeave::where('user_id',$user_id)
                                    ->where('leave_type',$leave_type)
                                    ->whereIn('status',['Approved','Pending'])
                                    ->where('date_from', 'LIKE', '%'.date('Y').'%')
                                    ->where('created_at', 'LIKE', '%'.date('Y').'%')
                                    ->get();

    $count = 0;
    if($employee_leave){
        foreach($employee_leave as $leave){
            if($leave->withpay == 1 && $leave->halfday == 1){
                if (!$employee || isLeaveCountableDay($leave->date_from, collect(), $employee->ScheduleData, $employee->location)) {
                    $count += 0.5;
                }
            }else{
                $date_range = dateRangeHelper($leave->date_from,$leave->date_to);
                if($date_range){
                    foreach($date_range as $date_r){
                        if(($leave->withpay == 1 || $leave->withpay == 0)
                            && (!$employee || isLeaveCountableDay($date_r, collect(), $employee->ScheduleData, $employee->location))){
                            $count += 1;
                        }
                    }
                }
            }
        }
    }

    return $count;
}

function checkIfHoliday($date,$location){
    $check_holiday = Holiday::where('holiday_date',$date)->first();
    if($check_holiday){
        if($check_holiday->location){
            if($check_holiday->location == $location){
                return $check_holiday->holiday_type;
            }else{
                return "";
            }
        }else{
            return $check_holiday->holiday_type;
        }
    }else{
        return "";
    }
}

function checkHasAttendanceHoliday($date,$employee_code,$location){

    $date_attendance = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date) ) ));
    $check_if_holiday = checkIfHoliday($date_attendance,$location);
    $check_if_restday = isRestDay($date_attendance);

    if($check_if_holiday){ //Holiday
        $date_attendance_1 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance) ) ));
        $check_if_holiday_1 = checkIfHoliday($date_attendance_1,$location);
        $check_if_restday_1 = isRestDay($date_attendance_1);

        if($check_if_holiday_1){ //Holiday
            
            $date_attendance_2 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_1) ) ));
            $check_if_holiday_2 = checkIfHoliday($date_attendance_2,$location);
            $check_if_restday_2 = isRestDay($date_attendance_2);

            if($check_if_holiday_2){ //Holiday

                $date_attendance_3 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_2) ) ));
                $check_if_holiday_3 = checkIfHoliday($date_attendance_3,$location);
                $check_if_restday_3 = isRestDay($date_attendance_3);

                if($check_if_holiday_3){ //Holiday

                }else{ //Regular Work
                    if($check_if_restday_3 == 0){ //Rest day no
                        return $date_attendance_3;
                    }
                }
            }else{ //Regular Work
                if($check_if_restday_2 == 0){ //Rest day no
                    return $date_attendance_2;
                }
            }

        }else{ //Regular Work
            if($check_if_restday_1 == 0){ //Rest day no
                return $date_attendance_1;
            }else{
                $date_attendance_2 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_1) ) ));
                $check_if_holiday_2 = checkIfHoliday($date_attendance_2,$location);
                $check_if_restday_2 = isRestDay($date_attendance_2);

                if($check_if_holiday_2){ //Holiday
                    
                    $date_attendance_3 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_2) ) ));
                    $check_if_holiday_3 = checkIfHoliday($date_attendance_3,$location);
                    $check_if_restday_3 = isRestDay($date_attendance_3);

                    if($check_if_holiday_3){ //Holiday

                        $date_attendance_4 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_3) ) ));
                        $check_if_holiday_4 = checkIfHoliday($date_attendance_4,$location);
                        $check_if_restday_4 = isRestDay($date_attendance_4);
                        
                        if($check_if_holiday_4){ //Holiday

                        }else{ //Regular Work
                            if($check_if_restday_4 == 0){ //Rest day no
                                return $date_attendance_4;
                            }
                        }
                    }else{ //Regular Work
                        if($check_if_restday_3 == 0){ //Rest day no
                            return $date_attendance_3;
                        }
                    }
                }else{ //Regular Work
                    if($check_if_restday_2 == 0){ //Rest day no
                        return $date_attendance_2;
                    }
                }
            }
        }
    }else{ //Regular Work
        if($check_if_restday == 0){
            return $date_attendance;
        }else{ // Regular days
            $date_attendance_1 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance) ) ));
            $check_if_holiday_1 = checkIfHoliday($date_attendance_1,$location);
            $check_if_restday_1 = isRestDay($date_attendance_1);

            if($check_if_holiday_1){ //Holiday

                $date_attendance_2 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_1) ) ));
                $check_if_holiday_2 = checkIfHoliday($date_attendance_2,$location);
                $check_if_restday_2 = isRestDay($date_attendance_2);

                if($check_if_holiday_2){ //Holiday
                    $date_attendance_3 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_2) ) ));
                    $check_if_holiday_3 = checkIfHoliday($date_attendance_3,$location);
                    $check_if_restday_3 = isRestDay($date_attendance_3);

                    if($check_if_holiday_3){ //Holiday

                    }else{ //Regular Work
                        if($check_if_restday_3 == 0){ //Rest day no
                            return $date_attendance_3;
                        }
                    }
                }else{ //Regular Work
                    if($check_if_restday_2 == 0){ //Rest day no
                        return $date_attendance_2;
                    }
                }
            }else{ //Regular Work
                if($check_if_restday_1 == 0){ //Rest day no
                    return $date_attendance_1;
                }else{

                    $date_attendance_2 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_1) ) ));
                    $check_if_holiday_2 = checkIfHoliday($date_attendance_2,$location);
                    $check_if_restday_2 = isRestDay($date_attendance_2);

                    if($check_if_holiday_2){ //Holiday

                        $date_attendance_3 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_2) ) ));
                        $check_if_holiday_3 = checkIfHoliday($date_attendance_3,$location);
                        $check_if_restday_3 = isRestDay($date_attendance_3);

                        if($check_if_holiday_3){ //Holiday
                            $date_attendance_4 = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_attendance_3) ) ));
                            $check_if_holiday_4 = checkIfHoliday($date_attendance_4,$location);
                            $check_if_restday_4 = isRestDay($date_attendance_4);

                            if($check_if_holiday_4){ //Holiday

                            }else{ //Regular Work
                                if($check_if_restday_4 == 0){ //Rest day no
                                    return $date_attendance_4;
                                }
                            }
                        }else{ //Regular Work
                            if($check_if_restday_3 == 0){ //Rest day no
                                return $date_attendance_3;
                            }
                        }
                    }else{
                        if($check_if_restday_2 == 0){ //Rest day no
                            return $date_attendance_2;
                        }
                    }
                }
            }
        }
    }
}

function checkHasAttendanceHolidayStatus($attendances=array(),$check_date){
    $status =  '';
    if(count($attendances) > 0 && $check_date){
        foreach($attendances as $item){            
            if(date('Y-m-d',strtotime($item['time_in'])) == date('Y-m-d',strtotime($check_date))){
               return $item['time_in'];
            }
        }
    }
    return $status;
}

function getLastWorkingDay($date, $location, $schedules, $schedule_id, $employee_code) {
    $date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    for ($i = 0; $i < 7; $i++) { 
       $schedule_for_day = employeeSchedule(
            $schedules,
            $date,
            $schedule_id,
            $employee_code
        );

        if ($schedule_for_day) {
            $is_rest_day = isRestDayBySchedule($schedule_for_day);
        } else {
            $is_rest_day = isRestDay($date);
        }
        if (!checkIfHoliday($date, $location) && $is_rest_day == 0) {
            return $date;
        }
        $date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    }
    return null; 
}

function isRestDayBySchedule($schedule_for_day) {

    if (!$schedule_for_day) {
       
        return 0;
    }

    $time_in_from = $schedule_for_day->time_in_from ?? ($schedule_for_day['time_in_from'] ?? null);
    $time_in_to = $schedule_for_day->time_in_to ?? ($schedule_for_day['time_in_to'] ?? null);
    $time_out_from = $schedule_for_day->time_out_from ?? ($schedule_for_day['time_out_from'] ?? null);
    $time_out_to = $schedule_for_day->time_out_to ?? ($schedule_for_day['time_out_to'] ?? null);

    if (
        empty($time_in_from) &&
        empty($time_in_to) &&
        empty($time_out_from) &&
        empty($time_out_to)
    ) {
        return 1; 
    }

    return 0; 
}

function leaveDailyScheduleForDate($dailySchedules, $date)
{
    if (!$dailySchedules) {
        return null;
    }

    if (method_exists($dailySchedules, 'firstWhere')) {
        return $dailySchedules->firstWhere('log_date', $date);
    }

    foreach ($dailySchedules as $schedule) {
        if (isset($schedule->log_date) && date('Y-m-d', strtotime($schedule->log_date)) == $date) {
            return $schedule;
        }
        if (isset($schedule['log_date']) && date('Y-m-d', strtotime($schedule['log_date'])) == $date) {
            return $schedule;
        }
    }

    return null;
}

function isLeaveCountableDay($date, $dailySchedules = null, $scheduleDatas = null, $location = null)
{
    $date = date('Y-m-d', strtotime($date));

    if (checkIfHoliday($date, $location)) {
        return false;
    }

    $dailySchedule = leaveDailyScheduleForDate($dailySchedules, $date);
    if ($dailySchedule) {
        return !is_null($dailySchedule->working_hours ?? null)
            && isRestDayBySchedule($dailySchedule) == 0;
    }

    if ($scheduleDatas && count($scheduleDatas) > 0) {
        $dayName = date('l', strtotime($date));

        foreach ($scheduleDatas as $schedule) {
            $name = $schedule->name ?? ($schedule['name'] ?? null);
            if ($name == $dayName) {
                return isRestDayBySchedule($schedule) == 0;
            }
        }

        return false;
    }

    return isRestDay($date) == 0;
}

function checkEmployeeLeaveCredits($user_id, $leave_type){
    $employee_leave = EmployeeLeaveCredit::where('user_id',$user_id)
                                    ->where('leave_type',$leave_type)
                                    ->first();
    if($employee_leave){
        return $employee_leave->count;
    }else{
        return 0;
    }
}

function attendanceDetailedRows($employees = array(), $dateRange = array(), $schedules = array(), $cutOffDate = null)
{
    $groups = collect();
    $context = attendanceDetailedBuildContext($employees, $dateRange, $schedules);

    foreach ($employees as $employee) {
        $state = [
            'previous_abs' => 0,
        ];
        $rows = collect();
        $subtotal = attendanceDetailedEmptyTotals();

        foreach ($dateRange as $date) {
            $row = attendanceDetailedRow($employee, $date, $schedules, $cutOffDate, $state, $context);
            $rows->push($row);

            foreach (attendanceDetailedTotalKeys() as $key) {
                $subtotal[$key] += $row[$key];
            }
        }

        $groups->push([
            'employee' => $employee,
            'rows' => $rows,
            'subtotal' => $subtotal,
        ]);
    }

    return $groups;
}

function attendanceDetailedBuildContext($employees = array(), $dateRange = array(), $schedules = array())
{
    $employeeCodes = collect($employees)->pluck('employee_code')->filter()->values();
    $from = count($dateRange) ? min($dateRange) : null;
    $to = count($dateRange) ? max($dateRange) : null;
    $dailySchedules = collect();
    $holidays = collect();

    if ($from && $to) {
        $dailySchedules = DailySchedule::whereIn('employee_code', $employeeCodes)
            ->whereBetween('log_date', [$from, $to])
            ->orderBy('id', 'DESC')
            ->get()
            ->groupBy(function ($item) {
                return $item->employee_code . '|' . $item->log_date;
            });

        $holidayFrom = date('Y-m-d', strtotime('-7 days', strtotime($from)));
        $holidays = Holiday::whereBetween('holiday_date', [$holidayFrom, $to])
            ->get()
            ->groupBy('holiday_date');
    }

    $scheduleData = collect($schedules)->groupBy(function ($item) {
        return $item->schedule_id . '|' . $item->name;
    });

    return [
        'daily_schedules' => $dailySchedules,
        'schedule_data' => $scheduleData,
        'holidays' => $holidays,
    ];
}

function attendanceDetailedRow($employee, $date, $schedules = array(), $cutOffDate = null, &$state = array(), $context = null)
{
    $schedule = attendanceDetailedSchedule($schedules, $date, $employee->schedule_id, $employee->employee_code, $context);
    $shift = ($schedule && $schedule->time_in_to)
        ? date('h:i A', strtotime($schedule->time_in_to)) . '-' . date('h:i A', strtotime($schedule->time_out_to))
        : 'RESTDAY';

    $timeWindowStart = date('Y-m-d 00:00:00', strtotime($date));
    if ($schedule && $schedule->time_in_from) {
        $timeWindowStart = date('Y-m-d H:i:s', strtotime('-6 hours', strtotime($date . ' ' . $schedule->time_in_from)));
    }

    $attendance = $employee->attendances
        ->whereBetween('time_in', [$timeWindowStart, $date . ' 23:59:59'])
        ->sortBy('time_in')
        ->first();

    $timeOutOnly = null;
    $timeIn = '';
    $timeOut = '';
    if ($attendance) {
        $timeIn = $attendance->time_in;
        $timeOut = $attendance->time_out;
    } else {
        $timeOutOnly = $employee->attendances
            ->whereBetween('time_out', [$date . ' 00:00:00', $date . ' 23:59:59'])
            ->where('time_in', null)
            ->first();
        if ($timeOutOnly) {
            $timeOut = $timeOutOnly->time_out;
        }
    }

    $ob = employeeHasOBDetails($employee->approved_obs, date('Y-m-d', strtotime($date)));
    $dtr = employeeHasDTRDetails($employee->approved_dtrs, date('Y-m-d', strtotime($date)));

    $timeStart = $timeIn ? date('Y-m-d h:i A', strtotime($timeIn)) : '';
    $timeEnd = $timeOut ? date('Y-m-d h:i A', strtotime($timeOut)) : '';

    if ($ob) {
        if ($timeIn) {
            $timeStart = strtotime($ob->date_from) < strtotime($timeIn)
                ? date('Y-m-d h:i A', strtotime($ob->date_from))
                : date('Y-m-d h:i A', strtotime($timeIn));
        } else {
            $timeStart = date('Y-m-d h:i A', strtotime($ob->date_from));
        }

        if ($timeOut) {
            $timeEnd = strtotime($ob->date_to) > strtotime($timeOut)
                ? date('Y-m-d h:i A', strtotime($ob->date_to))
                : date('Y-m-d h:i A', strtotime($timeOut));
        } else {
            $timeEnd = date('Y-m-d h:i A', strtotime($ob->date_to));
        }
    }

    if ($dtr) {
        $timeStart = date('Y-m-d h:i A', strtotime($dtr->dtr_date . ' ' . $dtr->time_in));
        $timeEnd = date('Y-m-d h:i A', strtotime($dtr->dtr_date . ' ' . $dtr->time_out));
    }

    $isRestday = (!$schedule || !$schedule->time_in_from);
    $holiday = attendanceDetailedHoliday(date('Y-m-d', strtotime($date)), $employee->location, $context);
    $leave = employeeHasLeave($employee->approved_leaves, date('Y-m-d', strtotime($date)), $schedule);
    $leaveCount = 0;
    $absHalf = 0;
    if ($leave) {
        $leaveParts = explode('-', $leave);
        $leaveCount = isset($leaveParts[1]) ? (double) $leaveParts[1] : 0;
        if (strpos($leave, 'Without') !== false) {
            $absHalf = $leaveCount;
            $leaveCount = 0;
        }
    }

    $abs = ($timeStart && $timeEnd) ? 0 : 1;
    if ($isRestday) {
        $abs = 0;
        $leaveCount = 0;
    }
    if ($leaveCount > 0 && $abs == 1) {
        $abs = $leaveCount;
    }
    if ($absHalf > 0) {
        $abs = $absHalf;
    }
    if (!empty($employee->original_date_hired) && date('Y-m-d', strtotime($date)) < $employee->original_date_hired) {
        $abs = 1;
    }

    $scheduleHours = attendanceDetailedScheduleHours($schedule, $date, $employee);
    $work = 0;
    $late = 0;
    $undertimeMinutes = 0;
    $overtime = 0;
    $nightDiff = 0;
    $nightDiffOt = 0;
    $restdayOt = 0;
    $restdayOtGe = 0;
    $restNd = 0;
    $restNdGe = 0;
    $lhOt = 0;
    $lhOtGe = 0;
    $lhNd = 0;
    $lhNdGe = 0;
    $shOt = 0;
    $shOtGe = 0;
    $shNd = 0;
    $shNdGe = 0;
    $rstLhOt = 0;
    $rstLhOtGe = 0;
    $rstLhNd = 0;
    $rstLhNdGe = 0;
    $rstShOt = 0;
    $rstShOtGe = 0;
    $rstShNd = 0;
    $rstShNdGe = 0;

    if ($timeStart && $timeEnd && $schedule && $schedule->time_in_from) {
        $timeStartTs = strtotime($timeStart);
        $timeEndTs = strtotime($timeEnd);
        $scheduleIn = strtotime($date . ' ' . $schedule->time_in_to);
        $scheduleOut = strtotime($date . ' ' . $schedule->time_out_to);
        if ($scheduleOut < $scheduleIn) {
            $scheduleOut += 86400;
        }

        $effectiveStart = max($timeStartTs, strtotime($date . ' ' . $schedule->time_in_from));
        $effectiveEnd = min($timeEndTs, $scheduleOut);
        $rawWork = max(0, round(($effectiveEnd - $effectiveStart) / 3600, 2));
        $workOt = max(0, round(($timeEndTs - $effectiveStart) / 3600, 2));
        $originalSchedule = max(0, round(($scheduleOut - $scheduleIn) / 3600, 2));
        $work = attendanceDetailedDeductLunch($rawWork, $effectiveStart, $effectiveEnd, $originalSchedule, $employee, $date);
        $work = min($work, $scheduleHours);

        if ($leaveCount == .5 || $absHalf == .5) {
            $work = min($work, $scheduleHours / 2);
        }

        $lateHours = 0;
        if ($timeStartTs > $scheduleIn) {
            $lateHours = round(($timeStartTs - $scheduleIn) / 3600, 2);
        }
        $late = max(0, $lateHours * 60);

        $undertimeHours = max(0, $scheduleHours - $work);
        if ($lateHours > 0) {
            $undertimeHours = max(0, $undertimeHours - $lateHours);
        }
        $undertimeMinutes = max(0, $undertimeHours * 60);

        $overtime = max(0, $workOt - $originalSchedule);
        $approvedOt = $employee->approved_ots ? employeeHasOTDetails($employee->approved_ots, date('Y-m-d', strtotime($date))) : 0;
        $overtime = attendanceDetailedPayableOt($overtime, $approvedOt);

        $nightStart = max($effectiveStart, $scheduleIn);
        $nightEnd = min($timeEndTs, $scheduleOut);
        if ($nightEnd > $nightStart) {
            $nightDiff = night_difference_per_company(date('Y-m-d H:i', $nightStart), date('Y-m-d H:i', $nightEnd));
            if ($originalSchedule > 8 && $nightDiff >= 5) {
                $nightDiff -= 1;
            }
        }

        if ($timeEndTs > $scheduleOut && strtotime($schedule->time_in_to) > strtotime($schedule->time_out_to)) {
            $nightDiffOt = max(0, night_difference_per_company($timeStart, $timeEnd) - $nightDiff);
        }
    }

    if ($timeStart && $timeEnd && $isRestday) {
        $workRest = max(0, round((strtotime($timeEnd) - strtotime($timeStart)) / 3600, 2));
        if ($workRest > 8) {
            $workRest -= 1;
        }
        $approvedOt = $employee->approved_ots ? employeeHasOTDetails($employee->approved_ots, date('Y-m-d', strtotime($date))) : 0;
        $workRest = attendanceDetailedPayableOt($workRest, $approvedOt);
        $restdayOt = min($workRest, 8);
        $restdayOtGe = max(0, $workRest - 8);
        $restNd = night_difference_per_company($timeStart, $timeEnd);
        $work = 0;
        $late = 0;
        $undertimeMinutes = 0;
    }

    if ($holiday && $timeStart && $timeEnd) {
        $approvedOt = $employee->approved_ots ? employeeHasOTDetails($employee->approved_ots, date('Y-m-d', strtotime($date))) : 0;
        $renderedHolidayOt = attendanceDetailedRenderedOt($timeStart, $timeEnd);
        $payableHolidayOt = attendanceDetailedPayableOt($renderedHolidayOt, $approvedOt);
        $late = 0;
        $nightDiff = 0;
        $nightDiffOt = 0;
        $undertimeMinutes = 0;
        $overtime = 0;

        if ($holiday == 'Special Holiday') {
            $shOt = min($payableHolidayOt ?: $work, 8);
            $shOtGe = max(0, ($payableHolidayOt ?: $work) - 8);
            $shNd = night_difference_per_company($timeStart, $timeEnd);
        } else {
            $lhOt = min($payableHolidayOt ?: $work, 8);
            $lhOtGe = max(0, ($payableHolidayOt ?: $work) - 8);
            $lhNd = night_difference_per_company($timeStart, $timeEnd);
            if ($lhNd >= 4.5 && $scheduleHours > 8) {
                $lhNd -= 1;
            }
        }
    }

    if ($work > 0) {
        $abs = 0;
    }
    if ($leaveCount > 0 && $abs == 0) {
        $abs = $leaveCount;
    }
    if ($isRestday) {
        $abs = 0;
        $leaveCount = 0;
    }

    $remarks = trim(($leave ?: '') . ($ob ? ' OB' : '') . ($dtr ? ' DTR' : ''));

    return [
        'company_id' => $employee->company_id,
        'company_code' => $employee->company ? $employee->company->company_code : '',
        'employee_no' => $employee->employee_code,
        'name' => trim($employee->last_name . ', ' . $employee->first_name . ' ' . $employee->middle_name),
        'display_name' => trim($employee->first_name . ' ' . $employee->last_name),
        'log_date' => date('Y-m-d', strtotime($date)),
        'shift' => $shift,
        'in' => $timeStart ? date('h:i A', strtotime($timeStart)) : '',
        'out' => $timeEnd ? date('h:i A', strtotime($timeEnd)) : '',
        'abs' => round($abs, 2),
        'lv_w_pay' => round($leaveCount, 2),
        'reg_hrs' => round(max(0, $work), 2),
        'late_min' => round(max(0, $late), 2),
        'undertime_min' => round(max(0, $undertimeMinutes), 2),
        'reg_ot' => round(max(0, $overtime), 2),
        'reg_nd' => round(max(0, $nightDiff), 2),
        'reg_ot_nd' => round(max(0, $nightDiffOt), 2),
        'rst_ot' => round(max(0, $restdayOt), 2),
        'rst_ot_over_eight' => round(max(0, $restdayOtGe), 2),
        'rst_nd' => round(max(0, $restNd), 2),
        'rst_nd_over_eight' => round(max(0, $restNdGe), 2),
        'lh_ot' => round(max(0, $lhOt), 2),
        'lh_ot_over_eight' => round(max(0, $lhOtGe), 2),
        'lh_nd' => round(max(0, $lhNd), 2),
        'lh_nd_over_eight' => round(max(0, $lhNdGe), 2),
        'sh_ot' => round(max(0, $shOt), 2),
        'sh_ot_over_eight' => round(max(0, $shOtGe), 2),
        'sh_nd' => round(max(0, $shNd), 2),
        'sh_nd_over_eight' => round(max(0, $shNdGe), 2),
        'rst_lh_ot' => round(max(0, $rstLhOt), 2),
        'rst_lh_ot_over_eight' => round(max(0, $rstLhOtGe), 2),
        'rst_lh_nd' => round(max(0, $rstLhNd), 2),
        'rst_lh_nd_over_eight' => round(max(0, $rstLhNdGe), 2),
        'rst_sh_ot' => round(max(0, $rstShOt), 2),
        'rst_sh_ot_over_eight' => round(max(0, $rstShOtGe), 2),
        'rst_sh_nd' => round(max(0, $rstShNd), 2),
        'rst_sh_nd_over_eight' => round(max(0, $rstShNdGe), 2),
        'remarks' => $remarks,
        'cut_off_date' => $cutOffDate,
        'has_ob' => (bool) $ob,
    ];
}

function attendanceDetailedPayableOt($renderedOt, $approvedOt)
{
    $renderedOt = max(0, (double) $renderedOt);

    if ($approvedOt === '' || $approvedOt === null) {
        return $renderedOt;
    }

    $approvedOt = max(0, (double) $approvedOt);

    if ($approvedOt <= 0) {
        return 0;
    }

    return min($renderedOt, $approvedOt);
}

function attendanceDetailedRenderedOt($timeStart, $timeEnd)
{
    if (!$timeStart || !$timeEnd) {
        return 0;
    }

    $renderedOt = max(0, round((strtotime($timeEnd) - strtotime($timeStart)) / 3600, 2));

    if ($renderedOt > 8) {
        $renderedOt -= 1;
    }

    return $renderedOt;
}

function attendanceDetailedSchedule($schedules = array(), $checkDate, $scheduleId, $employeeCode = '', $context = null)
{
    if ($context) {
        $daily = $context['daily_schedules']->get($employeeCode . '|' . $checkDate);
        if ($daily && $daily->first()) {
            return $daily->first();
        }

        $scheduleName = date('l', strtotime($checkDate));
        $schedule = $context['schedule_data']->get($scheduleId . '|' . $scheduleName);
        if ($schedule && $schedule->first()) {
            return $schedule->first();
        }

        return null;
    }

    return employeeSchedule($schedules, $checkDate, $scheduleId, $employeeCode);
}

function attendanceDetailedHoliday($date, $location, $context = null)
{
    if (!$context) {
        return checkIfHoliday($date, $location);
    }

    $holidays = $context['holidays']->get($date, collect());
    foreach ($holidays as $holiday) {
        if (!$holiday->location || $holiday->location == $location) {
            return $holiday->holiday_type;
        }
    }

    return '';
}

function attendanceDetailedScheduleHours($schedule, $date, $employee = null)
{
    if (!$schedule || !$schedule->time_in_from) {
        return 0;
    }

    $scheduleIn = strtotime($date . ' ' . $schedule->time_in_to);
    $scheduleOut = strtotime($date . ' ' . $schedule->time_out_to);
    if ($scheduleOut < $scheduleIn) {
        $scheduleOut += 86400;
    }

    $hours = max(0, ($scheduleOut - $scheduleIn) / 3600);
    if ($hours > 8) {
        $hours -= 1;
    }
    if ($employee && $employee->employee_code == 'A340612') {
        $hours -= 1;
    }

    return max(0, round($hours, 2));
}

function attendanceDetailedDeductLunch($work, $start, $end, $originalSchedule, $employee = null, $date = null)
{
    if ($originalSchedule > 8) {
        return $work >= (($originalSchedule - 1) / 1.5) ? max(0, $work - 1) : $work;
    }

    $lunchStart = strtotime(date('Y-m-d 12:00:00', $start));
    $lunchEnd = strtotime(date('Y-m-d 13:00:00', $start));
    $dayOfWeek = $date ? date('N', strtotime($date)) : date('N', $start);
    $isWeekend = $dayOfWeek >= 6;
    $isPbi = $employee && $employee->company_id == 10;
    $isWliHbu = $employee && $employee->company_id == 13;

    if (!(($isPbi && $isWeekend) || $isWliHbu || ($isPbi && $originalSchedule <= 8))) {
        if ($start <= $lunchStart && $end >= $lunchEnd) {
            return max(0, $work - 1);
        }
    }

    return $work;
}

function attendanceDetailedTotalKeys()
{
    return [
        'abs',
        'lv_w_pay',
        'reg_hrs',
        'late_min',
        'undertime_min',
        'reg_ot',
        'reg_nd',
        'reg_ot_nd',
        'rst_ot',
        'rst_ot_over_eight',
        'rst_nd',
        'rst_nd_over_eight',
        'lh_ot',
        'lh_ot_over_eight',
        'lh_nd',
        'lh_nd_over_eight',
        'sh_ot',
        'sh_ot_over_eight',
        'sh_nd',
        'sh_nd_over_eight',
        'rst_lh_ot',
        'rst_lh_ot_over_eight',
        'rst_lh_nd',
        'rst_lh_nd_over_eight',
        'rst_sh_ot',
        'rst_sh_ot_over_eight',
        'rst_sh_nd',
        'rst_sh_nd_over_eight',
    ];
}

function attendanceDetailedEmptyTotals()
{
    return array_fill_keys(attendanceDetailedTotalKeys(), 0);
}

function getEmployeeLeaveCreditTallies($employee)
{
    if (!$employee) {
        return collect();
    }

    $leave_lists = EmployeeLeaveList::with('leave')
        ->where('user_id', $employee->user_id)
        ->get();
    $leave_credits = EmployeeLeaveCredit::with('leave')
        ->where('user_id', $employee->user_id)
        ->get();
    $leave_setting = null;
    if (\Illuminate\Support\Facades\Schema::hasTable('employee_leave_settings')) {
        $leave_setting = $employee->leave_setting ?: EmployeeLeaveSetting::where('employee_id', $employee->id)->first();
    }

    $leave_ids = $leave_lists->pluck('leave_id')
        ->merge($leave_credits->pluck('leave_type'))
        ->merge($leave_setting ? collect([1, 2]) : collect())
        ->unique()
        ->values();

    return $leave_ids->map(function ($leave_id) use ($employee, $leave_lists, $leave_credits, $leave_setting) {
        $leave = optional($leave_lists->where('leave_id', $leave_id)->first())->leave
            ?: optional($leave_credits->where('leave_type', $leave_id)->first())->leave
            ?: Leave::find($leave_id);

        $is_accumulative = false;
        if ($leave_setting && $leave_id == 1) {
            $is_accumulative = $leave_setting->vl_is_accumulative == 1;
        } elseif ($leave_setting && $leave_id == 2) {
            $is_accumulative = $leave_setting->sl_is_accumulative == 1;
        }

        $earned_total = $leave_lists->where('leave_id', $leave_id);
        if (!$is_accumulative) {
            $earned_total = $earned_total->where('year', date('Y'));
        }
        $earned_total = $earned_total->sum('earned_per_month');

        $beginning_total = $leave_credits->where('leave_type', $leave_id)->sum('count');
        $total = $beginning_total + $earned_total;

        if ($leave_id == 1) {
            $used = $is_accumulative
                ? checkUsedSLVLSILLeave($employee->user_id, 1, $employee->original_date_hired, $employee->ScheduleData)
                : usedSlVlThisYear($employee->user_id, 1, $employee->original_date_hired, $employee->ScheduleData);
        } elseif ($leave_id == 2) {
            $used = $is_accumulative
                ? checkUsedSLVLSILLeave($employee->user_id, 2, $employee->original_date_hired, $employee->ScheduleData)
                : usedSlVlThisYear($employee->user_id, 2, $employee->original_date_hired, $employee->ScheduleData);
        } elseif ($leave_id == 10) {
            $used = checkUsedSLVLSILLeave($employee->user_id, 10, $employee->original_date_hired, $employee->ScheduleData);
        } else {
            $used = checkUsedLeave($employee->user_id, $leave_id);
        }

        $balance = $total - $used;

        return (object) [
            'leave_id' => $leave_id,
            'leave_type' => optional($leave)->leave_type ?: 'Leave ' . $leave_id,
            'beginning' => round($beginning_total, 3),
            'earned' => round($earned_total, 3),
            'total' => round($total, 3),
            'used' => round($used, 3),
            'balance' => round(max($balance, 0), 3),
        ];
    })->sortBy('leave_type')->values();
}

function addWorkingDays($date, $days)
{
    $current = Carbon::parse($date);
    $added = 0;

    while ($added < $days) {
        $current->addDay();
        if (!$current->isWeekend()) {
            $added++;
        }
    }

    return $current;
}

function employeeLeaveMinimumFileDate($workingDays = 3)
{
    return addWorkingDays(date('Y-m-d'), $workingDays)->format('Y-m-d');
}

function employeeCanFileVacationLeaveDate($date, $workingDays = 3)
{
    return Carbon::parse($date)->gte(addWorkingDays(date('Y-m-d'), $workingDays)->startOfDay());
}

function payrollScheduledItems($items, $isFirstCutoff, $scheduleField = 'schedule')
{
    if (empty($items)) {
        return collect();
    }

    $cutoffSchedule = $isFirstCutoff ? 'Every 1st cut off' : 'Every 2nd cut off';

    return collect($items)->filter(function ($item) use ($scheduleField, $cutoffSchedule) {
        $schedule = $item->{$scheduleField} ?? null;
        return in_array($schedule, ['Every cut off', 'This cut off', $cutoffSchedule]);
    })->values();
}

function payrollLoanBalance($loan)
{
    if (!$loan) {
        return 0;
    }

    return max(($loan->initial_amount ?? 0) - ($loan->pay)->sum('amount'), 0);
}

function payrollLoanDeductionAmount($loan)
{
    return min($loan->monthly_ammort_amt ?? 0, payrollLoanBalance($loan));
}

function compute_tax($employee_salary,$level) {
    if($level == 4)
    {
        $taxes = Tax::where('level',4)->get();
    }
    else

    {
        $taxes = Tax::where('level',null)->get();
    }
   

    foreach ($taxes as $tax) 
    {
        // if ($employee_salary >= $tax->from_gross && ($tax->to_gross == 0 || $employee_salary <= $tax->to_gross)) {
        //     $excess_income = $employee_salary - $tax->excess_over;
        //     $computed_tax = $tax->tax_plus + ($excess_income * ($tax->percentage / 100));
        //     return $computed_tax;
        // }
        if ($employee_salary >= $tax->from_gross && ($tax->to_gross == 0 || $employee_salary <= $tax->to_gross)) {
            $excess_income = $employee_salary - $tax->excess_over;
            $computed_tax = $tax->tax_plus + ($excess_income * ($tax->percentage / 100));
            return $computed_tax;
        }
    }

    return 0; 
}

function getEmployeeHierarchy($userId)
{
    // Get the employee
    $employee = Employee::where('user_id', $userId)->firstOrFail();
    $to_top = Employee::with(['allSupervisors' => function($query) {
        $query->with('immediateSupervisor');
    }])->where('user_id', $userId)->first();

    $to_bottom = Employee::where('status', 'Active')
    ->where('immediate_sup', $userId)
    ->with(['subordinates' => function($query) {
        $query->where('status', 'Active')->with('subordinates');
    }])
    ->get();
    // Get the immediate supervisor
    
    $datas = [];
    if ($to_bottom) {
        processSubordinates($to_bottom, $datas);
    }
    if ($to_top) {
        addEmployeeToDatas($to_top, $to_top->immediate_sup, $datas);
    
        if ($to_top->immediateSupervisor) {
            processApprovers($to_top->immediateSupervisor, $to_top->immediateSupervisor->immediate_sup, $datas);
        } elseif ($to_top->immediateSupervisor && $to_top->status === "Active") {
            $datas[] = (object)[
                'id' => $to_top->immediate_sup,
                'pid' => null,
                'name' => $to_top->first_name . ' ' . $to_top->last_name,
                'position' => $to_top->position,
                'img' => $to_top->avatar ? asset($to_top->avatar) : null,
            ];
        }
    } else {
        // Fallback case for self
        $datas[] = (object)[
            'id' => $employee->id,
            'pid' => null,
            'name' => $employee->first_name . ' ' . $employee->last_name,
            'position' => $employee->position,
            'img' => $employee->avatar ? asset($employee->avatar) : null,
        ];
    }
    return $datas;
}

function processSubordinates($subordinates, &$datas)
{
    foreach ($subordinates as $under) {
        if ($under->status === "Active") {
            $datas[] = (object)[
                "id" => $under->user_id,
                'pid' => $under->immediate_sup,
                'name' => $under->first_name . " " . $under->last_name,
                'position' => $under->position,
                'img' => $under->avatar ? asset($under->avatar) : null,
            ];
        }

        // Recursively process subordinates
        if ($under->subordinates) {
            processSubordinates($under->subordinates, $datas);
        }
       
    }
}

function addEmployeeToDatas($employee, $pid, &$datas)
{
    if ($employee->status === "Active") {
        $datas[] = (object)[
            'id' => $employee->user_id,
            'pid' => $pid,
            'name' => $employee->first_name . ' ' . $employee->last_name,
            'position' => $employee->position,
            'img' => $employee->avatar ? asset($employee->avatar) : null,
        ];
    }
}
$approvers_ids = [];
function processApprovers($approvers, $pid, &$datas)
{
    if ($approvers) {
        addEmployeeToDatas($approvers, $pid, $datas);

        if ($approvers->immediate_sup) {  
            processApprovers($approvers->immediateSupervisor, $approvers->immediateSupervisor->immediate_sup, $datas);
        } elseif ($approvers->immediateSupervisor && $approvers->status === "Active") {
            $datas[] = (object)[
                'id' => $approvers->user_id,
                'pid' => null,
                'name' => $approvers->first_name . ' ' . $approvers->last_name,
                'position' => $approvers->position,
                'img' => $approvers->avatar ? asset($approvers->avatar) : null,
            ];
        }
    }
}

function documentTypes() {
  $documentTypes = array(
    '1' => 'ID',
    '2' => 'Diploma',
    '3' => 'Transcript of Records',
    '4' => 'Original Clearance (NBI / Police / Barangay)',
    '5' => 'SSS',
    '6' => 'PAGIBIG',
    '7' => 'PHILHEALTH',
    '8' => 'Birth Certificate',
    '9' => 'Training Certificate',
    '10' => 'PRC License',
    '11' => 'Passport',
    '12' => 'Marriage Certificate',
    '13' => "Child's Birth Certificate",
    '14' => 'Certificate of Employment',
    '15' => 'BIR 2316',
    '16' => 'Medical Examination'
  );

  return $documentTypes;
}

function benefits() {
  $benefits = array(
    'SL' => 'Salary Loan',
    'EA' => 'Educational Assistance',
    'WG' => 'Wedding Gifts',
    'BA' => 'Bereavement Assistance',
    'HMO' => 'Health Card (HMO)'
  );

  return $benefits;
}

function pending_leave_count($approver_id){

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

function pending_overtime_count($approver_id){
    
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

function pending_ob_count($approver_id){
    
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

function pending_schedule_count($approver_id){
    return \App\ScheduleChangeRequest::whereHas('approver', function($q) use($approver_id) {
                $q->where('approver_id', $approver_id);
            })
            ->where('status', 'Pending')
            ->count();
}

function pending_offset_count($approver_id){
    return \App\OffsetRequest::whereHas('approver', function($q) use($approver_id) {
                $q->where('approver_id', $approver_id);
            })
            ->where('status', 'Pending')
            ->count();
}

// Employee
function pending_employee_count($userId = null)
{
    $userId = $userId ?? Auth::id(); // Use the passed ID or the logged-in user ID
    if ($userId == 593) {
        // For user 593: count Pending with is_review = 1
        return User::where('status', 'Pending')
                   ->where('is_review', 1)
                   ->count();
    }
    if ($userId == 875) {
        // For user 875: count Pending with is_review = NULL
        return User::where('status', 'Pending')
                   ->whereNull('is_review')
                   ->count();
    }
    return 0;
}

// Proof of Availment
function pending_hmo_count($approver_id){
    
    // Only allow if logged-in user is ID 586
    if (Auth::check() && Auth::id() == 17) {

        $today = date('Y-m-d');
        $from_date = date('Y-m-d', strtotime('-1 month', strtotime($today)));
        $to_date = date('Y-m-d');

        return Hmo::where('status', 'Pending')
            // ->whereBetween('created_at', [$from_date, $to_date])
            ->count();
    }

    // Return 0 (or null) if user is not ID 586
    return 0;
}

//clearance-functions
function for_clearance()
{
    $for_clearances = ExitClearanceSignatory::with('clearance')
    ->where('employee_id',auth()->user()->employee->id)
    ->where('status','Pending')
    ->where('deleted_at', null)
    ->count();

    return $for_clearances;
}
function for_setup()
{
    $exit = ExitResign::whereDoesntHave('exit_clearance')->count();

    return $exit;
}
function ongoing_clearance()
{
    $exit = ExitResign::where('status','Ongoing Clearance')->count();

    return $exit;
}
function cleared()
{
    $exit = ExitResign::where('status','Cleared')->count();

    return $exit;
}
function ongoing_computation()
{
    $exit = ExitResign::where('status','Ongoing Computation')->count();

    return $exit;
}
function for_release()
{
    $exit = ExitResign::where('status','For Release')->count();

    return $exit;
}

function get_avatar($id)
{
    $avatar = Employee::findOrfail($id);
    $image = "https://hris.wsystem.online/".$avatar->avatar;

    return $image;
}

function usedSlVlThisYear($user_id, $leave_type, $date_hired,$scheduleDatas)
{
    // dd($user_id, $leave_type, $date_hired, $scheduleDatas);
    // dd($scheduleDatas);
    $count = 0;
    $all_days = [];
    $workingDays = [];
    if ($date_hired) {
        if(count($scheduleDatas) > 0)
        {
            $workingDays = $scheduleDatas->pluck('name')->toArray();
        }
        
        // Fetch the employee_number from the Employee model
        $employee = Employee::where('user_id', $user_id)->first();
        if (empty($employee)) {
            return $count; // If no employee found, return the count as 0
        }
        
        $employee_number = $employee->employee_number;
        
        $employee_leave = EmployeeLeave::where('user_id', $user_id)
            ->where('leave_type', $leave_type)
            ->where(function ($query) {
                $query->where('status', 'Approved')
                    ->orWhere('status', 'Pending');
            })
            ->where('withpay',1)
            // ->whereYear('date_from', date('Y'))
            ->where(function($q) {
                $q->whereYear('date_from', date('Y'))->orWhereYear('date_from', date('Y', strtotime('+1 year')));
            })
            ->where('status','!=','Cancelled')
            ->whereYear('created_at', date('Y'))
            ->whereNull('is_previous_year')
            ->get();
        // // dd($employee_leave);
        if (count($employee_leave) > 0) 
        {
            foreach ($employee_leave as $leave) 
            {
                if ($leave->withpay == 1 && $leave->halfday == 1) 
                {
                    if (isLeaveCountableDay($leave->date_from, collect(), $scheduleDatas, $employee->location)) 
                    {
                        $count += 0.5;
                    }
                } else {
                    // Fetch daily schedules where log_date is within the leave date range
                    // $dailySchedules = DailySchedule::select('log_date')->where('employee_number', $employee_number)
                    //     ->whereBetween('log_date', [$leave->date_from, $leave->date_to])
                    //     ->get()
                    //     ->pluck('log_date')
                    //     ->toArray();
                    // dd($dailySchedules);
                    // // // Iterate through each date in the date range
                    $date_range = dateRangeHelperLeaveCount($leave->date_from, $leave->date_to);
                    // // dd($date_range);
                    if (count($date_range) > 0) {
                        foreach ($date_range as $date_r) {
                            $leave_Date = date('Y-m-d', strtotime($date_r));
                            // // Check if withpay is 1 and leave_Date is valid
                            if ($leave->withpay == 1) {
                                $daily = DailySchedule::where('employee_code', $employee->employee_code)
                                    ->whereDate('log_date', $leave_Date)
                                    ->first();

                                if (isLeaveCountableDay($leave_Date, collect($daily ? [$daily] : []), $scheduleDatas, $employee->location)) {
                                    $count += getLeaveIncrementForDay($leave_Date, $scheduleDatas, collect($daily ? [$daily] : []));
                                    $all_days[] = $leave_Date;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    return $count;
}

function countPreviousVLUsed($user_id, $leave_type, $date_hired,$scheduleDatas = [])
{
    $count = 0;
    $all_days = [];
    $workingDays = [];
    if ($date_hired) {
        if($scheduleDatas != [])
        {
            $workingDays = $scheduleDatas->pluck('name')->toArray();
        }

        // Fetch the employee_number from the Employee model
        $employee = Employee::where('user_id', $user_id)->first();
        if (!$employee) {
            return $count; // If no employee found, return the count as 0
        }
        $employee_number = $employee->employee_number;

        $employee_sl = EmployeeLeave::where('user_id', $user_id)
            ->where('leave_type', $leave_type)
            ->where(function ($query) {
                $query->where('status', 'Approved')
                    ->orWhere('status', 'Pending');
            })
            ->where('withpay',1)
            ->where('is_previous_year', 1)
            ->where('status','!=','Cancelled')
            ->get();
            
        if ($employee_sl) {
            foreach ($employee_sl as $leave) {
                if ($leave->withpay == 1 && $leave->halfday == 1) {
                    if (isLeaveCountableDay($leave->date_from, collect(), $scheduleDatas, $employee->location)) {
                        $count += 0.5;
                    }
                } else {
                    // Fetch daily schedules where log_date is within the leave date range
                    $dailySchedules = DailySchedule::where('employee_number', $employee_number)
                        ->whereBetween('log_date', [$leave->date_from, $leave->date_to])
                        ->get();
                    
                    // // // Iterate through each date in the date range
                    $date_range = dateRangeHelperLeaveCount($leave->date_from, $leave->date_to);
                    
                    if ($date_range) {
                        
                        foreach ($date_range as $date_r) {
                            $leave_Date = date('Y-m-d', strtotime($date_r));
                            // Check if withpay is 1 and leave_Date is valid
                            if ($leave->withpay == 1) {
                                if (isLeaveCountableDay($leave_Date, $dailySchedules, $scheduleDatas, $employee->location)) {
                                    $count++;
                                    $all_days[]=$leave_Date;
                                }
                            }
                        }
                    }
                    
                }
            }
        }
    }
    
    return $count;
}

function get_leave_entitlement($level, $date_hired, $company)
{
    $rank_level = Level::where('id', $level)->first();
    $hired_date = $date_hired;
    $date_now = new DateTime(date('Y-m-d'));
    $date_hired = new DateTime($date_hired);
    $date_diff = $date_now->diff($date_hired);
    
    $leave_entitlement = 0;
    $plant_company = [5,11,12];
    if ($rank_level->name == 'RANK&FILE')
    {
        if (in_array($company, $plant_company))
        {
            if (date('Y-m', strtotime($hired_date)) > '2015-04')
            {
                if ($date_diff->y < 1)
                {
                    $leave_entitlement =  5;
                }
                elseif ($date_diff->y >= 1 && $date_diff->y < 3)
                {
                    $leave_entitlement =  5;
                }
                elseif($date_diff->y >= 3 && $date_diff->y < 5)
                {
                    $leave_entitlement = 6;
                }
                elseif($date_diff->y >= 5 && $date_diff->y < 10)
                {
                    $leave_entitlement = 7;
                }
                elseif($date_diff->y >= 10 && $date_diff->y < 15)
                {
                    $leave_entitlement = 8;
                }
                elseif($date_diff->y >= 15 && $date_diff->y < 20)
                {
                    $leave_entitlement = 9;
                }
                elseif($date_diff->y >= 20)
                {
                    $leave_entitlement = 10;
                }
            }
            else 
            {
                if ($date_diff->y < 1)
                {
                    $leave_entitlement =  8;
                }
                elseif ($date_diff->y >= 1 && $date_diff->y < 3)
                {
                    $leave_entitlement =  8;
                }
                elseif($date_diff->y >= 3 && $date_diff->y < 5)
                {
                    $leave_entitlement = 8;
                }
                elseif($date_diff->y >= 5 && $date_diff->y < 10)
                {
                    $leave_entitlement = 10;
                }
                elseif($date_diff->y >= 10 && $date_diff->y < 15)
                {
                    $leave_entitlement = 11;
                }
                elseif($date_diff->y >= 15 && $date_diff->y < 20)
                {
                    $leave_entitlement = 12;
                }
                elseif($date_diff->y >= 20)
                {
                    $leave_entitlement = 13;
                }
            }
        }
        else
        {
            // Head Office
            if ($date_diff->y < 1)
            {
                $leave_entitlement =  10;
            }
            elseif ($date_diff->y >= 1 && $date_diff->y < 3)
            {
                $leave_entitlement =  10;
            }
            elseif($date_diff->y >= 3 && $date_diff->y < 5)
            {
                $leave_entitlement = 12;
            }
            elseif($date_diff->y >= 5 && $date_diff->y < 10)
            {
                $leave_entitlement = 15;
            }
            elseif($date_diff->y >= 10 && $date_diff->y < 15)
            {
                $leave_entitlement = 16;
            }
            elseif($date_diff->y >= 15 && $date_diff->y < 20)
            {
                $leave_entitlement = 17;
            }
            elseif($date_diff->y >= 20)
            {
                $leave_entitlement = 18;
            }
        }
    }
    elseif($rank_level->name == 'SUPERVISOR')
    {
        if (in_array($company, $plant_company))
        {
            if (date('Y-m', strtotime($hired_date)) > '2015-04')
            {
                if ($date_diff->y < 1)
                {
                    $leave_entitlement =  8;
                }
                elseif ($date_diff->y >= 1 && $date_diff->y < 3)
                {
                    $leave_entitlement =  8;
                }
                elseif($date_diff->y >= 3 && $date_diff->y < 5)
                {
                    $leave_entitlement = 8;
                }
                elseif($date_diff->y >= 5 && $date_diff->y <10)
                {
                    $leave_entitlement = 10;
                }
                elseif($date_diff->y >= 10 && $date_diff->y < 15)
                {
                    $leave_entitlement = 11;
                }
                elseif($date_diff->y >= 15 && $date_diff->y < 20)
                {
                    $leave_entitlement = 12;
                }
                elseif($date_diff->y >= 20)
                {
                    $leave_entitlement = 13;
                }
            }
            else 
            {
                if ($date_diff->y < 1)
                {
                    $leave_entitlement =  10;
                }
                elseif ($date_diff->y >= 1 && $date_diff->y < 3)
                {
                    $leave_entitlement = 10;
                }
                elseif($date_diff->y >= 3 && $date_diff->y < 5)
                {
                    $leave_entitlement = 12;
                }
                elseif($date_diff->y >= 5 && $date_diff->y <10)
                {
                    $leave_entitlement = 15;
                }
                elseif($date_diff->y >= 10 && $date_diff->y < 15)
                {
                    $leave_entitlement = 16;
                }
                elseif($date_diff->y >= 15 && $date_diff->y < 20)
                {
                    $leave_entitlement = 17;
                }
                elseif($date_diff->y >= 20)
                {
                    $leave_entitlement = 18;
                }
            }
            
        }
        else
        {
            if ($date_diff->y < 1)
            {
                $leave_entitlement =  12;
            }
            elseif ($date_diff->y >= 1 && $date_diff->y < 3)
            {
                $leave_entitlement =  12;
            }
            elseif($date_diff->y >= 3 && $date_diff->y < 5)
            {
                $leave_entitlement = 12;
            }
            elseif($date_diff->y >= 5 && $date_diff->y <10)
            {
                $leave_entitlement = 15;
            }
            elseif($date_diff->y >= 10 && $date_diff->y < 15)
            {
                $leave_entitlement = 16;
            }
            elseif($date_diff->y >= 15 && $date_diff->y < 20)
            {
                $leave_entitlement = 17;
            }
            elseif($date_diff->y >= 20)
            {
                $leave_entitlement = 18;
            }
        }
    }
    elseif($rank_level->name == 'MANAGER')
    {
        if (in_array($company, $plant_company))
        {
            if (date('Y-m', strtotime($hired_date)) > '2015-04')
            {
                if ($date_diff->y < 1)
                {
                    $leave_entitlement =  15;
                }
                elseif ($date_diff->y >= 1 && $date_diff->y < 3)
                {
                    $leave_entitlement =  15;
                }
                elseif($date_diff->y >= 3 && $date_diff->y < 5)
                {
                    $leave_entitlement = 15;
                }
                elseif($date_diff->y >= 5 && $date_diff->y < 10)
                {
                    $leave_entitlement = 17;
                }
                elseif($date_diff->y >= 10 && $date_diff->y < 15)
                {
                    $leave_entitlement = 18;
                }
                elseif($date_diff->y >= 15 && $date_diff->y < 20)
                {
                    $leave_entitlement = 19;
                }
                elseif($date_diff->y >= 20)
                {
                    $leave_entitlement = 20;
                }
            }
            else 
            {
                if ($date_diff->y < 1)
                {
                    $leave_entitlement =  15;
                }
                elseif ($date_diff->y >= 1 && $date_diff->y < 3)
                {
                    $leave_entitlement =  15;
                }
                elseif($date_diff->y >= 3 && $date_diff->y < 5)
                {
                    $leave_entitlement = 16;
                }
                elseif($date_diff->y >= 5 && $date_diff->y < 10)
                {
                    $leave_entitlement = 17;
                }
                elseif($date_diff->y >= 10 && $date_diff->y < 15)
                {
                    $leave_entitlement = 18;
                }
                elseif($date_diff->y >= 15 && $date_diff->y < 20)
                {
                    $leave_entitlement = 19;
                }
                elseif($date_diff->y >= 20)
                {
                    $leave_entitlement = 20;
                }
            }
        }
        else
        {
            if ($date_diff->y < 1)
            {
                $leave_entitlement =  15;
            }
            elseif ($date_diff->y >= 1 && $date_diff->y < 3)
            {
                $leave_entitlement =  15;
            }
            elseif($date_diff->y >= 3 && $date_diff->y < 5)
            {
                $leave_entitlement = 15;
            }
            elseif($date_diff->y >= 5 && $date_diff->y < 10)
            {
                $leave_entitlement = 17;
            }
            elseif($date_diff->y >= 10 && $date_diff->y < 15)
            {
                $leave_entitlement = 18;
            }
            elseif($date_diff->y >= 15 && $date_diff->y < 20)
            {
                $leave_entitlement = 19;
            }
            elseif($date_diff->y >= 20)
            {
                $leave_entitlement = 20;
            }
        }
    }
    
    return $leave_entitlement;
}

function compute_leave_credits($leave,$leave_entitlement,$date_hired,$date_regularization)
{
    $leave_type = Leave::where('id', $leave)->first();

    if ($leave_type->code == 'VL')
    {
        $date_regular = new DateTime($date_regularization);
        $end_date = new DateTime(date('Y').'-12-31');
        $count_days = $end_date->diff($date_regular);

        $days = $count_days->days+1;
        $total_vl_credits = (int)$days / 365 * (int)$leave_entitlement;

        return round($total_vl_credits,2);
    }
    elseif($leave_type->code == 'SL')
    {
        $date_regular = new DateTime($date_regularization);
        $end_date = new DateTime(date('Y').'-12-31');
        $count_days = $end_date->diff($date_regular);
        $days = $count_days->days+1;
        
        $total_vl_credits = (int)$days / 365 * (int)$leave_entitlement;

        return round($total_vl_credits,2);
    }
}

function earn_per_month($leave,$date_regularization="", $leave_entitlement)
{
    $leave_type = Leave::where('id', $leave)->first();
    
    if ($leave_type->code == 'VL')
    {
        $date_regular = new DateTime($date_regularization);
        $end_date = new DateTime(date('Y-m-t'));
        $count_days = $end_date->diff($date_regular);

        $days = $count_days->days+1;
        $total_earned = (int)$days / 365 * (int)$leave_entitlement;

        return round($total_earned, 2);
    }
    elseif($leave_type->code == 'SL')
    {
        $date_regular = new DateTime($date_regularization);
        $end_date = new DateTime(date('Y').'-12-31');
        $count_days = $end_date->diff($date_regular);

        $days = $count_days->days;
        $total_earned = (int)$days / 365;

        return round($total_earned, 2);
    }
}

function checkUsedPvl($id, $vl,$prev_vl,$scheduleData)
{
    $used_prev_vl = EmployeeLeave::with('employee.daily_schedules')
                                    ->where('leave_type', $vl)
                                    ->where('status','Approved')
                                    ->where('user_id', $id)
                                    ->where(function($query) {
                                        $query->whereYear('date_from', date('Y', strtotime('-1 year')))
                                            ->orWhereYear('date_from',date('Y'));
                                    })
                                    ->whereYear('created_at', date('Y', strtotime('-1 year')))
                                    ->whereNull('is_previous_year')
                                    ->get();

    $used_pvl = EmployeeLeave::where('leave_type',$prev_vl)
                                ->whereIn('status',['Pending','Approved'])
                                ->where('user_id',$id)
                                ->whereYear('date_from', date('Y'))
                                ->where('is_previous_year',1)
                                ->get();
    
    $count = 0;
    if(count($scheduleData) > 0)
    {
        $workingDays = $scheduleData->pluck('name')->toArray();
    }
    $all_days=[];
    foreach($used_prev_vl as $pvl)
    {
        if ($pvl->halfday == 1 && $pvl->withpay == 1)
        {
            if (isLeaveCountableDay($pvl->date_from, collect(), $scheduleData, optional($pvl->employee)->location)) {
                $count += 0.5;
            }
        }
        else 
        {
            $dailySchedules = ($pvl->employee->daily_schedules)->where('log_date','>=',$pvl->date_from)->where('log_date',"<=",$pvl->date_to)->unique('log_date');
            $dateRanges = dateRangeHelperLeaveCount($pvl->date_from, $pvl->date_to);
            if($dateRanges)
            {
                foreach ($dateRanges as $dateRange) {
                    $leaveDate = date('Y-m-d', strtotime($dateRange));
                    if (!isLeaveCountableDay($leaveDate, $dailySchedules, $scheduleData, optional($pvl->employee)->location)) {
                        continue;
                    }
                    if($pvl->withpay == 1)
                    {
                        $d = $dailySchedules->where('log_date',$leaveDate)->first();
                        
                        if ($d)
                        {
                            foreach($dailySchedules as $dailySched)
                            {
                                $logDate = $dailySched->log_date ? date('Y-m-d',strtotime($dailySched->log_date)) : null;
    
                                if($logDate === $leaveDate)
                                {
                                    if ($dailySched->working_hours)
                                    {
                                        $count++;
                                        $all_days[]=$leaveDate;
                                    }
                                }
                            }
                        }
                        else 
                        {
                            $dayName = date('l', strtotime($leaveDate));
                            if(in_array($dayName, $workingDays))
                            {
                                $count++;
                            }
                        }
                    }
                    // if(in_array($leaveDate, $workingDays))
                    // {
                    //     $count++;
                    // }
                }
            }
        }
    }

    $count_pvl=0;
    foreach($used_pvl as $pvl)
    {
        if ($pvl->halfday == 1)
        {
            if (isLeaveCountableDay($pvl->date_from, collect(), $scheduleData, optional($pvl->employee)->location)) {
                $count_pvl += 0.5;
            }
        }
        else 
        {
            // $dateRanges = dateRangeHelperLeaveCount($pvl->date_from, $pvl->date_to);
            // foreach ($dateRanges as $dateRange) {
            //     $count_pvl++;
            // }
            $dailySchedules = ($pvl->employee->daily_schedules)->where('log_date','>=',$pvl->date_from)->where('log_date',"<=",$pvl->date_to)->unique('log_date');
            $dateRanges = dateRangeHelperLeaveCount($pvl->date_from, $pvl->date_to);
            if ($dateRanges)
            {
                foreach ($dateRanges as $dateRange) {
                    $leaveDate = date('Y-m-d', strtotime($dateRange));
                    if (!isLeaveCountableDay($leaveDate, $dailySchedules, $scheduleData, optional($pvl->employee)->location)) {
                        continue;
                    }
                    if($pvl->withpay == 1)
                    {
                        $d = $dailySchedules->where('log_date',$leaveDate)->first();
                        
                        if ($d)
                        {
                            foreach($dailySchedules as $dailySched)
                            {
                                $logDate = $dailySched->log_date ? date('Y-m-d',strtotime($dailySched->log_date)) : null;
    
                                if($logDate === $leaveDate)
                                {
                                    if ($dailySched->working_hours)
                                    {
                                        $count_pvl++;
                                        $all_days[]=$leaveDate;
                                    }
                                }
                            }
                        }
                        else 
                        {
                            $dayName = date('l', strtotime($leaveDate));
                            if(in_array($dayName, $workingDays))
                            {
                                $count++;
                            }
                        }
                    }
                }
            }
        }
    }
    
    $total_count = $count + $count_pvl;
    return $total_count;
}

function checkUsedPsl($id, $sl,$prev_sl, $scheduleData)
{
    $used_prev_sl = EmployeeLeave::with('employee.daily_schedules')->where('leave_type', $sl)
                                    ->where('status','Approved')
                                    ->where('user_id', $id)
                                    // // ->where('date_from', 'LIKE', "%".date('Y')."%")
                                    ->where(function($query) {
                                        $query->whereYear('date_from', date('Y', strtotime('-1 year')))
                                            ->orWhereYear('date_from',date('Y'));
                                    })
                                    ->whereYear('date_from', date('Y', strtotime('-1 year')))
                                    ->whereNull('is_previous_year')
                                    ->where('withpay', 1)
                                    ->get();

    $used_psl = EmployeeLeave::where('leave_type',$prev_sl)
                                ->whereIn('status',['Pending','Approved'])
                                ->where('user_id',$id)
                                ->whereYear('date_from', date('Y'))
                                ->where('is_previous_year',1)
                                ->get();
    
    $count = 0;
    if(count($scheduleData) > 0)
    {
        $workingDays = $scheduleData->pluck('name')->toArray();
    }

    $all_days=[];
    foreach($used_prev_sl as $psl)
    {
        if ($psl->halfday == 1 && $psl->withpay == 1)
        {
            if (isLeaveCountableDay($psl->date_from, collect(), $scheduleData, optional($psl->employee)->location)) {
                $count += 0.5;
            }
        }
        else 
        {
            $dailySchedules = ($psl->employee->daily_schedules)->where('log_date','>=',$psl->date_from)->where('log_date',"<=",$psl->date_to)->unique('log_date');
            $dateRanges = dateRangeHelperLeaveCount($psl->date_from, $psl->date_to);
            if($dateRanges)
            {
                foreach ($dateRanges as $dateRange) {
                    $leaveDate = date('Y-m-d', strtotime($dateRange));
                    if (!isLeaveCountableDay($leaveDate, $dailySchedules, $scheduleData, optional($psl->employee)->location)) {
                        continue;
                    }
                    if($psl->withpay == 1)
                    {
                        $d = $dailySchedules->where('log_date',$leaveDate)->first();
                        
                        if ($d)
                        {
                            foreach($dailySchedules as $dailySched)
                            {
                                $logDate = $dailySched->log_date ? date('Y-m-d',strtotime($dailySched->log_date)) : null;
    
                                if($logDate === $leaveDate)
                                {
                                    if ($dailySched->working_hours)
                                    {
                                        $count++;
                                        $all_days[]=$leaveDate;
                                    }
                                }
                            }
                        }
                        else 
                        {
                            $dayName = date('l', strtotime($leaveDate));
                            if(in_array($dayName, $workingDays))
                            {
                                $count++;
                            }
                        }
                    }
                }
            }
        }
    }

    $count_psl=0;
    foreach($used_psl as $psl)
    {
        if ($psl->halfday == 1)
        {
            if (isLeaveCountableDay($psl->date_from, collect(), $scheduleData, optional($psl->employee)->location)) {
                $count_psl += 0.5;
            }
        }
        else 
        {
            $dateRanges = dateRangeHelperLeaveCount($psl->date_from, $psl->date_to);
            foreach ($dateRanges as $dateRange) {
                if (isLeaveCountableDay($dateRange, collect(), $scheduleData, optional($psl->employee)->location)) {
                    $count_psl++;
                }
            }
        }
    }
    
    $total_count = $count + $count_psl;
    return $total_count;
}

function employeeScheduleV2($schedules = array(), $dailySchedule=array(), $check_date, $schedule_id, $empNum=""){
    if (count($dailySchedule) > 0){
        foreach($dailySchedule as $item){
            if ($item['log_date'] == $check_date && $item['employee_code'] == $empNum) {
                return $item;
            }
        }
    }
    
    $schedule_name = date('l',strtotime($check_date));
    if(count($schedules) > 0){
        foreach($schedules as $item){
            if (isset($item['schedule_id'], $item['name'])) {
                if($item['schedule_id'] == $schedule_id && $item['name'] == $schedule_name){
                    return $item;
                }
            }
        }
    }
}

function employeePendingLeave($leaves=array(),$date)
{
    $status = 0;
    foreach($leaves as $leave)
    {
        if (date('Y-m-d', strtotime($leave->date_from)) == $date)
        {
            if ($leave->halfday == 0)
            {
                $status = 1;
            }
            else 
            {
                $status = 0.5;
            }
        }
    }

    return $status;
}

function employeePendingOvertime($overtime=array(),$date)
{
    $status = 0;
    foreach($overtime as $ot)
    {
        if (date('Y-m-d', strtotime($ot->ot_date)) == $date)
        {
            $status = 1;
        }
    }

    return $status;
}

function employeePendingObs($obs=array(),$date)
{
    $status = 0;
    foreach($obs as $ob)
    {
        if (date('Y-m-d', strtotime($ob->applied_date)) == $date)
        {
            $status = 1;
        }
    }

    return $status;
}
