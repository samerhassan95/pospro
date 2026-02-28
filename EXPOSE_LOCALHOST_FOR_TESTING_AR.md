# 🌐 حل مشكلة الاتصال من Master App لـ Localhost

## المشكلة

الـ Master App على `user.codgoo.com` (public) لا يمكنه الاتصال بـ `127.0.0.1:8000` (localhost) لأسباب أمنية.

---

## ✅ الحلول

### الحل 1: استخدام ngrok (الأسهل)

#### 1. تحميل ngrok
```bash
# من: https://ngrok.com/download
# أو
winget install ngrok
```

#### 2. تشغيل ngrok
```bash
ngrok http 8000
```

#### 3. انسخ الـ URL
```
Forwarding: https://abc123.ngrok.io -> http://localhost:8000
```

#### 4. استخدم الـ URL في Master App
```
https://abc123.ngrok.io/sso/auth?token=YOUR_JWT_TOKEN
```

---

### الحل 2: استخدام Laravel Valet (Windows)

```bash
# Install Valet
composer global require cpriego/valet-windows

# في مجلد المشروع
valet link nomupos

# الآن يمكن الوصول عبر:
http://nomupos.test/sso/auth?token=YOUR_JWT_TOKEN
```

---

### الحل 3: Expose (بديل ngrok)

```bash
# Install
npm install -g @expo/ngrok

# Run
npx expose 8000
```

---

### الحل 4: استخدام IP الخارجي

#### 1. احصل على IP الخارجي
```bash
ipconfig
# ابحث عن IPv4 Address
```

#### 2. اسمح بالاتصالات الخارجية
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

#### 3. استخدم IP في Master App
```
http://YOUR_IP:8000/sso/auth?token=YOUR_JWT_TOKEN
```

⚠️ **ملاحظة:** قد تحتاج لتعطيل Firewall مؤقتاً

---

## 🚀 الحل النهائي (Production)

ارفع المشروع على سيرفر حقيقي:

```
https://nomupos.com/sso/auth?token=YOUR_JWT_TOKEN
```

---

## 🧪 اختبار سريع مع ngrok

```bash
# 1. شغل Laravel
php artisan serve

# 2. في terminal تاني، شغل ngrok
ngrok http 8000

# 3. انسخ الـ HTTPS URL من ngrok
# مثال: https://abc123.ngrok.io

# 4. جرب في المتصفح
https://abc123.ngrok.io/sso/auth?token=YOUR_JWT_TOKEN
```

---

## ✅ Recommended: ngrok

- ✅ سهل الاستخدام
- ✅ HTTPS تلقائي
- ✅ يعمل مع أي متصفح
- ✅ مجاني للاستخدام الأساسي

**تحميل:** https://ngrok.com/download

---

**تاريخ:** 2026-02-28  
**الحالة:** ✅ حل مؤقت للاختبار
