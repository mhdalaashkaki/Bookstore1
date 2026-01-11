<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
// السطر التالي ضروري جداً لاستيراد الـ facade الخاص بالروابط
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // الكود القديم الموجود لديك (اتركه كما هو لحل مشاكل طول النصوص في قواعد البيانات القديمة)
        Schema::defaultStringLength(191);

        // =================================================================
        // التعديل الجديد: إجبار HTTPS لحل مشكلة "Not Secure"
        // =================================================================
        // هذا الشرط يتحقق مما إذا كان التطبيق يعمل في بيئة "production" (مثل Render)
        if ($this->app->environment('production')) {
            // إذا كان كذلك، أجبر جميع الروابط التي يولدها Laravel على استخدام https
            URL::forceScheme('https');
        }
    }
}