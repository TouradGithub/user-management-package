# مثال لملف composer.json في مشروع Laravel يستخدم الحزمة

```json
{
    "name": "laravel/laravel",
    "type": "project",
    "description": "My Laravel project with User Manager",
    "keywords": ["laravel", "framework"],
    "license": "MIT",
    "require": {
        "php": "^8.1",
        "laravel/framework": "^10.10",
        "laravel/sanctum": "^3.2",
        "laravel/tinker": "^2.8",
        "tourad/laravel-user-manager": "dev-master"
    },
    "require-dev": {
        "fakerphp/faker": "^1.9.1",
        "laravel/pint": "^1.0",
        "laravel/sail": "^1.18",
        "mockery/mockery": "^1.4.4",
        "nunomaduro/collision": "^7.0",
        "phpunit/phpunit": "^10.1",
        "spatie/laravel-ignition": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi"
        ]
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "pestphp/pest-plugin": true,
            "php-http/discovery": true
        }
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/TouradGithub/user-management-package.git"
        }
    ],
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

# أو بطريقة أبسط، أضف فقط repository والحزمة:

## إضافة Repository:
```bash
composer config repositories.user-manager vcs https://github.com/TouradGithub/user-management-package.git
```

## تثبيت الحزمة:
```bash
composer require tourad/laravel-user-manager:dev-master
```

## في حالة وجود مشكلة:
```bash
composer require tourad/laravel-user-manager:dev-master --ignore-platform-reqs --no-scripts
```

## بعد التثبيت:
```bash
php artisan vendor:publish --provider="Tourad\UserManager\UserManagerServiceProvider"
php artisan migrate
php artisan user-manager:install
```