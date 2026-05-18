<?php

namespace App\Providers;

use App\Interfaces\AIServiceInterface;
use App\Services\AI\AIService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AIServiceInterface::class, AIService::class);
    }

    public function boot(): void
    {
        //
    }
}
