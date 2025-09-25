# مثال سريع لاستخدام الحزمة 🚀

## إنشاء مشروع Laravel جديد واستخدام الحزمة

### 1. إنشاء مشروع جديد
```bash
composer create-project laravel/laravel my-user-management-app
cd my-user-management-app
```

### 2. إضافة الحزمة من GitHub
```bash
# إضافة repository بطريقة صحيحة
composer config repositories.user-manager vcs https://github.com/TouradGithub/user-management-package.git

# تثبيت الحزمة (بدون update أولاً)
composer require tourad/laravel-user-manager:dev-master --no-update

# ثم تحديث الحزمة فقط
composer update tourad/laravel-user-manager
```

### ⚠️ إذا واجهت مشكلة في التثبيت:
```bash
# حل المشكلة الشائعة
composer require tourad/laravel-user-manager:dev-master --ignore-platform-reqs
```

أو استخدم composer.json مباشرة:
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
ثم: `composer install`

### 3. نشر الملفات والإعدادات
```bash
# نشر جميع ملفات الحزمة
php artisan vendor:publish --provider="Tourad\UserManager\UserManagerServiceProvider"

# أو نشر ملفات محددة
php artisan vendor:publish --tag=user-manager-config
php artisan vendor:publish --tag=user-manager-migrations
php artisan vendor:publish --tag=user-manager-views
php artisan vendor:publish --tag=user-manager-assets
```

### 4. تشغيل الميجريشن
```bash
php artisan migrate
```

### 5. تثبيت البيانات الأساسية
```bash
php artisan user-manager:install
```

### 6. إنشاء مستخدم مدير
```bash
php artisan user-manager:create-user \
    --name="المدير العام" \
    --email="admin@myapp.com" \
    --password="admin123456" \
    --type=1
```

### 7. تشغيل المشروع
```bash
php artisan serve
```

### 8. الوصول للنظام
افتح المتصفح وتوجه إلى:
```
http://localhost:8000/user-manager/login
```

استخدم البيانات التي أنشأتها للدخول.

---

## إعداد سريع لملف .env

```env
APP_NAME="My User Management App"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_user_app
DB_USERNAME=root
DB_PASSWORD=

# User Manager Settings
USER_MANAGER_DEFAULT_TYPE=4
USER_MANAGER_MAX_LOGIN_ATTEMPTS=5
USER_MANAGER_LOCKOUT_DURATION=15
USER_MANAGER_FORCE_LOGIN=true

# Mail Settings (للإشعارات)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@myapp.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## استخدام سريع في Controller

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tourad\UserManager\Facades\UserManager;

class HomeController extends Controller
{
    public function dashboard()
    {
        // الحصول على إحصائيات سريعة
        $stats = UserManager::getDashboardStatistics();
        
        // الحصول على المستخدمين النشطين
        $activeUsers = UserManager::getActiveUsers();
        
        // الحصول على الأنشطة الأخيرة
        $recentActivities = UserManager::getRecentActivities(10);
        
        return view('dashboard', compact('stats', 'activeUsers', 'recentActivities'));
    }
    
    public function createUser(Request $request)
    {
        // إنشاء مستخدم جديد
        $user = UserManager::createUser([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'user_type_id' => $request->user_type_id ?? 4,
            'phone' => $request->phone,
            'country' => $request->country,
            'city' => $request->city,
        ]);
        
        // تسجيل النشاط
        UserManager::logActivity('تم إنشاء مستخدم جديد: ' . $user->name, auth()->user());
        
        return redirect()->back()->with('success', 'تم إنشاء المستخدم بنجاح');
    }
}
```

---

## إضافة مسارات مخصصة

في `routes/web.php`:

```php
use App\Http\Controllers\HomeController;

Route::middleware(['auth', 'check.user.type:1,2,3'])->group(function () {
    Route::get('/my-dashboard', [HomeController::class, 'dashboard'])->name('my.dashboard');
    Route::post('/create-user', [HomeController::class, 'createUser'])->name('my.create-user');
});

// مسار للمديرين فقط
Route::middleware(['auth', 'check.user.type:1'])->group(function () {
    Route::get('/admin-only', function() {
        return 'هذه الصفحة للمديرين فقط!';
    });
});
```

---

## استخدام Events

في `app/Providers/EventServiceProvider.php`:

```php
protected $listen = [
    \Tourad\UserManager\Events\UserCreated::class => [
        \App\Listeners\SendWelcomeNotification::class,
    ],
    \Tourad\UserManager\Events\UserLoggedIn::class => [
        \App\Listeners\LogUserActivity::class,
    ],
];
```

إنشاء Listener:

```bash
php artisan make:listener SendWelcomeNotification
```

```php
<?php

namespace App\Listeners;

use Tourad\UserManager\Events\UserCreated;
use Illuminate\Support\Facades\Mail;

class SendWelcomeNotification
{
    public function handle(UserCreated $event)
    {
        $user = $event->user;
        
        // إرسال بريد ترحيب
        // Mail::to($user->email)->send(new WelcomeMail($user));
        
        // أو إشعار داخلي
        logger("مرحباً بالمستخدم الجديد: {$user->name}");
    }
}
```

---

## تخصيص سريع للواجهة

بعد نشر الـ views، يمكنك تعديل الألوان في:
`resources/views/vendor/user-manager/layouts/app.blade.php`

```css
<style>
    :root {
        --primary-color: #your-brand-color;
        --secondary-color: #your-secondary-color;
    }
    
    .navbar {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
    }
</style>
```

---

## اختبار سريع

```bash
# إنشاء مستخدم تجريبي
php artisan user-manager:create-user --name="تجريبي" --email="test@test.com" --password="test123" --type=4

# تشغيل المشروع
php artisan serve

# زيارة الموقع
# http://localhost:8000/user-manager/login
```

**بيانات الدخول:**
- البريد: `test@test.com`
- كلمة المرور: `test123`

---

**تهانينا! 🎉 النظام جاهز للاستخدام**

يمكنك الآن:
- ✅ تسجيل الدخول بواجهة عربية جميلة
- ✅ إدارة المستخدمين والأنواع
- ✅ تتبع الأنشطة والجلسات
- ✅ استخدام النظام في مشروعك