# دليل إعداد Subdomain و Custom Domain على CloudPanel

## نظرة عامة

هنشرح كيفية إعداد:
1. **Subdomain** (مثل: `shop.nomupos.com`)
2. **Custom Domain** (مثل: `myshop.com`)

على سيرفر CloudPanel مع Laravel Multi-tenant System.

---

## الجزء الأول: إعداد Wildcard Subdomain

### 1. إعداد DNS Records

في لوحة تحكم الدومين (Namecheap, GoDaddy, Cloudflare, إلخ):

#### أضف Wildcard A Record:
```
Type: A
Host: *
Value: [IP السيرفر]
TTL: Automatic
```

#### مثال:
```
A     *     123.456.789.10
```

هذا يسمح بـ:
- `shop.nomupos.com`
- `store.nomupos.com`
- `any-name.nomupos.com`

⏱️ **الانتظار:** 5-30 دقيقة حتى ينتشر DNS

#### التحقق من DNS:
```bash
# من جهازك أو السيرفر
nslookup shop.nomupos.com
# يجب أن يرجع IP السيرفر
```

---

### 2. إعداد CloudPanel

#### الطريقة 1: Wildcard SSL (موصى به)

**الخطوات:**

1. **اذهب إلى CloudPanel:**
   - Sites → اختر الموقع الرئيسي (`nomupos.com`)

2. **SSL/TLS:**
   - اضغط على "Actions" → "Manage SSL"
   - اختر "Let's Encrypt"
   - في حقل "Domains"، أضف:
     ```
     nomupos.com
     www.nomupos.com
     *.nomupos.com
     ```
   - اضغط "Install"

⚠️ **ملاحظة:** Wildcard SSL يحتاج DNS Challenge (TXT Record)

**إذا طلب منك TXT Record:**
```
Type: TXT
Host: _acme-challenge
Value: [القيمة من CloudPanel]
TTL: Automatic
```

انتظر دقيقة ثم اضغط "Verify"

---

#### الطريقة 2: إضافة كل Subdomain يدوياً

إذا لم تنجح Wildcard SSL:

**لكل subdomain:**

1. **في CloudPanel:**
   - Sites → اختر الموقع
   - Domains → Add Domain
   - أدخل: `shop.nomupos.com`
   - Document Root: نفس مجلد الموقع الرئيسي
   - اضغط "Add"

2. **SSL:**
   - اضغط "Actions" → "Manage SSL"
   - اختر "Let's Encrypt"
   - اضغط "Install"

---

### 3. إعداد Nginx/Apache

#### في CloudPanel:

1. **اذهب إلى:**
   - Sites → اختر الموقع → Vhost

2. **عدل الـ Vhost:**

**للـ Nginx:**
```nginx
server {
    listen 80;
    listen [::]:80;
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    
    # الدومين الرئيسي + Wildcard
    server_name nomupos.com www.nomupos.com *.nomupos.com;
    
    root /home/clpuser/htdocs/nomupos.com/public;
    
    # باقي الإعدادات...
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

3. **احفظ وأعد تشغيل Nginx:**
```bash
sudo nginx -t
sudo systemctl reload nginx
```

---

## الجزء الثاني: إعداد Custom Domain

### 1. إعداد DNS للدومين الجديد

في لوحة تحكم الدومين الجديد (مثل: `myshop.com`):

#### أضف A Record:
```
Type: A
Host: @
Value: [IP السيرفر]
TTL: Automatic
```

#### أضف WWW Record:
```
Type: CNAME
Host: www
Value: myshop.com
TTL: Automatic
```

⏱️ **الانتظار:** 5-30 دقيقة

#### التحقق:
```bash
nslookup myshop.com
```

---

### 2. إضافة الدومين في CloudPanel

1. **اذهب إلى:**
   - Sites → اختر الموقع الرئيسي

2. **أضف Domain:**
   - Domains → Add Domain
   - Domain Name: `myshop.com`
   - Document Root: نفس مجلد الموقع الرئيسي
   - اضغط "Add"

3. **أضف WWW:**
   - Domains → Add Domain
   - Domain Name: `www.myshop.com`
   - Document Root: نفس مجلد الموقع الرئيسي
   - اضغط "Add"

4. **SSL:**
   - لكل دومين، اضغط "Actions" → "Manage SSL"
   - اختر "Let's Encrypt"
   - اضغط "Install"

---

### 3. تحديث Vhost

في CloudPanel → Sites → Vhost:

```nginx
server {
    listen 80;
    listen [::]:80;
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    
    # كل الدومينات
    server_name nomupos.com www.nomupos.com *.nomupos.com myshop.com www.myshop.com;
    
    root /home/clpuser/htdocs/nomupos.com/public;
    
    # باقي الإعدادات...
}
```

---

## الجزء الثالث: إعداد Laravel Application

### 1. تحديث `.env`

```env
APP_URL=https://nomupos.com
```

### 2. إضافة الدومينات في النظام

#### من Business Panel:

1. **سجل دخول كـ Business User**
2. **اذهب إلى:** Business Panel → Domains
3. **أضف Subdomain:**
   - اضغط "Add New Domain"
   - Type: Subdomain
   - Name: `shop` (بدون .nomupos.com)
   - اضغط "Save"

4. **أضف Custom Domain:**
   - اضغط "Add New Domain"
   - Type: Custom Domain
   - Name: `myshop.com`
   - اضغط "Save"

---

### 3. الموافقة من Admin Panel

1. **سجل دخول كـ Admin**
2. **اذهب إلى:** Admin Panel → Domains
3. **وافق على الدومينات:**
   - ابحث عن الدومين Pending
   - اضغط "Approve"
   - أو استخدم SQL:
   ```sql
   UPDATE domains 
   SET status = 1, is_verified = 1 
   WHERE domain IN ('shop.nomupos.com', 'myshop.com');
   ```

---

### 4. امسح الكاش

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## الجزء الرابع: التحقق من الإعداد

### 1. اختبر الدومين الرئيسي
```bash
curl -I https://nomupos.com
# يجب أن يرجع 200 OK
```

### 2. اختبر Subdomain
```bash
curl -I https://shop.nomupos.com
# يجب أن يرجع 200 OK
```

### 3. اختبر Custom Domain
```bash
curl -I https://myshop.com
# يجب أن يرجع 200 OK
```

### 4. اختبر من المتصفح
- افتح `https://shop.nomupos.com`
- يجب أن يظهر الموقع بدون أخطاء
- تحقق من SSL (القفل الأخضر)

---

## استكشاف الأخطاء

### مشكلة: SSL Certificate Error

**الحل:**
```bash
# في CloudPanel
sudo certbot certonly --manual --preferred-challenges dns -d "*.nomupos.com" -d "nomupos.com"
```

---

### مشكلة: 400 Domain Not Allowed

**الحل:**
```sql
-- تحقق من حالة الدومين
SELECT * FROM domains WHERE domain = 'shop.nomupos.com';

-- وافق على الدومين
UPDATE domains SET status = 1, is_verified = 1 WHERE domain = 'shop.nomupos.com';
```

ثم امسح الكاش:
```bash
php artisan cache:clear
```

---

### مشكلة: DNS Not Resolving

**الحل:**
```bash
# تحقق من DNS
nslookup shop.nomupos.com

# امسح DNS Cache على جهازك
# Windows:
ipconfig /flushdns

# Mac/Linux:
sudo dscacheutil -flushcache
```

---

### مشكلة: Nginx 404 Error

**الحل:**
```bash
# تحقق من Nginx config
sudo nginx -t

# أعد تشغيل Nginx
sudo systemctl reload nginx

# تحقق من الـ logs
sudo tail -f /var/log/nginx/error.log
```

---

## الإعدادات المتقدمة

### 1. تفعيل Automatic Approval (للتطوير فقط)

في Admin Panel → Settings → Domain Settings:
```
automatic_approve: on
ssl_required: off
```

⚠️ **تحذير:** لا تستخدم هذا في Production!

---

### 2. إعداد Business Context

الـ `CustomDomainMapping` middleware بيعمل mapping تلقائي:

```php
// في app/Http/Middleware/CustomDomainMapping.php
$domain = Domain::where('domain', $host)
    ->where('status', 1)
    ->where('is_verified', 1)
    ->first();

if ($domain) {
    config(['app.current_business_id' => $domain->business_id]);
    session(['mapped_business_id' => $domain->business_id]);
}
```

---

### 3. إعداد Email للدومينات

لكل custom domain، قد تحتاج إعداد:
- SPF Record
- DKIM Record
- DMARC Record

---

## الخلاصة

### Checklist للإعداد الكامل:

#### DNS:
- [ ] Wildcard A Record (`*`)
- [ ] Custom Domain A Record
- [ ] WWW CNAME Record

#### CloudPanel:
- [ ] Wildcard SSL أو SSL لكل subdomain
- [ ] إضافة Custom Domains
- [ ] تحديث Vhost

#### Laravel:
- [ ] تحديث `.env`
- [ ] إضافة الدومينات في النظام
- [ ] الموافقة من Admin Panel
- [ ] مسح الكاش

#### التحقق:
- [ ] DNS Resolution
- [ ] SSL Certificate
- [ ] HTTP Response 200
- [ ] Business Context Mapping

---

## الدعم

إذا واجهت أي مشاكل:

1. **تحقق من الـ logs:**
```bash
tail -f storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

2. **استخدم السكريبتات:**
```bash
php check_domain_settings.php
php approve_domain.php shop.nomupos.com
```

3. **تحقق من قاعدة البيانات:**
```sql
SELECT * FROM domains;
SELECT * FROM options WHERE key = 'domain-setting';
```

---

**الآن لديك نظام Multi-tenant كامل يعمل على CloudPanel! 🎉**
