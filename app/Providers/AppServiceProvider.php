<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use App\Observers\BookObserver;
use App\Observers\UserObserver;

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
        Book::observe(BookObserver::class);
        User::observe(UserObserver::class);
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
