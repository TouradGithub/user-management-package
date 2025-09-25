# 🔥 حل مشكلة التثبيت - Laravel User Manager

## 🚨 إذا واجهت خطأ في التثبيت

### المشكلة الشائعة:
```
Your requirements could not be resolved to an installable set of packages.
Problem 1 - tourad/laravel-user-manager requires illuminate/support
```

### الحل السريع:

#### 1. أضف الحزمة بطريقة صحيحة:

```bash
# في مجلد مشروع Laravel الخاص بك
composer config repositories.user-manager vcs https://github.com/TouradGithub/user-management-package.git

# ثم ثبت الحزمة
composer require tourad/laravel-user-manager:dev-master --no-update

# ثم نفذ التحديث
composer update tourad/laravel-user-manager
```

#### 2. أو استخدم هذه الطريقة البديلة:

```bash
# إضافة إلى composer.json مباشرة
```

في ملف `composer.json` الخاص بمشروعك، أضف:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/TouradGithub/user-management-package.git"
        }
    ],
    "require": {
        "tourad/laravel-user-manager": "dev-master"
    }
}
```

ثم:
```bash
composer install
```

---

## ✅ طريقة التثبيت الصحيحة خطوة بخطوة:

### الخطوة 1: إنشاء مشروع Laravel
```bash
composer create-project laravel/laravel my-user-app
cd my-user-app
```

### الخطوة 2: إضافة Repository
```bash
composer config repositories.user-manager vcs https://github.com/TouradGithub/user-management-package.git
```

### الخطوة 3: تثبيت الحزمة
```bash
composer require tourad/laravel-user-manager:dev-master
```

### الخطوة 4: نشر الملفات
```bash
php artisan vendor:publish --provider="Tourad\UserManager\UserManagerServiceProvider" --force
```

### الخطوة 5: إعداد قاعدة البيانات
```bash
# تأكد من إعداد DB في .env
php artisan migrate
php artisan user-manager:install
```

### الخطوة 6: إنشاء مستخدم مدير
```bash
php artisan user-manager:create-user \
  --name="المدير" \
  --email="admin@example.com" \
  --password="admin123456" \
  --type=1
```

### الخطوة 7: تشغيل المشروع
```bash
php artisan serve
```

### الخطوة 8: الوصول للنظام
```
http://localhost:8000/user-manager/login
```

---

## 🔧 إصلاح المشاكل الشائعة:

### مشكلة: Class not found
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### مشكلة: Migration files not found
```bash
php artisan vendor:publish --tag=user-manager-migrations --force
php artisan migrate
```

### مشكلة: Views not loading
```bash
php artisan vendor:publish --tag=user-manager-views --force
php artisan view:clear
```

### مشكلة: Assets not loading
```bash
php artisan vendor:publish --tag=user-manager-assets --force
```

---

## 📋 متطلبات النظام:

- ✅ PHP >= 8.0
- ✅ Laravel >= 9.0
- ✅ MySQL/PostgreSQL/SQLite
- ✅ Composer

---

## 🎯 اختبار التثبيت:

بعد التثبيت، اختبر أن كل شيء يعمل:

```bash
# اختبار أن الـ Service يعمل
php artisan tinker
```

في Tinker:
```php
use Tourad\UserManager\Facades\UserManager;
UserManager::getDashboardStatistics();
```

إذا ظهرت النتائج، فالتثبيت نجح! ✅

---

## 🆘 إذا لم تنجح الطرق السابقة:

### الطريقة اليدوية:

1. حمل الحزمة من GitHub مباشرة
2. ضعها في مجلد `packages/tourad/laravel-user-manager`
3. أضف إلى `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/tourad/laravel-user-manager"
        }
    ],
    "require": {
        "tourad/laravel-user-manager": "*"
    }
}
```

4. نفذ: `composer install`

---

## 📞 الدعم:

إذا واجهت أي مشاكل:
- 🐛 [افتح Issue على GitHub](https://github.com/TouradGithub/user-management-package/issues)
- 📧 أرسل لنا على: support@example.com

**نعدك بحل سريع! 🚀**