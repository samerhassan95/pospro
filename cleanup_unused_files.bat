@echo off
REM Cleanup Unused Files - Windows Batch Script
REM This script removes documentation, test files, and backup files

echo ============================================
echo   CLEANUP UNUSED FILES
echo ============================================
echo.
echo This will remove unused documentation and test files.
echo.
echo Press Ctrl+C to cancel, or
pause

echo.
echo Starting cleanup...
echo.

REM Documentation files
if exist "ACTIONS_AND_MODALS_FIXED_AR.md" del "ACTIONS_AND_MODALS_FIXED_AR.md" && echo Deleted: ACTIONS_AND_MODALS_FIXED_AR.md
if exist "ADD_BUTTON_FIX_AR.md" del "ADD_BUTTON_FIX_AR.md" && echo Deleted: ADD_BUTTON_FIX_AR.md
if exist "ADD_TABLE_FIXED_AR.md" del "ADD_TABLE_FIXED_AR.md" && echo Deleted: ADD_TABLE_FIXED_AR.md
if exist "DASHBOARD_COLORS_UPDATED.md" del "DASHBOARD_COLORS_UPDATED.md" && echo Deleted: DASHBOARD_COLORS_UPDATED.md
if exist "SVG_ICON_FLASH_FIXED.md" del "SVG_ICON_FLASH_FIXED.md" && echo Deleted: SVG_ICON_FLASH_FIXED.md

REM Backup files
if exist "lang\ar.json.backup" del "lang\ar.json.backup" && echo Deleted: lang\ar.json.backup
if exist "public\assets\plugins\custom\business-dashboard.js.backup" del "public\assets\plugins\custom\business-dashboard.js.backup" && echo Deleted: business-dashboard.js.backup

REM Test files
if exist "test-sidebar.html" del "test-sidebar.html" && echo Deleted: test-sidebar.html
if exist "test-sar-symbol.html" del "test-sar-symbol.html" && echo Deleted: test-sar-symbol.html
if exist "test-b2b-fields.html" del "test-b2b-fields.html" && echo Deleted: test-b2b-fields.html
if exist "test-b2b-button.html" del "test-b2b-button.html" && echo Deleted: test-b2b-button.html

REM Temporary files
if exist "category-fix.txt" del "category-fix.txt" && echo Deleted: category-fix.txt
if exist "missings" del "missings" && echo Deleted: missings
if exist "scroll-fix.css" del "scroll-fix.css" && echo Deleted: scroll-fix.css

echo.
echo ============================================
echo   CLEANUP COMPLETED
echo ============================================
echo.
echo Important files kept:
echo - README.md (main documentation)
echo - .env and .env.example (configuration)
echo - All application code
echo.
pause
