# Trading ERP Module Notes

This ERP area supports the 29 requested modules across masters, purchasing, inventory, sales, finance, returns, and reporting.

## Setup

Run:

```bash
php artisan migrate --path=database/migrations/2026_05_16_000001_create_trading_erp_tables.php
php artisan db:seed --class=TradingErpSeeder
```

## Implemented Foundation

- ERP dashboard and navigation.
- Master data CRUD for suppliers, customers, items/products, item categories, and tax codes.
- Normalized schema for the requested procurement, sales, inventory, finance, returns, audit, attachment, settings, role, and permission tables.
- Document sequences for common prefixes such as `SUP`, `CUS`, `ITM`, `PR`, `RFQ`, `PO`, `GRN`, `SINV`, `SO`, `DN`, `INV`, `PAY`, `REC`, `CN`.
- Seed data for ERP roles, module permissions, document numbering, basic settings, one category, and one tax code.

## Workflow Build Order

1. Supplier, customer, item, category, and tax master maintenance.
2. Inventory stocks and inventory movement posting services.
3. Purchase requisition to PO to GRN to supplier invoice to AP/payment.
4. Sales inquiry to quotation to SO to reservation/picking/delivery/POD/invoice/AR/collection.
5. Sales returns/credit notes and purchase returns/debit notes.
6. Reports and exports.

## Business Rules To Enforce In Services

- Stock-changing actions must post to `inventory_movements`.
- Financial actions must post to AP/AR tables and ERP audit logs.
- Document numbers must be generated through `document_sequences`.
- PO approval requires supplier and at least one item.
- GRN receipt must not exceed PO quantity unless setting allows over receipt.
- Supplier invoice posting requires PO/GRN/invoice matching unless override is approved.
- Sales order cannot reserve unavailable stock unless backorder setting allows it.
- Sales invoice should be generated after delivery or POD based on system setting.
