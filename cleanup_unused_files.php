<?php
/**
 * Cleanup Unused Files Script
 * This script removes documentation, test files, and backup files that are not needed in production
 * 
 * IMPORTANT: Review the list before running!
 * Usage: php cleanup_unused_files.php
 */

// Files to be removed (documentation and test files)
$filesToRemove = [
    // Documentation files created during development
    'ACTIONS_AND_MODALS_FIXED_AR.md',
    'ADD_BUTTON_FIX_AR.md',
    'ADD_TABLE_FIXED_AR.md',
    'ADMIN_CREATE_ACCOUNTS_GUIDE_AR.md',
    'ALL_ALERTS_REPLACED_AR.md',
    'ANSWER_TO_YOUR_QUESTION_AR.md',
    'AR_JSON_FIXED.md',
    'B2B_FIELDS_IMPLEMENTATION_COMPLETE.md',
    'B2B_IMPLEMENTATION_SUMMARY.md',
    'B2B_INVOICE_ENHANCEMENTS_COMPLETED.md',
    'B2B_INVOICE_FINAL_FIXES_AR.md',
    'B2B_INVOICE_MISSING_ELEMENTS_AR.md',
    'B2B_INVOICE_TEMPLATE_UPDATE_AR.md',
    'B2B_INVOICE_UPDATE.md',
    'B2B_UPDATES_FINAL_SUMMARY.md',
    'BACKEND_INTEGRATION_SUMMARY.md',
    'BARCODE_SCANNER_USAGE.md',
    'BRANCH_CREATION_FIX_AR.md',
    'CHANGES_SUMMARY_AR.md',
    'CHOICES_JS_FIX_AR.md',
    'CLEAR_BROWSER_CACHE_AR.md',
    'CLOUDPANEL_DOMAIN_SETUP_AR.md',
    'COLOR_THEMING_GUIDE.md',
    'COMPLETE_B2B_IMPLEMENTATION.md',
    'COMPLETE_GUIDE_VARIATIONS_AR.md',
    'COMPLETE_PAYMENT_ERROR_FIX_AR.md',
    'COMPLETE_SOLUTION_AR.md',
    'COMPLETE_SYSTEM_SUMMARY_AR.md',
    'CONVERSATION_SUMMARY_AR.md',
    'CR_NUMBER_DISPLAY_FIXED_AR.md',
    'CURRENCY_SYMBOL_GUIDE_AR.md',
    'CURRENCY_SYMBOL_UPDATE_COMPLETE_AR.md',
    'CURRENT_ISSUES_AR.md',
    'CURRENT_STATUS_AR.md',
    'CUSTOM_DOMAIN_FIX_AR.md',
    'CUSTOM_DOMAIN_FIXED_AR.md',
    'DASHBOARD_COLORS_UPDATED.md',
    'DASHBOARD_RECENT_SALES_SAR_FIX_AR.md',
    'DEPLOYMENT_GUIDE_COMPLETE.md',
    'DEPLOYMENT_GUIDE.md',
    'DEPLOYMENT_SUMMARY_AR.md',
    'DYNAMIC_DASHBOARD_BANNER_GUIDE.md',
    'DYNAMIC_FAVICON_AND_COMMON_HEADER_IMPLEMENTATION.md',
    'DYNAMIC_LOGOS_GUIDE.md',
    'DYNAMIC_VARIATIONS_IN_CATEGORY_AR.md',
    'EDIT_BUSINESS_B2B_FIELDS_COMPLETE_AR.md',
    'EXPOSE_LOCALHOST_FOR_TESTING_AR.md',
    'FINAL_ALL_ALERTS_DONE_AR.md',
    'FINAL_ANSWER_AR.md',
    'FINAL_DOMAIN_FIX_AR.md',
    'FINAL_EXPLANATION_AR.md',
    'FINAL_FIX_PLAN_AR.md',
    'FINAL_FIX_SUMMARY_AR.md',
    'FINAL_INVOICE_FIX_SUMMARY_AR.md',
    'FINAL_RESERVATION_SUMMARY_AR.md',
    'FINAL_SUMMARY.md',
    'FINAL_UPDATE_SUMMARY_AR.md',
    'FINAL_ZATCA_SAR_SUMMARY_AR.md',
    'FIX_400_ERROR_ON_SERVER_AR.md',
    'FIX_DOMAIN_400_ERROR_AR.md',
    'FIX_DOMAIN_ERROR_400.md',
    'FIX_MISSING_DATA_AR.md',
    'FORMS_UPDATED_AR.md',
    'GENERATE_VARIATIONS_FIXED_AR.md',
    'GUIDE_B2B_INVOICES_STEPS_AR.md',
    'HOW_TO_CREATE_B2B_INVOICE_AR.md',
    'HOW_TO_USE_BATCH_MODE_AR.md',
    'IMAGES_FIX_COMPLETE.md',
    'IMPLEMENTATION_COMPLETE.md',
    'INSTALLATION_B2B.md',
    'INVOICE_FIXES_AR.md',
    'LOCALSTORAGE_REMOVAL_COMPLETE.md',
    'LOCALSTORAGE_REPLACEMENT_INSTRUCTIONS.md',
    'LOGO_IMAGES_FIX_AR.md',
    'LOGO_SYSTEM_GUIDE.md',
    'LOGOS_FIXED_SUMMARY_AR.md',
    'MANAGE_RESERVATIONS_FIXED_AR.md',
    'MASTER_APP_COPY_PASTE_AR.md',
    'MASTER_APP_SIMPLE_INTEGRATION_AR.md',
    'MERCHANT_STEPS_B2C_AR.md',
    'MOYASAR_INTEGRATION.md',
    'moyasar-updated-testing.md',
    'MULTIBRANCH_SAR_SYMBOL_FIX_AR.md',
    'MY_TEST_ACCOUNTS_AR.md',
    'PHPMYADMIN_GUIDE_AR.md',
    'PLAN_LIMITS_WORKING_AR.md',
    'PLAN_PERMISSIONS_SYSTEM.md',
    'PLAN_SYSTEM_IMPLEMENTATION_SUMMARY_AR.md',
    'plan-subscription-testing-guide.md',
    'PRICING_PAGE_PERMISSIONS_AR.md',
    'PRODUCT_PRICE_AUTO_CALCULATION_FIX_AR.md',
    'PRODUCT_PRICE_CALCULATION_FIXED_AR.md',
    'PRODUCT_PRICE_FINAL_FIX_AR.md',
    'PRODUCT_PRICE_REAL_FIX_AR.md',
    'PRODUCT_PRICE_SINGLE_MODE_FIX_AR.md',
    'PRODUCT_SINGLE_MODE_FIX_AR.md',
    'QUICK_GUIDE_B2B_FIELDS_AR.md',
    'QUICK_GUIDE_VARIATIONS_AR.md',
    'QUICK_START_SAR_SYMBOL_AR.md',
    'QUICK_STEPS_B2B_INVOICE_AR.md',
    'QUICK_STEPS_NEXT.md',
    'QUICK_TEST_GUIDE_AR.md',
    'QUICK_TEST_ZATCA_SAR_AR.md',
    'README_B2B.md',
    'READY_TO_TEST_AR.md',
    'RESERVATION_SEARCH_FIXED_AR.md',
    'RESERVATION_SYSTEM_COMPLETE_AR.md',
    'RESERVATION_TIME_CONFLICT_FIX_AR.md',
    'RIYAL_SYMBOL_UPDATE_AR.md',
    'SAR_SVG_SYMBOL_COMPLETE_AR.md',
    'SAR_SYMBOL_ADMIN_FIXED_AR.md',
    'SAR_SYMBOL_DASHBOARD_POS_FIX_AR.md',
    'SAR_SYMBOL_FINAL_FIX_AR.md',
    'SAR_SYMBOL_SVG_IMPLEMENTATION_SUMMARY.md',
    'SAR_SYMBOL_TESTING_CHECKLIST_AR.md',
    'SAVE_BUTTON_EXPLANATION_AR.md',
    'SERVER_DEPLOYMENT_INSTRUCTIONS.md',
    'SIDEBAR_ICONS_IMPLEMENTATION.md',
    'SIDEBAR_PERMISSIONS_EXAMPLE.md',
    'SIDEBAR_UPDATED_AR.md',
    'SSO_BUSINESS_SUBSCRIPTION_GUIDE_AR.md',
    'SSO_DEPLOYMENT_CHECKLIST_AR.md',
    'SSO_FINAL_IMPLEMENTATION_AR.md',
    'SSO_FINAL_SUMMARY_AR.md',
    'SSO_IMPLEMENTATION_COMPLETED.md',
    'SSO_IMPLEMENTATION_FOR_SUB_APPS.md',
    'SSO_JWT_DEPLOYMENT_CHECKLIST_AR.md',
    'SSO_MASTER_APP_INTEGRATION_GUIDE.md',
    'SSO_Postman_Collection.json',
    'SSO_POSTMAN_TESTING_GUIDE.md',
    'SSO_PRODUCTION_DEPLOYMENT_AR.md',
    'SSO_QUICK_START_AR.md',
    'SSO_USER_TYPES_AND_ROLES.md',
    'START_HERE_AR.md',
    'STATUS_UPDATE.md',
    'SUBSCRIPTION_B2B_SUMMARY_AR.md',
    'SUBSCRIPTION_INVOICE_B2B_GUIDE_AR.md',
    'SUBSCRIPTION_INVOICE_ENHANCED_AR.md',
    'SUCCESS_GUIDE.md',
    'SVG_COLOR_THEMING_GUIDE.md',
    'SVG_ICON_FLASH_FIXED.md',
    'Table stuff.md',
    'TABLE_ACTIONS_COMPLETE_AR.md',
    'TABLE_API_QUICK_START_AR.md',
    'TABLE_BACKEND_COMPLETE_AR.md',
    'TABLE_BACKEND_INTEGRATION_COMPLETE.md',
    'TABLE_LOCALSTORAGE_REMOVED_AR.md',
    'TABLE_RESERVATION_API_CONVERSION_COMPLETE.md',
    'TABLE_RESERVATION_BACKEND.md',
    'TABLE_RESERVATION_CONVERSION_AR.md',
    'TABLE_SIMPLE_FIX_AR.md',
    'TABLE_SYSTEM_NOW_WORKING_AR.md',
    'TABLES_API_INTEGRATION_SUMMARY.md',
    'TEST_MANAGE_RESERVATIONS_AR.md',
    'TEST_TABLES_NOW_AR.md',
    'TEST_VAT_BATCH_MODE_AR.md',
    'TESTING_CHECKLIST_AR.md',
    'TESTING_GUIDE.md',
    'TRANSLATION_GUIDE.md',
    'TROUBLESHOOTING_B2B_BUTTON_AR.md',
    'UPLOAD_AR_JSON_INSTRUCTIONS.md',
    'VARIATIONS_FINAL_STATUS_AR.md',
    'VARIATIONS_SYSTEM_ENABLED_AR.md',
    'VAT_BATCH_MODE_COMPLETE_AR.md',
    'VAT_RATE_FROM_DATABASE_AR.md',
    'VERIFICATION_COMPLETE_AR.md',
    'WAREHOUSE_BRANCH_LIMITS_FIXED_AR.md',
    'WHAT_WE_DID_TODAY_AR.md',
    'ZATCA_B2B_VS_B2C_AR.md',
    'ZATCA_COMPLETE_CHECKLIST_AR.md',
    'ZATCA_FINAL_SUMMARY_AR.md',
    'ZATCA_README_AR.md',
    'ZATCA_SETTINGS_PAGE_READY_AR.md',
    'ZATCA_SETTINGS_STATUS_AR.md',
    'ZATCA_SUBSCRIPTION_COMPLETE_GUIDE_AR.md',
    'ZATCA_UPDATE_INSTRUCTIONS.md',
    
    // Test PHP files
    'add_test_cr_numbers.php',
    'approve_domain.php',
    'change_admin_credentials.php',
    'change_invoice_setting_to_a4.php',
    'check_all_businesses.php',
    'check_all_images.php',
    'check_and_add_cr_numbers.php',
    'check_b2b_data.php',
    'check_business_columns.php',
    'check_business_data.php',
    'check_category_variations.php',
    'check_current_plans.php',
    'check_current_user.php',
    'check_domain_limits.php',
    'check_domain_settings.php',
    'check_missing_tables.php',
    'check_my_test_accounts.php',
    'check_payment_types.php',
    'check_plan_names.php',
    'check_qr_code.php',
    'check_reservations.php',
    'check_sale_data.php',
    'check_sar_currency.php',
    'check_subscription_invoice_b2b.php',
    'check_tables_custom.php',
    'check_user_types.php',
    'check_zatca_settings.php',
    'cleanup_test_data.php',
    'clear_all_caches.php',
    'create_test_accounts.php',
    'delete_sso_user.php',
    'diagnose_b2b.php',
    'fix_missing_logos.php',
    'fix_product_price.php',
    'fix_sar_symbol_in_db.php',
    'generate_sso_token.php',
    'list_sales.php',
    'make_all_custom.php',
    'moyasar-health-check.php',
    'show_my_accounts.php',
    'test_api_direct.html',
    'test_jwt_sso.php',
    'test_plan_a_limits.php',
    'test_plan_permissions.php',
    'test_reservation_api.php',
    'test_save_business.php',
    'test_sso_auto.php',
    'test_sso_simple.php',
    'test_sso_with_plan.php',
    'test_tables_api.php',
    'test_tables_backend.php',
    'test_warehouse_branch_limits.php',
    'test-b2b-button.html',
    'test-b2b-fields.html',
    'test-sar-symbol.html',
    'test-sidebar.html',
    'update_domain_limits.php',
    'update_existing_sales_data.php',
    'update_plans_to_abc.php',
    'update_riyal_to_circumflex.php',
    'update_riyal_to_correct_symbol.php',
    'update_riyal_to_special_symbol.php',
    'update_riyal_to_word.php',
    'update_saudi_riyal_symbol.php',
    'update_system_owner_b2b.php',
    'update_system_owner_complete.php',
    'upgrade_abdullah_simple.php',
    'upgrade_abdullah_subscription.php',
    'verify_plan_limits.php',
    'verify_riyal_symbol.php',
    'verify_update.php',
    'zatca_subscription_wizard.php',
    
    // SQL files (keep only if needed for production)
    'add_subdomain.sql',
    'apply_database_changes_phpmyadmin.sql',
    'b2b_invoice_fields.sql',
    'fix_domain_error.sql',
    'fix_product_prices.sql',
    'reset_logos_to_default.sql',
    'update_footer.sql',
    
    // Backup files
    'lang/ar.json.backup',
    'public/assets/plugins/custom/business-dashboard.js.backup',
    
    // Other unused files
    'category-fix.txt',
    'missings',
    'purchase.blade.php',
    'sales.blade.php',
    'scroll-fix.css',
    'table-reservation-api.js',
    'table-reservation-backend-integration.js',
    'uploads.zip',
    'lang.rar',
];

echo "===========================================\n";
echo "  CLEANUP UNUSED FILES SCRIPT\n";
echo "===========================================\n\n";

echo "This script will remove " . count($filesToRemove) . " unused files.\n\n";
echo "Files to be removed:\n";
echo "- Documentation files (.md)\n";
echo "- Test PHP files\n";
echo "- SQL scripts\n";
echo "- Backup files\n";
echo "- Other temporary files\n\n";

echo "Do you want to see the list? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if (trim($line) == 'y') {
    foreach ($filesToRemove as $file) {
        echo "  - $file\n";
    }
    echo "\n";
}

echo "Do you want to proceed with deletion? (yes/no): ";
$line = fgets($handle);
if (trim($line) != 'yes') {
    echo "Aborted. No files were deleted.\n";
    exit;
}

$deleted = 0;
$notFound = 0;
$errors = 0;

foreach ($filesToRemove as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            $deleted++;
            echo "✓ Deleted: $file\n";
        } else {
            $errors++;
            echo "✗ Error deleting: $file\n";
        }
    } else {
        $notFound++;
    }
}

echo "\n===========================================\n";
echo "  CLEANUP SUMMARY\n";
echo "===========================================\n";
echo "✓ Deleted: $deleted files\n";
echo "- Not found: $notFound files\n";
echo "✗ Errors: $errors files\n";
echo "\nCleanup completed!\n";

fclose($handle);
