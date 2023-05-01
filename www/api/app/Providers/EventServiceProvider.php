<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Product;
use App\Models\RolePermission;
use App\Models\TgMessage;
use App\Observers\PermissionObserver;
use App\Observers\ProductObserver;
use App\Observers\RolePermissionObserver;
use App\Observers\TgMessageObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Product::observe(ProductObserver::class);
        TgMessage::observe(TgMessageObserver::class);
        Permission::observe(PermissionObserver::class);
        RolePermission::observe(RolePermissionObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
