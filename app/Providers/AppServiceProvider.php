<?php

namespace App\Providers;

use App\Services\Notification\NotificationService;
use App\Services\Notification\Providers\StubWhatsAppProvider;
use App\Services\Notification\Providers\WhatsAppProviderInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // WhatsApp provider abstraction: swap StubWhatsAppProvider for a real one
        // by binding a different concrete implementation here or via config.
        $this->app->bind(WhatsAppProviderInterface::class, StubWhatsAppProvider::class);

        $this->app->bind(NotificationService::class, function ($app) {
            return new NotificationService($app->make(WhatsAppProviderInterface::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
