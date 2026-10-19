<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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
        // غیرفعال کردن محافظت Mass Assignment برای کل مدل‌ها
        Model::unguard();
        // جلوگیری از مشکل N+1 در محیط توسعه (که خودتان پیشنهاد داده بودید)
        Model::preventLazyLoading(! app()->isProduction());
    }
}
