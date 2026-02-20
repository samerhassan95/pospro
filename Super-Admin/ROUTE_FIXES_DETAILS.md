# Route Fixes and Implementation Details

This document tracks the restoration of broken/missing routes in the sidebar. The logic for many of these reports was found in `App\Http\Controllers\Api\AcnooReportController.php` and is being ported to Web Controllers in the `Modules\Business` namespace.

## 1. Party Reports (Ledger) - ✅ Completed

- **Status:** Fixed.
- **Route:** `business.parties.ledger`
- **Implementation:**
    - Added `ledger` method to `AcnooPartyController`.
    - Created view `parties.ledger`.
    - Linked Sidebar "Customer Ledger" & "Supplier Ledger" to `parties.index` (with type filter) -> User selects party -> clicks "Ledger".

## 2. Product Reports - 🚧 In Progress

These reports were commented out in the sidebar.

### A. Top 5 Reports (Customer, Supplier, Product)

- **Status:** Pending Implementation.
- **Plan:**
    - Create `AcnooTopReportController`.
    - Implement `topCustomers`, `topSuppliers`, `topProducts`.
    - Create Views: `reports.top.customers`, `reports.top.suppliers`, `reports.top.products`.
    - **Logic Source:** Custom query (Order by `totalAmount` desc, take 5).

### B. Product History (Sale & Purchase)

- **Status:** Pending Implementation.
- **Plan:**
    - Create `AcnooProductHistoryReportController`.
    - Implement `productSaleHistory` (Logic from API `productSaleHistory`).
    - Implement `productPurchaseHistory` (Logic from API `productPurchaseHistory`).
    - Create Views.
    - **Logic Source:** `App\Http\Controllers\Api\AcnooReportController.php`.

### C. Discount & Combo Product Reports

- **Status:** Pending Implementation.
- **Plan:**
    - Add methods to `AcnooProductReportController` or use filters on existing product report.

## 3. Commission Reports - 🚧 In Progress

- **Status:** Missing / Needs Development.
- **Plan:**
    - Search for existing logic (failed so far).
    - If not found, implement basic structure:
        - `Set Commissions`: Table to set commission % per user/role or product.
        - `Sale Commission`: Report calculating commission based on sales.

## 4. Other Reports

- **Tax Report (Vat):**
    - Logic found in `taxReport` (API).
    - Check if `AcnooVatReportController` exists.

## Implementation Log

| Feature                  | Controller                      | Route Name                                | Status     |
| :----------------------- | :------------------------------ | :---------------------------------------- | :--------- |
| Customer Ledger          | `AcnooPartyController`          | `business.parties.ledger`                 | ✅ Done    |
| Supplier Ledger          | `AcnooPartyController`          | `business.parties.ledger`                 | ✅ Done    |
| Top 5 Customers          | `AcnooTopReportController`      | `business.top-customers.index`            | ⏳ Pending |
| Top 5 Suppliers          | `AcnooTopReportController`      | `business.top-suppliers.index`            | ⏳ Pending |
| Product Sale History     | `AcnooProductHistoryController` | `business.product-sale-history.index`     | ⏳ Pending |
| Product Purchase History | `AcnooProductHistoryController` | `business.product-purchase-history.index` | ⏳ Pending |
