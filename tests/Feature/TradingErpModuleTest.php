<?php

namespace Tests\Feature;

use Tests\TestCase;

class TradingErpModuleTest extends TestCase
{
    public function test_business_module_registry_has_all_required_modules()
    {
        $modules = businessModules();

        $this->assertCount(29, $modules);
        $this->assertEquals('Supplier Master', $modules->firstWhere('slug', 'supplier-master')['name']);
        $this->assertEquals('Reporting and Analytics', $modules->firstWhere('slug', 'reporting-and-analytics')['name']);
    }

    public function test_erp_navigation_groups_include_core_workflows()
    {
        $groups = erpNavigationGroups();

        $this->assertArrayHasKey('Masters', $groups);
        $this->assertArrayHasKey('Purchasing', $groups);
        $this->assertArrayHasKey('Sales', $groups);
        $this->assertArrayHasKey('Inventory', $groups);
        $this->assertArrayHasKey('Returns', $groups);
        $this->assertArrayHasKey('Reports', $groups);
    }
}
