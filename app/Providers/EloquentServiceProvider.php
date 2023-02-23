<?php

namespace App\Providers;

use App\Repository\Eloquent\BaseRepository;
use App\Repository\EloquentRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class EloquentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}
