# خطة الإصلاح النهائية

## المشاكل الحالية

### 1. إضافة طاولة جديدة
- ❌ تحفظ في localStorage فقط
- ❌ تختفي بعد Refresh
- ❌ يظهر alert بدلاً من toastr/modal
- ❌ لا يتحقق من تكرار الاسم

### 2. modal "Manage Tables"
- ❌ يقرأ من localStorage
- ❌ Actions غير موجودة لجميع الطاولات

## الحل

سأقوم بالتعديلات التالية بالترتيب:

### الخطوة 1: تعديل وظيفة حفظ الطاولة الجديدة
الموقع: السطر ~670 في scripts-placeholder.blade.php
```javascript
// القديم
saveCustomTable(newTable);
alert('Table added successfully!');

// الجديد
const tableData = {...};
const savedTable = await createTableInBackend(tableData);
await loadAndRenderTables();
toastr.success('Table added successfully!');
```

### الخطوة 2: تعديل modal "Manage Tables"
الموقع: السطر ~1880 في scripts-placeholder.blade.php
```javascript
// القديم
const customTables = JSON.parse(localStorage.getItem('customTables') || '[]');
const allTables = document.querySelectorAll('.table-item');

// الجديد
const tables = await getTablesFromBackend();
// عرض جميع الطاولات مع Actions
```

### الخطوة 3: إضافة validation في Controller
الموقع: AcnooRestaurantTableController.php
```php
// التحقق من تكرار الاسم
$existingTable = RestaurantTable::where('business_id', Auth::user()->business_id)
    ->where('table_name', $data['table_name'])
    ->first();
    
if ($existingTable) {
    return response()->json(['success' => false, 'message' => '...'], 422);
}
```

## التنفيذ

سأقوم بالتعديلات الآن...
