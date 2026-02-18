# تحديث إعدادات ZATCA للتجار

## الخطوات المطلوبة لتطبيق التحديث:

### 1. تشغيل Migration الجديد:
```bash
php artisan migrate
```

### 2. تشغيل Seeder للصلاحيات الجديدة:
```bash
php artisan db:seed --class=BusinessPermissionSeeder
```

### 3. إعطاء الصلاحيات للمستخدمين الحاليين:
```bash
php artisan tinker
```

ثم تشغيل الكود التالي في tinker:
```php
use App\Models\User;
use Spatie\Permission\Models\Permission;

// الحصول على جميع المستخدمين من نوع shop-owner
$shopOwners = User::where('role', 'shop-owner')->get();

// الحصول على الصلاحيات الجديدة
$zatcaPermissions = Permission::whereIn('name', [
    'zatca-settings-read',
    'zatca-settings-update',
    'moyasar-settings-read', 
    'moyasar-settings-update'
])->get();

// إعطاء الصلاحيات لجميع أصحاب المتاجر
foreach ($shopOwners as $user) {
    $user->givePermissionTo($zatcaPermissions);
}

echo "تم إعطاء الصلاحيات بنجاح!";
```

### 4. مسح الكاش:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## ما تم إضافته:

1. **إعدادات ZATCA في القائمة الجانبية**: تم إضافة رابط إعدادات ZATCA تحت قائمة الإعدادات
2. **الصلاحيات الجديدة**: تم إضافة صلاحيات للتحكم في الوصول لإعدادات ZATCA و Moyasar
3. **الحماية بالصلاحيات**: تم إضافة middleware للتحقق من الصلاحيات في جميع routes
4. **أيقونات القائمة**: تم إضافة أيقونات مناسبة لكل إعداد

## التحقق من النجاح:

1. تسجيل الدخول كتاجر
2. الذهاب إلى قائمة الإعدادات
3. يجب أن تظهر خيارات "ZATCA Settings" و "Moyasar Settings"
4. النقر على "ZATCA Settings" يجب أن يفتح صفحة إعدادات هيئة الزكاة

## ملاحظات:

- إعدادات ZATCA كانت موجودة مسبقاً في النظام ولكن لم تكن ظاهرة في القائمة
- الآن أصبحت متاحة ومحمية بالصلاحيات المناسبة
- يمكن للسوبر أدمن التحكم في من يستطيع الوصول لهذه الإعدادات