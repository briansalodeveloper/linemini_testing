<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $models = array(
            'Admin',
            'ContentPlan',
            'DisplayTargetContent',
            'DisplayTargetContentAO',
            'DisplayTargetContentUB',
            'Message',
        );

        foreach ($models as $model) {
            $this->app->bind("App\Interfaces\\{$model}RepositoryInterface", "App\Repositories\\{$model}EloquentRepository");
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
