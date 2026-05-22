<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TradingErpSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'Admin', 'code' => 'admin'],
            ['name' => 'Purchase Manager', 'code' => 'purchase_manager'],
            ['name' => 'Purchase Staff', 'code' => 'purchase_staff'],
            ['name' => 'Sales Manager', 'code' => 'sales_manager'],
            ['name' => 'Sales Staff', 'code' => 'sales_staff'],
            ['name' => 'Warehouse Staff', 'code' => 'warehouse_staff'],
            ['name' => 'Finance / Accounting Staff', 'code' => 'finance_accounting_staff'],
            ['name' => 'Management / Approver', 'code' => 'management_approver'],
        ];

        foreach ($roles as $role) {
            DB::table('erp_roles')->updateOrInsert(['code' => $role['code']], array_merge($role, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        foreach (businessModules() as $module) {
            foreach (['create', 'read', 'update', 'delete', 'approve', 'export', 'print'] as $action) {
                DB::table('erp_permissions')->updateOrInsert(
                    ['code' => $module['slug'] . '.' . $action],
                    [
                        'module' => $module['slug'],
                        'action' => $action,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $sequences = [
            'supplier' => 'SUP',
            'customer' => 'CUS',
            'item' => 'ITM',
            'purchase_requisition' => 'PR',
            'rfq' => 'RFQ',
            'purchase_order' => 'PO',
            'goods_receipt' => 'GRN',
            'supplier_invoice' => 'SINV',
            'sales_order' => 'SO',
            'delivery' => 'DN',
            'sales_invoice' => 'INV',
            'supplier_payment' => 'PAY',
            'customer_receipt' => 'REC',
            'credit_note' => 'CN',
            'debit_note' => 'DN',
        ];

        foreach ($sequences as $type => $prefix) {
            DB::table('document_sequences')->updateOrInsert(
                ['document_type' => $type],
                [
                    'prefix' => $prefix,
                    'next_number' => 1,
                    'padding' => 6,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        DB::table('system_settings')->updateOrInsert(['setting_key' => 'allow_negative_stock'], [
            'setting_value' => '0',
            'setting_group' => 'inventory',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('system_settings')->updateOrInsert(['setting_key' => 'allow_over_receipt'], [
            'setting_value' => '0',
            'setting_group' => 'purchasing',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('system_settings')->updateOrInsert(['setting_key' => 'invoice_after_proof_of_delivery'], [
            'setting_value' => '1',
            'setting_group' => 'sales',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('item_categories')->updateOrInsert(['code' => 'GEN'], [
            'name' => 'General Trading',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('tax_codes')->updateOrInsert(['code' => 'VAT12'], [
            'name' => 'VAT 12%',
            'rate' => 12,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
