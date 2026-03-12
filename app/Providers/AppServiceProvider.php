<?php

namespace App\Providers;

use App\Models\Translation;
use App\Observers\TranslationObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Translation::observe(TranslationObserver::class);
    }
}
