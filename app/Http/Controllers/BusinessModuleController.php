<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class BusinessModuleController extends Controller
{
    public function index()
    {
        $header = 'business_modules';
        $modules = businessModules();
        $groups = erpNavigationGroups();
        $metrics = $this->dashboardMetrics();

        return view('business_modules.index', compact('header', 'modules', 'groups', 'metrics'));
    }

    public function show($slug)
    {
        $module = businessModules()->firstWhere('slug', $slug);

        abort_unless($module, 404);

        $header = 'business_modules';
        $modules = businessModules();
        $groups = erpNavigationGroups();

        return view('business_modules.show', compact('header', 'module', 'modules', 'groups'));
    }

    public function master($master, Request $request)
    {
        $definition = $this->masterDefinition($master);
        abort_unless($definition, 404);

        $header = 'business_modules';
        $query = DB::table($definition['table']);

        if (Schema::hasColumn($definition['table'], 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        if ($request->search) {
            $query->where(function ($q) use ($definition, $request) {
                foreach ($definition['search'] as $field) {
                    $q->orWhere($field, 'like', '%' . $request->search . '%');
                }
            });
        }

        if ($request->status && Schema::hasColumn($definition['table'], 'status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderBy('id', 'desc')->paginate(15);

        return view('business_modules.master', compact('header', 'definition', 'records', 'master'));
    }

    public function storeMaster($master, Request $request)
    {
        $definition = $this->masterDefinition($master);
        abort_unless($definition, 404);

        $request->validate($this->validationRules($definition));
        $data = $this->masterPayload($definition, $request);
        $data['created_by'] = auth()->user()->id;
        $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table($definition['table'])->insert($data);
        $this->audit('Create', $definition['title'], null, null, $data);

        Alert::success($definition['title'] . ' created.')->persistent('Dismiss');
        return back();
    }

    public function updateMaster($master, $id, Request $request)
    {
        $definition = $this->masterDefinition($master);
        abort_unless($definition, 404);

        $request->validate($this->validationRules($definition, $id));
        $old = DB::table($definition['table'])->where('id', $id)->first();
        $data = $this->masterPayload($definition, $request);
        $data['updated_by'] = auth()->user()->id;
        $data['updated_at'] = now();

        DB::table($definition['table'])->where('id', $id)->update($data);
        $this->audit('Update', $definition['title'], $id, $old, $data);

        Alert::success($definition['title'] . ' updated.')->persistent('Dismiss');
        return back();
    }

    public function destroyMaster($master, $id)
    {
        $definition = $this->masterDefinition($master);
        abort_unless($definition, 404);

        $old = DB::table($definition['table'])->where('id', $id)->first();
        if (Schema::hasColumn($definition['table'], 'deleted_at')) {
            DB::table($definition['table'])->where('id', $id)->update([
                'deleted_at' => now(),
                'updated_by' => auth()->user()->id,
                'updated_at' => now(),
            ]);
        } else {
            DB::table($definition['table'])->where('id', $id)->delete();
        }

        $this->audit('Delete', $definition['title'], $id, $old, null);

        Alert::success($definition['title'] . ' removed.')->persistent('Dismiss');
        return back();
    }

    private function dashboardMetrics()
    {
        return [
            'sales_this_month' => $this->sumIfTableExists('sales_invoices', 'total_amount'),
            'purchases_this_month' => $this->sumIfTableExists('purchase_orders', 'total_amount'),
            'outstanding_receivables' => $this->sumIfTableExists('accounts_receivable', 'outstanding_amount'),
            'outstanding_payables' => $this->sumIfTableExists('accounts_payable', 'outstanding_amount'),
            'low_stock_items' => Schema::hasTable('items') ? DB::table('items')->whereColumn('available_stock_quantity', '<=', 'reorder_level')->count() : 0,
            'pending_approvals' => Schema::hasTable('approvals') ? DB::table('approvals')->where('action', 'pending')->count() : 0,
            'pending_deliveries' => Schema::hasTable('deliveries') ? DB::table('deliveries')->whereIn('status', ['draft', 'pending', 'dispatched'])->count() : 0,
            'recent_inventory_movements' => Schema::hasTable('inventory_movements') ? DB::table('inventory_movements')->orderBy('id', 'desc')->limit(8)->get() : collect(),
        ];
    }

    private function sumIfTableExists($table, $field)
    {
        if (!Schema::hasTable($table)) {
            return 0;
        }

        return DB::table($table)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum($field);
    }

    private function masterDefinition($master)
    {
        $definitions = [
            'suppliers' => [
                'title' => 'Supplier Master',
                'table' => 'suppliers',
                'search' => ['supplier_code', 'supplier_name', 'contact_person', 'email'],
                'fields' => [
                    ['name' => 'supplier_code', 'label' => 'Supplier Code', 'required' => true, 'unique' => true],
                    ['name' => 'supplier_name', 'label' => 'Supplier Name', 'required' => true],
                    ['name' => 'contact_person', 'label' => 'Contact Person'],
                    ['name' => 'phone', 'label' => 'Phone'],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
                    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea'],
                    ['name' => 'tax_id', 'label' => 'Tax ID / VAT / GST Number'],
                    ['name' => 'payment_terms', 'label' => 'Payment Terms'],
                    ['name' => 'bank_account_details', 'label' => 'Bank Account Details', 'type' => 'textarea'],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
                    ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea'],
                ],
            ],
            'customers' => [
                'title' => 'Customer Master',
                'table' => 'customers',
                'search' => ['customer_code', 'customer_name', 'contact_person', 'email'],
                'fields' => [
                    ['name' => 'customer_code', 'label' => 'Customer Code', 'required' => true, 'unique' => true],
                    ['name' => 'customer_name', 'label' => 'Customer Name', 'required' => true],
                    ['name' => 'billing_address', 'label' => 'Billing Address', 'type' => 'textarea'],
                    ['name' => 'delivery_address', 'label' => 'Delivery Address', 'type' => 'textarea'],
                    ['name' => 'contact_person', 'label' => 'Contact Person'],
                    ['name' => 'phone', 'label' => 'Phone'],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
                    ['name' => 'tax_id', 'label' => 'Tax ID / VAT / GST Number'],
                    ['name' => 'credit_limit', 'label' => 'Credit Limit', 'type' => 'number'],
                    ['name' => 'credit_terms', 'label' => 'Credit Terms'],
                    ['name' => 'outstanding_balance', 'label' => 'Outstanding Balance', 'type' => 'number'],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
                    ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea'],
                ],
            ],
            'items' => [
                'title' => 'Item / Product Master',
                'table' => 'items',
                'search' => ['item_code', 'product_name', 'description'],
                'fields' => [
                    ['name' => 'item_code', 'label' => 'Item Code', 'required' => true, 'unique' => true],
                    ['name' => 'product_name', 'label' => 'Product Name', 'required' => true],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                    ['name' => 'category_id', 'label' => 'Category ID', 'type' => 'number'],
                    ['name' => 'unit_of_measure', 'label' => 'Unit of Measure'],
                    ['name' => 'cost_price', 'label' => 'Cost Price', 'type' => 'number'],
                    ['name' => 'selling_price', 'label' => 'Selling Price', 'type' => 'number'],
                    ['name' => 'tax_code_id', 'label' => 'Tax Code ID', 'type' => 'number'],
                    ['name' => 'reorder_level', 'label' => 'Reorder Level', 'type' => 'number'],
                    ['name' => 'current_stock_quantity', 'label' => 'Current Stock', 'type' => 'number'],
                    ['name' => 'available_stock_quantity', 'label' => 'Available Stock', 'type' => 'number'],
                    ['name' => 'reserved_stock_quantity', 'label' => 'Reserved Stock', 'type' => 'number'],
                    ['name' => 'damaged_stock_quantity', 'label' => 'Damaged Stock', 'type' => 'number'],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
                ],
            ],
            'categories' => [
                'title' => 'Item Categories',
                'table' => 'item_categories',
                'search' => ['code', 'name'],
                'fields' => [
                    ['name' => 'code', 'label' => 'Code', 'required' => true, 'unique' => true],
                    ['name' => 'name', 'label' => 'Name', 'required' => true],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
                ],
            ],
            'tax-codes' => [
                'title' => 'Tax Codes',
                'table' => 'tax_codes',
                'search' => ['code', 'name'],
                'fields' => [
                    ['name' => 'code', 'label' => 'Code', 'required' => true, 'unique' => true],
                    ['name' => 'name', 'label' => 'Name', 'required' => true],
                    ['name' => 'rate', 'label' => 'Rate', 'type' => 'number'],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
                ],
            ],
        ];

        return $definitions[$master] ?? null;
    }

    private function validationRules($definition, $id = null)
    {
        $rules = [];
        foreach ($definition['fields'] as $field) {
            $fieldRules = [];
            if (!empty($field['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }
            if (($field['type'] ?? null) == 'email') {
                $fieldRules[] = 'email';
            }
            if (($field['type'] ?? null) == 'number') {
                $fieldRules[] = 'numeric';
            }
            if (!empty($field['unique'])) {
                $fieldRules[] = 'unique:' . $definition['table'] . ',' . $field['name'] . ($id ? ',' . $id : '');
            }
            $rules[$field['name']] = implode('|', $fieldRules);
        }

        return $rules;
    }

    private function masterPayload($definition, Request $request)
    {
        $payload = [];
        foreach ($definition['fields'] as $field) {
            $payload[$field['name']] = $request->input($field['name']);
        }

        return $payload;
    }

    private function audit($action, $module, $recordId, $oldValue, $newValue)
    {
        if (!Schema::hasTable('erp_audit_logs')) {
            return;
        }

        DB::table('erp_audit_logs')->insert([
            'user_id' => auth()->user()->id,
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'old_value' => $oldValue ? json_encode($oldValue) : null,
            'new_value' => $newValue ? json_encode($newValue) : null,
            'ip_address' => request()->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
