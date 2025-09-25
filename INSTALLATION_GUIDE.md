# دليل التثبيت واستخدام حزمة User Manager

## 📦 طريقتان للتثبيت

### الطريقة الأولى: التثبيت من GitHub مباشرة

#### 1. إضافة الحزمة إلى composer.json
في مشروع Laravel الخاص بك، أضف هذا إلى ملف `composer.json`:

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

#### 2. تثبيت الحزمة
```bash
composer install
```

### الطريقة الثانية: نشر على Packagist (مستقبلاً)

```bash
composer require tourad/laravel-user-manager
```

---

## 🚀 خطوات التثبيت في مشروع Laravel

### الخطوة 1: إنشاء مشروع Laravel جديد
```bash
composer create-project laravel/laravel my-project
cd my-project
```

### الخطوة 2: تثبيت الحزمة
```bash
# إضافة repository إلى composer.json
composer config repositories.user-manager vcs https://github.com/TouradGithub/user-management-package.git

# تثبيت الحزمة
composer require tourad/laravel-user-manager:dev-master
```

### الخطوة 3: نشر ملفات الإعداد والميجريشن
```bash
php artisan vendor:publish --provider="Tourad\UserManager\UserManagerServiceProvider"
```

سيتم نشر الملفات التالية:
- `config/user-manager.php` - ملف الإعدادات
- `database/migrations/` - ملفات الميجريشن
- `resources/views/vendor/user-manager/` - ملفات العرض (اختياري)
- `public/vendor/user-manager/` - الملفات العامة (CSS, JS, Images)

### الخطوة 4: تشغيل الميجريشن
```bash
php artisan migrate
```

### الخطوة 5: تثبيت البيانات الأساسية
```bash
php artisan user-manager:install
```

### الخطوة 6: إنشاء مستخدم مدير (اختياري)
```bash
php artisan user-manager:create-user --name="أحمد محمد" --email="admin@example.com" --password="password123" --type=1
```

---

## ⚙️ الإعدادات المطلوبة

### إعداد ملف .env
أضف هذه المتغيرات إلى ملف `.env`:

```env
# إعدادات User Manager
USER_MANAGER_DEFAULT_TYPE=4
USER_MANAGER_MAX_LOGIN_ATTEMPTS=5
USER_MANAGER_LOCKOUT_DURATION=15
USER_MANAGER_SESSION_TIMEOUT=60
USER_MANAGER_FORCE_LOGIN=true

# إعدادات رفع الملفات
USER_MANAGER_AVATAR_MAX_SIZE=2048
USER_MANAGER_ALLOWED_EXTENSIONS=jpg,jpeg,png,gif

# إعدادات الأمان
USER_MANAGER_PASSWORD_MIN_LENGTH=8
USER_MANAGER_ENABLE_2FA=false

# إعدادات البريد الإلكتروني
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourapp.com
MAIL_FROM_NAME="User Manager"
```

### إعداد Middleware في kernel.php
في ملف `app/Http/Kernel.php`، أضف:

```php
protected $routeMiddleware = [
    // ... الـ middleware الموجودة
    'check.user.type' => \Tourad\UserManager\Http\Middleware\CheckUserType::class,
    'check.login.attempts' => \Tourad\UserManager\Http\Middleware\CheckLoginAttempts::class,
    'track.user.session' => \Tourad\UserManager\Http\Middleware\TrackUserSession::class,
];
```

### إعداد User Model
في ملف `app/Models/User.php`، استبدل المحتوى بـ:

```php
<?php

namespace App\Models;

use Tourad\UserManager\Models\User as BaseUser;

class User extends BaseUser
{
    // يمكنك إضافة وظائف إضافية هنا إذا أردت
    
    // مثال: إضافة accessor مخصص
    public function getFullAddressAttribute()
    {
        return $this->city . ', ' . $this->country;
    }
}
```

---

## 🎯 الاستخدام الأساسي

### الوصول للنظام
بعد التثبيت، يمكنك الوصول للنظام عبر:

```
http://yourapp.com/user-manager/login
```

### المسارات المتاحة
- `/user-manager/login` - تسجيل الدخول
- `/user-manager/dashboard` - لوحة التحكم
- `/user-manager/users` - إدارة المستخدمين
- `/user-manager/user-types` - إدارة أنواع المستخدمين
- `/user-manager/activities` - سجل الأنشطة
- `/user-manager/sessions` - إدارة الجلسات
- `/user-manager/profile` - الملف الشخصي

---

## 🛠️ استخدام الـ Service

### في Controller
```php
<?php

namespace App\Http\Controllers;

use Tourad\UserManager\UserManagerService;

class YourController extends Controller
{
    protected $userManager;
    
    public function __construct(UserManagerService $userManager)
    {
        $this->userManager = $userManager;
    }
    
    public function createUser()
    {
        $user = $this->userManager->createUser([
            'name' => 'سارة أحمد',
            'email' => 'sara@example.com',
            'password' => 'password123',
            'user_type_id' => 2,
            'phone' => '01234567890',
            'country' => 'SA',
            'city' => 'الرياض'
        ]);
        
        return response()->json($user);
    }
    
    public function getActiveUsers()
    {
        $users = $this->userManager->getActiveUsers();
        return view('users.active', compact('users'));
    }
}
```

### استخدام Facade
```php
use Tourad\UserManager\Facades\UserManager;

// إنشاء مستخدم جديد
$user = UserManager::createUser([
    'name' => 'محمد علي',
    'email' => 'mohammed@example.com',
    'user_type_id' => 3,
]);

// الحصول على إحصائيات
$stats = UserManager::getDashboardStatistics();

// تسجيل نشاط
UserManager::logActivity('تم تحديث البيانات', auth()->user());

// الحصول على المستخدمين النشطين
$activeUsers = UserManager::getActiveUsers();
```

---

## 🔐 حماية المسارات

### حماية Route بنوع مستخدم معين
```php
// في routes/web.php
Route::middleware(['auth', 'check.user.type:1,2'])->group(function () {
    Route::get('/admin', 'AdminController@index');
    // مسارات المديرين فقط
});

Route::middleware(['auth', 'check.user.type:1,2,3'])->group(function () {
    Route::get('/staff', 'StaffController@index');
    // مسارات المديرين والموظفين
});
```

### حماية Controller
```php
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check.user.type:1']); // مديرين فقط
    }
}
```

---

## 📊 Events و Listeners

### الاستماع للأحداث
```php
// في EventServiceProvider.php
protected $listen = [
    \Tourad\UserManager\Events\UserCreated::class => [
        \App\Listeners\SendWelcomeEmail::class,
    ],
    \Tourad\UserManager\Events\UserLoggedIn::class => [
        \App\Listeners\LogUserLogin::class,
    ],
];
```

### إنشاء Listener
```php
php artisan make:listener SendWelcomeEmail
```

```php
class SendWelcomeEmail
{
    public function handle(UserCreated $event)
    {
        $user = $event->user;
        
        // إرسال بريد ترحيب
        Mail::to($user->email)->send(new WelcomeMail($user));
    }
}
```

---

## 🎨 تخصيص الواجهة

### نشر Views للتخصيص
```bash
php artisan vendor:publish --tag=user-manager-views
```

سيتم نسخ ملفات العرض إلى `resources/views/vendor/user-manager/`

### تخصيص الألوان والتصميم
في ملف CSS مخصص:

```css
:root {
    --primary-color: #your-color;
    --success-color: #your-success-color;
    --danger-color: #your-danger-color;
}

.user-manager-dashboard {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

---

## 🧪 Testing

### اختبار الوظائف الأساسية
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tourad\UserManager\Facades\UserManager;

class UserManagerTest extends TestCase
{
    public function test_can_create_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'user_type_id' => 4,
        ];
        
        $user = UserManager::createUser($userData);
        
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }
    
    public function test_dashboard_requires_authentication()
    {
        $response = $this->get('/user-manager/dashboard');
        $response->assertRedirect('/user-manager/login');
    }
}
```

---

## 📱 الاستخدام المتقدم

### استيراد مستخدمين من Excel
```php
use Tourad\UserManager\Services\UserImportExportService;

$import = new UserImportExportService();
$result = $import->importFromFile('path/to/users.xlsx', [
    'default_user_type_id' => 4,
    'send_welcome_email' => true
]);
```

### تصدير بيانات المستخدمين
```php
$export = new UserImportExportService();
$file = $export->exportUsers([
    'format' => 'xlsx', // أو csv
    'include_inactive' => false,
    'user_types' => [1, 2, 3] // أنواع محددة فقط
]);
```

---

## 🔧 مشاكل شائعة وحلولها

### مشكلة: الصفحات لا تظهر بشكل صحيح
**الحل:**
```bash
php artisan vendor:publish --tag=user-manager-assets --force
php artisan view:clear
php artisan config:clear
```

### مشكلة: خطأ في Middleware
**الحل:**
تأكد من إضافة الـ middleware إلى `kernel.php`

### مشكلة: البريد الإلكتروني لا يُرسل
**الحل:**
تأكد من إعداد MAIL في `.env` بشكل صحيح

---

## 📞 الدعم والمساعدة

- **GitHub Issues**: [الإبلاغ عن مشكلة](https://github.com/TouradGithub/user-management-package/issues)
- **التوثيق**: هذا الملف
- **أمثلة**: مجلد `examples/` في الحزمة

---

**نصائح هامة:**
1. احرص على عمل backup لقاعدة البيانات قبل التثبيت
2. اختبر النظام في بيئة التطوير أولاً
3. قم بتحديث composer.json في مشروعك
4. تأكد من صلاحيات كتابة المجلدات

**Happy Coding! 🚀**