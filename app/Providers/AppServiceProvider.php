<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\RemoteCatchService;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'course' => \App\Models\Course::class,
    'meeting' => \App\Models\Meeting::class,
    'article' => \App\Models\Article::class,
]);

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //远程图片抓取
        $this->app->singleton(RemoteCatchService::class, function($app){
            return new RemoteCatchService();
        });
        $this->app->alias(RemoteCatchService::class, 'remotecatch');
    }
}
