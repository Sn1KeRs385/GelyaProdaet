<?php

namespace App\Providers;

use App\Models\Compilation;
use App\Models\ListOption;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        Relation::morphMap([
            (new User())->getMorphClass() => User::class,
            (new Product())->getMorphClass() => Product::class,
            (new ListOption())->getMorphClass() => ListOption::class,
            (new Compilation())->getMorphClass() => Compilation::class,
        ]);
    }
}
